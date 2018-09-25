<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 3:02 AM
 *
 * @author haronarama
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

// TODO: Constant variable statically used: active_home
if (isset($_SESSION['navbar']) && $_SESSION['navbar'] != "active_home") {
    $_SESSION['navbar'] = "active_settings";
}

$page = new Web_Page($USER);

if (isset($_POST['submit_delete'])) {
    $page->processAccountDelete($_SESSION['user_info']['id']);
    $page->signOutUser();
}

if (isset($_POST['submit_settings'], $_POST['default_scope'])) {
    if (isset($_POST['display_ratings'])) {
        $_POST['display_ratings'] = 1;
    } else {
        $_POST['display_ratings'] = 0;
    }

    if (isset($_POST['display_receipts'])) {
        $_POST['display_receipts'] = 1;
    } else {
        $_POST['display_receipts'] = 0;
    }

    if (isset($_POST['display_description'])) {
        $_POST['display_description'] = 1;
    } else {
        $_POST['display_description'] = 0;
    }

    if (isset($_POST['enable_sms_notifications'])) {
        $_POST['enable_sms_notifications'] = 1;
    } else {
        $_POST['enable_sms_notifications'] = 0;
    }

    if (isset($_POST['enable_email_notifications'])) {
        $_POST['enable_email_notifications'] = 1;
    } else {
        $_POST['enable_email_notifications'] = 0;
    }

    $page->processSettings(
            $_POST['display_ratings'],
            $_POST['display_receipts'],
            $_POST['display_description'],
            $_POST['default_scope'],
            $_POST['enable_email_notifications'],
            $_POST['enable_sms_notifications'],
            $_POST['street'],
            $_POST['city'],
            $_POST['state'],
            $_POST['zip']
    );
}

$page->setTitle("Settings");
$page->addStylesheet("<link rel='stylesheet' type='text/css' href='$page->root_path/assets/css/settings.css' />");
$page->renderHeader();

echo $ALERT_MESSAGE;

$page->renderSettings($_SESSION['user_info']['id']);
$page->renderFooter();