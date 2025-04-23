<div class="table-responsive">

    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class="text-center hidden-xs">Hình ảnh</th>
            <th class="text-center hidden-xs">Nhóm KH</th>
            <th class="text-center">Tên KH</th>
            <th class="text-center">Điện thoại</th>
            <th class="text-center">Địa chỉ</th>
            <th class="text-center">Email/Facebook</th>
            <th class="text-center hidden-xs">Bản đồ</th>
            <th class="text-center hidden-xs">Lần cuối mua hàng</th>
            <th class="text-center hidden-xs">Tổng tiền hàng</th>
            <th class="text-center">Công nợ</th>
            <th></th>
        </tr>
        </thead>
        <tbody class="ajax-loadlist-customer">
        <?php if (isset($_list_customer) && count($_list_customer)) :
            foreach ((array)$_list_customer as $key => $item) :
                ?>
                <tr id="tr-item-<?php echo $item['ID']; ?>">
                    <td class="text-center zoomin hidden-xs">
                        <img height="30"
                             src="public/templates/uploads/<?php echo cms_show_image($item['customer_image']); ?>">
                    </td>
                    <td class="text-center hidden-xs">
                        <?php echo $item['customer_group'] == '0' ? 'Khách lẻ' : 'Khách sỉ'; ?>
                    </td>
                    <!--<td onclick="cms_detail_customer(<?php echo $item['ID']; ?>)"
                    //    class="text-center tr-detail-item hidden-xs"
                    //    style="cursor: pointer; color: #1b6aaa;"><?php echo $item['customer_code']; ?></td> -->
                    <td onclick="cms_detail_customer(<?php echo $item['ID']; ?>)" class="text-center tr-detail-item"
                        style="cursor: pointer; color: #1b6aaa;"><?php echo $item['customer_name']; ?></td>
                    <td class="text-center"><?php echo (!empty($item['customer_phone'])) ? $item['customer_phone'] :
                            '-'; ?></td>
                    <td class="text-center"><?php echo $item['customer_addr'] . cms_getFullAddress($item['ward_id'], $item['district_id'], $item['province_id']); ?></td>
                    <td class="text-center tr-detail-item"
                        style="cursor: pointer; color: #1b6aaa;"><?php echo $item['customer_email']; ?>
                    <td class="text-center hidden-xs">
                        <?php if ($item['customer_map'] != '') {
                            ?>
                            <a style="color: #0B87C9" class="href" target="_blank"
                               href="<?php echo $item['customer_map']; ?>">Bản đồ</a>
                            <?php
                        } ?>
                    </td>
                    <td class="text-center hidden-xs"><?php echo cms_ConvertDateTime($item['last_sell_date']); ?></td>
                    <td class="text-right hidden-xs"
                    ><?php echo (!empty($item['total_money_order'])) ? cms_encode_currency_format($item['total_money_order']) :
                            '-'; ?></td>
                    <td class="text-right"><?php echo (!empty($item['customer_debt'])) ? cms_encode_currency_format($item['customer_debt']) :
                            '-'; ?></td>
                    <td class="text-center">
                        <i class="fa fa-trash-o" style="cursor:pointer;"
                           onclick="cms_delCustomer(<?php echo $item['ID'] . ',' . $page; ?>);"></i>
                    </td>
                </tr>
            <?php
            endforeach;
        else: ?>
            <tr>
                <td colspan="80" class="text-center">Không có dữ liệu</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="ajax-loadlist-total sm-info pull-left padd-0">
        Số khách
        hàng:<span><?php echo (isset($_total_customer['total_quantity']) && !empty($_total_customer['total_quantity'])) ? $_total_customer['total_quantity'] : '0'; ?></span>
        Tổng tiền:
        <span><?php echo (isset($_total_customer['total_money_order']) && !empty($_total_customer['total_money_order'])) ? cms_encode_currency_format($_total_customer['total_money_order']) : '0'; ?> đ</span>
        Tổng nợ:
        <span><?php echo (isset($_total_customer['total_customer_debt']) && !empty($_total_customer['total_customer_debt'])) ? cms_encode_currency_format($_total_customer['total_customer_debt']) : '0'; ?> đ</span>
    </div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>


