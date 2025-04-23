<div class="quick-info report row" style="margin-bottom: 15px;">
    <div class="col-md-12 col-xs-12 padd-0">
        <div class="col-md-2 padd-right-0">
            <div class="report-box " style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-refresh orange" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title orange"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_money']) ? $total_orders['total_money'] : 0)); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Doanh số</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-undo blue" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title blue"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['return_money']) ? $total_orders['return_money'] : 0)); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Trả hàng</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-dollar cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($total_orders['total_origin_price']) ? $total_orders['total_origin_price'] : 0)); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Tiền vốn</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-inbox blue" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title blue" style="font-size: 25px;">
                        <?php echo cms_encode_currency_format((isset($receipt['total_money']) ? $receipt['total_money'] : 0)); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Tổng thu</span>
                </div>
            </div>
        </div>
        <div class="col-md-2 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-paypal cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format((isset($payment['total_money']) ? $payment['total_money'] : 0)); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center" style="font-size: 14px; color: #555;">Tổng chi</span>
                </div>
            </div>
        </div>

        <div class="col-md-2 padd-right-0">
            <div class="report-box " style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-dollar orange" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title orange"
                        style="font-size: 25px;"><?php echo cms_encode_currency_format($total_orders['total_money'] + $receipt['total_money'] - $payment['total_money'] - $total_orders['total_origin_price'] - $total_orders['return_money']); ?></h3>
                    <br>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Lợi nhuận</span>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="table-responsive">

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th></th>
            <th class="text-center">Mã đơn hàng</th>
            <th class="text-center hidden-xs">Kho xuất</th>
            <th class="text-center">Ngày bán</th>
            <th class="text-center hidden-xs">Thu ngân</th>
            <th class="text-center">Khách hàng</th>
            <th class="text-center hidden-xs">SL</th>
            <th class="text-center hidden-xs">Chiết khấu</th>
            <th class="text-center">Doanh số</th>
            <th class="text-center hidden-xs">Tiền vốn</th>
            <th class="text-center">Lợi nhuận</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($_list_orders) && count($_list_orders)) :
            foreach ((array)$_list_orders as $key => $item) :
                $list_products = json_decode($item['detail_order'], true);
                ?>
                <tr>
                    <td style="text-align: center;">
                        <?php if ($list_products != NULL) { ?>
                            <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                               onclick="cms_show_detail_order(<?php echo $item['ID']; ?>)"
                               class="fa fa-plus-circle i-detail-order-<?php echo $item['ID'] ?>">
                            </i>
                            <i style="color: #478fca!important;" title="Chi tiết đơn hàng"
                               onclick="cms_show_detail_order(<?php echo $item['ID']; ?>)"
                               class="fa fa-minus-circle i-hide i-detail-order-<?php echo $item['ID'] ?>">
                            </i>
                        <?php } ?>
                    </td>
                    <td class="text-center" style="color: #2a6496; cursor: pointer;"
                        onclick="cms_detail_order(<?php echo $item['ID']; ?>)"><?php echo $item['output_code']; ?></td>
                    <td class="text-center hidden-xs"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                    <td class="text-center"><span
                                class="hidden visible-xs"><?php echo cms_ConvertDate($item['sell_date']); ?></span><span
                                class="hidden-xs"><?php echo cms_ConvertDateTime($item['sell_date']); ?></span></td>
                    <td class="text-center hidden-xs"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                    <td class="text-center"><?php echo cms_getNamecustomerbyID($item['customer_id']); ?></td>
                    <td class="text-center hidden-xs"><?php echo $item['total_quantity']; ?></td>
                    <td class="text-center hidden-xs"><?php echo cms_encode_currency_format($item['coupon']); ?></td>
                    <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                    <td class="text-center hidden-xs"><?php echo cms_encode_currency_format($item['total_origin_price']); ?></td>
                    <td class="text-center"><?php echo cms_encode_currency_format($item['total_money'] - $item['total_origin_price']); ?></td>
                </tr>
                <tr class="tr-hide" id="tr-detail-order-<?php echo $item['ID'] ?>">
                    <td colspan="15">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab">
                                        <i class="green icon-reorder bigger-110"></i>
                                        Chi tiết đơn hàng
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    <div class="alert alert-success clearfix" style="display: flex;">

                                        <div class="padding-left-10">
                                            <i class="fa fa-sticky-note-o">
                                            </i>
                                            <span
                                                    class="hidden-768">Ghi chú:
                                        </span>
                                            <label><?php echo cms_encode_currency_format($item['notes']); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-bordered table-hover dataTable">
                                        <thead>
                                        <tr role="row">
                                            <th class="text-center hidden-xs">STT</th>
                                            <th class="text-left hidden-xs">Mã SP</th>
                                            <th class="text-left">Tên SP</th>
                                            <th class="text-center">SL</th>
                                            <th class="text-center">Đơn giá</th>
                                            <th class="text-center">Thành tiền</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                        $queue = 1;
                                        foreach ((array)$list_products as $product) {
                                            $_product = cms_finding_productbyID($product['id']);
                                            $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                                            $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                                            $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                                            $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';
                                            ?>
                                            <tr>
                                                <td class="text-center width-5 hidden-xs">
                                                    <?php echo $queue++; ?>
                                                </td>
                                                <td class="text-left hidden-xs">
                                                    <?php echo $_product['prd_code']; ?>
                                                </td>
                                                <td class="text-left">
                                                    <?php echo $_product['prd_name']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo $_product['quantity']; ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo cms_encode_currency_format($_product['price']); ?>
                                                </td>
                                                <td class="text-center">
                                                    <?php echo cms_encode_currency_format($_product['price'] * $_product['quantity']); ?>
                                                </td>
                                            </tr>
                                            <?php
                                        }
                                        ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            <?php endforeach;
        else :
            echo '<tr><td colspan="100" class="text-center">Không có dữ liệu</td></tr>';
        endif;
        ?>
        </tbody>
    </table>
</div>

<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>
