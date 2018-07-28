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

// TODO: Constant variable statically used: active_home
if (isset($_SESSION['navbar']) && $_SESSION['navbar'] != "active_home") {
    $_SESSION['navbar'] = "active_home";
}

$page = new Web_Page($PAGE_ID, $USER);

$page->setTitle("Home");
$page->renderHeader();

if (isset($_POST['requestFavr'])) {
    $successfulProcessToDB = $page->processFavrRequestToDB($_SESSION['user_info'], $_POST['requestDate'], $_POST['requestCategory'], $_POST['requestTaskDescription'], $_POST['requestPrice'], $_POST['requestFreelancerCount'], $_POST['requestStreetAddress'], $_POST['requestDifficulty'], $_FILES['requestPictures']);

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

//$array = unserialize("a:0:{}");
////array_push($array, 3);
//
//die(print_r(count($array)));

$page->renderFavrRequestForm($_SESSION['user_info'], $_SESSION['filter_marketplace_by'], $_SESSION['orient_marketplace_by'], $_SESSION['limit_marketplace_by']);
?>
<div class="zoom">
    <a class="zoom-fab zoom-btn-large text-center" id="zoomBtn">
        <i style="padding: .8rem;background: transparent;color: var(--white);font-size: xx-large" class="material-icons">create</i>
    </a>
</div>
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
    window.addEventListener('load', function(){
        var allimages= document.getElementsByTagName('img');
        for (var i=0; i<allimages.length; i++) {
            if (allimages[i].getAttribute('data-src')) {
                allimages[i].setAttribute('src', allimages[i].getAttribute('data-src'));
            }
        }
    }, false);
        
    $(document).ready(function() {
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
        
        $('#zoomBtn').click(function() {
          $('.request-favr-mobile').toggle();
          $('#zoomBtn').toggleClass('zoom-fade');
        });

        $('.request-favr-mobile').hide();
        $('#request-favr-web').click(function() {
            $('.request-favr-mobile').toggle();
            $('.request-favr-mobile').focus();
        });
        
        $('.request-favr').click(function() {
            $('.request-favr-mobile').toggle();
            $('.zoom-fab').toggle();
            $('.request-favr-mobile').focus();
        });
    } );
    
    var db;

    var openRequest = indexedDB.open('favr_db', 1);
    
    openRequest.onupgradeneeded = function(e) {
      var db = e.target.result;
      console.log('running onupgradeneeded');
      if (!db.objectStoreNames.contains('store')) {
        var storeOS = db.createObjectStore('store',
          {keyPath: 'id'});
        storeOS.createIndex('id', 'id', {unique: true});
      }
    };
    openRequest.onsuccess = function(e) {
      console.log('running onsuccess');
      db = e.target.result;
      addItem();
    };
    openRequest.onerror = function(e) {
      console.log('onerror!');
      console.dir(e);
    };
    
    function addItem() {
      var transaction = db.transaction(['store'], 'readwrite');
      var store = transaction.objectStore('store');
      
      var item = {
        id: 0,
        username: '". $_SESSION['user_info']['username'] ."',
        password: '". $_SESSION['user_info']['password'] ."',
        created: new Date().getTime()
      };
    
     var request = store.add(item);
    
     request.onerror = function(e) {
        console.log('Error', e.target.error.name);
      };
      request.onsuccess = function(e) {
        console.log('[success]');
      };
    }
    </script>
");
$page->renderFooter();
?>