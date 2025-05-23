<div class="row">
    <div class="report">
        <div class="col-md-12 col-xs-12"><h4 class="dashboard-title"><i class="fa fa-signal"></i>Hoạt động hôm nay</h4>
        </div>
        <div class="col-md-4">
            <div class="report-box box-green">
                <div class="infobox-icon">
                    <i class="fa fa-signal"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title">Tiền bán hàng</h3>
                    <span
                            class="infobox-data-number text-center"><?php echo (isset($tongtien)) ? cms_encode_currency_format($tongtien) : '0'; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-box box-blue">
                <div class="infobox-icon">
                    <i class="fa fa-shopping-cart"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title">Số đơn hàng :</h3>
                    <span
                            class="infobox-data-number text-center"><?php echo (isset($slsorders)) ? cms_encode_currency_format($slsorders) : '0'; ?></span>

                    <h3 class="infobox-title">SL SP :</h3>
                    <span
                            class="infobox-data-number text-center"><?php echo (isset($slsitem)) ? cms_encode_currency_format($slsitem) : '0'; ?></span>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="report-box box-red">
                <div class="infobox-icon">
                    <i class="fa fa-undo"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title">Số đơn trả :</h3>
                    <span
                            class="infobox-data-number text-center"><?php echo (isset($return_number)) ? cms_encode_currency_format($return_number) : '0'; ?></span>

                    <h3 class="infobox-title">SL SP :</h3>
                    <span
                            class="infobox-data-number text-center"><?php echo (isset($return_quantity)) ? cms_encode_currency_format($return_quantity) : '0'; ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row" style="background: #efefef; margin: 20px 0; overflow: hidden; ">
    <div class="col-md-4">
        <div class="widget widget-blue">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fa fa-signal"></i>Hoạt động</h3>
            </div>
            <div class="widget-body">
                <div class="row">
                    <div class="info col-xs-7">Tiền bán hàng</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($tongtien)) ? cms_encode_currency_format($tongtien) : '0'; ?></div>
                    <div class="info col-xs-7">Đơn bán / Đơn trả</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($slsorders)) ? cms_encode_currency_format($slsorders) : '0'; ?>
                        / <?php echo (isset($return_number)) ? cms_encode_currency_format($return_number) : '0'; ?></div>
                    <div class="info col-xs-7">SL bán / SL trả</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($slsitem)) ? cms_encode_currency_format($slsitem) : '0'; ?>
                        / <?php echo (isset($return_quantity)) ? cms_encode_currency_format($return_quantity) : '0'; ?></div>
                    <div class="info col-xs-7">Tiền trả hàng</div>
                    <div class="info col-xs-5 data text-right"><?php echo (isset($return_money)) ? cms_encode_currency_format($return_money) : '0'; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget widget-orange">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fa fa-tags"></i>Thông tin kho</h3>
            </div>
            <div class="widget-body">
                <div class="row">
                    <div class="info col-xs-7">Tồn kho</div>
                    <div class="info col-xs-5 data text-right href" onclick="cms_show_product_available()">
                        <?php echo (isset($prd_available)) ? cms_encode_currency_format($prd_available) : '0'; ?></div>
                    <div class="info col-xs-7">Hết Hàng</div>
                    <div class="info col-xs-5 data text-right href" onclick="cms_show_product_empty()">
                        <?php echo (isset($prd_empty)) ? cms_encode_currency_format($prd_empty) : '0'; ?></div>
                    <div class="info col-xs-7">Sắp hết hàng</div>
                    <div class="info col-xs-5 data text-right href" onclick="cms_show_product_min()">
                        <?php echo (isset($prd_min)) ? $prd_min : '0'; ?></div>
                    <div class="info col-xs-7">Vượt định mức</div>
                    <div class="info col-xs-5 data text-right href" onclick="cms_show_product_max()">
                        <?php echo (isset($prd_max)) ? $prd_max : '0'; ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="widget widget-green">
            <div class="widget-header">
                <h3 class="widget-title"><i class="fa fa-barcode"></i>Thông tin sản phẩm</h3>
            </div>
            <div class="widget-body">
                <div class="row">
                    <div class="info col-xs-7">Sản phẩm/Nhà sản xuất</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($data['_sl_product'])) ? $data['_sl_product'] : 0; ?>
                        /<?php echo (isset($data['_sl_manufacture'])) ? $data['_sl_manufacture'] : 0; ?></div>
                    <div class="info col-xs-7">Chưa làm giá bán</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($lamgiaban)) ? cms_encode_currency_format($lamgiaban) : '0'; ?></div>
                    <div class="info col-xs-7">Chưa nhập giá mua</div>
                    <div
                            class="info col-xs-5 data text-right"><?php echo (isset($lamgiamua)) ? cms_encode_currency_format($lamgiamua) : '0'; ?></div>
                    <div class="info col-xs-7">Hàng chưa phân loại</div>
                    <div class="info col-xs-5 data text-right">0</div>
                </div>
            </div>
        </div>
    </div>
</div>
