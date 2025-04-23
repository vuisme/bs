<?php defined('BASEPATH') or exit('No direct script access allowed');

/*
 *  ============================================================================== 
 *  Author	: Phong Trần
 *  Email	: admin@phongtran.info
 *  For		: ESC/POS Print Driver for PHP
 *  License	: cms License
 *  ============================================================================== 
 */
// require_once APPPATH . "/third_party/phpqrcode/qrlib.php";
include(APPPATH . "/third_party/phpqrcode/qrlib.php");

class Phpqrcode
{

    public function generate($params = array())
    {
        $params['data'] = (isset($params['data'])) ? $params['data'] : 'http://phongtran.info';
        if (isset($params['svg']) && !empty($params['svg'])) {

            QRcode::svg($params['data'], $params['savename'], 'H', 2, 0);
            // $svgCode = QRcode::svg($params['data']); 
            return $params['savename'];

        } else {

            QRcode::png($params['data'], $params['savename'], 'H', 2, 0);
            return $params['savename'];

        }
    }

}
