<div class="breadcrumbs-fixed panel-action">
    <div class="row">
        <div class="orders-act">
            <div class="col-md-3 col-xs-12 col-md-offset-2">
                <div class="left-action text-left clearfix">
                    <h2>Đơn hàng &raquo;<span
                                style="font-style: italic; font-weight: 400; font-size: 16px;"><?php echo $data['_order']['output_code']; ?></span>
                    </h2>
                </div>
            </div>
            <div class="col-md-7 col-xs-12 padd-0">
                <div class="right-action text-right">
                    <div class="btn-groups">

                        <?php if ($data['_order']['deleted'] == 0) {
                            if ($data['_order']['order_status'] == 2) { ?>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,3)"><i
                                            class="fa fa-taxi"></i> Đang giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,4)"><i
                                            class="fa fa-check-square-o"></i> Đã giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,1)"><i
                                            class="fa fa-check-circle-o"></i> Thành công
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,5)"><i
                                            class="fa fa-check-circle-o"></i> Hủy
                                </button>
                            <?php } else if ($data['_order']['order_status'] == 3) { ?>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,2)"><i
                                            class="fa fa-check"></i> Xác nhận
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,4)"><i
                                            class="fa fa-check-square-o"></i> Đã giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,1)"><i
                                            class="fa fa-check-circle-o"></i> Thành công
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,5)"><i
                                            class="fa fa-check-circle-o"></i> Hủy
                                </button>
                            <?php } else if ($data['_order']['order_status'] == 4) { ?>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,2)"><i
                                            class="fa fa-check"></i> Xác nhận
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,3)"><i
                                            class="fa fa-taxi"></i> Đang giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,1)"><i
                                            class="fa fa-check-circle-o"></i> Thành công
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,5)"><i
                                            class="fa fa-check-circle-o"></i> Hủy
                                </button>
                            <?php } else if ($data['_order']['order_status'] == 1) { ?>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,2)"><i
                                            class="fa fa-check"></i> Xác nhận
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,3)"><i
                                            class="fa fa-taxi"></i> Đang giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,4)"><i
                                            class="fa fa-check-square-o"></i> Đã giao
                                </button>
                                <button type="button" class="save btn btn-primary"
                                        onclick="cms_change_status_order(<?php echo $data['_order']['ID']; ?>,5)"><i
                                            class="fa fa-check-circle-o"></i> Hủy
                                </button>
                            <?php }
                        } ?>

                        <button type="button" class="save btn btn-primary"
                                onclick="cms_print_order(1,<?php echo $data['_order']['ID']; ?>)"><i
                                    class="fa fa-print"></i> In đơn hàng
                        </button>
                        <button type="button" class="save btn btn-back btn-default"
                                onclick="cms_javascript_redirect( cms_javascrip_fullURL() )"><i
                                    class="fa fa-arrow-left"></i> Quay lại
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="main-space orders-space"></div>
<div class="orders-content">
    <div class="row">
        <div class="col-md-7" style="overflow: auto">
            <table class="table table-bordered table-striped" style="margin-top: 30px;">
                <thead>
                <tr>
                    <th class="text-center hidden-xs">STT</th>
                    <th class="text-left hidden-xs">Mã SP</th>
                    <th class="text-left">Tên SP</th>
                    <th class="text-center hidden-xs">Hình ảnh</th>
                    <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">Ngày hết hạn</th>
                    <th class="text-center">SL</th>
                    <th class="text-center hidden-xs">ĐVT</th>
                    <th class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">Serial</th>
                    <th class="text-center">Đơn giá</th>
                    <th class="text-center">Thành tiền</th>
                </tr>
                </thead>
                <tbody>
                <?php if (isset($_list_products) && count($_list_products)) :
                    $nstt = 1;
                    foreach ((array)$_list_products as $product) :
                        ?>
                        <tr data-id="<?php echo $product['ID']; ?>">
                            <td class="text-center hidden-xs"><?php echo $nstt++; ?></td>
                            <td class="text-left hidden-xs"><?php echo $product['prd_code']; ?></td>
                            <td class="text-left"><?php echo $product['prd_name']; ?>
                                <br><span style="color: red;">
                                <?php echo $product['note']; ?>
                                    </span>
                            </td>
                            <td class="text-center zoomin hidden-xs"><img height="30"
                                                                          src="public/templates/uploads/<?php echo cms_show_image($product['prd_image_url']); ?>">
                            </td>
                            <td class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>"><?php echo cms_ConvertDate($product['expire']); ?></td>
                            <td class="text-center" style="max-width: 60px;"><?php echo $product['quantity']; ?> </td>
                            <td class="text-center hidden-xs"><?php echo $product['prd_unit_name']; ?> </td>
                            <td class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"><?php echo cms_convertserial($product['list_serial']); ?> </td>
                            <td class="text-center price-order"><?php echo cms_encode_currency_format($product['price']); ?></td>
                            <td class="text-center total-money"><?php echo cms_encode_currency_format($product['price'] * $product['quantity']); ?></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
        <div class="col-md-5">
            <div class="row">
                <div class="col-md-12 col-xs-12 padd-0">
                    <div class="morder-info" style="padding: 4px;">
                        <div class="tab-contents" style="padding: 8px 6px;">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Mã phiếu</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo $data['_order']['output_code']; ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Tình trạng</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo cms_getNamestatusbyID($data['_order']['order_status']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Khách hàng</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo $data['_order']['input_id'] > 0 ? cms_getNamesupplierbyID($data['_order']['customer_id']) : cms_getNamecustomerbyID($data['_order']['customer_id']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Kho xuất</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo cms_getNamestockbyID($data['_order']['store_id']); ?>
                                </div>
                            </div>

                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Ngày bán</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo cms_ConvertDateTime($data['_order']['sell_date']); ?>

                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>NV bán hàng</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <?php echo cms_getNameAuthbyID($data['_order']['sale_id']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-5 col-xs-6">
                                    <label>Ghi chú</label>
                                </div>
                                <div class="col-md-7 col-xs-6">
                                    <textarea readonly id="note-order" cols="" class="form-control" rows="2"
                                              style="border-radius: 0;"><?php echo $data['_order']['notes']; ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 col-xs-12">
                    <ul class="nav nav-tabs tab-setting" role="tablist">
                        <li role="presentation" class="active"><a href="#payment-detail" aria-controls="home" role="tab"
                                                                  data-toggle="tab"><i
                                        class="fa fa-user"></i> Thông tin thanh toán</a></li>
                        <li role="presentation"><a href="#history" aria-controls="profile" role="tab"
                                                   data-toggle="tab"><i
                                        class="fa fa-cog"></i> Lịch sử</a></li>
                    </ul>
                    <div class="tab-content" id="detail_payment">
                        <div role="tabpanel" class="tab-pane active" id="payment-detail">
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Hình thức</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div>
                                        <?php echo cms_getNameReceiptMethodByID($data['_order']['payment_method']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>VAT</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div>
                                        <?php echo cms_encode_currency_format($data['_order']['vat']) . '%'; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tiền hàng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div>
                                        <?php echo cms_encode_currency_format($data['_order']['total_price']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Chiết khấu</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div><?php echo cms_encode_currency_format($data['_order']['coupon']); ?></div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Tổng cộng</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div>
                                        <?php echo cms_encode_currency_format($data['_order']['total_money']); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Khách trả</label>
                                </div>
                                <div class="col-md-8 orange padd-0">
                                    <?php echo cms_encode_currency_format($data['_order']['customer_pay']); ?>
                                </div>
                            </div>
                            <div class="form-group marg-bot-10 clearfix">
                                <div class="col-md-4 col-xs-4 padd-0">
                                    <label>Còn nợ</label>
                                </div>
                                <div class="col-md-8 col-xs-8 padd-0">
                                    <div><?php echo cms_encode_currency_format($data['_order']['lack']);
                                        if ($data['_order']['lack'] > 0 && $data['_order']['order_status'] < 5 && $data['_order']['order_status'] > 0 && $data['_order']['deleted'] == 0) { ?>
                                            <button type="button" class="save btn btn-primary"
                                                    onclick="cms_show_receipt_order();">
                                                Thu nợ
                                            </button>
                                        <?php } ?></div>
                                </div>
                            </div>
                            <div id="receipt_order" style="display: none;width: 280px;">
                                <div class="col-md-12 col-xs-12">
                                    <?php
                                    $list = cms_getListReceiptMethod();
                                    foreach ((array)$list as $key => $item) : ?>
                                        <input type="radio" class="payment-method"
                                               name="method-pay"
                                               value="<?php echo $key; ?>" <?php echo ($key == 1) ? 'checked' : ''; ?>>
                                        <?php echo $item; ?> &nbsp;
                                    <?php endforeach; ?>
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <input id="receipt_note" class="form-control" type="text"
                                           placeholder="Ghi chú"
                                           style="border-radius: 0 !important;">
                                </div>
                                <div class="col-md-12 col-xs-12">
                                    <input id="receipt_date" class="form-control datepk" type="text"
                                           placeholder="Ngày thu"
                                           style="border-radius: 0 !important;">
                                </div>
                                <script>$('#receipt_date').datetimepicker({
                                        autoclose: true
                                    });
                                </script>
                                <div class="col-md-12 col-xs-12">
                                    <div class="col-md-6" style="padding: 0px;">
                                        <input id="receipt_money" class="form-control txtMoney" type="text"
                                               placeholder="Số tiền thu"
                                               value="<?php echo cms_encode_currency_format($data['_order']['lack']); ?>"
                                               style="border-radius: 0 !important;">
                                    </div>
                                    <div class="col-md-6" style="padding: 0px;display: inline-flex">
                                        <button type="button" class="save btn btn-primary"
                                                onclick="save_receipt_order(<?php echo $data['_order']['ID'] ?>);"
                                                title="Đồng ý">
                                            <i class="fa fa-plus"></i>
                                            Thu
                                        </button>
                                        <button type="button" class="save btn" title="Hủy"
                                                onclick="cms_hide_receipt_order();">
                                            <i class="fa fa-times"
                                               style="color: white !important;"></i>
                                            Hủy
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="history">
                            <table id="sample-table-2" class="table table-striped table-bordered table-hover">
                                <thead>
                                <tr>
                                    <th class="center hidden-xs">STT</th>
                                    <th>Ngày thanh toán</th>
                                    <th class="text-center">Số tiền</th>
                                    <th class="text-center">Hình thức</th>
                                    <th class="text-center">Xóa</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if (isset($data['_receipt']) && count($data['_receipt'])) :
                                    $nstt = 1;
                                    foreach ((array)$data['_receipt'] as $receipt) :
                                        ?>
                                        <tr>
                                            <td class="text-center hidden-xs"><?php echo $nstt++; ?></td>
                                            <td class="text-center ng-binding"><?php echo cms_ConvertDateTime($receipt['receipt_date']); ?></td>
                                            <td class="text-right ng-binding"><?php echo cms_encode_currency_format($receipt['total_money']); ?></td>
                                            <td class="text-center">
                                                <?php echo cms_getNameReceiptMethodByID($receipt['receipt_method']);
                                                ?>
                                            </td>
                                            <td class="text-center" style="color: darkred;">
                                                <i title="Xóa"
                                                   onclick="cms_delete_receipt_in_order(<?php echo $data['_order']['ID'] . ',' . $receipt['ID']; ?>)"
                                                   class="fa fa-trash-o"></i>
                                            </td>
                                        </tr>
                                    <?php endforeach; endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
