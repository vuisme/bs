<button type="button" class="save btn btn-default btn-hide-edit" onclick="cms_paging_input_by_supplier_id(1)"><i
            class="fa fa-arrow-left"></i>Thoát
</button>
<div class="row">
    <div class="col-md-8">
        <table class="table table-bordered table-striped" style="margin-top: 30px;">
            <thead>
            <tr>
                <th class="text-center hidden-xs">STT</th>
                <th class="text-left hidden-xs">Mã SP</th>
                <th class="text-left">Tên SP</th>
                <th class="text-center">SL</th>
                <th class="text-center">Giá nhập</th>
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
                        <td class="text-left"><?php echo $product['prd_name']; ?></td>
                        <td class="text-center" style="max-width: 60px;"><?php echo $product['quantity']; ?> </td>
                        <td class="text-center price-order"><?php echo cms_encode_currency_format($product['price']); ?></td>
                        <td class="text-center total-money"><?php echo cms_encode_currency_format($product['price'] * $product['quantity']); ?></td>
                    </tr>
                <?php endforeach; endif; ?>
            </tbody>
        </table>
    </div>
    <div class="col-md-4">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="morder-info" style="padding: 4px;">
                    <div class="tab-contents" style="padding: 8px 6px;">
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Mã phiếu</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <?php echo $data['_import']['input_code']; ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Nhà cung cấp</label>
                            </div>
                            <div class="col-md-8 padd-0" style="font-style: italic;">
                                <?php echo cms_getNamesupplierbyID($data['_import']['supplier_id']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Ngày nhập</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <?php echo cms_ConvertDateTime($data['_import']['input_date']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Người nhập</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <?php echo cms_getNameAuthbyID($data['_import']['user_init']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Ghi chú</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                                            <textarea readonly id="note-order" cols=""
                                                                      class="form-control" rows="3"
                                                                      style="border-radius: 0;"><?php echo $data['_import']['notes']; ?></textarea>

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
                                <div>
                                    <?php echo cms_getNameReceiptMethodByID($data['_import']['payment_method']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Tiền hàng</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <div>
                                    <?php echo cms_encode_currency_format($data['_import']['total_money']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Chiết khấu</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <?php echo cms_encode_currency_format($data['_import']['discount']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Tổng cộng</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <div>
                                    <?php echo cms_encode_currency_format((int)str_replace(",", "", $data['_import']['total_money']) - $data['_import']['discount']); ?>
                                </div>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Thanh toán</label>
                            </div>
                            <div class="col-md-8 orange padd-0">
                                <?php echo cms_encode_currency_format($data['_import']['payed']); ?>
                            </div>
                        </div>
                        <div class="form-group marg-bot-10 clearfix">
                            <div class="col-md-4 col-xs-4 padd-0">
                                <label>Còn nợ</label>
                            </div>
                            <div class="col-md-8 col-xs-8 padd-0">
                                <div>
                                    <?php echo cms_encode_currency_format($data['_import']['lack']); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
