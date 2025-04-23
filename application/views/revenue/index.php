<div class="orders">
    <div class="breadcrumbs-fixed col-md-offset-2 panel-action panel-action2 padding-left-10">
        <h5 style="float: left;">
            <label style="color: #428bca;font-size: 23px;">Báo cáo doanh số</label>
            <label style="color: #307ecc; padding-left: 10px;">
                <input type="radio" name="revenue" value="1" checked>
                <span class="lbl">Theo đơn hàng</span>
            </label>
            <label style="color: #307ecc;">
                <input type="radio" name="revenue" value="2">
                <span class="lbl">Theo KH</span>
            </label>
            <label style="color: #307ecc;">
                <input type="radio" name="revenue" value="3">
                <span class="lbl">Theo thu ngân</span>
            </label>
            <label style="color: #307ecc;">
                <input type="radio" name="revenue" value="4">
                <span class="lbl">Theo NV bán hàng</span>
            </label>
            <label style="color: #307ecc;">
                <input type="radio" name="revenue" value="5">
                <span class="lbl">Theo kho</span>
            </label>
            <label style="color: #307ecc;">
                <input type="radio" name="revenue" value="6">
                <span class="lbl">Theo sản phẩm</span>
            </label>
        </h5>
    </div>
    <div class="main-space orders-space"></div>
    <div class="orders-content">
        <div class="product-sear panel-sear">
            <div class="form-group col-md-12 col-xs-12 padd-0">
                <div class="col-md-2 col-xs-6 padd-0">
                    <select id="search_option_1" class="form-control">
                        <option value="-1">-Khách Hàng-</option>
                        <option value="0">Không nhập</option>
                        <?php foreach ((array)$data['customers'] as $item) : ?>
                            <option
                                    value="<?php echo $item['ID']; ?>"><?php echo $item['customer_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-xs-6 padd-0">
                    <select id="search_option_2" class="form-control">
                        <option value="-1">-Thu ngân-</option>
                        <?php foreach ((array)$data['users'] as $key => $item) : ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['username']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-xs-6 padd-0">
                    <select id="search_option_4" class="form-control">
                        <option value="-1">-NV bán hàng-</option>
                        <option value="0">Không nhập</option>
                        <?php foreach ((array)$data['sales'] as $key => $item) : ?>
                            <option value="<?php echo $item['id']; ?>"><?php echo $item['username']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 col-xs-6 padd-0 <?php if (count($data['list_store_show']) < 2) echo 'hidden'; ?>">
                    <select id="search_option_3" class="form-control">
                        <option value="-1">-Kho-</option>
                        <?php foreach ((array)$data['list_store_show'] as $item) : ?>
                            <option
                                    value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-2 col-xs-6 padd-0">
                    <div class="input-daterange input-group" id="datepicker" style="min-width: 130px;display: flex;">
                        <input type="text" class="input-sm form-control" autocomplete="off" id="search-date-from"
                               placeholder="Từ ngày"
                               name="start"/>
                        <span class="input-group-addon hidden-xs" style="padding: 6px 4px;width: auto">-</span>
                        <input type="text" class="input-sm form-control" autocomplete="off" id="search-date-to"
                               placeholder="Đến ngày"
                               name="end"/>
                    </div>
                </div>

                <div class="col-md-2 col-xs-6 padd-0">
                    <div class="btn-group order-btn-calendar">
                        <button type="button" onclick="cms_revenue_all_week()" class="btn btn-default">Tuần</button>
                        <button type="button" onclick="cms_revenue_all_month()" class="btn btn-default">Tháng</button>
                        <button type="button" onclick="cms_revenue_all_quarter()" class="btn btn-default">Quý</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="revenue-main-body">
        </div>
    </div>
</div>
