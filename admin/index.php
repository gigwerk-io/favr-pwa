<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/19/18
 * Time: 11:15 AM
 */
session_start();
include("/var/www/html/favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}



$page = new Web_Page($USER);
$page->setTitle("Administrator");
$page->renderAdminHeader($_SESSION['user_info']['id']);


echo "
<body>
<div class=\"container\">
    <div class=\"row my-3\">
        <div class=\"col-md-6\">
            <div class=\"card text-center\">
                <div class=\"card-block\">
                    <h4 class=\"card-title\">Chief Executive Officer</h4>
                    <h2><a href='ceo'> <i class=\"fa fa-cog fa-3x\" aria-hidden=\"true\"></i></a></h2>
                </div>
                <div class=\"row p-2 no-gutters\">
                    <div class=\"col-12\">
                        <div class=\"card card-block text-info rounded-0 border-left-0 border-top-o border-bottom-0\">
                            <h3>Solomon Antoine</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-md-6\">
            <div class=\"card text-center\">
                <div class=\"card-block\">
                    <h4 class=\"card-title\">Chief Technical Officer</h4>
                    <h2><a href='cto'><i class=\"fa fa-code fa-3x\" aria-hidden=\"true\"></i></a> </h2>
                </div>
                <div class=\"row p-2 no-gutters\">
                    <div class=\"col-12\">
                        <div class=\"card card-block text-info rounded-0 border-left-0 border-top-o border-bottom-0\">
                            <h3>Haron Arama</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-md-6 my-3\">
            <div class=\"card text-center card-info\">
                <div class=\"card-block\">
                    <h4 class=\"card-title\">Chief Marketing Officer</h4>
                    <h2><a href='cmo'><i class=\"fa fa-line-chart fa-3x\" aria-hidden=\"true\"></i></a></h2>
                </div>
                <div class=\"row p-2 no-gutters\">
                    <div class=\"col-12\">
                        <div class=\"card card-block text-info rounded-0 border-left-0 border-top-o border-bottom-0\">
                            <h3>D'Angelo Tines</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class=\"col-md-6 my-3\">
            <div class=\"card text-center card-info\">
                <div class=\"card-block\">
                    <h4 class=\"card-title\">Chief Financial Officer</h4>
                    <h2><a href='cfo'><i class=\"fa fa-usd fa-3x\" aria-hidden=\"true\"></i></a></h2>
                </div>
                <div class=\"row p-2 no-gutters\">
                    <div class=\"col-12\">
                        <div class=\"card card-block text-info rounded-0 border-left-0 border-top-o border-bottom-0\">
                            <h3>Ken Nguyen</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>";



$page->addScript("
<script src=\"https://code.jquery.com/jquery-3.2.1.slim.min.js\" integrity=\"sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN\" crossorigin=\"anonymous\"></script>
<script>window.jQuery || document.write('<script src=\"https://getbootstrap.com/docs/4.0/assets/js/vendor/jquery-slim.min.js\"><\/script>')</script>
<script src=\"https://getbootstrap.com/docs/4.0/assets/js/vendor/popper.min.js\"></script>
<script src=\"https://getbootstrap.com/docs/4.0/dist/js/bootstrap.min.js\"></script>

<!-- Icons -->
<script src=\"https://unpkg.com/feather-icons/dist/feather.min.js\"></script>
<script>
    feather.replace()
</script>
");