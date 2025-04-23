<?php if (isset($data['customers']) && count($data['customers'])) : ?>
    <ul class="list-unstyled">
        <?php
        foreach ((array)$data['customers'] as $key => $val) :
            ?>
            <li style="cursor: pointer;" onclick="cms_selected_cys(<?php echo $val['ID']; ?>)">
                <ul class="list-unstyled">
                    <li style="padding: 3px 10px;" class="data-cys-name-<?php echo $val['ID']; ?>"><i class="fa fa-user"
                                                                                                      style="color: #0B87C9;"
                                                                                                      aria-hidden="true"></i> <?php echo $val['customer_name']; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-barcode"
                                                      style="color: #0B87C9;"></i> <?php echo (!empty($val['customer_email'])) ? $val['customer_email'] : 'Không có'; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-phone" style="color: #0B87C9;"
                                                      aria-hidden="true"></i> <?php echo (!empty($val['customer_phone'])) ? $val['customer_phone'] : 'Không có'; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-cart-plus" style="color: #0B87C9;"
                                                      aria-hidden="true"></i> <?php echo (!empty($val['count']) || $val['count'] == 0) ? 'Đã mua ' . $val['count'] . ' đơn hàng' : 'Chưa từng mua hàng'; ?>
                    </li>
                    <li style="padding: 3px 10px;"><i class="fa fa-money" style="color: #0B87C9;"
                                                      aria-hidden="true"></i> <?php echo (!empty($val['count']) || $val['count'] == 0) ? 'Tổng ' . cms_encode_currency_format($val['total_money']) . 'đ' : 'Chưa từng mua'; ?>
                    </li>
                </ul>
            </li>
            <hr style="color: #0B87C9; margin: 10px 0;"/>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
