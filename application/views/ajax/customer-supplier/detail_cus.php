<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="customer-act act">
            <div class="col-md-4 col-xs-6 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Thông tin KH</h2>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="save btn btn-primary btn-hide-edit"
                                onclick="cms_edit_cusitem('customer')"><i class="fa fa-pencil-square-o"></i> sửa
                        </button>
                        <button type="button" class="save btn btn-default btn-hide-edit"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i>Thoát
                        </button>
                        <button type="button" class="save btn btn-primary btn-show-edit" style="display:none;"
                                onclick="cms_save_edit_customer()"><i class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-default btn-show-edit" style="display:none;"
                                onclick="cms_undo_cusitem('customer')"><i class="fa fa-undo"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="customer-info col-md-12">
    <?php if (isset($_list_cus) && count($_list_cus)) : ?>
        <div class="customer-inner tr-item-customer" id="item-<?php echo $_list_cus['ID']; ?>">
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Tên khách hàng</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_name']; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã khách hàng</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Điện thoại</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_cus['customer_phone'] != '') ? $_list_cus['customer_phone'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Email/Facebook</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_cus['customer_email'] != '') ? $_list_cus['customer_email'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Giới tính</label>

                        <div class="col-md-8 col-xs-6">
                            <input type="radio" disabled
                                   name="gender" <?php echo ($_list_cus['customer_gender'] == '0') ? 'checked' : ''; ?>>Nam
                            &nbsp;
                            <input type="radio" disabled
                                   name="gender" <?php echo ($_list_cus['customer_gender'] == '1') ? 'checked' : ''; ?>>
                            Nữ
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ngày sinh</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_cus['customer_birthday'] != '1970-01-01 07:00:00') ? $_list_cus['customer_birthday'] : '(chưa có)'; ?>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Nhóm khách hàng</label>
                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_group'] == '0' ? 'Khách lẻ' : 'Khách sỉ'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ghi chú</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_cus['notes'] != '') ? $_list_cus['notes'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Địa chỉ</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_addr'] . cms_getFullAddress($_list_cus['ward_id'], $_list_cus['district_id'], $_list_cus['province_id']); ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Bản đồ</label>

                        <div class="col-md-8 col-xs-6">
                            <?php if ($_list_cus['customer_map'] != '') {
                                ?>
                                <a style="color: #0B87C9" class="href" target="_blank"
                                   href="<?php echo $_list_cus['customer_map']; ?>">Bản đồ</a>
                                <?php
                            } ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã số thuế</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_tax']; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div class="customer-inner tr-edit-item-customer" style="display: none;">
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Tên khách hàng</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="customer_name" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_name'); ?>">
                            <span style="color: red;" class="error error-customer_name"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã khách hàng</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_cus['customer_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Điện thoại</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="customer_phone" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_phone'); ?>">
                            <span style="color: red;" class="error error-customer_phone"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Email</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="customer_email" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_email'); ?>">
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Giới tính</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="radio" class="customer_gender"
                                   name="gender1" <?php echo ($_list_cus['customer_gender'] == '0') ? 'checked' : ''; ?>
                                   value="0">Nam &nbsp;
                            <input type="radio" class="customer_gender"
                                   name="gender1" <?php echo ($_list_cus['customer_gender'] == '1') ? 'checked' : ''; ?>
                                   value="1"> Nữ
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ngày sinh</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" class="customer_birthday" id="customer_birthday"
                                   class="txttimes form-control"
                                   value=" <?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_birthday'); ?>">
                        </div>
                        <script>
                            $('.customer_birthday').datetimepicker({
                                timepicker: false,
                                autoclose: true,
                                format: 'Y/m/d',
                                formatDate: 'Y/m/d'
                            });</script>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Nhóm khách hàng</label>
                        <div class="col-md-8 col-xs-6 padd-0">
                            <select id="d_customer_group" class="form-control">
                                <option <?php if ($_list_cus['customer_group'] == 0) echo 'selected' ?> value="0">Khách
                                    lẻ
                                </option>
                                <option <?php if ($_list_cus['customer_group'] == 1) echo 'selected' ?> value="1">Khách
                                    sỉ
                                </option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ghi chú</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                                                            <textarea id="notes"
                                                                      class="form-control"><?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'notes'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Địa chỉ</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                                                            <textarea id="customer_addr"
                                                                      class="form-control"><?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_addr'); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Bản đồ</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                                                            <textarea id="customer_map"
                                                                      class="form-control"><?php echo $_list_cus['customer_map']; ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã số thuế</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="customer_tax" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_cus) ? $_list_cus : [], 'customer_tax'); ?>">
                        </div>
                    </div>
                </div>

            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="jumbotron text-center" id="img_upload"
                     style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                    <h3>Upload hình ảnh</h3>
                    <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Kích thướt tối đa 10MB)
                    </small>
                    <p>
                        <center>
                    <div id='edit_customer_img_preview' style="display: none;"></div>
                    <form id="edit_customer_image_upload_form" method="post" enctype="multipart/form-data"
                          action='product/upload_img' autocomplete="off">
                        <div class="file_input_container">
                            <div class="upload_button"><input type="file" name="photo" id="edit_customer_photo"
                                                              class="file_input"/></div>
                        </div>

                    </form>
                    </center>
                    </p>
                </div>
            </div>
        </div>
    <?php else:
    endif;
    ?>
</div>

<div class="orders-main-body">
</div>
<script>cms_paging_order_by_customer_id(1);</script>
