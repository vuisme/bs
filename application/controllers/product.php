<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Product extends CI_Controller
{
    public $auth;
    private $messages = '0';

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(3, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');


        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['_prd_manufacture'] = $this->db->from('products_manufacture')->get()->result_array();
        $data['data']['user'] = $this->auth;
        $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
        $data['data']['store_id'] = $this->auth['store_id'];
        $data['template'] = 'products/index';
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_export_product()
    {
        $option = $this->input->post('data');
        if ($option['option2'] > '-1') {
            $temp = $this->getCategoriesByParentId($option['option2']);
            $temp[] = $option['option2'];
            $this->db->where_in('prd_group_id', $temp);
        }

        if ($option['option1'] == '0') {
            $this->db->where(['prd_status' => 1, 'deleted' => 0]);
        } else if ($option['option1'] == '1') {
            $this->db->where(['prd_status' => 0, 'deleted' => 0]);
        } else if ($option['option1'] == '2') {
            $this->db->where(['deleted' => 1]);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('prd_manufacture_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(prd_descriptions LIKE '%" . $option['keyword'] . "%' OR prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $data['data']['_list_product'] = $this->db
            ->from('products')->where('prd_serial', 0)
            ->order_by('prd_code', 'acs')
            ->get()
            ->result_array();

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'DanhSachSanPham-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('TEN_SAN_PHAM');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('MA_SAN_PHAM');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('SO_LUONG');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('DON_VI_TINH');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('CHO_SUA_GIA_KHI_BAN');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('CHO_PHEP_BAN_AM');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('KEM_QUA_TANG');
        $objPHPExcel->getActiveSheet()->getCell('H1', true)->setValue('CO_SERIAL');
        $objPHPExcel->getActiveSheet()->getCell('I1', true)->setValue('GIA_VON');
        $objPHPExcel->getActiveSheet()->getCell('J1', true)->setValue('THONG_TIN_THEM');
        $objPHPExcel->getActiveSheet()->getCell('K1', true)->setValue('VI_TRI');
        $objPHPExcel->getActiveSheet()->getCell('L1', true)->setValue('LINK_NHAP');
        $objPHPExcel->getActiveSheet()->getCell('M1', true)->setValue('GIA_BAN_LE');
        $objPHPExcel->getActiveSheet()->getCell('N1', true)->setValue('GIA_BAN_SI');
        $objPHPExcel->getActiveSheet()->getCell('O1', true)->setValue('GHI_CHU');
        $objPHPExcel->getActiveSheet()->getCell('P1', true)->setValue('DANH_MUC');
        $objPHPExcel->getActiveSheet()->getCell('Q1', true)->setValue('NHA_SAN_XUAT');
        $objPHPExcel->getActiveSheet()->getCell('R1', true)->setValue('DINH_MUC_TOI_THIEU');
        $objPHPExcel->getActiveSheet()->getCell('S1', true)->setValue('DINH_MUC_TOI_DA');
        $objPHPExcel->getActiveSheet()->getCell('T1', true)->setValue('HINH_ANH');
        $objPHPExcel->getActiveSheet()->getCell('U1', true)->setValue('BAO_HANH');

        $rowCount = 2;
        foreach ((array)$data['data']['_list_product'] as $element) {
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['prd_name']);
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($element['prd_code']);
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['prd_sls']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue(cms_getNameunitbyID($element['prd_unit_id']));
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue(cms_getNameCheckbyID($element['prd_edit_price']));
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue(cms_getNameCheckbyID($element['prd_allownegative']));
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue(cms_getNameCheckbyID($element['prd_gift']));
            $objPHPExcel->getActiveSheet()->getCell('H' . $rowCount, true)->setValue(cms_getNameCheckbyID($element['prd_serial']));
            $objPHPExcel->getActiveSheet()->getCell('I' . $rowCount, true)->setValue((in_array(36, $this->auth['group_permission']) ? $element['prd_origin_price'] : ''));
            $objPHPExcel->getActiveSheet()->getCell('J' . $rowCount, true)->setValue($element['infor']);
            $objPHPExcel->getActiveSheet()->getCell('K' . $rowCount, true)->setValue($element['position']);
            $objPHPExcel->getActiveSheet()->getCell('L' . $rowCount, true)->setValue((in_array(35, $this->auth['group_permission']) ? $element['link'] : ''));
            $objPHPExcel->getActiveSheet()->getCell('M' . $rowCount, true)->setValue($element['prd_sell_price']);
            $objPHPExcel->getActiveSheet()->getCell('N' . $rowCount, true)->setValue($element['prd_sell_price2']);
            $objPHPExcel->getActiveSheet()->getCell('O' . $rowCount, true)->setValue($element['prd_size']);
            $objPHPExcel->getActiveSheet()->getCell('P' . $rowCount, true)->setValue(cms_getNamegroupbyID($element['prd_group_id']));
            $objPHPExcel->getActiveSheet()->getCell('Q' . $rowCount, true)->setValue(cms_getNamemanufacturebyID($element['prd_manufacture_id']));
            $objPHPExcel->getActiveSheet()->getCell('R' . $rowCount, true)->setValue($element['prd_min']);
            $objPHPExcel->getActiveSheet()->getCell('S' . $rowCount, true)->setValue($element['prd_max']);

            if ($element['prd_image_url'] != '') {
                $objPHPExcel->getActiveSheet()->getCell('T' . $rowCount, true)->setValue(CMS_BASE_URL . 'public/templates/uploads/' . cms_show_image($element['prd_image_url']));
            } else {
                $objPHPExcel->getActiveSheet()->getCell('T' . $rowCount, true)->setValue('');
            }

            $objPHPExcel->getActiveSheet()->getCell('U' . $rowCount, true)->setValue($element['prd_warranty']);

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

    function getCategoriesByParentId($category_id)
    {
        $category_data = array();

        $category_query = $this->db
            ->from('products_group')
            ->where('parentid', $category_id)
            ->get();

        foreach ((array)$category_query->result() as $category) {
            $category_data[] = $category->ID;
            $children = $this->getCategoriesByParentId($category->ID);

            if ($children) {
                $category_data = array_merge($children, $category_data);
            }
        }

        return $category_data;
    }

    public function cms_paging_product_history($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $option['date_to'] = date('Y-m-d', strtotime($option['date_to'] . ' +1 day'));

        if ($option['option1'] > -1)
            $this->db->where('cms_report.user_init', $option['option1']);

        if ($option['option2'] > -1)
            $this->db->where('cms_report.store_id', $option['option2']);

        if ($option['option3'] > -1)
            $this->db->where('cms_report.type', $option['option3']);

        if ($option['date_from'] != '' && $option['date_to'] != '')
            $this->db->where('date >=', $option['date_from'])->where('date <=', $option['date_to']);

        $total_history = $this->db
            ->from('report')
            ->where(['product_id' => $option['product_id'], 'deleted' => 0])
            ->count_all_results();

        if ($option['option1'] > -1)
            $this->db->where('cms_report.user_init', $option['option1']);

        if ($option['option2'] > -1)
            $this->db->where('cms_report.store_id', $option['option2']);

        if ($option['option3'] > -1)
            $this->db->where('cms_report.type', $option['option3']);

        if ($option['date_from'] != '' && $option['date_to'] != '')
            $this->db->where('date >=', $option['date_from'])->where('date <=', $option['date_to']);

        $data['data']['_list_history'] = $this->db
            ->select('transaction_id,type,(input+output) as quantity,input,output,cms_report.created,report_serial,display_name,notes,store_name,transaction_code')
            ->from('report')
            ->join('users', 'users.ID=report.user_init', 'LEFT')
            ->join('stores', 'stores.ID=report.store_id', 'LEFT')
            ->where(['product_id' => $option['product_id'], 'cms_report.deleted' => 0])
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('cms_report.created', 'desc')
            ->get()
            ->result_array();

        $data['data']['prd_serial'] = cms_finding_productbyID($option['product_id'])['prd_serial'];

        $number_product = count($data['data']['_list_history']);
        $config['base_url'] = 'cms_paging_product_history';
        $config['total_rows'] = $total_history;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['data']['_sl_product'] = $total_history;
        $data['_pagination_link'] = $_pagination_link;

        if ($number_product == 0)
            $data['display'] = '';
        else
            $data['display'] = 'Kết quả từ ' . (($page - 1) * 10 + 1) . '-' . ((($page - 1) * 10 + 1) + $number_product - 1) . ' trên tổng ' . $total_history;

        if ($page > 1 && ($total_history - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['data']['page'] = $page;
        $this->load->view('ajax/product/list_product_history', isset($data) ? $data : null);
    }

    public function cms_vcrproduct()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['data']['_prd_group'] = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        $data['data']['_prd_manufacture'] = $this->db->from('products_manufacture')->get()->result_array();
        $data['data']['_prd_unit'] = $this->db->from('products_unit')->get()->result_array();

        $this->load->view('products/add_prd', isset($data) ? $data : null);
    }

    public function cms_clone_product($id)
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $id = (int)$id;
        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['data']['_detail_product'] = $product;
            $data['data']['_prd_group'] = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
            $data['data']['_prd_manufacture'] = $this->db->from('products_manufacture')->get()->result_array();
            $data['data']['_prd_unit'] = $this->db->from('products_unit')->get()->result_array();
            $data['can_view_link'] = in_array(35, $this->auth['group_permission']);
            $data['can_view_price'] = in_array(36, $this->auth['group_permission']);
            $this->load->view('products/add_prd', isset($data) ? $data : null);
        }
    }

    public function cms_edit_product($id)
    {
        if ($this->auth == null || !in_array(25, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
            if (!empty($product) && count($product)) {
                $data['data']['_detail_product'] = $product;
                $data['store_id'] = $this->auth['store_id'];
                $data['data']['_prd_group'] = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
                $data['data']['_prd_manufacture'] = $this->db->from('products_manufacture')->get()->result_array();
                $data['data']['_prd_unit'] = $this->db->from('products_unit')->get()->result_array();
                $data['can_view_link'] = in_array(35, $this->auth['group_permission']);
                $data['can_view_price'] = in_array(36, $this->auth['group_permission']);
                $this->load->view('products/edit_prd', isset($data) ? $data : null);
            }
        }
    }

    public function cms_create_manufacture()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $prd_manuf = $this->db->from('products_manufacture')->where('prd_manuf_name', $data['prd_manuf_name'])->get()->row_array();
        if (!empty($prd_manuf) && count($prd_manuf)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_manufacture', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_create_unit()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $prd_unit = $this->db->from('products_unit')->where('prd_unit_name', $data['prd_unit_name'])->get()->row_array();
        if (!empty($prd_unit) && count($prd_unit)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_unit', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_paging_manufacture($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdmanuf = $this->db->from('products_manufacture')->count_all_results();
        $config['base_url'] = 'cms_paging_manufacture';
        $config['total_rows'] = $total_prdmanuf;

        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_manuf'] = $this->db->from('products_manufacture')->limit($config['per_page'], ($page - 1) * $config['per_page'])->order_by('created', 'desc')->get()->result_array();
        if ($page > 1 && ($total_prdmanuf - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_manufacture', isset($data) ? $data : null);
    }

    public function cms_paging_unit($page = 1)
    {
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdunit = $this->db->from('products_unit')->count_all_results();
        $config['base_url'] = 'cms_paging_unit';
        $config['total_rows'] = $total_prdunit;

        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_unit'] = $this->db->from('products_unit')->limit($config['per_page'], ($page - 1) * $config['per_page'])->order_by('created', 'desc')->get()->result_array();
        if ($page > 1 && ($total_prdunit - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_unit', isset($data) ? $data : null);
    }

    public function cms_delete_manufacture($id)
    {
        $id = (int)$id;
        $prd_manuf = $this->db->from('products_manufacture')->where('ID', $id)->get()->row_array();
        if (!isset($prd_manuf) || count($prd_manuf) == 0) {
            echo $this->messages;
            return;
        } else {
            $this->db->where('ID', $id)->delete('products_manufacture');
            echo $this->messages = '1';
        }
    }

    public function cms_delete_unit($id)
    {
        $id = (int)$id;
        $prd_manuf = $this->db->from('products_unit')->where('ID', $id)->get()->row_array();
        if (!isset($prd_manuf) || count($prd_manuf) == 0) {
            echo $this->messages;
            return;
        } else {
            $this->db->where('ID', $id)->delete('products_unit');
            echo $this->messages = '1';
        }
    }

    public function cms_update_prdmanufacture($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $check = $this->db->from('products_manufacture')->where('ID', $id)->count_all_results();
        if ($check > 0) {
            $check = $this->db->from('products_manufacture')->where('prd_manuf_name', $data['prd_manuf_name'])->where('ID <>', $id)->count_all_results();
            if ($check == 0) {
                $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $data['user_upd'] = $this->auth['id'];
                $this->db->where('ID', $id)->update('products_manufacture', $data);
                echo $this->messages = '1';
            }
        } else
            echo $this->messages = '0';
    }

    public function cms_update_prdunit($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $check = $this->db->from('products_unit')->where('ID', $id)->count_all_results();
        if ($check > 0) {
            $check = $this->db->from('products_unit')->where('prd_unit_name', $data['prd_unit_name'])->where('ID <>', $id)->count_all_results();
            if ($check == 0) {
                $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
                $data['user_upd'] = $this->auth['id'];
                $this->db->where('ID', $id)->update('products_unit', $data);
                echo $this->messages = '1';
            }
        } else
            echo $this->messages = '0';
    }

    public function cms_create_group()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data = $this->input->post('data');
        $data['level'] = 0;
        if (isset($data['parentid']) && $data['parentid'] > 0) {
            $level = $this->db->select('level')->from('products_group')->where('ID', $data['parentid'])->limit(1)->get()->row_array();
            $data['level'] = $level['level'] + 1;
            $prd_group = $this->db->from('products_group')->where(['parentid' => $data['parentid'], 'prd_group_name' => $data['prd_group_name']])->get()->row_array();
        } else {
            $prd_group = $this->db->from('products_group')->where(['parentid' => -1, 'prd_group_name' => $data['prd_group_name']])->get()->row_array();
        }

        if (!empty($prd_group) && count($prd_group)) {
            echo $this->messages = '0';
            return;
        } else {
            $data['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_group', $data);
            echo $this->messages = '1';
        }
    }

    public function cms_load_listgroup()
    {
        $this->cms_nestedset->set('products_group');
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        ob_start();
        echo '<option value="-1" selected="selected">-Danh mục-</option>';
        echo '<optgroup label="Chọn danh mục">';
        if ($sls_group)
            foreach ((array)$sls_group as $key => $val) :
                ?>
                <option
                        value="<?php echo $val['ID']; ?>"><?php echo $val['prd_group_name']; ?>
                </option>
            <?php
            endforeach;

        echo '</optgroup>';
        echo '<optgroup label="------------------------">
                                                <option value="product_group" data-toggle="modal" data-target="#list-prd-group">Tạo mới danh
                                                    mục
                                                </option>
                                            </optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listgroup_withoutCreate()
    {
        $this->cms_nestedset->set('products_group');
        $sls_group = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        ob_start();
        echo '<option value="-1" selected="selected">-Danh mục-</option>';
        echo '<optgroup label="Chọn danh mục">';
        if ($sls_group)
            foreach ((array)$sls_group as $key => $val) :
                ?>
                <option
                        value="<?php echo $val['ID']; ?>"><?php echo $val['prd_group_name']; ?>
                </option>
            <?php
            endforeach;
        echo '</optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listmanufacture()
    {
        $this->cms_nestedset->set('products_group');
        $data = $this->db->from('products_manufacture')->order_by('created', 'desc')->get()->result_array();
        ob_start();
        echo '<option value="-1" selected="selected">-Nhà sản xuất-</option>';
        echo '<optgroup label="Chọn nhà sản xuất">';
        foreach ((array)$data as $key => $item) :
            ?>
            <option
                    value="<?php echo $item['ID']; ?>"><?php echo $item['prd_manuf_name']; ?>
            </option>
        <?php
        endforeach;
        echo '</optgroup>';
        echo '<optgroup label="------------------------">
        <option value="product_manufacture" data-toggle="modal" data-target="#list-prd-manufacture">Tạo mới nhà sản xuất
        </option></optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_load_listunit()
    {
        $this->cms_nestedset->set('products_group');
        $data = $this->db->from('products_unit')->order_by('created', 'desc')->get()->result_array();
        ob_start();
        echo '<option value="-1" selected="selected">--Đơn vị tính--</option>';
        echo '<optgroup label="Chọn đơn vị tính">';
        foreach ((array)$data as $key => $item) :
            ?>
            <option
                    value="<?php echo $item['ID']; ?>"><?php echo $item['prd_unit_name']; ?>
            </option>
        <?php
        endforeach;
        echo '</optgroup>';
        echo '<optgroup label="------------------------">
        <option value="product_unit" data-toggle="modal" data-target="#list-prd-unit">Tạo mới đơn vị tính
        </option></optgroup>';
        $html = ob_get_contents();
        ob_end_clean();
        echo $this->messages = $html;
    }

    public function cms_paging_group($page = 1)
    {
        $this->cms_nestedset->set('products_group');
        $config = $this->cms_common->cms_pagination_custom();
        $total_prdGroup = $this->db->from('products_group')->count_all_results();
        $config['base_url'] = 'cms_paging_group';
        $config['total_rows'] = $total_prdGroup;

        $this->pagination->initialize($config);
        $data['_pagination_link'] = $this->pagination->create_links();
        $data ['_list_prd_group'] = $this->db->from('products_group')->limit($config['per_page'], ($page - 1) * $config['per_page'])->get()->result_array();
        if ($page > 1 && ($total_prdGroup - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data ['page'] = $page;
        $this->load->view('ajax/product/list_prd_group', isset($data) ? $data : null);
    }

    public function cms_save_item_prdGroup($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $prd_group = $this->db->from('products_group')->where('id', $id)->get()->row_array();
        if (empty($prd_group) && count($prd_group) == 0) {
            echo $this->messages = '0';
            return;
        }
        $check = $this->db->from('products_group')->where(['parentid' => $prd_group['parentid'], 'prd_group_name' => $data['prd_group_name'], 'ID <>' => $id])->count_all_results();
        if ($check == 0) {
            $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
            $data['user_upd'] = $this->auth['id'];
            $this->db->where('ID', $id)->update('products_group', $data);
            echo $this->messages = '1';
        } else
            echo $this->messages = '0';
    }

    public function cms_delete_Group($id)
    {
        $id = (int)$id;
        $prd_group = $this->db->where('id', $id)->from('products_group')->get()->row_array();
        if (isset($prd_group) && count($prd_group)) {
            $countitem = $this->db->where('parentid', $prd_group['ID'])->from('products_group')->count_all_results();
            $countprd = $this->db->where('prd_group_id', $prd_group['ID'])->from('products')->where('prd_serial', 0)->count_all_results();
            if ($countitem > 0) {
                echo $this->messages = 'Không thể xóa danh mục khi có danh mục cấp con.';;
            } elseif ($countprd > 0) {
                echo $this->messages = '2';
            } else {
                $this->db->delete('products_group', ['id' => $id]);
                echo $this->messages = '1';
            }
        }
    }

    public function cms_delete_Group_WithProduct($id)
    {
        $data['prd_group_id'] = 0;
        $this->db->where('prd_group_id', $id)->update('products', $data);
        $this->db->delete('products_group', ['id' => $id]);
        echo $this->messages = '1';
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
            $createArray = array('TEN_SAN_PHAM',
                'MA_SAN_PHAM',
                'SO_LUONG',
                'DON_VI_TINH',
                'CHO_SUA_GIA_KHI_BAN',
                'CHO_PHEP_BAN_AM',
                'KEM_QUA_TANG',
                'CO_SERIAL',
                'GIA_VON',
                'THONG_TIN_THEM',
                'VI_TRI',
                'LINK_NHAP',
                'GIA_BAN_LE',
                'GIA_BAN_SI',
                'GHI_CHU',
                'DANH_MUC',
                'NHA_SAN_XUAT',
                'HINH_ANH',
                'DINH_MUC_TOI_THIEU',
                'DINH_MUC_TOI_DA',
                'BAO_HANH',
            );
            $makeArray = array('TEN_SAN_PHAM' => 'TEN_SAN_PHAM',
                'MA_SAN_PHAM' => 'MA_SAN_PHAM',
                'SO_LUONG' => 'SO_LUONG',
                'DON_VI_TINH' => 'DON_VI_TINH',
                'CHO_SUA_GIA_KHI_BAN' => 'CHO_SUA_GIA_KHI_BAN',
                'CHO_PHEP_BAN_AM' => 'CHO_PHEP_BAN_AM',
                'KEM_QUA_TANG' => 'KEM_QUA_TANG',
                'CO_SERIAL' => 'CO_SERIAL',
                'GIA_VON' => 'GIA_VON',
                'THONG_TIN_THEM' => 'THONG_TIN_THEM',
                'VI_TRI' => 'VI_TRI',
                'LINK_NHAP' => 'LINK_NHAP',
                'GIA_BAN_LE' => 'GIA_BAN_LE',
                'GIA_BAN_SI' => 'GIA_BAN_SI',
                'GHI_CHU' => 'GHI_CHU',
                'DANH_MUC' => 'DANH_MUC',
                'NHA_SAN_XUAT' => 'NHA_SAN_XUAT',
                'HINH_ANH' => 'HINH_ANH',
                'DINH_MUC_TOI_THIEU' => 'DINH_MUC_TOI_THIEU',
                'DINH_MUC_TOI_DA' => 'DINH_MUC_TOI_DA',
                'BAO_HANH' => 'BAO_HANH',
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
                $prd_name = $SheetDataKey['TEN_SAN_PHAM'];
                $prd_code = $SheetDataKey['MA_SAN_PHAM'];
                $prd_sls = $SheetDataKey['SO_LUONG'];
                $prd_unit_id = $SheetDataKey['DON_VI_TINH'];
                $prd_allownegative = $SheetDataKey['CHO_PHEP_BAN_AM'];
                $prd_edit_price = $SheetDataKey['CHO_SUA_GIA_KHI_BAN'];
                $prd_origin_price = $SheetDataKey['GIA_VON'];
                $prd_sell_price = $SheetDataKey['GIA_BAN_LE'];
                $infor = $SheetDataKey['THONG_TIN_THEM'];
                $position = $SheetDataKey['VI_TRI'];
                $link = $SheetDataKey['LINK_NHAP'];
                $prd_sell_price2 = $SheetDataKey['GIA_BAN_SI'];
                $prd_size = $SheetDataKey['GHI_CHU'];
                $prd_gift = $SheetDataKey['KEM_QUA_TANG'];
                $prd_serial = $SheetDataKey['CO_SERIAL'];
                $prd_min = $SheetDataKey['DINH_MUC_TOI_THIEU'];
                $prd_max = $SheetDataKey['DINH_MUC_TOI_DA'];
                $prd_group_id = $SheetDataKey['DANH_MUC'];
                $prd_manufacture_id = $SheetDataKey['NHA_SAN_XUAT'];
                $prd_image_url = $SheetDataKey['HINH_ANH'];
                $prd_warranty = $SheetDataKey['BAO_HANH'];

                for ($i = 2; $i <= $arrayCount; $i++) {
                    $data = array();
                    $data['prd_name'] = filter_var(trim($allDataInSheet[$i][$prd_name]), FILTER_SANITIZE_STRING);
                    if ($data['prd_name'] != '') {
                        $data['prd_code'] = filter_var(trim($allDataInSheet[$i][$prd_code]), FILTER_SANITIZE_STRING);
                        $data['prd_sls'] = filter_var(trim($allDataInSheet[$i][$prd_sls]), FILTER_SANITIZE_STRING);
                        $data['prd_unit_id'] = $this->cms_check_and_save_unit(filter_var(trim($allDataInSheet[$i][$prd_unit_id]), FILTER_SANITIZE_STRING));
                        $data['infor'] = filter_var(trim($allDataInSheet[$i][$infor]), FILTER_SANITIZE_STRING);
                        $data['position'] = filter_var(trim($allDataInSheet[$i][$position]), FILTER_SANITIZE_STRING);
                        $data['link'] = filter_var(trim($allDataInSheet[$i][$link]), FILTER_SANITIZE_STRING);
                        $data['prd_allownegative'] = $this->cms_check_yes_no(filter_var(trim($allDataInSheet[$i][$prd_allownegative]), FILTER_SANITIZE_STRING));
                        $data['prd_size'] = filter_var(trim($allDataInSheet[$i][$prd_size]), FILTER_SANITIZE_STRING);
                        $data['prd_gift'] = $this->cms_check_yes_no(filter_var(trim($allDataInSheet[$i][$prd_gift]), FILTER_SANITIZE_STRING));
                        $data['prd_serial'] = $this->cms_check_yes_no(filter_var(trim($allDataInSheet[$i][$prd_serial]), FILTER_SANITIZE_STRING));
                        $data['prd_min'] = filter_var(trim($allDataInSheet[$i][$prd_min]), FILTER_SANITIZE_STRING);
                        $data['prd_max'] = filter_var(trim($allDataInSheet[$i][$prd_max]), FILTER_SANITIZE_STRING);
                        $data['prd_edit_price'] = $this->cms_check_yes_no(filter_var(trim($allDataInSheet[$i][$prd_edit_price]), FILTER_SANITIZE_STRING));
                        $data['prd_origin_price'] = filter_var(trim($allDataInSheet[$i][$prd_origin_price]), FILTER_SANITIZE_STRING);
                        $data['prd_sell_price'] = filter_var(trim($allDataInSheet[$i][$prd_sell_price]), FILTER_SANITIZE_STRING);
                        $data['prd_sell_price2'] = filter_var(trim($allDataInSheet[$i][$prd_sell_price2]), FILTER_SANITIZE_STRING);
                        $data['prd_group_id'] = $this->cms_check_and_save_group(filter_var(trim($allDataInSheet[$i][$prd_group_id]), FILTER_SANITIZE_STRING));
                        $data['prd_manufacture_id'] = $this->cms_check_and_save_manufacture(filter_var(trim($allDataInSheet[$i][$prd_manufacture_id]), FILTER_SANITIZE_STRING));
                        $data['prd_image_url'] = str_replace(CMS_BASE_URL . 'public/templates/uploads/', '', filter_var(trim($allDataInSheet[$i][$prd_image_url]), FILTER_SANITIZE_STRING));
                        $data['prd_warranty'] = filter_var(trim($allDataInSheet[$i][$prd_warranty]), FILTER_SANITIZE_STRING);

                        if ($data['prd_code'] != '') {
                            $check_code = $this->db->from('products')->where('prd_serial', 0)->where(['prd_code' => $data['prd_code']])->where('deleted <>', 2)->count_all_results();
                            if ($check_code > 0) {
                                unset($data['prd_sls']);
                                unset($data['prd_serial']);

                                if (!in_array(35, $this->auth['group_permission']))
                                    unset($data['link']);

                                if (!in_array(36, $this->auth['group_permission']))
                                    unset($data['prd_origin_price']);

                                $this->db->where('prd_code', $data['prd_code'])->update('products', $data);
                            } else
                                $this->cms_save_product($data);
                        } else
                            $this->cms_save_product($data);
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

    public function cms_check_and_save_unit($unit_name)
    {
        $unit_id = $this->db->select('ID')->from('products_unit')->where('prd_unit_name', $unit_name)->get()->row_array();
        if (!empty($unit_id) && count($unit_id) > 0) {
            return $unit_id['ID'];
        } else {
            $data = array();
            $data['prd_unit_name'] = $unit_name;
            $data['user_init'] = $this->auth['id'];
            $this->db->insert('products_unit', $data);
            return $this->db->insert_id();
        }
    }

    public function cms_check_yes_no($text)
    {
        $temp = array('1', 'Co', 'co', 'Có', 'có', 'yes', 'Yes', 'CÓ', 'CO', 'cÓ', 'cO', 'đúng', 'Đúng', 'ĐÚNG', 'DUNG', 'dung', 'Dúng', 'dúng', 'DÚNG');
        if (in_array($text, $temp))
            return 1;
        else
            return 0;
    }

    public function cms_check_and_save_group($group_name)

    {

        $group = $this->db->select('ID')->from('products_group')->where('prd_group_name', $group_name)->get()->row_array();

        if (!empty($group) && count($group) > 0) {
            return $group['ID'];
        } else {

            $data = array();

            $data['parentid'] = -1;

            $data['prd_group_name'] = $group_name;

            $data['user_init'] = $this->auth['id'];

            $this->db->insert('products_group', $data);

            return $this->db->insert_id();
        }

    }

    public function cms_check_and_save_manufacture($group_name)

    {

        $group = $this->db->select('ID')->from('products_manufacture')->where('prd_manuf_name', $group_name)->get()->row_array();

        if (!empty($group) && count($group) > 0) {
            return $group['ID'];
        } else {

            $data = array();

            $data['prd_manuf_name'] = $group_name;

            $data['user_init'] = $this->auth['id'];

            $this->db->insert('products_manufacture', $data);

            return $this->db->insert_id();
        }

    }

    public function cms_save_product($data)
    {
        $store_id = $this->auth['store_id'];
        $data['user_init'] = $this->auth['id'];
        if ($data['prd_code'] == '') {
            $max_product_code = $this->db->select_max('prd_code')->from('products')->where('prd_serial', 0)->where('LENGTH(prd_code) = 7')->where("(prd_code LIKE 'SP" . "%')", NULL, FALSE)->where('deleted <>', 2)->get()->row_array();

            if (isset($max_product_code) && count($max_product_code) > 0) {
                $max_code = (int)(str_replace('SP', '', $max_product_code['prd_code'])) + 1;

                if ($max_code < 10)
                    $data['prd_code'] = 'SP0000' . ($max_code);
                else if ($max_code < 100)
                    $data['prd_code'] = 'SP000' . ($max_code);
                else if ($max_code < 1000)
                    $data['prd_code'] = 'SP00' . ($max_code);
                else if ($max_code < 10000)
                    $data['prd_code'] = 'SP0' . ($max_code);
                else if ($max_code < 100000)
                    $data['prd_code'] = 'SP' . ($max_code);
            } else {
                $data['prd_code'] = 'SP00001';
            }
        }

        if ($data['prd_sls'] == '')
            $data['prd_sls'] = 0;

        $quantity = $data['prd_sls'];

        $this->db->insert('products', $data);
        $product_id = $this->db->insert_id();
        $user_init = $data['user_init'];
        $inventory = ['store_id' => $store_id, 'product_id' => $product_id, 'quantity' => $quantity, 'user_init' => $user_init];
        $this->db->insert('inventory', $inventory);

        $report = array();
        $report['transaction_code'] = $data['prd_code'];
        $report['notes'] = 'Khai báo sản phẩm';
        $report['price'] = $data['prd_origin_price'];
        $report['total_money'] = $report['price'] * $quantity;
        $report['user_init'] = $user_init;
        $report['type'] = 1;
        $report['store_id'] = $store_id;
        $report['product_id'] = $product_id;
        $report['input'] = $quantity;
        $report['stock'] = $quantity;

        $this->db->insert('report', $report);
    }

    public function upload_img($product_id = 0)

    {

        $path = "public/templates/uploads/";

        $valid_formats = array("jpg", "png", "gif", "bmp", "jpeg", "JPG", "PNG", "GIF", "BMP", "JPEG");

        if (isset($_POST) and $_SERVER['REQUEST_METHOD'] == "POST") {

            $name = $_FILES['photo']['name'];

            $size = $_FILES['photo']['size'];

            if (strlen($name)) {

                list($txt, $ext) = explode(".", $name);

                if (in_array($ext, $valid_formats)) {

                    if ($size < (10024 * 10024)) {
                        $image_name = $txt . '_' . gmdate("d_m_Y", time() + 7 * 3600) . "." . $ext;
                        $tmp = $_FILES['photo']['tmp_name'];

                        echo $image_name;
                        if (!move_uploaded_file($tmp, $path . $image_name)) {
                            echo '<script>alert("Upload hình không thành công do kích thướt quá lớn. Vui lòng liên hệ quản trị hosting")</script>';
                        } else {
                            if ($product_id > 0 && $image_name != '') {
                                $this->db->where('ID', $product_id)->update('products', ['prd_image_url' => $image_name]);
                            }
                        }
                    } else
                        echo '<script>alert("Kích thướt ảnh quá lớn. Vui lòng chọn lại")</script>';
                } else

                    echo '<script>alert("File không đúng định dạng. Vui lòng chọn file khác")</script>';

            } else

                echo "Please select image..!";

            exit;

        }
    }

    public function cms_add_product()
    {
        $data = $this->input->post('data');
        $check_code = $this->db->select('ID')->from('products')->where('prd_serial', 0)->where('deleted <>', 2)->where('prd_code', $data['prd_code'])->get()->row_array();
        if (!empty($check_code) && count($check_code)) {
            echo $this->messages = 'Mã SP ' . $data['prd_code'] . ' đã tồn tại trong hệ thống. Vui lòng chọn mã khác.';
        } else {
            $this->db->trans_begin();

            $this->cms_save_product($data);

            if ($this->db->trans_status() === FALSE) {
                $this->db->trans_rollback();
                echo $this->messages = "0";
            } else {
                $this->db->trans_commit();
                echo $this->messages = "1";
            }
        }
    }

    public function cms_update_product($id)
    {
        if ($this->auth == null || !in_array(25, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $data = $this->input->post('data');

            $check_product = cms_finding_productbyID($id);

            if ($check_product['prd_allownegative'] == 1 && $data['prd_allownegative'] == 0) {
                $check_inventory = $this->db->from('inventory')->where('product_id', $id)->where('quantity <', 0)->count_all_results();
                if ($check_inventory > 0) {
                    echo $this->messages = "Sản phẩm có tồn kho bị âm. Vui lòng nhập thêm hàng vào để có thể thiết lập không cho phép bán âm";
                    return;
                }
            }

            if ($data['prd_image_url'] == '')
                unset($data['prd_image_url']);

            if (!in_array(35, $this->auth['group_permission']))
                unset($data['link']);

            if (!in_array(36, $this->auth['group_permission']))
                unset($data['prd_origin_price']);

            $data['user_upd'] = $this->auth['id'];

            $user_init = $this->auth['id'];

            $this->db->trans_begin();

            $this->db->where('ID', $id)->update('products', $data);

            $adjust_temp = $this->input->post('adjust');

            if ($adjust_temp['inventory'] != $adjust_temp['inventory_edit'] && $check_product['prd_serial'] == 0) {
                $total_quantity = 0;

                $total_different = 0;

                $detail_adjust = array();

                $adjust['created'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);

                $adjust['adjust_date'] = $adjust['created'];

                $inventory_quantity = $this->db->select('quantity')->from('inventory')->where(['store_id' => $adjust_temp['store_id'], 'product_id' => $id])->get()->row_array();
                $item = array();
                $item['id'] = $id;
                $item['quantity'] = $adjust_temp['inventory'];
                if (!empty($inventory_quantity)) {

                    $different = $adjust_temp['inventory'] - $inventory_quantity['quantity'];

                    $item['inventory'] = $inventory_quantity['quantity'];

                    $this->db->where(['store_id' => $adjust_temp['store_id'], 'product_id' => $id])->update('inventory', ['quantity' => $adjust_temp['inventory'], 'user_upd' => $this->auth['id']]);

                } else {

                    $different = $adjust_temp['inventory'];

                    $item['inventory'] = 0;

                    $inventory = ['store_id' => $adjust_temp['store_id'], 'product_id' => $id, 'quantity' => $adjust_temp['inventory'], 'user_init' => $user_init];

                    $this->db->insert('inventory', $inventory);

                }


                $product = $this->db->select('prd_sls')->from('products')->where('prd_serial', 0)->where(['ID' => $id])->get()->row_array();

                if (empty($product)) {

                    $this->db->trans_rollback();

                    echo $this->messages = "0";

                    return;

                } else {

                    $product['prd_sls'] += $different;

                    $this->db->where('ID', $id)->update('products', $product);

                }


                $total_quantity += $adjust_temp['inventory'];

                $total_different += $different;

                $detail_adjust[] = $item;

                $adjust['total_quantity'] = $total_quantity;

                $adjust['total_different'] = $total_different;

                $adjust['user_init'] = $user_init;

                $adjust['detail_adjust'] = json_encode($detail_adjust);

                $this->db->select_max('adjust_code')->like('adjust_code', 'KK');
                $max_adjust_code = $this->db->get('adjust')->row();
                $max_code = (int)(str_replace('KK', '', $max_adjust_code->adjust_code)) + 1;
                if ($max_code < 10)
                    $adjust['adjust_code'] = 'KK000000' . ($max_code);
                else if ($max_code < 100)
                    $adjust['adjust_code'] = 'KK00000' . ($max_code);
                else if ($max_code < 1000)
                    $adjust['adjust_code'] = 'KK0000' . ($max_code);
                else if ($max_code < 10000)
                    $adjust['adjust_code'] = 'KK000' . ($max_code);
                else if ($max_code < 100000)
                    $adjust['adjust_code'] = 'KK00' . ($max_code);
                else if ($max_code < 1000000)
                    $adjust['adjust_code'] = 'KK0' . ($max_code);
                else if ($max_code < 10000000)
                    $adjust['adjust_code'] = 'KK' . ($max_code);

                $this->db->insert('adjust', $adjust);

                $adjust_id = $this->db->insert_id();

                $temp = array();

                $temp['transaction_code'] = $adjust['adjust_code'];

                $temp['transaction_id'] = $adjust_id;

                $temp['notes'] = 'Tạo phiếu kiểm kho ';

                $temp['user_init'] = $user_init;

                $temp['store_id'] = $adjust_temp['store_id'];

                if ($adjust_temp['inventory'] != $item['inventory']) {

                    if ($adjust_temp['inventory'] > $item['inventory']) {

                        $report = $temp;

                        $report['type'] = 8;

                        $report['product_id'] = $id;

                        $report['input'] = $adjust_temp['inventory'] - $item['inventory'];

                        $report['stock'] = $item['inventory'];

                        $this->db->insert('report', $report);

                    } elseif ($adjust_temp['inventory'] < $item['inventory']) {

                        $report = $temp;

                        $report['type'] = 8;

                        $report['product_id'] = $id;

                        $report['output'] = $item['inventory'] - $adjust_temp['inventory'];

                        $report['stock'] = $item['inventory'];

                        $this->db->insert('report', $report);

                    }
                }
            }

            if ($this->db->trans_status() === FALSE) {

                $this->db->trans_rollback();

                echo $this->messages = "0";

            } else {

                $this->db->trans_commit();

                echo $this->messages = $id;

            }

            echo $this->messages = "1";
        }
    }

    public function cms_paging_product($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $temp = array();
        if ($option['option2'] > '-1') {
            $temp = $this->getCategoriesByParentId($option['option2']);
            $temp[] = $option['option2'];
            $this->db->where_in('prd_group_id', $temp);
        }

        if ($option['option1'] == '0') {
            $this->db->where(['prd_status' => 1, 'deleted' => 0]);
        } else if ($option['option1'] == '1') {
            $this->db->where(['prd_status' => 0, 'deleted' => 0]);
        } else if ($option['option1'] == '2') {
            $this->db->where(['deleted' => 1]);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('prd_manufacture_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(prd_descriptions LIKE '%" . $option['keyword'] . "%' OR prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $total_prd = $this->db
            ->from('products')->where('prd_serial', 0)
            ->count_all_results();

        if ($option['option2'] > '-1') {
            $this->db->where_in('prd_group_id', $temp);
        }

        if ($option['option1'] == '0') {
            $this->db->where(['prd_status' => 1, 'deleted' => 0]);
        } else if ($option['option1'] == '1') {
            $this->db->where(['prd_status' => 0, 'deleted' => 0]);
        } else if ($option['option1'] == '2') {
            $this->db->where(['deleted' => 1]);
        }

        if ($option['option3'] > '-1') {
            $this->db->where('prd_manufacture_id', $option['option3']);
        }

        if ($option['keyword'] != '') {
            $this->db->where("(prd_descriptions LIKE '%" . $option['keyword'] . "%' OR prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE);
        }

        $data['data']['_list_product'] = $this->db
            ->select('ID,prd_code,prd_name,prd_sls,prd_sell_price,prd_sell_price2,prd_group_id,prd_manufacture_id,prd_image_url,prd_status')
            ->from('products')->where('prd_serial', 0)
            ->limit($config['per_page'], ($page - 1) * $config['per_page'])
            ->order_by('prd_code', 'DESC')
            ->get()
            ->result_array();

        $config['base_url'] = 'cms_paging_product';
        $config['total_rows'] = $total_prd;
        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['data']['_sl_product'] = $total_prd;
        $data['data']['_sl_manufacture'] = $this->db->from('products_manufacture')->count_all_results();
        $data['_pagination_link'] = $_pagination_link;
        if ($page > 1 && ($total_prd - 1) / ($page - 1) == 10)
            $page = $page - 1;

        $data['data']['option'] = $option['option1'];
        $data['data']['page'] = $page;
        $data['data']['store_id'] = $this->auth['store_id'];
        $this->load->view('ajax/product/list_products', isset($data) ? $data : null);
    }

    public function cms_delete_product($id)
    {
        if ($this->auth == null || !in_array(26, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $id)->count_all_results();
            if ($product == 1) {
                $this->db->where('ID', $id)->update('products', ['deleted' => 1]);
                echo $this->messages = '1';
            } else {
                echo $this->messages = '0';
            }
        }

    }

    public function cms_delete_forever_product($id)
    {
        if ($this->auth == null || !in_array(26, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->select('prd_name')->from('products')->where('prd_serial', 0)->where('ID', $id)->where('deleted', 1)->get()->row_array();
            if (!empty($product) && count($product)) {
                $this->db->where('ID', $id)->update('products', ['deleted' => 2]);
                echo $this->messages = '1';
            } else {
                echo $this->messages;
            }
        }

    }

    public function cms_restore_product_deleted($id)
    {
        if ($this->auth == null || !in_array(25, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->select('prd_code')->from('products')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
            if (!empty($product) && count($product)) {
                $check_code = $this->db->from('products')->where('prd_serial', 0)->where('deleted', 0)->where('ID <>', $id)->where('prd_code', $product['prd_code'])->count_all_results();
                if ($check_code > 0) {
                    echo $this->messages = '0';
                } else {
                    $this->db->where('ID', $id)->update('products', ['deleted' => 0]);
                    echo $this->messages = '1';
                }

            } else {
                echo $this->messages;
            }
        }
    }

    public function cms_restore_product_deactivated($id)
    {
        if ($this->auth == null || !in_array(25, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->select('prd_name')->from('products')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
            if (!empty($product) && count($product)) {
                $this->db->where('ID', $id)->update('products', ['prd_status' => 1]);
                echo $this->messages = '1';
            } else {
                echo $this->messages;
            }
        }

    }

    public function cms_deactivate_product($id)
    {
        if ($this->auth == null || !in_array(25, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');
        else {
            $id = (int)$id;
            $product = $this->db->select('prd_name')->from('products')->where('prd_serial', 0)->get()->row_array();
            if (!empty($product) && count($product)) {
                $this->db->where('ID', $id)->update('products', ['prd_status' => 0]);
                echo $this->messages = '1';
            } else {
                echo $this->messages;
            }
        }
    }

    public function cms_detail_product($id)
    {
        $id = (int)$id;
        $option = $this->input->post('data');
        $product = $this->db->from('products')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $data['option'] = $option['option1'];
            $data['store_id'] = $this->auth['store_id'];
            $data['can_view_link'] = in_array(35, $this->auth['group_permission']);
            $data['can_view_price'] = in_array(36, $this->auth['group_permission']);
            $this->load->view('ajax/product/detail_product', isset($data) ? $data : null);
        }
    }
    
    public function cms_detail_product_buyer($id)
    {
        $id = (int)$id;
        $product = $this->db->from('orders')->where('prd_serial', 0)->where('ID', $id)->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $this->load->view('ajax/product/detail_product_buyer', isset($data) ? $data : null);
        }
    }

    public function cms_detail_product_deleted($id)
    {
        $id = (int)$id;
        $product = $this->db->from('products')->where('prd_serial', 0)->where(['ID' => $id, 'deleted' => 1])->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $data['can_view_link'] = in_array(35, $this->auth['group_permission']);
            $data['can_view_price'] = in_array(36, $this->auth['group_permission']);
            $this->load->view('ajax/product/detail_product_deleted', isset($data) ? $data : null);
        }
    }

    public function cms_detail_product_deactivated($id)
    {
        $id = (int)$id;
        $product = $this->db->from('products')->where('prd_serial', 0)->where(['ID' => $id, 'prd_status' => 0])->get()->row_array();
        if (!empty($product) && count($product)) {
            $data['_detail_product'] = $product;
            $data['can_view_link'] = in_array(35, $this->auth['group_permission']);
            $data['can_view_price'] = in_array(36, $this->auth['group_permission']);
            $this->load->view('ajax/product/detail_product_deactivated', isset($data) ? $data : null);
        }
    }
}
