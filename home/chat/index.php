<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 11:37 AM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 5;
$USER = "";
$chat = new Web_Chat();

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

//header("Location: ../../assets/css/chat.css");

$page->setTitle("Chat");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/chat.css'>");
$page->renderHeader(false);

if(isset($_POST['message'])){
    $chat->sendMessage($_GET['chat_room'], $_SESSION['user_info']['id'], $_POST['message']);
    $_POST = array();
}
?>
<!--    <meta http-equiv="refresh" content="30"/>-->

<div class="messaging m-0 p-0">
    <div class="inbox_msg">
        <div class="headind_srch">
            <div class="recent_heading">
                <?php
                if (isset($_GET['chat_room'])) {
                    ?>
                    <a class="request-favr-web" href="<?php echo $page->root_path; ?>/home/?navbar=active_home&nav_scroller=active_marketplace">
                        <img src="<?php echo $page->root_path; ?>/assets/brand/favr_logo_rd.png" height="21" width="70"
                             class="navbar-brand mr-0" style="padding-top: 0; padding-bottom: 0" alt="Logo">
                    </a>
                    <a class="mobile-footer" href="<?php echo $page->root_path; ?>/home/chat/">
                        <i class="material-icons">chevron_left</i><p class="d-inline-flex">back</p>
                    </a>
                    <?php
                } else {
                    ?>
                    <a href="<?php echo $page->root_path; ?>/home/?navbar=active_home&nav_scroller=active_marketplace">
                        <img src="<?php echo $page->root_path; ?>/assets/brand/favr_logo_rd.png" height="21" width="70"
                             class="navbar-brand mr-0" style="padding-top: 0; padding-bottom: 0" alt="Logo">
                    </a>
                    <?php
                }
                ?>

            </div>
            <div class="srch_bar">
                <div class="stylish-input-group">
                    <input type="text" class="search-bar" placeholder="Search">
                    <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span></div>
            </div>
        </div>
        <div id="chat_list" class="inbox_people <?php echo $mobile_view = (isset($_GET['chat_room'])) ?  "web-messaging" : ""; ?>">

            <?php
            echo "<div class=\"inbox_chat\">";
            $chat->getChatList();
            echo "</div>";
            echo "<div id=\"status\">
                </div>";
            ?>
        </div>
        <div id="chat_room" class="mesgs p-0 <?php echo $mobile_view = (!isset($_GET['chat_room'])) ?  "web-messaging" : ""; ?>">
            <div class="msg_history p-2 pt-5" onload="ajax();">
                <?php
                if(isset($_GET['chat_room']))
                {
                    $chat_id = $_GET['chat_room'];
                    echo "<div id=\"chat\"></div>";
                    //$chat->getAllMessages($_GET['chat_room']);
                }
                ?>
            </div>
            <?php
            if(isset($_GET['chat_room'])){
                echo " <div class=\"type_msg m-0\" style='
                                                            position: fixed!important;
                                                            bottom:  0;
                                                            right:  0;
                                                            left: 0;'>
                        <div class=\"input_msg_write\">
                                <form action=\"index.php?chat_room=$chat_id\" method=\"post\" id=\"Message\">
                                    <textarea style=\"border-radius: 0;\" type=\"text\" class=\"form-control m-0 pt-2\" name=\"message\" placeholder=\"Type a message...\"></textarea>
                                </form>
                                <button class=\"msg_send_btn text-center p-1\" form=\"Message\" type=\"submit\"><i class=\"material-icons\"
                                                                                                             aria-hidden=\"true\">send</i></button>
                            </div>
                        </div></div>";
            }
            ?>
        </div>
    </div>
</div>
<?php
// TODO: I shouldn't need to do this if there already is a getChatList function make it return some IDs
$query = $page->db->query("SELECT * FROM marketplace_favr_chat_rooms WHERE customer_id=$chat->id or freelancer_id=$chat->id");
$rows = $query->fetchAll(PDO::FETCH_ASSOC);

$chat_room_ids = "var char_room_ids = [";
foreach ($rows as $row) {
    $chat_room_ids .=  $row['id'] . ",";
}
$chat_room_ids .= "-1];";

$page->addScript("
    <script>
        
        function ajax() {
            var req = new XMLHttpRequest();

            req.onreadystatechange = function () {
                if(req.readyState == 4 && req.status==200){
                    document.getElementById('chat').innerHTML = req.responseText;
                }
            }
            req.open('GET', 'process.php?chat_room=$chat_id',true);
            req.send();
        }
        setInterval(function () {
            ajax();
        }, 1000)
    </script>
");
$page->renderFooter(false);
?>
