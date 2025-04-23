<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Payment extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(18, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $data['seo']['title'] = "Phần mềm quản lý bán hàng";
            $data['data']['user'] = $this->auth;
            $data['template'] = 'payment/index';
            $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
            $data['data']['users'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();
            $data['data']['store_id'] = $this->auth['store_id'];
            $this->load->view('layout/index', isset($data) ? $data : null);
        }
    }

    public function cms_paging_payment($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] > '-1') {
            $this->db->where('type_id', $option['option1']);
        }

        if ($option['option2'] > '-1') {
            $this->db->where('payment_method', $option['option2']);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('store_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(payment_code LIKE '%" . $option['keyword'] . "%' OR notes LIKE '%" . $option['keyword'] . "%' OR total_money = '" . $option['keyword'] . "')", NULL, FALSE);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('payment_date >=', $option['date_from'])
                ->where('payment_date <=', $option['date_to']);
        }

        $total_payment = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money')
            ->from('payment')
            ->where('deleted', 0)
            ->get()
            ->row_array();

        if ($option['option1'] > '-1') {
            $this->db->where('type_id', $option['option1']);
        }

        if ($option['option2'] > '-1') {
            $this->db->where('payment_method', $option['option2']);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('store_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(payment_code LIKE '%" . $option['keyword'] . "%' OR notes LIKE '%" . $option['keyword'] . "%' OR total_money = '" . $option['keyword'] . "')", NULL, FALSE);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('payment_date >=', $option['date_from'])
                ->where('payment_date <=', $option['date_to']);
        }

        $data['_list_payment'] = $this->db
            ->from('payment')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_payment';
        $config['total_rows'] = $total_payment['quantity'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_payment'] = $total_payment;
        if ($page > 1 && ($total_payment['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        if (in_array(22, $this->auth['group_permission']))
            $data['delete_payment'] = 1;
        else
            $data['delete_payment'] = 0;

        if (in_array(24, $this->auth['group_permission']))
            $data['edit_payment'] = 1;
        else
            $data['edit_payment'] = 0;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/payment/list_payment', isset($data) ? $data : null);
    }

    public function cms_export_payment()
    {
        $option = $this->input->post('data');
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] > '-1') {
            $this->db->where('type_id', $option['option1']);
        }

        if ($option['option2'] > '-1') {
            $this->db->where('payment_method', $option['option2']);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('store_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(payment_code LIKE '%" . $option['keyword'] . "%' OR notes LIKE '%" . $option['keyword'] . "%' OR total_money = '" . $option['keyword'] . "')", NULL, FALSE);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('payment_date >=', $option['date_from'])
                ->where('payment_date <=', $option['date_to']);
        }

        $data['_list_payment'] = $this->db
            ->from('payment')
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->get()
            ->result_array();


        $fileName = 'PhieuChi-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Mã phiếu chi');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Phiếu nhập');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Nhà cung cấp');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Kho chi');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Ngày chi');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Người chi');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Chi cho');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Ghi chú');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Hình thức chi');
        $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('Hình thức thanh toán');
        $objPHPExcel->getActiveSheet()->getCell('K1', true)->setValue('Tổng tiền');

        $rowCount = 2;
        foreach ((array)$data['_list_payment'] as $element) {
            $input = cms_getsuppliernamebyinputid($element['input_id']);
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['payment_code']);
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($element['input_id'] == 0 ? '' : $input['input_code']);
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['input_id'] == 0 ? '' : $input['supplier_name']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_ConvertDateTime($element['payment_date']));
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['user_init']));
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_getNameAuthbyID($element['payment_for']));
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue($element['notes']);
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue(cms_getNamepaymentTypeByID($element['type_id']));
            $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue(cms_getNameReceiptMethodByID($element['payment_method']));
            $objPHPExcel->getActiveSheet()->getCell('K' . $rowCount, true)->setValue($element['total_money']);

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

    public function cms_print_payment()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->get()->row_array();
        $data_template['content'] = str_replace("{page_break}", '<div style="display: block; page-break-before: always;"></div>', $data_template['content']);

        $payment = $this->db->from('payment')->where('ID', $data_post['id_payment'])->get()->row_array();

        $user_name = cms_getNameAuthbyID($payment['user_init']);

        $supplier_infor = '';

        if ($payment['input_id'] > 0) {
            $input = $this->db->from('input')->where('ID', $payment['input_id'])->get()->row_array();

            if (isset($input) && count($input) > 0) {
                if ($input['supplier_id'] > 0) {
                    $supplier = $this->db->from('suppliers')->where('ID', $input['supplier_id'])->get()->row_array();
                    if (isset($supplier) && count($supplier) > 0) {
                        $supplier_name = $supplier['supplier_name'];
                        $supplier_code = $supplier['supplier_code'];
                        $supplier_phone = $supplier['supplier_phone'];
                        $supplier_address = $supplier['supplier_addr'];

                        $supplier_infor = '<div style="text-align:center">&nbsp;</div>
<table style="width:100%">
	<tbody>
		<tr>
			<td colspan="1" style="text-align:left">Nhà cung cấp: {NCC}</td>
			<td style="text-align:left">Mã nhà cung cấp: {Ma_NCC}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Địa chỉ:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {DC_NCC}</td>
			<td style="text-align:left">
			<p>SĐT:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{DT_NCC}</p>
			</td>
		</tr>
	</tbody>
</table>
<div>';

                        $supplier_infor = str_replace("{NCC}", $supplier_name, $supplier_infor);
                        $supplier_infor = str_replace("{Ma_NCC}", $supplier_code, $supplier_infor);
                        $supplier_infor = str_replace("{DT_NCC}", $supplier_phone, $supplier_infor);
                        $supplier_infor = str_replace("{DC_NCC}", $supplier_address, $supplier_infor);
                    }
                }
            }
        }

        $ngayin = gmdate("H:i d/m/Y", time() + 7 * 3600);
        $nguoiin = cms_getUserNameAuthbyID($this->auth['id']);

        $data_template['content'] = str_replace("{Ngay_In}", $ngayin, $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_In}", $nguoiin, $data_template['content']);
        $data_template['content'] = str_replace("{Thong_Tin_NCC}", $supplier_infor, $data_template['content']);
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", "Phong Tran", $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Chi}", cms_ConvertDateTime($payment['payment_date']), $data_template['content']);
        $data_template['content'] = str_replace("{Thu_Ngan}", $user_name, $data_template['content']);
        $data_template['content'] = str_replace("{Hinh_Thuc_Chi}", cms_getNameReceiptMethodByID($payment['payment_method']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien}", cms_encode_currency_format($payment['total_money']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Phieu_Chi}", $payment['payment_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $payment['notes'], $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", cms_convert_number_to_words($payment['total_money']), $data_template['content']);
        echo $this->messages = $data_template['content'];
    }

    public function cms_del_temp_payment($id)
    {
        if ($this->auth == null || !in_array(22, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $payment = $this->db->from('payment')->where(['ID' => $id, 'deleted' => 0])->get()->row_array();
            $user_id = $this->auth['id'];
            $this->db->trans_begin();
            if (isset($payment) && count($payment)) {
                if ($payment['input_id'] > 0) {
                    $input = $this->db->select('payed,lack')->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
                    if (!empty($input)) {
                        $input['payed'] = $input['payed'] - $payment['total_money'];
                        $input['lack'] = $input['lack'] + $payment['total_money'];
                        $input['user_upd'] = $user_id;
                        $this->db->where('ID', $payment['input_id'])->update('input', $input);
                    }
                }

                $this->db->where('ID', $id)->update('payment', ['deleted' => 1, 'user_upd' => $user_id]);
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

    public function cms_edit_payment($id, $page)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $payment = $this->db->from('payment')->where('ID', $id)->get()->row_array();
            if (!empty($payment) && count($payment)) {
                $data['data']['users'] = $this->db->from('users')->where('user_status', '1')->get()->result_array();
                $data['_detail_payment'] = $payment;
                $data['page'] = $page;
                $this->load->view('ajax/payment/edit_payment', isset($data) ? $data : null);
            }
        }
    }

    public function cms_save_payment()
    {
        $data = $this->input->post('data');
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        if ($data['payment_date'] == '') {
            $data['payment_date'] = $data['created'];
        }

        $data['user_init'] = $this->auth['id'];

        $this->db->select_max('ID');
        $max_payment_code = $this->db->get('payment')->row();
        $max_code = (int)($max_payment_code->ID) + 1;

        if ($max_code < 10)
            $data['payment_code'] = 'PC000000' . ($max_code);
        else if ($max_code < 100)
            $data['payment_code'] = 'PC00000' . ($max_code);
        else if ($max_code < 1000)
            $data['payment_code'] = 'PC0000' . ($max_code);
        else if ($max_code < 10000)
            $data['payment_code'] = 'PC000' . ($max_code);
        else if ($max_code < 100000)
            $data['payment_code'] = 'PC00' . ($max_code);
        else if ($max_code < 1000000)
            $data['payment_code'] = 'PC0' . ($max_code);
        else if ($max_code < 10000000)
            $data['payment_code'] = 'PC' . ($max_code);

        $this->db->insert('payment', $data);
        echo $this->messages = "1";
    }

    public function cms_update_payment($id)
    {
        $id = (int)$id;
        $payment = $this->db->from('payment')->where('ID', $id)->get()->row_array();
        if (!empty($payment) && count($payment)) {
            $data = $this->input->post('data');
            if ($data['payment_date'] == '') {
                $data['payment_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            }

            if ($data['payment_image'] == '') {
                unset($data['payment_image']);
            }

            $data['user_upd'] = $this->auth['id'];

            if ($payment['input_id'] > 0 && $data['total_money'] != $payment['total_money']) {
                echo $this->messages = "Phiếu chi thuộc về phiếu nhập hàng nên không thể chỉnh sửa số tiền";
                return;
            }

            $this->db->where('ID', $id)->update('payment', $data);
            echo $this->messages = "1";
        } else
            echo $this->messages = "0";
    }
}
