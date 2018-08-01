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

if (isset($_GET['withdraw_request_id'], $_GET['freelancer_id'])) {
    $page->processCancelPendingRequest($_GET['withdraw_request_id'], $_GET['freelancer_id']);
}

if (isset($_GET['accept_freelancer_request_id'])) {
    $page->processFreelancerAcceptRequest($_GET['accept_freelancer_request_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['accept_customer_request_id'], $_GET['freelancer_id'])) {
    $page->processCustomerAcceptRequest($_GET['accept_customer_request_id'], $_GET['freelancer_id'], $_SESSION['user_info']['id']);
} else if (isset($_GET['reject_customer_request_id'], $_GET['freelancer_id'])) {
    $page->processCancelPendingRequest($_GET['reject_customer_request_id'], $_GET['freelancer_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['completed_request_id'], $_GET['freelancer_id'], $_GET['customer_id'])) {
    $page->processCompleteRequest($_GET['completed_request_id'], $_GET['customer_id'], $_GET['freelancer_id']);
}

$page->setTitle("Notifications");
$page->renderHeader();

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
?>
    <div class="my-1 p-2">
        <h6 class="border-bottom border-gray pb-2 mb-0">
            <small class="d-inline text-left font-italic text-muted">
                <p>"Frankie says ..." - Frankie Goes To Hollywood</p>
            </small>
            <small class="d-inline text-right">
                <a href="?navbar=active_notifications">Refresh</a>
            </small>
<!--            <small class="col-sm-6 pl-0"></small>-->
<!--            <small class="col-sm-6 pl-0"></small>-->
        </h6>
    </div>
<?php
$page->renderMainNotifications($_SESSION['user_info']);
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
</script>
");
$page->renderFooter();