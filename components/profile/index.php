<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 12:48 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 7;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

if (isset($_GET['d_request_id'])) {
    $page->processDeleteRequest($_GET['d_request_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}

$page->setTitle("". $_SESSION['user_info']['username'] ."");
$page->renderHeader();


echo $ALERT_MESSAGE;
?>
<!--    <img class="d-block img-fluid" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EComing%20soon%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Coming soon">-->
    <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 3rem;">
        <div class="row pb-2 mb-0">
            <div class="col-md-4">
            </div>
            <div class="col-md-4 text-center border-bottom border-gray">
                <img data-src="holder.js/7remx7rem?theme=thumb&amp;bg=007bff&amp;fg=007bff&amp;size=1" alt="128x128" class="rounded" style="bottom: 3.5rem;width: 7rem;height: 7rem;position: relative;" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" data-holder-rendered="true">
                <h3><?php echo $_SESSION['user_info']['first_name'] . " " . $_SESSION['user_info']['last_name']; ?><i class="material-icons text-primary">verified_user</i></h3>
                <p class="d-inline-flex mb-0" style="font-size: -webkit-xxx-large;font-weight: lighter">4.4<p class="row pl-3 d-inline-flex"><i style="position:relative;font-weight: lighter;font-size: medium;bottom:  1.2rem;left: .1rem;color:  var(--green);border: 1px solid;border-radius: 1rem;" class="material-icons">arrow_upward</i></p><p class="row pl-2 mb-0 d-inline-flex" style="font-weight: lighter;font-size: medium">29</p></p>
                <p style="color: var(--yellow)" class="small d-inline-flex"><i class="material-icons">star</i><i class="material-icons">star</i><i class="material-icons">star</i><i class="material-icons">star</i><i class="material-icons">star_half</i></p>
            </div>
            <div class="col-md-4">
            </div>
        </div>
        <div class="pb-2 mb-0">
            <p class="mr-3 text-center">
                I am a developer, and a verified freelancer.
            </p>
        </div>
    </div>
    <div class="row m-3 pt-3">
    </div>
<?php
$page->renderFavrProfileHistory($_SESSION['user_info']['id']);
$page->renderFooter();
?>