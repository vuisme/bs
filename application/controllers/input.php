<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Input extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(5, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['user'] = $this->auth;
        $data['template'] = 'input/index';
        $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
        $data['data']['store_id'] = $this->auth['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_vsell_input()
    {
        if ($this->auth == null) $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        $data['data']['user'] = $this->auth;
        $this->load->view('ajax/input/import_bill', isset($data) ? $data : null);
    }

    public function cms_search_box_sup($keyword = '')
    {
        $sup = $this->db->like('supplier_name', $keyword)->or_like('supplier_phone', $keyword)->or_like('supplier_email', $keyword)->or_like('supplier_code', $keyword)->from('suppliers')->get()->result_array();
        $data['data']['suppliers'] = $sup;
        $this->load->view('ajax/input/search_box_sup', isset($data) ? $data : null);
    }

    public function cms_export_excel($input_id)
    {
        $input = $this->db
            ->from('input')
            ->where('ID', $input_id)
            ->get()
            ->row_array();

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'PhieuNhapHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Tên SP');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Mã SP');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Số lượng');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Đơn giá');

        $detail_input = json_decode($input['detail_input'], true);

        $rowCount = 2;
        foreach ((array)$detail_input as $element) {
            $product = $this->db
                ->from('products')->where('prd_serial', 0)
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

    public function cms_export_report_input()
    {
        $option = $this->input->post('data');
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option4'] >= 0) {
            $this->db->where('input.store_id', $option['option4']);
        }

        if ($option['option1'] == '0') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['report.deleted' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            } else {
                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['report.deleted' => 0])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '1') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {
                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['input.deleted' => 1])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            } else {
                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['input.deleted' => 1])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            }
        } else if ($option['option1'] == '2') {
            if ($option['date_from'] != '' && $option['date_to'] != '') {

                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['report.deleted' => 0, 'lack >' => 0])
                    ->where('input_date >=', $option['date_from'])
                    ->where('input_date <=', $option['date_to'])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            } else {
                $data['_list_products'] = $this->db
                    ->select('product_debt,transaction_code,input_date,cms_input.store_id,supplier_name,prd_code,prd_name,prd_group_id,input,username,price,prd_unit_id')
                    ->from('report')
                    ->join('products', 'report.product_id=products.ID', 'INNER')
                    ->join('input', 'input.ID=report.transaction_id', 'INNER')
                    ->join('users', 'users.id=report.user_init', 'INNER')
                    ->join('suppliers', 'suppliers.ID=report.supplier_id', 'LEFT')
                    ->order_by('report.created', 'desc')
                    ->where(['report.deleted' => 0, 'lack >' => 0])
                    ->where('type', 2)
                    ->get()
                    ->result_array();
            }
        }

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'BaoCaoNhapHang-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Ngày nhập');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Mã phiếu nhập');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Kho nhập');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Nhà cung cấp');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Mã SP');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Tên SP');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Danh mục');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('Người nhập');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('Số lượng nhập');
        $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('Giá nhập');
        $objPHPExcel->getActiveSheet()->getCell('K1', true)->setValue('ĐVT');
        $objPHPExcel->getActiveSheet()->getCell('L1', true)->setValue('Thành tiền');
        $objPHPExcel->getActiveSheet()->getCell('M1', true)->setValue('Nợ');

        $rowCount = 2;
        foreach ((array)$data['_list_products'] as $element) {
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue(cms_ConvertDateTime($element['input_date']));
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue(($element['transaction_code']));
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue(cms_getNamestockbyID($element['store_id']));
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($element['supplier_name']);
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($element['prd_code']);
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['prd_name']);
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_getNamegroupbyID($element['prd_group_id']));
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(($element['username']));
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue(($element['input']));
            $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue(($element['price']));
            $objPHPExcel->getActiveSheet()->getCell('K' . $rowCount, true)->setValue(cms_getNameunitbyID($element['prd_unit_id']));
            $objPHPExcel->getActiveSheet()->getCell('L' . $rowCount, true)->setValue(($element['input'] * $element['price']));
            $objPHPExcel->getActiveSheet()->getCell('M' . $rowCount, true)->setValue(($element['product_debt']));
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

        $path = "public/templates/uploads/";

        $valid_formats = array("xlsx", "xls");

        if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

            $name = $_FILES['excel']['name'];

            if (strlen($name)) {

                list($txt, $ext) = explode(".", $name);

                if (in_array($ext, $valid_formats)) {
                    $excel_name = time() . "." . $ext;
                    $tmp = $_FILES['excel']['tmp_name'];
                    if (move_uploaded_file($tmp, $path . $excel_name)) {

                        $inputFileName = $path . $excel_name;
                        try {
                            $inputFileType = \PhpOffice\PhpSpreadsheet\IOFactory::identify($inputFileName);
                            $objReader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader($inputFileType);
                            $objPHPExcel = $objReader->load($inputFileName);
                        } catch (Exception $e) {
                            echo $this->messages = '<script>
                                    alert("Bạn chưa chọn file. Vui lòng chọn lại");</script>';
                            return;
                        }
                        $allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null, true, true, true);

                        $arrayCount = count($allDataInSheet);
                        $createArray = array('Ma_San_Pham', 'So_Luong', 'Gia_Nhap', 'Serial');
                        $makeArray = array('Ma_San_Pham' => 'Ma_San_Pham', 'So_Luong' => 'So_Luong', 'Gia_Nhap' => 'Gia_Nhap', 'Serial' => 'Serial');
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
                            $su = '';
                            $er = '';
                            $prd_code = $SheetDataKey['Ma_San_Pham'];
                            $prd_sls = $SheetDataKey['So_Luong'];
                            $prd_origin_price = $SheetDataKey['Gia_Nhap'];
                            $serial = $SheetDataKey['Serial'];
                            for ($i = 2; $i <= $arrayCount; $i++) {
                                $data = array();
                                $data['prd_code'] = filter_var(trim($allDataInSheet[$i][$prd_code]), FILTER_SANITIZE_STRING);
                                if ($data['prd_code'] != '') {
                                    $data['prd_sls'] = filter_var(trim($allDataInSheet[$i][$prd_sls]), FILTER_SANITIZE_STRING);
                                    $data['prd_origin_price'] = filter_var(trim($allDataInSheet[$i][$prd_origin_price]), FILTER_SANITIZE_STRING);
                                    $data['serial'] = filter_var(trim($allDataInSheet[$i][$serial]), FILTER_SANITIZE_STRING);
                                    $data['serial'] = str_replace(' ', '', $data['serial']);
                                    if ($prd_code != '') {
                                        $check_code = $this->db->select('ID')->from('products')->where('prd_serial', 0)->where('deleted', 0)->where('deleted', 0)->where('prd_status', 1)->where(['prd_code' => $data['prd_code']])->get()->row_array();
                                        if (!empty($check_code)) {
                                            $su .= $this->cms_select_product($check_code['ID'], $i - 1, $data['prd_sls'], $data['prd_origin_price'], $data['serial']);
                                        } else
                                            $er .= '\nMã sản phẩm ' . $data['prd_code'] . ' ở dòng thứ ' . $i . ' không tồn tại hoặc đã bị xóa.';
                                    }
                                }
                            }

                            if ($er == '') {
                                $result = $su;
                            } else {
                                $result = '<script>alert("Sản phẩm nhập không thành công. ' . $er . '");</script>';
                            }

                            echo $result;
                        } else {
                            echo $this->messages = '<script>
                                    alert("File không đúng định dạng. Vui lòng tải file mẫu và thao tác lại");
                                    
                            </script>';
                        }
                    }
                } else
                    echo '<script>alert("File không đúng định dạng. Vui lòng chọn file khác")</script>';

            } else
                echo "Please select image..!";

            exit;

        }

    }

    public function cms_select_product($id = 0, $seq = 0, $quantity = 1, $price = 0, $serial = '')
    {
        if ($id == 0) {
            $id = $this->input->post('id');
            $seq = $this->input->post('seq');

            $product = $this->db
                ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_origin_price,prd_serial')
                ->from('products')->where('prd_serial', 0)
                ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
                ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
                ->get()
                ->row_array();
            if (isset($product) && count($product) != 0) {
                $price = $product['prd_origin_price'];
                if ($product['prd_serial'] == 1 && CMS_SERIAL == 1) {
                    $quantity = 0;
                } else {
                    $quantity = 1;
                }
            }
        } else {
            $product = $this->db
                ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_origin_price,prd_serial')
                ->from('products')->where('prd_serial', 0)
                ->where(['products.ID' => $id, 'deleted' => 0, 'prd_status' => 1])
                ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
                ->get()
                ->row_array();

            if ($product['prd_serial'] == 1 && CMS_SERIAL == 1) {
                $quantity = 0;
            }
        }

        if (isset($product) && count($product) != 0) {
            ob_start(); ?>
            <tr data-id="<?php echo $product['ID']; ?>">
                <td class="text-center seq hidden-xs"><?php echo $seq; ?></td>
                <td class="text-left hidden-xs"><?php echo $product['prd_code']; ?></td>
                <td class="text-left"><?php echo $product['prd_name']; ?></td>
                <td class="text-center zoomin hidden-xs"><img height="30"
                                                              src="public/templates/uploads/<?php echo cms_show_image($product['prd_image_url']); ?>">
                </td>

                <td class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>" style="max-width: 80px;"><input
                            style="min-width:55px;max-height: 34px;"
                            type="text"
                            class="txtMoney form-control expire text-center"
                            value="" placeholder="Hạn sử dụng"></td>
                <td class="text-center" style="max-width: 80px;"><input
                            style="min-width:55px;max-height: 34px;" <?php echo ($product['prd_serial'] == 1) ? 'disabled' : ''; ?>
                            type="text"
                            class="txtNumber form-control quantity_product_import text-center"
                            value="<?php echo $quantity; ?>"></td>

                <td class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>" style="max-width: 80px;"><input
                            style="min-width:55px;max-height: 34px;"
                            type="text"
                            class="txtNumber form-control item_discount text-center"
                            placeholder="0%"></td>

                <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>

                <td class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"><input
                            style="min-width:55px;max-height: 34px;" type="text"
                            class="form-control serial text-left <?php echo ($product['prd_serial'] == 0) ? 'hidden' : 'tags_input'; ?>"
                            value="<?php echo $serial; ?>"
                            placeholder="Nhập số Serial và enter"></td>

                <td class="text-center" style="max-width: 120px;">
                    <input style="min-width:82px;max-height: 34px;" type="text"
                           class="txtMoney form-control text-center price-input"
                           value="<?php echo cms_encode_currency_format($price); ?>">
                </td>
                <td class="text-center total-money"><?php echo cms_encode_currency_format($price * $quantity); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-input"></i></td>
            </tr>
            <script>
                $('input.tags_input').tagsinput({
                    confirmKeys: [13, 32, 44]
                });

                $('input.tags_input').on('itemAdded', function (event) {
                    cms_load_infor_import();
                });

                $('input.tags_input').on('itemRemoved', function (event) {
                    cms_load_infor_import();
                });
            </script>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function cms_select_product_input_excel()
    {
        $id = $this->input->post('id');
        $seq = $this->input->post('seq');
        $quantity = $this->input->post('quantity');
        $price = $this->input->post('price');
        $product = $this->db
            ->select('products.ID,prd_code,prd_unit_name,prd_name, prd_sell_price, prd_image_url,prd_origin_price')
            ->from('products')->where('prd_serial', 0)
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
                <td class="text-center" style="max-width: 80px;"><input style="min-width:55px;max-height: 34px;"
                                                                        type="text"
                                                                        class="txtNumber form-control quantity_product_import text-center"
                                                                        value="<?php echo $quantity; ?>"></td>
                <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>
                <td class="text-center" style="max-width: 120px;">
                    <input style="min-width:82px;max-height: 34px;" type="text"
                           class="txtMoney form-control text-center price-input"
                           value="<?php echo cms_encode_currency_format($price); ?>">
                </td>
                <td class="text-center total-money"><?php echo cms_encode_currency_format($price * $quantity); ?></td>
                <td class="text-center"><i class="fa fa-trash-o del-pro-input"></i></td>
            </tr>
            <?php
            $html = ob_get_contents();
            ob_end_clean();
            echo $html;
        }
    }

    public function cms_return_input($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $input = $this->db->from('input')->where(['ID' => $id, 'deleted' => 0, 'input_status' => 1, 'canreturn' => 1])->get()->row_array();
        if (isset($input) && count($input)) {
            $detail_input = $this->db
                ->select('cms_canreturn.ID,product_id,prd_code,prd_name,quantity,price,canreturn_expire as expire,prd_serial')
                ->from('canreturn')
                ->join('products', 'products.ID=canreturn.product_id', 'INNER')
                ->where(['input_id' => $input['ID'], 'quantity >' => 0])
                ->get()
                ->result_array();
        }
        $data['data']['_input'] = $input;
        $data['data']['_detail_input'] = $detail_input;
        $this->load->view('ajax/input/return', isset($data) ? $data : null);
    }

    public function cms_save_import($store_id)
    {
        $input = $this->input->post('data');
        $created_from = gmdate("Y-m-d H:i:s", time() + 7 * 3600 - 5);
        $created_to = gmdate("Y-m-d H:i:s", time() + 7 * 3600 + 5);
        $check_recent = $this->db->from('input')->where('store_id', $store_id)->where('supplier_id', $input['supplier_id'])->where('user_init', $this->auth['id'])->where('created >', $created_from)->where('created <', $created_to)->count_all_results();
        if ($check_recent > 0) {
            echo $this->messages = 'Vui lòng chờ 5 giây trước khi tạo phiếu nhập tiếp theo';
            return;
        } else
            if ($this->auth['store_id'] == $store_id) {
                if (empty($input['input_date'])) {
                    $input['input_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                } else {
                    $input['input_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $input['input_date'])) + 7 * 3600);
                }

                $total_price = 0;
                $total_quantity = 0;
                $this->db->trans_begin();
                $detail_input = array();
                if ($input['input_status'] == 1) {
                    foreach ((array)$input['detail_input'] as $item) {
                        $total_price += (($item['price'] - ($item['price'] * $item['discount']) / 100) * $item['quantity']);
                        $total_quantity += $item['quantity'];

                        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();
                        if ($item['price'] != $product['prd_origin_price']) {
                            $sls = array();
                            if ($product['prd_sls'] <= 0)
                                $sls['prd_origin_price'] = $item['price'];
                            else
                                $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) + ($item['quantity'] * $item['price'])) / ($product['prd_sls'] + $item['quantity']);

                            $this->db->where('ID', $item['id'])->update('products', $sls);
                        }

                        $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);
                        $detail_input[] = $item;
                    }

                    if (CMS_SERIAL == 0)
                        $resu = cms_input_inventory_and_expire_date($input['detail_input'], $store_id);
                    else
                        $resu = cms_input_inventory_and_serial($input['detail_input'], $store_id);

                    if ($resu != 1) {
                        $this->db->trans_rollback();
                        echo $this->messages = $resu;
                        return;
                    }
                } else
                    foreach ((array)$input['detail_input'] as $item) {
                        $total_price += (($item['price'] - ($item['price'] * $item['discount']) / 100) * $item['quantity']);
                        $total_quantity += $item['quantity'];
                        $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);
                        $detail_input[] = $item;
                    }

                $input['total_quantity'] = $total_quantity;
                $input['total_price'] = $total_price;
                $lack = $total_price - $input['payed'] - $input['discount'];
                $input['total_money'] = $total_price - $input['discount'];
                $input['lack'] = $lack > 0 ? $lack : 0;
                $input['store_id'] = $store_id;
                $input['user_init'] = $this->auth['id'];
                $input['detail_input'] = json_encode($detail_input);

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
                $id = $this->db->insert_id();

                $percent_discount = 0;
                if ($total_price != 0)
                    $percent_discount = $input['discount'] / $total_price;

                if ($input['input_status'] == 1) {
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
                    $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
                    $payment['user_init'] = $input['user_init'];
                    $this->db->insert('payment', $payment);

                    $temp = array();
                    $temp['transaction_code'] = $input['input_code'];
                    $temp['transaction_id'] = $id;
                    $temp['supplier_id'] = isset($input['supplier_id']) ? $input['supplier_id'] : 0;
                    $temp['date'] = $input['input_date'];
                    $temp['notes'] = $input['notes'];
                    $temp['user_init'] = $input['user_init'];
                    $temp['type'] = 2;
                    $temp['store_id'] = $store_id;

                    $canreturn_temp = array();
                    $canreturn_temp['store_id'] = $input['store_id'];
                    $canreturn_temp['input_id'] = $id;
                    $canreturn_temp['user_init'] = $input['user_init'];

                    foreach ((array)$detail_input as $item) {
                        $report = $temp;
                        $report['product_id'] = $item['id'];
                        $report['price'] = $item['price'];
                        $report['discount'] = $percent_discount * $item['quantity'] * $item['price'] + ($item['price'] * $item['discount'] * $item['quantity']) / 100;
                        $report['input'] = $item['quantity'];
                        $report['stock'] = 0;
                        $report['total_money'] = ($report['price'] * $report['input']) - $report['discount'];
                        $report['report_expire'] = $item['expire'];
                        $report['report_serial'] = $item['list_serial'];
                        $this->db->insert('report', $report);

                        if ($item['list_serial'] != '') {
                            $canreturn = $canreturn_temp;
                            $canreturn['product_id'] = $item['id'];
                            $canreturn['price'] = $item['price'] - ($percent_discount * $item['price']);
                            $canreturn['quantity'] = $item['quantity'];
                            $canreturn['canreturn_expire'] = $item['expire'];
                            $this->db->insert('canreturn', $canreturn);
                            $canreturn_id = $this->db->insert_id();

                            $list_serial = explode(",", $item['list_serial']);

                            foreach ((array)$list_serial as $serial) {
                                $canreturn_serial = array();
                                $canreturn_serial['serial'] = $serial;
                                $canreturn_serial['canreturn_id'] = $canreturn_id;
                                $canreturn_serial['input_id'] = $id;
                                $this->db->insert('canreturn_serial', $canreturn_serial);
                            }
                        } else {
                            $canreturn = $canreturn_temp;
                            $canreturn['product_id'] = $item['id'];
                            $canreturn['price'] = $item['price'] - ($percent_discount * $item['price']);
                            $canreturn['quantity'] = $item['quantity'];
                            $canreturn['canreturn_expire'] = $item['expire'];
                            $this->db->insert('canreturn', $canreturn);
                        }
                    }
                }

                $this->cms_update_report($id);
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

    public function cms_update_report($input_id)
    {
        $input = $this->db->from('input')->where(['ID' => $input_id, 'deleted' => 0])->get()->row_array();
        if (isset($input) && count($input)) {
            $input_detail = json_decode($input['detail_input'], true);

            if ($input['total_price'] == 0) {
                $percent_discount = 0;
            } else {
                $percent_discount = $input['discount'] / $input['total_price'];
            }

            if ($input['total_money'] == 0)
                $percent = 0;
            else
                $percent = $input['lack'] / $input['total_money'];
            foreach ((array)$input_detail as $item) {
                $total_money = ($item['price'] - $item['price'] * $percent_discount) * $item['quantity'];
                $report = array();
                $report['product_debt'] = $total_money * $percent;
                $this->db->where(['type' => 2, 'transaction_id' => $input_id, 'deleted' => 0, 'product_id' => $item['id']])->update('report', $report);
            }
        }
    }

    public function cms_update_input($input_id)
    {
        if ($this->auth == null || !in_array(15, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $input = $this->input->post('data');
        $store_id = $input['store_id'];
        $check_input = $this->db->from('input')->where(['deleted' => 0, 'ID' => $input_id])->get()->row_array();
        if ($this->auth['store_id'] == $store_id && !empty($check_input)) {
            $this->db->trans_begin();
            $user_init = $this->auth['id'];

            if ($check_input['input_status'] != 0) {
                $list_product_delete = json_decode($check_input['detail_input'], true);
                foreach ((array)$list_product_delete as $item) {
                    $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();

                    if ($item['price'] != $product['prd_origin_price'] && $product['prd_sls'] > $item['quantity']) {
                        $sls = array();
                        $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) - ($item['quantity'] * $item['price'])) / ($product['prd_sls'] - $item['quantity']);
                        $this->db->where('ID', $item['id'])->update('products', $sls);
                    }
                }

                $this->db->where(['transaction_id' => $input_id, 'store_id' => $check_input['store_id'], 'type' => 2])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);
                $this->db->where(['input_id' => $check_input['ID']])->delete('canreturn');
                $this->db->where(['input_id' => $check_input['ID']])->delete('canreturn_serial');
            } else {
                $input['input_status'] = 1;
            }

            if (empty($input['input_date'])) {
                $input['input_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $input['input_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $input['input_date'])) + 7 * 3600);
            }

            $total_price = 0;
            $total_quantity = 0;
            $detail_input = array();
            foreach ((array)$input['detail_input'] as $item) {
                $total_price += (($item['price'] - ($item['price'] * $item['discount']) / 100) * $item['quantity']);
                $total_quantity += $item['quantity'];

                $item['list_serial'] = $item['list_serial'] == '' ? '' : implode(",", $item['list_serial']);
                $detail_input[] = $item;
            }

            if ($check_input['input_status'] != 0) {
                $list_product_delete = json_decode($check_input['detail_input'], true);

                $resu = cms_output_inventory_and_serial_without_alert($list_product_delete, $check_input['store_id']);

                if ($resu != 1) {
                    $this->db->trans_rollback();
                    echo $this->messages = $resu;
                    return;
                }
            }

            $resu = cms_input_inventory_and_serial_without_alert($input['detail_input'], $input['store_id']);

            if ($resu != 1) {
                $this->db->trans_rollback();
                echo $this->messages = $resu;
                return;
            }

            if ($check_input['input_status'] != 0) {
                $list_product_delete = json_decode($check_input['detail_input'], true);

                $resu = cms_check_serial_with_alert($list_product_delete, $check_input['store_id']);

                if ($resu != 1) {
                    $this->db->trans_rollback();
                    echo $this->messages = $resu;
                    return;
                }
            }

            foreach ((array)$input['detail_input'] as $item) {
                $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();
                if ($item['price'] != $product['prd_origin_price']) {
                    $sls = array();

                    if (($product['prd_sls'] - $item['quantity']) <= 0)
                        $sls['prd_origin_price'] = $item['price'];
                    else
                        $sls['prd_origin_price'] = (($product['prd_origin_price'] * ($product['prd_sls'] - $item['quantity'])) + ($item['quantity'] * $item['price'])) / ($product['prd_sls']);

                    $this->db->where('ID', $item['id'])->update('products', $sls);
                }
            }

            $input['total_quantity'] = $total_quantity;
            $input['total_price'] = $total_price;
            $lack = $total_price - $input['payed'] - $input['discount'];
            $input['total_money'] = $total_price - $input['discount'];
            $input['lack'] = $lack > 0 ? $lack : 0;
            $input['store_id'] = $store_id;
            $input['detail_input'] = json_encode($detail_input);

            if ($input['supplier_id'] < 1 && $input['lack'] > 0) {
                $this->db->trans_rollback();
                echo $this->messages = "Vui lòng chọn nhà cung cấp để có thể nhập hàng nợ";
                return;
            }

            $this->db->where(['deleted' => 0, 'ID' => $input_id])->update('input', $input);

            $percent_discount = 0;
            if ($total_price != 0)
                $percent_discount = $input['discount'] / $total_price;

            $check_payment = $this->db->from('payment')->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->count_all_results();
            if ($check_payment > 1) {
                $this->db->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->update('payment', ['deleted' => 0, 'user_upd' => $user_init]);

                $payment['input_id'] = $input_id;
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
                $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
                $payment['user_init'] = $user_init;
                $this->db->insert('payment', $payment);
            } else {
                $check = $this->db->from('payment')->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->get()->row_array();
                if (empty($check)) {
                    $payment['input_id'] = $input_id;
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
                    $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
                    $payment['user_init'] = $user_init;
                    $this->db->insert('payment', $payment);
                } else {
                    $payment['store_id'] = $store_id;
                    $payment['notes'] = $input['notes'];
                    $payment['user_upd'] = $user_init;
                    $payment['payment_method'] = $input['payment_method'];
                    $payment['total_money'] = $input['payed'] - $total_price + $input['discount'] < 0 ? $input['payed'] : $total_price - $input['discount'];
                    $this->db->where(['deleted' => 0, 'input_id' => $input_id, 'total_money >' => 0])->update('payment', $payment);
                }
            }

            $temp = array();
            $temp['transaction_code'] = $check_input['input_code'];
            $temp['transaction_id'] = $input_id;
            $temp['supplier_id'] = isset($input['supplier_id']) ? $input['supplier_id'] : 0;
            $temp['date'] = $input['input_date'];
            $temp['notes'] = $input['notes'];
            $temp['user_init'] = $user_init;
            $temp['type'] = 2;
            $temp['store_id'] = $store_id;

            foreach ((array)$detail_input as $item) {
                $report = $temp;
                $report['product_id'] = $item['id'];
                $report['price'] = $item['price'];
                $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                $report['input'] = $item['quantity'];
                $report['stock'] = 0;
                $report['total_money'] = ($report['price'] * $report['input']) - $report['discount'];
                $report['report_expire'] = $item['expire'];
                $report['report_serial'] = $item['list_serial'];
                $this->db->insert('report', $report);

                $canreturn_temp = array();
                $canreturn_temp['store_id'] = $input['store_id'];
                $canreturn_temp['input_id'] = $input_id;
                $canreturn_temp['user_init'] = $check_input['user_init'];

                if ($item['list_serial'] != '') {
                    $canreturn = $canreturn_temp;
                    $canreturn['product_id'] = $item['id'];
                    $canreturn['price'] = $item['price'] - ($percent_discount * $item['price']);
                    $canreturn['quantity'] = $item['quantity'];
                    $canreturn['canreturn_expire'] = $item['expire'];
                    $this->db->insert('canreturn', $canreturn);
                    $canreturn_id = $this->db->insert_id();

                    $list_serial = explode(",", $item['list_serial']);

                    foreach ((array)$list_serial as $serial) {
                        $canreturn_serial = array();
                        $canreturn_serial['serial'] = $serial;
                        $canreturn_serial['canreturn_id'] = $canreturn_id;
                        $canreturn_serial['input_id'] = $input_id;
                        $this->db->insert('canreturn_serial', $canreturn_serial);
                    }
                } else {
                    $canreturn = $canreturn_temp;
                    $canreturn['product_id'] = $item['id'];
                    $canreturn['price'] = $item['price'] - ($percent_discount * $item['price']);
                    $canreturn['quantity'] = $item['quantity'];
                    $canreturn['canreturn_expire'] = $item['expire'];
                    $this->db->insert('canreturn', $canreturn);
                }
            }

            $this->cms_update_report($input_id);

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

    public function cms_save_input_return($store_id)
    {
        $order = $this->input->post('data');
        $created_from = gmdate("Y-m-d H:i:s", time() + 7 * 3600 - 5);
        $created_to = gmdate("Y-m-d H:i:s", time() + 7 * 3600 + 5);
        $check_recent = $this->db->from('orders')->where('store_id', $store_id)->where('user_init', $this->auth['id'])->where('created >', $created_from)->where('created <', $created_to)->count_all_results();
        if ($check_recent > 0) {
            echo $this->messages = 'Vui lòng chờ 5 giây trước khi tạo đơn hàng tiếp theo';
            return;
        } else
            if ($store_id == $this->auth['store_id']) {
                $detail_order = array();
                if (empty($order['sell_date'])) {
                    $order['sell_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                } else {
                    $order['sell_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $order['sell_date'])) + 7 * 3600);
                }
                $this->db->trans_begin();
                $user_init = $this->auth['id'];
                $total_price = 0;
                $total_origin_price = 0;
                $total_quantity = 0;

                if ($order['order_status'] == 1) {
                    foreach ((array)$order['detail_order'] as $item) {
                        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();
                        $canreturn = $this->db->select('ID,quantity,price')->from('canreturn')->where(['input_id' => $order['input_id'], 'ID' => $item['return_id']])->get()->row_array();
                        if (empty($canreturn) || $canreturn['quantity'] < 1 || $canreturn['quantity'] < $item['quantity']) {
                            $this->db->trans_rollback();
                            echo $this->messages = "0";
                            return;
                        } else {
                            $canreturn['quantity'] = $canreturn['quantity'] - $item['quantity'];
                            $canreturn['user_upd'] = $user_init;
                            $this->db->where(['input_id' => $order['input_id'], 'ID' => $item['return_id']])->update('canreturn', $canreturn);

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

                        $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
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
                } else
                    foreach ((array)$order['detail_order'] as $item) {
                        $total_price += ($item['price'] - $item['discount']) * $item['quantity'];
                        $total_quantity += $item['quantity'];
                        $detail_order[] = $item;
                    }

                if ($order['coupon'] == 'NaN')
                    $order['coupon'] = 0;

                $order['total_price'] = $total_price;
                $order['total_origin_price'] = $total_origin_price;
                $order['total_money'] = $total_price - $order['coupon'];
                $order['total_quantity'] = $total_quantity;
                $order['lack'] = $total_price - $order['customer_pay'] - $order['coupon'] > 0 ? $total_price - $order['customer_pay'] - $order['coupon'] : 0;
                $order['user_init'] = $this->auth['id'];
                $order['store_id'] = $store_id;
                $order['detail_order'] = json_encode($detail_order);

                $this->db->select_max('output_code')->like('output_code', 'PXT')->where('input_id >', 0);
                $max_output_code = $this->db->get('orders')->row();
                $max_code = (int)(str_replace('PXT', '', $max_output_code->output_code)) + 1;
                if ($max_code < 10)
                    $order['output_code'] = 'PXT00000' . ($max_code);
                else if ($max_code < 100)
                    $order['output_code'] = 'PXT0000' . ($max_code);
                else if ($max_code < 1000)
                    $order['output_code'] = 'PXT000' . ($max_code);
                else if ($max_code < 10000)
                    $order['output_code'] = 'PXT00' . ($max_code);
                else if ($max_code < 100000)
                    $order['output_code'] = 'PXT0' . ($max_code);
                else if ($max_code < 1000000)
                    $order['output_code'] = 'PXT' . ($max_code);

                $order['canreturn'] = 0;
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
                    $temp['user_init'] = $order['user_init'];
                    $temp['type'] = 7;
                    $temp['store_id'] = $order['store_id'];
                    foreach ((array)$detail_order as $item) {
                        $report = $temp;
                        $stock = $this->db->select('quantity')->from('inventory')->where(['store_id' => $temp['store_id'], 'product_id' => $item['id'], 'inventory_expire' => $item['expire']])->get()->row_array();
                        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();
                        $report['origin_price'] = $product['prd_origin_price'] * $item['quantity'];
                        $report['product_id'] = $item['id'];
                        $report['discount'] = $percent_discount * $item['quantity'] * $item['price'];
                        $report['price'] = $item['price'];
                        $report['output'] = $item['quantity'];
                        $report['stock'] = isset($stock['quantity']) ? $stock['quantity'] : 0;
                        $report['total_money'] = ($report['price'] * $report['output']) - $report['discount'];
                        $report['report_expire'] = $item['expire'];
                        $report['report_serial'] = $item['list_serial'];
                        $this->db->insert('report', $report);
                    }
                }

                $check = $this->db
                    ->select('sum(quantity) as total_quantity')
                    ->from('canreturn')
                    ->where('input_id', $order['input_id'])
                    ->get()
                    ->row_array();
                if (empty($check) || $check['total_quantity'] < 1) {
                    $this->db->where('ID', $order['input_id'])->update('input', ['canreturn' => 0, 'user_upd' => $user_init]);
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

    public function cms_autocomplete_products()
    {
        $data = $this->input->get('term');
        $products = $this->db
            ->from('products')->where('prd_serial', 0)
            ->where('(prd_code like "%' . $data . '%" or prd_name like "%' . $data . '%") and prd_status = 1 and deleted =0 ')
            ->get()
            ->result_array();
        echo json_encode($products);
    }

    public function cms_del_temp_import($id)
    {
        if ($this->auth == null || !in_array(16, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $input = $this->db->from('input')->where('ID', $id)->get()->row_array();
            $store_id = $input['store_id'];
            $this->db->trans_begin();
            $user_init = $this->auth['id'];
            if (isset($input) && count($input)) {
                if ($input['input_status'] == 1) {
                    $list_products = json_decode($input['detail_input'], true);
                    foreach ((array)$list_products as $item) {

                        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $item['id'])->get()->row_array();

                        if ($item['price'] != $product['prd_origin_price'] && $input['order_id'] == 0) {
                            if ($product['prd_sls'] - $item['quantity'] > 0) {
                                $sls = array();
                                $sls['prd_origin_price'] = (($product['prd_origin_price'] * $product['prd_sls']) - ($item['quantity'] * $item['price'])) / ($product['prd_sls'] - $item['quantity']);
                                $this->db->where('ID', $item['id'])->update('products', $sls);
                            }
                        }

                        if ($input['order_id'] > 0) {
                            $canreturn = $this->db->select('quantity,price')->from('canreturn')->where(['order_id' => $input['order_id'], 'ID' => $item['return_id']])->get()->row_array();
                            if (!empty($canreturn)) {
                                $canreturn['quantity'] = $canreturn['quantity'] + $item['quantity'];
                                $canreturn['user_upd'] = $user_init;
                                $this->db->where(['order_id' => $input['order_id'], 'product_id' => $item['id'], 'canreturn_expire' => $item['expire']])->update('canreturn', $canreturn);
                            }
                        }
                    }

                    $resu = cms_output_inventory_and_serial($list_products, $store_id);
                    if ($resu != 1) {
                        $this->db->trans_rollback();
                        echo $this->messages = $resu;
                        return;
                    }

                    $this->db->where(['transaction_id' => $id, 'store_id' => $store_id])->where_in('type', [2, 6])->update('report', ['deleted' => 1, 'user_upd' => $user_init]);

                    $this->db->where('input_id', $id)->update('payment', ['deleted' => 1, 'user_upd' => $user_init]);
                    $this->db->where('ID', $id)->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
                } else {
                    $this->db->where('ID', $id)->update('input', ['deleted' => 1, 'user_upd' => $user_init]);
                }
            }

            $check = $this->db
                ->select('sum(quantity) as check_quantity,canreturn')
                ->from('canreturn')
                ->where('order_id', $input['order_id'])
                ->join('orders', 'orders.ID=canreturn.order_id', 'INNER')
                ->get()
                ->row_array();
            if ($check['check_quantity'] > 0 && $check['canreturn'] == 0) {
                $this->db->where('ID', $input['order_id'])->update('orders', ['canreturn' => 1, 'user_upd' => $user_init]);
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

    public function cms_del_import($id)
    {
        if ($this->auth == null || !in_array(16, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $input = $this->db->from('input')->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        $this->db->trans_begin();
        if (isset($input) && count($input)) {
            $this->db->where('ID', $id)->update('input', ['deleted' => 2, 'user_upd' => $this->auth['id']]);
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

    public function cms_paging_input($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));
        $input_id = array();
        if ($option['keyword'] != '') {
            $input = $this->db
                ->select('distinct(cms_input.ID) as ID')
                ->from('input')
                ->join('suppliers', 'suppliers.ID=input.supplier_id', 'LEFT')
                ->where("(output_code LIKE '%" . $option['keyword'] . "%' OR supplier_email LIKE '%" . $option['keyword'] . "%' OR supplier_code LIKE '%" . $option['keyword'] . "%' OR supplier_name LIKE '%" . $option['keyword'] . "%' OR supplier_phone LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->result_array();

            if (isset($input) && count($input) > 0) {
                foreach ($input as $id) {
                    $input_id[] = $id['ID'];
                }
            } else {
                $input_id[] = 0;
            }

            $this->db->where_in('ID', $input_id);
        }

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_input.user_init', $this->auth['id']);
        }

        if ($option['option1'] == '0') {
            $this->db->where('deleted', 0);
        } else if ($option['option1'] == '1') {
            $this->db->where('deleted', 1);
        } else if ($option['option1'] == '2') {
            $this->db->where('deleted', 0)->where('lack >', 0);
        }

        if ($option['option2'] >= 0) {
            $this->db->where('input_status', $option['option2']);
        }

        if ($option['option3'] >= 0) {
            $this->db->where('supplier_id', $option['option3']);
        }

        if ($option['option4'] >= 0) {
            $this->db->where('store_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('input_date >=', $option['date_from'])
                ->where('input_date <=', $option['date_to']);
        }

        $total_input = $this->db
            ->select('count(ID) as quantity, sum(total_money) as total_money, sum(lack) as total_debt')
            ->from('input')
            ->get()
            ->row_array();

        if ($option['keyword'] != '') {
            $this->db->where_in('ID', $input_id);
        }

        if (!in_array(28, $this->auth['group_permission'])) {
            $this->db->where('cms_input.user_init', $this->auth['id']);
        }

        if ($option['option1'] == '0') {
            $this->db->where('deleted', 0);
        } else if ($option['option1'] == '1') {
            $this->db->where('deleted', 1);
        } else if ($option['option1'] == '2') {
            $this->db->where('deleted', 0)->where('lack >', 0);
        }

        if ($option['option2'] >= 0) {
            $this->db->where('input_status', $option['option2']);
        }

        if ($option['option3'] >= 0) {
            $this->db->where('supplier_id', $option['option3']);
        }

        if ($option['option4'] >= 0) {
            $this->db->where('store_id', $option['option4']);
        }

        if ($option['date_from'] != '' || $option['date_to'] != '') {
            $this->db->where('input_date >=', $option['date_from'])
                ->where('input_date <=', $option['date_to']);
        }

        $data['_list_input'] = $this->db
            ->from('input')
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('created', 'desc')
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_input';
        $config['total_rows'] = $total_input['quantity'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_input'] = $total_input;
        $data['auth_name'] = $this->auth['display_name'];
        if ($page > 1 && ($total_input['quantity'] - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['option'] = $option['option1'];
        $data['page'] = $page;
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/input/list_input', isset($data) ? $data : null);
    }

    public function cms_print_input()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data_post = $this->input->post('data');
        $data_template = $this->db->select('content')->from('templates')->where('id', $data_post['id_template'])->limit(1)->get()->row_array();
        $data_template['content'] = str_replace("{page_break}", '<div style="display: block; page-break-before: always;"></div>', $data_template['content']);

        $data_input = $this->db->from('input')->where('id', $data_post['id_input'])->limit(1)->get()->row_array();
        $supplier_name = '';
        if ($data_input['supplier_id'] != null)
            $supplier_name = cms_getNamesupplierbyID($data_input['supplier_id']);

        $user_name = '';
        if ($data_input['supplier_id'] != null)
            $user_name = cms_getNameAuthbyID($data_input['user_init']);

        $ngayin = gmdate("H:i d/m/Y", time() + 7 * 3600);
        $nguoiin = cms_getUserNameAuthbyID($this->auth['id']);

        $data_template['content'] = str_replace("{Ngay_In}", $ngayin, $data_template['content']);
        $data_template['content'] = str_replace("{Nguoi_In}", $nguoiin, $data_template['content']);
        $data_template['content'] = str_replace("{Ten_Cua_Hang}", "Phong Tran", $data_template['content']);
        $data_template['content'] = str_replace("{Ngay_Nhập}", $data_input['input_date'], $data_template['content']);
        $data_template['content'] = str_replace("{Nha_Cung_Cap}", $supplier_name, $data_template['content']);
        $data_template['content'] = str_replace("{Thu_Ngan}", $user_name, $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien_Hang}", cms_encode_currency_format($data_input['total_price']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_So_Luong}", $data_input['total_quantity'], $data_template['content']);
        $data_template['content'] = str_replace("{Chiet_Khau}", cms_encode_currency_format($data_input['discount']), $data_template['content']);
        $data_template['content'] = str_replace("{Tong_Tien}", cms_encode_currency_format($data_input['total_money']), $data_template['content']);
        $data_template['content'] = str_replace("{Tra_Tien}", cms_encode_currency_format($data_input['payed']), $data_template['content']);
        $data_template['content'] = str_replace("{Con_No}", cms_encode_currency_format($data_input['lack']), $data_template['content']);
        $data_template['content'] = str_replace("{Ma_Phieu_Nhap}", $data_input['input_code'], $data_template['content']);
        $data_template['content'] = str_replace("{Ghi_Chu}", $data_input['notes'], $data_template['content']);
        $data_template['content'] = str_replace("{So_Tien_Bang_Chu}", cms_convert_number_to_words($data_input['total_money']), $data_template['content']);

        $detail = '';
        $number = 1;
        if (CMS_SERIAL == 1) {
            if (isset($data_input) && count($data_input)) {
                $list_products = json_decode($data_input['detail_input'], true);
                foreach ((array)$list_products as $product) {
                    $prd = cms_finding_productbyID($product['id']);
                    $quantity = $product['quantity'];
                    $total = $quantity * $product['price'];
                    $detail = $detail . '<tr><td  style="text-align:center;">' . $number++ . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '</td><td style = "text-align:center">' . $quantity . '</td><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td><td style = "text-align:center">' . cms_convertserial($product['list_serial']) . '</td><td  style="text-align:center;">' . cms_encode_currency_format($product['price']) . '</td><td style="text-align:center;">' . cms_encode_currency_format($total) . '</td></tr>';
                }
            }

            $table = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Tên SP</strong></td>
                            <td style="text-align:center;"><strong>SL</strong></td>
                            <td style="text-align:center;"><strong>ĐVT</strong></td>
                            <td style="text-align:center;"><strong>Serial</strong></td>
                            <td style="text-align:center;"><strong>Đơn giá</strong></td>
                            <td style="text-align:center;"><strong>Thành tiền</strong></td>
                        </tr>' . $detail . '
                    </tbody>
                 </table>';
        } else {
            if (isset($data_input) && count($data_input)) {
                $list_products = json_decode($data_input['detail_input'], true);
                foreach ((array)$list_products as $product) {
                    $prd = cms_finding_productbyID($product['id']);
                    $quantity = $product['quantity'];
                    $total = $quantity * $product['price'];
                    $detail = $detail . '<tr><td  style="text-align:center;">' . $number++ . '</td><td  style="text-align:center;">' . $prd['prd_name'] . '</td><td style = "text-align:center">' . $quantity . '</td><td style = "text-align:center">' . $prd['prd_unit_name'] . '</td><td  style="text-align:center;">' . cms_encode_currency_format($product['price']) . '</td><td style="text-align:center;">' . cms_encode_currency_format($total) . '</td></tr>';
                }
            }

            $table = '<table border="1" style="width:100%;font-size: 13px;border-collapse:collapse;">
                    <tbody>
                        <tr>
                            <td style="text-align:center;"><strong>STT</strong></td>
                            <td style="text-align:center;"><strong>Tên SP</strong></td>
                            <td style="text-align:center;"><strong>SL</strong></td>
                            <td style="text-align:center;"><strong>ĐVT</strong></td>
                            <td style="text-align:center;"><strong>Đơn giá</strong></td>
                            <td style="text-align:center;"><strong>Thành tiền</strong></td>
                        </tr>' . $detail . '
                    </tbody>
                 </table>';
        }

        $data_template['content'] = str_replace("{Chi_Tiet_San_Pham}", $table, $data_template['content']);

        echo $this->messages = $data_template['content'];
    }

    public function cms_delete_payment_in_input($id)
    {
        $id = (int)$id;
        $payment = $this->db->from('payment')->where(['ID' => $id, 'deleted' => 0, 'type_id' => 2])->get()->row_array();
        $user_id = $this->auth['id'];
        $this->db->trans_begin();
        if (isset($payment) && count($payment)) {
            $input = $this->db->select('payed,lack')->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
            $input['payed'] = $input['payed'] - $payment['total_money'];
            $input['lack'] = $input['lack'] + $payment['total_money'];
            $input['user_upd'] = $user_id;
            $this->db->where('ID', $payment['input_id'])->update('input', $input);
            $this->db->where('ID', $id)->update('payment', ['deleted' => 1, 'user_upd' => $user_id]);

            $this->cms_update_report($payment['input_id']);
        }

        if ($this->db->trans_status() === FALSE) {
            $this->db->trans_rollback();
            echo $this->messages = "0";
        } else {
            $this->db->trans_commit();
            echo $this->messages = "1";
        }
    }

    public function save_payment_input()
    {
        $payment = $this->input->post('data');

        $input = $this->db->from('input')->where(['ID' => $payment['input_id'], 'deleted' => 0])->get()->row_array();
        if ($input['lack'] > 0) {
            $this->db->trans_begin();
            $update_input = array();
            if ($payment['total_money'] > $input['lack']) {
                $payment['total_money'] = $input['lack'];
                $update_input['payed'] = $input['payed'] + $input['lack'];
                $update_input['lack'] = 0;
                $update_input['user_upd'] = $this->auth['id'];
            } else {
                $update_input['payed'] = $input['payed'] + $payment['total_money'];
                $update_input['lack'] = $input['lack'] - $payment['total_money'];
                $update_input['user_upd'] = $this->auth['id'];
            }
            $this->db->where(['ID' => $payment['input_id'], 'deleted' => 0])->update('input', $update_input);

            if (empty($payment['payment_date'])) {
                $payment['payment_date'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            } else {
                $payment['payment_date'] = gmdate("Y-m-d H:i:s", strtotime(str_replace('/', '-', $payment['payment_date'])) + 7 * 3600);
            }

            $payment['user_init'] = $this->auth['id'];
            $payment['store_id'] = $this->auth['store_id'];
            $payment['type_id'] = 2;
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

            $this->db->insert('payment', $payment);

            $this->cms_update_report($payment['input_id']);

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

    public function cms_detail_input()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');
        $import = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $payment = $this->db->from('payment')->where(['input_id' => $id, 'type_id' => 2, 'deleted' => 0])->get()->result_array();
        $data['_list_products'] = array();

        if (isset($import) && count($import)) {
            $list_products = json_decode($import['detail_input'], true);

            foreach ((array)$list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                $_product['discount'] = isset($product['discount']) ? $product['discount'] : 0;
                $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';
                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_input'] = $import;
        $data['data']['_payment'] = $payment;
        $this->load->view('ajax/input/detail_input', isset($data) ? $data : null);
    }

    public function cms_edit_input()
    {
        if ($this->auth == null || !in_array(15, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = $this->input->post('id');
        $import = $this->db->from('input')->where('ID', $id)->get()->row_array();
        $data['_list_products'] = array();

        if (isset($import) && count($import)) {
            $list_products = json_decode($import['detail_input'], true);

            foreach ((array)$list_products as $product) {
                $_product = cms_finding_productbyID($product['id']);
                $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                $_product['discount'] = isset($product['discount']) ? $product['discount'] : 0;
                $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';

                $data['_list_products'][] = $_product;
            }
        }

        $data['data']['_input'] = $import;
        $this->load->view('ajax/input/edit_import', isset($data) ? $data : null);
    }
}

