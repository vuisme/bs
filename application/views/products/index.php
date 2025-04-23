<div class="products">
    <div class="breadcrumbs-fixed panel-action">
        <div class="row">
            <div class="products-act">
                <div class="col-md-3 col-sm-3 col-xs-6 col-md-offset-2">
                    <div class="left-action text-left clearfix">
                        <h2>Sản phẩm</h2>
                    </div>
                </div>
                <div class="col-md-7 col-sm-9 col-xs-6 padd-0">
                    <div class="right-action text-right">
                        <div class="btn-groups">
                            <div class="col-md-5 col-sm-6 hidden-xs" style="line-height: 50px;">

                            </div>
                            <div class="col-md-7 col-sm-6 col-xs-12">
                                <a class="save btn btn-primary"
                                   onclick="cms_vcrproduct(1)"> <i
                                            class="fa fa-plus"></i> <span
                                            class="hidden-xs hidden-sm"> Tạo sản phẩm</span></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="main-space orders-space"></div>

    <div class="products-content">
        <div class="product-sear panel-sear">

            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 padd-0">
                <div class="form-group col-lg-2 col-md-3 col-sm-2 col-xs-6 padd-0" style="display: flex">
                    <input type="text" class="form-control" placeholder="Nhập thông tin sản phẩm để tìm kiếm"
                           id="product-search">
                </div>
                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 padd-0">
                    <select class="form-control" id="search_option_1">
                        <option value="0">Đang kinh doanh</option>
                        <option value="1">Đã ngừng kinh doanh</option>
                        <option value="2">Đã xóa</option>
                    </select>
                </div>

                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-6 padd-0">
                    <select class="form-control search_option_2" id="prd_group_id">
                        <option value="-1" selected="selected">-Danh mục-</option>
                    </select>
                </div>

                <div class="form-group col-lg-2 col-md-2 col-sm-2 col-xs-5 padd-0">
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
                            <option value="product_manufacture" data-toggle="modal"
                                    data-target="#list-prd-manufacture">Tạo mới
                                Nhà sản xuất
                            </option>
                        </optgroup>
                    </select>
                </div>

                <div class="form-group col-lg-4 col-md-3 col-sm-4 col-xs-1 padd-0" style="display: flex">
                    <button type="button" class="save btn btn-primary btn-large btn-ssup"
                            onclick="cms_paging_product(1)"><i
                                class="fa fa-search"></i> <span class="hidden-xs hidden-sm"> Tìm</span>
                    </button>
                </div>
            </div>
        </div>
        <div class="product-main-body">
        </div>
    </div>
</div>
