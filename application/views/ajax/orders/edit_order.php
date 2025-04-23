<div id="edit_order" class="hidden"></div>
<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-3 col-xs-12 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Đơn hàng &raquo;<?php echo $data['_order']['output_code']; ?></h2>
                </div>
            </div>
            <div class="col-md-7 col-xs-12 padd-0">
                <div class="right-action text-right">
                    <div class="btn-groups">
                        <?php if ($data['_order']['order_status'] == 0) { ?>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,0)">
                                <i class="fa fa-floppy-o"></i> Lưu tạm
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,2)"><i
                                        class="fa fa-check"></i> Xác nhận
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,3)"><i
                                        class="fa fa-taxi"></i> Đang giao
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,4)"><i
                                        class="fa fa-check-square-o"></i> Đã giao
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,1)"><i
                                        class="fa fa-check-circle-o"></i> Thành công
                            </button>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,5)"><i
                                        class="fa fa-check-circle-o"></i> Hủy
                            </button>
                            <button type="button" class="save btn-back btn btn-default"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                        class="fa fa-arrow-left"></i> Thoát
                            </button>
                        <?php } else { ?>
                            <button type="button" class="save btn btn-primary"
                                    onclick="cms_update_orders(<?php echo $data['_order']['ID']; ?>,6)"><i
                                        class="fa fa-check-circle-o"></i> Lưu
                            </button>
                            <button type="button" class="save btn-back btn btn-default"
                                    onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                        class="fa fa-arrow-left"></i> Thoát
                            </button>
                        <?php } ?>
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
                    <?php $seq = 1;
                    foreach ((array)$_list_products as $product):
                        $list_serial = $this->db->select('distinct(serial)')->from('inventory')->join('inventory_serial', 'inventory.ID_temp=inventory_serial.inventory_id', 'INNER')->where('inventory_serial.quantity >', 0)->where('inventory.quantity >', 0)->where('inventory.product_id', $product['ID'])->where('store_id', $data['_order']['store_id'])->get()->result_array();
                        ?>
                        <tr data-id="<?php echo $product['ID'] ?>">
                            <td class="text-center seq hidden-xs"><?php echo $seq++; ?></td>
                            <td class="text-left hidden-xs"><?php echo $product['prd_code']; ?></td>
                            <td class="text-left"><?php echo $product['prd_name']; ?>
                                <input type="text" class="form-control note_product_order" placeholder="Ghi chú"
                                       value="<?php echo $product['note']; ?>">
                            </td>

                            <td class="hidden-xs"><?php echo $product['position']; ?></td>

                            <td class="text-left hidden"><?php echo $product['prd_descriptions']; ?></td>
                            <td class="text-center zoomin hidden-xs"><img height="30"
                                                                          src="public/templates/uploads/<?php echo cms_show_image($product['prd_image_url']); ?>">
                            </td>
                            <td class="<?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">
                                <select class="form-control expire">
                                    <?php
                                    if (isset($product['list_expire']) && count($product['list_expire'])) {
                                        $check = false;
                                        foreach ((array)$product['list_expire'] as $expire) {
                                            if ($expire['inventory_expire'] == $product['expire'])
                                                $check = true;
                                            ?>
                                            <option <?php if ($product['expire'] == $expire['inventory_expire']) echo 'selected' ?>
                                                    value="<?php echo $expire['inventory_expire'] ?>"><?php echo cms_ConvertDate($expire['inventory_expire']) ?></option>
                                            <?php
                                        }

                                        if ($check == false) {
                                            ?>
                                            <option selected
                                                    value="<?php echo $product['expire'] ?>"><?php echo $product['expire'] ?></option>
                                            <?php
                                        }
                                    } else {
                                        ?>
                                        <option selected
                                                value="<?php echo $product['expire'] ?>"><?php echo $product['expire'] ?></option>
                                        <?php
                                    } ?>

                                </select>
                            </td>
                            <td class="text-center" style="max-width: 80px;"><input
                                        style="min-width:55px;max-height: 34px;"
                                        type="text" <?php echo ($product['prd_serial'] == 1) ? 'disabled' : ''; ?>
                                        class="txtNumber form-control quantity_product_order text-center"
                                        value="<?php echo $product['quantity']; ?>">
                            </td>
                            <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>
                            <td class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">
                                <?php

                                $list_check = explode(",", $product['list_serial']);

                                foreach ((array)$list_check as $serial) {
                                    if ($serial != '') {
                                        ?>
                                        <input type="checkbox" checked class="serial checkbox"
                                               onclick="cms_load_infor_order()" style="display: inherit"
                                               value="<?php echo $serial; ?>">
                                        <?php echo $serial; ?>
                                        <br>
                                        <?php
                                    }
                                }

                                foreach ((array)$list_serial as $serial) {
                                    ?>
                                    <input type="checkbox" class="serial checkbox" onclick="cms_load_infor_order()"
                                           style="display: inherit" value="<?php echo $serial['serial']; ?>">
                                    <?php echo $serial['serial']; ?>
                                    <br>
                                    <?php
                                }
                                ?>

                            </td>
                            <td style="max-width: 100px;" class="text-center output">

                                <div>
                                    <input type="text" <?php if ($product['prd_edit_price'] == 0) echo 'disabled'; ?>
                                           style="min-width:80px;max-height: 34px;"
                                           class="txtMoney form-control text-center price-order"
                                           value="<?php echo cms_encode_currency_format($product['price']); ?>">
                                    <i class="fa fas fa-gift bigger-120 href"
                                       style="line-height: 34px; padding-right: 2px;"
                                       onclick="cms_show_discount_order(<?php echo $product['ID']; ?>)"></i>
                                </div>

                                <span style="color: red"
                                      class="discount_show href"><?php echo ($product['discount'] != 0 && $product['discount'] != '') ? cms_encode_currency_format($product['discount']) : '' ?></span>
                                <div id="discount_order_<?php echo $product['ID']; ?>" class="discount_order"
                                     style="display: none;width: 280px;z-index: 9999">
                                    <div class="col-md-12 text-center" style="line-height: 40px;background: #0B87C9;"
                                         onclick="$('#discount_order_<?php echo $product['ID']; ?>').toggle()">
                                        <label style="color: white">Giảm giá</label>
                                    </div>
                                    <div class="col-md-12 col-xs-12" style="padding: 10px;line-height: 30px;">
                                        <div class="col-md-4 text-left">
                                            <label>Giảm</label>
                                        </div>
                                        <div class="col-md-8" style="display: flex">
                                            <input type="text"
                                                   class="txtNumber form-control toggle-discount-item-order_<?php echo $product['ID']; ?> discount-item-percent-order discount_percent"
                                                   placeholder="0%" value="<?php echo $product['percent']; ?>">

                                            <input type="text"
                                                   class="form-control toggle-discount-item-order_<?php echo $product['ID']; ?> txtMoney discount-item-order discount_money"
                                                   placeholder="0" style="display:none;"
                                                   value="<?php echo cms_encode_currency_format($product['discount']); ?>">
                                            <button onclick="cms_change_discount_item_order(<?php echo $product['ID']; ?>)"
                                                    style="display:none;"
                                                    class="toggle-discount-item-order_<?php echo $product['ID']; ?> btn btn-success">
                                                vnđ
                                            </button>

                                            <button onclick="cms_change_discount_item_order(<?php echo $product['ID']; ?>)"
                                                    style="display:none;"
                                                    class="toggle-discount-item-order_<?php echo $product['ID']; ?> btn btn-success">
                                                %
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <td class="text-center total-money"><?php echo cms_encode_currency_format($product['quantity'] * ($product['price'] - $product['discount'])); ?></td>
                            <td class="text-center"><i class="fa fa-trash-o del-pro-order"></i></td>
                        </tr>
                    <?php endforeach; ?>
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
                                        <input id="search-box-cys" autocomplete="off" class="form-control"
                                               type="text"
                                               placeholder="<?php echo cms_getNamecustomerbyID($data['_order']['customer_id']); ?>"
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
                                           style="border-radius: 0 !important;"
                                           value="<?php echo $data['_order']['sell_date']; ?>">
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
                                        <?php foreach ((array)$data['user'] as $item) { ?>
                                            <option <?php if ($item['id'] == $data['_order']['sale_id']) echo ' selected ' ?>
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
                                                                      style="border-radius: 0;""><?php echo $data['_order']['notes']; ?></textarea>
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
                                                   value="<?php echo $key; ?>" <?php echo ($data['_order']['payment_method'] == $key) ? 'checked' : ''; ?>>
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
                                            <option <?php if ($data['_order']['vat'] == $key) echo 'selected' ?>
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
                                        <?php echo cms_encode_currency_format($data['_order']['total_price']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tổng SL</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="total-quantity">
                                        <?php echo $data['_order']['total_quantity']; ?>
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
                                           placeholder="0" style="border-radius: 0 !important;"
                                           value="<?php echo cms_encode_currency_format($data['_order']['discount_item']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Giảm giá</label>
                                </div>
                                <div class="col-md-8 padd-0" style="display: flex;">
                                    <button onclick="cms_change_discount_order()"
                                            class="toggle-discount-order btn btn-primary hidden">vnđ
                                    </button>
                                    <button onclick="cms_change_discount_order()" style="display: none;"
                                            class="toggle-discount-order btn btn-primary hidden">%
                                    </button>
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right discount-percent-order"
                                           placeholder="0%" style="border-radius: 0 !important;"
                                           value="<?php echo($data['_order']['discount_percent']); ?>">
                                    <input type="text"
                                           class="toggle-discount-order form-control text-right txtMoney discount-order hidden"
                                           placeholder="0" style="border-radius: 0 !important;"
                                           value="<?php echo cms_encode_currency_format($data['_order']['coupon']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tổng cộng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="total-after-discount">
                                        <?php echo cms_encode_currency_format($data['_order']['total_price'] - $data['_order']['coupon']); ?>
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
                                           placeholder="0" style="border-radius: 0 !important;"
                                           value="<?php echo cms_encode_currency_format($data['_order']['customer_pay']); ?>">
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label class="debt">Còn nợ</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div class="debt"><?php echo cms_encode_currency_format($data['_order']['lack']); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $name = $('li.data-cys-name-' + <?php echo $data['_order']['customer_id']; ?>).text();
    $("#search-box-cys").prop('readonly', true).attr('data-id', <?php echo $data['_order']['customer_id']; ?>).val($name);
    $(".del-cys").html('<i class="fa fa-minus-circle" aria-hidden="true"></i>');
    $('#cys-suggestion-box').hide();

    $(function () {
        $("#search-pro-box").autocomplete({
            minLength: 1,
            source: 'orders/cms_autocomplete_products/<?php echo $data['_order']['customer_id'] . '/' . $data['_order']['store_id']; ?>',
            focus: function (event, ui) {
                $("#search-pro-box").val(ui.item.prd_code);
                return false;
            },
            select: function (event, ui) {
                cms_select_product_sell(ui.item.ID);
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

    cms_load_infor_order();
</script>
