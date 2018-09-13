<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/12/18
 * Time: 11:24 PM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);

$page->setTitle("Results");
$page->renderHeader();

if(!empty($_GET['q'])){
    $page->searchFeature($_GET['q']);
}
$page->renderFooter();