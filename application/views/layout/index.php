<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <base href="<?php echo CMS_BASE_URL; ?>"/>
    <link rel="shortcut icon" type="image/png" href="public/templates/images/ico.png"/>
    <title><?php echo isset($seo['title']) ? $seo['title'] : 'Phần mềm quản lý bán hàng'; ?></title>
    <link href="public/templates/css/bootstrap.min.css" rel="stylesheet">
    <link href="public/templates/css/select2.min.css" rel="stylesheet"/>
    <link href="public/templates/css/bootstrap-datepicker.css" rel="stylesheet">
    <link href="public/templates/css/bootstrap-datetimepicker.min.css" rel="stylesheet">
    <link href="public/templates/css/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="public/templates/css/font-awesome.min.css" rel="stylesheet">
    <link href="public/templates/css/style.css" rel="stylesheet">
    <link href="public/templates/css/jquery-ui.min.css" rel="stylesheet">
    <link href="public/templates/css/jquery.datetimepicker.css" rel="stylesheet">
    <script src="public/templates/js/jquery.js"></script>
    <script src="public/templates/js/jquery.form.js"></script>
    <?php if (CMS_DEMO == 1) {
        ?>
        <!--        <script id="ze-snippet"-->
        <!--                src="https://static.zdassets.com/ekr/snippet.js?key=0276835d-28d3-4bc2-8ad8-acd25bb427b4"></script>-->
        <?php
    } ?>
</head>
<body>
<header>
    <?php $this->load->view('common/header', isset($data) ? $data : NULL); ?>
</header>
<section class="main" role="main">
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-1">
                <div class="sidebar sidebar-fixed hidden-xs hidden-sm" id="sidebar">
                    <?php $this->load->view('common/sidebar', isset($data) ? $data : NULL); ?>
                </div>
            </div>
            <div class="col-md-11">
                <div class="main-content">
                    <?php
                    $this->load->view('common/modal', isset($data) ? $data : NULL);
                    ?>
                    <?php
                    $this->load->view($template, isset($data) ? $data : NULL);
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
</body>
<script src="public/templates/js/select2.min.js"></script>
<script src="public/templates/js/jquery-ui.min.js"></script>
<script src="public/templates/js/html5shiv.min.js"></script>
<script src="public/templates/js/respond.min.js"></script>
<script src="public/templates/js/bootstrap.min.js"></script>
<script src="public/templates/js/jquery.datetimepicker.full.js"></script>
<script src="public/templates/js/bootstrap-datepicker.min.js"></script>
<script src="public/templates/js/bootstrap-datepicker.vi.min.js"></script>
<script src="public/templates/js/ckeditor.js"></script>
<script src="public/templates/js/editor.js"></script>
<script src="public/templates/js/ajax2.js"></script>
<script src="public/templates/js/bootstrap-tagsinput.min.js"></script>
</html>
