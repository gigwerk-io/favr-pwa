<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 8/19/18
 * Time: 6:00 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "favr-pwa/include/autoload.php");

if (isset($_SESSION['user'])) {
    $USER = $_SESSION['user'];
} else {
    $USER = 'guest';
}

$page = new Web_Page($USER);
$page->setTitle("On Demand Services");
$page->addStylesheet("<link href='$page->root_path/assets/css/favr-home.css' rel='stylesheet' />");
$page->renderHeader(false, false, true);
?>
    <section class="jumbotron rounded-0 text-center">
        <div class="container text-white">
            <h1 style="text-shadow: 0 2px 2px red" class="jumbotron-heading font-italic font-weight-bold">Get things done</h1>
            <strong class="lead text-light">At your own price and at your own time. Make friends, and give/receive FAVRs for free! Join today and earn money as a freelancer too!</strong>
            <p>
                <a href="../signin" class="btn btn-primary my-2"><strong>Freelancer</strong> <img class="d-inline" src="../assets/brand/favr_logo_blk.png" height="13.3" width="45"></a>
                <a href="../signin" class="btn btn-dark my-2"><strong>Customer</strong> <img class="d-inline" src="../assets/brand/favr_logo_rd.png" height="13.3" width="45"></a>
            </p>
        </div>
    </section>

    <div class="album py-5 bg-light">
        <div class="container">

            <div class="row">
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <img class="card-img-top" src="../favr-profile-images/freelancers.jpg" alt="Freelancer" style="height: 100%; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">
                                As a freelancer control your schedule and make money on the side doing simple quick tasks within your expertise.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <img class="card-img-top" src="../favr-profile-images/freelancer.jpg" alt="Freelancers" style="height: 100%; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">
                                Available task categories are but not limited to...
                            </p>
                            <hr class="text-muted">
                            <ul>
                                <li>Yardwork <p class="d-inline small">(Spring, Summer, Fall)</p></li>
                                <li>Snow Removal <p class="d-inline small">(Winter)</p></li>
                                <li>Home Improvement <p class="d-inline small">(All Seasons)</p></li>
                                <li>General Requests <p class="d-inline small">(Need groceries picked up? No problem!)</p></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card mb-4 box-shadow">
                        <img class="card-img-top" src="../favr-profile-images/rochester.jpg" alt="Rochester" style="height: 100%; width: 100%; display: block;">
                        <div class="card-body">
                            <p class="card-text">At the moment we are operating in Rochester, Minnesota only but we've got big plans to expand soon to a place near you.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
<?php
$page->renderFooter();
?>
