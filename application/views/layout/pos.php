<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo CMS_BASE_URL; ?>"/>
    <link rel="shortcut icon" type="image/png" href="public/templates/images/check.png"/>
    <title><?php echo isset($seo['title']) ? $seo['title'] : 'Phần mềm quản lý bán hàng'; ?></title>
    <link href="public/templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/templates/css/bootstrap-datepicker.css" rel="stylesheet">
    <link href="public/templates/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="public/templates/css/font-awesome.min.css" rel="stylesheet">
    <link href="public/templates/css/style.css" rel="stylesheet">
    <link href="public/templates/css/jquery-ui.min.css" rel="stylesheet">
    <link href="public/templates/css/jquery.datetimepicker.css" rel="stylesheet">
    <script src="public/templates/js/jquery.js"></script>
    <script src="public/templates/js/jquery.form.js"></script>
    <link href="public/templates/css/select2.min.css" rel="stylesheet"/>
</head>
<body>
<header>
    <?php $this->load->view('common/header', isset($data) ? $data : NULL); ?>
</header>
<section id="pos" class="main" role="main">
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12 col-xs-12 padd-left-0">
                <div class="sidebar sidebar-fixed hidden-xs hidden-sm hidden-md hidden-lg" id="sidebar">
                    <?php
                    $this->load->view('common/sidebar', isset($data) ? $data : NULL);
                    ?>
                </div>
                <div class="main-content">
                    <?php
                    $this->load->view('common/modal', isset($data) ? $data : NULL);
                    ?>
                    <div>
                        <div class="row">
                            <div class="orders-act">
                                <div class="col-md-8 col-xs-12">
                                    <div class="order-search" style="margin: 10px 0px; position: relative;">
                                        <input type="text" class="form-control"
                                               placeholder="Nhập mã sản phẩm tên sản phẩm (F2)"
                                               id="search-pro-box">
                                    </div>
                                    <div class="product-results">
                                        <table class="table table-bordered table-striped">
                                            <thead>
                                            <tr>
                                                <th class="text-center hidden-xs">STT</th>
                                                <th class="text-left hidden-xs">Mã SP</th>
                                                <th class="text-left">Tên SP</th>
                                                <th class="hidden-xs">Vị trí</th>
                                                <th class="text-center hidden-xs">Hình ảnh</th>
                                                <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">
                                                    Ngày hết hạn
                                                </th>
                                                <th class="text-center">SL</th>
                                                <th class="text-center hidden-xs">ĐVT</th>
                                                <th class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">
                                                    Serial
                                                </th>
                                                <th class="text-center">Đơn giá</th>
                                                <th class="text-center">Thành tiền</th>
                                                <th></th>
                                            </tr>
                                            </thead>
                                            <tbody id="pro_search_append">
                                            </tbody>
                                        </table>
                                        <div class="alert alert-success hidden-xs" style="margin-top: 30px;"
                                             role="alert">Gõ mã
                                            tên sản phẩm vào hộp
                                            tìm kiếm để thêm hàng vào đơn hàng
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-12">
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
                                                                <input id="search-box-cys" autocomplete="off"
                                                                       class="form-control"
                                                                       type="text"
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
                                                            <label>NV bán hàng</label>
                                                        </div>
                                                        <div class="col-md-8 col-xs-8 padd-0">
                                                            <select class="form-control" id="sale_id">
                                                                <?php foreach ((array)$data['sale'] as $item) { ?>
                                                                    <option <?php if ($data['user_id'] == $item['id']) echo 'selected' ?>
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
                                                            <label>Giảm giá (F7)</label>
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
                                                            <label>Khách trả (F8)</label>
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
                                        <div class="col-md-12 col-xs-12">
                                            <div class="btn-groups pull-right" style="margin-bottom: 50px;">
                                                <button type="button" class="save btn btn-primary"
                                                        onclick="cms_save_orders(3)"><i
                                                            class="fa fa-check"></i> Lưu (F9)
                                                </button>
                                                <button type="button" class="save btn btn-primary"
                                                        onclick="cms_save_orders(4)"><i class="fa fa-print"></i> Lưu và
                                                    in (F10)
                                                </button>
                                                <a onclick="window.history.back();">
                                                    <button type="button" class="save btn-back btn btn-default"><i
                                                                class="fa fa-arrow-left"></i> Hủy
                                                    </button>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</body>

<script src="public/templates/js/select2.min.js"></script>
<script src="public/templates/js/jquery-ui.min.js"></script>
<script src="public/templates/js/html5shiv.min.js"></script>
<script src="public/templates/js/respond.min.js"></script>
<script src="public/templates/js/bootstrap.min.js"></script>
<script src="public/templates/js/jquery.datetimepicker.full.js"></script>
<script src="public/templates/js/bootstrap-datepicker.min.js"></script>
<script src="public/templates/js/bootstrap-datepicker.vi.min.js"></script>
<script src="public/templates/js/ckeditor.js"></script>
<script src="public/templates/js/editor.js"></script>
<script src="public/templates/js/ajax2.js"></script>
<script src="public/templates/js/bootstrap-tagsinput.min.js"></script>
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
    document.addEventListener('keyup', hotkey, false);
</script>
</html>
