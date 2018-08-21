<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/16/18
 * Time: 2:58 PM
 */

include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");
require_once '../../libraries/Api/Stripe/init.php';
$payment = new Web_Payment();
echo '<pre>';
if(isset($_POST['stripeToken'])){
    $payment->addStripeToken($_POST['stripeToken'], $_GET['id']);
    $payment->charge($_POST['stripeToken'])->update($_GET['id'], $_GET['url']);
}else{
    echo "<script>
            alert('Payment Failure!');
         </script>";
}