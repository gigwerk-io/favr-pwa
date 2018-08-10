<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 8/9/18
 * Time: 11:27 AM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 7;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

if (!isset($_SESSION['navbar'])) {
    $_SESSION['navbar'] = "active_home";
}

$page = new Web_Page($PAGE_ID, $USER);

$userInfo = $page->getUserInfo($_GET['user_id']);

if (!isset($_GET['user_id']) || $userInfo == false) {
    header("Location: ../../home/");
} else if (isset($userInfo) && $userInfo['id'] == $_SESSION['user_info']['id']) { // if this user
    header("Location: ../profile/?navbar=active_profile");
} else {
    $page->setTitle("". $userInfo['username'] ."");
    $page->renderHeader(true, true);
    echo $ALERT_MESSAGE;
    $page->renderFavrProfile($_GET['user_id']);
    $page->renderFavrProfileHistory($_GET['user_id']);
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
}
