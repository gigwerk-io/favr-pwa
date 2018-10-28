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
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

if (!isset($_SESSION['navbar'])) {
    $_SESSION['navbar'] = "active_home";
}

$page = new Web_Page($USER);

$userInfo = $page->getUserInfo($_GET['id']);

if (!isset($_GET['id']) || $userInfo == false) {
    header("Location: ../../home/");
} else if (isset($userInfo) && $userInfo['id'] == $_SESSION['user_info']['id']) { // if this user
    header("Location: ../profile/?navbar=active_profile");
} else {
    if (isset($_GET['add_friend'])) {
        $user_id = $_GET['id'];
        $requester_id = $_SESSION['user_info']['id'];
        if ($_GET['add_friend'] == 'true') {
            $page->processFavrFriendRequest($user_id, $requester_id, true);
        } else if ($_GET['add_friend'] == 'false') {
            $page->processFavrFriendRequest($user_id, $requester_id, false);
        }
    }

    $page->setTitle("". $userInfo['username'] ."");
    $page->renderHeader(true, true);
    echo $ALERT_MESSAGE;
    $page->renderFavrProfile($_GET['id'], $_SESSION['user_info']['id']);
    $page->renderFavrProfileHistory($_GET['id'], $_SESSION['user_info']['id']);
    $page->addScript("
    <script>  
        
    //    window.setInterval(function(){
    //      // call your function here
    //        $('#notifications').load('notifications.php')
    //    }, 5000);
    </script>
    ");
    $page->renderFooter();
}
