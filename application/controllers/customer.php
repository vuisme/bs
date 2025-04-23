<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Customer extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(4, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $data['supplier'] = 0;
            if (in_array(27, $this->auth['group_permission'])) {
                $data['supplier'] = 1;
            }

            $data['seo']['title'] = "Phần mềm quản lý bán hàng";
            $data['user'] = $this->auth;
            $data['template'] = 'customer/index';
            $this->load->view('layout/index', isset($data) ? $data : null);
        }
    }

    public function cms_print_customer()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_template['content'] = str_replace("{page_break}", '<div style="display: block; page-break-before: always;"></div>', $data_template['content']);

        $customer = $this->db->from('customers')->where('ID', $data_post['id_customer'])->get()->row_array();
        $customer_name = $customer['customer_name'];
        $customer_code = $customer['customer_code'];
        $customer_email = $customer['customer_email'];
        $customer_phone = $customer['customer_phone'];
        $customer_address = $customer['customer_addr'];
        $order = $this->db
            ->select('sum(lack) as debt')
            ->from('orders')
            ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'customer_id' => $customer['ID']])
            ->get()
            ->row_array();
        $debt = $order['debt'];
        $ngayin = gmdate("H:i d/m/Y", time() + 7 * 3600);
        $nguoiin = cms_getUserNameAuthbyID($this->auth['id']);
        $data_template['content'] = str_replace("{Ngay_In}", $ngayin, $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_In}", $nguoiin, $data_template['content']);
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", cms_getNamestockbyID($this->auth['store_id']), $data_template['content']);
        $data_template['content'] = str_replace("{Khach_Hang}", $customer_name, $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Khach_Hang}", $customer_code, $data_template['content']);
        $data_template['content'] = str_replace("{DT_Khach_Hang}", $customer_phone, $data_template['content']);
        $data_template['content'] = str_replace("{DC_Khach_Hang}", $customer_address, $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", cms_convert_number_to_words($debt), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Cong_No}", cms_encode_currency_format($debt), $data_template['content']);
        $number = 1;
        $detail = '';
        if (isset($customer) && count($customer)) {
            $list_order = $this->db
                ->from('orders')
                ->where(['deleted' => 0, 'order_status' => 1, 'lack >' => 0, 'customer_id' => $customer['ID']])
                ->get()
                ->result_array();
            foreach ((array)$list_order as $order) {
                $list_products = json_decode($order['detail_order'], true);
                $product_string = '';
                foreach ((array)$list_products as $product) {
                    $prd = cms_finding_productbyID($product['id']);
                    $product_string .= '&nbsp;- &nbsp;' . $prd['prd_name'] . ': ' . $product['quantity'] . '<br>';
                }

                $detail = $detail . '<tr>
                                        <td style="text-align:center;">' . $number++ . '</td>
                                        <td style = "text-align:center">' . $order['output_code'] . '</td>
                                        <td style = "text-align:left">' . $product_string . '</td>
                                        <td style = "text-align:center">' . cms_ConvertDateTime($order['sell_date']) . '</td>
                                        <td style = "text-align:center">' . $order['total_quantity'] . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($order['total_money']) . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($order['customer_pay']) . '</td>
                                        <td style = "text-align:center">' . cms_encode_currency_format($order['lack']) . '</td>';
            }
        }

        $table = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Mã đơn hàng</strong></td>
                            <td style="text-align:center;"><strong>Sản phẩm</strong></td>
                            <td style="text-align:center;"><strong>Ngày bán</strong></td>
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

    public function cms_change_province($id)

    {

        $id = (int)$id;

        $data['list_district'] = cms_getListDistrictByProvince($id);

        $this->load->view('ajax/customer-supplier/list_district', isset($data) ? $data : null);

    }

    public function cms_change_district($id)

    {

        $id = (int)$id;

        $data['list_ward'] = cms_getListWardByDistrict($id);

        $this->load->view('ajax/customer-supplier/list_ward', isset($data) ? $data : null);

    }

    public function cms_export_customer()
    {
        $option = $this->input->post('data');

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_customers.user_init', $this->auth['id']);
        }

        if ($option['option'] == 1) {
            $this->db->where('total_money_order >', 0);
        } else if ($option['option'] == 2) {
            $this->db->where('customer_debt >', 0);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(customer_addr LIKE '%" . $option['keyword'] . "%' OR customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $data['_list_customer'] = $this->db
            ->from('customers')
            ->order_by('ID', 'desc')
            ->get()
            ->result_array();

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'KhachHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Hinh_Anh');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Nhom_Khach_Hang');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ma_Khach_Hang');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Ten_Khach_Hang');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Dien_Thoai');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Email');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Ma_So_Thue');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Tinh_Thanh_Pho');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Quan_Huyen');
        $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('Phuong_Xa');
        $objPHPExcel->getActiveSheet()->getCell('K1', true)->setValue('Dia_Chi');
        $objPHPExcel->getActiveSheet()->getCell('L1', true)->setValue('Ban_Do');
        $objPHPExcel->getActiveSheet()->getCell('M1', true)->setValue('Ghi_Chu');
        $objPHPExcel->getActiveSheet()->getCell('N1', true)->setValue('Ngay_Sinh');
        $objPHPExcel->getActiveSheet()->getCell('O1', true)->setValue('Gioi_Tinh');
        $objPHPExcel->getActiveSheet()->getCell('P1', true)->setValue('Sản phẩm đã mua');
        $objPHPExcel->getActiveSheet()->getCell('Q1', true)->setValue('Lần cuối mua hàng');
        $objPHPExcel->getActiveSheet()->getCell('R1', true)->setValue('Tổng tiền hàng');
        $objPHPExcel->getActiveSheet()->getCell('S1', true)->setValue('Cong_No');
        $rowCount = 2;
        foreach ((array)$data['_list_customer'] as $element) {
            $product_string = '';

            $list_product = $this->db->select('product_id,sum(output) as total_quantity')->from('report')->where('customer_id', $element['ID'])->where('type', 3)->where('deleted', 0)->group_by('product_id')->get()->result_array();

            foreach ((array)$list_product as $prd) {
                $product = cms_finding_productbyID($prd['product_id']);
                $product_string .= $product['prd_name'] . ': ' . $prd['total_quantity'] . PHP_EOL;
            }

            $product_string = trim($product_string);

            if ($element['customer_image'] != '') {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(CMS_BASE_URL . 'public/templates/uploads/' . cms_show_image($element['customer_image']));
            } else {
                $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue('');
            }

            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($element['customer_group'] == 0 ? 'Khách lẻ' : 'Khách sỉ');
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['customer_code']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($element['customer_name']);
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($element['customer_phone']);
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['customer_email']);
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($element['customer_tax']);
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(cms_getProvinceNameByID($element['province_id']));
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue(cms_getDistrictNameByID($element['district_id']));
            $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue(cms_getWardNameByID($element['ward_id']));
            $objPHPExcel->getActiveSheet()->getCell('K' . $rowCount, true)->setValue($element['customer_addr']);
            $objPHPExcel->getActiveSheet()->getCell('L' . $rowCount, true)->setValue($element['customer_map']);
            $objPHPExcel->getActiveSheet()->getCell('M' . $rowCount, true)->setValue($element['notes']);
            $objPHPExcel->getActiveSheet()->getCell('N' . $rowCount, true)->setValue(cms_ConvertDate($element['customer_birthday']));
            $objPHPExcel->getActiveSheet()->getCell('O' . $rowCount, true)->setValue($element['customer_gender'] == 0 ? 'Nam' : 'Nữ');
            $objPHPExcel->getActiveSheet()->getCell('P' . $rowCount, true)->setValue($product_string);
            $objPHPExcel->getActiveSheet()->getStyle('P' . $rowCount)->getAlignment()->setWrapText(true);
            $objPHPExcel->getActiveSheet()->getCell('Q' . $rowCount, true)->setValue(cms_ConvertDateTime($element['last_sell_date']));
            $objPHPExcel->getActiveSheet()->getCell('R' . $rowCount, true)->setValue(($element['total_money_order']));
            $objPHPExcel->getActiveSheet()->getStyle('R' . $rowCount)->getNumberFormat()->setFormatCode("#,##0");

            $objPHPExcel->getActiveSheet()->getCell('S' . $rowCount, true)->setValue(($element['customer_debt']));
            $objPHPExcel->getActiveSheet()->getStyle('S' . $rowCount)->getNumberFormat()->setFormatCode("#,##0");

            $rowCount++;
        }
        foreach (range('A', $objPHPExcel->getActiveSheet()->getHighestDataColumn()) as $col) {
            $objPHPExcel->getActiveSheet()
                ->getColumnDimension($col, true)
                ->setAutoSize(true);
        }

        $style = array(
            'alignment' => array(
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
            )
        );

        $objPHPExcel->getActiveSheet()->getStyle('A1:S' . ($rowCount - 1))->applyFromArray($style, true);

        $objWriter = new Xlsx($objPHPExcel);
        $objWriter->save(ROOT_UPLOAD_IMPORT_PATH . $fileName);
        header("Content-Type: application/vnd.ms-excel");
        echo $this->messages = (HTTP_UPLOAD_IMPORT_PATH . $fileName);
    }

    public function cms_paging_order_by_customer_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();

        $total_orders = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('orders')
            ->where_not_in('order_status', [0, 5])
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where('customer_id', $option['customer_id'])
            ->get()
            ->row_array();
        $data['_list_orders'] = $this->db
            ->from('orders')
            ->where_not_in('order_status', [0, 5])
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where('customer_id', $option['customer_id'])
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_order_by_customer_id';
        $config['total_rows'] = $total_orders['quantity'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        if (count($data['_list_orders']) > 0)
            $this->load->view('ajax/customer-supplier/list_orders', isset($data) ? $data : null);
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
                'Nhom_Khach_Hang',
                'Ma_Khach_Hang',
                'Ten_Khach_Hang',
                'Dien_Thoai',
                'Email',
                'Ma_So_Thue',
                'Tinh_Thanh_Pho',
                'Quan_Huyen',
                'Phuong_Xa',
                'Dia_Chi',
                'Ban_Do',
                'Ghi_Chu',
                'Ngay_Sinh',
                'Gioi_Tinh',
                'Cong_No');
            $makeArray = array(
                'Hinh_Anh' => 'Hinh_Anh',
                'Nhom_Khach_Hang' => 'Nhom_Khach_Hang',
                'Ma_Khach_Hang' => 'Ma_Khach_Hang',
                'Ten_Khach_Hang' => 'Ten_Khach_Hang',
                'Dien_Thoai' => 'Dien_Thoai',
                'Email' => 'Email',
                'Ma_So_Thue' => 'Ma_So_Thue',
                'Tinh_Thanh_Pho' => 'Tinh_Thanh_Pho',
                'Quan_Huyen' => 'Quan_Huyen',
                'Phuong_Xa' => 'Phuong_Xa',
                'Dia_Chi' => 'Dia_Chi',
                'Ban_Do' => 'Ban_Do',
                'Ghi_Chu' => 'Ghi_Chu',
                'Ngay_Sinh' => 'Ngay_Sinh',
                'Gioi_Tinh' => 'Gioi_Tinh',
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
                $customer_image = $SheetDataKey['Hinh_Anh'];
                $customer_group = $SheetDataKey['Nhom_Khach_Hang'];
                $customer_code = $SheetDataKey['Ma_Khach_Hang'];
                $customer_name = $SheetDataKey['Ten_Khach_Hang'];
                $customer_phone = $SheetDataKey['Dien_Thoai'];
                $customer_email = $SheetDataKey['Email'];
                $customer_tax = $SheetDataKey['Ma_So_Thue'];
                $province_id_temp = $SheetDataKey['Tinh_Thanh_Pho'];
                $district_id_temp = $SheetDataKey['Quan_Huyen'];
                $ward_id_temp = $SheetDataKey['Phuong_Xa'];
                $customer_addr = $SheetDataKey['Dia_Chi'];
                $customer_map = $SheetDataKey['Ban_Do'];
                $notes = $SheetDataKey['Ghi_Chu'];
                $customer_birthday = $SheetDataKey['Ngay_Sinh'];
                $customer_gender = $SheetDataKey['Gioi_Tinh'];
                $customer_debt = $SheetDataKey['Cong_No'];

                for ($i = 2; $i <= $arrayCount; $i++) {
                    $data = array();
                    $data['customer_name'] = filter_var(trim($allDataInSheet[$i][$customer_name]), FILTER_SANITIZE_STRING);
                    if ($data['customer_name'] != '') {
                        $data['customer_image'] = str_replace(CMS_BASE_URL . 'public/templates/uploads/', '', filter_var(trim($allDataInSheet[$i][$customer_image]), FILTER_SANITIZE_STRING));
                        $data['customer_group'] = $this->cms_check_customer_group(filter_var(trim($allDataInSheet[$i][$customer_group]), FILTER_SANITIZE_STRING));
                        $data['customer_code'] = filter_var(trim($allDataInSheet[$i][$customer_code]), FILTER_SANITIZE_STRING);
                        $data['customer_phone'] = filter_var(trim($allDataInSheet[$i][$customer_phone]), FILTER_SANITIZE_STRING);
                        $data['customer_email'] = filter_var(trim($allDataInSheet[$i][$customer_email]), FILTER_SANITIZE_STRING);
                        $data['customer_tax'] = filter_var(trim($allDataInSheet[$i][$customer_tax]), FILTER_SANITIZE_STRING);
                        $data['customer_addr'] = filter_var(trim($allDataInSheet[$i][$customer_addr]), FILTER_SANITIZE_STRING);
                        $data['customer_map'] = filter_var(trim($allDataInSheet[$i][$customer_map]), FILTER_SANITIZE_STRING);
                        $data['notes'] = filter_var(trim($allDataInSheet[$i][$notes]), FILTER_SANITIZE_STRING);
                        $data['customer_birthday'] = filter_var(trim($allDataInSheet[$i][$customer_birthday]), FILTER_SANITIZE_STRING);
                        $data['customer_birthday'] = ($data['customer_birthday'] == '' ? '' : gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600));
                        $data['customer_gender'] = filter_var(trim($allDataInSheet[$i][$customer_gender]), FILTER_SANITIZE_STRING);
                        $data['customer_gender'] = $data['customer_gender'] == 'Nam' ? 0 : 1;
                        $data['customer_debt'] = filter_var(trim($allDataInSheet[$i][$customer_debt]), FILTER_SANITIZE_STRING);

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

                        if ($customer_code != '') {
                            $check_code = $this->db->from('customers')->where(['customer_code' => $data['customer_code']])->count_all_results();
                            if ($check_code == 1) {
                                unset($data['customer_debt']);
                                $this->db->where('customer_code', $data['customer_code'])->update('customers', $data);
                            } else if ($check_code > 1) {
                                $er .= '\n Mã khách hàng ' . $data['customer_code'] . ' ở dòng thứ ' . $i . ' đã bị trùng nhiều lần.';
                            } else if ($data['customer_name'] == '') {
                                $er .= '\n Tên khách hàng ' . $data['customer_name'] . ' ở dòng thứ ' . $i . ' không được rỗng.';
                            } else {
                                $id = $this->cms_save_customer($data);

                                if ($data['customer_debt'] != '' && $data['customer_debt'] > 0) {
                                    $total_debt = $data['customer_debt'];
                                    $order['lack'] = $total_debt;
                                    $order['user_init'] = $this->auth['id'];
                                    $order['store_id'] = $this->auth['store_id'];
                                    $order['total_price'] = $total_debt;
                                    $order['customer_id'] = $id;
                                    $order['order_status'] = 1;

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
                                    $this->db->insert('orders', $order);
                                }

                                cms_updatecustomerdebtbycustomerid($id);
                            }
                        } else {
                            if ($data['customer_name'] == '') {
                                $er .= '\n Tên khách hàng ' . $data['customer_name'] . ' ở dòng thứ ' . $i . ' không được rỗng.';
                            } else {
                                $id = $this->cms_save_customer($data);

                                if ($data['customer_debt'] != '' && $data['customer_debt'] > 0) {
                                    $total_debt = $data['customer_debt'];
                                    $order['lack'] = $total_debt;
                                    $order['user_init'] = $this->auth['id'];
                                    $order['store_id'] = $this->auth['store_id'];
                                    $order['total_price'] = $total_debt;
                                    $order['customer_id'] = $id;
                                    $order['order_status'] = 1;

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
                                    $this->db->insert('orders', $order);
                                }

                                cms_updatecustomerdebtbycustomerid($id);
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

    public function cms_check_customer_group($text)
    {
        $temp = array('1', 'Sỉ', 'sỉ', 'Si', 'si', 'Khách sỉ', 'Khách Sỉ', 'khách sỉ', 'khách Sỉ', 'khach si', 'khach sỉ', 'khách si');
        if (in_array($text, $temp))
            return 1;
        else
            return 0;
    }

    public function cms_save_customer($data)
    {
        $data['customer_birthday'] = ($data['customer_birthday'] == '' ? '' : gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600));
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];
        if ($data['customer_code'] == '') {
            $max_customer_code = $this->db->select_max('customer_code')->from('customers')->where('LENGTH(customer_code) = 8')->where("(customer_code LIKE 'KH" . "%')", NULL, FALSE)->get()->row_array();

            if (isset($max_customer_code) && count($max_customer_code) > 0) {
                $max_code = (int)(str_replace('KH', '', $max_customer_code['customer_code'])) + 1;
                if ($max_code < 10)
                    $data['customer_code'] = 'KH00000' . ($max_code);
                else if ($max_code < 100)
                    $data['customer_code'] = 'KH0000' . ($max_code);
                else if ($max_code < 1000)
                    $data['customer_code'] = 'KH000' . ($max_code);
                else if ($max_code < 10000)
                    $data['customer_code'] = 'KH00' . ($max_code);
                else if ($max_code < 100000)
                    $data['customer_code'] = 'KH0' . ($max_code);
                else if ($max_code < 1000000)
                    $data['customer_code'] = 'KH' . ($max_code);
            } else {
                $data['customer_code'] = 'KH000001';
            }

            $this->db->insert('customers', $data);
            $id = $this->db->insert_id();

            return $id;
        } else {
            $this->db->insert('customers', $data);
            $id = $this->db->insert_id();

            return $id;
        }
    }

    public function cms_paging_order_debt_by_customer_id($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $config['per_page'] = 100;

        $total_orders = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('orders')->where('orders.store_id', $this->auth['store_id'])
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where_not_in('order_status', [0, 5])
            ->where(['customer_id' => $option['customer_id'], 'lack >' => 0])
            ->get()
            ->row_array();
        $data['_list_orders'] = $this->db
            ->from('orders')->where('orders.store_id', $this->auth['store_id'])
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'asc')
            ->where('deleted', 0)
            ->where('input_id', 0)
            ->where_not_in('order_status', [0, 5])
            ->where(['customer_id' => $option['customer_id'], 'lack >' => 0])
            ->get()
            ->result_array();
        $config['base_url'] = 'cms_paging_order_debt_by_customer_id';
        $config['total_rows'] = $total_orders['quantity'];
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_orders'] = $total_orders;
        if ($page > 1 && ($total_orders['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/customer-supplier/list_orders_debt', isset($data) ? $data : null);
    }

    public function cms_paging_customer($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $option = $this->input->post('data');

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_customers.user_init', $this->auth['id']);
        }

        if ($option['option'] == 1) {
            $this->db->where('total_money_order >', 0);
        } else if ($option['option'] == 2) {
            $this->db->where('customer_debt >', 0);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(customer_addr LIKE '%" . $option['keyword'] . "%' OR customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $total_customer = $this->db
            ->select('count(distinct(ID)) as total_quantity,sum(customer_debt) as total_customer_debt,sum(total_money_order) as total_money_order')
            ->from('customers')
            ->get()
            ->row_array();

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_customers.user_init', $this->auth['id']);
        }

        if ($option['option'] == 1) {
            $this->db->where('total_money_order >', 0);
        } else if ($option['option'] == 2) {
            $this->db->where('customer_debt >', 0);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(customer_addr LIKE '%" . $option['keyword'] . "%' OR customer_code LIKE '%" . $option['keyword'] . "%' OR customer_name LIKE '%" . $option['keyword'] . "%' OR customer_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $data['_list_customer'] = $this->db
            ->from('customers')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('customers.ID', 'desc')
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_customer';
        $config['total_rows'] = isset($total_customer['total_quantity']) ? $total_customer['total_quantity'] : 0;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['_total_customer'] = $total_customer;
        $data['_pagination_link'] = $_pagination_link;
        if ($page > 1 && ($total_customer['total_quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['page'] = $page;
        $this->load->view('ajax/customer-supplier/list_customer', isset($data) ? $data : null);
    }

    public function cms_detail_customer($id)
    {
        $id = (int)$id;
        $cus = $this->db->from('customers')->where('ID', $id)->get()->row_array();
        if (!isset($cus) && count($cus) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_cus'] = $cus;
            $data['customer_id'] = $id;
            $this->load->view('ajax/customer-supplier/detail_cus', isset($data) ? $data : null);
        }
    }

    public function cms_crcustomer($total_debt)
    {
        $data = $this->input->post('data');
        $data['customer_birthday'] = ($data['customer_birthday'] == '' ? '' : gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600));
        $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_init'] = $this->auth['id'];

        $check_phone = $this->db->from('customers')->where(['customer_phone' => $data['customer_phone'], 'customer_phone <>' => ''])->count_all_results();
        if ($check_phone > 0) {
            echo $this->messages = "Số điện thoại khách hàng bị trùng. Vui lòng kiểm tra lại";
            return;
        }

        if ($data['customer_code'] == '') {
            $max_customer_code = $this->db->select_max('customer_code')->from('customers')->where('LENGTH(customer_code) = 8')->where("(customer_code LIKE 'KH" . "%')", NULL, FALSE)->get()->row_array();

            if (isset($max_customer_code) && count($max_customer_code) > 0) {
                $max_code = (int)(str_replace('KH', '', $max_customer_code['customer_code'])) + 1;
                if ($max_code < 10)
                    $data['customer_code'] = 'KH00000' . ($max_code);
                else if ($max_code < 100)
                    $data['customer_code'] = 'KH0000' . ($max_code);
                else if ($max_code < 1000)
                    $data['customer_code'] = 'KH000' . ($max_code);
                else if ($max_code < 10000)
                    $data['customer_code'] = 'KH00' . ($max_code);
                else if ($max_code < 100000)
                    $data['customer_code'] = 'KH0' . ($max_code);
                else if ($max_code < 1000000)
                    $data['customer_code'] = 'KH' . ($max_code);
            } else {
                $data['customer_code'] = 'KH000001';
            }
            $this->db->insert('customers', $data);
            $id = $this->db->insert_id();

            if ($total_debt != '' && $total_debt > 0) {
                $order['lack'] = $total_debt;
                $order['user_init'] = $this->auth['id'];
                $order['store_id'] = $this->auth['store_id'];
                $order['total_price'] = $total_debt;
                $order['customer_id'] = $id;
                $order['order_status'] = 1;

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
                $this->db->insert('orders', $order);
            }

            cms_updatecustomerdebtbycustomerid($id);

            echo $this->messages = $id;
        } else {
            $count = $this->db->where('customer_code', $data['customer_code'])->from('customers')->count_all_results();
            if ($count > 0) {
                echo $this->messages = "0";
            } else {
                $this->db->insert('customers', $data);
                $id = $this->db->insert_id();

                if ($total_debt != '' && $total_debt > 0) {
                    $order['lack'] = $total_debt;
                    $order['user_init'] = $this->auth['id'];
                    $order['store_id'] = $this->auth['store_id'];
                    $order['total_price'] = $total_debt;
                    $order['customer_id'] = $id;
                    $order['order_status'] = 1;

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
                    $this->db->insert('orders', $order);
                }

                cms_updatecustomerdebtbycustomerid($id);

                echo $this->messages = $id;
            }
        }
    }

    public function cms_delCustomer()
    {
        $id = (int)$this->input->post('id');
        $customer = $this->db->from('customers')->where('ID', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages = '-1';
            return;
        } else {
            $check_order = $this->db->from('orders')->where(['customer_id' => $id, 'deleted' => 0, 'order_status <' => 5])->count_all_results();
            if ($check_order > 0) {
                echo $this->messages = '0';
                return;
            } else {
                $this->db->where('ID', $id)->delete('customers');
                echo $this->messages = '1';
            }
        }
    }

    public function cms_edit_customer()
    {
        $id = (int)$this->input->post('id');
        $customer = $this->db->from('customers')->where('id', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;
            return;
        } else {
            ob_start();
            $html = ob_get_contents();
            ob_end_clean();
        }
    }

    public function cms_detail_itemcust($id)
    {
        $id = (int)$id;
        $customer = $this->db->from('customers')->where('id', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;
            return;
        } else {
            $data['_list_cus'] = $customer;
            $data['_list_cus']['customer_birthday'] = ($customer['customer_birthday'] != '1970-01-01 07:00:00') ? gmdate("d/m/Y", strtotime(str_replace('-', '/', $customer['customer_birthday'])) + 7 * 3600) : '';
            $this->load->view('ajax/customer-supplier/detail_cus', isset($data) ? $data : null);
        }
    }

    public function cms_detail_order_in_customer()
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
        $this->load->view('ajax/customer-supplier/detail_order', isset($data) ? $data : null);
    }

    public function cms_save_edit_customer($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        if ($data['customer_image'] == '')
            unset($data['customer_image']);

        $data['customer_birthday'] = ($data['customer_birthday'] == '' ? '' : gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $data['customer_birthday'])) + 7 * 3600));
        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('customers', $data);
        echo $this->messages = '1';
    }
}
