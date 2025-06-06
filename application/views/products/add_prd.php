<div class="products">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="products-act">
                <div class="col-md-4 col-xs-12 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2><i class="fa fa-refresh" style="font-size: 14px; cursor: pointer;"
                               onclick="cms_vcrproduct('1')"></i>Tạo sản phẩm</h2>
                    </div>
                </div>
                <div class="col-md-6 col-xs-12">
                    <div class="right-action text-right">
                        <div class="btn-groups">
                            <button type="button" class="save btn btn-primary" onclick="cms_add_product( 'save' );"><i
                                        class="fa fa-check"></i> Lưu
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_add_product('saveandcontinue');"><i class="fa fa-floppy-o"></i> Lưu
                                và tiếp tục
                            </button>
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
                <div class="col-md-12 col-xs-12">
                    <div class="col-md-4">
                        <h4>Thông tin cơ bản</h4>
                        <small>Nhập tên và các thông tin cơ bản của sản phẩm</small>
                    </div>
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Tên SP</label>
                                <input type="text" id="prd_name"
                                       value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_name'] . ' - copy' ?>"
                                       class="form-control"
                                       placeholder="Nhập tên sản phẩm"/>
                                <script>
                                    $(function () {
                                        $("#prd_name").autocomplete({
                                            minLength: 1,
                                            source: 'orders/cms_autocomplete_products/' + 0 + '/' + 0,
                                            focus: function (event, ui) {
                                                $("#prd_name").val(ui.item.prd_name);
                                                return false;
                                            },
                                            select: function (event, ui) {
                                                $("#prd_name").val(ui.item.prd_name);
                                                return false;
                                            }
                                        })
                                            .autocomplete("instance")._renderItem = function (ul, item) {
                                            return $("<li>")
                                                .append("<div>" + item.prd_name + "</div>")
                                                .appendTo(ul);
                                        };
                                    });
                                </script>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Mã SP</label>
                                <input type="text" id="prd_code" class="form-control "
                                       placeholder="Nếu không nhập, hệ thống sẽ tự sinh."/>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Số lượng</label>
                                <input type="text" id="prd_sls" value="" placeholder="0"
                                       class="form-control text-right txtNumber"/>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Đơn vị tính</label>
                                <div style="display: flex">
                                    <select class="form-control" id="prd_unit_id">
                                        <optgroup label="Chọn đơn vị tính">
                                            <?php $unit_id = 0;
                                            if (isset($data['_detail_product']))
                                                $unit_id = $data['_detail_product']['prd_unit_id'];
                                            echo $unit_id;
                                            ?>
                                            <?php if (isset($data['_prd_unit']) && count($data['_prd_unit'])):
                                                foreach ((array)$data['_prd_unit'] as $key => $val) :
                                                    ?>
                                                    <option <?php if ($unit_id == $val['ID']) echo 'selected ' ?>
                                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_unit_name']; ?></option>
                                                <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </optgroup>
                                        <optgroup label="------------------------">
                                            <option value="product_unit" data-toggle="modal"
                                                    data-target="#list-prd-unit">Tạo mới
                                                đơn vị tính
                                            </option>
                                        </optgroup>
                                    </select>

                                    <button type="button" class="save btn btn-primary" data-toggle="modal"
                                            data-target="#list-prd-unit"
                                            style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label class="checkbox"><input type="checkbox"
                                                               id="prd_edit_price"
                                                               class="checkbox"
                                                               name="confirm"
                                                               value="1"
                                        <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_edit_price'] == 1) echo 'checked="checked"' ?>
                                    ><span></span> Sửa giá khi bán?</label>
                                <br>
                                <label class="checkbox <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"><input
                                            type="checkbox"
                                            id="prd_gift"
                                            class="checkbox"
                                            name="confirm"
                                            value="1"
                                        <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_gift'] == 1) echo 'checked="checked"' ?>>
                                    <span></span> Quà tặng?</label>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label class="checkbox"><input type="checkbox"
                                                               id="prd_allownegative"
                                                               class="checkbox"
                                                               name="confirm"
                                                               value="1"
                                        <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_allownegative'] == 1) echo 'checked="checked"' ?>>
                                    <span></span> Cho bán âm?</label>

                                <br>
                                <label class="checkbox <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"><input
                                            type="checkbox"
                                            id="prd_serial"
                                            class="checkbox"
                                            name="confirm"
                                            value="1"
                                        <?php if (isset($data['_detail_product']) and $data['_detail_product']['prd_serial'] == 1) echo 'checked="checked"' ?>>
                                    <span></span> Có số Serial?</label>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Giá vốn</label>
                                <input type="text" id="prd_origin_price"
                                       value="<?php if (isset($data['_detail_product'])) echo $can_view_price ? cms_encode_currency_format($data['_detail_product']['prd_origin_price']) : ''; ?>"
                                       class="form-control text-right txtMoney" placeholder="Nhập giá vốn"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Thông tin thêm</label>
                                <input type="text" id="infor"
                                       value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['infor'] ?>"
                                       class="form-control text-right" placeholder="Thông tin thêm"/>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Vị trí</label>
                                <input type="text" id="position"
                                       value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['position'] ?>"

                                       class="form-control text-right" placeholder="Vị trí"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Link nhập</label>
                                <input type="text" id="link"
                                       value="<?php if (isset($data['_detail_product'])) echo $can_view_link ? ($data['_detail_product']['link']) : ''; ?>"
                                       class="form-control text-right" placeholder="Link nhập"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Giá lẻ</label>
                                <input type="text" id="prd_sell_price"
                                       value="<?php if (isset($data['_detail_product'])) echo cms_encode_currency_format($data['_detail_product']['prd_sell_price']) ?>"
                                       class="form-control txtMoney text-right" placeholder="0"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Giá sỉ</label>
                                <input type="text" id="prd_sell_price2"
                                       value="<?php if (isset($data['_detail_product'])) echo cms_encode_currency_format($data['_detail_product']['prd_sell_price2']) ?>"
                                       class="form-control txtMoney text-right" placeholder="0"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Bảo hành (tháng)</label>
                                <input type="text" id="prd_warranty"
                                       value="<?php if (isset($data['_detail_product'])) echo($data['_detail_product']['prd_warranty']) ?>"
                                       class="form-control txtMoney" placeholder="Nhập số tháng bảo hành"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5 <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"
                                 style="margin-top:10px">
                                <label>Ghi chú</label>
                                <input type="text" id="prd_size"
                                       value="<?php if (isset($data['_detail_product'])) echo($data['_detail_product']['prd_size']) ?>"
                                       class="form-control" placeholder="Nhập ghi chú"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Danh mục</label>

                                <div style="display: flex">
                                    <select class="form-control" id="prd_group_id">
                                        <optgroup label="Chọn danh mục">
                                            <?php $group_id = 0;
                                            if (isset($data['_detail_product']))
                                                $group_id = $data['_detail_product']['prd_group_id'];
                                            ?>
                                            <?php if (isset($data['_prd_group']) && count($data['_prd_group'])):
                                                foreach ((array)$data['_prd_group'] as $key => $item) :
                                                    ?>
                                                    <option <?php if ($group_id == $item['ID']) echo 'selected ' ?>
                                                            value="<?php echo $item['ID']; ?>"><?php echo $item['prd_group_name']; ?></option>
                                                <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </optgroup>
                                        <optgroup label="------------------------">
                                            <option value="product_group" data-toggle="modal"
                                                    data-target="#list-prd-group">Tạo mới danh
                                                mục
                                            </option>
                                        </optgroup>
                                    </select>

                                    <button type="button" class="save btn btn-primary" data-toggle="modal"
                                            data-target="#list-prd-group"
                                            style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Nhà sản xuất</label>

                                <div style="display: flex">
                                    <select class="form-control" id="prd_manufacture_id">
                                        <optgroup label="Chọn nhà sản xuất">
                                            <?php $manufacture_id = 0;
                                            if (isset($data['_detail_product']))
                                                $manufacture_id = $data['_detail_product']['prd_manufacture_id'];
                                            echo $manufacture_id;
                                            ?>
                                            <?php if (isset($data['_prd_manufacture']) && count($data['_prd_manufacture'])):
                                                foreach ((array)$data['_prd_manufacture'] as $key => $val) :
                                                    ?>
                                                    <option <?php if ($manufacture_id == $val['ID']) echo 'selected ' ?>
                                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_manuf_name']; ?></option>
                                                <?php
                                                endforeach;
                                            endif;
                                            ?>
                                        </optgroup>
                                        <optgroup label="------------------------">
                                            <option value="product_manufacture" data-toggle="modal"
                                                    data-target="#list-prd-manufacture">Tạo mới
                                                Nhà sản xuất
                                            </option>
                                        </optgroup>
                                    </select>

                                    <button type="button" class="save btn btn-primary" data-toggle="modal"
                                            data-target="#list-prd-manufacture"
                                            style="border-radius: 0 3px 3px 0; box-shadow: none;">...
                                    </button>
                                </div>
                            </div>

                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Định mức tối thiểu</label>
                                <input type="text" id="prd_min"
                                       value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_min']; ?>"
                                       placeholder="0"
                                       class="form-control text-right txtNumber"/>
                            </div>
                            <div class="col-md-6 col-xs-6 padding-5" style="margin-top:10px">
                                <label>Định mức tối đa</label>
                                <input type="text" id="prd_max"
                                       value="<?php if (isset($data['_detail_product'])) echo $data['_detail_product']['prd_max']; ?>"
                                       class="form-control text-right txtNumber"
                                       placeholder="0"/>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 padd-0 hidden" style="margin-top: 10px;">
                        <div class="row">
                            <h2 class="text-center">Tương thích</h2>
                            <textarea id="prd_descriptions" cols="" class="form-control"
                                      rows="4"
                                      style="border-radius: 0;"></textarea>
                        </div>
                    </div>
                    <div class="col-md-12 padd-0" style="margin-top: 10px;">
                        <div class="row">
                            <div class="col-md-12 padd-20">
                                <div class="jumbotron text-center"
                                     style="border-radius: 0; margin-bottom: 10px; padding: 15px 20px;">
                                    <h3>Upload hình ảnh</h3>
                                    <small style="font-size: 14px; margin-bottom: 5px; display: inline-block;">(Kích
                                        thướt tối đa 10MB)
                                    </small>
                                    <p>
                                        <center>
                                    <div id='img_preview' style="display: none;"></div>
                                    <form id="image_upload_form" method="post" enctype="multipart/form-data"
                                          action='product/upload_img' autocomplete="off">
                                        <div class="file_input_container">
                                            <div class="upload_button"><input type="file" name="photo" id="photo"
                                                                              class="file_input"/></div>
                                        </div>

                                    </form>

                                    <script>
                                        $('#photo').on('change', function () {
                                            $("#img_preview").html('');
                                            $("#image_upload_form").ajaxForm({
                                                target: '#img_preview'
                                            }).submit();
                                        });
                                    </script>
                                    </center>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="expand-info hidden">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <h4 style="border-bottom: 1px solid #0B87C9; padding-bottom: 10px;"><i
                                class="fa fa-th-large blue"></i> <a style="color: #0B87C9; text-decoration: none;"
                                                                    data-toggle="collapse" href="#collapseproductinfo"
                                                                    aria-expanded="false"
                                                                    aria-controls="collapseExample">Thông
                            tin mở rộng(
                            <small> Nhấn để thêm các thông tin cho thuộc tính web</small>
                            )</a></h4>
                </div>
                <div class="col-md-12 col-xs-12">
                    <div style="margin-top: 5px;"></div>
                    <div class="collapse" id="collapseproductinfo">
                        <div class="col-md-12 padd-20">
                            <h4 style="margin-top: 0;">Mô tả
                                <small style="font-style: italic;">(Nhập thông tin mô tả chi tiết hơn để khách
                                    hàng hiểu sản phẩm của bạn)
                                </small>
                            </h4>
                            <div id="ckeditor"></div>
                        </div>
                        <div class="col-md-3 padd-20">
                            <h4>Thông tin cho web</h4>
                            <small
                            ">Hiện thị trên trang web, tối ưu SEO.</small>
                        </div>
                        <div class="col-md-9">
                            <div class="row">
                                <div class="checkbox-group" style="margin-top: 20px;">
                                    <label class="checkbox"><input type="checkbox" class="checkbox"
                                                                   id="display_website"><span></span> Hiện thị ra
                                        website</label>
                                    <br>
                                    <label class="checkbox"><input type="checkbox" id="prd_highlight"
                                                                   class="checkbox"><span></span> Nổi bật</label>&nbsp;&nbsp;<label
                                            class="checkbox"><input type="checkbox" class="checkbox"
                                                                    id="prd_new"><span></span> Hàng mới</label>&nbsp;&nbsp;&nbsp;<label
                                            class="checkbox"><input type="checkbox" class="checkbox"
                                                                    id="prd_hot"><span></span> Đang bán chạy</label>
                                </div>
                            </div>
                            <div class="btn-groups pull-right" style="margin-top: 15px;">
                                <button type="button" class="save btn btn-primary" onclick="cms_add_product( 'save' );">
                                    <i
                                            class="fa fa-check"></i> Lưu
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_add_product( 'saveandcontinue' );"><i class="fa fa-floppy-o"></i>
                                    Lưu và tiếp tục
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
    </div>
</div>
