<div class="login-container" id="login-form">
    <div class="login-frame clearfix" style="background: #E5E1E2">
        <h3 class="heading col-md-12 padd-0 text-center" style="color: black !important;">ĐĂNG NHẬP</h3>
        <ul class="validation-summary-errors col-md-10 col-md-offset-1">
            <?php echo validation_errors(); ?>
        </ul>
        <div class="col-md-12 col-xs-12" style="padding-left: 30px;padding-right: 30px;">
            <form class="form-horizontal login-form frm-sm" method="post" action="">
                <div class="form-group input-icon">
                    <input type="text" name="data[username]"
                           value="<?php if (CMS_DEMO == 1) echo 'admin'; ?>" style="height: 50px;background: #C2C2C2"
                           class="form-control" id="inputEmail3" placeholder="Điện thoại/Mã Đăng nhập">
                    <img src="public/templates/images/icon tai khoan.png" class="icon_login" style="width: 30px;">
                </div>
                <div class="form-group input-icon">
                    <input type="password" name="data[password]"
                           value="<?php if (CMS_DEMO == 1) echo '12345678'; ?>" style="height: 50px;background: #C2C2C2"
                           class="form-control" id="inputPassword3" placeholder="Mật khẩu">
                    <img src="public/templates/images/icon mat khau.png" class="icon_login" style="width: 30px;">
                </div>

                <div class="form-group">
                    <button TYPE="submit" name="login" value="Đăng nhập" class="form-control"
                            style="background: red;color: white;font-size: 28px;line-height: 28px;height: 40px;">ĐĂNG
                        NHẬP
                    </button>
                </div>

                <div class="form-group text-center" style="color: red;">
                    <h3 class="margin-1">BO AUTHENTIC</h3>
                </div>
            </form>

        </div>

    </div>

</div>
