<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/5/18
 * Time: 2:03 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php");

// component constants
$PAGE_ID = 5;
$USER = "";
$chat = new Web_Chat();


if(isset($_GET['chat_room']))
{
    echo "<div id=\"chat_data\">";
    $chat->getAllMessages($_GET['chat_room']);
    echo "</div>";
}