<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 10/15/18
 * Time: 9:27 AM
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
$page->setTitle("Chat");
$page->renderHeader();
$chat = new Web_Chat();
?>
<style>
    #messenger {
        width: 100%;
        max-width: 400px;
        margin: 30px auto;
        /* height: 450px;
        overflow: auto; */
        position: relative;
        box-shadow: 0 0 6px black;
    }

    li,
    ul {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .temizle {
        clear: both;
    }

    ul#menu {
        background: #007ee5;
    }

    ul#menu li {
        display: inline-block;
        width: 24%;
        text-align: center;
        padding: 19px 0 16px 0;
        box-sizing: border-box;
    }

    .border {
        border-bottom: 3px solid white;
    }

    ul#menu li a {
        text-decoration: none;
        color: white;
    }

    #ana {
        box-sizing: border-box;
        padding: 5px;
        font-family: "Arial";
        font-size: 14px;
    }

    .pfoto img {
        border-radius: 100%;
        width: 100%;
        max-width: 40px;
        height: 45px;
    }

    .pfoto {
        padding: 5px;
        float: left;
        width: 15%;
        box-sizing: border-box;
    }

    .mesaj {
        float: left;
        margin-top: 7px;
        position: relative;
        width: 85%;
        box-sizing: border-box;
        line-height: 1.4em;
    }

    .mesaj span.right {
        position: absolute;
        right: 10px;
        color: #007ee5;
        font-weight: bold;
    }

    .mesaj span.kisi {
        font-weight: 600;
    }

    ul#asagi {
        background: #007ee5;
    }

    ul#asagi li {
        display: inline-block;
        width: 32.3333%;
        text-align: center;
        padding: 19px 0 16px 0;
        box-sizing: border-box;
    }

    ul#asagi li a {
        text-decoration: none;
        color: white;
    }

    .mesaj img.sag {
        position: absolute;
        right: 10px;
        border-radius: 100%;
        width: 100%;
        max-width: 15px;
        padding-top: 5px;
    }

    .mesaj span.right-g {
        position: absolute;
        right: 10px;
    }

    .profil {
        width: 100%;
        padding: 6px;
        box-sizing: border-box;
        border-bottom: 1px solid #eee;
    }

    #baglanti {
        width: 100%;
        text-align: center;
        padding: 3px;
        box-sizing: border-box;
        color: white;
        background-color: #01DF01;
    }
</style>
<div class="card-header text-center" style="margin-top: 20px;">
    Chat Rooms
</div>
<div class="col-sm-12 card">
    <div id="ana">
        <?php
        $chat->processChatListing();
        ?>
    </div>
</div>
<?php
$page->renderFooter();
?>

