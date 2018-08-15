<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/15/18
 * Time: 11:18 AM
 */
require '../../libraries/Api/Stripe/init.php';
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 5;
$USER = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);
$connect = new Web_Connect();
if(!is_null($connect->payment_id)) {
//    echo "<a href='?auth=1'><button>Click</button></a>";
//    if(isset($_GET['auth']) && ($_GET['auth'] == 1))
//    {
        $connect->stripeLogin($connect->payment_id);
//    }
} else{
    if(!isset($_GET['code']))
    {
        echo "<script> window.location.href = 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=https://askfavr.com/favr-pwa/home/payments/&client_id=ca_C2CKbfLxpwpxjuTp9xdtuBcL5zSws9mN&state=true'; </script>";
    }
    elseif(isset($_GET['code']))
    {
        $connect->savePaymentAccount($_GET['code']);
    }
}

