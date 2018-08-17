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

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}


if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);

if (isset($_POST['requestFavr'])) {
    $successfulProcessToDB = $page->processFavrFriendRequestToDB($_SESSION['user_info'], $_POST['requestDate'], $_POST['requestCategory'], $_POST['requestTaskDescription'], $_POST['requestPrice'], 1, $_POST['requestStreetAddress'], $_POST['requestDifficulty'], $_FILES['requestPictures']);

    if (!$successfulProcessToDB) {
        $ALERT_MESSAGE = "There was a problem submitting your request to the server try again!";
        $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-warning alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Whoops!</strong> $ALERT_MESSAGE
            </div>
        ";
    }
}

if (isset($_GET['d_friend_request_id'])) {
    $page->processFriendDeleteRequest($_GET['d_friend_request_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['friends_list']) && $_GET['friends_list'] == 'true') {
    $render_back_button = true;
} else if (isset($_GET['ask_favr'], $_GET['id']) && $_GET['ask_favr'] == 'true') {
    $render_back_button = true;
} else {
    $render_back_button = false;
}

$page->setTitle("Friends");
$page->renderHeader(true, $render_back_button);
echo $ALERT_MESSAGE;
if (isset($_GET['friends_list']) && $_GET['friends_list'] == 'true') {

    if (isset($_GET['add_friend'])) {
        $user_id = $_GET['id'];
        $requester_id = $_SESSION['user_info']['id'];
        if ($_GET['add_friend'] == 'true') {
            $page->processFavrFriendRequest($user_id, $requester_id, true);
        } else if ($_GET['add_friend'] == 'false') {
            $page->processFavrFriendRequest($user_id, $requester_id, false);
        }
    }

    $_SESSION['navbar'] = "friends_list";
    $page->renderFriendList($_SESSION['user_info']['id']);
} else if (isset($_GET['ask_favr'], $_GET['id']) && $_GET['ask_favr'] == 'true') {
    if ($_GET['id'] != $_SESSION['user_info']['id']) { // can't ask yourself for a favr
        $page->renderFavrFriendsRequestForm(true, $_GET['id']);
    }
} else {
    if (isset($_GET['add_friend'])) {
        $user_id = $_GET['id'];
        $requester_id = $_SESSION['user_info']['id'];
        if ($_GET['add_friend'] == 'true') {
            $page->processFavrFriendRequest($user_id, $requester_id, true);
        } else if ($_GET['add_friend'] == 'false') {
            $page->processFavrFriendRequest($user_id, $requester_id, false);
        }
    }

    $page->renderFavrFriendsRequestForm();
    ?>
    <div class="my-3 p-3">
        <h6 class="border-bottom border-gray pb-2 mb-0">
            <small class="col-sm-6 pl-0">
                Filter by:
                <a href="?filter_marketplace_by=task_price&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Price </a>|
                <a href="?filter_marketplace_by=task_date&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Date </a>|
                <a href="?filter_marketplace_by=task_price&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Time </a>|
                <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>&scope=<?php echo $_SESSION['user_info']['id']; ?>">Mine </a>|
                <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>&scope=global">Global</a>

            </small>
            <small class="col-sm-6 pl-0">
                Orientation:
                <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=ASC&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Asc </a>|
                <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=DESC&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>">Desc</a>
            </small>
        </h6>
        <small class="d-block mt-3">
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by=<?php echo $_SESSION['limit_marketplace_by']; ?>"
               class="float-left">Refresh</a>
            <a href="?friends_list=true"
               class="ml-5">My friends</a>
            <a href="?filter_marketplace_by=<?php echo $_SESSION['filter_marketplace_by']; ?>&orient_marketplace_by=<?php echo $_SESSION['orient_marketplace_by']; ?>&limit_marketplace_by="
               class="float-right">All updates</a>
        </small>
    </div>
    <?php
    $page->renderFavrFriendsMarketplace($_SESSION['scope'], $_SESSION['filter_marketplace_by'], $_SESSION['orient_marketplace_by'], $_SESSION['limit_marketplace_by']);
    $page->renderFriendSuggestions($_SESSION['user_info']['id']);
}
$page->addScript("
<script>
    $(function(){
        var dtToday = new Date();
        
        var month = dtToday.getMonth() + 1;
        var day = dtToday.getDate();
        var year = dtToday.getFullYear();
        var hour = dtToday.getHours();
        var minute = dtToday.getMinutes();
        
        if (month < 10)
            month = '0' + month.toString();
        if (day < 10)
            day = '0' + day.toString();
        if (hour < 10)
            hour = '0' + hour.toString();
        if (minute < 10)
            minute = dtToday.getMinutes();
        
        var maxDate = year + '-' + month + '-' + day + '\T' + hour + ':' + minute;
        $('#inputDate').attr('min', maxDate);
    });
    
    window.addEventListener('load', function(){
        var allimages= document.getElementsByTagName('img');
        for (var i=0; i<allimages.length; i++) {
            if (allimages[i].getAttribute('data-src')) {
                allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
            }
        }
    }, false);
        
    $(document).ready(function() {
//        window.setInterval(function(){
//            $('#marketplace').load('marketplace.php')
//        }, 10000);
        
        $('#hard-button').click(function() {
            $('#hard-button').removeClass('unfocus');
            $('#hard-button').addClass('focus');
            $('#medium-button').removeClass('focus');
            $('#medium-button').addClass('unfocus');
            $('#easy-button').removeClass('focus');
            $('#easy-button').addClass('unfocus');
            $('#difficulty').val('Hard');
        });
        
        $('#medium-button').click(function() {
            $('#hard-button').removeClass('focus');
            $('#hard-button').addClass('unfocus');
            $('#medium-button').removeClass('unfocus');
            $('#medium-button').addClass('focus');
            $('#easy-button').removeClass('focus');
            $('#easy-button').addClass('unfocus');
            $('#difficulty').val('Medium');
        });
        
        $('#easy-button').click(function() {
            $('#hard-button').removeClass('focus');
            $('#hard-button').addClass('unfocus');
            $('#medium-button').removeClass('focus');
            $('#medium-button').addClass('unfocus');
            $('#easy-button').removeClass('unfocus');
            $('#easy-button').addClass('focus');
            $('#difficulty').val('Easy');
        });
        
        $('#favr-fabBtn').click(function() {
          $('.request-favr-mobile').toggle();
          $('#favr-fabBtn').toggleClass('favr-fab-fade');
        });

        $('.request-favr-mobile').hide();
        $('#request-favr-web').click(function() {
            $('.request-favr-mobile').toggle();
            $('.request-favr-mobile').focus();
        });
        
        $('.request-favr').click(function() {
            $('.request-favr-mobile').toggle();
            $('.favr-fab-fab').toggle();
            $('.request-favr-mobile').focus();
        });
    } );
</script>
");
$page->renderFooter();
?>