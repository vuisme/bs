<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

ini_set('max_execution_time', '0');
ini_set('memory_limit', '512M');

class Update extends CI_Controller
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
        else {
            if (!$this->db->table_exists('adjust')) {

                $this->load->dbforge();

                $fields = array(
                    'ID' => array(
                        'type' => 'INT',
                        'constraint' => 10,
                        'unsigned' => TRUE,
                        'null' => FALSE,
                        'auto_increment' => TRUE
                    ),
                    'adjust_code' => array(
                        'type' => 'varchar',
                        'constraint' => 11,
                        'null' => TRUE,
                    ),
                    'store_id' => array(
                        'type' => 'int',
                        'constraint' => 11,
                        'default' => 0,
                    ),
                    'notes' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ),
                    'total_different' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'total_quantity' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'detail_adjust' => array(
                        'type' => 'longtext',
                        'null' => TRUE,
                    ),
                    'adjust_date' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'adjust_status' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 1,
                    ),
                    'deleted' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'created' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'updated' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'user_init' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'user_upd' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                );

                $this->dbforge->add_key('ID', TRUE);

                $this->dbforge->add_field($fields);

                $this->dbforge->create_table('adjust', true);
            }

            if (!$this->db->field_exists('token', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'token' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('qrcode', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'qrcode' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('from_date', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'from_date' => array('type' => 'datetime', 'null' => true)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('prd_manuf_id', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'prd_manuf_id' => array('type' => 'int', 'constraint' => '13', 'default' => 0)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('returned', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'returned' => array('type' => 'tinyint', 'constraint' => '1', 'default' => 0)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('note', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'note' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('name', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'name' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('phone', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'phone' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('email', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'email' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('addr', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'addr' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('fix_code', 'fix_detail')) {
                $this->load->dbforge();
                $fields = array(
                    'fix_code' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('fix_detail', $fields);
            }

            if (!$this->db->field_exists('token', 'users')) {
                $this->load->dbforge();
                $fields = array(
                    'token' => array('type' => 'text', 'default' => null)
                );
                $this->dbforge->add_column('users', $fields);
            }

            if (!$this->db->field_exists('total_discount', 'orders')) {
                $this->load->dbforge();
                $fields = array(
                    'total_discount' => array('type' => 'int', 'constraint' => '13', 'default' => 0)
                );
                $this->dbforge->add_column('orders', $fields);
            }

            if (!$this->db->field_exists('prd_warranty', 'products')) {
                $this->load->dbforge();
                $fields = array(
                    'prd_warranty' => array('type' => 'int', 'constraint' => '13', 'default' => 0)
                );
                $this->dbforge->add_column('products', $fields);
            }

            if (!$this->db->field_exists('report_serial', 'report')) {
                $this->load->dbforge();
                $fields = array(
                    'report_serial' => array('type' => 'text', 'null' => true)
                );
                $this->dbforge->add_column('report', $fields);
            }

            $check = $this->db->from('templates')->where('ID', 7)->count_all_results();
            if ($check == 0) {
                $permissions = array();
                $permissions['id'] = 7;
                $permissions['type'] = 7;
                $permissions['name'] = 'In xác nhận công nợ khách hàng';
                $permissions['content'] = '<div style="text-align:center">
<table style="width:100%">
	<tbody>
		<tr>
			<td colspan="2" style="text-align:center"><img alt="" src="public/templates/images/logo.jpg" style="float:left; height:100px; width:100px" /></td>
		</tr>
		<tr>
			<td style="text-align:left">MESA CẦN THƠ</td>
			<td style="text-align:right">printed date: {Ngay_In}</td>
		</tr>
		<tr>
			<td style="text-align:left">166 CMT8, P.B&Ugrave;I HỮU NGHĨA, QUẬN B&Igrave;NH THỦY, TPCT</td>
			<td style="text-align:right">user: {Nguoi_In}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><strong><span style="font-size:24px">PHIẾU X&Aacute;C NHẬN C&Ocirc;NG NỢ KHÁCH HÀNG</span></strong></td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Kh&aacute;ch h&agrave;ng:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{Khach_Hang}</td>
			<td style="text-align:left">Mã khách hàng: {Ma_Khach_Hang}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Địa chỉ:&nbsp; &nbsp; {DC_Khach_Hang}</td>
			<td style="text-align:left">
			<p>SĐT:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {DT_Khach_Hang}</p>
			</td>
		</tr>
	</tbody>
</table>
</div>

<div><span style="font-size:12px">{Chi_Tiet_San_Pham}</span></div>

<div>&nbsp;</div>

<div>Tổng tiền h&agrave;ng phải trả: {Tong_Cong_No}</div>

<div>Bằng chữ: {So_Tien_Bang_Chu}</div>

<div>&nbsp;</div>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td style="text-align:center">
			<p><strong>Kế to&aacute;n</strong></p>

			<p><em><span style="font-size:11px">(K&yacute; ghi r&otilde; họ &amp; t&ecirc;n)</span></em></p>
			</td>
			<td style="text-align:center">
			<p><strong>Kh&aacute;ch h&agrave;ng</strong></p>

			<p><em><span style="font-size:11px">{k&yacute; ghi r&otilde; họ &amp; t&ecirc;n}</span></em></p>
			</td>
		</tr>
		<tr>
			<td>
			<p>&nbsp;</p>
			</td>
			<td>
			<p>&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>
';
                $this->db->insert('templates', $permissions);
            }

            $check = $this->db->from('templates')->where('ID', 8)->count_all_results();
            if ($check == 0) {
                $permissions = array();
                $permissions['id'] = 8;
                $permissions['type'] = 8;
                $permissions['name'] = 'In xác nhận công nợ nhà cung cáp';
                $permissions['content'] = '<div style="text-align:center">
<table style="width:100%">
	<tbody>
		<tr>
			<td colspan="2" style="text-align:center"><img alt="" src="public/templates/images/logo.jpg" style="float:left; height:100px; width:100px" /></td>
		</tr>
		<tr>
			<td style="text-align:left">MESA CẦN THƠ</td>
			<td style="text-align:right">printed date: {Ngay_In}</td>
		</tr>
		<tr>
			<td style="text-align:left">166 CMT8, P.B&Ugrave;I HỮU NGHĨA, QUẬN B&Igrave;NH THỦY, TPCT</td>
			<td style="text-align:right">user: {Nguoi_In}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><strong><span style="font-size:24px">PHIẾU X&Aacute;C NHẬN C&Ocirc;NG NỢ NHÀ CUNG C&Acirc;́P</span></strong></td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Nhà cung c&acirc;́p:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{Nha_Cung_Cap}</td>
			<td style="text-align:left">Mã nhà cung c&acirc;́p: {Ma_NCC}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Địa chỉ:&nbsp; &nbsp; {DC_NCC}</td>
			<td style="text-align:left">
			<p>SĐT:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {DT_NCC}</p>
			</td>
		</tr>
	</tbody>
</table>
</div>

<div><span style="font-size:12px">{Chi_Tiet_San_Pham}</span></div>

<div>&nbsp;</div>

<div>Tổng tiền h&agrave;ng phải trả: {Tong_Cong_No}</div>

<div>Bằng chữ: {So_Tien_Bang_Chu}</div>

<div>&nbsp;</div>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td style="text-align:center">
			<p><strong>Kế to&aacute;n</strong></p>

			<p><em><span style="font-size:11px">(K&yacute; ghi r&otilde; họ &amp; t&ecirc;n)</span></em></p>
			</td>
			<td style="text-align:center">
			<p><strong>Nhà Cung C&acirc;́p</strong></p>

			<p><em><span style="font-size:11px">{k&yacute; ghi r&otilde; họ &amp; t&ecirc;n}</span></em></p>
			</td>
		</tr>
		<tr>
			<td>
			<p>&nbsp;</p>
			</td>
			<td>
			<p>&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>
';
                $this->db->insert('templates', $permissions);
            }

            $check = $this->db->from('templates')->where('ID', 9)->count_all_results();
            if ($check == 0) {
                $permissions = array();
                $permissions['id'] = 9;
                $permissions['type'] = 9;
                $permissions['name'] = 'Biên nhận bảo hành';
                $permissions['content'] = '<div style="text-align:center">
<table style="width:100%">
	<tbody>
		<tr>
			<td colspan="2" style="text-align:center"><img alt="" src="public/templates/images/logo.jpg" style="float:left; height:100px; width:100px" /></td>
		</tr>
		<tr>
			<td style="text-align:left">MESA CẦN THƠ</td>
			<td style="text-align:right">printed date: {Ngay_In}</td>
		</tr>
		<tr>
			<td style="text-align:left">166 CMT8, P.B&Ugrave;I HỮU NGHĨA, QUẬN B&Igrave;NH THỦY, TPCT</td>
			<td style="text-align:right">user: {Nguoi_In}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><strong><span style="font-size:24px">BI&Ecirc;N NH&Acirc;̣N BẢO HÀNH</span></strong></td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Mã bi&ecirc;n nh&acirc;̣n:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {Ma_Don_Hang}</td>
			<td style="text-align:left">Ng&agrave;y nh&acirc;̣n:&nbsp; &nbsp; &nbsp; &nbsp; {Ngay_Nhan}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Kh&aacute;ch h&agrave;ng:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{Khach_Hang}</td>
			<td style="text-align:left">SĐT:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {DT_Khach_Hang}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Nh&acirc;n vi&ecirc;n nh&acirc;̣n:&nbsp; &nbsp; &nbsp; &nbsp;{Nguoi_Nhan}&nbsp;&nbsp;</td>
			<td style="text-align:left">Chi nh&aacute;nh:&nbsp; &nbsp; &nbsp;{Ten_Cua_Hang}</td>
		</tr>
	</tbody>
</table>
</div>

<div><span style="font-size:12px">{Chi_Tiet_San_Pham}</span></div>

<div>&nbsp;</div>

<div>Ghi chú: {Ghi_Chu}</div>

<div>&nbsp;</div>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td style="text-align:center">
			<p><strong>Nh&acirc;n vi&ecirc;n nh&acirc;̣n</strong></p>

			<p><em><span style="font-size:11px">(K&yacute; ghi r&otilde; họ &amp; t&ecirc;n)</span></em></p>
			</td>
			<td style="text-align:center">
			<p><strong>Kh&aacute;ch h&agrave;ng</strong></p>

			<p><em><span style="font-size:11px">{k&yacute; ghi r&otilde; họ &amp; t&ecirc;n}</span></em></p>
			</td>
		</tr>
		<tr>
			<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</td>
			<td>
			<p>&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>
';
                $this->db->insert('templates', $permissions);
            }

            $check = $this->db->from('templates')->where('ID', 10)->count_all_results();
            if ($check == 0) {
                $permissions = array();
                $permissions['id'] = 10;
                $permissions['type'] = 10;
                $permissions['name'] = 'Phiếu báo giá';
                $permissions['content'] = '<div style="text-align:center">
<table style="width:100%">
	<tbody>
		<tr>
			<td style="text-align:left">MESA CẦN THƠ</td>
			<td style="text-align:right">printed date: {Ngay_In}</td>
		</tr>
		<tr>
			<td style="text-align:left">166 CMT8, P.B&Ugrave;I HỮU NGHĨA, QUẬN B&Igrave;NH THỦY, TPCT</td>
			<td style="text-align:right">user: {Nguoi_In}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align:center"><strong><span style="font-size:24px">PHIẾU GIAO H&Agrave;NG KI&Ecirc;M X&Aacute;C NHẬN C&Ocirc;NG NỢ</span></strong></td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">H&oacute;a đơn số:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {Ma_Don_Hang}</td>
			<td style="text-align:left">Ng&agrave;y HĐ:&nbsp; &nbsp; &nbsp; &nbsp; {Ngay_Xuat}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Kh&aacute;ch h&agrave;ng:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{Khach_Hang}</td>
			<td style="text-align:left">SĐT:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; {DT_Khach_Hang}</td>
		</tr>
		<tr>
			<td colspan="1" style="text-align:left">Nh&acirc;n vi&ecirc;n BH:&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;{NVBH}&nbsp;&nbsp;</td>
			<td style="text-align:left">Chi nh&aacute;nh:&nbsp; &nbsp; &nbsp;{Ten_Cua_Hang}</td>
		</tr>
	</tbody>
</table>
</div>

<div><span style="font-size:12px">{Chi_Tiet_San_Pham5}</span></div>

<div>&nbsp;</div>

<table border="0" cellpadding="0" cellspacing="0" style="width:100%">
	<tbody>
		<tr>
			<td style="text-align:center">
			<p><strong>Nh&acirc;n vi&ecirc;n giao h&agrave;ng</strong></p>

			<p><em><span style="font-size:11px">(K&yacute; ghi r&otilde; họ &amp; t&ecirc;n)</span></em></p>
			</td>
			<td style="text-align:center">
			<p><strong>Kh&aacute;ch h&agrave;ng</strong></p>

			<p><em><span style="font-size:11px">{k&yacute; ghi r&otilde; họ &amp; t&ecirc;n}</span></em></p>
			</td>
		</tr>
		<tr>
			<td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp;</td>
			<td>
			<p>&nbsp;</p>
			</td>
		</tr>
	</tbody>
</table>
';
                $this->db->insert('templates', $permissions);
            }

            if (!$this->db->table_exists('canwarranty')) {

                $this->load->dbforge();

                $fields = array(
                    'ID' => array(
                        'type' => 'INT',
                        'constraint' => 10,
                        'unsigned' => TRUE,
                        'null' => FALSE,
                        'auto_increment' => TRUE
                    ),
                    'store_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'product_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'customer_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'order_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'quantity' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'serial' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ),
                    'price' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'to_date' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'deleted' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'created' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'updated' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'user_init' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'user_upd' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                );

                $this->dbforge->add_key('ID', TRUE);

                $this->dbforge->add_field($fields);

                $this->dbforge->create_table('canwarranty', true);
            }

            if (!$this->db->table_exists('warranty')) {

                $this->load->dbforge();

                $fields = array(
                    'ID' => array(
                        'type' => 'INT',
                        'constraint' => 10,
                        'unsigned' => TRUE,
                        'null' => FALSE,
                        'auto_increment' => TRUE
                    ), 'warranty_code' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ),
                    'store_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'customer_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'total_quantity' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'note' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ), 'status' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'price' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'price_fix' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'deleted' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'created' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'updated' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'user_init' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'user_upd' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                );

                $this->dbforge->add_key('ID', TRUE);

                $this->dbforge->add_field($fields);

                $this->dbforge->create_table('warranty', true);
            }

            if (!$this->db->table_exists('warranty_detail')) {

                $this->load->dbforge();

                $fields = array(
                    'ID' => array(
                        'type' => 'INT',
                        'constraint' => 10,
                        'unsigned' => TRUE,
                        'null' => FALSE,
                        'auto_increment' => TRUE
                    ), 'canwarranty_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'warranty_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'store_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'product_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'customer_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'order_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'quantity' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'serial' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ), 'error' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ), 'error_fix' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    ), 'status' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'price' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'price_fix' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'to_date' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'deleted' => array(
                        'type' => 'tinyint',
                        'constraint' => 1,
                        'default' => 0,
                    ),
                    'created' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'updated' => array(
                        'type' => 'datetime',
                        'null' => TRUE,
                    ),
                    'user_init' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'user_upd' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                );

                $this->dbforge->add_key('ID', TRUE);

                $this->dbforge->add_field($fields);

                $this->dbforge->create_table('warranty_detail', true);
            }

            if (!$this->db->field_exists('supplier_tax', 'suppliers')) {
                $this->load->dbforge();
                $fields = array(
                    'supplier_tax' => array('type' => 'text', 'null' => true)
                );
                $this->dbforge->add_column('suppliers', $fields);
            }

            if (!$this->db->field_exists('customer_tax', 'customers')) {
                $this->load->dbforge();
                $fields = array(
                    'customer_tax' => array('type' => 'text', 'null' => true)
                );
                $this->dbforge->add_column('customers', $fields);
            }

            if (!$this->db->field_exists('customer_debt', 'customers')) {
                $this->load->dbforge();
                $fields = array(
                    'customer_debt' => array('type' => 'int',
                        'constraint' => 13,
                        'default' => 0,)
                );
                $this->dbforge->add_column('customers', $fields);
            }

            if (!$this->db->field_exists('total_money_order', 'customers')) {
                $this->load->dbforge();
                $fields = array(
                    'total_money_order' => array('type' => 'int',
                        'constraint' => 13,
                        'default' => 0,)
                );
                $this->dbforge->add_column('customers', $fields);
            }

            if (!$this->db->field_exists('last_sell_date', 'customers')) {
                $this->load->dbforge();
                $fields = array(
                    'last_sell_date' => array('type' => 'datetime',
                        'null' => TRUE,)
                );
                $this->dbforge->add_column('customers', $fields);
            }

            $list_customer = $this->db->from('customers')->get()->result_array();

            foreach ((array)$list_customer as $customer) {
                cms_updatecustomerdebtbycustomerid($customer['ID']);
            }

            if (!$this->db->table_exists('canreturn_serial')) {

                $this->load->dbforge();

                $fields = array(
                    'ID' => array(
                        'type' => 'INT',
                        'constraint' => 10,
                        'unsigned' => TRUE,
                        'null' => FALSE,
                        'auto_increment' => TRUE
                    ), 'canreturn_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'order_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ), 'input_id' => array(
                        'type' => 'int',
                        'constraint' => 13,
                        'default' => 0,
                    ),
                    'serial' => array(
                        'type' => 'text',
                        'null' => TRUE,
                    )
                );

                $this->dbforge->add_key('ID', TRUE);

                $this->dbforge->add_field($fields);

                $this->dbforge->create_table('canreturn_serial', true);
            }

            $list_repair = $this->db->from('fix_detail')->get()->result_array();
            foreach ((array)$list_repair as $fix) {
                $fix_detail = array();
                $salt = $this->cms_common_string->random(4, true);
                $fix_detail['token'] = $fix['user_init'] . $fix['customer_id'] . $salt;

                $fix_detail['qrcode'] = qrcode('link', CMS_BASE_URL . 'check' . '/x/' . $fix_detail['token']);
                $this->db->where('ID', $fix['ID'])->update('fix_detail', $fix_detail);
            }

            if (!$this->db->field_exists('position', 'products')) {
                $this->load->dbforge();
                $fields = array(
                    'position' => array(
                        'type' => 'text',
                        'default' => '')
                );
                $this->dbforge->add_column('products', $fields);
            }

            if (!$this->db->field_exists('link', 'products')) {
                $this->load->dbforge();
                $fields = array(
                    'link' => array(
                        'type' => 'text',
                        'default' => '')
                );
                $this->dbforge->add_column('products', $fields);
            }

            echo 'update thành công';
        }
    }
}
