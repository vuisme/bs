<?php

/** This file is part of KCFinder project
 *
 * @desc This file is included first, before each other
 * @package KCFinder
 * @version 2.51
 * @author Pavel Tzonkov <pavelc@users.sourceforge.net>
 * @copyright 2010, 2011 KCFinder Project
 * @license http://www.opensource.org/licenses/gpl-2.0.php GPLv2
 * @license http://www.opensource.org/licenses/lgpl-2.1.php LGPLv2
 * @link http://kcfinder.sunhater.com
 *
 * This file is the place you can put any code (at the end of the file),
 * which will be executed before any other. Suitable for:
 *     1. Set PHP ini settings using ini_set()
 *     2. Custom session save handler with session_set_save_handler()
 *     3. Any custom integration code. If you use any global variables
 *        here, they can be accessed in config.php via $GLOBALS array.
 *        It's recommended to use constants instead.
 */

// PHP VERSION CHECK
if (substr(PHP_VERSION, 0, strpos(PHP_VERSION, '.')) < 5)
    die("You are using PHP " . PHP_VERSION . " when KCFinder require at least version 5! Some systems has an option to change the active PHP version. Please refer to your hosting provider or upgrade your PHP distribution.");

// GD EXTENSION CHECK
if (!function_exists("imagecopyresampled"))
    die("The GD PHP extension is not available! It's required to run KCFinder.");

// SAFE MODE CHECK
if (ini_get("safe_mode"))
    die("The \"safe_mode\" PHP ini setting is turned on! You cannot run KCFinder in safe mode.");

// CMS INTEGRATION
if (isset($_GET['cms'])) {
    switch ($_GET['cms']) {
        case "drupal":
            require "integration/drupal.php";
    }
}

// MAGIC AUTOLOAD CLASSES FUNCTION
function __autoload($class)
{
    if ($class == "uploader")
        require "core/uploader.php";
    elseif ($class == "browser")
        require "core/browser.php";
    elseif (file_exists("core/types/$class.php"))
        require "core/types/$class.php";
    elseif (file_exists("lib/class_$class.php"))
        require "lib/class_$class.php";
    elseif (file_exists("lib/helper_$class.php"))
        require "lib/helper_$class.php";
}

// json_encode() IMPLEMENTATION IF JSON EXTENSION IS MISSING
if (!function_exists("json_encode")) {

    function kcfinder_json_string_encode($string)
    {
        return '"' .
            str_replace('/', "\\/",
                str_replace("\t", "\\t",
                    str_replace("\r", "\\r",
                        str_replace("\n", "\\n",
                            str_replace('"', "\\\"",
                                str_replace("\\", "\\\\",
                                    $string)))))) . '"';
    }

    function json_encode($data)
    {

        if (is_array($data)) {
            $ret = array();

            // OBJECT
            if (array_keys($data) !== range(0, count($data) - 1)) {
                foreach ($data as $key => $val)
                    $ret[] = kcfinder_json_string_encode($key) . ':' . json_encode($val);
                return "{" . implode(",", $ret) . "}";

                // ARRAY
            } else {
                foreach ($data as $val)
                    $ret[] = json_encode($val);
                return "[" . implode(",", $ret) . "]";
            }

            // BOOLEAN OR NULL
        } elseif (is_bool($data) || ($data === null))
            return ($data === null)
                ? "null"
                : ($data ? "true" : "false");

        // FLOAT
        elseif (is_float($data))
            return rtrim(rtrim(number_format($data, 14, ".", ""), "0"), ".");

        // INTEGER
        elseif (is_int($data))
            return $data;

        // STRING
        return kcfinder_json_string_encode($data);
    }
}

// CUSTOM SESSION SAVE HANDLER CLASS EXAMPLE
//
// Uncomment & edit it if the application you want to integrate with, have
// its own session save handler. It's not even needed to save instances of
// this class in variables. Just add a row:
// new SessionSaveHandler();
// and your handler will rule the sessions ;-)

/*
class SessionSaveHandler {
    protected $savePath;
    protected $sessionName;

    public function __construct() {
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "gc")
        );
    }

    // Open function, this works like a constructor in classes and is
    // executed when the session is being opened. The open function expects
    // two parameters, where the first is the save path and the second is the
    // session name.
    public function open($savePath, $sessionName) {
        $this->savePath = $savePath;
        $this->sessionName = $sessionName;
        return true;
    }

    // Close function, this works like a destructor in classes and is
    // executed when the session operation is done.
    public function close() {
        return true;
    }

    // Read function must return string value always to make save handler
    // work as expected. Return empty string if there is no data to read.
    // Return values from other handlers are converted to boolean expression.
    // TRUE for success, FALSE for failure.
    public function read($id) {
        $file = $this->savePath . "/sess_$id";
        return (string) @file_get_contents($file);
    }

    // Write function that is called when session data is to be saved. This
    // function expects two parameters: an identifier and the data associated
    // with it.
    public function write($id, $data) {
        $file = $this->savePath . "/sess_$id";
        if (false !== ($fp = @fopen($file, "w"))) {
            $return = fwrite($fp, $data);
            fclose($fp);
            return $return;
        } else
            return false;
    }

    // The destroy handler, this is executed when a session is destroyed with
    // session_destroy() and takes the session id as its only parameter.
    public function destroy($id) {
        $file = $this->savePath . "/sess_$id";
        return @unlink($file);
    }

    // The garbage collector, this is executed when the session garbage
    // collector is executed and takes the max session lifetime as its only
    // parameter.
    public function gc($maxlifetime) {
        foreach (glob($this->savePath . "/sess_*") as $file)
            if (filemtime($file) + $maxlifetime < time())
                @unlink($file);
        return true;
    }
}

new SessionSaveHandler();

*/

// PUT YOUR ADDITIONAL CODE HERE

?>
