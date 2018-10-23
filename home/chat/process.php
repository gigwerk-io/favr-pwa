<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/5/18
 * Time: 2:03 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

$chat = new Web_Chat();


if(isset($_GET['id']))
{
    //echo "<div id=\"chat_data\">";
    $chat->processChatMessages($_GET['id']);
    //echo "</div>";
    if(isset($_GET['message'])){
        $chat->processSendMessage($_GET['id'], $_GET['user'], $_GET['message']);
    }
}

