<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-4 col-xs-12 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Đơn hàng &raquo;</h2>
                </div>
            </div>
            <div class="col-md-6 col-xs-12 padd-0">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(0)">
                            <i class="fa fa-floppy-o"></i> Lưu tạm
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(1)"><i
                                    class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(2)"><i
                                    class="fa fa-print"></i> Lưu & In
                        </button>
                        <button type="button" class="save btn-back btn btn-default"
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
        <div class="col-md-8">
            <div class="order-search" style="margin: 10px 0px; position: relative;">
                <input type="text" class="form-control" placeholder="Nhập mã sản phẩm tên sản phẩm"
                       id="search-pro-box">
            </div>
            <script>
                $store_id = $('#store-id').val();
                $(function () {
                    $("#search-pro-box").autocomplete({
                        minLength: 1,
                        source: 'orders/cms_autocomplete_products/' + 0 + '/' + $store_id,
                        focus: function (event, ui) {
                            $("#search-pro-box").val(ui.item.prd_code);
                            return false;
                        },
                        select: function (event, ui) {
                            cms_select_product_sell(ui.item.ID, 0);
                            $("#search-pro-box").val('');
                            return false;
                        }
                    }).keyup(function (e) {
                        if (e.which === 13) {
                            cms_autocomplete_enter_sell();
                            $("#search-pro-box").val('');
                            $(".ui-menu-item").hide();
                        }
                    })
                        .autocomplete("instance")._renderItem = function (ul, item) {
                        return $("<li>")
                            .append("<div>" + item.prd_code + " - " + item.prd_name + " - " + item.prd_sell_price + " - Tồn: " + item.quantity + "</div>")
                            .appendTo(ul);
                    };
                });
            </script>
            <div class="product-results">
                <table class="table table-bordered table-striped">
                    <thead>
                    <tr>
                        <th class="text-center hidden-xs">STT</th>
                        <th class="hidden-xs">Mã SP</th>
                        <th class="text-left">Tên SP</th>
                        <th class="hidden-xs">Vị trí</th>
                        <th class="hidden-xs">Hình ảnh</th>
                        <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">Ngày hết hạn</th>

                        <th class="text-center">SL</th>
                        <th class="text-center hidden-xs">ĐVT</th>
                        <th class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">Serial</th>
                        <th class="text-center">Đơn giá</th>
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
                                    <label>Khách hàng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="col-md-12 padd-0"
                                         style="position: relative;display: flex">
                                        <input id="search-box-cys" class="form-control"
                                               autocomplete="off" type="text"
                                               placeholder="Tìm khách hàng (F4)"
                                               style="border-radius: 3px 0 0 3px !important;"><span
                                                style="color: red; position: absolute; right: 34px; top:5px; "
                                                class="del-cys"></span>

                                        <div id="cys-suggestion-box"
                                             style="border: 1px solid #444; display: none; overflow-y: auto;background-color: #fff; z-index: 2 !important; position: absolute; left: 0; width: 100%; padding: 5px 0px; max-height: 400px !important;top:34px;">
                                            <div class="search-cys-inner"></div>
                                        </div>

                                        <button type="button" data-toggle="modal"
                                                data-target="#create-cust"
                                                class="save btn btn-primary"
                                                style="border-radius: 0 3px 3px 0; box-shadow: none; padding: 6px 11px;">
                                            +
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Ngày bán</label>
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
                                    <label>NV bán hàng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <select class="form-control" id="sale_id">
                                        <?php foreach ($data as $item) { ?>
                                            <option <?php if ($user_id == $item['id']) echo 'selected' ?>
                                                    value="<?php echo $item['id']; ?>"><?php echo $item['display_name']; ?></option>
                                        <?php } ?>
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
                        <button onclick="toggle_promotion()" class="hidden">Khuyến mãi</button>
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
                                    <label>VAT</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <select class="form-control" id="vat">
                                        <?php $list = cms_getListVAT();
                                        foreach ((array)$list as $key => $val) { ?>
                                            <option
                                                    value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                        <?php } ?>
                                    </select>
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
                                    <label>Tổng SL</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="total-quantity">
                                        0
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix hidden">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Giảm giá mỗi sp</label>
                                </div>
                                <div class="col-md-8 padd-0" style="display: flex;">
                                    <input type="text"
                                           class="form-control text-right txtMoney discount-item"
                                           placeholder="0" style="border-radius: 0 !important;">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Giảm giá</label>
                                </div>
                                <div class="col-md-8 padd-0" style="display: flex;">
                                    <button onclick="cms_change_discount_order()"
                                            style="display: none;"
                                            class="toggle-discount-order btn btn-primary">vnđ
                                    </button>
                                    <button onclick="cms_change_discount_order()"

                                            class="toggle-discount-order btn btn-primary">%
                                    </button>
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right discount-percent-order"
                                           placeholder="0"
                                           style="border-radius: 0 !important;">
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right txtMoney discount-order"
                                           placeholder="0"
                                           style="display:none;border-radius: 0 !important;">
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
                                    <label>Khách trả</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <input type="text"
                                           class="form-control text-right txtMoney customer-pay"
                                           placeholder="0" style="border-radius: 0 !important;">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label class="debt">Còn nợ</label>
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
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(0)">
                            <i class="fa fa-floppy-o"></i> Lưu tạm
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(1)"><i
                                    class="fa fa-check"></i> Lưu
                        </button>
                        <button type="button" class="save btn btn-primary" onclick="cms_save_orders(2)"><i
                                    class="fa fa-print"></i> Lưu & In
                        </button>
                        <button type="button" class="save btn-back btn btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Hủy
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
