<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/14/18
 * Time: 3:29 PM
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
$page->setTitle("CFO Admin");
$page->renderAdminHeader($_SESSION['user_info']['id']);

echo "
<body>
<div class=\"container-fluid\">
    <div class=\"row\">";
$page->renderAdmin("CFO");
$page->getTransfersTable();
echo "</div>
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

<!-- Graphs -->
<script src=\"https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.1/Chart.min.js\"></script>
<script>
    var ctx = document.getElementById(\"myChart\");
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: [\"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\", \"Friday\", \"Saturday\"],
            datasets: [{
                data: [15339, 21345, 18483, 24003, 23489, 24092, 12034],
                lineTension: 0,
                backgroundColor: 'transparent',
                borderColor: '#007bff',
                borderWidth: 4,
                pointBackgroundColor: '#007bff'
            }]
        },
        options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: false
                    }
                }]
            },
            legend: {
                display: false,
            }
        }
    });
</script>");