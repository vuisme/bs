<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        EllisLab Dev Team
 * @copyright        Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright        Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter CAPTCHA Helper
 *
 * @package        CodeIgniter
 * @subpackage    Helpers
 * @category    Helpers
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/helpers/xml_helper.html
 */

// ------------------------------------------------------------------------

/**
 * Create CAPTCHA
 *
 * @access    public
 * @param array    array of data for the CAPTCHA
 * @param string    path to create the image in
 * @param string    URL to the CAPTCHA image folder
 * @param string    server path to font
 * @return    string
 */
if (!function_exists('create_captcha')) {
    function create_captcha($data = '', $img_path = '', $img_url = '', $font_path = '')
    {
        $defaults = array('word' => '', 'img_path' => '', 'img_url' => '', 'img_width' => '150', 'img_height' => '30', 'font_path' => '', 'expiration' => 7200);

        foreach ($defaults as $key => $val) {
            if (!is_array($data)) {
                if (!isset($$key) or $$key == '') {
                    $$key = $val;
                }
            } else {
                $$key = (!isset($data[$key])) ? $val : $data[$key];
            }
        }

        if ($img_path == '' or $img_url == '') {
            return FALSE;
        }

        if (!@is_dir($img_path)) {
            return FALSE;
        }

        if (!is_writable($img_path)) {
            return FALSE;
        }

        if (!extension_loaded('gd')) {
            return FALSE;
        }

        // -----------------------------------
        // Remove old images
        // -----------------------------------

        list($usec, $sec) = explode(" ", microtime());
        $now = ((float)$usec + (float)$sec);

        $current_dir = @opendir($img_path);

        while ($filename = @readdir($current_dir)) {
            if ($filename != "." and $filename != ".." and $filename != "index.html") {
                $name = str_replace(".jpg", "", $filename);

                if (($name + $expiration) < $now) {
                    @unlink($img_path . $filename);
                }
            }
        }

        @closedir($current_dir);

        // -----------------------------------
        // Do we have a "word" yet?
        // -----------------------------------

        // -----------------------------------
        // Do we have a "word" yet?
        // -----------------------------------

        if (empty($word)) {
            $word = '';
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pool_length = strlen($pool);
            $rand_max = $pool_length - 1;

            // PHP7 or a suitable polyfill
            if (function_exists('random_int')) {
                try {
                    for ($i = 0; $i < $word_length; $i++) {
                        $word .= $pool[random_int(0, $rand_max)];
                    }
                } catch (Exception $e) {
                    // This means fallback to the next possible
                    // alternative to random_int()
                    $word = '';
                }
            }
        }

        if (empty($word)) {
            // To avoid numerous get_random_bytes() calls, we'll
            // just try fetching as much bytes as we need at once.
            if (($bytes = _ci_captcha_get_random_bytes($pool_length)) !== FALSE) {
                $byte_index = $word_index = 0;
                while ($word_index < $word_length) {
                    if (($rand_index = unpack('C', $bytes[$byte_index++])) > $rand_max) {
                        // Was this the last byte we have?
                        // If so, try to fetch more.
                        if ($byte_index === $pool_length) {
                            // No failures should be possible if
                            // the first get_random_bytes() call
                            // didn't return FALSE, but still ...
                            for ($i = 0; $i < 5; $i++) {
                                if (($bytes = _ci_captcha_get_random_bytes($pool_length)) === FALSE) {
                                    continue;
                                }

                                $byte_index = 0;
                                break;
                            }

                            if ($bytes === FALSE) {
                                // Sadly, this means fallback to mt_rand()
                                $word = '';
                                break;
                            }
                        }

                        continue;
                    }

                    $word .= $pool[$rand_index];
                    $word_index++;
                }
            }
        }

        if (empty($word)) {
            for ($i = 0; $i < $word_length; $i++) {
                $word .= $pool[mt_rand(0, $rand_max)];
            }
        } elseif (!is_string($word)) {
            $word = (string)$word;
        }

        // -----------------------------------
        // Determine angle and position
        // -----------------------------------

        $length = strlen($word);
        $angle = ($length >= 6) ? rand(-($length - 6), ($length - 6)) : 0;
        $x_axis = rand(6, (360 / $length) - 16);
        $y_axis = ($angle >= 0) ? rand($img_height, $img_width) : rand(6, $img_height);

        // -----------------------------------
        // Create image
        // -----------------------------------

        // PHP.net recommends imagecreatetruecolor(), but it isn't always available
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($img_width, $img_height);
        } else {
            $im = imagecreate($img_width, $img_height);
        }

        // -----------------------------------
        //  Assign colors
        // -----------------------------------

        $bg_color = imagecolorallocate($im, 255, 255, 255);
        $border_color = imagecolorallocate($im, 153, 102, 102);
        $text_color = imagecolorallocate($im, 204, 153, 153);
        $grid_color = imagecolorallocate($im, 255, 182, 182);
        $shadow_color = imagecolorallocate($im, 255, 240, 240);

        // -----------------------------------
        //  Create the rectangle
        // -----------------------------------

        ImageFilledRectangle($im, 0, 0, $img_width, $img_height, $bg_color);

        // -----------------------------------
        //  Create the spiral pattern
        // -----------------------------------

        $theta = 1;
        $thetac = 7;
        $radius = 16;
        $circles = 20;
        $points = 32;

        for ($i = 0; $i < ($circles * $points) - 1; $i++) {
            $theta = $theta + $thetac;
            $rad = $radius * ($i / $points);
            $x = ($rad * cos($theta)) + $x_axis;
            $y = ($rad * sin($theta)) + $y_axis;
            $theta = $theta + $thetac;
            $rad1 = $radius * (($i + 1) / $points);
            $x1 = ($rad1 * cos($theta)) + $x_axis;
            $y1 = ($rad1 * sin($theta)) + $y_axis;
            imageline($im, $x, $y, $x1, $y1, $grid_color);
            $theta = $theta - $thetac;
        }

        // -----------------------------------
        //  Write the text
        // -----------------------------------

        $use_font = ($font_path != '' and file_exists($font_path) and function_exists('imagettftext')) ? TRUE : FALSE;

        if ($use_font == FALSE) {
            $font_size = 5;
            $x = rand(0, $img_width / ($length / 3));
            $y = 0;
        } else {
            $font_size = 16;
            $x = rand(0, $img_width / ($length / 1.5));
            $y = $font_size + 2;
        }

        for ($i = 0; $i < strlen($word); $i++) {
            if ($use_font == FALSE) {
                $y = rand(0, $img_height / 2);
                imagestring($im, $font_size, $x, $y, substr($word, $i, 1), $text_color);
                $x += ($font_size * 2);
            } else {
                $y = rand($img_height / 2, $img_height - 3);
                imagettftext($im, $font_size, $angle, $x, $y, $text_color, $font_path, substr($word, $i, 1));
                $x += $font_size;
            }
        }

        // -----------------------------------
        //  Create the border
        // -----------------------------------

        imagerectangle($im, 0, 0, $img_width - 1, $img_height - 1, $border_color);

        // -----------------------------------
        //  Generate the image
        // -----------------------------------

        $img_name = $now . '.jpg';

        ImageJPEG($im, $img_path . $img_name);

        $img = "<img src=\"$img_url$img_name\" width=\"$img_width\" height=\"$img_height\" style=\"border:0;\" alt=\" \" />";

        ImageDestroy($im);

        return array('word' => $word, 'time' => $now, 'image' => $img);
    }

    function _ci_captcha_get_random_bytes($length)
    {
        if (defined('MCRYPT_DEV_URANDOM')) {
            return mcrypt_create_iv($length, MCRYPT_DEV_URANDOM);
        } elseif (function_exists('openssl_random_pseudo_bytes')) {
            return openssl_random_pseudo_bytes($length);
        }

        return FALSE;
    }
}

// ------------------------------------------------------------------------

/* End of file captcha_helper.php */
/* Location: ./system/heleprs/captcha_helper.php */
