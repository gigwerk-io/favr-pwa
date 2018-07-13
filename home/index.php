<?php
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 1;
$USER = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID", "friends-table", $page);
$chart = new Data_Chart("$PAGE_ID", "rent-chart", $page);

$page->setTitle("Home");
$page->renderHeader();

$page->renderFavrRequestForm();

if (isset($_POST['requestFavr'])) {
   $page->processFavrRequestToDB($_SESSION['user_info'], $_POST['requestDate'], $_POST['requestTimeToAccomplish'], $_POST['requestTaskDescription'], $_POST['requestPrice']);
}

?>
<div class="my-3 p-3 bg-white rounded box-shadow">
    <h6 class="border-bottom border-gray pb-2 mb-0">
        Recent updates
        <small class="d-inline float-right">
            Filter:
        </small>
    </h6>
        <?php
        $page->renderFavrMarketplace($_SESSION['user_info']);
        ?>
    <small class="d-block text-right mt-3">
        <a href="#">All updates</a>
    </small>
</div>
<?php

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