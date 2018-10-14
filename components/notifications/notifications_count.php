<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 8/20/18
 * Time: 2:57 AM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
$notificationCount = $page->processNotifications($_SESSION['user_info']);

$page->renderNotificationCount($notificationCount);