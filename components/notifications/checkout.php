<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/18/2018
 * Time: 11:40 PM
 *
 * @author Solomon Antoine
 */

include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");
include($_SERVER['DOCUMENT_ROOT'] .  '/favr-pwa/libraries/Api/Stripe/init.php');

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);
$page->setTitle("FAVR | Checkout");
$checkout = new Web_Payment();
if(!empty($_GET['task_id']) && !empty($_GET['url'])){
    $checkout->select($_GET['task_id'])->checkOut($_GET['task_id'], $_GET['url']); //redirects to payment process
}else{
    echo "<script> window.location.href = 'https://askfavr.com/favr-pwa/home'; </script>";
}



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
$page->renderFooter();

