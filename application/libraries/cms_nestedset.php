<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class cms_nestedset
{
    public $temp = array();
    private $CI;

    public function __construct()
    {
        $this->CI =& get_instance();
    }

    // Tự động tạo node gốc nếu là bảng trắng
    public function check_empty($table = '')
    {
        $count = $this->CI->db->from($table)->count_all_results();
        if ($count == 0) {
//			if($schoolsubject > 0){
//            , $schoolsubject = 0
//				$post_data['schoolsubject'] = $schoolsubject;
//			}
            $post_data['prd_group_name'] = 'Root';
            //$post_data['lang'] = 'all';
            $post_data['created'] = gmdate('Y-m-d H:i:s', time() + 7 * 3600);
            $post_data['user_init'] = $this->CI->auth['id'];
            $this->CI->db->insert($table, $post_data);
        }
    }

    public function dropdown($table = '', $param = NULL, $type = 'manufacture', $text = '---', $or_param = NULL)
    {
        $temp = NULL;

        $data = $this->CI->db->select('ID, prd_group_name, parentid')->from('products_group')->get()->result_array();
        $this->showCategories($data);
        return $this->temp;
    }

    public function showCategories($categories, $parentid = -1, $char = '')
    {
        foreach ($categories as $key => $item) {
            // Nếu là chuyên mục con thì hiển thị
            if ($item['parentid'] == $parentid) {
                // Xóa chuyên mục đã lặp
                $item['prd_group_name'] = $char . $item['prd_group_name'];
                $this->temp[] = $item;
                unset($categories[$key]);

                // Tiếp tục đệ quy để tìm chuyên mục con của chuyên mục đang lặp
                $this->showCategories($categories, $item['ID'], $char . '|---');
            }
        }
    }

    // Mảng dữ liệu để hiển thị danh sách

    public function data($table = '', $param = NULL, $limit = [])
    {
        $temp = null;

        $data = $this->CI->db->select('ID, prd_group_name, parentid,level')->from('products_group')->limit($limit['per_page'], ($limit['page'] - 1) * $limit['per_page'])->get()->result_array();
        $this->showCategories($data);
        return $this->temp;
    }

    // Mảng dữ liệu
    public function arr($table = '')
    {

        return $this->CI->db->select('ID, prd_group_name, parentid, level')->from($table)->order_by('created asc, ID asc')->get()->result_array();
    }

    // Chi tiết
    public function get($table = '', $param = NULL)
    {
        return $this->CI->db->select('ID, prd_group_name, parentid, level')->from($table)->where($param)->get()->row_array();
    }

    public function level($table = '')
    {
        $data = $this->CI->cms_nestedset->recursive(0, $this->CI->cms_nestedset->arr($table));
        if (isset($data) && count($data)) {
            // Duyệt tuần tự từ trên xuống theo mảng đệ quy
            foreach ($data as $key => $val) {
                // Nếu là node Root
                if ($val['parentid'] == 0) {
                    $level = 0;
                } // Nếu không phải node Root thì lấy level của cấp cha đó + thêm 1
                else if ($val['parentid'] > 0) {
                    $parent = $this->CI->cms_nestedset->get($table, array('ID' => $val['parentid']));
                    $level = ($parent['level'] + 1);
                }
                $this->CI->db->where('ID', $val['ID'])->update($table, array('level' => $level));
            }
        }
    }

    // Set level

    public function lftrgt($table = '')
    {
        $data = $this->CI->cms_nestedset->recursive(0, $this->CI->cms_nestedset->arr($table));
        if (isset($data) && count($data)) {
            $i = 0;
            $max = NULL;
            $flag = 0;
            foreach ($data as $key => $val) {
                // Tổng số node con của node
                $countSubItem = count(((array)$this->recursive($val['ID'], $data)));
                // Các node đầu tiên trong level
                if (!isset($max[$val['level']])) {
                    $left = $i;
                    $right = ($countSubItem * 2) + 1 + $i;
                    $max[$val['level']] = $right;
                    if ($left + 1 == $right) {
                        $flag = 1;
                    } else {
                        $i++;
                    }
                } else {
                    // Các node được duyệt ngay sau node lá
                    if ($flag == 1) {
                        $flag = 0;
                        $i = $max[$val['level']] + 1;
                        $left = $i;
                        $right = ($countSubItem * 2) + 1 + $i;
                        $max[$val['level']] = $right;
                        if ($left + 1 == $right) {
                            $flag = 1;
                        } else {
                            $i++;
                        }
                    } else {
                        $left = $i;
                        $right = ($countSubItem * 2) + 1 + $i;
                        $max[$val['level']] = $right;
                        if ($left + 1 == $right) {
                            $flag = 1;
                        } else {
                            $i++;
                        }
                    }
                }
                $this->CI->db->where('ID', $val['ID'])->update($table, array('lft' => $left, 'rgt' => $right,));
            }
        }
    }

    // Tạo left - right

    public function recursive($id = 0, $arr = NULL, $tree = NULL)
    {
        foreach ($arr as $val) {
            if ($val['parentid'] == $id) {
                $tree[] = $val;
                $tree = $this->recursive($val['ID'], $arr, $tree);
            }
        }
        return $tree;
    }

    public function set($table = '')
    {
        $this->CI->cms_nestedset->level($table);
        $this->CI->cms_nestedset->lftrgt($table);
    }

    public function check_parentid($table = '', $parentid = 0, $catid = 0)
    {

        if ($parentid == $catid) {
            $this->CI->form_validation->set_message('_parentid', 'Không thể chọn chính nó làm danh mục cha.');
            return FALSE;
        }
        $data = $this->CI->db->select('lft, rgt')->from($table)->where(array('ID' => $catid))->get()->row_array();
        if (isset($data) && count($data)) {
            $chidren = $this->CI->db->select('ID')->from($table)->where(array('lft >' => $data['lft'], 'lft <' => $data['rgt']))->get()->result_array();
            if (isset($chidren) && count($chidren)) {
                foreach ($chidren as $key => $val) {
                    if ($parentid == $val['ID']) {
                        $this->CI->form_validation->set_message('_parentid', 'Không thể chọn danh mục con làm danh mục cha.');
                        return FALSE;
                    }
                }
            }
        } else {
            $this->CI->form_validation->set_message('_parentid', 'Danh mục cha không tồn tại.');
            return FALSE;
        }
        return TRUE;
    }

    // Danh sách node con
    public function children($table = '', $param = NULL)
    {
        $temp = NULL;
        $_lang = $this->CI->session->userdata('_lang');
        $children = $this->CI->db->select('ID')->from($table)->where($param)->get()->result_array();
        if (isset($children) && count($children)) {
            foreach ($children as $key => $val) {
                $temp[] = $val['ID'];
            }
        }
        return $temp;
    }
}
