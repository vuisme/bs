<div class="quick-info report row" style="margin-bottom: 15px;">
    <div class="col-md-12 col-xs-12 padd-0">
        <div class="col-md-4 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-tag blue" style="font-size: 45px;" aria-hidden="true"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title blue"
                        style="font-size: 25px;"><?php echo(isset($total_sls) ? $total_sls : '0'); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Tổng tồn kho</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 padd-right-0">
            <div class="report-box " style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-refresh orange" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title orange"
                        style="font-size: 25px;"><?php echo(isset($totaloinvent) ? cms_encode_currency_format($totaloinvent) : '0'); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Vốn tồn kho</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 padd-right-0">
            <div class="report-box" style="border: 1px dotted #ddd; border-radius: 0">
                <div class="infobox-icon">
                    <i class="fa fa-shopping-cart cred" style="font-size: 45px;"></i>
                </div>
                <div class="infobox-data">
                    <h3 class="infobox-title cred"
                        style="font-size: 25px;"><?php echo(isset($totalsinvent) ? cms_encode_currency_format($totalsinvent) : '0'); ?></h3>
                    <span class="infobox-data-number text-center"
                          style="font-size: 14px; color: #555;">Giá trị tồn kho</span>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
        <tr>
            <th class="text-center hidden-xs">Mã SP</th>
            <th class="text-center">Ảnh SP</th>
            <th class="text-center">Tên SP</th>
            <th class="text-center hidden-xs" <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>>Ghi chú</th>
            <th class="text-center hidden-xs" <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>>Quà</th>
            <th class="text-center hidden-xs <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>">Ngày hết hạn</th>
            <th class="text-center">SL</th>
            <th class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>">Serial</th>
            <th class="text-center">Vốn tồn kho</th>
            <th class="text-center">Giá trị tồn</th>
        </tr>
        </thead>
        <tbody>
        <?php if (isset($data['_list_product']) && count($data['_list_product'])) :
            foreach ((array)$data['_list_product'] as $key => $item) : ?>
                <tr>
                    <td onclick="cms_show_product_history(<?php echo $item['ID']; ?>)" class="hidden-xs"
                        style="color: #2a6496; cursor: pointer;"><?php echo $item['prd_code']; ?></td>
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
                    <td class="text-left"
                        onclick="cms_show_product_history(<?php echo $item['ID']; ?>)"><?php echo $item['prd_name']; ?></td>
                    <td class="text-center hidden-xs <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>"><?php echo $item['prd_size']; ?></td>
                    <td class="text-center hidden-xs <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>"><?php echo $item['prd_gift'] == 1 ? 'Có' : 'Không'; ?></td>
                    <td class="text-center hidden-xs <?php if (CMS_EXPIRE == 0) echo ' hidden' ?>"
                        style="max-width: 30px;"><?php echo cms_ConvertDate($item['expire_date']); ?> </td>
                    <td class="text-center"><?php echo $item['quantity']; ?></td>
                    <td class="text-center <?php if (CMS_SERIAL == 0) echo ' hidden' ?>"><?php echo cms_getListSerialByproductid($item['ID'], $data['store_id']); ?></td>
                    <td class="text-right"><?php echo cms_encode_currency_format($item['prd_origin_price'] * $item['quantity']); ?></td>
                    <td class="text-right"><?php echo cms_encode_currency_format($item['prd_sell_price'] * $item['quantity']); ?></td>
                </tr>
            <?php endforeach;
        else :
            echo '<tr><td colspan="50" class="text-center">Không có dữ liệu</td></tr>';
        endif;
        ?>
        </tbody>
    </table>
</div>

<div class="alert alert-info summany-info clearfix" role="alert">
    <div class="sm-info pull-left padd-0"></div>
    <div class="pull-right ajax-pagination">
        <?php echo $_pagination_link; ?>
    </div>
</div>
