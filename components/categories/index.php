<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/22/18
 * Time: 11:52 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 9;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

$page->setTitle("Categories");
$page->renderHeader();
?>
<!--<div class="row p-3">-->
<!--    <div class="my-3 p-3 bg-white rounded box-shadow" style="height: 15rem;width: 50%;">-->
<!--        Hello-->
<!--    </div>-->
<!--</div>-->
<?php
$page->renderFooter();
?>