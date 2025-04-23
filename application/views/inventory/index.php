<div class="inventory">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="orders-act">
                <div class="col-md-4 col-xs-6 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Tồn kho</h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-space orders-space"></div>
    <div class="inventory-content">
        <input id="modal_product_id" style="display: none;">
        <div class="product-sear panel-sear">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padd-0">
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-12 padd-0"
                     style="display: flex">
                    <input type="text" class="form-control txt-sinventory"
                           placeholder="Nhập tên hoặc mã sản phẩm để tìm kiếm">
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 padd-0">
                    <select class="form-control" id="prd_group_id">
                        <option value="-1" selected='selected'>-Danh mục-</option>
                        <optgroup label="Chọn danh mục">
                            <?php if (isset($data['_prd_group']) && count($data['_prd_group'])):
                                foreach ((array)$data['_prd_group'] as $key => $item) :
                                    ?>
                                    <option
                                            value="<?php echo $item['ID']; ?>"><?php echo $item['prd_group_name']; ?></option>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </optgroup>
                        <optgroup label="------------------------">
                        </optgroup>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 padd-0">
                    <select class="form-control search_option_3" id="prd_manufacture_id">
                        <option value="-1" selected="selected">-Nhà sản xuất-</option>
                        <optgroup label="Chọn nhà sản xuất">
                            <?php if (isset($data['_prd_manufacture']) && count($data['_prd_manufacture'])):
                                foreach ((array)$data['_prd_manufacture'] as $key => $val) :
                                    ?>
                                    <option
                                            value="<?php echo $val['ID']; ?>"><?php echo $val['prd_manuf_name']; ?></option>
                                <?php
                                endforeach;
                            endif;
                            ?>
                        </optgroup>
                        <optgroup label="------------------------">
                        </optgroup>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 padd-0">
                    <select class="form-control" id="option_inventory">
                        <option value="0">--Tất cả--</option>
                        <option value="1" selected="selected">Chỉ lấy hàng tồn</option>
                        <option value="2">Hết Hàng</option>
                        <?php if (CMS_EXPIRE == 1) {
                            ?>
                            <option value="3">Cảnh báo hết hạn</option>
                            <option value="4">Hết hạn</option>
                            <?php
                        } ?>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-4 padd-0 <?php if (count($data['list_store_show']) < 2) echo 'hidden'; ?>">
                    <select id="store_id" class="form-control">
                        <option value="-1">-Kho-</option>
                        <?php foreach ((array)$data['list_store_show'] as $item) : ?>
                            <option <?php if ($item['ID'] == $data['store_id']) echo 'selected '; ?>
                                    value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-2 padd-0" style="display: flex">
                    <button style="box-shadow: none;" type="button" class="save btn btn-primary btn-large"
                            onclick="cms_paging_inventory(1)"><i class="fa fa-search"></i> <span
                                class="hidden-xs hidden-sm">Xem</span>
                    </button>
                </div>
            </div>
        </div>


        <div class="inventory-main-body">
        </div>
    </div>
</div>

<!-- Modal product-->
<div id="myProduct" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content">
            <div class="modal-body">
                <div class="row">
                    <div class="form-inline">
                        <div class="col-md-12 col-xs-12">
                            <div class="input-daterange input-group" id="datepicker" style="max-width: 220px">
                                <input type="text" class="input-sm form-control"
                                       id="history-search-date-from" placeholder="Từ ngày"
                                       name="start"/>
                                <span class="input-group-addon hidden-xs" style="padding: 6px 4px;width: auto">-</span>
                                <input type="text" class="input-sm form-control"
                                       id="history-search-date-to" placeholder="Đến ngày"
                                       name="end"/>
                            </div>
                            <select class="form-control" id="modal_user_id">
                                <option value="-1">Lọc nhân viên</option>
                                <?php foreach ((array)$data['users'] as $item) : ?>
                                    <option
                                            value="<?php echo $item['id']; ?>"><?php echo $item['display_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select id="modal_store_id" class="form-control"
                                    style="margin: 8px auto">
                                <option value="-1">Lọc chi nhánh</option>
                                <?php foreach ((array)$data['list_store_show'] as $item) : ?>
                                    <option
                                            value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <select class="form-control" id="modal_report_type_id">
                                <option value="-1">Lọc theo tác</option>
                                <?php
                                $list = cms_getListReporttype();
                                foreach ((array)$list as $key => $val) : ?>
                                    <option
                                            value="<?php echo $key; ?>"><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button class="save btn btn-primary"
                                    onclick="cms_paging_product_history(1)">
                                <i class="fa fa-search" aria-hidden="true"></i> Tìm kiếm
                            </button>
                        </div>
                    </div>

                    <div class="col-md-12 col-xs-12 mt10" id="modal_product_history">
                    </div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="save btn btn-default" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal end product -->
