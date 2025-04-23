<div class="alert alert-dange ajax-error" role="alert"><span
            style="font-weight: bold; font-size: 18px;">Thông báo!</span><br>

    <div class="ajax-error-ct"></div>
</div>
<div class="alert ajax-success" role="alert"
     style="width: 350px;background: rgba(92,130,79,0.9); display:none; color: #fff;"><span
            style="font-weight: bold; font-size: 18px;">Thông báo!</span>
    <br>

    <div class="ajax-success-ct"></div>
</div>
<!-- Start create employee -->
<div class="modal fade" id="create-nv" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="text-transform: uppercase;"><i class="fa fa-user"></i>
                    Thêm tài khoản đăng nhập </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="frm-cruser">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="tennhanvien">Tên nhân viên</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="display_name" name="display_name" class="form-control" value=""
                                   placeholder="Nhập tên nhân viên">
                            <span style="color: red; font-style: italic;" class="error error-display_name"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="manv">Mã nhân viên</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="manv" name="manv" class="form-control" value=""
                                   placeholder="Nhập mã nhân viên">
                            <span style="color: red; font-style: italic;" class="error error-manv"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="manv">Email</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="mail" name="email" class="form-control" value=""
                                   placeholder="Nhập email của bạn">
                            <span style="color: red; font-style: italic;" class="error error-mail"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="commission">Hệ số thưởng</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="commission" name="commission" class="form-control txtNumber" value=""
                                   placeholder="Nhập % hoa hồng ( hệ số thưởng)">
                            <span style="color: red; font-style: italic;" class="error error-commission"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="manv">Mật khẩu</label>
                        </div>
                        <div class="col-md-9">
                            <input type="password" id="password" name="password" class="form-control" value=""
                                   placeholder="Nhập Mật khẩu">
                            <span style="color: red; font-style: italic;" class="error error-password"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-3 padd-right-0">
                            <label for="manv">Nhóm người dùng</label>
                        </div>
                        <div class="col-md-9">
                            <div class="group-user">
                                <div class="group-selbox">
                                    <select name="group" id="sel-group" class="form-control">
                                        <?php
                                        if (isset($data['group']))
                                            foreach ((array)$data['group'] as $group) { ?>
                                                <option value="<?php echo $group['id'] ?>"><?php echo $group['group_name'] ?></option>
                                            <?php } ?>
                                    </select>
                                </div>
                                <span style="color: red; font-style: italic;" class="error error-group"></span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="stock">Kho làm việc</label>
                        </div>
                        <div class="col-md-9">
                            <div class="stock-selbox"></div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm btn-crnv" onclick="cms_cruser()"><i
                            class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>
<!-- end create employee -->

<!-- Start create function -->
<div class="modal fade" id="create-func" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="text-transform: uppercase;"><i class="fa fa-user"></i>
                    Thêm Chức năng </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="tennhanvien">URL</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="permisstion_url" name="permisstion_url" class="form-control" value=""
                                   placeholder="Nhập url cho phép của chức năng">
                            <span style="color: red; font-style: italic;" class="error error-permisstion_url"></span>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="manv">Tên chức năng</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="permisstion_name" name="permisstion_name" class="form-control"
                                   value="" placeholder="Nhập tên chức năng">
                            <span style="color: red; font-style: italic;" class="error error-permisstion_name"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm btn-crfunc"><i class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="text-transform: uppercase;"><i class="fa fa-user"></i>
                    Thêm nhóm người dùng </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="group-name">Tên Nhóm</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="group-name" name="group_name" class="form-control" value=""
                                   placeholder="Nhập tên nhóm người dùng">
                            <span style="color: red; font-style: italic;" class="error error-group_name"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm btn-crgroup"><i class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="create-store" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel" style="text-transform: uppercase;"><i class="fa fa-user"></i>
                    Thêm kho </h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-3">
                            <label for="group-name">Tên Kho</label>
                        </div>
                        <div class="col-md-9">
                            <input type="text" id="store-name" name="store_name" class="form-control" value=""
                                   placeholder="Nhập tên kho">
                            <span style="color: red; font-style: italic;" class="error error-store_name"></span>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm" onclick="cms_crstore();"><i
                            class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- start create customer -->

<div class="modal fade" id="create-cust" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tạo mới khách hàng</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="frm-crcust">
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_group">Nhóm KH</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="customer_group" class="form-control">
                                <option value="0">Khách lẻ</option>
                                <option value="1">Khách sỉ</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_code">Mã KH</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_code" name="customer_code" class="form-control" value=""
                                   placeholder="Mã khách hàng(tự sinh nếu bỏ trống)">
                            <span style="color: red; font-style: italic;" class="error error-customer_code"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_name">Tên KH</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_name" name="customer_name" class="form-control" value=""
                                   placeholder="Nhập tên khách hàng (bắt buộc)">
                            <span style="color: red; font-style: italic;" class="error error-customer_name"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_phone">Điện thoại</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_phone" name="customer_phone"
                                   class="form-control" value="" placeholder="Nhập số điện thoại">
                            <span style="color: red; font-style: italic;" class="error error-customer_phone"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_email">Email</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_email" name="customer_email" class="form-control"
                                   value=""
                                   placeholder="Nhập email khách hàng ( ví dụ: kh10@gmail.com )">
                            <span style="color: red; font-style: italic;" class="error error-customer_email"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_tax">Mã số thuế</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_tax" name="customer_tax" class="form-control" value=""
                                   placeholder="Nhập mã số thuế khách hàng">
                            <span style="color: red; font-style: italic;" class="error error-customer_tax"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_province_modal">Tỉnh/Tp</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="customer_province_modal" onchange="cms_change_province()"
                                    style="width: 100% !important;"
                                    class="form-control province">
                                <option value="-1">Chọn tỉnh/ thành phố</option>
                                <?php $province = cms_getListProvince();
                                foreach ((array)$province as $item) {
                                    ?>
                                    <option value="<?php echo $item['ID'] ?>"><?php echo $item['province_name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_district_modal">Quận/Huyện</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="customer_district_modal" onchange="cms_change_district()"
                                    style="width: 100% !important;"
                                    class="form-control district">
                                <option value="0">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_ward_modal">Phường/Xã</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="customer_ward_modal" style="width: 100% !important;"
                                    class="form-control ward">
                                <option value="0">Chọn Xã/Phường</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_addr">Địa chỉ</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_addr" name="customer_addr" class="form-control"
                                   value="" placeholder="Nhập địa chỉ">
                            <span style="color: red; font-style: italic;" class="error error-customer_addr"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_addr">Bản đồ</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_map" name="customer_map" class="form-control"
                                   value="" placeholder="Nhập bản đồ">
                            <span style="color: red; font-style: italic;" class="error error-customer_addr"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_notes">Ghi chú</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_notes" name="customer_notes" class="form-control"
                                   value=""
                                   placeholder="Nhập ghi chú">
                            <span style="color: red; font-style: italic;" class="error error-customer_notes"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_birthday">Ngày sinh</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="customer_birthday" name="customer_birthday"
                                   class="form-control txttimes" value="" placeholder="yyyy-mm-dd">
                            <span style="color: red;" class="error error-customer_birthday"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_gender">Giới tính</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="radio" name="gender" checked class="customer_gender" value="0"> Nam
                            <input type="radio" name="gender" class="customer_gender" value="1"> Nữ
                            <span style="color: red; font-style: italic;"
                                  class="error error-customer_gender"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="customer_gender">Nợ</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="is_customer_debt" name="is_customer_debt"
                                   class="txtMoney form-control" value=""
                                   placeholder="Khách hàng đang nợ">
                            <span style="color: red; font-style: italic;" class="error error-customer_debt"></span>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12" style="margin-top: 18px;">
                        <div class="jumbotron text-center" id="img_upload"
                             style="border-radius: 0; margin-bottom: 10px; padding: 5px 20px;">
                            <h4>Upload hình ảnh</h4>

                            <center>
                                <div id='customer_img_preview' style="display: none;"></div>
                                <form id="customer_image_upload_form" method="post" enctype="multipart/form-data"
                                      action='product/upload_img' autocomplete="off">
                                    <div class="file_input_container">
                                        <div class="upload_button"><input type="file" name="photo"
                                                                          id="customer_photo"
                                                                          class="file_input"/></div>
                                    </div>

                                </form>
                            </center>

                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm btn-crcust" onclick="cms_crCustomer();"><i
                            class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>

<!-- end customer -->

<!-- start create supplier -->

<div class="modal fade" id="create-sup" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document" style="width: 90%">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Tạo mới nhà cung cấp</h4>
            </div>
            <div class="modal-body">
                <div class="form-horizontal" id="frm-crsup">

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_name">Mã nhà cung cấp</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_code" name="supplier_code" class="form-control" value=""
                                   placeholder="Mã nhà cung cấp (Tự sinh nếu bỏ trống)">
                            <span style="color: red; font-style: italic;" class="error error-supplier_code"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_name">Tên nhà cung cấp</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_name" name="supplier_name" class="form-control" value=""
                                   placeholder="Nhập tên nhà cung cấp (bắt buộc)">
                            <span style="color: red; font-style: italic;" class="error error-supplier_name"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_phone">Điện thoại</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_phone" name="supplier_phone" class="form-control"
                                   value="" placeholder="Nhập số điện thoại">
                            <span style="color: red; font-style: italic;" class="error error-supplier_phone"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_email">Email</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_email" name="supplier_email" class="form-control" value=""
                                   placeholder="Nhập email nhà cung cấp ( ví dụ: kh10@gmail.com )">
                            <span style="color: red; font-style: italic;" class="error error-supplier_email"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_tax">Mã số thuế</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_tax" name="supplier_tax" class="form-control" value=""
                                   placeholder="Nhập mã số thuế">
                            <span style="color: red; font-style: italic;" class="error error-supplier_tax"></span>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_province_modal">Tỉnh/Tp</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="supplier_province_modal" onchange="cms_s_change_province()" style="width: 100%"
                                    class="form-control province">
                                <option value="-1">Chọn tỉnh/ thành phố</option>
                                <?php $province = cms_getListProvince();
                                foreach ((array)$province as $item) {
                                    ?>
                                    <option value="<?php echo $item['ID'] ?>"><?php echo $item['province_name'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_district_modal">Quận/Huyện</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="supplier_district_modal" onchange="cms_s_change_district()" style="width: 100%"
                                    class="form-control district">
                                <option value="0">Chọn Quận/Huyện</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_ward_modal">Phường/Xã</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <select id="supplier_ward_modal" style="width: 100%"
                                    class="form-control ward">
                                <option value="0">Chọn Xã/Phường</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_addr">Địa chỉ</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_addr" name="supplier_addr" class="form-control"
                                   value="" placeholder="Nhập địa chỉ">
                            <span style="color: red; font-style: italic;" class="error error-supplier_addr"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="supplier_notes">Ghi chú</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="supplier_notes" name="notes" class="form-control" value=""
                                   placeholder="Nhập ghi chú">
                            <span style="color: red; font-style: italic;" class="error error-supplier_notes"></span>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 padd-0" style="margin-top: 18px;">
                        <div class="col-md-3 col-xs-5">
                            <label for="is_supplier_debt">Nợ</label>
                        </div>
                        <div class="col-md-9 col-xs-7 padd-0">
                            <input type="text" id="is_supplier_debt" name="is_supplier_debt"
                                   class="form-control txtMoney" value=""
                                   placeholder="Đang nợ nhà cung cấp">
                            <span style="color: red; font-style: italic;" class="error error-supplier_debt"></span>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12" style="margin-top: 18px;">
                        <div class="jumbotron text-center" id="img_upload"
                             style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                            <h3>Upload hình ảnh</h3>
                            <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Kích thướt
                                tối đa 10MB)
                            </small>
                            <p>
                                <center>
                            <div id='supplier_img_preview' style="display: none;"></div>
                            <form id="supplier_image_upload_form" method="post" enctype="multipart/form-data"
                                  action='product/upload_img' autocomplete="off">
                                <div class="file_input_container">
                                    <div class="upload_button"><input type="file" name="photo"
                                                                      id="supplier_photo"
                                                                      class="file_input"/></div>
                                </div>

                            </form>
                            </center>
                            </p>
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-primary btn-sm btn-crsup" onclick="cms_crsup();"><i
                            class="fa fa-check"></i> Lưu
                </button>
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Bỏ qua
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="list-prd-manufacture" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Quản lý nhà sản xuất</h4>
            </div>
            <div class="modal-body">
                <div class="tabtable">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs tab-setting" role="tablist"
                        style="background-color: #EFF3F8; padding: 5px 0 0 15px;">
                        <li role="presentation" class="active list-manufacture-click" style="margin-right: 3px;"><a
                                    href="#list-manufacture" aria-controls="list-manufacture" role="tab"
                                    data-toggle="tab"><i class="fa fa-list"></i> Danh sách Nhà sản xuất</a></li>
                        <li role="presentation"><a href="#create-manufacture" aria-controls="create-manufacture"
                                                   role="tab" data-toggle="tab"><i class="fa fa-plus"></i> Tạo mới nhà
                                sản xuất
                            </a></li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" style="padding:10px; border: 1px solid #ddd; border-top: none;">
                        <div role="tabpanel" class="tab-pane active" id="list-manufacture">
                            <div class="prd_manufacture-body">

                            </div>
                        </div>

                        <!-- Tab Function -->
                        <div role="tabpanel" class="tab-pane" id="create-manufacture">
                            <div class="row form-horizontal">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-7">
                                            <input type="text" style="border-radius: 0 !important;" class="form-control"
                                                   id="prd_manuf_name" value="" placeholder="Nhập tên Nhà sản xuất">
                                        </div>
                                        <div class="input-groups-btn col-md-5">
                                            <button type="button" class="save btn btn-primary"
                                                    style="border-radius: 0 3px 3px 0;"
                                                    onclick="cms_create_manufacture(1);"><i class="fa fa-check"></i> Lưu
                                            </button>
                                            <button type="button" class="save btn btn-primary"
                                                    onclick="cms_create_manufacture(0);"><i
                                                        class="fa fa-floppy-o"></i> Lưu và tiếp tục
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="list-prd-unit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Quản lý đơn vị tính</h4>
            </div>
            <div class="modal-body">
                <div class="tabtable">
                    <ul class="nav nav-tabs tab-setting" role="tablist"
                        style="background-color: #EFF3F8; padding: 5px 0 0 15px;">
                        <li role="presentation" class="active list-unit-click" style="margin-right: 3px;"><a
                                    href="#list-unit" aria-controls="list-unit" role="tab"
                                    data-toggle="tab"><i class="fa fa-list"></i> Danh sách đơn vị tính</a></li>
                        <li role="presentation"><a href="#create-unit" aria-controls="create-unit"
                                                   role="tab" data-toggle="tab"><i class="fa fa-plus"></i> Tạo mới đơn
                                vị tính</a></li>
                    </ul>
                    <div class="tab-content" style="padding:10px; border: 1px solid #ddd; border-top: none;">
                        <div role="tabpanel" class="tab-pane active" id="list-unit">
                            <div class="prd_unit-body">
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="create-unit">
                            <div class="row form-horizontal">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-7">
                                            <input type="text" style="border-radius: 0 !important;" class="form-control"
                                                   id="prd_unit_name" value="" placeholder="Nhập tên đơn vị tính">
                                        </div>
                                        <div class="input-groups-btn col-md-5">
                                            <button type="button" class="save btn btn-primary"
                                                    style="border-radius: 0 3px 3px 0;"
                                                    onclick="cms_create_unit(1);"><i class="fa fa-check"></i> Lưu
                                            </button>
                                            <button type="button" class="save btn btn-primary"
                                                    onclick="cms_create_unit(0);"><i
                                                        class="fa fa-floppy-o"></i> Lưu và tiếp tục
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="list-prd-group" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Quản lý danh mục</h4>
            </div>
            <div class="modal-body">
                <div class="tabtable">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs tab-setting" role="tablist"
                        style="background-color: #EFF3F8; padding: 5px 0 0 15px;">
                        <li role="presentation" class="active" style="margin-right: 3px;"><a href="#list-groups"
                                                                                             aria-controls="list-group"
                                                                                             role="tab"
                                                                                             data-toggle="tab"><i
                                        class="fa fa-list"></i> Danh sách danh mục</a></li>
                        <li role="presentation"><a href="#create-groups" aria-controls="create-group" role="tab"
                                                   data-toggle="tab"><i class="fa fa-plus"></i> Tạo mới danh mục</a>
                        </li>
                    </ul>

                    <!-- Tab panes -->
                    <div class="tab-content" style="padding:10px; border: 1px solid #ddd; border-top: none;">
                        <div role="tabpanel" class="tab-pane active" id="list-groups">
                            <div class="prd_group-body">
                                <div class="text-center"><img src="public/templates/images/balls.gif"/></div>
                            </div>
                        </div>

                        <!-- Tab Function -->
                        <div role="tabpanel" class="tab-pane" id="create-groups">
                            <div class="row form-horizontal">
                                <div class="col-md-12 col-xs-12">
                                    <div class="form-group">
                                        <div class="col-md-4 text-right">
                                            <label>Tên danh mục</label>
                                        </div>
                                        <div class="col-md-8">
                                            <input type="text" id="prd_group_name" class="form-control"
                                                   placeholder="Nhập tên danh mục.">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-4 text-right">
                                            <label>Danh mục cấp cha</label>
                                        </div>
                                        <div class="col-md-8">
                                            <select id="parentid" class="form-control">

                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="col-md-8 col-md-offset-4">
                                            <button type="button" class="save btn btn-primary"
                                                    style="border-radius: 0 3px 3px 0;" onclick="cms_create_group(1);">
                                                <i
                                                        class="fa fa-check"></i> Lưu
                                            </button>
                                            <button type="button" class="save btn btn-primary"
                                                    onclick="cms_create_group(0);"><i class="fa fa-floppy-o"></i> Lưu
                                                và tiếp tục
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-default btn-sm btn-close" data-dismiss="modal"><i
                            class="fa fa-undo"></i> Đóng
                </button>
            </div>
        </div>
    </div>
</div>
