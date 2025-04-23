<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-xs-12 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Tạo phiếu nhập &raquo;</h2>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 padd-0">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(0)"><i
                                    class="fa fa-save"></i> Lưu tạm
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(1)"><i
                                    class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(2)"><i
                                    class="fa fa-print"></i> Lưu & In
                        </button>
                        <button type="button" class="save btn btn-default btn-back"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="main-space orders-space"></div>

<div class="orders-content check-order">
    <div class="row">
        <div class="col-md-8 left_site">
            <div class="order-search" style="margin: 10px 0px; position: relative;">
                <input type="text" class="form-control" placeholder="Nhập mã sản phẩm tên sản phẩm"
                       id="search-pro-box">
                <script>
                    $(function () {
                        $("#search-pro-box").autocomplete({
                            minLength: 1,
                            source: 'input/cms_autocomplete_products/',
                            focus: function (event, ui) {
                                $("#search-pro-box").val(ui.item.prd_code);
                                return false;
                            },
                            select: function (event, ui) {
                                cms_select_product_import(ui.item.ID);
                                $("#search-pro-box").val('');
                                return false;
                            }
                        }).keyup(function (e) {
                            if (e.which === 13) {
                                cms_autocomplete_enter_import();
                                $("#search-pro-box").val('');
                                $(".ui-menu-item").hide();
                            }
                        })
                            .autocomplete("instance")._renderItem = function (ul, item) {
                            return $("<li>")
                                .append("<div>" + item.prd_code + " - " + item.prd_name + "</div>")
                                .appendTo(ul);
                        };
                    });
                </script>
            </div>
            <div class="product-results">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center hidden-xs">STT</th>
                        <th class="text-left hidden-xs">Mã SP</th>
                        <th class="text-left">Tên SP</th>
                        <th class="text-center hidden-xs">Hình ảnh</th>
                        <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">Số ngày hết hạn</th>
                        <th class="text-center">SL</th>
                        <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">CK</th>
                        <th class="text-center hidden-xs">ĐVT</th>
                        <th class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">Serial</th>
                        <th class="text-center">Giá nhập</th>
                        <th class="text-center">Thành tiền</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="pro_search_append">
                    </tbody>
                </table>
                <div class="alert alert-success hidden-xs" style="margin-top: 30px;" role="alert">Gõ mã tên sản phẩm vào
                    hộp
                    tìm kiếm để thêm hàng vào đơn hàng
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12 col-xs-12">
                    <div class="morder-info" style="padding: 4px;">
                        <div class="tab-contents" style="padding: 8px 6px;">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Nhà cung cấp</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="col-md-12 padd-0" style="position: relative;display: flex">
                                        <input id="search-box-mas" class="form-control" type="text"
                                               placeholder="Tìm nhà cung cấp"
                                               style="border-radius: 3px 0 0 3px !important;"><span
                                                style="color: red; position: absolute; right: 34px; top:5px; "
                                                class="del-mas"></span>

                                        <div id="mas-suggestion-box"
                                             style="border: 1px solid #444; display: none; overflow-y: auto;background-color: #fff; z-index: 2 !important; position: absolute; left: 0; width: 100%; padding: 5px 0px; max-height: 400px !important;top:34px;">
                                            <div class="search-mas-inner"></div>
                                        </div>
                                        <button type="button" data-toggle="modal" data-target="#create-sup"
                                                class="save btn btn-primary"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none; padding: 6px 11px;">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Ngày nhập</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <input id="date-order" class="form-control datepk" type="text" placeholder="Hôm nay"
                                           style="border-radius: 0 !important;">
                                </div>
                                <script>$('#date-order').datetimepicker({
                                        autoclose: true
                                    });
                                </script>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Người nhập</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <select disabled="disabled" class="form-control">
                                        <option
                                                value="1"><?php if (isset($data['user'])) echo $data['user']['display_name']; ?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Ghi chú</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                                            <textarea id="note-order" cols="" class="form-control"
                                                                      rows="2"
                                                                      style="border-radius: 0;"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <h4 class="lighter" style="margin-top: 0;">
                        <i class="fa fa-info-circle blue"></i>
                        Thông tin thanh toán
                    </h4>

                    <div class="morder-info" style="padding: 4px;">
                        <div class="tab-contents" style="padding: 8px 6px;">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Hình thức</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="input-group">
                                        <?php
                                        $list = cms_getListReceiptMethod();
                                        foreach ((array)$list as $key => $item) : ?>
                                            <input type="radio" class="payment-method"
                                                   name="method-pay"
                                                   value="<?php echo $key; ?>" <?php echo ($key == 1) ? 'checked' : ''; ?>>
                                            <?php echo $item; ?> &nbsp;
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tiền hàng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="total-money">
                                        0
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Chiết khấu</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0" style="display: flex;">
                                    <button onclick="cms_change_discount_import()"
                                            class="toggle-discount-import btn btn-primary">vnđ
                                    </button>
                                    <button onclick="cms_change_discount_import()"
                                            style="display: none;"
                                            class="toggle-discount-import btn btn-primary">%
                                    </button>
                                    <input type="text"
                                           class="toggle-discount-import form-control text-right discount-percent-import"
                                           placeholder="0"
                                           style="display:none;border-radius: 0 !important;">
                                    <input type="text"
                                           class="toggle-discount-import form-control text-right txtMoney discount-import"
                                           placeholder="0" style="border-radius: 0 !important;">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tổng cộng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="total-after-discount">
                                        0
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Thanh toán</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <input type="text"
                                           class="form-control text-right txtMoney customer-pay"
                                           placeholder="0" style="border-radius: 0 !important;">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Còn nợ</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="debt">0</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12 padd-0">
                    <div class="btn-groups pull-right" style="margin-bottom: 50px;">
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(0)"><i
                                    class="fa fa-save"></i> Lưu tạm
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(1)"><i
                                    class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_import(2)"><i
                                    class="fa fa-print"></i> Lưu & In
                        </button>
                        <button type="button" class="save btn btn-default btn-back"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>