<div class="orders">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="orders-act">
                <div class="col-md-4 col-xs-6 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Đơn hàng</h2>
                    </div>
                </div>
                <div class="col-md-6 col-xs-6">
                    <div class="right-action text-right">
                        <div class="btn-groups">
                            <button type="button" class="save btn btn-primary" onclick="cms_vsell_order();"><i
                                        class="fa fa-plus"></i> <span class="hidden-xs hidden-sm">Tạo đơn</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-space orders-space"></div>
    <div class="orders-content">
        <div class="product-sear panel-sear">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padd-0">
                <div class="form-group col-lg-2 col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '3'; ?> col-sm-3 col-xs-<?php if (count($data['list_store_show']) < 2) echo '6'; else echo '12'; ?> padd-0"
                     style="display: flex">
                    <input type="text" class="form-control" id="order-search"
                           placeholder="Nhập thông tin để tìm kiếm" value="">
                </div>
                <div class="form-group col-lg-1 col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '3'; ?> col-sm-3 col-xs-6 padd-0">
                    <select id="search_option_1" class="form-control">
                        <option value="0">-Đơn hàng-</option>
                        <option value="1">Đơn đã xóa</option>
                        <option value="2">Đơn còn nợ</option>
                    </select>
                </div>
                <div class="form-group col-lg-1 col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '3'; ?> col-sm-3 col-xs-6 padd-0">
                    <select id="search_option_2" class="form-control">
                        <option value="-1">-Tình trạng-</option>
                        <?php
                        $list_order_status = cms_getListOrderStatus();
                        foreach ((array)$list_order_status as $key => $val) : ?>
                            <option value="<?php echo $key; ?>"><?php echo $val; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-lg-1 col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '3'; ?> col-sm-3 col-xs-6 padd-0">
                    <select id="search_option_3" class="form-control">
                        <option value="-1">-Khách Hàng-</option>
                        <option value="0">Không nhập</option>
                        <?php foreach ((array)$data['customers'] as $item) : ?>
                            <option
                                    value="<?php echo $item['ID']; ?>"><?php echo $item['customer_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-lg-1 col-md-3 col-sm-3 col-xs-6 padd-0 <?php if (count($data['list_store_show']) < 2) echo 'hidden'; ?>">
                    <select id="search_option_4" class="form-control">
                        <option value="-1">-Kho-</option>
                        <?php foreach ((array)$data['list_store_show'] as $item) : ?>
                            <option
                                    value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group col-lg-3 col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '3'; ?> col-sm-4 col-xs-6 padd-0"
                     style="display: flex">
                    <div class="input-daterange input-group" id="datepicker" style="min-width: 130px;display: flex;">
                        <input type="text" class="input-sm form-control" autocomplete="off" id="search-date-from"
                               placeholder="Từ ngày"
                               name="start"/>
                        <span class="input-group-addon hidden-xs hidden-md"
                              style="padding: 6px 4px;width: auto">-</span>
                        <input type="text" class="input-sm form-control" autocomplete="off" id="search-date-to"
                               placeholder="Đến ngày"
                               name="end"/>
                    </div>
                    <button style="box-shadow: none;" type="button"
                            class="save btn btn-primary btn-large"
                            onclick="cms_paging_order(1)"><i class="fa fa-search"></i> <span
                                class="hidden-xs hidden-sm hidden-md">Tìm</span>
                    </button>
                </div>

                <div class="form-group col-lg-<?php if (count($data['list_store_show']) < 2) echo '4'; else echo '3'; ?> col-md-<?php if (count($data['list_store_show']) < 2) echo '2'; else echo '5'; ?> col-sm-<?php if (count($data['list_store_show']) < 2) echo '8'; else echo '5'; ?> col-xs-6 padd-0 text-right">
                    <div class="btn-group order-btn-calendar">
                        <button type="button" onclick="cms_order_week()" class="btn btn-default">Tuần</button>
                        <button type="button" onclick="cms_order_month()" class="btn btn-default">Tháng</button>
                        <button type="button" onclick="cms_order_quarter()" class="btn btn-default">Quý</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="orders-main-body">

        </div>
    </div>
</div>
