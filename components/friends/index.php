<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 3:02 AM
 *
 * @author haronarama
 */

include($_SERVER['DOCUMENT_ROOT'] . "/property-manager/include/autoload.php");

// component constants
$PAGE_ID = 4;
$USER = "Richard"; // user is set from initial configuration

$page = new Web_Page($PAGE_ID, $USER);
$data = new Data_Table("$PAGE_ID-friends", "friends-table", $page);
$page->setTitle("Tenants");
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
$page->printHeader();

if (isset($_GET['id'])) {
    $data->deleteTenant($_GET['id'], true, true);
}
?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
        <h1 class="h2">Tenants</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group mr-2">
                <button class="btn btn-sm btn-outline-secondary">Share</button>
                <button class="btn btn-sm btn-outline-secondary">Export</button>
            </div>
            <button class="btn btn-sm btn-outline-secondary dropdown-toggle">
                <span data-feather="calendar"></span>
                This week
            </button>
        </div>
    </div>

    <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel" data-pause="hover">
        <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
<!--            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>-->
        </ol>
        <div class="carousel-inner">
            <div class="carousel-item active">
                <h3>My Tenants</h3>
                <?php
                $query = "SELECT t.id, t.first_name, t.middle_name, t.last_name, t.rent, u.address, t.payment_status, t.notes FROM `friends` t INNER JOIN `units` u ON t.`units_id`=u.`id` AND t.`status`='Active'";
                $columns = array(
                    "id",
                    "first_name",
                    "middle_name",
                    "last_name",
                    "rent",
                    "address",
                    "payment_status",
                    "notes"
                );

                $column_names = array(
                    "ID",
                    "First Name",
                    "Middle Name",
                    "Last Name",
                    "Rent",
                    "Address",
                    "Rental Status",
                    "Notes",
                    "Actions"
                );

                $data->createTableFromQuery($query, $columns, $column_names, true, $data->table_id);
                ?>
            </div>
            <div class="carousel-item">
                <h3>Rent Graph</h3>
<!--                <canvas class="my-4" id="myChart" width="900" height="380"></canvas>-->
                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EComing%20soon%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Coming soon">
            </div>
<!--            <div class="carousel-item">-->
<!--                <img class="d-block w-100" src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22800%22%20height%3D%22400%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20800%20400%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_16404fff51b%20text%20%7B%20fill%3A%23333%3Bfont-weight%3Anormal%3Bfont-family%3AHelvetica%2C%20monospace%3Bfont-size%3A40pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_16404fff51b%22%3E%3Crect%20width%3D%22800%22%20height%3D%22400%22%20fill%3D%22%23555%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%22277%22%20y%3D%22218.3%22%3EThird%20slide%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E" alt="Third slide">-->
<!--            </div>-->
        </div>
        <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

<?php
//$data->editDataTableEntry();

$page->addScript("
    <script>
        $(document).ready(function() {
            $(fr$PAGE_IDifriendsataTable();
        } );
    </script>
");
$page->printFooter();
?>