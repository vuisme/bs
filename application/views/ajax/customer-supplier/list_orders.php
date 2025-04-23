<div class="col-md-12 col-xs-12">
    <div class="col-md-4 col-xs-6 padd-0">
        <div class="left-action text-left clearfix ">
            <h3 id="order_info" class="padd-0 no-margin" style="margin-top: 10px;">Đơn hàng</h3>
        </div>
    </div>
    <div class="col-md-8 col-xs-6 padd-0">
        <div class="right-action text-right">
            <div class="btn-groups">

            </div>
        </div>
    </div>
</div>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th></th>
        <th class="text-center">Mã đơn</th>
        <th class="text-center hidden-xs">Kho xuất</th>
        <th class="text-center">Ngày bán</th>
        <th class="text-center hidden-xs">Thu ngân</th>
        <th class="text-center hidden-xs">Tình trạng</th>
        <th class="text-center hidden-xs">Tổng SL</th>
        <th class="text-center">Tổng tiền</th>
        <th class="text-center"><i class="fa fa-clock-o"></i> Nợ</th>
        <th></th>
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
                    <?php } ?>
                </td>
                <td class="text-center" style="color: #2a6496; cursor: pointer;"
                    onclick="cms_detail_order_in_customer(<?php echo $item['ID']; ?>)"><?php echo $item['output_code']; ?></td>
                <td class="text-center hidden-xs"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                <td class="text-center"><span
                            class="hidden visible-xs"><?php echo cms_ConvertDate($item['sell_date']); ?></span><span
                            class="hidden-xs"><?php echo cms_ConvertDateTime($item['sell_date']); ?></span></td>
                <td class="text-center hidden-xs"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>

                <td class="text-center hidden-xs"><?php echo cms_getNamestatusbyID($item['order_status']); ?></td>
                <td class="text-center hidden-xs"><?php echo($item['total_quantity']); ?>
                </td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']); ?>
                </td>
                <td class="text-center"><?php echo cms_encode_currency_format($item['lack']); ?>
                </td>
                <td class="text-center"><i title="In"
                                           onclick="cms_print_order(1,<?php echo $item['ID']; ?>)"
                                           class="fa fa-print blue"
                                           style="margin-right: 5px;"></i>

                    <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                       onclick="cms_del_order_in_customer(<?php echo $item['ID'] . ',' . $page; ?>)"></i></td>
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
        echo '<tr><td colspan="90" class="text-center">Không có dữ liệu</td></tr>';
    endif;

    ?>
    </tbody>
</table>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Tổng số hóa đơn: <span><?php echo (isset($total_orders['quantity'])) ? $total_orders['quantity'] : 0; ?></span>
        Tổng tiền:
        <span><?php echo cms_encode_currency_format((isset($total_orders['total_money']) ? $total_orders['total_money'] : 0)); ?></span>
        Tổng nợ:
        <span><?php echo cms_encode_currency_format((isset($total_orders['total_debt']) ? $total_orders['total_debt'] : 0)); ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>
