<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 12:48 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 8;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID", "friends-table", $page);
$chart = new Data_Chart("$PAGE_ID", "rent-chart", $page);

$page->setTitle("Notifications");
$page->renderHeader();


if (isset($_GET['accept_request_id'])) {
    $page->processAcceptRequest($_GET['accept_request_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['completed_request_id'], $_GET['freelancer_id'], $_GET['customer_id'])) {
    $page->processCompleteRequest($_GET['completed_request_id'], $_GET['customer_id'], $_GET['freelancer_id']);
}

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}

echo $ALERT_MESSAGE;



$page->renderMainNotifications($_SESSION['user_info']);

$page->renderFooter();
?>