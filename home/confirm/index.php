<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/12/18
 * Time: 1:49 PM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

$confirm = new Web_Confirm();
$USER = "guest";
$page = new Web_Page($USER);

if(isset($_GET['src']) && isset($_GET['auth'])){
    $confirm->confirmAccount($_GET['src']);
    $signInSuccessful  = $page->signInUser(
      $page->encrypt_decrypt('decrypt', $_GET['src']),
      $page->encrypt_decrypt('decrypt', $_GET['auth'])
    );
    if ($signInSuccessful ) {
        // successful signin with redirect
        $_SESSION['user'] = $page->encrypt_decrypt('decrypt', $_GET['src']);
        header("Location: $page->root_path/home");
    }
}
