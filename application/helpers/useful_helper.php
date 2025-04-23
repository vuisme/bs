<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

if (!function_exists('cms_common_input')) {
    function cms_common_input($obj, $item)
    {
        return (isset($obj[$item]) && !empty($obj[$item])) ? htmlspecialchars($obj[$item]) : '';
    }
}

if (!function_exists('cms_updatecustomerdebtbycustomerid')) {
    function cms_updatecustomerdebtbycustomerid($customer_id)
    {
        $CI =& get_instance();
        $customer = $CI->db->from('customers')->where('ID', $customer_id)->get()->row_array();
        if (isset($customer) && count($customer) > 0) {

            $order = $CI->db->select('sum(lack) as lack')->from('orders')->where('input_id', 0)->where('deleted', 0)->where('customer_id', $customer['ID'])->where('order_status >', 0)->where('order_status <', 5)->get()->row_array();

            if (isset($order['lack']) && count($order) > 0)
                $order_lack = $order['lack'];
            else
                $order_lack = 0;

            $order_return = $CI->db->select('sum(lack) as lack')->from('input')->where('deleted', 0)->where('supplier_id', $customer['ID'])->where('input_status', 1)->where('order_id >', 0)->get()->row_array();

            if (isset($order_return['lack']) && count($order_return) > 0)
                $order_lack = $order_lack - $order_return['lack'];

            $total_order = $CI->db
                ->select('max(sell_date) as last_sell_date,sum(total_money) as total_money_order')
                ->from('orders')
                ->where('customer_id', $customer['ID'])
                ->where('deleted', 0)
                ->where_not_in('order_status', [0, 5])
                ->get()
                ->row_array();

            $update_customer_debt = array();
            $update_customer_debt['customer_debt'] = $order_lack;

            if (isset($total_order['total_money_order']) && count($total_order) > 0) {
                $update_customer_debt['total_money_order'] = $total_order['total_money_order'];
                $update_customer_debt['last_sell_date'] = $total_order['last_sell_date'];
            } else {
                $update_customer_debt['total_money_order'] = 0;
                $update_customer_debt['last_sell_date'] = null;
            }

            $CI->db->where('ID', $customer['ID'])->update('customers', $update_customer_debt);
        }
    }
}

if (!function_exists('cms_getcustomernamebyorderid')) {
    function cms_getcustomernamebyorderid($id)
    {
        $CI =& get_instance();
        $order = $CI->db
            ->select('customer_name,output_code')
            ->from('orders')
            ->join('customers', 'customers.ID=orders.customer_id', 'LEFT')
            ->where(['cms_orders.ID' => $id])
            ->get()
            ->row_array();

        if (isset($order) && count($order) > 0)
            return $order;
        else
            return null;
    }
}

if (!function_exists('cms_getsuppliernamebyinputid')) {
    function cms_getsuppliernamebyinputid($id)
    {
        $CI =& get_instance();
        $order = $CI->db
            ->select('supplier_name,input_code')
            ->from('input')
            ->join('suppliers', 'suppliers.ID=input.supplier_id', 'LEFT')
            ->where(['input.ID' => $id])
            ->get()
            ->row_array();

        if (isset($order) && count($order) > 0)
            return $order;
        else
            return null;
    }
}

if (!function_exists('cms_ConvertDateTime')) {

    function cms_ConvertDateTime($date)

    {

        if ($date == null)
            return '';
        else
            return ($date == '' || $date == '0000-00-00 00:00:00') ? '' : date('H:i d/m/Y', strtotime($date));

    }
}

if (!function_exists('cms_ConvertDate')) {

    function cms_ConvertDate($date)

    {

        if ($date == null || $date == '0000-00-00')
            return '';
        else
            return $date == '' ? '' : date('d/m/Y', strtotime($date));

    }

}

if (!function_exists('cms_show_image')) {

    function cms_show_image($name)

    {

        if ($name == '' || $name == null)
            return 'no_image.jpg';
        else
            return $name;

    }

}

if (!function_exists('cms_getSettingValueByID')) {

    function cms_getSettingValueByID($id)

    {

        if ($id == '' || $id == 0)

            return '';

        $CI =& get_instance();

        $data = $CI->db->select('setting_value')->from('setting')->where('ID', $id)->get()->row_array();

        if (!isset($data) && count($data) == 0) {
            return '';
        } else {
            return $data['setting_value'];
        }

    }

}

if (!function_exists('GetVolumeLabel')) {
    function GetVolumeLabel()
    {

        if (preg_match('#Volume Serial Number is (.*)\n#i', shell_exec('dir ' . 'c' . ':'), $m)) {
            $volname = ' (' . $m[1] . ')';
        } else {
            $volname = '';
        }

        $volname = str_replace("(", "", str_replace(")", "", $volname));

        return $volname;

    }
}

if (!function_exists('cms_render_html')) {
    function cms_render_html($val, $class, $icon = [], $text = [])
    {
        return ($val == 1) ? "<span class='{$class}'><i class='fa {$icon[0]}'></i> " . $text[0] . "</span>" : "<span class='{$class}'><i class='fa {$icon[1]}'></i> " . $text[1] . "</span>";
    }
}

if (!function_exists('cms_delete_public_file_by_extend')) {

    function cms_delete_public_file_by_extend($extend)
    {

        {
            $fullPath = ROOT_UPLOAD_IMPORT_PATH;
            array_map('unlink', glob("$fullPath*" . $extend));
        }

    }
}

if (!function_exists('cms_getEmployee')) {
    function cms_getEmployee($gid)
    {
        $CI =& get_instance();
        $count = $CI->db->where('group_id', $gid)->from('users')->count_all_results();

        return (!isset($count) && !empty($count)) ? '-' : $count;
    }
}

if (!function_exists('cms_getNameReceiptMethodByID')) {
    function cms_getNameReceiptMethodByID($id)
    {
        $list = cms_getListReceiptMethod();
        return $list[$id];
    }
}

if (!function_exists('cms_getListReceiptMethod')) {
    function cms_getListReceiptMethod()
    {
        $list = ['1' => 'Tiền mặt', '2' => 'Thẻ', '3' => 'CK'];
        return $list;
    }
}

if (!function_exists('cms_convertserial')) {
    function cms_convertserial($list_serial)
    {
        if ($list_serial != '')
            return str_replace(",", "<br>", $list_serial);
        else
            return '';
    }
}

if (!function_exists('cms_convert_number_to_words')) {
    function cms_convert_number_to_words($amount)
    {
        if ($amount == 0) {
            return "Không đồng";
        } else if ($amount < 0) {
            return "Tiền phải là số nguyên dương lớn hơn số 0";
        }

        $Text = array("không", "một", "hai", "ba", "bốn", "năm", "sáu", "bảy", "tám", "chín");
        $TextLuythua = array("", "nghìn", "triệu", "tỷ", "ngàn tỷ", "triệu tỷ", "tỷ tỷ");
        $textnumber = "";
        $length = strlen($amount);

        for ($i = 0; $i < $length; $i++)
            $unread[$i] = 0;

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);

            if (($so == 0) && ($i % 3 == 0) && ($unread[$i] == 0)) {
                for ($j = $i + 1; $j < $length; $j++) {
                    $so1 = substr($amount, $length - $j - 1, 1);
                    if ($so1 != 0)
                        break;
                }

                if (intval(($j - $i) / 3) > 0) {
                    for ($k = $i; $k < intval(($j - $i) / 3) * 3 + $i; $k++)
                        $unread[$k] = 1;
                }
            }
        }

        for ($i = 0; $i < $length; $i++) {
            $so = substr($amount, $length - $i - 1, 1);
            if ($unread[$i] == 1)
                continue;

            if (($i % 3 == 0) && ($i > 0))
                $textnumber = $TextLuythua[$i / 3] . " " . $textnumber;

            if ($i % 3 == 2)
                $textnumber = 'trăm ' . $textnumber;

            if ($i % 3 == 1)
                $textnumber = 'mươi ' . $textnumber;


            $textnumber = $Text[$so] . " " . $textnumber;
        }

        $textnumber = str_replace("không mươi", "lẻ", $textnumber);
        $textnumber = str_replace("lẻ không", "", $textnumber);
        $textnumber = str_replace("mươi không", "mươi", $textnumber);
        $textnumber = str_replace("một mươi", "mười", $textnumber);
        $textnumber = str_replace("mươi năm", "mươi lăm", $textnumber);
        $textnumber = str_replace("mươi một", "mươi mốt", $textnumber);
        $textnumber = str_replace("mười năm", "mười lăm", $textnumber);

        return ucfirst($textnumber . " đồng");
    }
}

if (!function_exists('cms_getNamegroupbyID')) {
    function cms_getNamegroupbyID($id)
    {
        $name = 'Chưa có';
        $CI =& get_instance();
        $group = $CI->db->select('prd_group_name')->from('products_group')->where('ID', $id)->get()->row_array();
        if (isset($group) && count($group)) {
            return $name = $group['prd_group_name'];
        }

        return $name;
    }
}
if (!function_exists('cms_getNamemanufacturebyID')) {
    function cms_getNamemanufacturebyID($id)
    {
        $name = 'Chưa có';
        $CI =& get_instance();
        $manufacture = $CI->db->select('prd_manuf_name')->from('products_manufacture')->where('ID', $id)->get()->row_array();
        if (isset($manufacture) && count($manufacture)) {
            $name = $manufacture['prd_manuf_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_input_inventory')) {
    function cms_input_inventory($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID'], 'inventory_expire' => $item['expire']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);
            } else {
                $inventory = ['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $product['ID'], 'quantity' => $item['quantity']];
                $CI->db->insert('inventory', $inventory);
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_input_inventory_and_serial')) {
    function cms_input_inventory_and_serial($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity,ID_temp')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $product['ID']])->get()->row_array();

            if ($product['prd_serial'] == 0) {
                if (!empty($inventory_quantity)) {
                    $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => $item['quantity']];
                    $CI->db->insert('inventory', $inventory);
                }
            } else {
                if (!is_array($item['list_serial']))
                    $item['list_serial'] = explode(",", $item['list_serial']);

                if ($item['quantity'] != count($item['list_serial'])) {
                    return 'Mã sp ' . $product['prd_code'] . ' có số lượng và số serial không trùng khớp';
                } else {
                    if (!empty($inventory_quantity)) {
                        $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);

                        foreach ($item['list_serial'] as $serial) {
                            $check_serial = $CI->db->select('quantity,ID_temp')->from('inventory_serial')->where(['inventory_id' => $inventory_quantity['ID_temp'], 'serial' => $serial])->get()->row_array();
                            if (isset($check_serial) && count($check_serial) > 0) {
                                if ($check_serial['quantity'] == 1) {
                                    return 'Mã sp ' . $product['prd_code'] . ' có số serial ' . $serial . ' đã tồn tại. Vui lòng kiểm tra lại';
                                } else {
                                    $CI->db->where(['ID_temp' => $check_serial['ID_temp']])->update('inventory_serial', ['quantity' => 1]);
                                }
                            } else {
                                $inventory_serial = ['inventory_id' => $inventory_quantity['ID_temp'], 'product_id' => $product['ID'], 'serial' => $serial, 'quantity' => 1];
                                $CI->db->insert('inventory_serial', $inventory_serial);
                            }
                        }
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => $item['quantity']];
                        $CI->db->insert('inventory', $inventory);

                        $inventory_id = $CI->db->insert_id();

                        foreach ($item['list_serial'] as $serial) {
                            $inventory_serial = ['inventory_id' => $inventory_id, 'product_id' => $product['ID'], 'serial' => $serial, 'quantity' => 1];
                            $CI->db->insert('inventory_serial', $inventory_serial);
                        }
                    }
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_input_inventory_and_serial_without_alert')) {
    function cms_input_inventory_and_serial_without_alert($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity,ID_temp')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $product['ID']])->get()->row_array();

            if ($product['prd_serial'] == 0) {
                if (!empty($inventory_quantity)) {
                    $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => $item['quantity']];
                    $CI->db->insert('inventory', $inventory);
                }
            } else {
                if (!is_array($item['list_serial']))
                    $item['list_serial'] = explode(",", $item['list_serial']);

                if ($item['quantity'] != count($item['list_serial'])) {
                    return 'Mã sp ' . $product['prd_code'] . ' có số lượng và số serial không trùng khớp';
                } else {
                    if (!empty($inventory_quantity)) {
                        $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);

                        foreach ($item['list_serial'] as $serial) {
                            $check_serial = $CI->db->select('quantity,ID_temp')->from('inventory_serial')->where(['inventory_id' => $inventory_quantity['ID_temp'], 'serial' => $serial])->get()->row_array();
                            if (isset($check_serial) && count($check_serial) > 0) {
                                if ($check_serial['quantity'] == 1) {
                                    return 'Mã sp ' . $product['prd_code'] . ' có số serial ' . $serial . ' đã tồn tại. Vui lòng kiểm tra lại';
                                } else {
                                    $CI->db->where(['ID_temp' => $check_serial['ID_temp']])->update('inventory_serial', ['quantity' => ($check_serial['quantity'] + 1)]);
                                }
                            } else {
                                $inventory_serial = ['inventory_id' => $inventory_quantity['ID_temp'], 'product_id' => $product['ID'], 'serial' => $serial, 'quantity' => 1];
                                $CI->db->insert('inventory_serial', $inventory_serial);
                            }
                        }
                    } else {
                        $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => $item['quantity']];
                        $CI->db->insert('inventory', $inventory);

                        $inventory_id = $CI->db->insert_id();

                        foreach ($item['list_serial'] as $serial) {
                            $inventory_serial = ['inventory_id' => $inventory_id, 'product_id' => $product['ID'], 'serial' => $serial, 'quantity' => 1];
                            $CI->db->insert('inventory_serial', $inventory_serial);
                        }
                    }
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_input_inventory_and_expire_date')) {
    function cms_input_inventory_and_expire_date($list, $store_id, $create_date = null)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $today = date('Y-m-d');
            $inventory_expire = '';
            if ($item['expire'] != '' && $item['expire'] > 0) {
                if ($create_date == null)
                    $inventory_expire = date("Y-m-d", strtotime($today . " +" . $item['expire'] . " days"));
                else
                    $inventory_expire = date("Y-m-d", strtotime($create_date . " +" . $item['expire'] . " days"));
            }

            $inventory_quantity = $CI->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'inventory_expire' => $inventory_expire, 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID'], 'inventory_expire' => $inventory_expire])->update('inventory', ['quantity' => $inventory_quantity['quantity'] + $item['quantity']]);
            } else {
                $inventory = ['store_id' => $store_id, 'inventory_expire' => $inventory_expire, 'product_id' => $product['ID'], 'quantity' => $item['quantity']];

                if ($inventory_expire != '') {
                    $inventory['expire_date'] = $inventory_expire;
                }

                $CI->db->insert('inventory', $inventory);
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] + $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_getListProvince')) {

    function cms_getListProvince()

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID,province_name')->from('province')->get()->result_array();

        return $data;

    }

}

if (!function_exists('cms_getListWard')) {

    function cms_getListWard()

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID,ward_name')->from('ward_ghn')->get()->result_array();

        return $data;

    }

}

if (!function_exists('cms_getListSerialByproductid')) {

    function cms_getListSerialByproductid($product_id, $store_id)

    {
        $CI =& get_instance();
        if ($store_id > 0)
            $list_serial = $CI->db->select('distinct(serial)')->from('inventory')->join('inventory_serial', 'inventory.ID_temp=inventory_serial.inventory_id', 'INNER')->where('inventory_serial.quantity >', 0)->where('inventory.quantity >', 0)->where('inventory.product_id', $product_id)->where('store_id', $store_id)->get()->result_array();
        else
            $list_serial = $CI->db->select('serial')->where('product_id', $product_id)->where('quantity >', 0)->from('inventory_serial')->get()->result_array();

        $detail = array();
        foreach ((array)$list_serial as $serial) {
            $detail[] = $serial['serial'];
        }

        return implode('<br>', $detail);

    }

}

if (!function_exists('cms_getListDistrictByProvince')) {

    function cms_getListDistrictByProvince($id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID,district_name')->from('district')->where('province_id', $id)->get()->result_array();
        return $data;
    }

}

if (!function_exists('qrcode')) {
    function qrcode($type = 'text', $text = 'http://phongtran.info', $size = 2, $level = 'H', $sq = null)
    {
        $CI =& get_instance();
        $file_name = 'public/templates/uploads/' . time() . '.png';
        if (is_readable($file_name) && unlink($file_name)) {

        }
        if ($type == 'link') {
            $text = urldecode($text);
        }

        $CI->load->library('phpqrcode');
        $config = array('data' => $text, 'size' => $size, 'level' => $level, 'savename' => $file_name);
        $config['svg'] = 1;
        $CI->phpqrcode->generate($config);
        $imagedata = file_get_contents($file_name);
        unlink($file_name);
        return "<img src='data:image/svg+xml;base64," . base64_encode($imagedata) . "' alt='{$text}' class='qrimg' style='float:center;' />";
    }
}

if (!function_exists('cms_getListWardByDistrict')) {

    function cms_getListWardByDistrict($id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID,ward_name')->from('ward')->where('district_id', $id)->get()->result_array();
        return $data;
    }

}

if (!function_exists('cms_getWardNameByID')) {

    function cms_getWardNameByID($id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ward_name')->from('ward')->where('ID', $id)->get()->row_array();
        if (isset($data) && count($data))
            return $data['ward_name'];
        else
            return '';
    }

}

if (!function_exists('cms_getDistrictNameByID')) {

    function cms_getDistrictNameByID($id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('district_name')->from('district')->where('ID', $id)->get()->row_array();
        if (isset($data) && count($data))
            return $data['district_name'];
        else
            return '';
    }

}

if (!function_exists('cms_getProvinceNameByID')) {

    function cms_getProvinceNameByID($id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('province_name')->from('province')->where('ID', $id)->get()->row_array();
        if (isset($data) && count($data))
            return $data['province_name'];
        else
            return '';
    }

}

if (!function_exists('cms_CheckProvinceNameByName')) {

    function cms_CheckProvinceNameByName($name)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID')->from('province')->where('province_name', $name)->get()->row_array();
        if (isset($data) && count($data))
            return $data['ID'];
        else
            return 0;
    }

}

if (!function_exists('cms_CheckDistrictNameByName')) {

    function cms_CheckDistrictNameByName($name, $province_id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID')->from('district')->where('district_name', $name)->where('province_id', $province_id)->get()->row_array();
        if (isset($data) && count($data))
            return $data['ID'];
        else
            return 0;
    }

}

if (!function_exists('cms_CheckWardNameByName')) {

    function cms_CheckWardNameByName($name, $district_id)

    {
        $CI =& get_instance();
        $data = $CI->db->select('ID')->from('ward')->where('ward_name', $name)->where('district_id', $district_id)->get()->row_array();
        if (isset($data) && count($data))
            return $data['ID'];
        else
            return 0;
    }

}

if (!function_exists('cms_getFullAddress')) {

    function cms_getFullAddress($ward_id, $district_id, $province_id)

    {
        $CI =& get_instance();
        if ($ward_id > 0) {
            $data = $CI->db->select('ward_name,district_name,province_name')
                ->from('ward')
                ->join('district', 'district.ID=ward.district_id', 'INNER')
                ->join('province', 'province.ID=district.province_id', 'INNER')
                ->where('cms_ward.ID', $ward_id)
                ->get()->row_array();
            if (isset($data) && count($data)) {
                return ',' . $data['ward_name'] . ',' . $data['district_name'] . ',' . $data['province_name'];
            }
        } else if ($district_id > 0) {
            $data = $CI->db->select('district_name,province_name')
                ->from('district')
                ->join('province', 'province.ID=district.province_id', 'INNER')
                ->where('cms_district.ID', $district_id)
                ->get()->row_array();
            if (isset($data) && count($data)) {
                return ',' . $data['district_name'] . ',' . $data['province_name'];
            }
        } else if ($province_id > 0) {
            $data = $CI->db->select('province_name')
                ->from('province')
                ->where('ID', $province_id)
                ->get()->row_array();
            if (isset($data) && count($data)) {
                return ',' . $data['province_name'];
            }
        } else
            return '';
    }

}

if (!function_exists('cms_output_inventory_and_expire_date')) {
    function cms_output_inventory_and_expire_date($list, $store_id, $create_date)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_expire = '';
            if ($item['expire'] != '' && $item['expire'] > 0) {

                $inventory_expire = date("Y-m-d", strtotime($create_date . " +" . $item['expire'] . " days"));
            }

            $inventory_quantity = $CI->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'inventory_expire' => $inventory_expire, 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                if ($product['prd_allownegative'] == 0 && $inventory_quantity['quantity'] < $item['quantity']) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang còn tồn chỉ ' . $inventory_quantity['quantity'] . ' sản phẩm';
                } else {
                    $CI->db->where(['store_id' => $store_id, 'inventory_expire' => $inventory_expire, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity']]);
                }
            } else {
                if ($product['prd_allownegative'] == 0) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang hết hàng.';
                } else {
                    $inventory = ['store_id' => $store_id, 'inventory_expire' => $inventory_expire, 'product_id' => $product['ID'], 'quantity' => -$item['quantity']];
                    $CI->db->insert('inventory', $inventory);
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_output_inventory')) {
    function cms_output_inventory($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity')->from('inventory')->where(['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                if ($product['prd_allownegative'] == 0 && $inventory_quantity['quantity'] < $item['quantity']) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang còn tồn chỉ ' . $inventory_quantity['quantity'] . ' sản phẩm';
                } else {
                    $CI->db->where(['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity']]);
                }
            } else {
                if ($product['prd_allownegative'] == 0) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang hết hàng.';
                } else {
                    $inventory = ['store_id' => $store_id, 'inventory_expire' => $item['expire'], 'product_id' => $product['ID'], 'quantity' => -$item['quantity']];
                    $CI->db->insert('inventory', $inventory);
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_output_inventory_and_serial')) {
    function cms_output_inventory_and_serial($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity,ID_temp')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                if ($product['prd_allownegative'] == 0 && $inventory_quantity['quantity'] < $item['quantity']) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang còn tồn chỉ ' . $inventory_quantity['quantity'] . ' sản phẩm';
                } else {
                    $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity']]);
                }
            } else {
                if ($product['prd_allownegative'] == 0) {
                    return 'Mã sp ' . $product['prd_code'] . ' đang hết hàng.';
                } else {
                    $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => -$item['quantity']];
                    $CI->db->insert('inventory', $inventory);
                }
            }

            if ($product['prd_serial'] == 1) {
                if (!is_array($item['list_serial']))
                    $item['list_serial'] = explode(",", $item['list_serial']);

                foreach ($item['list_serial'] as $serial) {
                    $check_serial = $CI->db->from('inventory_serial')->where('inventory_id', $inventory_quantity['ID_temp'])->where('serial', $serial)->where('quantity', 1)->count_all_results();
                    if ($check_serial == 1) {
                        $CI->db->where('inventory_id', $inventory_quantity['ID_temp'])->where('serial', $serial)->where('quantity', 1)->update('inventory_serial', ['quantity' => 0]);
                    } else {
                        return 'Mã sp ' . $product['prd_code'] . ' có số serial ' . $serial . ' không tồn tại. Vui lòng kiểm tra lại';
                    }
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_check_serial_with_alert')) {
    function cms_check_serial_with_alert($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity,ID_temp')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                if ($product['prd_allownegative'] == 0 && $inventory_quantity['quantity'] < 0) {
                    return 'Mã sp ' . $product['prd_code'] . ' không còn đủ tồn kho';
                }
            }

            if ($product['prd_serial'] == 1) {
                if (!is_array($item['list_serial']))
                    $item['list_serial'] = explode(",", $item['list_serial']);

                foreach ($item['list_serial'] as $serial) {
                    $check_serial = $CI->db->from('inventory_serial')->where('inventory_id', $inventory_quantity['ID_temp'])->where('serial', $serial)->where('quantity <', 0)->count_all_results();
                    if ($check_serial > 0) {
                        return 'Mã sp ' . $product['prd_code'] . ' có số serial ' . $serial . ' bị âm. Vui lòng kiểm tra lại';
                    }
                }
            }
        }

        return 1;
    }
}

if (!function_exists('cms_output_inventory_and_serial_without_alert')) {
    function cms_output_inventory_and_serial_without_alert($list, $store_id)
    {
        $CI =& get_instance();

        foreach ((array)$list as $item) {
            $product = $CI->db->from('products')->where('ID', $item['id'])->get()->row_array();

            $inventory_quantity = $CI->db->select('quantity,ID_temp')->from('inventory')->where(['store_id' => $store_id, 'product_id' => $product['ID']])->get()->row_array();

            if (!empty($inventory_quantity)) {
                $CI->db->where(['store_id' => $store_id, 'product_id' => $product['ID']])->update('inventory', ['quantity' => $inventory_quantity['quantity'] - $item['quantity']]);
            } else {
                $inventory = ['store_id' => $store_id, 'product_id' => $product['ID'], 'quantity' => -$item['quantity']];
                $CI->db->insert('inventory', $inventory);

            }

            if ($product['prd_serial'] == 1) {
                if (!is_array($item['list_serial']))
                    $item['list_serial'] = explode(",", $item['list_serial']);

                foreach ($item['list_serial'] as $serial) {
                    $check_serial = $CI->db->from('inventory_serial')->where('inventory_id', $inventory_quantity['ID_temp'])->where('serial', $serial)->get()->row_array();
                    if (isset($check_serial) && count($check_serial)) {
                        $CI->db->where('ID_temp', $check_serial['ID_temp'])->update('inventory_serial', ['quantity' => ($check_serial['quantity'] - 1)]);
                    } else {
                        return 'Mã sp ' . $product['prd_code'] . ' có số serial ' . $serial . ' không tồn tại. Vui lòng kiểm tra lại';
                    }
                }
            }

            $sls = array();

            $sls['prd_sls'] = $product['prd_sls'] - $item['quantity'];

            $CI->db->where('ID', $product['ID'])->update('products', $sls);
        }

        return 1;
    }
}

if (!function_exists('cms_getNameunitbyID')) {
    function cms_getNameunitbyID($id)
    {
        $name = 'Chưa có';
        $CI =& get_instance();
        $unit = $CI->db->select('prd_unit_name')->from('products_unit')->where('ID', $id)->get()->row_array();
        if (isset($unit) && count($unit)) {
            $name = $unit['prd_unit_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNameCheckbyID')) {
    function cms_getNameCheckbyID($id)
    {
        $name = 'Không';
        if ($id == 1)
            $name = 'Có';

        return $name;
    }
}

if (!function_exists('cms_getCodecustomerbyID')) {
    function cms_getCodecustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_code')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_code'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamecustomerbyID')) {
    function cms_getNamecustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_name')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getAddresscustomerbyID')) {
    function cms_getAddresscustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_addr')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_addr'];
        }

        return $name;
    }
}

if (!function_exists('cms_getPhonecustomerbyID')) {
    function cms_getPhonecustomerbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('customer_phone')->from('customers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['customer_phone'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamesupplierbyID')) {
    function cms_getNamesupplierbyID($id)
    {
        $name = 'Không nhập';
        $CI =& get_instance();
        $customer = $CI->db->select('supplier_name')->from('suppliers')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['supplier_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNameVATbyID')) {
    function cms_getNameVATbyID($id)
    {
        $list = cms_getListVAT();
        return $list[$id];
    }
}

if (!function_exists('cms_getListVAT')) {
    function cms_getListVAT()
    {
        return array(
            '0' => '0%',
            '5' => '5%',
            '10' => '10%'
        );
    }
}

if (!function_exists('cms_getListReceiptType')) {
    function cms_getListReceiptType()
    {
        return array(
            '3' => 'Thu bán hàng',
            '4' => 'Thu khách lẻ',
            '5' => 'Thu HĐGTGT',
            '6' => 'Thu khác'
        );
    }
}

if (!function_exists('cms_getListPaymentType')) {
    function cms_getListPaymentType()
    {
        return array(
            '2' => 'Chi mua hàng',
            '3' => 'Chi lương',
            '4' => 'Chi nhập hàng',
            '5' => 'Tiền xăng',
            '6' => 'Thuê xe và gửi hàng',
            '7' => 'Tiền ứng',
            '8' => 'Chi khác'
        );
    }
}

if (!function_exists('cms_getListReporttype')) {
    function cms_getListReporttype()
    {
        return array(
            '1' => 'Tạo sản phẩm mới',
            '2' => 'Nhập hàng',
            '3' => 'Bán hàng',
            '4' => 'Chuyển hàng',
            '5' => 'Xác nhận nhập kho',
            '6' => 'Nhập trả hàng',
            '7' => 'Xuất trả hàng',
            '8' => 'Kiểm kê hàng',
        );
    }
}

if (!function_exists('cms_getNameReportTypeByID')) {
    function cms_getNameReportTypeByID($id)
    {
        $list = cms_getListReporttype();
        return $list[$id];
    }
}

if (!function_exists('cms_getNamePaymentTypeByID')) {
    function cms_getNamePaymentTypeByID($id)
    {
        $list = cms_getListPaymentType();
        return $list[$id];
    }
}

if (!function_exists('cms_getNameReceiptTypeByID')) {
    function cms_getNameReceiptTypeByID($id)
    {
        $list = cms_getListReceiptType();
        return $list[$id];
    }
}

if (!function_exists('cms_getListOrderStatus')) {
    function cms_getListOrderStatus()
    {
        return array(
            '0' => 'Lưu tạm',
            '1' => 'Hoàn thành',
            '2' => 'Xác nhận',
            '3' => 'Đang giao',
            '4' => 'Đã giao',
            '5' => 'Hủy'
        );
    }
}

if (!function_exists('cms_getListFix')) {
    function cms_getListFix()
    {
        return array(
            '0' => 'Mới nhận',
            '1' => 'Chưa xử lý',
            '2' => 'Đang xử lý',
            '3' => 'Không thể xử lý',
            '4' => 'Đã xử lý hoàn tất'
        );
    }
}

if (!function_exists('cms_getListFixreturn')) {
    function cms_getListFixreturn()
    {
        return array(
            '0' => 'Chưa trả',
            '2' => 'Đã gọi khách',
            '4' => 'Đã trả khách',
        );
    }
}

if (!function_exists('cms_getListInputStatus')) {
    function cms_getListInputStatus()
    {
        return array(
            '0' => 'Lưu tạm',
            '1' => 'Hoàn thành',
        );
    }
}

if (!function_exists('cms_getNamestatusFixbyID')) {
    function cms_getNamestatusFixbyID($id)
    {
        $list = cms_getListFix();
        return $list[$id];
    }
}

if (!function_exists('cms_getNamestatusFixReturnbyID')) {
    function cms_getNamestatusFixReturnbyID($id)
    {
        $list = cms_getListFixreturn();
        return $list[$id];
    }
}

if (!function_exists('cms_getNamestatusbyID')) {
    function cms_getNamestatusbyID($id)
    {
        $list = cms_getListOrderStatus();
        return $list[$id];
    }
}

if (!function_exists('  mySort')) {

    function mySort($a, $b)
    {

        {

            if ($a['invoice_date'] == $b['invoice_date']) {
                return 0;
            }
            return ($a['invoice_date'] < $b['invoice_date']) ? -1 : 1;

        }

    }
}

if (!function_exists('cms_getNamestatusWarrantybyID')) {
    function cms_getNamestatusWarrantybyID($id)
    {
        $name = "";
        switch ($id) {
            case '0':
            {
                $name = 'Đang bảo hành';
                break;
            }
            case '1':
            {
                $name = 'Bảo hành xong';
                break;
            }
        }
        return $name;
    }
}

if (!function_exists('cms_getInventory')) {
    function cms_getInventory($product_id, $store_id)
    {
        $name = 0;
        $CI =& get_instance();
        $group = $CI->db->from('inventory')->where('product_id', $product_id)->where('store_id', $store_id)->get()->row_array();
        if (isset($group) && count($group)) {
            $name = $group['quantity'];
        }

        return $name;
    }
}

if (!function_exists('cms_finding_productbyID')) {
    function cms_finding_productbyID($id)
    {
        $CI =& get_instance();
        $product = $CI->db
            ->select('products.*,prd_unit_name')
            ->where('products.ID', $id)
            ->from('products')
            ->join('products_unit', 'products_unit.ID=products.prd_unit_id', 'LEFT')
            ->get()
            ->row_array();
        return $product;
    }
}

if (!function_exists('cms_finding_customerbyID')) {
    function cms_finding_customerbyID($id)
    {
        $CI =& get_instance();
        $customer = $CI->db
            ->where('ID', $id)
            ->from('customers')
            ->get()
            ->row_array();
        if (isset($customer) && count($customer))
            return $customer;
        else
            return null;
    }
}

if (!function_exists('cms_getNameAuthbyID')) {
    function cms_getNameAuthbyID($id)
    {
        $name = "Không nhập";
        $CI =& get_instance();
        $customer = $CI->db->select('display_name')->from('users')->where('id', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['display_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_getUserNameAuthbyID')) {
    function cms_getUserNameAuthbyID($id)
    {
        $name = "Không nhập";
        $CI =& get_instance();
        $customer = $CI->db->select('username')->from('users')->where('id', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['username'];
        }

        return $name;
    }
}

if (!function_exists('cms_getNamestockbyID')) {

    function cms_getNamestockbyID($id)
    {
        $name = "không xác định";
        $CI =& get_instance();
        $customer = $CI->db->select('store_name')->from('stores')->where('ID', $id)->get()->row_array();
        if (isset($customer) && count($customer)) {
            $name = $customer['store_name'];
        }

        return $name;
    }
}

if (!function_exists('cms_encode_currency_format')) {
    function cms_encode_currency_format($priceFloat)
    {
        $symbol_thousand = ',';
        $decimal_place = 0;
        if ($priceFloat == '')
            return $priceFloat;

        if ($priceFloat == 0)
            return 0;

        return number_format($priceFloat, $decimal_place, '', $symbol_thousand);
    }
}

if (!function_exists('filter_mark_search')) {
    function filter_mark_search($str)
    {
        $str = preg_replace("/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/", 'a', $str);
        $str = preg_replace("/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/", 'e', $str);
        $str = preg_replace("/(ì|í|ị|ỉ|ĩ)/", 'i', $str);
        $str = preg_replace("/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/", 'o', $str);
        $str = preg_replace("/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/", 'u', $str);
        $str = preg_replace("/(ỳ|ý|ỵ|ỷ|ỹ)/", 'y', $str);
        $str = preg_replace("/(đ)/", 'd', $str);
        $str = preg_replace("/(À|Á|Ạ|Ả|Ã|Â|Ầ|Ấ|Ậ|Ẩ|Ẫ|Ă|Ằ|Ắ|Ặ|Ẳ|Ẵ)/", 'A', $str);
        $str = preg_replace("/(È|É|Ẹ|Ẻ|Ẽ|Ê|Ề|Ế|Ệ|Ể|Ễ)/", 'E', $str);
        $str = preg_replace("/(Ì|Í|Ị|Ỉ|Ĩ)/", 'I', $str);
        $str = preg_replace("/(Ò|Ó|Ọ|Ỏ|Õ|Ô|Ồ|Ố|Ộ|Ổ|Ỗ|Ơ|Ờ|Ớ|Ợ|Ở|Ỡ)/", 'O', $str);
        $str = preg_replace("/(Ù|Ú|Ụ|Ủ|Ũ|Ư|Ừ|Ứ|Ự|Ử|Ữ)/", 'U', $str);
        $str = preg_replace("/(Ỳ|Ý|Ỵ|Ỷ|Ỹ)/", 'Y', $str);
        $str = preg_replace("/(Đ)/", 'D', $str);
        $str = preg_replace("/(\?|\(|\)|\^)/", '', $str);
//        $str = ereg_replace("[^A-Za-z0-9 ]", "", $str);
        $str = trim(strtolower($str));
        return $str;
    }
}
