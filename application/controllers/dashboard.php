<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Dashboard extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null)
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'authentication');

        $today = date('Y-m-d');
        $orders = $this->db
            ->select('count(ID) as order_number,sum(total_quantity) as total_quantity,sum(total_money) as total_money')
            ->from('orders')
            ->where_in('order_status', [1, 2, 3, 4])
            ->where('DATE(sell_date)', $today)
            ->where(('deleted'), 0)
            ->get()
            ->row_array();
        $input = $this->db
            ->select('count(ID) as return_number,sum(total_quantity) as total_quantity,sum(total_money) as total_money')
            ->from('input')
            ->where('DATE(input_date)', $today)
            ->where('input_status', 1)
            ->where(['deleted' => 0, 'order_id >' => 0])
            ->get()
            ->row_array();

        $data['prd_min'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id and cms_inventory.quantity < cms_products.prd_min', 'INNER')
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->count_all_results();

        $data['prd_max'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id and cms_inventory.quantity > cms_products.prd_max', 'INNER')
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->count_all_results();

        $data['prd_available'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id', 'INNER')
            ->where(['prd_status' => 1, 'deleted' => 0, 'quantity >' => 0])
            ->count_all_results();

        $data['prd_empty'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id', 'INNER')
            ->where(['prd_status' => 1, 'deleted' => 0, 'quantity <=' => 1])
            ->count_all_results();

        $data['lamgiaban'] = $this->db->from('products')->where('prd_serial', 0)->where(['prd_status' => 1, 'deleted' => 0, 'prd_sell_price' => 0])->count_all_results();
        $data['lamgiamua'] = $this->db->from('products')->where('prd_serial', 0)->where(['prd_status' => 1, 'deleted' => 0, 'prd_origin_price' => 0])->count_all_results();
        $data['data']['_sl_product'] = $this->db->from('products')->where('prd_serial', 0)->where(['prd_status' => 1, 'deleted' => 0])->count_all_results();
        $data['data']['_sl_manufacture'] = $this->db->from('products_manufacture')->count_all_results();
        $data['tongtien'] = $orders['total_money'];
        $data['slsorders'] = $orders['order_number'];
        $data['slsitem'] = $orders['total_quantity'];
        $data['return_number'] = $input['return_number'];
        $data['return_quantity'] = $input['total_quantity'];
        $data['return_money'] = $input['total_money'];
        $data['data']['store_id'] = $this->auth['store_id'];
        $data['data']['user'] = $this->auth;
        $today = date("Y-m-d");

        $date_15 = date("Y-m-d", strtotime($today . " -10 days"));

        $revenue_report = $this->db
            ->select('(DAY(sell_date)) as day, sum(total_money) as total_money')
            ->from('orders')
            ->where('deleted', 0)
            ->where('DATE(sell_date) >=', $date_15)
            ->where('order_status', 1)
            ->group_by(('DAY(sell_date)'))
            ->get()
            ->result_array();

        $data['revenue_report'] = $revenue_report;

        $this->db->where('DATE(sell_date) >=', $date_15);

        $total_money_last_month = $this->db
            ->select('sum(total_money) as total_money_last_month')
            ->from('orders')
            ->where('order_status', 1)
            ->get()
            ->row_array();

        $data['total_money_last_month'] = $total_money_last_month['total_money_last_month'];

        $data['chart2'] = $this->db
            ->select('prd_name,sum(cms_report.output) as total_sell')
            ->from('report')
            ->join('orders', 'orders.ID=cms_report.transaction_id', 'INNER')
            ->join('products', 'products.ID=cms_report.product_id', 'INNER')
            ->where_in('order_status', [1, 2, 3, 4])
            ->where(['cms_report.deleted' => 0, 'type' => 3])
            ->limit(10)
            ->group_by('product_id')
            ->order_by('total_sell', 'desc')
            ->get()
            ->result_array();

        if (in_array(1, $this->auth['group_permission']))
            $data['template'] = 'home/index';
        else
            $data['template'] = 'home/deny';
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_paging_product_available()
    {
        $data['prd'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id', 'INNER')
            ->where('quantity >', 0)
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->order_by('quantity desc')
            ->get()
            ->result_array();

        $this->load->view('ajax/dashboard/list_product', isset($data) ? $data : null);
    }

    public function cms_paging_product_empty()
    {
        $data['prd'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id', 'INNER')
            ->where('quantity <=', 0)
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->order_by('quantity desc')
            ->get()
            ->result_array();

        $this->load->view('ajax/dashboard/list_product', isset($data) ? $data : null);
    }

    public function cms_paging_product_min()
    {
        $data['prd'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id and cms_inventory.quantity < cms_products.prd_min', 'INNER')
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->order_by('quantity desc')
            ->get()
            ->result_array();

        $this->load->view('ajax/dashboard/list_product', isset($data) ? $data : null);
    }

    public function cms_paging_product_max()
    {
        $data['prd'] = $this->db
            ->from('inventory')
            ->join('products', 'products.ID=inventory.product_id and cms_inventory.quantity > cms_products.prd_max', 'INNER')
            ->where('prd_status', 1)
            ->where(('deleted'), 0)
            ->order_by('quantity desc')
            ->get()
            ->result_array();

        $this->load->view('ajax/dashboard/list_product', isset($data) ? $data : null);
    }

}
