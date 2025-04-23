<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Revenue extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(8, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'revenue/index';
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

    public function cms_paging_revenue($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        $this->cms_revenue_option($option);
        if ($option['type'] == 1) {
            $total_orders = $this->db
                ->select('count(store_id) as quantity, sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_option($option);

            $data['_list_orders'] = $this->db
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->result_array();

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/all', isset($data) ? $data : null);
        } else if ($option['type'] == 2) {
            $total_orders = $this->db
                ->select('count(distinct(customer_id)) as quantity, sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_option($option);

            $list_customers = $this->db
                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('customer_id')
                ->get()
                ->result_array();

            foreach ((array)$list_customers as $item) {
                $this->cms_revenue_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where('customer_id', $item['customer_id'])
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->get()
                    ->result_array();
                $data['_list_customers'][] = $item;
            }

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/customer', isset($data) ? $data : null);
        } else if ($option['type'] == 3) {
            $total_orders = $this->db
                ->select('count(distinct(user_init)) as quantity, sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_option($option);

            $list_users = $this->db
                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('user_init')
                ->get()
                ->result_array();

            foreach ((array)$list_users as $item) {
                $this->cms_revenue_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('user_init', $item['user_init'])
                    ->get()
                    ->result_array();
                $data['_list_users'][] = $item;
            }

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/user', isset($data) ? $data : null);
        } else if ($option['type'] == 4) {
            $total_orders = $this->db
                ->select('count(distinct(sale_id)) as quantity, sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_option($option);

            $list_sales = $this->db
                ->select('sale_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('sale_id')
                ->get()
                ->result_array();

            foreach ((array)$list_sales as $item) {
                $this->cms_revenue_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('sale_id', $item['sale_id'])
                    ->get()
                    ->result_array();
                $data['_list_sales'][] = $item;
            }

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/sale', isset($data) ? $data : null);
        } else if ($option['type'] == 5) {
            $total_orders = $this->db
                ->select('count(distinct(store_id)) as quantity, sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_option($option);

            $list_stores = $this->db
                ->select('store_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('store_id')
                ->get()
                ->result_array();

            foreach ((array)$list_stores as $item) {
                $this->cms_revenue_option($option);

                $item['_list_orders'] = $this->db
                    ->from('orders')
                    ->order_by('created', 'desc')
                    ->where(['deleted' => 0, 'order_status' => 1])
                    ->where('store_id', $item['store_id'])
                    ->get()
                    ->result_array();
                $data['_list_stores'][] = $item;
            }

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_orders['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/store', isset($data) ? $data : null);
        } else if ($option['type'] == 6) {
            $total_orders = $this->db
                ->select('sum(total_money) as total_money, sum(lack) as total_debt, sum(total_discount) as total_discount, sum(total_quantity) as total_quantity')
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->row_array();

            $this->cms_revenue_report($option);

            $total_row = $this->db
                ->select('count(distinct(cms_report.product_id)) as quantity')
                ->from('report')
                ->order_by('report.created', 'desc')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->get()
                ->row_array();

            $this->cms_revenue_report($option);

            $data['_list_products'] = $this->db
                ->select('product_id, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                ->from('report')
                ->join('products', 'report.product_id=products.ID')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('report.created', 'desc')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->group_by('product_id')
                ->get()
                ->result_array();

            $config['base_url'] = 'cms_paging_revenue';
            $config['total_rows'] = $total_row['quantity'];

            $this->pagination->initialize($config);
            $_pagination_link = $this->pagination->create_links();
            $data['total_orders'] = $total_orders;
            if ($page > 1 && ($total_row['quantity'] - 1) / ($page - 1) == 10)
                $page = $page - 1;

            $data['page'] = $page;
            $data['_pagination_link'] = $_pagination_link;
            $this->load->view('ajax/revenue/product', isset($data) ? $data : null);
        }
    }

    public function cms_revenue_option($option)
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

    public function cms_revenue_report($option)
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

    public function cms_export_revenue()
    {
        $option = $this->input->post('data');
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        if ($option['type'] < 6) {
            $this->cms_revenue_option($option);
        } else {
            $this->cms_revenue_report($option);
        }

        if ($option['type'] == 1) {
            $data['_list_orders'] = $this->db
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Mã đơn hàng');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Kho xuất');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ngày bán');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Thu ngân');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Khách hàng');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Số lượng');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Nợ');

            $objPHPExcel->getActiveSheet()->getStyle('A1:I1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');
            $rowCount = 2;
            foreach ((array)$data['_list_orders'] as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['output_code']);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(($element['sell_date']));
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['user_init']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNamecustomerbyID($element['customer_id']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($element['coupon']);
                $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($element['total_money']));
                $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue(($element['lack']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':I' . $rowCount++)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:I' . ($rowCount - 1))->applyFromArray($BStyle, true);
        } else if ($option['type'] == 2) {
            $list_customers = $this->db
                ->select('customer_id, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('customer_id')
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $seq = 1;
            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('STT');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Tên khách hàng');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Tổng số đơn');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Tổng chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng SP');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tổng nợ');

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');
            $rowCount = 2;
            foreach ((array)$list_customers as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($seq++);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNamecustomerbyID($element['customer_id']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_discount']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_money']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_quantity']));
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_debt']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');

                $rowCount++;
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($rowCount - 1))->applyFromArray($BStyle, true);
        } else if ($option['type'] == 3) {
            $list_users = $this->db
                ->select('user_init, sum(total_money) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('user_init')
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $seq = 1;
            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('STT');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Tên thu ngân');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Tổng số đơn');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Tổng chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng SP');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tổng nợ');

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');

            $rowCount = 2;
            foreach ((array)$list_users as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($seq++);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['user_init']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_discount']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_money']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_quantity']));
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_debt']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');

                $rowCount++;
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($rowCount - 1))->applyFromArray($BStyle, true);
        } else if ($option['type'] == 4) {
            $list_sales = $this->db
                ->select('user_init, sum(sale_id) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('sale_id')
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $seq = 1;
            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('STT');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Tên NVBH');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Tổng số đơn');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Tổng chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng SP');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tổng nợ');

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');
            $rowCount = 2;
            foreach ((array)$list_sales as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($seq++);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['sale_id']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_discount']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_money']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_quantity']));
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_debt']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');

                $rowCount++;
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($rowCount - 1))->applyFromArray($BStyle, true);
        } else if ($option['type'] == 5) {
            $list_stores = $this->db
                ->select('store_id, sum(sale_id) as total_money,count(*) as total_order, sum(total_quantity) as total_quantity, sum(total_discount) as total_discount, sum(lack) as total_debt')
                ->from('orders')
                ->order_by('created', 'desc')
                ->where(['deleted' => 0, 'order_status' => 1])
                ->group_by('store_id')
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $seq = 1;
            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('STT');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Tên cửa hàng');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Tổng số đơn');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Tổng chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng SP');
            $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tổng nợ');

            $objPHPExcel->getActiveSheet()->getStyle('A1:G1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');
            $rowCount = 2;
            foreach ((array)$list_stores as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($seq++);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['total_order']);
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_discount']));
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_money']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_quantity']));
                $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_debt']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':G' . $rowCount)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');

                $rowCount++;
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );
            $objPHPExcel->getActiveSheet()->getStyle('A1:G' . ($rowCount - 1))->applyFromArray($BStyle, true);
        } else if ($option['type'] == 6) {
            $list_products = $this->db
                ->select('product_id, prd_name, prd_code, sum(total_money) as total_money, sum(output) as total_quantity, sum(discount) as total_discount')
                ->from('report')
                ->join('products', 'report.product_id=products.ID')
                ->order_by('report.created', 'desc')
                ->where(['report.deleted' => 0])
                ->where('type', 3)
                ->group_by('product_id')
                ->get()
                ->result_array();


            $total_quantity = 0;
            $total_order = 0;
            $fileName = 'DoanhSo-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


            $objPHPExcel = new Spreadsheet();
            $objPHPExcel->setActiveSheetIndex(0);

            $seq = 1;
            $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('STT');
            $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Mã SP');
            $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Tên SP');
            $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('SL bán');
            $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Chiết khấu');
            $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tổng tiền');
            $objPHPExcel->getActiveSheet()->getStyle('A1:F1')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setARGB('F8F800');

            $rowCount = 2;
            foreach ((array)$list_products as $element) {
                $total_order++;
                $total_quantity += $element['total_quantity'];
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($seq++);
                $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(($element['prd_code']));
                $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(($element['prd_name']));
                $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($element['total_quantity']);
                $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_discount']));
                $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_encode_currency_format($element['total_money']));
                $objPHPExcel->getActiveSheet()->getStyle('A' . $rowCount . ':F' . $rowCount)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('D9D9D9');

                $rowCount++;
            }

            foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
                $objPHPExcel->getActiveSheet()
                    ->getColumnDimension($col, true)
                    ->setAutoSize(true);
            }

            $BStyle = array(
                'borders' => array(
                    'allborders' => array(
                        'style' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
                    )
                )
            );

            $objPHPExcel->getActiveSheet()->getStyle('A1:F' . ($rowCount - 1))->applyFromArray($BStyle, true);
        }

        $objWriter = new Xlsx($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH . $fileName);

        header("Content-Type: application/vnd.ms-excel");
        echo $this->messages = (HTTP_UPLOAD_IMPORT_PATH . $fileName);
    }
}
