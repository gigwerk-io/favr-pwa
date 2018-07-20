<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 1;
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID", "friends-table", $page);
$chart = new Data_Chart("$PAGE_ID", "rent-chart", $page);

$page->setTitle("Home");
$page->renderHeader();

if (isset($_POST['requestFavr'])) {
    $page->processFavrRequestToDB($_SESSION['user_info'], $_POST['requestDate'], $_POST['requestTimeToAccomplish'], $_POST['requestTaskDescription'], $_POST['requestPrice']);
}

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

echo $ALERT_MESSAGE;

print_r($_SESSION);

$page->renderFavrRequestForm($_SESSION['user_info'], $_SESSION['filter_marketplace_by'], $_SESSION['orient_marketplace_by'], $_SESSION['limit_marketplace_by']);
?>
<div class="my-3 p-3">
    <h6 class="border-bottom border-gray pb-2 mb-0">
        <small class="col-sm-6 pl-0">
            Filter by:
            <a href="?filter_marketplace_by=task_price&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Price </a>|
            <a href="?filter_marketplace_by=task_date&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Date </a>|
            <a href="?filter_marketplace_by=task_price&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Time </a>|

<!--            Needs to be implemented-->
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>&scope=<?php echo $_SESSION['user_info']['id']; ?>">Mine </a>|
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>&scope=global">Global</a>

        </small>
        <small class="col-sm-6 pl-0">
            Orientation:
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=ASC&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Asc </a>|
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=DESC&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Desc</a>
        </small>
    </h6>
    <small class="d-block text-right mt-3">
        <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=">All updates</a>
    </small>
</div>
<?php
$page->renderFavrMarketplace($_SESSION['scope'], $_SESSION['filter_marketplace_by'], $_SESSION['orient_marketplace_by'], $_SESSION['limit_marketplace_by']);


$page->addScript("
    <script>
        $(document).ready(function() {
            $('.request-favr-mobile').hide();
            $('#request-favr-web').click(function() {
                $('.request-favr-mobile').toggle('slide', { direction: 'top' }, 4000);
            });
            
            $('.request-favr').click(function() {
                $('.request-favr-mobile').toggle('slide', { direction: 'top' }, 4000);
            });
        } );
    </script>
");
$page->renderFooter();
?>