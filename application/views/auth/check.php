<div class="login-container col-md-4 col-md-offset-4" id="login-form">
    <div class="login-frame clearfix">
        <h3 class="heading col-md-10 col-md-offset-1 padd-0"><i class="fa fa-lock"></i>Check bản quyền</h3>
        <h5 class="heading col-md-10 col-md-offset-1 padd-0">Nhập thông tin để kích hoạt bản quyền</h5>
        <ul class="validation-summary-errors col-md-10 col-md-offset-1">
            <?php echo isset($loi) ? $loi : ''; ?>
        </ul>
        <div class="col-md-10 col-md-offset-1">
            <form class="form-horizontal login-form frm-sm" method="post" action="">
                <div class="form-group input-icon">
                    <label for="inputEmail3" class="sr-only control-label">Email</label>
                    <input type="text" name="data[username]"
                           value=""
                           class="form-control" id="inputEmail3" placeholder="Nhập mã bản quyền">
                    <i class="fa fa-user icon-right"></i>
                </div>

                <div class="form-group">
                    <input type="submit" name="login" value="Gửi" class="save btn btn-primary btn-sm"/>
                </div>
            </form>
        </div>
    </div>

    <!--    <div class="link-action text-center">-->
    <!--        <div class="col-sm-6 col-xs-12">-->
    <!--            <a href="authentication/fg_password" style="display:inline-block; margin-top: 5px;" class="fg-passw">Quên-->
    <!--                mật khẩu</a>-->
    <!--        </div>-->
    <!--        <div class="col-sm-6 col-xs-12">-->
    <!--            <a href="authentication/register" style="display:inline-block; margin-top: 5px;" class="register">Đăng-->
    <!--                kí</a>-->
    <!--        </div>-->
    <!---->
    <!--    </div>-->
</div>
