<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class="text-center">Mã SP</th>
            <th class="text-center">Khách mua</th>
            <th class="text-center">Tên SP</th>
            <th class="text-center hidden-xs">Hình ảnh</th>
            <th class="text-center">Tồn</th>
            <th class="text-center">Giá lẻ</th>
            <th class="text-center hidden-xs">Giá sỉ</th>
            <th class="text-center hidden-xs">Danh mục</th>
            
            <th></th>
            <th class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                      class="checkbox chkAll"><span
                            style="width: 15px; height: 15px;"></span></label></th>
        </tr>
        </thead>
        <tbody id="list_product_show">
        <?php if (isset($data['_list_product']) && count($data['_list_product'])) :
            foreach ((array)$data['_list_product'] as $key => $item) : ?>
                <tr class="product_change" data-id="<?php echo $item['ID']; ?>">
                    <td class="text-center"><?php echo $item['prd_code']; ?></td>
                    <td style="text-align: center;">

            
                            <i style="color: #478fca!important;" title="Xem khách mua hàng"
                               onclick="cms_show_detail_order(<?php echo $item['ID']; ?>)"
                               class="fa fa-plus-circle i-detail-order-<?php echo $item['ID'] ?>">
                            </i>
                            <i style="color: #478fca!important;" title="Xem khách mua hàng"
                               onclick="cms_show_detail_order(<?php echo $item['ID']; ?>)"
                               class="fa fa-minus-circle i-hide i-detail-order-<?php echo $item['ID'] ?>">
                            </i>
             

                    </td>
                    <td class="text-left prd_name"
                        onclick="cms_detail_product(<?php echo $item['ID']; ?>)"
                        style="color: #2a6496; cursor: pointer;"><?php echo $item['prd_name']; ?></td>
                    <td class="text-center zoomin hidden-xs">

                        <?php if ($item['prd_image_url'] != '') {
                            ?>
                            <img height="30"
                                 src="public/templates/uploads/<?php echo cms_show_image($item['prd_image_url']); ?>">
                            <?php
                        } else {
                            ?>
                            <img id="product_review_<?php echo $item['ID']; ?>">
                            <form id="image_upload_form_<?php echo $item['ID']; ?>" method="post"
                                  enctype="multipart/form-data" class="hidden"
                                  action='product/upload_img/<?php echo $item['ID']; ?>' autocomplete="off">
                                <div id='product_image_<?php echo $item['ID']; ?>' style="display: none;"></div>
                                <div>
                                    <a class="btn btn-blue">
                                        <input type="file" name="photo" id="photo_<?php echo $item['ID']; ?>"
                                               class="product_image product_image_<?php echo $item['ID']; ?>"/>
                                        <input type="input" name="product_id" value="<?php echo $item['ID']; ?>"
                                               class="hidden"/>
                                    </a>
                                </div>
                            </form>

                            <span class="href" id="product_hide_<?php echo $item['ID']; ?>"
                                  onclick="$('.product_image_<?php echo $item['ID']; ?>').trigger('click')">Up hình</span>

                            <?php
                        } ?>


                    </td>
                    <td class="text-center"><?php echo cms_getInventory($item['ID'], $data['store_id']); ?></td>
                    <td class="text-right"
                        style="font-weight: bold;"><?php echo cms_encode_currency_format($item['prd_sell_price']); ?></td>
                    <td class="text-right hidden-xs"
                        style="font-weight: bold;"><?php echo cms_encode_currency_format($item['prd_sell_price2']); ?></td>
                    <td class="hidden-xs"><?php echo cms_getNamegroupbyID($item['prd_group_id']); ?></td>
                    <!--<td class="hidden-xs"><?php echo cms_getNamemanufacturebyID($item['prd_manufacture_id']); ?></td>-->
                    
                    <td class="text-center">
                        <i title="Copy" onclick="cms_clone_product(<?php echo $item['ID']; ?>);"
                           class="fa fa-files-o blue"
                           style="margin-right: 5px;"></i>
                        <?php
                        if (isset($data['option'])) {
                            if ($data['option'] == 1) {
                                ?>
                                <i title="Khôi phục" class="fa fa-repeat"
                                   onclick="cms_restore_product_deactivated(<?php echo $item['ID'] . ',' . $data['page']; ?>);"
                                   style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                                <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                                   onclick="cms_delete_product(<?php echo $item['ID'] . ',' . $data['page']; ?>)"></i>
                                <?php
                            } elseif ($data['option'] == 2) {
                                ?>
                                <i title="Khôi phục" class="fa fa-repeat"
                                   onclick="cms_restore_product_deleted(<?php echo $item['ID'] . ',' . $data['page']; ?>);"
                                   style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                                <i class="fa fa-trash" style="color: darkred;" title="Xóa vĩnh viễn"
                                   onclick="cms_delete_forever_product(<?php echo $item['ID'] . ',' . $data['page']; ?>)"></i>
                                <?php
                            } else {
                                ?>
                                <i title="Ngừng kinh doanh" class="fa fa-pause"
                                   onclick="cms_deactivate_product(<?php echo $item['ID'] . ',' . $data['page']; ?>);"
                                   style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                                <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                                   onclick="cms_delete_product(<?php echo $item['ID'] . ',' . $data['page']; ?>)"></i>
                                <?php
                            }
                        } else {
                            ?>
                            <i title="Ngừng kinh doanh" class="fa fa-pause"
                               onclick="cms_deactivate_product(<?php echo $item['ID'] . ',' . $data['page']; ?>);"
                               style="margin-right: 5px; color: #C6699F; cursor: pointer;"></i>
                            <i class="fa fa-trash-o" style="color: darkred;" title="Xóa"
                               onclick="cms_delete_product(<?php echo $item['ID'] . ',' . $data['page']; ?>)"></i>
                            <?php
                        }

                        ?>
                    </td>
                    <td class="text-center"><label class="checkbox" style="margin: 0;"><input type="checkbox"
                                                                                              value="<?php echo $item['ID']; ?>"
                                                                                              class="checkbox chk checkbox_value"><span
                                    style="width: 15px; height: 15px;"></span></label>
                    </td>
                    
                </tr>
                <tr class="tr-hide" id="tr-detail-order-<?php echo $item['ID'] ?>">
                    <td colspan="15">
                        <div class="tabbable">
                            <ul class="nav nav-tabs">
                                <li class="active">
                                    <a data-toggle="tab">
                                        <i class="green icon-reorder bigger-110"></i>
                                        Danh sách khách mua
                                    </a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane active">
                                    

                                    <table class="table table-striped table-bordered table-hover dataTable">
                                        <thead>
                                        <tr role="row">
                                            <th class="text-center hidden-xs">STT</th>
                                            <th class="text-left hidden-xs">Mã đơn hàng</th>
                                            <th class="text-left">Tên Khách Hàng</th>
                                            <th class="text-center">Ngày mua</th>
                                            <th class="text-center">Chi tiết đơn hàng</th>
                                            <th class="text-center">Trạng thái</th>
                                            
                                        </tr>
                                        </thead>
                                        <tbody>
                                           <?php
                                               $queue = 1;
                                               $order = $this->db
                                                    ->select('*')
                                                    ->from('orders')
                                                    ->like('detail_order','"id":"' . $item['ID'] . '"')
                                                    ->get()
                                                    ->result_array();
                                                $debt = $order;
                                                //print_r($order);
                                                foreach ($order as $row)
                                                { ?>
                                                    <tr>
                                                        <td class="text-center width-5 hidden-xs">
                                                            <?php echo $queue++; ?>
                                                        </td>
                                                        <td class="text-left hidden-xs">
                                                            <?php echo $row['output_code']; ?>
                                                        </td>
                                                        <td class="text-left">
                                                            <?php echo cms_getNamecustomerbyID($row['customer_id']); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo cms_ConvertDate($row['sell_date']); ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php
                                                            $list_products = json_decode($row['detail_order'], true);
                                                            foreach ((array)$list_products as $product) {
                                                                $_product = cms_finding_productbyID($product['id']);
                                                                $_product['quantity'] = isset($product['quantity']) ? $product['quantity'] : 0;
                                                                $_product['price'] = isset($product['price']) ? $product['price'] : 0;
                                                                $_product['expire'] = isset($product['expire']) ? $product['expire'] : '';
                                                                $_product['list_serial'] = isset($product['list_serial']) ? $product['list_serial'] : '';
                                                                echo $_product['prd_name'] .' - SL: ' . $_product['quantity'] .' - Giá bán: ' .cms_encode_currency_format($_product['price']) ."\r\n";
                                                                echo nl2br("\n");
                                                            }
                                                            ?>
                                                        </td>
                                                        <td class="text-center">
                                                            <?php echo cms_getNamestatusbyID($row['order_status']); ?>
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

<div id="list_product_change_show" class="row text-right" style="padding-right: 20px;display: none;">
    <?php
    if (isset($data['option'])) {
        if ($data['option'] == 1) {
            ?>
            <button type="button" class="save btn btn-primary"
                    onclick="cms_restore_all_product_deactivated(<?php echo $data['page']; ?>)"><i
                        class="fa fa-repeat"></i> Khôi phục sản phẩm đang chọn
            </button>

            <button type="button" class="save btn btn-primary"
                    onclick="cms_delete_all_product(<?php echo $data['page']; ?>)"><i
                        class="fa fa-trash-o"></i> Xóa sản phẩm đang chọn
            </button>

            <?php
        } elseif ($data['option'] == 2) {
            ?>
            <button type="button" class="save btn btn-primary"
                    onclick="cms_restore_all_product_deleted(<?php echo $data['page']; ?>)"><i
                        class="fa fa-repeat"></i> Khôi phục sản phẩm đang chọn
            </button>

            <button type="button" class="save btn btn-primary"
                    onclick="cms_delete_forever_all_product(<?php echo $data['page']; ?>)"><i
                        class="fa fa-trash-o"></i> Xóa vĩnh viễn sản phẩm đang chọn
            </button>

            <?php
        } else {
            ?>
            <button type="button" class="save btn btn-primary"
                    onclick="cms_deactivate_all_product(<?php echo $data['page']; ?>)"><i
                        class="fa fa-pause"></i> Ngừng kinh doanh sản phẩm đang chọn
            </button>

            <button type="button" class="save btn btn-primary"
                    onclick="cms_delete_all_product(<?php echo $data['page']; ?>)"><i
                        class="fa fa-trash-o"></i> Xóa sản phẩm đang chọn
            </button>
            <?php
        }
    } else {
        ?>
        <button type="button" class="save btn btn-primary"
                onclick="cms_deactivate_all_product(<?php echo $data['page']; ?>)"><i
                    class="fa fa-pause"></i> Ngừng kinh doanh sản phẩm đang chọn
        </button>

        <button type="button" class="save btn btn-primary"
                onclick="cms_delete_all_product(<?php echo $data['page']; ?>)"><i
                    class="fa fa-trash-o"></i> Xóa sản phẩm đang chọn
        </button>
        <?php
    }
    ?>
</div>
<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0">Tổng số sản phẩm
        <span><?php echo (isset($data['_sl_product'])) ? $data['_sl_product'] : 0; ?></span></div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>

<script>
    $('input.product_image').on('change', function () {
        $product_id = $(this).parents('.product_change').attr('data-id');
        $("#product_image_" + $product_id).html('');
        $("#image_upload_form_" + $product_id).ajaxForm(
            {
                target: '#product_image_' + $product_id,
                success: function () {
                    if ($.trim($("#product_image_" + $product_id).text()) != '') {
                        $("#product_review_" + $product_id).attr("src", 'public/templates/uploads/' + $("#product_image_" + $product_id).text());
                        $("#product_hide_" + $product_id).hide();
                    }
                }
            }
        ).submit();
    });
</script>