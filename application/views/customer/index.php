<div class="customer-supplier">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="customer-act act">
                <div class="col-md-3 col-sm-3 col-xs-7 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Khách hàng</h2>
                    </div>
                </div>
                <div class="col-md-7 col-sm-9 col-xs-5 padd-0">
                    <div class="right-action text-right">
                        <div class="col-md-6 col-sm-7 hidden-xs" style="line-height: 50px;">

                        </div>
                        <div class="col-md-6 col-sm-5 col-xs-12">
                            <button type="button" class="save btn btn-primary" data-toggle="modal"
                                    data-target="#create-cust">

                                <i class="fa fa-plus"></i> <span
                                        class="hidden-xs hidden-sm"> Tạo KH</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="supplier-act act" style="display: none;">
                <div class="col-md-3 col-sm-3 col-xs-7 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Nhà cung cấp</h2>
                    </div>
                </div>
                <div class="col-md-7 col-sm-9 col-xs-5 padd-0">
                    <div class="right-action text-right">
                        <div class="col-md-6 col-sm-7 hidden-xs" style="line-height: 50px;">
                        </div>
                        <div class="col-md-6 col-sm-5 col-xs-12">
                            <button type="button" class="save btn btn-primary" data-toggle="modal"
                                    data-target="#create-sup">
                                <i class="fa fa-plus"></i> <span
                                        class="hidden-xs hidden-sm"> Tạo NCC</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="main-space orders-space"></div>
    <div>
        <ul class="nav nav-tabs tab-setting" role="tablist" style="padding-left: 20px;">
            <li role="presentation" onclick="tab_click_act('customer');" class="active"><a href="#cus"
                                                                                           aria-controls="customer"
                                                                                           role="tab"
                                                                                           data-toggle="tab"><i
                            class="fa fa-user"></i> Khách hàng</a></li>
            <?php if ($supplier) { ?>
                <li role="presentation" onclick="tab_click_act('supplier');"><a href="#sup"
                                                                                aria-controls="supplier"
                                                                                role="tab"
                                                                                data-toggle="tab"><i
                                class="fa fa-truck"></i> Nhà cung cấp</a></li>
            <?php } ?>

        </ul>
        <div class="tab-content">
            <div id="cus" class="tab-pane active">
                <div class="cus-sear panel-sear">
                    <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-0" style="margin-bottom:0px;">
                        <input type="text" class="form-control txt-scustomer"
                               placeholder="Nhập tên, mã hoặc SDT khách hàng">
                    </div>
                    <div class="form-group col-md-9 col-sm-9 col-xs-12 padd-0">
                        <div class="col-md-3 col-sm-4 col-xs-6 padd-0">
                            <select id="cus-option" class="form-control">
                                <option value="0">Tất cả</option>
                                <option value="1">KH từng mua hàng</option>
                                <option value="2">KH còn nợ</option>
                            </select>
                        </div>
                        <button type="button" onclick="cms_paging_customer(1)"
                                class="save btn btn-primary btn-large btn-sCustomer"><i
                                    class="fa fa-search"></i> <span class="hidden-xs hidden-sm"> Tìm</span>
                        </button>
                    </div>
                </div>
                <div class="cus-body">
                </div>
            </div>
            <div id="sup" class="tab-pane">
                <div class="sup-sear panel-sear">
                    <div class="form-group col-md-3 col-sm-3 col-xs-12 padd-0" style="margin-bottom:0px;">
                        <input type="text" class="form-control txt-ssupplier"
                               placeholder="Nhập tên, mã hoặc SDT Nhà cung cấp">
                    </div>
                    <div class="form-group col-md-9 col-sm-9 col-xs-12 padd-0">
                        <div class="col-md-3 col-sm-4 col-xs-6 padd-0">
                            <select id="sup-option" class="form-control">
                                <option value="0">Tất cả</option>
                                <option value="1">NCC từng nhập hàng</option>
                                <option value="2">Còn nợ NCC</option>
                            </select>
                        </div>
                        <button type="button" class="save btn btn-primary btn-large btn-ssup"
                                onclick="cms_paging_supplier(1)"><i class="fa fa-search"></i> <span
                                    class="hidden-xs hidden-sm">Tìm</span>
                        </button>
                    </div>
                </div>
                <div class="sup-body">
                </div>
            </div>
        </div>

    </div>
</div>
