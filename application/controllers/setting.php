<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Setting extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function index()
    {
        if ($this->auth == null || !in_array(11, $this->auth['group_permission']))
            $this->cms_common_string->cms_redirect(CMS_BASE_URL . 'backend');

        $data['seo']['title'] = "Phần mềm quản lý bán hàng";
        $user = $this->db->select('users.id, username, email, display_name, user_status, group_name,commission,group_id')->from('users')->join('users_group', 'users_group.id = users.group_id')->get()->result_array();
        $data['data']['template'] = $this->db->select('content')->from('templates')->where('id', 1)->limit(1)->get()->row_array();
        $data['data']['list_template'] = $this->db->from('templates')->where('ID <', 4)->get()->result_array();
        $data['data']['group'] = $this->db->from('users_group')->get()->result_array();
        $data['data']['_user'] = $user;
        $data['data']['user'] = $this->auth;
        $data['data']['list_store_show'] = $this->db->from('stores')->get()->result_array();
        $data['data']['store_id'] = $this->auth['store_id'];
        $data['template'] = 'setting/setting';
        $this->load->view('layout/index', isset($data) ? $data : null);
    }

    public function cms_update_setting($id)

    {

        $data = $this->input->post('data');

        $setting = $this->db->from('setting')->where('ID', $id)->get()->row_array();

        if (!isset($setting) && count($setting) == 0) {

            echo $this->messages = 0;

            return;

        } else {

            $data['user_upd'] = $this->auth['id'];

            $this->db->where('ID', $id)->update('setting', $data);

            echo $this->messages = "1";

        }

    }

    public function cms_save_template($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $template = $this->db->from('templates')->where('id', $id)->get()->row_array();
        if (count($template) == 0) {
            echo $this->messages = '0';
            return;
        }

        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('id', $id)->update('templates', $data);
        echo $this->messages = '1';
    }

    public function cms_load_template($id)
    {
        $template = $this->db->from('templates')->where('id', $id)->get()->row_array();
        if (count($template) == 0) {
            echo $this->messages = '0';
            return;
        }
        echo $this->message = $template['content'];
    }

    public function cms_crstore()
    {
        $store_name = $this->input->post('store_name');
        $count = $this->db->where('store_name', $store_name)->from('stores')->count_all_results();
        if ($count == 0) {
            $data = ['store_name' => $store_name, 'user_init' => $this->auth['id']];
            $this->db->insert('stores', $data);
            echo $this->messages = '1';
        } else {
            echo $this->messages = 'Kho ' . $store_name . ' đã tồn tại trong hệ thống.Vui lòng tạo tên nhóm khác.';
        }
    }
}
