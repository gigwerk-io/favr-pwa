<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/15/18
 * Time: 11:18 AM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "favr-pwa/include/autoload.php");
//require '../../libraries/Api/Stripe/init.php';

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
$connect = new Web_Connect();
if(!empty($connect->payment_id)) {
        $connect->stripeLogin();
} else{
    if(!isset($_GET['code']))
    {
        $connect_site = Data_Constants::STRIPE_CONNECT;
        echo "<script> window.location.href = '$connect_site'; </script>";
    }
    elseif(isset($_GET['code']))
    {
        $connect->savePaymentAccount($_GET['code']);
    }
}

