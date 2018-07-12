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
$page->addStylesheet("
<style>
/**
 * Carousel
 */
.carousel-control-prev,
.carousel-control-next {
    width: 2%;
}
</style>
");
$page->renderHeader();

print_r("Here");
?>

<!--    <div class="d-flex align-items-center p-3 my-3 text-white-50 bg-secondary rounded box-shadow">-->
<!--        <img class="mr-3" src="--><?php //echo $page->root_path; ?><!--/assets/brand/safari-pinned-tab.svg" alt="" width="48" height="48">-->
<!--        <div class="lh-100">-->
<!--            <h6 class="mb-0 text-white lh-100">FAVR</h6>-->
<!--            <small>Since 2017, "Quid pro Quo"</small>-->
<!--        </div>-->
<!--    </div>-->

<!--    <div class="my-3 p-3 alert alert-warning alert-dismissible" role="alert">-->
<!--        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>-->
<!--        <strong>Warning!</strong> Better check yourself, you're not looking too good.-->
<!--    </div>-->

    <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="border-bottom border-gray pb-2 mb-0">Recent updates</h6>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">
            <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <strong class="d-block text-gray-dark">@username</strong>
                Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
            </p>
        </div>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=e83e8c&fg=e83e8c&size=1" alt="" class="mr-2 rounded">
            <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <strong class="d-block text-gray-dark">@username</strong>
                Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
            </p>
        </div>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=6f42c1&fg=6f42c1&size=1" alt="" class="mr-2 rounded">
            <p class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <strong class="d-block text-gray-dark">@username</strong>
                Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus.
            </p>
        </div>
        <small class="d-block text-right mt-3">
            <a href="#">All updates</a>
        </small>
    </div>

    <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="border-bottom border-gray pb-2 mb-0">Suggestions</h6>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">
            <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <strong class="text-gray-dark">Full Name</strong>
                    <a href="#">Follow</a>
                </div>
                <span class="d-block">@username</span>
            </div>
        </div>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">
            <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <strong class="text-gray-dark">Full Name</strong>
                    <a href="#">Follow</a>
                </div>
                <span class="d-block">@username</span>
            </div>
        </div>
        <div class="media text-muted pt-3">
            <img data-src="holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1" alt="" class="mr-2 rounded">
            <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                <div class="d-flex justify-content-between align-items-center w-100">
                    <strong class="text-gray-dark">Full Name</strong>
                    <a href="#">Follow</a>
                </div>
                <span class="d-block">@username</span>
            </div>
        </div>
        <small class="d-block text-right mt-3">
            <a href="#">All suggestions</a>
        </small>
    </div>
<?php
//$chart->createChart(array(15339, 21345, 18483, 24003, 23489, 24092, 12034), array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), "myChart");
$page->addScript("
    <script>
        $(document).ready(function() {
            $('#$PAGE_ID-friends').DataTable();
        } );
        $(document).ready(function() {
            $('#$PAGE_ID-units').DataTable();
        } );
    </script>
");
$page->renderFooter();
?>