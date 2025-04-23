<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="customer-act act">
            <div class="col-md-4 col-xs-6 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Thông tin NCC</h2>
                </div>
            </div>
            <div class="col-md-6 col-xs-6">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="save btn btn-primary btn-hide-edit"
                                onclick="cms_edit_cusitem( 'sup')">
                            <i class="fa fa-pencil-square-o"></i> sửa
                        </button>
                        <button type="button" class="save btn btn-default btn-hide-edit"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i>Thoát
                        </button>
                        <button type="button" class="save btn btn-primary btn-show-edit" style="display:none;"
                                onclick="cms_save_edit_sup()"><i class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-default btn-show-edit" style="display:none;"
                                onclick="cms_undo_cusitem('sup')"><i class="fa fa-undo"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="supplier-info col-md-12 col-xs-12">
    <?php if (isset($_list_sup) && count($_list_sup)) : ?>
        <div id="item-<?php echo $_list_sup['ID']; ?>" class="supplier-inner tr-item-sup">
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Tên NCC</label>
                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_sup['supplier_name']; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã NCC</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_sup['supplier_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Điện thoại</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_sup['supplier_phone'] != '') ? $_list_sup['supplier_phone'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Email</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_sup['supplier_email'] != '') ? $_list_sup['supplier_email'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã số thuế</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_sup['supplier_tax'] != '') ? $_list_sup['supplier_tax'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Địa chỉ</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_sup['supplier_addr'] . cms_getFullAddress($_list_sup['ward_id'], $_list_sup['district_id'], $_list_sup['province_id']); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ghi chú</label>

                        <div class="col-md-8 col-xs-6">
                            <?php echo ($_list_sup['notes'] != '') ? $_list_sup['notes'] : '(chưa có)'; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="supplier-inner tr-edit-item-sup" style="display: none;">
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Tên NCC</label>
                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="supplier_name" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'supplier_name'); ?>">
                            <span style="color: red;" class="error error-supplier_name"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã NCC</label>
                        <div class="col-md-8 col-xs-6">
                            <?php echo $_list_sup['supplier_code']; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Điện thoại</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="supplier_phone" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'supplier_phone'); ?>">
                            <span style="color: red;" class="error error-supplier_phone"></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Email</label>
                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="supplier_email" class="form-control"
                                   value="<?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'supplier_email'); ?>">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Mã số thuế</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                            <input type="text" id="supplier_tax" class="form-control"
                                   value=" <?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'supplier_tax'); ?>">
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Địa chỉ</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                                                            <textarea id="supplier_addr"
                                                                      class="form-control"><?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'supplier_addr'); ?></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 col-xs-12 padd-0">
                <div class="col-md-6 col-xs-12 padd-0" style="margin-bottom: 10px;">
                    <div class="form-group">
                        <label class="col-md-4 col-xs-6 padding-5">Ghi chú</label>

                        <div class="col-md-8 col-xs-6 padd-0">
                                                            <textarea id="notes"
                                                                      class="form-control"><?php echo cms_common_input(isset($_list_sup) ? $_list_sup : [], 'notes'); ?></textarea>
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
                    <div id='edit_supplier_img_preview' style="display: none;"></div>
                    <form id="edit_supplier_image_upload_form" method="post" enctype="multipart/form-data"
                          action='product/upload_img' autocomplete="off">
                        <div class="file_input_container">
                            <div class="upload_button"><input type="file" name="photo"
                                                              id="edit_supplier_photo"
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

<div class="inputs-main-body">
</div>
<script>cms_paging_input_by_supplier_id(1);</script>
