<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Orders extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(2, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $data['seo']['title'] = "Phần mềm quản lý bán hàng";
            $data['data']['user'] = $this->auth;
            $data['template'] = 'order/index';
            $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
            $data['data']['customers'] = $this->db->from('customers')->get()->result_array();
            $data['data']['store_id'] = $this->auth['store_id'];
            $this->load->view('layout/index', isset($data) ? $data : null);
        }
    }

    public function cms_print_order()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_template['content'] = str_replace("{page_break}", '<div style="display: block; page-break-before: always;"></div>', $data_template['content']);

        $data_order = $this->db->from('orders')->where('ID', $data_post['id_order'])->get()->row_array();
        $customer_name = '';
        $customer_phone = '';
        $customer_address = '';
        $customer_code = '';
        $debt = 0;
        if ($data_order['customer_id'] > 0) {
            $customer_name = cms_getNamecustomerbyID($data_order['customer_id']);
            $customer_code = cms_getCodecustomerbyID($data_order['customer_id']);
            $customer_phone = cms_getPhonecustomerbyID($data_order['customer_id']);
            $customer_address = cms_getAddresscustomerbyID($data_order['customer_id']);
            $order = $this->db
                ->select('sum(lack) as debt')
                ->from('orders')
                ->where(['deleted' => 0, 'lack >' => 0, 'customer_id' => $data_order['customer_id']])
                ->where_not_in('order_status', [0, 5])
                ->get()
                ->row_array();
            $debt = $order['debt'];
        }

        $user_name = '';
        if ($data_order['customer_id'] != null)
            $user_name = cms_getNameAuthbyID($data_order['user_init']);

        $ngayin = gmdate("H:i d/m/Y", time() + 7 * 3600);
        $nguoiin = cms_getUserNameAuthbyID($this->auth['id']);

        $data_template['content'] = str_replace("{Ngay_In}", $ngayin, $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_In}", $nguoiin, $data_template['content']);
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", cms_getNamestockbyID($data_order['store_id']), $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Xuat}", cms_ConvertDateTime($data_order['sell_date']), $data_template['content']);
        $data_template['content'] = str_replace("{Khach_Hang}", $customer_name, $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Khach_Hang}", $customer_code, $data_template['content']);
        $data_template['content'] = str_replace("{DT_Khach_Hang}", $customer_phone, $data_template['content']);
        $data_template['content'] = str_replace("{DC_Khach_Hang}", $customer_address, $data_template['content']);
        $data_template['content'] = str_replace("{Thu_Ngan}", $user_name, $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien_Hang}", cms_encode_currency_format($data_order['total_price']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_So_Luong}", $data_order['total_quantity'], $data_template['content']);
        $data_template['content'] = str_replace("{Chiet_Khau}", cms_encode_currency_format($data_order['coupon']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien}", cms_encode_currency_format($data_order['total_money']), $data_template['content']);
        $data_template['content'] = str_replace("{Khach_Dua}", cms_encode_currency_format($data_order['customer_pay']), $data_template['content']);
        $data_template['content'] = str_replace("{VAT}", ($data_order['vat'] . '%'), $data_template['content']);
        $data_template['content'] = str_replace("{NVBH}", (cms_getNameAuthbyID($data_order['sale_id'])), $data_template['content']);
        $data_template['content'] = str_replace("{Con_No}", cms_encode_currency_format($data_order['lack']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Don_Hang}", $data_order['output_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $data_order['notes'], $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", cms_convert_number_to_words($data_order['total_money']), $data_template['content']);
        $data_template['content'] = str_replace("{Cong_No}", cms_encode_currency_format($debt), $data_template['content']);
        $data_template['content'] = str_replace("{No_Cu}", cms_encode_currency_format($debt - $data_order['lack']), $data_template['content']);

        $detail = '';
        $detail2 = '';
        $detail5 = '';
        $number = 1;
        $total_after_discount = 0;
        $total_price = 0;
        $total_discount = 0;

        if (isset($data_order) && count($data_order)) {
            $list_products = json_decode($data_order['detail_order'], true);
            foreach ((array)$list_products as $product) {
                $prd = cms_finding_productbyID($product['id']);
                $quantity = $product['quantity'];
                $total = $quantity * $product['price'];
                $after_discount = $quantity * ($product['price'] - $product['discount']);
                $total_price += $total;
                $total_after_discount += $after_discount;
                $total_discount += ($quantity * $product['discount']);
                $detail = $detail . '<tr><td style="text-align:center;">' . $number . '</td><td  style="text-align:center;">' . $prd['prd_code'] . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '<br>' . (isset($product['note']) ? $product['note'] : '') . '</td><td style = "text-align:center">' . $quantity . '</td><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td><td style = "text-align:center">' . cms_convertserial($product['list_serial']) . '</td><td style = "text-align:center">' . $prd['infor'] . '</td><td  style="text-align:center;">' . cms_encode_currency_format($product['price']) . '</td><td style="text-align:center;">' . cms_encode_currency_format($total) . '</td></tr>';
                $detail2 = $detail2 . '
                <tr>
                    <td>' . $prd['prd_name'] . '<br>' . (isset($product['note']) ? $product['note'] : '') . '</td>
                    <td style = "text-align:center">' . $quantity . '</td>
                    <td style="text-align:center;">' . cms_encode_currency_format($total) . '</td>
                </tr>';
                $detail5 = $detail5 . '<tr><td style="text-align:center;">' . $number++ . '</td><td  style="text-align:center;">' . $prd['prd_code'] . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '<br>' . (isset($product['note']) ? $product['note'] : '') . '</td><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td><td style = "text-align:center">' . $quantity . '</td><td style = "text-align:center"></td><td style = "text-align:center"></td></tr>';
            }
        }

        $table = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Mã SP</strong></td>
                            <td style="text-align:center;"><strong>Tên SP</strong></td>
                            <td style="text-align:center;"><strong>SL</strong></td>
                            <td style="text-align:center;"><strong>ĐVT</strong></td>
                            <td style="text-align:center;"><strong>Serial</strong></td>
                            <td style="text-align:center;"><strong>Thông tin thêm</strong></td>
                            <td style="text-align:center;"><strong>Đơn giá</strong></td>
                            <td style="text-align:center;"><strong>Thành tiền</strong></td>
                        </tr>' . $detail . '
                    </tbody>
                 </table>';

        $table2 = '<table border="1" style="width:100%;font-size: 11px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>Tên SP</strong></td>
                            <td style="text-align:center;"><strong>SL</strong></td>
                            <td style="text-align:center;"><strong>Thành tiền</strong></td>
                        </tr>' . $detail2 . '
                    </tbody>
                 </table>';

        $table5 = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Mã SP</strong></td>
                            <td style="text-align:center;"><strong>Tên SP</strong></td>
                            <td style="text-align:center;"><strong>ĐVT</strong></td>
                            <td style="text-align:center;"><strong>SL</strong></td>
                            <td style="text-align:center;"><strong>Thực nhận</strong></td>
                            <td style="text-align:center;"><strong>Ghi chú</strong></td>
                        </tr>' . $detail5 . '
                    </tbody>
                 </table>';

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);
        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham2}", $table2, $data_template['content']);
        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham3}", $table, $data_template['content']);
        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham4}", $table, $data_template['content']);
        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham5}", $table5, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_paging_order($page = 1)
    {
        $option = $this->input->post('data');
        $order_id = array();

        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        if ($option['keyword'] != '') {
            $order1 = $this->db
                ->select('distinct(cms_orders.ID) as ID')
                ->from('orders')
                ->join('customers', 'customers.ID=orders.customer_id', 'LEFT')
                ->where("(output_code LIKE '%" . $option['keyword'] . "%' OR customer_email LIKE '%" . $option['keyword'] . "%' OR customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->result_array();

            if (isset($order1) && count($order1) > 0) {
                foreach ($order1 as $id) {
                    $order_id[] = $id['ID'];
                }
            } else {
                $order_id[] = 0;
            }

            $this->db->where_in('ID', $order_id);
        }

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_orders.user_init', $this->auth['id']);
        }

        if ($option['option1'] == '0') {
            $this->db->where('deleted', 0);
        } else if ($option['option1'] == '1') {
            $this->db->where('deleted', 1);
        } else if ($option['option1'] == '2') {
            $this->db->where('deleted', 0)->where('lack >', 0);
        }

        if ($option['option2'] >= 0) {
            $this->db->where('order_status', $option['option2']);
        }

        if ($option['option3'] >= 0) {
            $this->db->where('customer_id', $option['option3']);
        }

        if ($option['option4'] >= 0) {
            $this->db->where('store_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('sell_date >=', $option['date_from'])
                ->where('sell_date <=', $option['date_to']);
        }

        $total_orders = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) total_debt')
            ->from('orders')
            ->get()
            ->row_array();

        if ($option['keyword'] != '') {
            $this->db->where_in('ID', $order_id);
        }

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_orders.user_init', $this->auth['id']);
        }

        if ($option['option1'] == '0') {
            $this->db->where('deleted', 0);
        } else if ($option['option1'] == '1') {
            $this->db->where('deleted', 1);
        } else if ($option['option1'] == '2') {
            $this->db->where('deleted', 0)->where('lack >', 0);
        }

        if ($option['option2'] >= 0) {
            $this->db->where('order_status', $option['option2']);
        }

        if ($option['option3'] >= 0) {
            $this->db->where('customer_id', $option['option3']);
        }

        if ($option['option4'] >= 0) {
            $this->db->where('store_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('sell_date >=', $option['date_from'])
                ->where('sell_date <=', $option['date_to']);
        }

        $data['_list_orders'] = $this->db
            ->from('orders')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('sell_date', 'desc')
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_order';
        $config['total_rows'] = $total_orders['quantity'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option1'];
        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;

        $this->load->view('ajax/orders/list_orders', isset($data) ? $data : null);
    }

    public function cms_export_order()
    {
        $option = $this->input->post('data');

        $order_id = array();
        if ($option['keyword'] != '') {
            $order1 = $this->db
                ->select('distinct(cms_orders.ID) as ID')
                ->from('orders')
                ->join('customers', 'customers.ID=orders.customer_id', 'LEFT')
                ->where("(output_code LIKE '%" . $option['keyword'] . "%' OR customer_email LIKE '%" . $option['keyword'] . "%' OR customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->result_array();

            if (isset($order1) && count($order1) > 0) {
                foreach ($order1 as $id) {
                    $order_id[] = $id['ID'];
                }
            } else {
                $order_id[] = 0;
            }

            $this->db->where_in('ID', $order_id);
        }

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_orders.user_init', $this->auth['id']);
        }

        if ($option['option1'] == '0') {
            $this->db->where('deleted', 0);
        } else if ($option['option1'] == '1') {
            $this->db->where('deleted', 1);
        } else if ($option['option1'] == '2') {
            $this->db->where('deleted', 0)->where('lack >', 0);
        }

        if ($option['option2'] >= 0) {
            $this->db->where('order_status', $option['option2']);
        }

        if ($option['option3'] >= 0) {
            $this->db->where('customer_id', $option['option3']);
        }

        if ($option['option4'] >= 0) {
            $this->db->where('store_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('sell_date >=', $option['date_from'])
                ->where('sell_date <=', $option['date_to']);
        }

        $data['_list_orders'] = $this->db
            ->from('orders')
            ->order_by('created', 'desc')
            ->get()
            ->result_array();


        $fileName = 'DonHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Mã đơn hàng');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Kho xuất');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ngày xuất');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Thu ngân');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Khách hàng');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tình trạng');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tổng SL');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Tổng tiền');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Nợ');
        $rowCount = 2;
        foreach ((array)$data['_list_orders'] as $element) {
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['output_code']);
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_ConvertDateTime($element['sell_date']));
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['user_init']));
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNamecustomerbyID($element['customer_id']));
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_getNamestatusbyID($element['order_status']));
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($element['total_quantity']);
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($element['total_money']);
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue($element['lack']);
            $rowCount++;
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

    public function cms_del_temp_order($id)
    {
        if ($this->auth == null || !in_array(13, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
            $store_id = $order['store_id'];
            $this->db->trans_begin();
            $user_init = $this->auth['id'];
            if (isset($order) && count($order)) {
                if (!in_array($order['order_status'], [0, 5])) {
                    $list_products = json_decode($order['detail_order'], true);
                    cms_input_inventory_and_serial($list_products, $store_id);

                    $this->db->where(['transaction_id' => $id, 'store_id' => $store_id])->where_in('type', [3, 7])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);

                    $this->db->where('order_id', $id)->update('receipt', ['deleted' => 1, 'user_upd' => $user_init]);
                    $this->db->where('ID', $id)->update('orders', ['deleted' => 1, 'user_upd' => $user_init]);
                    $this->cms_del_temp_input($id);
                    if ($order['customer_id'] > 0) {
                        cms_updatecustomerdebtbycustomerid($order['customer_id']);
                    }
                } else {
                    $this->db->where('ID', $id)->update('orders', ['deleted' => 1, 'user_upd' => $user_init]);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        }
    }

    public function cms_del_temp_input($order_id)
    {
        if ($this->auth == null || !in_array(16, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $order_id = (int)$order_id;
        $input_list = $this->db->from('input')->where(['order_id' => $order_id, 'deleted' => 0, 'input_status' => 1])->get()->result_array();
        foreach ((array)$input_list as $input) {
            $store_id = $input['store_id'];
            $user_init = $this->auth['id'];
            if (isset($input) && count($input)) {
                $list_products = json_decode($input['detail_input'], true);
                $resu = cms_output_inventory_and_serial($list_products, $store_id);
                if ($resu != 1) {
                    $this->db->trans_rollback();
                    echo $this->messages = $resu;
                    return;
                }
                $this->db->where(['transaction_id' => $input['ID'], 'type' => 2, 'store_id' => $store_id])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('input_id', $input['ID'])->update('payment', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('ID', $input['ID'])->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
            }
        }
    }

    public function cms_change_status_order($id)
    {
        $data = $this->input->post('data');
        $id = (int)$id;
        $user_init = $this->auth['id'];
        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
        if ($order['order_status'] == 5 || $order['order_status'] == 0) {
            echo $this->messages = "0";
        } else if ($data['order_status'] == 5) {
            $store_id = $order['store_id'];
            $this->db->trans_begin();
            if (isset($order) && count($order)) {
                $list_products = json_decode($order['detail_order'], true);
                cms_input_inventory_and_serial($list_products, $store_id);

                $this->db->where(['transaction_id' => $id, 'store_id' => $store_id])->where_in('type', [3, 7])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where('ID', $id)->update('orders', ['order_status' => 5, 'user_upd' => $user_init]);
                $this->cms_del_temp_input($id);

                if ($order['customer_id'] > 0) {
                    cms_updatecustomerdebtbycustomerid($order['customer_id']);
                }
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        } else {
            $this->db->where('ID', $id)->update('orders', ['order_status' => $data['order_status'], 'user_upd' => $user_init]);
            echo $this->messages = "1";
        }
    }

    public function cms_delete_receipt_in_order($id)
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $receipt = $this->db->from('receipt')->where(['ID' => $id, 'deleted' => 0, 'type_id' => 3])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($receipt) && count($receipt)) {
            $order = $this->db->select('customer_pay,lack')->from('orders')->where(['ID' => $receipt['order_id'], 'deleted' => 0])->get()->row_array();
            $order['customer_pay'] = $order['customer_pay'] - $receipt['total_money'];
            $order['lack'] = $order['lack'] + $receipt['total_money'];
            $order['user_upd'] = $user_id;
            $this->db->where('ID', $receipt['order_id'])->update('orders', $order);
            $this->db->where('ID', $id)->update('receipt', ['deleted' => 1, 'user_upd' => $user_id]);

            $this->cms_update_report($receipt['order_id']);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_update_report($order_id)
    {
        $order = $this->db->from('orders')->where(['ID' => $order_id, 'deleted' => 0])->get()->row_array();
        if (isset($order) && count($order)) {
            $order_detail = json_decode($order['detail_order'], true);

            if ($order['vat'] == 10) {
                $order['total_money'] = $order['total_money'] / 1.1;
            } else if ($order['vat'] == 5) {
                $order['total_money'] = $order['total_money'] / 1.05;
            }

            if ($order['total_price'] == 0) {
                $percent_discount = 0;
            } else {
                $percent_discount = $order['coupon'] / $order['total_price'];
            }

            if ($order['total_money'] == 0) {
                $percent = 0;
            } else {
                $percent = $order['lack'] / $order['total_money'];
            }

            $count = 0;
            foreach ((array)$order_detail as $item) {
                $total_money = ($item['price'] - $item['price'] * $percent_discount) * $item['quantity'];
                $report = array();
                $report['product_debt'] = $total_money * $percent;
                $this->db->where(['type' => 3, 'transaction_id' => $order_id, 'deleted' => 0, 'product_id' => $item['id'], 'price' => $item['price']])->update('report', $report);

                $count++;
            }
        }
    }

    public function cms_del_order($id)
    {
        if ($this->auth == null || !in_array(13, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        $this->db->trans_begin();
        if (isset($order) && count($order)) {
            $this->db->where('ID', $id)->update('orders', ['deleted' => 2, 'user_upd' => $this->auth['id']]);
        } else
            echo $this->messages = "0";

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function cms_save_order_return($store_id)
    {
        if ($this->auth['store_id'] == $store_id) {
            $input = $this->input->post('return');
            $check_order = $this->db->where('ID', $input['order_id'])->from('orders')->get()->row_array();
            if (empty($check_order)) {
                echo $this->messages = "0";
                return;
            }

            $input['supplier_id'] = $check_order['customer_id'];
            $input['input_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $total_price = 0;
            $total_quantity = 0;
            $this->db->trans_begin();
            $user_init = $this->auth['id'];
            $input['input_status'] = 1;
            $input['total_origin_price_return'] = 0;
            $detail_input = array();
            foreach ((array)$input['detail_input'] as $item) {
                $product = cms_finding_productbyID($item['id']);
                $canreturn = $this->db->select('ID,quantity,price')->from('canreturn')->where(['order_id' => $input['order_id'], 'ID' => $item['return_id']])->get()->row_array();
                if (empty($canreturn) || $canreturn['quantity'] < 1 || $canreturn['quantity'] < $item['quantity']) {
                    $this->db->trans_rollback();
                    echo $this->messages = "0";
                    return;
                } else {
                    $canreturn['quantity'] = $canreturn['quantity'] - $item['quantity'];
                    $canreturn['user_upd'] = $user_init;
                    $this->db->where(['order_id' => $input['order_id'], 'ID' => $item['return_id']])->update('canreturn', $canreturn);

                    if ($product['prd_serial'] == 1) {
                        if (!is_array($item['list_serial']))
                            $item['list_serial'] = explode(",", $item['list_serial']);

                        foreach ((array)$item['list_serial'] as $serial) {
                            $canreturn_serial = $this->db->select('ID')->from('canreturn_serial')->where(['canreturn_id' => $canreturn['ID'], 'serial' => $serial])->get()->row_array();
                            if (isset($canreturn_serial) && count($canreturn_serial)) {
                                $this->db->where('ID', $canreturn_serial['ID'])->delete('canreturn_serial');
                            } else {
                                $this->db->trans_rollback();
                                echo $this->messages = "Serial " . $serial . ' không tồn tại. Vui lòng kiểm tra lại';
                                return;
                            }
                        }
                    }
                }

                $total_price += ($item['price'] * $item['quantity']);
                $total_quantity += $item['quantity'];

                $report_temp = $this->db->select('output,origin_price')->from('report')->where(['transaction_id' => $input['order_id'], 'type' => 3, 'report_expire' => $item['expire'], 'product_id' => $item['id']])->get()->row_array();
                $input['total_origin_price_return'] += $item['quantity'] * ($report_temp['origin_price'] / $report_temp['output']);

                $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);

                $detail_input[] = $item;
            }

            cms_input_inventory_and_serial($input['detail_input'], $store_id);

            $input['total_quantity'] = $total_quantity;
            $input['total_price'] = $total_price;
            $input['total_money'] = $total_price;
            $input['payed'] = $total_price;
            $input['store_id'] = $store_id;
            $input['user_init'] = $this->auth['id'];
            $input['detail_input'] = json_encode($detail_input);

            $this->db->select_max('input_code')->like('input_code', 'PNT')->where('order_id >', 0);
            $max_input_code = $this->db->get('input')->row();
            $max_code = (int)(str_replace('PNT', '', $max_input_code->input_code)) + 1;
            if ($max_code < 10)
                $input['input_code'] = 'PNT00000' . ($max_code);
            else if ($max_code < 100)
                $input['input_code'] = 'PNT0000' . ($max_code);
            else if ($max_code < 1000)
                $input['input_code'] = 'PNT000' . ($max_code);
            else if ($max_code < 10000)
                $input['input_code'] = 'PNT00' . ($max_code);
            else if ($max_code < 100000)
                $input['input_code'] = 'PNT0' . ($max_code);
            else if ($max_code < 1000000)
                $input['input_code'] = 'PNT' . ($max_code);

            $input['canreturn'] = 0;
            $this->db->insert('input', $input);
            $id = $this->db->insert_id();

            $payment = array();
            $payment['input_id'] = $id;
            $this->db->select_max('ID');
            $max_payment_code = $this->db->get('payment')->row();
            $max_code = (int)($max_payment_code->ID) + 1;
            if ($max_code < 10)
                $payment['payment_code'] = 'PC000000' . ($max_code);
            else if ($max_code < 100)
                $payment['payment_code'] = 'PC00000' . ($max_code);
            else if ($max_code < 1000)
                $payment['payment_code'] = 'PC0000' . ($max_code);
            else if ($max_code < 10000)
                $payment['payment_code'] = 'PC000' . ($max_code);
            else if ($max_code < 100000)
                $payment['payment_code'] = 'PC00' . ($max_code);
            else if ($max_code < 1000000)
                $payment['payment_code'] = 'PC0' . ($max_code);
            else if ($max_code < 10000000)
                $payment['payment_code'] = 'PC' . ($max_code);

            $payment['type_id'] = 2;
            $payment['store_id'] = $store_id;
            $payment['payment_date'] = $input['input_date'];
            $payment['notes'] = $input['notes'];
            $payment['payment_method'] = $input['payment_method'];
            $payment['total_money'] = $total_price;
            $payment['user_init'] = $input['user_init'];
            $this->db->insert('payment', $payment);

            $temp = array();
            $temp['transaction_code'] = $input['input_code'];
            $temp['transaction_id'] = $id;
            $temp['supplier_id'] = isset($input['supplier_id']) ? $input['supplier_id'] : 0;
            $temp['date'] = $input['input_date'];
            $temp['notes'] = $input['notes'];
            $temp['user_init'] = $input['user_init'];
            $temp['type'] = 6;
            $temp['store_id'] = $store_id;
            foreach ((array)$detail_input as $item) {
                $report = $temp;
                $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $item['id']])->get()->row_array();
                $report['product_id'] = $item['id'];
                $report['price'] = $item['price'];
                $report['input'] = $item['quantity'];
                $report['stock'] = isset($stock['quantity']) ? $stock['quantity'] : 0;
                $report['total_money'] = $report['price'] * $report['input'];
                $report['report_expire'] = $item['expire'];
                $report['report_serial'] = $item['list_serial'];
                $this->db->insert('report', $report);
            }

            $check = $this->db
                ->select('sum(quantity) as total_quantity')
                ->from('canreturn')
                ->where('order_id', $input['order_id'])
                ->get()
                ->row_array();
            if (empty($check) || $check['total_quantity'] < 1) {
                $this->db->where('ID', $input['order_id'])->update('orders', ['canreturn' => 0, 'user_upd' => $user_init]);
            }

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = $id;
            }

        } else
            echo $this->messages = "0";
    }

    public function cms_return_order($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0, 'order_status' => 1, 'canreturn' => 1])->get()->row_array();
        if (isset($order) && count($order)) {
            $detail_order = $this->db
                ->select('cms_canreturn.ID,product_id,prd_code,prd_name,quantity,price,canreturn_expire as expire,prd_serial')
                ->from('canreturn')
                ->join('products', 'products.ID=canreturn.product_id', 'INNER')
                ->where(['order_id' => $order['ID'], 'quantity >' => 0])
                ->get()
                ->result_array();
        }
        $data['data']['_order'] = $order;
        $data['data']['_detail_order'] = $detail_order;
        $this->load->view('ajax/orders/return', isset($data) ? $data : null);
    }

    public function cms_vsell_order()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $data['user_id'] = $this->auth['id'];
            $data['data'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();

            $this->load->view('ajax/orders/sell_bill', isset($data) ? $data : null);
        }

    }

    public function cms_detail_order()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $id = $this->input->post('id');
        $order = $this->db->from('orders')->where('ID', $id)->get()->row_array();
        $receipt = $this->db->from('receipt')->where(['order_id' => $id, 'type_id' => 3, 'deleted' => 0])->get()->result_array();
        $data['_list_products'] = array();

        if (isset($order) && count($order)) {
            $list_products = json_decode($order['detail_order'], true);
            foreach ((array)$list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';
                $_product['item_discount'] = isset($product['item_discount']) ? $product['item_discount'] . '%' : '';
                $_product['note'] = isset($product['note']) ? $product['note'] : '';
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_order'] = $order;
        $data['data']['_receipt'] = $receipt;
        $this->load->view('ajax/orders/detail_order', isset($data) ? $data : null);
    }

    public function cms_export_excel($order_id)
    {
        $order = $this->db
            ->from('orders')
            ->where('ID', $order_id)
            ->get()
            ->row_array();

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'PhieuXuatHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Tên SP');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Mã SP');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Số lượng');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Đơn giá');

        $detail_order = json_decode($order['detail_order'], true);

        $rowCount = 2;
        foreach ((array)$detail_order as $element) {
            $product = $this->db
                ->from('products')
                ->where('ID', $element['id'])
                ->get()
                ->row_array();
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($product['prd_name']);
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($product['prd_code']);
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['quantity']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($product['prd_sell_price']);
            $rowCount++;
        }
        $objWriter = new Xlsx($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        echo $this->messages = (HTTP_UPLOAD_IMPORT_PATH . $fileName);
    }

    public function cms_edit_order()
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = $this->input->post('id');

            $order = $this->db->from('orders')->where(['ID' => $id, 'deleted' => 0, 'order_status <' => 5])->get()->row_array();
            $data['_list_products'] = array();
            $data['data']['user'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();
            if (isset($order) && count($order)) {
                $list_products = json_decode($order['detail_order'], true);
                foreach ((array)$list_products as $product) {
                    $_product = cms_finding_productbyID($product['id']);
                    $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                    $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                    $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                    $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';
                    $_product['note'] = isset($product['note']) ? $product['note'] : '';
                    $_product['discount'] = isset($product['discount']) ? $product['discount'] : '';
                    $_product['item_discount'] = isset($product['item_discount']) ? $product['item_discount'] : '';
                    $_product['percent'] = isset($product['percent']) ? $product['percent'] : '';

                    if ($_product['prd_allownegative'] == 1)
                        $_product['list_expire'] = $this->db->from('inventory')->where('store_id', $this->auth['store_id'])->where('product_id', $product['id'])->get()->result_array();
                    else
                        $_product['list_expire'] = $this->db->from('inventory')->where('store_id', $this->auth['store_id'])->where('product_id', $product['id'])->where('quantity >', 0)->get()->result_array();

                    $data['_list_products'][] = $_product;
                }
            }

            $data['data']['_order'] = $order;
            $this->load->view('ajax/orders/edit_order', isset($data) ? $data : null);
        }
    }

    public function cms_autocomplete_products($customer_id, $store_id)
    {
        $data = $this->input->get('term');
        $store_id = $this->auth['store_id'];
        if ($customer_id == 0) {
            $products = $this->db
                ->select('prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                ->from('products')->where('prd_serial', 0)
                ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                ->where('(prd_descriptions like "%' . $data . '%" or prd_code like "%' . $data . '%" or prd_name like "%' . $data . '%") and prd_status = 1 and deleted =0 ')
                ->group_by('product_id')
                ->get()
                ->result_array();
        } else {
            $customer = $this->db->from('customers')->where('ID', $customer_id)->get()->row_array();
            if (empty($customer) || $customer['customer_group'] == 0) {
                $products = $this->db
                    ->select('prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                    ->from('products')->where('prd_serial', 0)
                    ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                    ->where('(prd_descriptions like "%' . $data . '%" or prd_code like "%' . $data . '%" or prd_name like "%' . $data . '%") and prd_status = 1 and deleted =0 ')
                    ->group_by('product_id')
                    ->get()
                    ->result_array();
            } else {
                $products = $this->db
                    ->select('prd_sell_price2 as prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                    ->from('products')->where('prd_serial', 0)
                    ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                    ->where('(prd_descriptions like "%' . $data . '%" or prd_code like "%' . $data . '%" or prd_name like "%' . $data . '%") and prd_status = 1 and deleted =0 ')
                    ->group_by('product_id')
                    ->get()
                    ->result_array();
            }
        }

        if (empty($products)) {
            if ($customer_id == 0) {
                $products = $this->db
                    ->select('prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                    ->from('products')->where('prd_serial', 0)
                    ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                    ->where('(prd_status = 1 and deleted =0)')
                    ->where('MATCH (alias_search) AGAINST ("' . $data . '")', NULL, FALSE)
                    ->group_by('product_id')
                    ->get()
                    ->result_array();
            } else {
                $customer = $this->db->from('customers')->where('ID', $customer_id)->get()->row_array();
                if (empty($customer) || $customer['customer_group'] == 0) {
                    $products = $this->db
                        ->select('prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                        ->from('products')->where('prd_serial', 0)
                        ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                        ->where('(prd_status = 1 and deleted =0)')
                        ->where('MATCH (alias_search) AGAINST ("' . $data . '")', NULL, FALSE)
                        ->group_by('product_id')
                        ->get()
                        ->result_array();
                } else {
                    $products = $this->db
                        ->select('prd_sell_price2 as prd_sell_price,ID,prd_name,prd_code,sum(quantity) as quantity')
                        ->from('products')->where('prd_serial', 0)
                        ->join('inventory', 'inventory.product_id = products.ID and store_id =' . $store_id, 'LEFT')
                        ->where('(prd_status = 1 and deleted =0)')
                        ->where('MATCH (alias_search) AGAINST ("' . $data . '")', NULL, FALSE)
                        ->group_by('product_id')
                        ->get()
                        ->result_array();
                }
            }
        }

        echo json_encode($products);
    }

    public function cms_check_barcode($keyword)
    {
        $products = $this->db->from('products')->where('prd_serial', 0)->where(array('prd_status' => '1', 'deleted' => '0', 'prd_code' => $keyword))->get()->result_array();
        if (count($products) == 1)
            echo $products[0]['ID'];
        else
            echo 0;
    }

    public function cms_search_box_customer()
    {
        $data = $this->input->post('data');
        if (in_array(28, $this->auth['group_permission'])) {
            $customer =
                $this->db
                    ->select('cms_customers.ID,customer_code,customer_name,customer_phone,count(cms_orders.ID) as count,sum(total_money) as total_money')
                    ->like('customer_name', $data['keyword'])
                    ->or_like('customer_phone', $data['keyword'])
                    ->or_like('customer_email', $data['keyword'])
                    ->or_like('customer_code', $data['keyword'])
                    ->from('customers')
                    ->join('orders', 'orders.customer_id = customers.ID and cms_orders.deleted = 0 and order_status != 5', 'LEFT')
                    ->group_by('cms_customers.ID')
                    ->get()
                    ->result_array();
        } else {
            $customer = $this->db
                ->select('cms_customers.ID,customer_code,customer_name,customer_phone,count(cms_orders.ID) as count,sum(total_money) as total_money')
                ->where('(customer_name like "%' . $data['keyword'] . '%" or customer_phone like "%' . $data['keyword'] . '%" or customer_email like "%' . $data['keyword'] . '%" or customer_code like "%' . $data['keyword'] . '%") and cms_customers.user_init =' . $this->auth['id'])
                ->from('customers')
                ->join('orders', 'orders.customer_id = customers.ID and cms_orders.deleted = 0 and order_status != 5', 'LEFT')
                ->group_by('cms_customers.ID')
                ->get()
                ->result_array();
        }

        $data['data']['customers'] = $customer;
        $this->load->view('ajax/orders/search_box_customer', isset($data) ? $data : null);
    }

    public function cms_select_product($customer_id)
    {
        $id = $this->input->post('id');
        $seq = $this->input->post('seq');
        if ($customer_id == 0) {
            $product = $this->db
                ->select('products.ID,prd_code,prd_unit_name,position,prd_name, prd_sell_price, prd_image_url,prd_edit_price,prd_allownegative,prd_serial,prd_descriptions')
                ->from('products')->where('prd_serial', 0)
                ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
                ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
                ->get()
                ->row_array();
        } else {
            $customer = $this->db->from('customers')->where('ID', $customer_id)->get()->row_array();
            if (empty($customer) || $customer['customer_group'] == 0) {
                $product = $this->db
                    ->select('products.ID,prd_code,prd_unit_name,position,prd_name, prd_sell_price, prd_image_url,prd_edit_price,prd_allownegative,prd_serial,prd_descriptions')
                    ->from('products')->where('prd_serial', 0)
                    ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
                    ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
                    ->get()
                    ->row_array();
            } else {
                $product = $this->db
                    ->select('products.ID,prd_code,prd_unit_name,position,prd_name, prd_sell_price2 as prd_sell_price, prd_image_url,prd_edit_price,prd_allownegative,prd_serial,prd_descriptions')
                    ->from('products')->where('prd_serial', 0)
                    ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
                    ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
                    ->get()
                    ->row_array();
            }
        }

        if ($product['prd_allownegative'] == 1)
            $list_expire = $this->db->from('inventory')->where('store_id', $this->auth['store_id'])->where('product_id', $id)->order_by('expire_date', 'asc')->get()->result_array();
        else
            $list_expire = $this->db->from('inventory')->where('store_id', $this->auth['store_id'])->where('product_id', $id)->order_by('expire_date', 'asc')->where('quantity >', 0)->get()->result_array();

        $list_serial = $this->db->select('distinct(serial)')->from('inventory')->join('inventory_serial', 'inventory.ID_temp=inventory_serial.inventory_id', 'INNER')->where('inventory_serial.quantity >', 0)->where('inventory.quantity >', 0)->where('inventory.product_id', $id)->where('store_id', $this->auth['store_id'])->get()->result_array();

        if (isset($product) && count($product) != 0) {
            ob_start(); ?>
            <tr data-id="<?php echo $product['ID']; ?>">
                <td class="text-center seq hidden-xs"><?php echo $seq; ?></td>
                <td class="text-left hidden-xs"><?php echo $product['prd_code']; ?></td>
                <td class="text-left"><?php echo $product['prd_name']; ?>
                    <input type="text" class="form-control note_product_order" placeholder="Ghi chú" value="">
                </td>
                <td class="hidden-xs"><?php echo $product['position']; ?></td>
                <td class="text-left hidden"><?php echo $product['prd_descriptions']; ?></td>
                <td class="text-center zoomin hidden-xs"><img height="30"
                                                              src="public/templates/uploads/<?php echo cms_show_image($product['prd_image_url']); ?>">
                </td>

                <td class="<?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">
                    <select class="form-control expire">
                        <?php if (isset($list_expire) && count($list_expire)) {
                            foreach ((array)$list_expire as $expire) {
                                ?>
                                <option value="<?php echo $expire['inventory_expire'] ?>"><?php echo cms_ConvertDate($expire['inventory_expire']) ?></option>
                                <?php
                            }
                        } else {
                            ?>
                            <option value=""></option>
                            <?php
                        } ?>

                    </select>
                </td>

                <td class="text-center" style="max-width: 100px;"><input style="min-width:50px;max-height: 34px;"
                                                                         type="text" <?php echo ($product['prd_serial'] == 1) ? 'disabled' : ''; ?>
                                                                         class="txtNumber form-control quantity_product_order text-center"
                                                                         value="<?php echo ($product['prd_serial'] == 1) ? 0 : 1; ?>">
                </td>

                <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>

                <td class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">
                    <?php
                    foreach ((array)$list_serial as $serial) {
                        ?>
                        <input type="checkbox" class="serial checkbox" onclick="cms_load_infor_order()"
                               style="display: inherit" value="<?php echo $serial['serial']; ?>">
                        <?php echo $serial['serial']; ?>
                        <br>
                        <?php
                    }
                    ?>
                </td>

                <td style="max-width: 100px;" class="text-center output">
                    <div>
                        <input type="text" <?php if ($product['prd_edit_price'] == 0) echo 'disabled'; ?>
                               style="min-width:80px;max-height: 34px;"
                               class="txtMoney form-control text-center price-order"
                               value="<?php echo cms_encode_currency_format($product['prd_sell_price']); ?>">
                        <i class="fa fas fa-gift bigger-120 href" style="line-height: 34px; padding-right: 2px;"
                           onclick="cms_show_discount_order(<?php echo $seq; ?>)"></i>
                    </div>

                    <span style="color: red" class="discount_show href"></span>
                    <div id="discount_order_<?php echo $seq; ?>" class="discount_order"
                         style="display: none;width: 280px;z-index: 9999">
                        <div class="col-md-12 text-center" style="line-height: 40px;background: #0B87C9;"
                             onclick="$('#discount_order_<?php echo $seq; ?>').toggle()">
                            <label style="color: white">Giảm giá</label>
                        </div>
                        <div class="col-md-12 col-xs-12" style="padding: 10px;line-height: 30px;">
                            <div class="col-md-4 text-left">
                                <label>Giảm</label>
                            </div>
                            <div class="col-md-8" style="display: flex">
                                <input type="text"
                                       class="txtNumber form-control toggle-discount-item-order_<?php echo $seq; ?> discount-item-percent-order discount_percent"
                                       placeholder="0%">

                                <input type="text"
                                       class="form-control toggle-discount-item-order_<?php echo $seq; ?> txtMoney discount-item-order discount_money"
                                       placeholder="0" style="display:none;">
                                <button onclick="cms_change_discount_item_order(<?php echo $seq; ?>)"
                                        style="display:none;"
                                        class="toggle-discount-item-order_<?php echo $seq; ?> btn btn-success">
                                    vnđ
                                </button>

                                <button onclick="cms_change_discount_item_order(<?php echo $seq; ?>)"
                                        style="display:none;"
                                        class="toggle-discount-item-order_<?php echo $seq; ?> btn btn-success">
                                    %
                                </button>
                            </div>
                        </div>
                    </div>
                </td>
                <td class="text-center total-money"><?php echo cms_encode_currency_format($product['prd_sell_price']); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-order"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function cms_select_product_order_return()
    {
        $id = $this->input->post('id');
        $seq = $this->input->post('seq');
        $product = $this->db
            ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_edit_price')
            ->from('products')
            ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
            ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
            ->get()
            ->row_array();
        if (isset($product) && count($product) != 0) {
            ob_start(); ?>
            <tr data-id="<?php echo $product['ID']; ?>">
                <td class="text-center seq hidden-xs"><?php echo $seq; ?></td>
                <td class="text-left hidden-xs"><?php echo $product['prd_code']; ?></td>
                <td class="text-left"><?php echo $product['prd_name']; ?></td>
                <td class="text-center zoomin hidden-xs"><img height="30"
                                                              src="public/templates/uploads/<?php echo cms_show_image($product['prd_image_url']); ?>">
                </td>
                <td class="text-center" style="max-width: 100px;"><input style="min-width:50px;max-height: 34px;"
                                                                         type="text"
                                                                         class="txtNumber form-control quantity_sell text-center"
                                                                         value="1"></td>
                <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>
                <td style="max-width: 100px;" class="text-center output">
                    <input type="text" <?php if ($product['prd_edit_price'] == 0) echo 'disabled'; ?>
                           style="min-width:80px;max-height: 34px;"
                           class="txtMoney form-control text-center price_sell"
                           value="<?php echo cms_encode_currency_format($product['prd_sell_price']); ?>"></td>
                <td class="text-center total_price_sell"><?php echo cms_encode_currency_format($product['prd_sell_price']); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-sell"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function save_receipt_order()
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $receipt = $this->input->post('data');

        $order = $this->db->from('orders')->where(['ID' => $receipt['order_id'], 'deleted' => 0])->get()->row_array();
        if ($order['lack'] > 0) {
            $this->db->trans_begin();
            $update_order = array();
            if ($receipt['total_money'] > $order['lack']) {
                $receipt['total_money'] = $order['lack'];
                $update_order['customer_pay'] = $order['customer_pay'] + $order['lack'];
                $update_order['lack'] = 0;
                $update_order['user_upd'] = $this->auth['id'];
            } else {
                $update_order['customer_pay'] = $order['customer_pay'] + $receipt['total_money'];
                $update_order['lack'] = $order['lack'] - $receipt['total_money'];
                $update_order['user_upd'] = $this->auth['id'];
            }
            $this->db->where(['ID' => $receipt['order_id'], 'deleted' => 0])->update('orders', $update_order);

            if (empty($receipt['receipt_date'])) {
                $receipt['receipt_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $receipt['receipt_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $receipt['receipt_date'])) + 7 * 3600);
            }
            $receipt['store_id'] = $this->auth['store_id'];
            $receipt['user_init'] = $this->auth['id'];
            $receipt['type_id'] = 3;
            $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
            $max_receipt_code = $this->db->get('receipt')->row();
            $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
            if ($max_code < 10)
                $receipt['receipt_code'] = 'PT000000' . ($max_code);
            else if ($max_code < 100)
                $receipt['receipt_code'] = 'PT00000' . ($max_code);
            else if ($max_code < 1000)
                $receipt['receipt_code'] = 'PT0000' . ($max_code);
            else if ($max_code < 10000)
                $receipt['receipt_code'] = 'PT000' . ($max_code);
            else if ($max_code < 100000)
                $receipt['receipt_code'] = 'PT00' . ($max_code);
            else if ($max_code < 1000000)
                $receipt['receipt_code'] = 'PT0' . ($max_code);
            else if ($max_code < 10000000)
                $receipt['receipt_code'] = 'PT' . ($max_code);

            $this->db->insert('receipt', $receipt);
            if ($order['customer_id'] > 0)
                cms_updatecustomerdebtbycustomerid($order['customer_id']);

            $this->cms_update_report($receipt['order_id']);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = 1;
            }
        } else
            echo $this->messages = "0";
    }

    public function cms_save_orders($store_id)
    {
        $order = $this->input->post('data');
        $created_from = gmdate("Y-m-d H:i:s", time() + 7 * 3600 - 5);
        $created_to = gmdate("Y-m-d H:i:s", time() + 7 * 3600 + 5);
        $check_recent = $this->db->from('orders')->where('store_id', $store_id)->where('customer_id', $order['customer_id'])->where('user_init', $this->auth['id'])->where('created >', $created_from)->where('created <', $created_to)->count_all_results();
        if ($check_recent > 0) {
            echo $this->messages = 'Vui lòng chờ 5 giây trước khi tạo đơn hàng tiếp theo';
            return;
        } else
            if ($store_id == $this->auth['store_id']) {
                if (empty($order['sell_date'])) {
                    $order['sell_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                } else {
                    $order['sell_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                }

                $this->db->trans_begin();
                $total_price = 0;
                $total_origin_price = 0;
                $total_quantity = 0;
                $total_discount = 0;

                foreach ((array)$order['detail_order'] as $item) {
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                    if ($product['prd_edit_price'] == 0) {
                        $item['price'] = $product['prd_sell_price'];
                    } elseif ($item['price'] != $product['prd_sell_price']) {
                        if ($order['customer_id'] > 0) {
                            $customer = $this->db->from('customers')->where('ID', $order['customer_id'])->get()->row_array();
                            if (empty($customer) || $customer['customer_group'] == 0) {
                                $this->db->where('ID', $item['id'])->update('products', ['prd_sell_price' => $item['price']]);
                            } else {
                                $this->db->where('ID', $item['id'])->update('products', ['prd_sell_price2' => $item['price']]);
                            }
                        } else {
                            $this->db->where('ID', $item['id'])->update('products', ['prd_sell_price' => $item['price']]);
                        }
                    }

                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_discount += ($item['discount']) * $item['quantity'];
                    $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                    $total_quantity += $item['quantity'];

                    $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);
                    $detail_order[] = $item;
                }

                if ($order['order_status'] == 1) {
                    $resu = cms_output_inventory_and_serial($order['detail_order'], $store_id);

                    if ($resu != 1) {
                        $this->db->trans_rollback();
                        echo $this->messages = $resu;
                        return;
                    }
                }

                if ($order['coupon'] == 'NaN')
                    $order['coupon'] = 0;

                if ($order['vat'] > 0)
                    $total_price = ($total_price + ($total_price * $order['vat']) / 100);

                $order['total_discount'] = $order['coupon'] + $total_discount;
                $order['total_price'] = $total_price;
                $order['total_origin_price'] = $total_origin_price;
                $order['total_money'] = $total_price - $order['coupon'];
                $order['total_quantity'] = $total_quantity;
                $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
                $order['user_init'] = $this->auth['id'];
                $order['store_id'] = $store_id;
                $order['detail_order'] = json_encode($detail_order);

                $this->db->select_max('output_code')->like('output_code', 'PX')->where('input_id', 0);
                $max_output_code = $this->db->get('orders')->row();
                $max_code = (int)(str_replace('PX', '', $max_output_code->output_code)) + 1;
                if ($max_code < 10)
                    $order['output_code'] = 'PX000000' . ($max_code);
                else if ($max_code < 100)
                    $order['output_code'] = 'PX00000' . ($max_code);
                else if ($max_code < 1000)
                    $order['output_code'] = 'PX0000' . ($max_code);
                else if ($max_code < 10000)
                    $order['output_code'] = 'PX000' . ($max_code);
                else if ($max_code < 100000)
                    $order['output_code'] = 'PX00' . ($max_code);
                else if ($max_code < 1000000)
                    $order['output_code'] = 'PX0' . ($max_code);
                else if ($max_code < 10000000)
                    $order['output_code'] = 'PX' . ($max_code);

                if ($order['sale_id'] == null)
                    $order['sale_id'] = 0;

                if ($order['customer_id'] < 1 && $order['lack'] > 0) {
                    $this->db->trans_rollback();
                    echo $this->messages = "-1";
                    return;
                }

                $order['commission_order'] = $this->auth['commission'];

                $this->db->insert('orders', $order);
                $id = $this->db->insert_id();

                if ($total_price == 0)
                    $percent_discount = 0;
                else
                    $percent_discount = $order['coupon'] / $total_price;

                if ($order['order_status'] == 1) {
                    $receipt = array();
                    $receipt['order_id'] = $id;
                    $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                    $max_receipt_code = $this->db->get('receipt')->row();
                    $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                    if ($max_code < 10)
                        $receipt['receipt_code'] = 'PT000000' . ($max_code);
                    else if ($max_code < 100)
                        $receipt['receipt_code'] = 'PT00000' . ($max_code);
                    else if ($max_code < 1000)
                        $receipt['receipt_code'] = 'PT0000' . ($max_code);
                    else if ($max_code < 10000)
                        $receipt['receipt_code'] = 'PT000' . ($max_code);
                    else if ($max_code < 100000)
                        $receipt['receipt_code'] = 'PT00' . ($max_code);
                    else if ($max_code < 1000000)
                        $receipt['receipt_code'] = 'PT0' . ($max_code);
                    else if ($max_code < 10000000)
                        $receipt['receipt_code'] = 'PT' . ($max_code);

                    $receipt['type_id'] = 3;
                    $receipt['store_id'] = $store_id;
                    $receipt['receipt_date'] = $order['sell_date'];
                    $receipt['notes'] = $order['notes'];
                    $receipt['receipt_method'] = $order['payment_method'];
                    $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                    $receipt['user_init'] = $order['user_init'];
                    $this->db->insert('receipt', $receipt);

                    $temp = array();
                    $temp['transaction_code'] = $order['output_code'];
                    $temp['transaction_id'] = $id;
                    $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
                    $temp['date'] = $order['sell_date'];
                    $temp['notes'] = $order['notes'];
                    $temp['sale_id'] = $order['sale_id'];
                    $temp['user_init'] = $order['user_init'];
                    $temp['type'] = 3;
                    $temp['store_id'] = $order['store_id'];
                    $canreturn_temp = array();
                    $canreturn_temp['store_id'] = $order['store_id'];
                    $canreturn_temp['order_id'] = $id;
                    $canreturn_temp['user_init'] = $order['user_init'];

                    $canwarranty_temp = array();
                    $canwarranty_temp['store_id'] = $order['store_id'];
                    $canwarranty_temp['order_id'] = $id;
                    $canwarranty_temp['user_init'] = $this->auth['id'];
                    $canwarranty_temp['customer_id'] = $order['customer_id'];
                    foreach ((array)$detail_order as $item) {
                        $report = $temp;
                        $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id'], 'inventory_expire' => $item['expire']])->get()->row_array();
                        $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                        $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                        $report['product_id'] = $item['id'];
                        $report['discount'] = $percent_discount * $item['quantity'] * ($item['price'] - $item['discount']) + $item['quantity'] * $item['discount'];
                        $report['price'] = $item['price'];
                        $report['output'] = $item['quantity'];
                        $report['report_serial'] = $item['list_serial'];
                        $report['stock'] = isset($stock['quantity']) ? $stock['quantity'] : 0;
                        $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                        $report['report_expire'] = $item['expire'];
                        $this->db->insert('report', $report);

                        $canwarranty = $canwarranty_temp;
                        $canwarranty['product_id'] = $item['id'];
                        $canwarranty['price'] = $item['price'] - $percent_discount * ($item['price'] - $item['discount']) - $item['discount'];
                        if ($product['prd_warranty'] > 0) {
                            $canwarranty['to_date'] = date("Y-m-d H:i:s", strtotime($order['sell_date'] . " +" . $product['prd_warranty'] . " months"));
                        } else {
                            $canwarranty['to_date'] = $order['sell_date'];
                        }

                        if ($item['list_serial'] != '') {
                            $canreturn = $canreturn_temp;
                            $canreturn['product_id'] = $item['id'];
                            $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                            $canreturn['quantity'] = $item['quantity'];
                            $canreturn['canreturn_expire'] = $item['expire'];
                            $this->db->insert('canreturn', $canreturn);
                            $canreturn_id = $this->db->insert_id();

                            $list_serial = explode(",", $item['list_serial']);

                            if ($product['prd_warranty'] > 0) {
                                foreach ((array)$list_serial as $serial) {
                                    $canwarranty['serial'] = $serial;
                                    $canwarranty['quantity'] = 1;
                                    $this->db->insert('canwarranty', $canwarranty);

                                    $canreturn_serial = array();
                                    $canreturn_serial['serial'] = $serial;
                                    $canreturn_serial['canreturn_id'] = $canreturn_id;
                                    $canreturn_serial['order_id'] = $id;
                                    $this->db->insert('canreturn_serial', $canreturn_serial);
                                }
                            }
                        } else {
                            if ($product['prd_warranty'] > 0) {
                                $canwarranty['quantity'] = $item['quantity'];
                                $this->db->insert('canwarranty', $canwarranty);
                            }

                            $canreturn = $canreturn_temp;
                            $canreturn['product_id'] = $item['id'];
                            $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                            $canreturn['quantity'] = $item['quantity'];
                            $canreturn['canreturn_expire'] = $item['expire'];
                            $this->db->insert('canreturn', $canreturn);
                        }
                    }
                }

                $this->cms_update_report($id);

                if ($order['customer_id'] > 0) {
                    cms_updatecustomerdebtbycustomerid($order['customer_id']);
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $this->messages = "0";
                } else {
                    $this->db->trans_commit();
                    echo $this->messages = $id;
                }
            } else
                echo $this->messages = "0";
    }

    public function cms_update_orders($order_id)
    {
        if ($this->auth == null || !in_array(12, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $check_order = $this->db->from('orders')->where(['deleted' => 0, 'ID' => $order_id])->get()->row_array();
            $store_id = $this->auth['store_id'];
            if ($check_order['order_status'] == 0) {
                $order = $this->input->post('data');
                $order['store_id'] = $this->auth['store_id'];
                $user_id = $this->auth['id'];
                if ($order['order_status'] == 5) {
                    $order_cancel['user_upd'] = $user_id;
                    $order_cancel['order_status'] = 5;
                    $order_cancel['notes'] = $order['notes'];
                    $this->db->where(['order_status' => 0, 'deleted' => 0, 'ID' => $order_id])->update('orders', $order_cancel);
                    echo $this->messages = "5";
                } else {
                    if (empty($order['sell_date'])) {
                        $date = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                        $order['sell_date'] = $date;
                    } else {
                        $date = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                        $order['sell_date'] = $date;
                    }

                    $this->db->trans_begin();
                    $total_price = 0;
                    $total_origin_price = 0;
                    $total_quantity = 0;
                    $total_discount = 0;

                    foreach ((array)$order['detail_order'] as $item) {
                        $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                        if ($product['prd_edit_price'] == 0)
                            $item['price'] = $product['prd_sell_price'];
                        $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                        $total_discount += ($item['discount'] * $item['quantity']);
                        $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                        $total_quantity += $item['quantity'];
                        $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);

                        $detail_order[] = $item;
                    }

                    if ($order['order_status'] != 0) {
                        $resu = cms_output_inventory_and_serial($order['detail_order'], $store_id);
                        if ($resu != 1) {
                            $this->db->trans_rollback();
                            echo $this->messages = $resu;
                            return;
                        }
                    }

                    if ($order['coupon'] == 'NaN')
                        $order['coupon'] = 0;

                    if ($order['vat'] > 0)
                        $total_price = ($total_price + ($total_price * $order['vat']) / 100);

                    $order['total_discount'] = $order['coupon'] + $total_discount;
                    $order['total_price'] = $total_price;
                    $order['total_origin_price'] = $total_origin_price;
                    $order['total_money'] = $total_price - $order['coupon'];
                    $order['total_quantity'] = $total_quantity;
                    $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
                    $order['detail_order'] = json_encode($detail_order);

                    if ($order['sale_id'] == null)
                        $order['sale_id'] = 0;

                    if ($order['order_status'] == 1 && $order['customer_id'] > 0 && $order['customer_pay'] > $order['total_money']) {
                        $orders = $this->db
                            ->from('orders')
                            ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'ID !=' => $order_id, 'customer_id' => $order['customer_id']])
                            ->get()
                            ->result_array();
                        $money = $order['customer_pay'] - $order['total_money'];
                        foreach ((array)$orders as $order_temp) {
                            if ($money > $order_temp['lack']) {
                                $update_order = array();
                                $receipt['order_id'] = $order_temp['ID'];
                                $receipt['store_id'] = $store_id;
                                $receipt['receipt_method'] = $order['payment_method'];
                                $receipt['total_money'] = $order_temp['lack'];
                                $update_order['customer_pay'] = $order_temp['customer_pay'] + $order_temp['lack'];
                                $update_order['lack'] = 0;
                                $update_order['user_upd'] = $user_id;

                                $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                                $receipt['receipt_date'] = $date;

                                $receipt['user_init'] = $user_id;
                                $receipt['type_id'] = 3;
                                $this->db->select_max('ID');
                                $max_receipt_code = $this->db->get('receipt')->row();
                                $max_code = (int)($max_receipt_code->ID) + 1;
                                if ($max_code < 10)
                                    $receipt['receipt_code'] = 'PT000000' . ($max_code);
                                else if ($max_code < 100)
                                    $receipt['receipt_code'] = 'PT00000' . ($max_code);
                                else if ($max_code < 1000)
                                    $receipt['receipt_code'] = 'PT0000' . ($max_code);
                                else if ($max_code < 10000)
                                    $receipt['receipt_code'] = 'PT000' . ($max_code);
                                else if ($max_code < 100000)
                                    $receipt['receipt_code'] = 'PT00' . ($max_code);
                                else if ($max_code < 1000000)
                                    $receipt['receipt_code'] = 'PT0' . ($max_code);
                                else if ($max_code < 10000000)
                                    $receipt['receipt_code'] = 'PT' . ($max_code);

                                $this->db->insert('receipt', $receipt);

                                $money -= $order_temp['lack'];
                            } else {
                                $update_order = array();
                                $receipt['order_id'] = $order_temp['ID'];
                                $receipt['store_id'] = $store_id;
                                $receipt['receipt_method'] = $order['payment_method'];
                                $receipt['total_money'] = $money;
                                $update_order['customer_pay'] = $order_temp['customer_pay'] + $money;
                                $update_order['lack'] = $order_temp['lack'] - $money;
                                $update_order['user_upd'] = $user_id;

                                $this->db->where(['ID' => $order_temp['ID'], 'deleted' => 0])->update('orders', $update_order);

                                $receipt['receipt_date'] = $date;

                                $receipt['user_init'] = $user_id;
                                $receipt['type_id'] = 3;
                                $this->db->select_max('ID');
                                $max_receipt_code = $this->db->get('receipt')->row();
                                $max_code = (int)($max_receipt_code->ID) + 1;
                                if ($max_code < 10)
                                    $receipt['receipt_code'] = 'PT000000' . ($max_code);
                                else if ($max_code < 100)
                                    $receipt['receipt_code'] = 'PT00000' . ($max_code);
                                else if ($max_code < 1000)
                                    $receipt['receipt_code'] = 'PT0000' . ($max_code);
                                else if ($max_code < 10000)
                                    $receipt['receipt_code'] = 'PT000' . ($max_code);
                                else if ($max_code < 100000)
                                    $receipt['receipt_code'] = 'PT00' . ($max_code);
                                else if ($max_code < 1000000)
                                    $receipt['receipt_code'] = 'PT0' . ($max_code);
                                else if ($max_code < 10000000)
                                    $receipt['receipt_code'] = 'PT' . ($max_code);

                                $this->db->insert('receipt', $receipt);

                                break;
                            }
                        }
                    }

                    if ($order['customer_id'] < 1 && $order['lack'] > 0) {
                        $this->db->trans_rollback();
                        echo $this->messages = "-1";
                        return;
                    }

                    $this->db->where(['order_status' => 0, 'deleted' => 0, 'ID' => $order_id])->update('orders', $order);
                    $id = $order_id;

                    if ($total_price == 0)
                        $percent_discount = 0;
                    else
                        $percent_discount = $order['coupon'] / $total_price;

                    if ($order['order_status'] != 0) {
                        $receipt = array();
                        $receipt['order_id'] = $id;
                        $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                        $max_receipt_code = $this->db->get('receipt')->row();
                        $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                        if ($max_code < 10)
                            $receipt['receipt_code'] = 'PT000000' . ($max_code);
                        else if ($max_code < 100)
                            $receipt['receipt_code'] = 'PT00000' . ($max_code);
                        else if ($max_code < 1000)
                            $receipt['receipt_code'] = 'PT0000' . ($max_code);
                        else if ($max_code < 10000)
                            $receipt['receipt_code'] = 'PT000' . ($max_code);
                        else if ($max_code < 100000)
                            $receipt['receipt_code'] = 'PT00' . ($max_code);
                        else if ($max_code < 1000000)
                            $receipt['receipt_code'] = 'PT0' . ($max_code);
                        else if ($max_code < 10000000)
                            $receipt['receipt_code'] = 'PT' . ($max_code);

                        $receipt['type_id'] = 3;
                        $receipt['store_id'] = $store_id;
                        $receipt['receipt_date'] = $date;
                        $receipt['notes'] = $order['notes'];
                        $receipt['receipt_method'] = $order['payment_method'];
                        $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                        $receipt['user_init'] = $user_id;
                        $this->db->insert('receipt', $receipt);

                        $temp = array();
                        $temp['transaction_code'] = $check_order['output_code'];
                        $temp['transaction_id'] = $id;
                        $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
                        $temp['date'] = $date;
                        $temp['notes'] = $order['notes'];
                        $temp['sale_id'] = $order['sale_id'];
                        $temp['user_init'] = $user_id;
                        $temp['type'] = 3;
                        $temp['store_id'] = $store_id;

                        $canreturn_temp = array();
                        $canreturn_temp['store_id'] = $store_id;
                        $canreturn_temp['order_id'] = $id;
                        $canreturn_temp['user_init'] = $user_id;

                        $canwarranty_temp = array();
                        $canwarranty_temp['store_id'] = $order['store_id'];
                        $canwarranty_temp['order_id'] = $id;
                        $canwarranty_temp['user_init'] = $this->auth['id'];
                        $canwarranty_temp['customer_id'] = $order['customer_id'];
                        foreach ((array)$detail_order as $item) {
                            $report = $temp;
                            $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id'], 'inventory_expire' => $item['expire']])->get()->row_array();
                            $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                            $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                            $report['product_id'] = $item['id'];
                            $report['discount'] = $percent_discount * $item['quantity'] * ($item['price'] - $item['discount']) + $item['quantity'] * $item['discount'];
                            $report['price'] = $item['price'];
                            $report['output'] = $item['quantity'];
                            $report['report_serial'] = $item['list_serial'];
                            $report['stock'] = isset($stock['quantity']) ? $stock['quantity'] : 0;
                            $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                            $report['report_expire'] = $item['expire'];
                            $this->db->insert('report', $report);
                            $canwarranty = $canwarranty_temp;
                            $canwarranty['product_id'] = $item['id'];
                            $canwarranty['price'] = $item['price'] - $percent_discount * ($item['price'] - $item['discount']) - $item['discount'];
                            if ($product['prd_warranty'] > 0) {
                                $canwarranty['to_date'] = date("Y-m-d H:i:s", strtotime($order['sell_date'] . " +" . $product['prd_warranty'] . " months"));
                            } else {
                                $canwarranty['to_date'] = $order['sell_date'];
                            }

                            if ($item['list_serial'] != '') {
                                $canreturn = $canreturn_temp;
                                $canreturn['product_id'] = $item['id'];
                                $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                                $canreturn['quantity'] = $item['quantity'];
                                $canreturn['canreturn_expire'] = $item['expire'];
                                $this->db->insert('canreturn', $canreturn);
                                $canreturn_id = $this->db->insert_id();

                                if ($product['prd_warranty'] > 0) {
                                    $list_serial = explode(",", $item['list_serial']);

                                    foreach ((array)$list_serial as $serial) {
                                        $canwarranty['serial'] = $serial;
                                        $canwarranty['quantity'] = 1;
                                        $this->db->insert('canwarranty', $canwarranty);

                                        $canreturn_serial = array();
                                        $canreturn_serial['serial'] = $serial;
                                        $canreturn_serial['canreturn_id'] = $canreturn_id;
                                        $canreturn_serial['order_id'] = $id;
                                        $this->db->insert('canreturn_serial', $canreturn_serial);
                                    }
                                }
                            } else {
                                if ($product['prd_warranty'] > 0) {
                                    $canwarranty['quantity'] = $item['quantity'];
                                    $this->db->insert('canwarranty', $canwarranty);
                                }

                                $canreturn = $canreturn_temp;
                                $canreturn['product_id'] = $item['id'];
                                $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                                $canreturn['quantity'] = $item['quantity'];
                                $canreturn['canreturn_expire'] = $item['expire'];
                                $this->db->insert('canreturn', $canreturn);
                            }
                        }
                    }

                    $this->cms_update_report($order_id);

                    if ($order['customer_id'] > 0) {
                        cms_updatecustomerdebtbycustomerid($order['customer_id']);
                    }

                    if ($check_order['customer_id'] > 0) {
                        cms_updatecustomerdebtbycustomerid($check_order['customer_id']);
                    }

                    if ($this->db->trans_status() === FALSE) {
                        $this->db->trans_rollback();
                        echo $this->messages = "6";
                    } else {
                        $this->db->trans_commit();
                        echo $this->messages = $order['order_status'];
                    }
                }
            } else if ($check_order['order_status'] > 0 && $check_order['order_status'] != 5) {
                $order = $this->input->post('data');
                $order['store_id'] = $this->auth['store_id'];
                unset($order['order_status']);
                $this->db->trans_begin();
                $delete = $this->db->from('orders')->where(['ID' => $check_order['ID'], 'deleted' => 0])->get()->row_array();
                $user_id = $this->auth['id'];
                if (isset($delete) && count($delete)) {
                    $list_products_delete = json_decode($delete['detail_order'], true);
                    cms_input_inventory_and_serial($list_products_delete, $check_order['store_id']);

                    $this->db->where(['transaction_id' => $delete['ID'], 'type' => 3, 'store_id' => $delete['store_id']])->update('report', ['deleted' => 1, 'user_upd' => $user_id]);
                    $this->db->where(['order_id' => $check_order['ID']])->delete('canreturn');
                    $this->db->where(['order_id' => $check_order['ID']])->delete('canreturn_serial');
                }

                if (empty($order['sell_date'])) {
                    $date = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                    $order['sell_date'] = $date;
                } else {
                    $date = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                    $order['sell_date'] = $date;
                }

                $total_price = 0;
                $total_origin_price = 0;
                $total_quantity = 0;
                $total_discount = 0;
                $detail_order = array();
                foreach ((array)$order['detail_order'] as $item) {
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                    if ($product['prd_edit_price'] == 0)
                        $item['price'] = $product['prd_sell_price'];
                    $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                    $total_discount += ($item['discount'] * $item['quantity']);
                    $total_origin_price += $product['prd_origin_price'] * $item['quantity'];
                    $total_quantity += $item['quantity'];
                    $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);
                    $detail_order[] = $item;
                }

                $resu = cms_output_inventory_and_serial($order['detail_order'], $store_id);
                if ($resu != 1) {
                    $this->db->trans_rollback();
                    echo $this->messages = $resu;
                    return;
                }

                if ($order['coupon'] == 'NaN')
                    $order['coupon'] = 0;

                if ($order['vat'] > 0)
                    $total_price = ($total_price + ($total_price * $order['vat']) / 100);

                $order['total_discount'] = $order['coupon'] + $total_discount;
                $order['total_price'] = $total_price;
                $order['total_origin_price'] = $total_origin_price;
                $order['total_money'] = $total_price - $order['coupon'];
                $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
                $order['total_quantity'] = $total_quantity;
                $order['detail_order'] = json_encode($detail_order);

                if ($order['sale_id'] == null)
                    $order['sale_id'] = 0;

                if ($order['customer_id'] < 1 && $order['lack'] > 0) {
                    $this->db->trans_rollback();
                    echo $this->messages = "-1";
                    return;
                }

                $this->db->where(['deleted' => 0, 'ID' => $order_id])->update('orders', $order);
                $id = $order_id;

                if ($total_price == 0)
                    $percent_discount = 0;
                else
                    $percent_discount = $order['coupon'] / $total_price;

                $check_receipt = $this->db->from('receipt')->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->count_all_results();
                if ($check_receipt > 1) {
                    $this->db->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->update('receipt', ['deleted' => 0, 'user_upd' => $user_id]);

                    $receipt['order_id'] = $id;
                    $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                    $max_receipt_code = $this->db->get('receipt')->row();
                    $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                    if ($max_code < 10)
                        $receipt['receipt_code'] = 'PT000000' . ($max_code);
                    else if ($max_code < 100)
                        $receipt['receipt_code'] = 'PT00000' . ($max_code);
                    else if ($max_code < 1000)
                        $receipt['receipt_code'] = 'PT0000' . ($max_code);
                    else if ($max_code < 10000)
                        $receipt['receipt_code'] = 'PT000' . ($max_code);
                    else if ($max_code < 100000)
                        $receipt['receipt_code'] = 'PT00' . ($max_code);
                    else if ($max_code < 1000000)
                        $receipt['receipt_code'] = 'PT0' . ($max_code);
                    else if ($max_code < 10000000)
                        $receipt['receipt_code'] = 'PT' . ($max_code);

                    $receipt['type_id'] = 3;
                    $receipt['store_id'] = $store_id;
                    $receipt['receipt_date'] = $date;
                    $receipt['notes'] = $order['notes'];
                    $receipt['receipt_method'] = $order['payment_method'];
                    $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                    $receipt['user_init'] = $user_id;
                    $this->db->insert('receipt', $receipt);
                } else {
                    $check = $this->db->from('receipt')->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->get()->row_array();
                    if (empty($check)) {
                        $receipt['order_id'] = $id;
                        $this->db->select_max('receipt_code')->like('receipt_code', 'PT');
                        $max_receipt_code = $this->db->get('receipt')->row();
                        $max_code = (int)(str_replace('PT', '', $max_receipt_code->receipt_code)) + 1;
                        if ($max_code < 10)
                            $receipt['receipt_code'] = 'PT000000' . ($max_code);
                        else if ($max_code < 100)
                            $receipt['receipt_code'] = 'PT00000' . ($max_code);
                        else if ($max_code < 1000)
                            $receipt['receipt_code'] = 'PT0000' . ($max_code);
                        else if ($max_code < 10000)
                            $receipt['receipt_code'] = 'PT000' . ($max_code);
                        else if ($max_code < 100000)
                            $receipt['receipt_code'] = 'PT00' . ($max_code);
                        else if ($max_code < 1000000)
                            $receipt['receipt_code'] = 'PT0' . ($max_code);
                        else if ($max_code < 10000000)
                            $receipt['receipt_code'] = 'PT' . ($max_code);

                        $receipt['type_id'] = 3;
                        $receipt['store_id'] = $store_id;
                        $receipt['receipt_date'] = $date;
                        $receipt['notes'] = $order['notes'];
                        $receipt['receipt_method'] = $order['payment_method'];
                        $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                        $receipt['user_init'] = $user_id;
                        $this->db->insert('receipt', $receipt);
                    } else {
                        $receipt['store_id'] = $store_id;
                        $receipt['notes'] = $order['notes'];
                        $receipt['user_upd'] = $user_id;
                        $receipt['receipt_method'] = $order['payment_method'];
                        $receipt['total_money'] = $order['customer_pay'] - $total_price + $order['coupon'] < 0 ? $order['customer_pay'] : $total_price - $order['coupon'];
                        $this->db->where(['deleted' => 0, 'order_id' => $order_id, 'total_money >' => 0])->update('receipt', $receipt);
                    }
                }

                $temp = array();
                $temp['transaction_code'] = $check_order['output_code'];
                $temp['transaction_id'] = $id;
                $temp['customer_id'] = isset($order['customer_id']) ? $order['customer_id'] : 0;
                $temp['date'] = $date;
                $temp['notes'] = $order['notes'];
                $temp['sale_id'] = $order['sale_id'];
                $temp['user_init'] = $user_id;
                $temp['type'] = 3;
                $temp['store_id'] = $store_id;

                $canreturn_temp = array();
                $canreturn_temp['store_id'] = $store_id;
                $canreturn_temp['order_id'] = $id;
                $canreturn_temp['user_init'] = $user_id;

                $canwarranty_temp = array();
                $canwarranty_temp['store_id'] = $store_id;
                $canwarranty_temp['order_id'] = $id;
                $canwarranty_temp['user_init'] = $check_order['user_init'];
                $canwarranty_temp['customer_id'] = $order['customer_id'];

                $id_warranty = array();

                foreach ((array)$detail_order as $item) {
                    $report = $temp;
                    $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id'], 'inventory_expire' => $item['expire']])->get()->row_array();
                    $product = $this->db->from('products')->where('ID', $item['id'])->get()->row_array();

                    $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                    $report['product_id'] = $item['id'];
                    $report['discount'] = $percent_discount * $item['quantity'] * ($item['price'] - $item['discount']) + $item['quantity'] * $item['discount'];
                    $report['price'] = $item['price'];
                    $report['output'] = $item['quantity'];
                    $report['report_serial'] = $item['list_serial'];
                    $report['stock'] = isset($stock['quantity']) ? $stock['quantity'] : 0;
                    $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                    $report['report_expire'] = $item['expire'];
                    $this->db->insert('report', $report);

                    $canwarranty = $canwarranty_temp;
                    $canwarranty['product_id'] = $item['id'];
                    $canwarranty['price'] = $item['price'] - $percent_discount * ($item['price'] - $item['discount']) - $item['discount'];
                    if ($product['prd_warranty'] > 0) {
                        $canwarranty['to_date'] = date("Y-m-d H:i:s", strtotime($order['sell_date'] . " +" . $product['prd_warranty'] . " months"));
                    } else {
                        $canwarranty['to_date'] = $order['sell_date'];
                    }

                    if ($item['list_serial'] != '') {
                        $canreturn = $canreturn_temp;
                        $canreturn['product_id'] = $item['id'];
                        $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                        $canreturn['quantity'] = $item['quantity'];
                        $canreturn['canreturn_expire'] = $item['expire'];
                        $this->db->insert('canreturn', $canreturn);
                        $canreturn_id = $this->db->insert_id();

                        if ($product['prd_warranty'] > 0) {
                            $list_serial = explode(",", $item['list_serial']);

                            foreach ((array)$list_serial as $serial) {
                                $canwarranty['serial'] = $serial;
                                $canwarranty['quantity'] = 1;

                                $check = $this->db->from('canwarranty')->where('product_id', $item['id'])->where('serial', $serial)->where('order_id', $id)->get()->row_array();
                                if (isset($check) && count($check)) {
                                    unset($canwarranty['quantity']);
                                    $this->db->where('ID', $check['ID'])->update('canwarranty', $canwarranty);
                                    $id_warranty[] = $check['ID'];
                                } else {
                                    $this->db->insert('canwarranty', $canwarranty);
                                    $warranty_id = $this->db->insert_id();
                                    $id_warranty[] = $warranty_id;
                                }

                                $canreturn_serial = array();
                                $canreturn_serial['serial'] = $serial;
                                $canreturn_serial['canreturn_id'] = $canreturn_id;
                                $canreturn_serial['order_id'] = $id;
                                $this->db->insert('canreturn_serial', $canreturn_serial);
                            }
                        }
                    } else {
                        if ($product['prd_warranty'] > 0) {
                            $canwarranty['quantity'] = $item['quantity'];

                            $check = $this->db->from('canwarranty')->where('product_id', $item['id'])->where('order_id', $id)->get()->row_array();
                            if (isset($check) && count($check)) {
                                unset($canwarranty['quantity']);
                                $this->db->where('ID', $check['ID'])->update('canwarranty', $canwarranty);
                                $id_warranty[] = $check['ID'];
                            } else {
                                $warranty_id = $this->db->insert_id();
                                $id_warranty[] = $warranty_id;
                            }
                        }

                        $canreturn = $canreturn_temp;
                        $canreturn['product_id'] = $item['id'];
                        $canreturn['price'] = $item['price'] - $percent_discount * $item['price'];
                        $canreturn['quantity'] = $item['quantity'];
                        $canreturn['canreturn_expire'] = $item['expire'];
                        $this->db->insert('canreturn', $canreturn);
                    }
                }

                if (count($id_warranty) > 0) {
                    $this->db->where('order_id', $order_id)->where_not_in('ID', $id_warranty)->delete('canwarranty');
                } else {
                    $this->db->where('order_id', $order_id)->delete('canwarranty');
                }

                $this->cms_update_report($order_id);

                if ($order['customer_id'] > 0) {
                    cms_updatecustomerdebtbycustomerid($order['customer_id']);
                }

                if ($check_order['customer_id'] > 0) {
                    cms_updatecustomerdebtbycustomerid($check_order['customer_id']);
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $this->messages = "9";
                } else {
                    $this->db->trans_commit();
                    echo $this->messages = 1;
                }
            } else
                echo $this->messages = "0";
        }
    }
}

