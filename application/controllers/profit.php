<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Profit extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(10, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'profit/index';
        $data['data']['users'] = $this->db
            ->distinct()
            ->select('users.id,username')
            ->from('orders')
            ->join('users', 'orders.user_init = users.id', 'LEFT')
            ->where(['deleted' => 0, 'order_status' => 1])
            ->get()
            ->result_array();

        $data['data']['sales'] = $this->db
            ->select('users.id,username')
            ->from('users')
            ->get()
            ->result_array();

        $data['data']['customers'] = $this->db->from('customers')->get()->result_array();
        $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_profit($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option2'] > -1) {
            $this->db->where('user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        $data['receipt'] = $this->db
            ->select('sum(total_money) as total_money')
            ->from('receipt')
            ->where('deleted', 0)
            ->where('order_id', 0)
            ->where('receipt_date >=', $option['date_from'])
            ->where('receipt_date <=', $option['date_to'])
            ->get()
            ->row_array();

        if ($option['option2'] > -1) {
            $this->db->where('user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        $data['payment'] = $this->db
            ->select('sum(total_money) as total_money')
            ->from('payment')
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where('payment_date >=', $option['date_from'])
            ->where('payment_date <=', $option['date_to'])
            ->get()
            ->row_array();

        $this->cms_profit_option($option);

        if ($option['type'] == 1) {
            $total_orders = $this->db
                ->select('count(*) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_option($option);

            $data['_list_orders'] = $this->db
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->result_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/all', isset($data) ? $data : null);
        } else if ($option['type'] == 2) {
            $total_orders = $this->db
                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_option($option);

            $list_customers = $this->db
                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('customer_id')
                ->get()
                ->result_array();
            foreach ((array)$list_customers as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where('customer_id', $item['customer_id'])
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->get()
                    ->result_array();
                $data['_list_customers'][] = $item;
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/customer', isset($data) ? $data : null);
        } else if ($option['type'] == 3) {
            $total_orders = $this->db
                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_option($option);

            $list_users = $this->db
                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('user_init')
                ->get()
                ->result_array();
            foreach ((array)$list_users as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('user_init', $item['user_init'])
                    ->get()
                    ->result_array();
                $data['_list_users'][] = $item;
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/user', isset($data) ? $data : null);
        } else if ($option['type'] == 4) {
            $total_orders = $this->db
                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_option($option);

            $list_sales = $this->db
                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('sale_id')
                ->get()
                ->result_array();
            foreach ((array)$list_sales as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('sale_id', $item['sale_id'])
                    ->get()
                    ->result_array();
                $data['_list_sales'][] = $item;
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/sale', isset($data) ? $data : null);
        } else if ($option['type'] == 5) {
            $total_orders = $this->db
                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_option($option);

            $list_stores = $this->db
                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('store_id')
                ->get()
                ->result_array();
            foreach ((array)$list_stores as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('store_id', $item['store_id'])
                    ->get()
                    ->result_array();
                $data['_list_stores'][] = $item;
            }

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/store', isset($data) ? $data : null);
        } else if ($option['type'] == 6) {
            $total_orders = $this->db
                ->select('count(distinct(ID)) as quantity, sum(total_money) as total_money, sum(total_origin_price) as total_origin_price, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_return($option);

            $return_money = $this->db
                ->select('sum(cms_input.total_money) as return_money,sum(total_origin_price_return) as total_origin_price_return')
                ->from('input')
                ->join('orders', 'orders.ID=input.order_id', 'INNER')
                ->where(['input.deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_profit_report($option);

            $total_row = $this->db
                ->select('count(distinct(cms_report.product_id)) as quantity')
                ->from('report')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->get()
                ->row_array();

            $this->cms_profit_report($option);

            $data['_list_products'] = $this->db
                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                ->from('report')
                ->join('products', 'report.product_id=products.ID')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('report.created', 'desc')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->group_by('product_id')
                ->get()
                ->result_array();

            $total_orders['return_money'] = $return_money['return_money'];
            $total_orders['total_origin_price'] -= $return_money['total_origin_price_return'];
            $config['base_url'] = 'cms_paging_profit';
            $config['total_rows'] = $total_row['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_row['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/profit/product', isset($data) ? $data : null);
        }
    }

    public function cms_profit_option($option)
    {
        if ($option['option1'] > -1) {
            $this->db->where('customer_id', $option['option1']);
        }

        if ($option['option2'] > -1) {
            $this->db->where('user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        if ($option['option4'] > -1) {
            $this->db->where('sale_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('sell_date >=', $option['date_from'])
                ->where('sell_date <=', $option['date_to']);
        }
    }

    public function cms_profit_return($option)
    {
        if ($option['option1'] > -1) {
            $this->db->where('customer_id', $option['option1']);
        }

        if ($option['option2'] > -1) {
            $this->db->where('orders.user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('orders.store_id', $option['option3']);
        }

        if ($option['option4'] > -1) {
            $this->db->where('sale_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('input_date >=', $option['date_from'])
                ->where('input_date <=', $option['date_to']);
        }
    }

    public function cms_profit_report($option)
    {
        if ($option['option1'] > -1) {
            $this->db->where('customer_id', $option['option1']);
        }

        if ($option['option2'] > -1) {
            $this->db->where('report.user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        if ($option['option4'] > -1) {
            $this->db->where('sale_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('date >=', $option['date_from'])
                ->where('date <=', $option['date_to']);
        }
    }

    public function cms_export_profit()
    {
        $option = $this->input->post('data');
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option2'] > -1) {
            $this->db->where('user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        $data['receipt'] = $this->db
            ->select('sum(total_money) as total_money')
            ->from('receipt')
            ->where('deleted', 0)
            ->where('order_id', 0)
            ->where('receipt_date >=', $option['date_from'])
            ->where('receipt_date <=', $option['date_to'])
            ->get()
            ->row_array();

        if ($option['option2'] > -1) {
            $this->db->where('user_init', $option['option2']);
        }

        if ($option['option3'] > -1) {
            $this->db->where('store_id', $option['option3']);
        }

        $data['payment'] = $this->db
            ->select('sum(total_money) as total_money')
            ->from('payment')
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where('payment_date >=', $option['date_from'])
            ->where('payment_date <=', $option['date_to'])
            ->get()
            ->row_array();

        cms_delete_public_file_by_extend('xlsx');


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        if ($option['type'] == 1) {
            $this->cms_profit_option($option);

            $data['_list_orders'] = $this->db
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->result_array();

            $fileName = 'LoiNhuan-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Mã đơn hàng');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Kho xuất');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ngày xuất');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Thu ngân');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Khách hàng');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng SL');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Doanh số');
            $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Tiền vốn');
            $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('Lợi nhuận');

            $rowCount = 2;
            foreach ((array)$data['_list_orders'] as $element) {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['output_code']);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_ConvertDateTime($element['sell_date']));
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['user_init']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNamecustomerbyID($element['customer_id']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($element['coupon']);
                $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($element['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($element['total_origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($element['total_money'] - $element['total_origin_price']);
                $rowCount++;
            }
        } else if ($option['type'] == 2) {
            $this->cms_profit_option($option);

            $list_customers = $this->db
                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('customer_id')
                ->get()
                ->result_array();
            foreach ((array)$list_customers as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where('customer_id', $item['customer_id'])
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->get()
                    ->result_array();
                $data['_list_customers'][] = $item;
            }

            $fileName = 'LoiNhuanTheoKhachHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $rowCount = 1;
            foreach ((array)$data['_list_customers'] as $customer) {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('Tên khách hàng');
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Tổng số đơn');
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Tổng SP');
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Tổng chiết khấu');
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Doanh số');
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tiền vốn');
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Lợi nhuận');
                $rowCount++;
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(cms_getNamecustomerbyID($customer['customer_id']));
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($customer['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($customer['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($customer['total_discount']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($customer['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($customer['total_origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($customer['total_money'] - $customer['total_origin_price']);
                $rowCount++;

                foreach ((array)$customer['_list_orders'] as $order) {
                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Mã đơn hàng');
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Kho xuất');
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Ngày xuất');
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Thu ngân');
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tổng SL');
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Chiết khấu');
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Doanh số');
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue('Tiền vốn');
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue('Lợi nhuận');
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($order['output_code']);
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_getNamestockbyID($order['store_id']));
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_ConvertDateTime($order['sell_date']));
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNameAuthbyID($order['user_init']));
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($order['total_quantity']);
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($order['coupon']);
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($order['total_money']);
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($order['total_origin_price']);
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($order['total_money'] - $order['total_origin_price']);
                    $rowCount++;

                    $list_product = json_decode($order['detail_order'], true);
                    if (($list_product) != null) {
                        $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('STT');
                        $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Mã SP');
                        $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Tên SP');
                        $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Số lượng');
                        $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Đơn giá');
                        $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Thành tiền');
                        $rowCount++;

                        $seq = 1;
                        foreach ((array)$list_product as $product) {
                            $_product = cms_finding_productbyID($product['id']);
                            $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                            $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                            $_product['discount'] = $product['discount'];

                            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($seq++);
                            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($_product['prd_code']);
                            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($_product['prd_name']);
                            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($_product['quantity']);
                            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($_product['price']);
                            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($_product['price'] * $_product['quantity']) - $_product['discount']);
                            $rowCount++;
                        }
                    }

                    $rowCount++;
                }

                $rowCount++;
            }

        } else if ($option['type'] == 3) {
            $this->cms_profit_option($option);

            $list_users = $this->db
                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('user_init')
                ->get()
                ->result_array();
            foreach ((array)$list_users as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('user_init', $item['user_init'])
                    ->get()
                    ->result_array();
                $data['_list_users'][] = $item;
            }

            $fileName = 'LoiNhuanTheoThuNgan-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $rowCount = 1;
            foreach ((array)$data['_list_users'] as $customer) {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('Tên thu ngân');
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Tổng số đơn');
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Tổng SP');
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Tổng chiết khấu');
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Doanh số');
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tiền vốn');
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Lợi nhuận');
                $rowCount++;
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(cms_getNameAuthbyID($customer['user_init']));
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($customer['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($customer['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($customer['total_discount']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($customer['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($customer['total_origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($customer['total_money'] - $customer['total_origin_price']);
                $rowCount++;

                foreach ((array)$customer['_list_orders'] as $order) {
                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Mã đơn hàng');
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Kho xuất');
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Ngày xuất');
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Thu ngân');
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tổng SL');
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Chiết khấu');
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Doanh số');
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue('Tiền vốn');
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue('Lợi nhuận');
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($order['output_code']);
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_getNamestockbyID($order['store_id']));
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_ConvertDateTime($order['sell_date']));
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNameAuthbyID($order['user_init']));
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($order['total_quantity']);
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($order['coupon']);
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($order['total_money']);
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($order['total_origin_price']);
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($order['total_money'] - $order['total_origin_price']);
                    $rowCount++;

                    $list_product = json_decode($order['detail_order'], true);
                    if (($list_product) != null) {
                        $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('STT');
                        $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Mã SP');
                        $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Tên SP');
                        $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Số lượng');
                        $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Đơn giá');
                        $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Thành tiền');
                        $rowCount++;

                        $seq = 1;
                        foreach ((array)$list_product as $product) {
                            $_product = cms_finding_productbyID($product['id']);
                            $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                            $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                            $_product['discount'] = $product['discount'];

                            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($seq++);
                            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($_product['prd_code']);
                            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($_product['prd_name']);
                            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($_product['quantity']);
                            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($_product['price']);
                            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($_product['price'] * $_product['quantity']) - $_product['discount']);
                            $rowCount++;
                        }
                    }

                    $rowCount++;
                }

                $rowCount++;
            }
        } else if ($option['type'] == 4) {
            $this->cms_profit_option($option);

            $list_sales = $this->db
                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('sale_id')
                ->get()
                ->result_array();
            foreach ((array)$list_sales as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('sale_id', $item['sale_id'])
                    ->get()
                    ->result_array();
                $data['_list_sales'][] = $item;
            }

            $fileName = 'LoiNhuanTheoNVBanHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $rowCount = 1;
            foreach ((array)$data['_list_sales'] as $customer) {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('Tên NV bán hàng');
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Tổng số đơn');
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Tổng SP');
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Tổng chiết khấu');
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Doanh số');
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tiền vốn');
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Lợi nhuận');
                $rowCount++;
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(cms_getNameAuthbyID($customer['sale_id']));
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($customer['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($customer['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($customer['total_discount']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($customer['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($customer['total_origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($customer['total_money'] - $customer['total_origin_price']);
                $rowCount++;

                foreach ((array)$customer['_list_orders'] as $order) {
                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Mã đơn hàng');
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Kho xuất');
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Ngày xuất');
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Thu ngân');
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tổng SL');
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Chiết khấu');
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Doanh số');
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue('Tiền vốn');
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue('Lợi nhuận');
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($order['output_code']);
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_getNamestockbyID($order['store_id']));
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_ConvertDateTime($order['sell_date']));
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNameAuthbyID($order['user_init']));
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($order['total_quantity']);
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($order['coupon']);
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($order['total_money']);
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($order['total_origin_price']);
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($order['total_money'] - $order['total_origin_price']);
                    $rowCount++;

                    $list_product = json_decode($order['detail_order'], true);
                    if (($list_product) != null) {
                        $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('STT');
                        $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Mã SP');
                        $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Tên SP');
                        $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Số lượng');
                        $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Đơn giá');
                        $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Thành tiền');
                        $rowCount++;

                        $seq = 1;
                        foreach ((array)$list_product as $product) {
                            $_product = cms_finding_productbyID($product['id']);
                            $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                            $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                            $_product['discount'] = $product['discount'];

                            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($seq++);
                            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($_product['prd_code']);
                            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($_product['prd_name']);
                            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($_product['quantity']);
                            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($_product['price']);
                            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($_product['price'] * $_product['quantity']) - $_product['discount']);
                            $rowCount++;
                        }
                    }

                    $rowCount++;
                }

                $rowCount++;
            }
        } else if ($option['type'] == 5) {
            $this->cms_profit_option($option);

            $list_stores = $this->db
                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(total_origin_price) as total_origin_price')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('store_id')
                ->get()
                ->result_array();
            foreach ((array)$list_stores as $item) {
                $this->cms_profit_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('store_id', $item['store_id'])
                    ->get()
                    ->result_array();
                $data['_list_stores'][] = $item;
            }

            $fileName = 'LoiNhuanTheoCuaHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $rowCount = 1;
            foreach ((array)$data['_list_stores'] as $customer) {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('Tên kho');
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Tổng số đơn');
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Tổng SP');
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Tổng chiết khấu');
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Doanh số');
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tiền vốn');
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Lợi nhuận');
                $rowCount++;
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(cms_getNamestockbyID($customer['store_id']));
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($customer['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($customer['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($customer['total_discount']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($customer['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($customer['total_origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($customer['total_money'] - $customer['total_origin_price']);
                $rowCount++;

                foreach ((array)$customer['_list_orders'] as $order) {
                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Mã đơn hàng');
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('Kho xuất');
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Ngày xuất');
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Thu ngân');
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tổng SL');
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Chiết khấu');
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Doanh số');
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue('Tiền vốn');
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue('Lợi nhuận');
                    $rowCount++;

                    $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($order['output_code']);
                    $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_getNamestockbyID($order['store_id']));
                    $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_ConvertDateTime($order['sell_date']));
                    $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNameAuthbyID($order['user_init']));
                    $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($order['total_quantity']);
                    $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($order['coupon']);
                    $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($order['total_money']);
                    $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($order['total_origin_price']);
                    $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($order['total_money'] - $order['total_origin_price']);
                    $rowCount++;

                    $list_product = json_decode($order['detail_order'], true);
                    if (($list_product) != null) {
                        $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('STT');
                        $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Mã SP');
                        $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Tên SP');
                        $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Số lượng');
                        $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Đơn giá');
                        $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue('Thành tiền');
                        $rowCount++;

                        $seq = 1;
                        foreach ((array)$list_product as $product) {
                            $_product = cms_finding_productbyID($product['id']);
                            $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                            $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                            $_product['discount'] = $product['discount'];

                            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($seq++);
                            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($_product['prd_code']);
                            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($_product['prd_name']);
                            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($_product['quantity']);
                            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($_product['price']);
                            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($_product['price'] * $_product['quantity']) - $_product['discount']);
                            $rowCount++;
                        }
                    }

                    $rowCount++;
                }

                $rowCount++;
            }
        } else if ($option['type'] == 6) {
            $this->cms_profit_report($option);

            $data['_list_products'] = $this->db
                ->select('product_id, sum(origin_price) as origin_price, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                ->from('report')
                ->join('products', 'report.product_id=products.ID')
                ->order_by('report.created', 'desc')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->group_by('product_id')
                ->get()
                ->result_array();

            $fileName = 'LoiNhuanTheoHangHoa-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';

            $rowCount = 1;

            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('Mã SP');
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue('Tên SP');
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue('SL bán');
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue('Chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue('Tiền vốn');
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue('Lợi nhuận');
            $rowCount++;

            foreach ((array)$data['_list_products'] as $product) {
                $prd = cms_finding_productbyID($product['product_id']);
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($prd['prd_code']);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($prd['prd_name']);
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($product['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($product['total_discount']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($product['total_money']);
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($product['origin_price']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($product['total_money'] - $product['origin_price']);
                $rowCount++;
            }
        }

        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($col, true)
                ->setAutoSize(true);
        }

        $objWriter = new Xlsx($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH . $fileName);

        header("Content-Type: application/vnd.ms-excel");
        echo $this->messages = (HTTP_UPLOAD_IMPORT_PATH . $fileName);
    }
}
