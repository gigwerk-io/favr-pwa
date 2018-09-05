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
$page->renderHeader();

if(isset($_POST['message'])){
    $chat->sendMessage($_GET['chat_room'], $_SESSION['user_info']['id'], $_POST['message']);
    $_POST = array();
}
?>
    <!--    <meta http-equiv="refresh" content="30"/>-->

    <div class="messaging m-0 p-0">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h4>Recent</h4>
                    </div>
                    <div class="srch_bar">
                        <div class="stylish-input-group">
                            <input type="text" class="search-bar" placeholder="Search">
                            <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span></div>
                    </div>
                </div>
                <?php
                echo "<div class=\"inbox_chat\">";
                $chat->getChatList();
                echo "</div>";
                echo "<div id=\"status\">
                </div>";
                ?>
            </div>
            <div class="web-messaging-contact mesgs p-0">
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
                    echo " <div class=\"type_msg m-0\">
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
<!--                <div class="type_msg m-0">-->
<!--                    <div class="input_msg_write">-->
<!--                        <form action="index.php" method="post" id="Message">-->
<!--                            <textarea onkeyup="resetTimer = true" style="border-radius: 0;" type="text" class="form-control m-0 pt-2" name="message" placeholder="Type a message..."></textarea>-->
<!--                        </form>-->
<!--                        <button class="msg_send_btn text-center p-1" form="Message" type="submit"><i class="material-icons"-->
<!--                                                                                                     aria-hidden="true">send</i></button>-->
<!--                    </div>-->
<!--                </div>-->
            </div>
        </div>
    </div>
<?php

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