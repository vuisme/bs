<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class CMS_authentication
{
    private $CI;
    private $_permissID;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function check()
    {
        if (CMS_Cookie::exists('user_logged' . str_replace('.', '', CMS_DB_NAME))) {
            $cookie = CMS_Cookie::get('user_logged' . str_replace('.', '', CMS_DB_NAME));
            $cookie = json_decode(CMS_Cookie::decode($cookie), true);
            $user = $this->CI->db->select('id,username,password,salt,display_name,email,group_id,store_id,commission')->where('username', $cookie['username'])->or_where('email', $cookie['username'])->from('users')->get()->row_array();
            if (isset($user) && count($user)) {
                $group = $this->CI->db->select('id, group_permission, group_name')->where('id', $user['group_id'])->from('users_group')->get()->row_array();
                if ($user['username'] == $cookie['username'] && $user['password'] == $cookie['password'] && $user['salt'] == $cookie['salt']) {
                    $data = ['username' => $user['username'], 'password' => $user['password'], 'salt' => $user['salt']];
                    CMS_Cookie::put('user_logged' . str_replace('.', '', CMS_DB_NAME), CMS_Cookie::encode(json_encode($data)), COOKIE_EXPIRY);

                    return ['id' => $user['id'],
                        'commission' => $user['commission'],
                        'username' => $user['username'],
                        'password' => $user['password'],
                        'salt' => $user['salt'],
                        'email' => $user['email'],
                        'display_name' => $user['display_name'],
                        'group_id' => $user['group_id'],
                        'group_name' => $group['group_name'],
                        'group_permission' => json_decode($group['group_permission'], true),
                        'store_id' => $user['store_id']
                    ];
                }
            }
        }
        return null;
    }
}
