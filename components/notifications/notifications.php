<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 8/7/18
 * Time: 8:25 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
$page->renderMainNotifications($_SESSION['user_info']);