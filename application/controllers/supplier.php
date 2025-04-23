<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Supplier extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function cms_crsup($total_debt)
    {
        $data = $this->input->post('data');

        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        if ($data['supplier_code'] == '') {
            $max_supplier_code = $this->db->select_max('supplier_code')->from('suppliers')->where('LENGTH(supplier_code) = 8')->where("(supplier_code LIKE 'NCC" . "%')", NULL, FALSE)->get()->row_array();

            if (isset($max_supplier_code) && count($max_supplier_code) > 0) {
                $max_code = (int)(str_replace('NCC', '', $max_supplier_code['supplier_code'])) + 1;
                if ($max_code < 10)
                    $data['supplier_code'] = 'NCC0000' . ($max_code);
                else if ($max_code < 100)
                    $data['supplier_code'] = 'NCC000' . ($max_code);
                else if ($max_code < 1000)
                    $data['supplier_code'] = 'NCC00' . ($max_code);
                else if ($max_code < 10000)
                    $data['supplier_code'] = 'NCC0' . ($max_code);
                else if ($max_code < 100000)
                    $data['supplier_code'] = 'NCC' . ($max_code);
            } else {
                $data['supplier_code'] = 'NCC00001';
            }
            $this->db->insert('suppliers', $data);
            $id = $this->db->insert_id();

            if ($total_debt != '' && $total_debt > 0) {
                $input['lack'] = $total_debt;
                $input['user_init'] = $this->auth['id'];
                $input['store_id'] = $this->auth['store_id'];
                $input['total_price'] = $total_debt;
                $input['supplier_id'] = $id;
                $input['input_status'] = 1;

                $this->db->select_max('input_code')->like('input_code', 'PN')->where('order_id', 0);
                $max_input_code = $this->db->get('input')->row();
                $max_code = (int)(str_replace('PN', '', $max_input_code->input_code)) + 1;
                if ($max_code < 10)
                    $input['input_code'] = 'PN000000' . ($max_code);
                else if ($max_code < 100)
                    $input['input_code'] = 'PN00000' . ($max_code);
                else if ($max_code < 1000)
                    $input['input_code'] = 'PN0000' . ($max_code);
                else if ($max_code < 10000)
                    $input['input_code'] = 'PN000' . ($max_code);
                else if ($max_code < 100000)
                    $input['input_code'] = 'PN00' . ($max_code);
                else if ($max_code < 1000000)
                    $input['input_code'] = 'PN0' . ($max_code);
                else if ($max_code < 10000000)
                    $input['input_code'] = 'PN' . ($max_code);

                if ($input['supplier_id'] < 1 && $input['lack'] > 0) {
                    $this->db->trans_rollback();
                    echo $this->messages = "Vui lòng chọn nhà cung cấp để có thể nhập hàng nợ";
                    return;
                }

                $this->db->insert('input', $input);

            }

            echo $this->messages = $id;
        } else {
            $count = $this->db->where('supplier_code', $data['supplier_code'])->from('suppliers')->count_all_results();
            if ($count > 0) {
                echo $this->messages = "0";
            } else {
                $this->db->insert('suppliers', $data);
                $id = $this->db->insert_id();

                if ($total_debt != '' && $total_debt > 0) {
                    $input['lack'] = $total_debt;
                    $input['user_init'] = $this->auth['id'];
                    $input['store_id'] = $this->auth['store_id'];
                    $input['total_price'] = $total_debt;
                    $input['supplier_id'] = $id;
                    $input['input_status'] = 1;

                    $this->db->select_max('input_code')->like('input_code', 'PN')->where('order_id', 0);
                    $max_input_code = $this->db->get('input')->row();
                    $max_code = (int)(str_replace('PN', '', $max_input_code->input_code)) + 1;
                    if ($max_code < 10)
                        $input['input_code'] = 'PN000000' . ($max_code);
                    else if ($max_code < 100)
                        $input['input_code'] = 'PN00000' . ($max_code);
                    else if ($max_code < 1000)
                        $input['input_code'] = 'PN0000' . ($max_code);
                    else if ($max_code < 10000)
                        $input['input_code'] = 'PN000' . ($max_code);
                    else if ($max_code < 100000)
                        $input['input_code'] = 'PN00' . ($max_code);
                    else if ($max_code < 1000000)
                        $input['input_code'] = 'PN0' . ($max_code);
                    else if ($max_code < 10000000)
                        $input['input_code'] = 'PN' . ($max_code);

                    if ($input['supplier_id'] < 1 && $input['lack'] > 0) {
                        $this->db->trans_rollback();
                        echo $this->messages = "Vui lòng chọn nhà cung cấp để có thể nhập hàng nợ";
                        return;
                    }

                    $this->db->insert('input', $input);

                }

                echo $this->messages = $id;
            }
        }
    }

    public function cms_detail_input_in_supplier()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');
        $input = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($input) && count($input)) {
            $list_products = json_decode($input['detail_input'], true);

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

        $data['data']['_import'] = $input;
        $this->load->view('ajax/customer-supplier/detail_input', isset($data) ? $data : null);
    }

    public function cms_print_supplier()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_template['content'] = str_replace("{page_break}", '<div style="display: block; page-break-before: always;"></div>', $data_template['content']);

        $supplier = $this->db->from('suppliers')->where('ID', $data_post['id_supplier'])->get()->row_array();
        $supplier_name = $supplier['supplier_name'];
        $supplier_code = $supplier['supplier_code'];
        $supplier_phone = $supplier['supplier_phone'];
        $supplier_address = $supplier['supplier_addr'];
        $input = $this->db
            ->select('sum(lack) as debt')
            ->from('input')
            ->where(['deleted' => 0, 'input_status' => 1, 'lack >' => 0, 'supplier_id' => $supplier['ID']])
            ->get()
            ->row_array();
        $debt = $input['debt'];

        $ngayin = gmdate("H:i d/m/Y", time() + 7 * 3600);
        $nguoiin = cms_getUserNameAuthbyID($this->auth['id']);

        $data_template['content'] = str_replace("{Ngay_In}", $ngayin, $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_In}", $nguoiin, $data_template['content']);
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", cms_getNamestockbyID($this->auth['store_id']), $data_template['content']);
        $data_template['content'] = str_replace("{Nha_Cung_Cap}", $supplier_name, $data_template['content']);
        $data_template['content'] = str_replace("{Ma_NCC}", $supplier_code, $data_template['content']);
        $data_template['content'] = str_replace("{DT_NCC}", $supplier_phone, $data_template['content']);
        $data_template['content'] = str_replace("{DC_NCC}", $supplier_address, $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", cms_convert_number_to_words($debt), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Cong_No}", cms_encode_currency_format($debt), $data_template['content']);
        $number = 1;
        $detail = '';
        if (isset($supplier) && count($supplier)) {
            $list_input = $this->db
                ->from('input')
                ->where(['deleted' => 0, 'input_status' => 1, 'lack >' => 0, 'supplier_id' => $supplier['ID']])
                ->get()
                ->result_array();
            foreach ((array)$list_input as $input) {

                $detail = $detail . '<tr>
                                        <td style="text-align:center;">' . $number++ . '</td>
                                        <td style = "text-align:center">' . $input['input_code'] . '</td>
                                        <td style = "text-align:center">' . $input['input_date'] . '</td>
                                        <td style = "text-align:center">' . $input['total_quantity'] . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($input['total_money']) . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($input['payed']) . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($input['lack']) . '</td>';
            }
        }

        $table = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Mã phiếu</strong></td>
                            <td style="text-align:center;"><strong>Ngày nhập</strong></td>
                            <td style="text-align:center;"><strong>Tổng SL</strong></td>
                            <td style="text-align:center;"><strong>Tổng tiền</strong></td>
                            <td style="text-align:center;"><strong>Đã thanh toán</strong></td>
                            <td style="text-align:center;"><strong>Còn nợ</strong></td>
                        </tr>' . $detail . '
                    </tbody>
                 </table>';

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_paging_input_by_supplier_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();

        $total_inputs = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('input')
            ->where('deleted', 0)
            ->where('supplier_id', $option['supplier_id'])
            ->get()
            ->row_array();
        $data['_list_input'] = $this->db
            ->from('input')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->where('supplier_id', $option['supplier_id'])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_input'], 'supplier_id');
        $data['supplier_id'] = $option['supplier_id'];
        $config['base_url'] = 'cms_paging_input_by_supplier_id';
        $config['total_rows'] = $total_inputs['quantity'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_inputs'] = $total_inputs;
        if ($page > 1 && ($total_inputs['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_input', isset($data) ? $data : null);
    }

    public function cms_paging_input_debt_by_supplier_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $config['per_page'] = 100;
        $total_inputs = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('input')
            ->where(['deleted' => 0, 'input_status' => 1])
            ->where(['supplier_id' => $option['supplier_id'], 'lack >' => 0])
            ->get()
            ->row_array();
        $data['_list_input'] = $this->db
            ->from('input')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'asc')
            ->where(['deleted' => 0, 'input_status' => 1])
            ->where(['supplier_id' => $option['supplier_id'], 'lack >' => 0])
            ->get()
            ->result_array();

        $data['_list_customer'] = $this->cms_common->unique_multidim_array($data['_list_input'], 'supplier_id');
        $data['supplier_id'] = $option['supplier_id'];
        $config['base_url'] = 'cms_paging_input_debt_by_supplier_id';
        $config['total_rows'] = $total_inputs['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_inputs'] = $total_inputs;
        if ($page > 1 && ($total_inputs['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_input_debt', isset($data) ? $data : null);
    }

    public function cms_paging_supplier($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $option = $this->input->post('data');

        if ($option['option'] == 0) {
            $total_supplier = $this->db
                ->select('sum(distinct(cms_suppliers.ID)) as quantity,sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $data['_list_supplier'] = $this->db
                ->select('province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else if ($option['option'] == 1) {
            $total_supplier = $this->db
                ->select('sum(distinct(cms_suppliers.ID)) as quantity,sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'INNER')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();
            $data['_list_supplier'] = $this->db
                ->select('province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'INNER')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else {
            $total_supplier = $this->db
                ->select('sum(distinct(cms_suppliers.ID)) as quantity,sum(total_money) as total_money, sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'INNER')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->where('lack >', 0)
                ->get()
                ->row_array();
            $data['_list_supplier'] = $this->db
                ->select('province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'INNER')
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
        }

        $config['base_url'] = 'cms_paging_supplier';

        $config['total_rows'] = $total_supplier['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['_total_supplier'] = $total_supplier;
        $data['_pagination_link'] = $_pagination_link;
        $data['user'] = $this->auth;
        if ($page > 1 && ($total_supplier['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option'];
        $data['page'] = $page;
        $this->load->view('ajax/customer-supplier/list_supplier', isset($data) ? $data : null);
    }

    public function cms_export_supplier()
    {
        $option = $this->input->post('data');

        if ($option['option'] == 0) {
            $data['_list_supplier'] = $this->db
                ->select('cms_suppliers.notes,supplier_tax,supplier_email,province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money_input,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'LEFT')
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else if ($option['option'] == 1) {
            $data['_list_supplier'] = $this->db
                ->select('province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->get()
                ->result_array();
        } else {
            $data['_list_supplier'] = $this->db
                ->select('province_id,district_id,ward_id,supplier_code,suppliers.ID,supplier_image,supplier_name,supplier_phone,supplier_addr,max(input_date) as input_date,sum(total_money) as total_money,sum(lack) as total_debt')
                ->from('suppliers')
                ->where("(supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->join('input', 'input.supplier_id=suppliers.ID and cms_input.deleted=0', 'RIGHT')
                ->order_by('suppliers.created', 'desc')
                ->group_by('suppliers.ID')
                ->having('sum(lack) > 0')
                ->get()
                ->result_array();
        }
        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'NhaCungCap-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Hinh_Anh');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Ma_NCC');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ten_NCC');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Dien_Thoai');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Email');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Ma_So_Thue');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Tinh_Thanh_Pho');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Quan_Huyen');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Phuong_Xa');
        $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('Dia_Chi');
        $objPHPExcel->getActiveSheet()->getCell('K1', true)->setValue('Ghi_Chu');
        $objPHPExcel->getActiveSheet()->getCell('L1', true)->setValue('Lần cuối nhập hàng');
        $objPHPExcel->getActiveSheet()->getCell('M1', true)->setValue('Tổng tiền hàng');
        $objPHPExcel->getActiveSheet()->getCell('N1', true)->setValue('Cong_No');
        $rowCount = 2;
        foreach ((array)$data['_list_supplier'] as $element) {

            if ($element['supplier_image'] != '') {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(CMS_BASE_URL . 'public/templates/uploads/' . cms_show_image($element['supplier_image']));
            } else {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('');
            }

            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($element['supplier_code']);
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['supplier_name']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($element['supplier_phone']);
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($element['supplier_email']);
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['supplier_tax']);
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_getProvinceNameByID($element['province_id']));
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(cms_getDistrictNameByID($element['district_id']));
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue(cms_getWardNameByID($element['ward_id']));
            $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($element['supplier_addr']);
            $objPHPExcel->getActiveSheet()->getCell('K' . $rowCount, true)->setValue($element['notes']);

            $objPHPExcel->getActiveSheet()->getCell('L' . $rowCount, true)->setValue(cms_ConvertDateTime($element['input_date']));
            $objPHPExcel->getActiveSheet()->getCell('M' . $rowCount, true)->setValue(($element['total_money_input']));
            $objPHPExcel->getActiveSheet()->getCell('N' . $rowCount, true)->setValue(($element['total_debt']));

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

    public function upload_excel()
    {


        if ($this->input->post('importfile')) {
            $path = 'public/templates/uploads/';
            $config['upload_path'] = $path;
            $config['allowed_types'] = 'xlsx|xls|jpg|png';
            $config['remove_spaces'] = TRUE;
            $this->load->library('upload', $config);
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('userfile')) {
                $error = array('error' => $this->upload->display_errors());
            } else {
                $data = array('upload_data' => $this->upload->data());
            }

            if (!empty($data['upload_data']['file_name'])) {
                $import_xls_file = $data['upload_data']['file_name'];
            } else {
                $import_xls_file = 0;
            }
            $inputFileName = $path . $import_xls_file;
            try {
                $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch (Exception $e) {
                echo $this->messages = '<script>
                                    alert("Bạn chưa chọn file. Vui lòng chọn file và thao tác lại");
                                    window.history.back();
                            </script>';
                return;
            }
            $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

            $arrayCount = count($allDataInSheet);
            $createArray = array(
                'Hinh_Anh',
                'Ma_NCC',
                'Ten_NCC',
                'Dien_Thoai',
                'Email',
                'Ma_So_Thue',
                'Tinh_Thanh_Pho',
                'Quan_Huyen',
                'Phuong_Xa',
                'Dia_Chi',
                'Ghi_Chu',
                'Cong_No');
            $makeArray = array(
                'Hinh_Anh' => 'Hinh_Anh',
                'Ma_NCC' => 'Ma_NCC',
                'Ten_NCC' => 'Ten_NCC',
                'Dien_Thoai' => 'Dien_Thoai',
                'Email' => 'Email',
                'Ma_So_Thue' => 'Ma_So_Thue',
                'Tinh_Thanh_Pho' => 'Tinh_Thanh_Pho',
                'Quan_Huyen' => 'Quan_Huyen',
                'Phuong_Xa' => 'Phuong_Xa',
                'Dia_Chi' => 'Dia_Chi',
                'Ghi_Chu' => 'Ghi_Chu',
                'Cong_No' => 'Cong_No'
            );
            $SheetDataKey = array();
            foreach ((array)$allDataInSheet as $dataInSheet) {
                foreach ((array)$dataInSheet as $key => $value) {
                    if (in_array(trim($value), $createArray)) {
                        $value = preg_replace('/\s+/', '', $value);
                        $SheetDataKey[trim($value)] = $key;
                    }
                }
            }
            $data = array_diff_key($makeArray, $SheetDataKey);

            if (empty($data)) {
                $this->db->trans_begin();
                $er = '';
                $supplier_image = $SheetDataKey['Hinh_Anh'];
                $supplier_code = $SheetDataKey['Ma_NCC'];
                $supplier_name = $SheetDataKey['Ten_NCC'];
                $supplier_phone = $SheetDataKey['Dien_Thoai'];
                $supplier_email = $SheetDataKey['Email'];
                $supplier_tax = $SheetDataKey['Ma_So_Thue'];
                $province_id_temp = $SheetDataKey['Tinh_Thanh_Pho'];
                $district_id_temp = $SheetDataKey['Quan_Huyen'];
                $ward_id_temp = $SheetDataKey['Phuong_Xa'];
                $supplier_addr = $SheetDataKey['Dia_Chi'];
                $notes = $SheetDataKey['Ghi_Chu'];
                $supplier_debt = $SheetDataKey['Cong_No'];

                for ($i = 2; $i <= $arrayCount; $i++) {
                    $data = array();
                    $data['supplier_name'] = filter_var(trim($allDataInSheet[$i][$supplier_name]), FILTER_SANITIZE_STRING);
                    if ($data['supplier_name'] != '') {
                        $data['supplier_image'] = str_replace(CMS_BASE_URL . 'public/templates/uploads/', '', filter_var(trim($allDataInSheet[$i][$supplier_image]), FILTER_SANITIZE_STRING));
                        $data['supplier_code'] = filter_var(trim($allDataInSheet[$i][$supplier_code]), FILTER_SANITIZE_STRING);
                        $data['supplier_phone'] = filter_var(trim($allDataInSheet[$i][$supplier_phone]), FILTER_SANITIZE_STRING);
                        $data['supplier_email'] = filter_var(trim($allDataInSheet[$i][$supplier_email]), FILTER_SANITIZE_STRING);
                        $data['supplier_tax'] = filter_var(trim($allDataInSheet[$i][$supplier_tax]), FILTER_SANITIZE_STRING);
                        $data['supplier_addr'] = filter_var(trim($allDataInSheet[$i][$supplier_addr]), FILTER_SANITIZE_STRING);
                        $data['notes'] = filter_var(trim($allDataInSheet[$i][$notes]), FILTER_SANITIZE_STRING);
                        $data['supplier_debt'] = filter_var(trim($allDataInSheet[$i][$supplier_debt]), FILTER_SANITIZE_STRING);

                        $province_name = filter_var(trim($allDataInSheet[$i][$province_id_temp]), FILTER_SANITIZE_STRING);
                        $district_name = filter_var(trim($allDataInSheet[$i][$district_id_temp]), FILTER_SANITIZE_STRING);
                        $ward_name = filter_var(trim($allDataInSheet[$i][$ward_id_temp]), FILTER_SANITIZE_STRING);

                        if ($province_name != '') {
                            $province_id = cms_CheckProvinceNameByName($province_name);
                            if ($province_id > 0) {
                                $data['province_id'] = $province_id;

                                if ($district_name != '') {
                                    $district_id = cms_CheckDistrictNameByName($district_name, $province_id);
                                    if ($district_id > 0) {
                                        $data['district_id'] = $district_id;

                                        if ($ward_name != '') {
                                            $ward_id = cms_CheckWardNameByName($ward_name, $district_id);
                                            if ($ward_id > 0) {
                                                $data['ward_id'] = $ward_id;
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if ($supplier_code != '') {
                            $check_code = $this->db->from('suppliers')->where(['supplier_code' => $data['supplier_code']])->count_all_results();
                            if ($check_code == 1) {
                                unset($data['supplier_debt']);
                                $this->db->where('supplier_code', $data['supplier_code'])->update('suppliers', $data);
                            } else if ($check_code > 1) {
                                $er .= '\n Mã NCC ' . $data['supplier_code'] . ' ở dòng thứ ' . $i . ' đã bị trùng nhiều lần.';
                            } else if ($data['supplier_name'] == '') {
                                $er .= '\n Tên NCC ' . $data['supplier_name'] . ' ở dòng thứ ' . $i . ' không được rỗng.';
                            } else {
                                $id = $this->cms_save_supplier($data);

                                if ($data['supplier_debt'] != '' && $data['supplier_debt'] > 0) {
                                    $total_debt = $data['supplier_debt'];
                                    $input = array();
                                    $input['lack'] = $total_debt;
                                    $input['user_init'] = $this->auth['id'];
                                    $input['store_id'] = $this->auth['store_id'];
                                    $input['total_price'] = $total_debt;
                                    $input['supplier_id'] = $id;
                                    $input['input_status'] = 1;

                                    $this->db->select_max('input_code')->like('input_code', 'PN')->where('order_id', 0);
                                    $max_input_code = $this->db->get('input')->row();
                                    $max_code = (int)(str_replace('PN', '', $max_input_code->input_code)) + 1;
                                    if ($max_code < 10)
                                        $input['input_code'] = 'PN000000' . ($max_code);
                                    else if ($max_code < 100)
                                        $input['input_code'] = 'PN00000' . ($max_code);
                                    else if ($max_code < 1000)
                                        $input['input_code'] = 'PN0000' . ($max_code);
                                    else if ($max_code < 10000)
                                        $input['input_code'] = 'PN000' . ($max_code);
                                    else if ($max_code < 100000)
                                        $input['input_code'] = 'PN00' . ($max_code);
                                    else if ($max_code < 1000000)
                                        $input['input_code'] = 'PN0' . ($max_code);
                                    else if ($max_code < 10000000)
                                        $input['input_code'] = 'PN' . ($max_code);
                                    $this->db->insert('input', $input);
                                }
                            }
                        } else {
                            if ($data['supplier_name'] == '') {
                                $er .= '\n Tên NCC ' . $data['supplier_name'] . ' ở dòng thứ ' . $i . ' không được rỗng.';
                            } else {
                                $id = $this->cms_save_supplier($data);

                                if ($data['supplier_debt'] != '' && $data['supplier_debt'] > 0) {
                                    $total_debt = $data['supplier_debt'];
                                    $input = array();
                                    $input['lack'] = $total_debt;
                                    $input['user_init'] = $this->auth['id'];
                                    $input['store_id'] = $this->auth['store_id'];
                                    $input['total_price'] = $total_debt;
                                    $input['supplier_id'] = $id;
                                    $input['input_status'] = 1;

                                    $this->db->select_max('input_code')->like('input_code', 'PN')->where('order_id', 0);
                                    $max_input_code = $this->db->get('input')->row();
                                    $max_code = (int)(str_replace('PN', '', $max_input_code->input_code)) + 1;
                                    if ($max_code < 10)
                                        $input['input_code'] = 'PN000000' . ($max_code);
                                    else if ($max_code < 100)
                                        $input['input_code'] = 'PN00000' . ($max_code);
                                    else if ($max_code < 1000)
                                        $input['input_code'] = 'PN0000' . ($max_code);
                                    else if ($max_code < 10000)
                                        $input['input_code'] = 'PN000' . ($max_code);
                                    else if ($max_code < 100000)
                                        $input['input_code'] = 'PN00' . ($max_code);
                                    else if ($max_code < 1000000)
                                        $input['input_code'] = 'PN0' . ($max_code);
                                    else if ($max_code < 10000000)
                                        $input['input_code'] = 'PN' . ($max_code);

                                    $this->db->insert('input', $input);
                                }
                            }
                        }
                    }
                }

                if ($this->db->trans_status() === FALSE) {
                    $this->db->trans_rollback();
                    echo $this->messages = "0";
                } else {
                    cms_delete_public_file_by_extend('xlsx');
                    if ($er == '') {
                        $this->db->trans_commit();
                        echo $this->messages = '<script>
                                    alert("Nhập thành công");
                                    window.history.back();
                            </script>';
                    } else {
                        $this->db->trans_rollback();
                        echo $this->messages = '<script>alert("Nhập không thành công: ' . $er . '");window.history.back();
                        </script>';
                    }
                }
            } else {
                echo $this->messages = '<script>
                                    alert("File không đúng định dạng. Vui lòng tải file mẫu và thao tác lại");
                                    window.history.back();
                            </script>';
            }
        }
    }

    public function cms_save_supplier($data)
    {
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        unset($data['supplier_debt']);
        if ($data['supplier_code'] == '') {
            $max_supplier_code = $this->db->select_max('supplier_code')->from('suppliers')->where('LENGTH(supplier_code) = 8')->where("(supplier_code LIKE 'NCC" . "%')", NULL, FALSE)->get()->row_array();

            if (isset($max_supplier_code) && count($max_supplier_code) > 0) {
                $max_code = (int)(str_replace('NCC', '', $max_supplier_code['supplier_code'])) + 1;
                if ($max_code < 10)
                    $data['supplier_code'] = 'NCC0000' . ($max_code);
                else if ($max_code < 100)
                    $data['supplier_code'] = 'NCC000' . ($max_code);
                else if ($max_code < 1000)
                    $data['supplier_code'] = 'NCC00' . ($max_code);
                else if ($max_code < 10000)
                    $data['supplier_code'] = 'NCC0' . ($max_code);
                else if ($max_code < 100000)
                    $data['supplier_code'] = 'NCC' . ($max_code);
            } else {
                $data['supplier_code'] = 'NCC00001';
            }

            $this->db->insert('suppliers', $data);
            $id = $this->db->insert_id();

            return $id;
        } else {
            $this->db->insert('suppliers', $data);
            $id = $this->db->insert_id();

            return $id;
        }
    }

    public function cms_delsup()
    {
        $id = (int)$this->input->post('id');

        $sup = $this->db->from('suppliers')->where('id', $id)->get()->row_array();
        if (!isset($sup) && count($sup) == 0) {
            echo $this->messages;

            return;
        } else {
            $this->db->where('ID', $id)->delete('suppliers');
            echo $this->messages = '1';
        }
    }

    public function cms_detail_supplier($id)
    {
        $id = (int)$id;
        $sup = $this->db->from('suppliers')->where('id', $id)->get()->row_array();
        if (!isset($sup) && count($sup) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_sup'] = $sup;
            $this->load->view('ajax/customer-supplier/detail_sup', isset($data) ? $data : null);
        }
    }

    public function cms_save_edit_sup($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');

        if ($data['supplier_image'] == '') {
            unset($data['supplier_image']);
        }

        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('suppliers', $data);
        echo $this->messages = '1';
    }
}
