<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/15/18
 * Time: 11:18 AM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");
require '../../libraries/Api/Stripe/init.php';

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
$connect = new Web_Connect();
if(!is_null($connect->payment_id)) {
        $connect->stripeLogin($connect->payment_id);
} else{
    if(!isset($_GET['code']))
    {
        echo "<script> window.location.href = 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=htts://askfavr.com/favr-pwa/home/payments/&client_id=ca_C2CKbfLxpwpxjuTp9xdtuBcL5zSws9mN&state=true'; </script>";
    }
    elseif(isset($_GET['code']))
    {
        $connect->savePaymentAccount($_GET['code']);
    }
}

