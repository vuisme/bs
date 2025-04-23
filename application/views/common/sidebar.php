<ul class="nav nav-pills nav-list nav-stacked">
    <?php if (in_array(19, $user['group_permission'])) : ?>
        <li id="pos"><a href="pos" style="color: white;background-color: #ffb752!important;"><i
                        class="fa fa-sellsy"></i>Pos</a></li>
    <?php endif; ?>
    <?php if (in_array(1, $user['group_permission'])) : ?>
        <li id="dashboard"><a href="dashboard"><i class="fa fa-dashboard"></i>Tổng quan</a></li>
    <?php endif; ?>
    <?php if (in_array(2, $user['group_permission'])) : ?>
        <li id="orders"><a href="orders"><i class="fa fa-shopping-cart"></i>Đơn hàng</a></li>
    <?php endif; ?>
    <?php if (in_array(3, $user['group_permission'])) : ?>
        <li id="product"><a href="product"><i class="fa fa-barcode"></i>Sản phẩm</a></li>
    <?php endif; ?>
    <?php if (in_array(4, $user['group_permission'])) : ?>
        <li id="customer"><a href="customer"><i class="fa fa-users"></i>KH - NCC</a></li>
    <?php endif; ?>
    <?php if (in_array(5, $user['group_permission'])) : ?>
        <li id="input"><a href="input"><i class="fa fa-inbox"></i>Nhập kho</a></li>
    <?php endif; ?>
    <?php if (in_array(7, $user['group_permission'])) : ?>
        <li id="inventory"><a href="inventory"><i class="fa fa-list-alt"></i>Tồn
                kho</a></li>
    <?php endif; ?>
    <?php if (in_array(8, $user['group_permission'])) : ?>
        <li id="revenue"><a href="revenue"><i class="fa fa-signal"></i>Doanh số</a></li>
    <?php endif; ?>
    <?php if (in_array(10, $user['group_permission'])) : ?>
        <li id="profit"><a href="profit"><i class="fa fa-usd"></i>Lợi nhuận</a></li>
    <?php endif; ?>
    <?php if (in_array(11, $user['group_permission'])) : ?>
        <li id="setting"><a href="setting"><i class="fa fa-cogs"></i>Thiết lập</a></li>
    <?php endif; ?>
</ul>
