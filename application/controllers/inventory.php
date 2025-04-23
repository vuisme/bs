<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Inventory extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(7, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $data['data']['_prd_group'] = $this->cms_nestedset->dropdown('products_group', NULL, 'manufacture');
        $data['data']['_prd_manufacture'] = $this->db->from('products_manufacture')->get()->result_array();
        $data['data']['users'] = $this->db->from('users')->where('user_status', 1)->get()->result_array();
        $data['data']['user'] = $this->auth;
        $data['template'] = 'inventory/index';
        $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();

        $data['data']['store_id'] = $this->auth['store_id'];
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_export_inventory()
    {
        $option = $this->input->post('data');

        if ($option['store_id'] == '-1') {
            if ($option['option1'] == '-1') {
                if ($option['option2'] == '-1') {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where('deleted', 0)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where('deleted', 0)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->having('sum(quantity)>0')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    }
                } else {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    }
                }
            } else {
                $temp = $this->getCategoriesByParentId($option['option1']);
                $temp[] = $option['option1'];
                if ($option['option2'] == '-1') {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where('deleted', 0)
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0])
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0])
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    }
                } else {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->group_by('product_id,inventory_expire')
                            ->get()
                            ->result_array();
                    }
                }
            }
        } else {
            if ($option['option1'] == '-1') {
                if ($option['option2'] == '-1') {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where('deleted', 0)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    }
                } else {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    }
                }
            } else {
                $temp = $this->getCategoriesByParentId($option['option1']);
                $temp[] = $option['option1'];
                if ($option['option2'] == '-1') {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where('deleted', 0)
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0])
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0])
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    }
                } else {
                    if ($option['option3'] == '0') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '1') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity >' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    } else if ($option['option3'] == '2') {
                        $data['data']['_list_product'] = $this->db
                            ->select('products.ID,prd_code,prd_name,quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_gift')
                            ->from('inventory')
                            ->join('products', 'products.ID=inventory.product_id', 'INNER')
                            ->where(['deleted' => 0, 'quantity ' => 0, 'prd_manufacture_id' => $option['option2']])
                            ->where_in('prd_group_id', $temp)
                            ->where('store_id', $option['store_id'])
                            ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                            ->order_by('inventory.quantity', 'desc')
                            ->get()->result_array();
                    }
                }
            }
        }

        cms_delete_public_file_by_extend('xlsx');


        $fileName = 'TonKho-' . gmdate("d_m_Y", time() + 7 * 3600) . '.xlsx';


        $objPHPExcel = new Spreadsheet();
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->getCell('A1', true)->setValue('Mã SP');
        $objPHPExcel->getActiveSheet()->getCell('B1', true)->setValue('Tên SP');
        $objPHPExcel->getActiveSheet()->getCell('C1', true)->setValue('Ghi chú SP');
        $objPHPExcel->getActiveSheet()->getCell('D1', true)->setValue('Quà');
        $objPHPExcel->getActiveSheet()->getCell('E1', true)->setValue('Số lượng');
        $objPHPExcel->getActiveSheet()->getCell('F1', true)->setValue('Vốn tồn kho');
        $objPHPExcel->getActiveSheet()->getCell('G1', true)->setValue('Giá trị tồn');
        $rowCount = 2;
        foreach ((array)$data['data']['_list_product'] as $element) {
            $objPHPExcel->getActiveSheet()->getCell('A' . $rowCount, true)->setValue($element['prd_code']);
            $objPHPExcel->getActiveSheet()->getCell('B' . $rowCount, true)->setValue($element['prd_name']);
            $objPHPExcel->getActiveSheet()->getCell('C' . $rowCount, true)->setValue($element['prd_size']);
            $objPHPExcel->getActiveSheet()->getCell('D' . $rowCount, true)->setValue($element['prd_gift'] == 1 ? 'Có' : 'Không');
            $objPHPExcel->getActiveSheet()->getCell('E' . $rowCount, true)->setValue($element['quantity']);
            $objPHPExcel->getActiveSheet()->getCell('F' . $rowCount, true)->setValue($element['prd_origin_price'] * $element['quantity']);
            $objPHPExcel->getActiveSheet()->getCell('G' . $rowCount, true)->setValue($element['prd_sell_price'] * $element['quantity']);

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

    public function cms_paging_inventory($page = 1)
    {
        $option = $this->input->post('data');
        $config = $this->cms_common->cms_pagination_custom();
        $today = date('Y-m-d');

        $temp = array();
        if ($option['option1'] > '-1') {
            $temp = $this->getCategoriesByParentId($option['option1']);
            $temp[] = $option['option1'];
            $this->db->where_in('prd_group_id', $temp);
        }

        if ($option['store_id'] > '-1') {
            $this->db->where('store_id', $option['store_id']);
        }

        if ($option['option2'] > '-1') {
            $this->db->where('prd_manufacture_id', $option['option2']);
        }

        if ($option['option3'] == '0') {
            $total_prd = $this->db
                ->select('count(distinct(product_id)) as total_prd, sum(quantity) as total_quantity, sum(prd_origin_price*quantity) as total_origin_price, sum(prd_sell_price*quantity) as total_sell_price')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('deleted', 0)
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();

            if ($option['option1'] > '-1') {
                $this->db->where_in('prd_group_id', $temp);
            }

            if ($option['store_id'] > '-1') {
                $this->db->where('store_id', $option['store_id']);
            }

            if ($option['option2'] > '-1') {
                $this->db->where('prd_manufacture_id', $option['option2']);
            }

            $data['data']['_list_product'] = $this->db
                ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_image_url')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('deleted', 0)
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('inventory.quantity', 'desc')
                ->group_by('product_id,inventory_expire')
                ->get()
                ->result_array();
        } else if ($option['option3'] == '1') {
            $total_prd = $this->db
                ->select('count(distinct(product_id)) as total_prd, sum(quantity) as total_quantity, sum(prd_origin_price*quantity) as total_origin_price, sum(prd_sell_price*quantity) as total_sell_price')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where(['deleted' => 0, 'prd_sls >' => 0])
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();

            if ($option['option1'] > '-1') {
                $this->db->where_in('prd_group_id', $temp);
            }

            if ($option['store_id'] > '-1') {
                $this->db->where('store_id', $option['store_id']);
            }

            if ($option['option2'] > '-1') {
                $this->db->where('prd_manufacture_id', $option['option2']);
            }

            $data['data']['_list_product'] = $this->db
                ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size,prd_image_url')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('deleted', 0)
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('inventory.quantity', 'desc')
                ->group_by('product_id,inventory_expire')
                ->having('sum(quantity)>0')
                ->get()
                ->result_array();
        } else if ($option['option3'] == '2') {
            $total_prd = $this->db
                ->select('count(distinct(product_id)) as total_prd, sum(quantity) as total_quantity, sum(prd_origin_price*quantity) as total_origin_price, sum(prd_sell_price*quantity) as total_sell_price')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where(['deleted' => 0, 'quantity ' => 0])
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();

            if ($option['option1'] > '-1') {
                $this->db->where_in('prd_group_id', $temp);
            }

            if ($option['store_id'] > '-1') {
                $this->db->where('store_id', $option['store_id']);
            }

            if ($option['option2'] > '-1') {
                $this->db->where('prd_manufacture_id', $option['option2']);
            }

            $data['data']['_list_product'] = $this->db
                ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where(['deleted' => 0, 'quantity ' => 0])
                ->where('store_id', $option['store_id'])
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('inventory.quantity', 'desc')
                ->group_by('product_id,inventory_expire')
                ->get()
                ->result_array();
        } else if ($option['option3'] == '3') {
            $total_prd = $this->db
                ->select('count(distinct(product_id)) as total_prd, sum(quantity) as total_quantity, sum(prd_origin_price*quantity) as total_origin_price, sum(prd_sell_price*quantity) as total_sell_price')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('DATE(expire_date) >=', $today)
                ->where(['deleted' => 0, 'prd_sls >' => 0])
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();

            if ($option['option1'] > '-1') {
                $this->db->where_in('prd_group_id', $temp);
            }

            if ($option['store_id'] > '-1') {
                $this->db->where('store_id', $option['store_id']);
            }

            if ($option['option2'] > '-1') {
                $this->db->where('prd_manufacture_id', $option['option2']);
            }

            $data['data']['_list_product'] = $this->db
                ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('DATE(expire_date) >=', $today)
                ->where('deleted', 0)
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('inventory.expire_date', 'desc')
                ->group_by('product_id,inventory_expire')
                ->having('sum(quantity)>0')
                ->get()
                ->result_array();
        } else if ($option['option3'] == '4') {
            $total_prd = $this->db
                ->select('count(distinct(product_id)) as total_prd, sum(quantity) as total_quantity, sum(prd_origin_price*quantity) as total_origin_price, sum(prd_sell_price*quantity) as total_sell_price')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('DATE(expire_date) <', $today)
                ->where(['deleted' => 0, 'prd_sls >' => 0])
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->get()
                ->row_array();

            if ($option['option1'] > '-1') {
                $this->db->where_in('prd_group_id', $temp);
            }

            if ($option['store_id'] > '-1') {
                $this->db->where('store_id', $option['store_id']);
            }

            if ($option['option2'] > '-1') {
                $this->db->where('prd_manufacture_id', $option['option2']);
            }

            $data['data']['_list_product'] = $this->db
                ->select('prd_gift,products.ID,prd_code,prd_name,sum(quantity) as quantity,prd_sell_price,prd_origin_price,expire_date,prd_size')
                ->from('inventory')
                ->join('products', 'products.ID=inventory.product_id', 'INNER')
                ->where('DATE(expire_date) <', $today)
                ->where('deleted', 0)
                ->where("(prd_code LIKE '%" . $option['keyword'] . "%' OR prd_name LIKE '%" . $option['keyword'] . "%')", NULL, FALSE)
                ->limit($config['per_page'], ($page - 1) * $config['per_page'])
                ->order_by('inventory.quantity', 'desc')
                ->group_by('product_id,inventory_expire')
                ->having('sum(quantity)>0')
                ->get()
                ->result_array();
        }

        $data['data']['store_id'] = $option['store_id'];

        $config['base_url'] = 'cms_paging_inventory';
        $config['total_rows'] = $total_prd['total_prd'];

        $this->pagination->initialize($config);
        $_pagination_link = $this->pagination->create_links();
        $data['total_sls'] = $total_prd['total_quantity'];
        $data['totaloinvent'] = $total_prd['total_origin_price'];
        $data['totalsinvent'] = $total_prd['total_sell_price'];
        $data['data']['_sl_product'] = $total_prd['total_prd'];
        $data['data']['_sl_manufacture'] = $this->db->from('products_manufacture')->count_all_results();
        $data['_pagination_link'] = $_pagination_link;
        $this->load->view('ajax/inventory/list_inven', isset($data) ? $data : null);
    }
}

