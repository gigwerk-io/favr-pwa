<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 3:02 AM
 *
 * @author haronarama
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

// TODO: Constant variable statically used: active_home
if (isset($_SESSION['navbar']) && $_SESSION['navbar'] != "active_home") {
    $_SESSION['navbar'] = "active_settings";
}

$page = new Web_Page($USER);

$page->setTitle("Settings");
$page->addStylesheet("<link rel='stylesheet' type='text/css' href='$page->root_path/assets/css/settings.css' />");
$page->renderHeader();

echo $ALERT_MESSAGE;
?>
    <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 1.2rem;">
        <div class="row pb-2 mb-0">
            <h3 style="width: 100%" class="text-center border-bottom border-gray">Account Settings</h3>
        </div>
        <div class="row p-0 mb-0">
            <div class="col-md-4">
                <div class="form-group border-bottom border-gray">
                    <label class="small text-left pb-0">Display my ratings</label>
                    <span class="float-right switch switch-sm">
                        <input type="checkbox" checked class="switch" id="rating">
                        <label for="rating"></label>
                    </span>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group border-bottom border-gray">
                    <label class="small text-left pb-0">Display receipts</label>
                    <span class="float-right switch switch-sm">
                        <input type="checkbox" checked class="switch" id="receipts">
                        <label for="receipts"></label>
                    </span>
                </div>
            </div>
            <div class="col-md-5">
                <div class="small form-group border-bottom border-gray">
                    <label class="text-left pb-0">Display my description</label>
                    <span class="float-right switch switch-sm">
                        <input type="checkbox" checked class="switch" id="description">
                        <label for="description"></label>
                    </span>
                </div>
            </div>
        </div>
        <div class="row pb-0 pt-0 mb-0">
            <div class="col-md-3 request-favr-web border-bottom border-gray"></div>
            <div class="col-md-6 pl-2 pr-2 pt-0 pb-2 border-bottom border-gray">
                <label for="scope">My default scope</label>
                <select class="form-control">
                    <option>Only me</option>
                    <option selected>Friends</option>
                    <option>Friends of friends</option>
                    <option>Public</option>
                </select>
            </div>
            <div class="col-md-3 request-favr-web border-bottom border-gray"></div>
        </div>
        <div class="row pb-1 mb-0">
            <div class="col-md-6 p-2 border-bottom border-gray">
                <a href="#">Terms of Service and Conditions
                    <i class="mobile-footer float-right text-muted material-icons">chevron_right</i>
                </a>
            </div>
            <div class="col-md-6 p-2 border-bottom border-gray">
                <a href="#">Change password
                    <i class="mobile-footer float-right text-muted material-icons">chevron_right</i>
                </a>
            </div>
        </div>
        <div class="row pb-1 mb-0">
            <div class="col-lg-12 text-center">
                <a href="#" class="text-danger">Delete my account</a>
            </div>
        </div>
    </div>
    <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 1.2rem;">
        <div class="row pb-2 mb-0">
            <h3 style="width: 100%" class="text-center border-bottom border-gray">Signed-In Sessions</h3>
        </div>
    </div>
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
    
//    window.setInterval(function(){
//      // call your function here
//        $('#notifications').load('notifications.php')
//    }, 5000);
</script>
");
$page->renderFooter();
?>