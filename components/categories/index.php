<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/22/18
 * Time: 11:52 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);

$page->setTitle("Categories");
$page->renderHeader();
?>
<!--<div class="row p-3">-->
<!--    <div class="my-3 p-3 bg-white rounded box-shadow" style="height: 15rem;width: 50%;">-->
<!--        Hello-->
<!--    </div>-->
<!--</div>-->
<?php
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
</script>
");
$page->renderFooter();
?>