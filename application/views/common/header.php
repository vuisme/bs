<nav id="navbar-container" class="navbar navbar- navbar-fixed-top">
    <div class="container-fluid">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle menu-toggler pull-left"
                    style="margin-right: 0px !important;width: 15%"
                    onclick="$('#sidebar').toggleClass('hidden-xs hidden-sm hidden-md')">
                <span class="sr-only">Toggle sidebar</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <span type="button" class="btn btn-success navbar-toggle navbar-toggle-header menu-toggler pull-left"
                  style="background: #0B87C9;width: 70%;margin-right: 0px !important;">
                LH: 0967.49.00.79
            </span>
            <button type="button" class="navbar-toggle-header navbar-toggle menu-toggler pull-right text-right"
                    style="margin-right: 0px !important;width: 15%"
                    onclick="$('#header').toggleClass('hidden-xs hidden-sm hidden-md')">
                <span class="sr-only">Toggle sidebar</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
        </div>
        <!--        --><?php //if (CMS_DEMO == 1) {
        //            ?>
        <div class="collapse navbar-collapse navbar-header hidden-768 col-xs-4 col-md-6 text-right"
             style="line-height:45px;color:white;height:45px;vertical-align:middle;">

                <span style="font-size: 18px;"
                      class="hidden-sm hidden-xs">Bo Authentic</span>
        </div>
        <div class="hidden-xs navbar-collapse" id="header">
            <ul class="nav navbar-nav navbar-right" id="set-background" style="background: #0B87C9">
                <?php if (isset($data['list_store_show'])) { ?>
                    <li class="hidden-xs <?php if (count($data['list_store_show']) < 2) echo 'hidden'; ?>">
                        <label style="margin: 13px 15px; color: white">
                            Kho </label>
                    </li>
                    <li style="border-right: 1px solid #E1E1E1; padding-right: 15px;"
                        class="<?php if (count($data['list_store_show']) < 2) echo 'hidden'; ?>">
                        <select id="store-id" class="form-control" style="margin: 8px auto">
                            <?php foreach ((array)$data['list_store_show'] as $item) : ?>
                                <option <?php if ($item['ID'] == $data['user']['store_id']) echo 'selected '; ?>
                                        value="<?php echo $item['ID']; ?>"><?php echo $item['store_name']; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </li>
                <?php } ?>
                <li class="dropdown user-profile">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false"><span class="hello">Xin chào, </span><?php echo (isset($user)) ?
                            $user['display_name'] : $user['username']; ?><span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="account"><i class="fa fa-user"></i>Tài khoản</a></li>
                        <li><a href="authentication/logout"><i class="fa fa-power-off"></i>Thoát</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
