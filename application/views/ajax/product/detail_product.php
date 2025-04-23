<?php if (isset($_detail_product) && count($_detail_product))  : ?>
<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="products-act">
            <div class="col-md-4 col-xs-12 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Chi tiết sản phẩm</h2>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 padding-5">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <?php if ($option == 0) {
                            ?>
                            <button type="button" class="save btn btn-primary" style="background-color: #8B8B8B;"
                                    onclick="cms_deactivate_product_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pause"></i> Ngừng KD
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_edit_product(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pencil-square-o"></i> Sửa
                            </button>
                            <button type="button" class="save btn btn-danger"
                                    onclick="cms_delete_product_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-trash-o"></i> Xóa
                            </button>
                            <?php
                        } else if ($option == 1) {
                            ?>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_edit_product(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pencil-square-o"></i> Sửa
                            </button>
                            <button type="button" class="save btn btn-danger"
                                    onclick="cms_restore_product_deactivated_bydetail(<?php echo $_detail_product['ID']; ?>)">
                                <i
                                        class="fa fa-trash-o"></i> Khôi phục
                            </button>
                            <?php
                        } else if ($option == 2) {
                            ?>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_edit_product(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pencil-square-o"></i> Sửa
                            </button>
                            <button type="button" class="save btn btn-danger"
                                    onclick="cms_restore_product_deleted_bydetail(<?php echo $_detail_product['ID']; ?>)">
                                <i
                                        class="fa fa-trash-o"></i> Khôi phục
                            </button>
                            <?php
                        } ?>
                        <button type="button" class="save btn btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i>Thoát
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="products-content" style="margin-bottom: 25px;">
    <div class="basic-info">
        <div class="row">
            <div class="col-md-4">
                <h4>Thông tin cơ bản</h4>
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Tên SP</label>
                        <div><?php echo $_detail_product['prd_name']; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Mã SP</label>

                        <div><?php echo $_detail_product['prd_code']; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Tồn</label>
                        <div><?php echo cms_getInventory($_detail_product['ID'], $store_id); ?></div>
                    </div>

                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Đơn vị tính</label>
                        <div class="col-md-12 padd-0">
                            <div><?php echo (cms_getNameunitbyID($_detail_product['prd_unit_id']) == 'Chưa có') ? 'Chưa có đơn vị tính' : cms_getNameunitbyID($_detail_product['prd_unit_id']); ?></div>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; ">
                            <span>Sửa giá khi bán :</span> <?php echo (isset($_detail_product['prd_edit_price']) && ($_detail_product['prd_edit_price'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                        </div>
                        <br>

                        <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; "
                             class="<?php if (CMS_SERIAL == 0) echo ' hidden' ?>">
                            <span>Quà tặng :</span> <?php echo (isset($_detail_product['prd_gift']) && ($_detail_product['prd_gift'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; ">
                            <span>Cho bán âm :</span> <?php echo (isset($_detail_product['prd_allownegative']) && ($_detail_product['prd_allownegative'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                        </div>
                        <br>
                        <div style="padding-bottom: 5px; font-weight: 700; color: #9d9d9d; "
                             class="<?php if (CMS_SERIAL == 0) echo ' hidden' ?>">
                            <span>Có số serial :</span> <?php echo (isset($_detail_product['prd_serial']) && ($_detail_product['prd_serial'] == '1')) ? '<span class="yes">Có</span>' : '<span class="no">Không</span>'; ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Giá vốn</label>

                        <div><?php echo $can_view_price ? cms_encode_currency_format($_detail_product['prd_origin_price']) : '****'; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Thông tin thêm</label>

                        <div><?php echo $_detail_product['infor'] == '' ? '-' : $_detail_product['infor']; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Vị trí</label>

                        <div><?php echo $_detail_product['position'] == '' ? '-' : $_detail_product['position']; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Link nhập</label>

                        <div><?php echo $can_view_link ? ($_detail_product['link'] == '' ? '-' : $_detail_product['link']) : '****'; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Giá lẻ</label>

                        <div><?php echo cms_encode_currency_format($_detail_product['prd_sell_price']); ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Giá sỉ</label>

                        <div><?php echo cms_encode_currency_format($_detail_product['prd_sell_price2']); ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Bảo hành</label>
                        <div><?php echo cms_encode_currency_format($_detail_product['prd_warranty']) . ' tháng'; ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5 <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"
                         style="margin-top:10px">
                        <label>Ghi chú</label>

                        <div><?php echo($_detail_product['prd_size'] == '' ? ' - ' : $_detail_product['prd_size']); ?></div>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Danh mục</label>

                        <div><?php echo (cms_getNamegroupbyID($_detail_product['prd_group_id']) == 'Chưa có') ? 'Chưa có danh mục' : cms_getNamegroupbyID($_detail_product['prd_group_id']); ?></div>

                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Nhà sản xuất</label>

                        <div><?php echo (cms_getNamemanufacturebyID($_detail_product['prd_manufacture_id']) == 'Chưa có') ? 'Chưa có nhà sản xuất' : cms_getNamemanufacturebyID($_detail_product['prd_manufacture_id']); ?></div>

                    </div>

                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Định mức tối thiểu</label>
                        <div><?php echo $_detail_product['prd_min']; ?></div>
                        <br>
                    </div>
                    <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                        <label>Định mức tối đa</label>

                        <div><?php echo $_detail_product['prd_max']; ?></div>
                        <br>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 padd-0 hidden" style="margin-top: 10px;">
            <div class="row">
                <h2 class="text-center">Tương thích</h2>
                <?php echo $_detail_product['prd_descriptions']; ?>

            </div>
        </div>
    </div>
    <div class="expand-info hidden">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <h4 style="border-bottom: 1px solid #0B87C9; padding-bottom: 10px;"><i
                            class="fa fa-th-large blue"></i> <a style="color: #0B87C9; text-decoration: none;"
                                                                data-toggle="collapse" href="#collapseproductinfo"
                                                                aria-expanded="false" aria-controls="collapseExample">Thông
                        tin mở rộng(
                        <small> Nhấn để thêm các thông tin cho thuộc tính web</small>
                        )</a></h4>
            </div>
            <div class="col-md-12 col-xs-12">
                <div style="margin-top: 5px;"></div>
                <div class="collapse" id="collapseproductinfo">

                    <div class="col-md-12 padd-20">
                        <div class="row">
                            <div class="col-md-12 padd-20">
                                <div class="jumbotron text-center" id="img_upload"
                                     style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                                    <h3>Upload hình ảnh</h3>
                                    <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Kích
                                        thướt tối đa 10MB)
                                    </small>
                                    <p>
                                        <button class="btn" style="background-color: #337ab7; "
                                                onclick="browseKCFinder('img_upload','image');return false;"><i
                                                    class="fa fa-picture-o" style="font-size: 40px;color: #fff; "></i>
                                        </button>
                                    </p>
                                </div>
                            </div>

                            <div class="col-md-12 padd-20">
                                <h4 style="margin-top: 0;">Mô tả
                                    <small style="font-style: italic;">(Nhập thông tin mô tả chi tiết hơn để khách
                                        hàng hiểu sản phẩm của bạn)
                                    </small>
                                </h4>
                                <div id="ckeditor"><?php echo $_detail_product['prd_descriptions']; ?></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 padd-20">
                        <h4>Thông tin cho web</h4>
                        <small
                        ">Hiện thị trên trang web, tối ưu SEO.</small>
                    </div>
                    <div class="col-md-9">
                        <div class="row">
                            <div class="checkbox-group" style="margin-top: 20px;">
                                <label class="checkbox"><input type="checkbox"
                                                               disabled <?php echo ($_detail_product['display_website'] == 1) ? 'checked' : ''; ?>
                                                               class="checkbox" id="display_website"><span></span>
                                    Hiện thị ra website</label>
                                <br>
                                <label class="checkbox"><input type="checkbox"
                                                               disabled <?php echo ($_detail_product['prd_highlight'] == 1) ? 'checked' : ''; ?>
                                                               class="checkbox"><span></span> Nổi bật</label>&nbsp;&nbsp;<label
                                        class="checkbox"><input type="checkbox" disabled
                                                                class="checkbox" <?php echo ($_detail_product['prd_new'] == 1) ? 'checked' : ''; ?>><span></span>
                                    Hàng mới</label>&nbsp;&nbsp;&nbsp;<label class="checkbox"><input type="checkbox"
                                                                                                     disabled <?php echo ($_detail_product['prd_hot'] == 1) ? 'checked' : ''; ?>
                                                                                                     class="checkbox"
                                                                                                     id="prd_hot"><span></span>
                                    Đang bán chạy</label>
                            </div>
                        </div>
                        <div class="btn-groups pull-right" style="margin-top: 15px;">
                            <button type="button" class="save btn btn-primary" style="background-color: #8B8B8B;"
                                    onclick="cms_deactivate_product_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pause"></i> Ngừng KD
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_edit_product(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-pencil-square-o"></i> Sửa
                            </button>
                            <button type="button" class="save btn btn-danger"
                                    onclick="cms_delete_product_bydetail(<?php echo $_detail_product['ID']; ?>)"><i
                                        class="fa fa-trash-o"></i> Xóa
                            </button>
                            <button type="button" class="save btn btn-default btn-back"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                        class="fa fa-arrow-left"></i>Thoát
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

