<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 3:01 AM
 *
 * @author haronarama
 */

include($_SERVER['DOCUMENT_ROOT'] . "/property-manager/include/autoload.php");

// component constants
$PAGE_ID = 2;
$USER = "Richard"; // user is set from initial configuration

$page = new Web_Page($PAGE_ID, $USER);
$page->setTitle("Reports");
$page->printHeader();
?>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2">Reports</h1>
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
<?php
$page->printFooter();
?>