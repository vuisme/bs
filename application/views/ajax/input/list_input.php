<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th></th>
            <th class="text-center">Mã phiếu nhập</th>
            <th class="text-center hidden-xs">Kho nhập</th>
            <th class="text-center hidden-xs">Tình trạng</th>
            <th class="text-center">Ngày nhập</th>
            <th class="text-center hidden-xs">Người nhập</th>
            <th class="text-center">Tổng tiền</th>
            <th class="text-center"><i class="fa fa-clock-o"></i> Nợ</th>
            <th></th>
            <th class="text-center hidden-xs"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                                class="checkbox chkAll"><span
                            style="width: 15px; height: 15px;"></span></label></th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($_list_input) && count($_list_input)) :
            foreach ((array)$_list_input as $key => $item) :
                $list_products = json_decode($item['detail_input'], true);
                ?>
                <tr>
                    <td style="text-align: center;">
                        <i style="color: #478fca!important;" title="Chi tiết phiếu nhập"
                           onclick="cms_show_detail_input(<?php echo $item['ID']; ?>)"
                           class="fa fa-plus-circle i-detail-input-<?php echo $item['ID'] ?>">
                        </i>
                        <i style="color: #478fca!important;" title="Chi tiết phiếu nhập"
                           onclick="cms_show_detail_input(<?php echo $item['ID']; ?>)"
                           class="fa fa-minus-circle i-hide i-detail-input-<?php echo $item['ID'] ?>">

                        </i>
                    </td>
                    <td class="text-center" style="color: #2a6496; cursor: pointer;"
                        onclick="cms_detail_input(<?php echo $item['ID']; ?>)">
                        <?php echo $item['input_code'];

                        if ($item['order_id'] > 0) {
                            echo '<br>(' . cms_getcustomernamebyorderid($item['order_id'])['output_code'] . ')';
                        }
                        ?></td>
                    <td class="text-center hidden-xs"><?php echo cms_getNamestockbyID($item['store_id']); ?></td>
                    <td class="text-center hidden-xs"><?php echo cms_getNamestatusbyID($item['input_status']); ?></td>
                    <td class="text-center"><span
                                class="hidden visible-xs"><?php echo cms_ConvertDate($item['input_date']); ?></span><span
                                class="hidden-xs"><?php echo cms_ConvertDateTime($item['input_date']); ?></span></td>
                    <td class="text-center hidden-xs"><?php echo cms_getNameAuthbyID($item['user_init']); ?></td>
                    <td class="text-center"><?php echo cms_encode_currency_format($item['total_money']); ?></td>
                    <td class="text-center"><?php echo cms_encode_currency_format($item['lack']); ?></td>
                    <td class="text-center">
                        <?php if ($item['deleted'] == 0) {
                            if ($item['order_id'] == 0) { ?>
                                <i title="Sửa" onclick="cms_edit_input(<?php echo $item['ID']; ?>)"
                                   class="fa fa-pencil-square-o"
                                   style="margin-right: 5px;">
                                </i>
                                <?php
                            }
                        } ?>

                        <i title="In" onclick="cms_print_input(3,<?php echo $item['ID']; ?>)"
                           class="fa fa-print blue"
                           style="margin-right: 5px;"></i>
                        <i class="fa fa-trash-o" style="color: darkred;" title="<?php if ($option == 1)
                            echo 'Xóa vĩnh viễn';
                        else
                            echo 'Xóa' ?>"
                           onclick="<?php if ($option == 1)
                               echo 'cms_del_import';
                           else
                               echo 'cms_del_temp_import' ?>(<?php echo $item['ID'] . ',' . $page; ?>)"></i></td>
                    <td class="text-center hidden-xs"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                                        value="<?php echo $item['ID']; ?>"
                                                                                                        class="checkbox chk"><span
                                    style="width: 15px; height: 15px;"></span></label>
                    </td>
                </tr>
                <tr class="tr-hide" id="tr-detail-input-<?php echo $item['ID'] ?>">
                    <td colspan="15">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab">
                                        <i class="green icon-reorder bigger-110"></i>
                                        Chi tiết phiếu nhập
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    <div class="alert alert-success clearfix" style="display: flex;">
                                        <div>
                                            <i class="fa fa-cart-arrow-down">
                                            </i>
                                            <span
                                                    class="hidden-768">Số lượng SP:
                                        </span>
                                            <label><?php echo $item['total_quantity']; ?>
                                            </label>
                                        </div>
                                        <div class="padding-left-10">
                                            <i class="fa fa-dollar">
                                            </i>
                                            <span
                                                    class="hidden-768">Tiền hàng:
                                        </span>
                                            <label><?php echo cms_encode_currency_format($item['total_price']); ?>
                                            </label>
                                        </div>
                                        <div class="padding-left-10">
                                            <i class="fa fa-dollar">
                                            </i>
                                            <span
                                                    class="hidden-768">Giảm giá:
                                        </span>
                                            <label><?php echo cms_encode_currency_format($item['discount']); ?>
                                            </label>
                                        </div>
                                        <div class="padding-left-10">
                                            <i class="fa fa-dollar">
                                            </i>
                                            <span
                                                    class="hidden-768">Tổng tiền:
                                        </span>
                                            <label><?php echo cms_encode_currency_format($item['total_money']); ?>
                                            </label>
                                        </div>
                                        <div class="padding-left-10">
                                            <i class="fa fa-clock-o"></i>
                                            <span class="hidden-768">Còn nợ: </span>
                                            <label
                                            ><?php echo cms_encode_currency_format($item['lack']); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <table class="table table-striped table-bordered table-hover dataTable">
                                        <thead>
                                        <tr role="row">
                                            <th class="text-center hidden-xs">STT</th>
                                            <th class="text-left hidden-xs">Mã SP</th>
                                            <th class="text-left">Tên SP</th>
                                            <th class="text-center hidden-xs">Hình ảnh</th>
                                            <th class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">Ngày
                                                hết
                                                hạn
                                            </th>
                                            <th class="text-center">SL</th>
                                            <th class="text-center hidden-xs">ĐVT</th>
                                            <th class="text-center">Giá nhập</th>
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
                                                <td class="text-center zoomin hidden-xs"><img height="30"
                                                                                              src="public/templates/uploads/<?php echo cms_show_image($_product['prd_image_url']); ?>">
                                                </td>
                                                <td class="text-center <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>"><?php echo $product['expire']; ?></td>
                                                <td class="text-center">
                                                    <?php echo $_product['quantity']; ?>
                                                </td>
                                                <td class="text-center hidden-xs">
                                                    <?php echo $_product['prd_unit_name']; ?>
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
</div>

<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">
        Tổng số phiếu nhập:
        <span><?php echo (isset($total_input['quantity'])) ? $total_input['quantity'] : 0; ?></span>
        Tổng tiền:
        <span><?php echo isset($total_input['total_money']) ? cms_encode_currency_format($total_input['total_money']) : 0; ?></span>
        Tổng nợ:
        <span><?php echo isset($total_input['total_debt']) ? cms_encode_currency_format($total_input['total_debt']) : 0; ?></span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>

