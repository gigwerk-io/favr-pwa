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
//$list = new Web_Chat();

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

//header("Location: ../../assets/css/chat.css");

$page->setTitle("Chat");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/chat.css'>");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/test-chat.css'>");
$page->renderHeader();
?>
    <div id="container" >
        <div id="chat_box">
            <div id="chat"></div>
        </div>
        <form method="post" action="index.php">
            <!--Session Name-->
            <textarea name="message" placeholder="enter message"></textarea>
            <input type="submit" name="submit" value="Send">
        </form>

        <?php
        if(isset($_POST['submit'])){
            if(!empty($_POST['message'])){
                $message = $_POST['message'];
                $sender_id = $_SESSION['user_info']['id'];
                $insert = $page->db->query("INSERT INTO 
                                                      marketplace_favr_messages(
                                                        chat_room_id,
                                                        sender_id,
                                                        recipient_id,
                                                        message
                                                      ) 
                                                      values(
                                                        1,
                                                        $sender_id,
                                                        47,
                                                        '$message'
                                                      )");
                if($insert){
                    echo "<embed loop='false' src='$page->root_path/assets/chat.wav' hidden='true' autoplay='true'/>";
                }
            }else{
                echo "<script> alert('Message Body Cannot Be Empty!');</script>";
            }
        }
        ?>
    </div>
<?php
$page->renderFooter(false);
?>