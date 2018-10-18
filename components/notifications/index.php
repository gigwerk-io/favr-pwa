<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 12:48 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");
include '../../libraries/Api/Twilio/twilio-php-master/Twilio/autoload.php';
include '../../libraries/Api/Sendgrid/vendor/autoload.php';

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
//$checkout = new Web_Payment();

//handle friend requests
if (isset($_GET['add_friend'])) {
    $user_id = $_GET['id'];
    $requester_id = $_SESSION['user_info']['id'];
    if ($_GET['add_friend'] == 'true') {
        $page->processFavrFriendRequest($user_id, $requester_id, true);
    } else if ($_GET['add_friend'] == 'false') {
        $page->processFavrFriendRequest($user_id, $requester_id, false);
    }
}
//--------------------- friend market action

// handle a direct favr request
if (isset($_POST['requestFavr'])) {
    if (isset($_GET['ask_favr'], $_GET['id']) && $_GET['ask_favr'] = 'true') {
        $successfulProcessToDB = $page->processFavrFriendRequestToDB($_SESSION['user_info'], $_POST['requestDate'], $_POST['requestCategory'], $_POST['requestTaskDescription'], $_POST['requestPrice'], 1, $_POST['requestStreetAddress'], $_POST['requestDifficulty'], $_FILES['requestPictures'], "private", $_GET['id']);
        if ($successfulProcessToDB) {
            $ALERT_MESSAGE = ""; // alert message
        }
    }
}

// handle freelancer acceptance and withdrawal
if (isset($_GET['accept_friend_request_id'])) {
    $page->processFriendFreelancerAcceptRequest($_GET['accept_friend_request_id'], $_SESSION['user_info']['id']);
} else if (isset($_GET['withdraw_friend_request_id'], $_GET['friend_id'])) {
    $page->processFriendCancelPendingRequest($_GET['withdraw_friend_request_id'], $_GET['friend_id']);
}

// handle customer accept and reject freelancer
if (isset($_GET['accept_customer_friend_request_id'], $_GET['friend_id'])) {
    $page->processFriendCustomerAcceptRequest($_GET['accept_customer_friend_request_id'], $_GET['friend_id'], $_SESSION['user_info']['id']);
} else if (isset($_GET['reject_customer_friend_request_id'], $_GET['friend_id'])) {
    $page->processFriendCancelPendingRequest($_GET['reject_customer_friend_request_id'], $_GET['friend_id'], $_SESSION['user_info']['id']);
}

// handle freelancer arrival
if (isset($_GET['friend_arrived'], $_GET['arrived_friend_request_id'])) {
    if ($_GET['friend_arrived'] == 'true') {
        $timestamp = date("Y-m-d h:i:s", time());
        $page->processFriendFreelancerArrived($_GET['friend_arrived'], $timestamp, $_GET['arrived_friend_request_id'], $_SESSION['user_info']['id']);
    }
}

// handle customer task completion
if (isset($_GET['completed_friend_request_id'], $_POST['complete_friend_request'], $_POST['friend_id'], $_POST['customer_id'], $_POST['request_rating'])) {
    $review = isset($_POST['request_review']) ? $_POST['request_review'] : "";
    $timestamp = date("Y-m-d h:i:s", time());
    $page->processFriendCompleteRequest($_GET['completed_friend_request_id'], $_POST['customer_id'], $_POST['friend_id'], $_POST['request_rating'], $review, $timestamp);
}

//--------------------- marketplace actions

// handle freelancer acceptance and withdrawal
if (isset($_GET['accept_freelancer_request_id'])) {
    $page->processFreelancerAcceptRequest($_GET['accept_freelancer_request_id'], $_SESSION['user_info']['id']);
} else if (isset($_GET['withdraw_request_id'], $_GET['freelancer_id'])) {
    $page->processCancelPendingRequest($_GET['withdraw_request_id'], $_GET['freelancer_id']);
}

// handle customer accept and reject freelancer
if (isset($_GET['accept_customer_request_id'], $_GET['freelancer_id'])) {
    $page->processCustomerAcceptRequest($_GET['accept_customer_request_id'], $_GET['freelancer_id'], $_SESSION['user_info']['id']);
} else if (isset($_GET['reject_customer_request_id'], $_GET['freelancer_id'])) {
    $page->processCancelPendingRequest($_GET['reject_customer_request_id'], $_GET['freelancer_id'], $_SESSION['user_info']['id']);
}

// handle freelancer arrival
if (isset($_GET['freelancer_arrived'], $_GET['arrived_request_id'])) {
    if ($_GET['freelancer_arrived'] == 'true') {
        $timestamp = date("Y-m-d h:i:s", time());
        $page->processFreelancerArrived($_GET['freelancer_arrived'], $timestamp, $_GET['arrived_request_id'], $_SESSION['user_info']['id']);
    }
}

// handle customer task completion
if (isset($_GET['completed_request_id'], $_POST['complete_request'], $_POST['freelancer_id'], $_POST['customer_id'], $_POST['request_rating'])) {
    $review = isset($_POST['request_review']) ? addslashes($_POST['request_review']) : "";
    $timestamp = date("Y-m-d h:i:s", time());

    $complete = $page->processCompleteRequest($_GET['completed_request_id'], $_POST['customer_id'], $_POST['freelancer_id'], $_POST['request_rating'], $review, $timestamp);

    if($complete) {
        //Send Invoice to Customer
        $page->invoice->sendCustomerInvoice($_GET['completed_request_id'])->sendFreelancerInvoice($_GET['completed_request_id']);

        //Send Pay out to Freelancer
        $page->payout->payoutFundsToFreelancer($_GET['completed_request_id']);

        //Redirect to Home to Prevent Double Payout
        header("Refresh:2; url=$this->root_path/home");
    }
}

$page->setTitle("Notifications");
$page->renderHeader();

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE.
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
    <div id="notifications"></div>
<?php
$page->renderMainNotifications($_SESSION['user_info']);
$page->addScript("
<script>
    //hide stripe button, automatically checkout
    $('.stripe-button-el').hide();
    $(document).ready(function(){
      $('.stripe-button-el').click();
    });
            
    
//    window.setInterval(function(){
//      // call your function here
//        $('#notifications').load('notifications.php')
//    }, 5000);
</script>
");
$page->addScript("
<!-- Hotjar Tracking Code for askfavr.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:893054,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
   
    <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1650241185015256');
        fbq('track', 'PageView');
        </script>
        <noscript>
            <img height=\"1\" width=\"1\" style=\"display:none\"
            src=\"https://www.facebook.com/tr?id=1650241185015256&ev=PageView&noscript=1\"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    <script>");
$page->renderFooter();