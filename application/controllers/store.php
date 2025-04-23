<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Store extends CI_Controller
{
    private $auth;

    public function __construct()
    {
        parent::__construct();
        $this->auth = $this->cms_authentication->check();
    }

    public function cms_del_store()
    {
        $id = (int)$this->input->post('id');
        $customer = $this->db->from('stores')->where('ID', $id)->get()->row_array();
        if (!isset($customer) && count($customer) == 0) {
            echo $this->messages;
            return;
        } else {
            $this->db->where('ID', $id)->delete('stores');
            echo $this->messages = '1';
        }
    }

    public function cms_update_store($id)
    {
        $id = (int)$id;
        $data = $this->input->post('data');
        $data['updated'] = gmdate("Y:m:d H:i:s", time() + 7 * 3600);
        $data['user_upd'] = $this->auth['id'];
        $this->db->where('ID', $id)->update('stores', $data);
        echo $this->messages = '1';
    }
}
