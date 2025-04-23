<?php
/************ Cấu hình ************/
define('CMS_DB_HOST', getenv('CMS_DB_HOST') ?: 'localhost');
define('CMS_DB_USER', getenv('CMS_DB_USER') ?: 'root');
define('CMS_DB_PASSWORD', getenv('CMS_DB_PASSWORD') ?: '');
define('CMS_DB_NAME', getenv('CMS_DB_NAME') ?: 'test_db');
define('CMS_BASE_URL', getenv('CMS_BASE_URL') ?: 'http://localhost');

define('CMS_DB_PREFIX', 'cms_');
define('CMS_DEFAULT_LANGUAGE', 'vietnamese');
define('CMS_DEMO', 0);
define('CMS_EXPIRE', 0);
define('CMS_SERIAL', 0);
define('CMS_PREFIX', md5('CMS_'));
define('COOKIE_EXPIRY', 60480);
define('CMS_SMTP', 'ssl://smtp.googlemail.com');
define('CMS_PORT', '465');
define('CMS_MAIL_USER', 'pmbanhang2017@gmail.com');
define('CMS_MAIL_PASS', 'admin123546@PhongTran.info');
