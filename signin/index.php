<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/6/18
 * Time: 11:42 PM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/include/autoload.php");

if (isset($_SESSION['user_info']) && $_SESSION['user_info']['id'] != -1) {
    header("Location: ../home/");
}
// constants
$USER = "guest";
$ALERT_MESSAGE = "";
$CRUD_INDEX_DB = "";
//
//die(print_r($_SESSION));

$page = new Web_Page($USER);

if (isset($_GET['d_idb']) && $_GET['d_idb'] = true) {
    $CRUD_INDEX_DB = "
        <script>
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
          deleteItem();
        };
        openRequest.onerror = function(e) {
          console.log('onerror!');
          console.dir(e);
        };
        
        function deleteItem() {
          var transaction = db.transaction(['store'], 'readwrite');
          var store = transaction.objectStore('store');
          
          var request = store.delete(0);
          
          request.onerror = function (ev) { 
              console.log('Error', ev.target.result);
           };
          request.onsuccess = function (ev) {
              console.log('[Delete]');
          }
        }
        </script>
    ";

    $page->addScript($CRUD_INDEX_DB);
//    $page->addScript("
//<!-- Hotjar Tracking Code for askfavr.com -->
//    <script>
//        (function(h,o,t,j,a,r){
//            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
//            h._hjSettings={hjid:893054,hjsv:6};
//            a=o.getElementsByTagName('head')[0];
//            r=o.createElement('script');r.async=1;
//            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
//            a.appendChild(r);
//        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
//    </script>
//
//    <!-- Facebook Pixel Code -->
//        <script>
//        !function(f,b,e,v,n,t,s)
//        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
//        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
//        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
//        n.queue=[];t=b.createElement(e);t.async=!0;
//        t.src=v;s=b.getElementsByTagName(e)[0];
//        s.parentNode.insertBefore(t,s)}(window, document,'script',
//        'https://connect.facebook.net/en_US/fbevents.js');
//        fbq('init', '1650241185015256');
//        fbq('track', 'PageView');
//        </script>
//        <noscript>
//            <img height=\"1\" width=\"1\" style=\"display:none\"
//            src=\"https://www.facebook.com/tr?id=1650241185015256&ev=PageView&noscript=1\"/>
//        </noscript>
//        <!-- End Facebook Pixel Code -->
//    <script>");
}

if (isset($_GET['signout']) && $_GET['signout'] == true) {
    if ($page->signOutUser()) {
        header("Location: ../signin/?d_idb=true");
    }
    die("You have successfully signed out, come back again");
}

// Script to process user sign in
if (isset($_POST['signIn'], $_POST['signInUsernameEmail'], $_POST['signInPass'])) {
    $signInUsernameEmail = $_POST['signInUsernameEmail'];
    $signInPass = $_POST['signInPass'];

    $signInSuccessful = $page->signInUser($signInUsernameEmail, $signInPass);

    if ($signInSuccessful) {
        // successful signin with redirect
        $_SESSION['user'] = $signInUsernameEmail;
        header("Location: ../home/");
    } else {
        // failure
        $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-danger alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Error!</strong> Ope, Better check yourself, your credentials don't seem to match anything in our system.
            </div>
        ";
    }
}

$page->setTitle("FAVR | Sign In");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/signin.css' />");
$page->renderHeader(false);

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
?>
    <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="pb-2 mb-0">
            <div class="text-center">
                <img src="<?php echo $page->root_path; ?>/assets/brand/favr_logo_rd.png" class="img-fluid" height="30%"
                     width="30%">
            </div>
        </h6>
        <form class="form-signin" method="post">
            <div class="form-label-group">
                <input type="text" name="signInUsernameEmail" id="inputUsernameEmail" class="form-control" placeholder="Email or Username" required="">
                <label for="inputUsernameEmail">Email or Username</label>
            </div>
            <div class="form-label-group">
                <input type="password" name="signInPass" id="inputPassword" class="form-control" placeholder="Password" required="">
                <label for="inputPassword">Password</label>
            </div>
            <div class="row">
                <div class="d-inline-flex">
                    <label>
                        <a href="#">Forgot password</a>
                    </label>
                    <label class="ml-2">
                        <a href="<?php echo $page->root_path; ?>/signup/">New account</a>
                    </label>
                </div>
                <input type="submit" name="signIn" class="btn btn-lg btn-primary btn-block" value="Sign In">
            </div>
        </form>
    </div>
<?php
$page->renderFooter();
?>