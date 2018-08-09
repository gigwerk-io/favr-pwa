<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 8/7/18
 * Time: 9:12 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 11;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

$page->renderFavrMarketplace($_SESSION['scope'], $_SESSION['filter_marketplace_by'], $_SESSION['orient_marketplace_by'], $_SESSION['limit_marketplace_by']);
$_SESSION['main_notifications'] = $page->processNotifications($_SESSION['user_info']);
