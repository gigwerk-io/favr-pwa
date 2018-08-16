<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/16/18
 * Time: 2:58 PM
 */
require_once '../../libraries/Api/Stripe/init.php';
$payment = new Web_Payment();
if(isset($_POST['stripeToken'])){
    $payment->charge($_POST['stripeToken'])->update($_GET['id']);
}else{

}