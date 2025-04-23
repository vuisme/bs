<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="utf-8">

    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="shortcut icon" type="image/png" href="public/templates/images/favicon.png"/>

    <base href="<?php echo CMS_BASE_URL; ?>"/>

    <title><?php echo isset($seo['title']) ? $seo['title'] : 'Phần mềm quản lý bán hàng Thịnh Phát Store'; ?></title>

    <link href="public/templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/templates/css/font-awesome.min.css" rel="stylesheet">
    <link href="public/templates/css/main2.css" rel="stylesheet">
    <link href="public/templates/css/login.css" rel="stylesheet">
    <script src="public/templates/js/jquery.js"></script>
    <script src="public/templates/js/login.js"></script>
    <script src="public/templates/js/bootstrap.min.js"></script>

</head>

<body style="background-image: url('public/templates/images/nen.jpg'); background-size: cover;">

<div class="col-md-8 col-lg-9"></div>
<div class="col-md-4 col-lg-3" id="login_modal">

    <section class="main" role="main">

        <div style="padding: 30% 10px !important;">

            <div class="row">

                <div>
                    <div style="padding: 10px; background: #E5E1E2; border-radius: 5px; margin: 10px 0; text-align: center">
                        <img src="public/templates/images/header_logo.png" style="width: 90%"/>
                    </div>
                </div>

            </div>
            <div class="row">

                <?php

                $this->load->view($template, isset($data) ? $data : NULL);

                ?>

            </div>

        </div>

    </section>
</div>

</body>

</html>
