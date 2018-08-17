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
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

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

    $page->processSettings($_POST['display_ratings'], $_POST['display_receipts'], $_POST['display_description'], $_POST['default_scope']);
}

$page->setTitle("Settings");
$page->addStylesheet("<link rel='stylesheet' type='text/css' href='$page->root_path/assets/css/settings.css' />");
$page->renderHeader();

echo $ALERT_MESSAGE;
$page->renderSettings($_SESSION['user_info']['id']);
$page->addScript("
<script>
    window.addEventListener('load', function(){
        var allimages= document.getElementsByTagName('img');
        for (var i=0; i<allimages.length; i++) {
            if (allimages[i].getAttribute('data-src')) {
                allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
            }
        }
    }, false);    
    
//    window.setInterval(function(){
//      // call your function here
//        $('#notifications').load('notifications.php')
//    }, 5000);
</script>
");
$page->renderFooter();
?>