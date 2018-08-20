<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/14/18
 * Time: 10:59 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

if (isset($_SESSION['user'])) {
    $USER = $_SESSION['user'];
} else {
    $USER = 'guest';
}

$page = new Web_Page('ENTRY_POINT');

if (isset($_POST['persistUsernameEmail'], $_POST['persistPassword'])) {
    $signInSuccessful = $page->signInUser($_POST['persistUsernameEmail'], $_POST['persistPassword']);
    if ($signInSuccessful) {
        // successful persistent sign in
        $_SESSION['user'] = $_POST['persistUsernameEmail'];
        header("Location: home/?navbar=active_home&nav_scroller=active_marketplace");
//        die(print_r($_POST));
    } else {
        // error in persist
        header("Location: signin/");
    }
}

$page->renderHeader(false);

?>
    <form name="entry_point" method="POST">
        <input id="username" type="hidden" name="persistUsernameEmail" required>
        <input id="password" type="hidden" name="persistPassword" required>
        <input id="submit" style="visibility: hidden" type="submit" value="FAVR" />
    </form>
<?php
$page->addScript("
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
      getItem();
    };
    openRequest.onerror = function(e) {
      console.log('onerror!');
      console.dir(e);
    };
    
    function getItem() {
      var transaction = db.transaction(['store'], 'readwrite');
      var store = transaction.objectStore('store');
      
      var request = store.get(0);
      
      request.onerror = function (ev) { 
          console.log('Error', ev.target.result);
          $('#username').val('guest');
          $('#password').val('gotosignin');
          $('#submit').click();
       };
      request.onsuccess = function (ev) { 
//          console.log('YESSSS', ev.target.result.username);
          if (ev.target.result === undefined) {
              console.log('[undefined]');
              $('#username').val('guest');
              $('#password').val('gotosignin!');
              $('#submit').click();
          } else {
              console.log('[persist]');
              $('#username').val(ev.target.result.username);
              $('#password').val(ev.target.result.password);
              $('#submit').click();
          }
          
       }
    }
    </script>
");
$page->renderFooter();