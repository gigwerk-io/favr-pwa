<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/13/2018
 * Time: 7:16 AM
 *
 * @author Solomon Antoine
 */
//date_default_timezone_set("America/Chicago");
class Web_Chat
{

    /**
     * @var PDO
     */
    private $db;

    /**
     * Data source name
     * @var string
     */
    private $dsn = Data_Constants::DB_DSN;

    /**
     * root path
     *
     * @var string
     */
    private $root_path;

    /**
     * Backend username
     * @var string
     */
    private $username = Data_Constants::DB_USERNAME;

    /**
     * Backend password
     * @var string
     */
    private $password = Data_Constants::DB_PASSWORD;

    /**
     * @var int
     */
    public $id;

    /**
     * Web_Chat constructor.
     */
    function __construct() {
        $this->db = $this->connect();
        $this->id = $_SESSION['user_info']['id'];
        $this->root_path = Data_Constants::ROOT_PATH;
    }

    private function connect()

    {
        //Set up PDO connection
        try {
            $db = new PDO($this->dsn, $this->username, $this->password);
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            return $db;
        } catch (PDOException $e) {
            echo "Error: Unable to load this page. Please contact arama006@umn.edu for assistance.";
            echo "<br/>Error: " . $e;
        }
    }

    /**
     * Chat Listing View
     *
     * @param $name
     * @param $lastMessage
     * @param $profileImage
     * @param $time
     */
    private function renderChatListing($chatRoomId, $name, $lastMessage, $profileImage, $time)
    {
        echo "<a href='room.php?id=$chatRoomId' style='color: black;'><div class=\"profil\">
            <div class=\"pfoto\">
                <img src=\"$profileImage\">
            </div>

            <div class=\"mesaj\">
                <b><span>$name</span> <span class=\"right\">$time</span>
                    <br>
                    <span>$lastMessage</span></b>
            </div>
            <div class=\"temizle\"></div>
        </div></a>";
    }

    /**
     * The Chat Listing Controller.
     */
    public function processChatListing()
    {
        $rooms = $this->getChatListFromDB();
        foreach ($rooms as $room){
            $message = $this->getLastMessage($room['id']);
            $this->renderChatListing(
                $room['id'],
                $this->getUser($this->getRecipient($room['id'], $this->id)),
                $message['message'],
                $this->getProfileImage($this->getRecipient($room['id'], $this->id)),
                $message['date']
            );
        }
    }

    /**
     * Chat Listing Model
     * @return bool|PDOStatement
     */
    private function getChatListFromDB()
    {
        $select_chat_rooms = "SELECT * FROM marketplace_favr_chat_rooms WHERE customer_id=$this->id or freelancer_id=$this->id";
        return $this->db->query($select_chat_rooms);
    }

    /**
     * Messages View.
     * @param $senderId
     * @param $message
     * @param $time
     */
    private function renderChatMessages($senderId, $message, $time)
    {
        if($this->id == $senderId){
            $this->renderOutgoingMessage($message, $time);
        }else{
            $name = $this->getUser($senderId);
            $this->renderIncomingMessage($message, $time, $name);
        }
    }

    /**
     * View for Sent Message.
     * @param $message
     * @param $time
     */
    private function renderOutgoingMessage($message, $time)
    {
        echo "<div class=\"balon1 p-2 m-0 position-relative\" data-is=\"You - $time\">
                <a class=\"float-right\"> $message </a>
              </div>";
    }

    /**
     * View for Received Message.
     * @param $message
     * @param $time
     * @param $name
     */
    private function renderIncomingMessage($message, $time, $name)
    {
        echo "<div class=\"balon2 p-2 m-0 position-relative\" data-is=\"$name - $time\">
                <a class=\"float-left sohbet2\"> $message </a>
             </div>";
    }

    /**
     * Chat Header Controller.
     * @param $chat_id
     */
    public function processChatHeader($chat_id)
    {
        $recipientId = $this->getRecipient($chat_id, $this->id);
        $user = $this->getUser($recipientId);
        $profileImage = $this->getProfileImage($recipientId);
        $this->renderChatHeader($user, $profileImage);
    }

    /**
     * Chat Header View
     * @param $name
     * @param $profileImage
     */
    private function renderChatHeader($name, $profileImage)
    {
        echo "<div class=\"card-header p-1 bg-light border border-top-0 border-left-0 border-right-0\" style=\"color: rgba(96, 125, 139,1.0);\">
                    <div class=\"dropdown show\">
                        <a href='chat.php' class=\"btn btn-sm float-left text-secondary\" role=\"button\"><h5><i class=\"fa fa-arrow-left\" title=\"Ayarlar!\" aria-hidden=\"true\"></i>&nbsp; </h5></a>
                    </div>
                    <div class=\"container text-center\">
                    <img class=\"rounded text-center\" style=\"width: 50px; height: 50px;\" src=\"$profileImage\" />

                    <h6 class=\"text-center\" style=\"margin: 0px; margin-left: 10px;\"> $name <i class=\"fa fa-check text-primary\" title=\"Onaylanmış Hesap!\" aria-hidden=\"true\"></i> </br></h6>
                </div>

                </div>";
    }

    /**
     * Opening Div For Messages (View)
     */
    private function renderMessageOpeningDiv()
    {
        echo "<div class=\"card bg-sohbet border-0 m-0 p-0\" >
                    <div id=\"sohbet\" class=\"card border-0 m-0 p-0 position-relative bg-transparent\" >";
    }

    /**
     * Closing Div For Messages (View)
     */
    private function renderMessageClosingDiv()
    {
        echo "</div> </div>";
    }

    /**
     * Messages Controller
     * @param $chat_id
     */
    public function processChatMessages($chat_id)
    {
        $messages = $this->getChatMessagesFromDB($chat_id);
        $this->renderMessageOpeningDiv();
        foreach ($messages as $message){
            $this->renderChatMessages($message['sender_id'], $message['message'], $this->timeAgo($message['created_at']));
        }
        $this->renderMessageClosingDiv();
    }

    /**
     * Messages Model.
     * @param $chat_id
     * @return bool|PDOStatement
     */
    private function getChatMessagesFromDB($chat_id)
    {
        $get_all_messages = "SELECT * FROM marketplace_favr_messages WHERE chat_room_id=$chat_id";
        return $this->db->query($get_all_messages);
    }


    /**
     * Message Controller
     * @param int $id
     * @return $this
     */
    public function getAllMessages(int $id)
    {
        $query = $this->db->query("SELECT * FROM marketplace_favr_messages WHERE chat_room_id=$id");
        $img_path = $this->getProfileImage($this->getRecipient($id, $this->id));
        while($messages = $query->fetch(PDO::FETCH_ASSOC)){
            $this->displayChat(
                $messages['sender_id'],
                $messages['message'],
                $this->timeAgo($messages['created_at']),
                $img_path
            );
        }
        return $this;
    }

    /**
     * Send Message Controller
     * @param int $chat_room
     * @param int $sender_id
     * @param string $message
     */
    public function processSendMessage(int $chat_room, int $sender_id, string $message)
    {
        $recipient_id = $this->getRecipient($chat_room, $sender_id);
        $send_message_query = $this->db->query("INSERT INTO marketplace_favr_messages
                                    (chat_room_id,
                                     sender_id, 
                                     recipient_id, 
                                     message)
                                 VALUES 
                                    ($chat_room,
                                     $sender_id,
                                     $recipient_id,
                                     '$message'
                                     )
            ");
        if($send_message_query){
            $this->renderMessagePing();
        }
    }

    /**
     * Getter for Recipient
     * @param int $chat_room
     * @param int $user_id
     * @return mixed
     */
    private function getRecipient(int $chat_room, int $user_id)
    {
        $query = $this->db->query("SELECT * FROM marketplace_favr_chat_rooms WHERE id=$chat_room");
        $result = $query->fetch(PDO::FETCH_ASSOC);
        if($user_id == $result['freelancer_id']){
            return $result['customer_id'];
        }else{
            return $result['freelancer_id'];
        }
    }
    /**
     * Getter for User
     * @param int $id
     * @return string
     */
    private function getUser(int $id)
    {
        $query = $this->db->query("SELECT * FROM users WHERE id=$id");
        $user = $query->fetch(PDO::FETCH_ASSOC);
        return $user['first_name'] . ' ' . $user['last_name'];
    }

    /**
     * Getter for latest message
     * @param int $chat_id
     * @return array
     */
    private function getLastMessage(int $chat_id)
    {
        $query = $this->db->query("SELECT * FROM marketplace_favr_messages WHERE chat_room_id=$chat_id ORDER BY created_at DESC");
        $details = $query->fetch(PDO::FETCH_ASSOC);
        return [
            'message' => $details['message'],
            'date' => $this->timeAgo($details['created_at'])
        ];
    }

    /**
     * Getter for profile image
     * @param int $user_id
     * @return string
     */
    private function getProfileImage(int $user_id)
    {

        $query = $this->db->query("SELECT * FROM users WHERE id=$user_id");
        $user = $query->fetch(PDO::FETCH_ASSOC);
        $profile_img = unserialize($user['profile_picture_path']);
        $profile_img_name = "";
        $profile_img_type = "";
        if (!empty($profile_img)) {
            $profile_img_name = $profile_img['name'];
            $profile_img_type = $profile_img['type'];
        }
        return "$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true";
    }

    /**
     * View/Sound for message sending.
     */
    private function renderMessagePing()
    {
        echo "<embed loop='false' src='chat.wav' hidden='true' autoplay='true'/>";
    }

    /**
     * @param $time_ago
     * @return string
     */
    private function timeAgo($time_ago)
    {
        $time_ago = strtotime($time_ago);
        $cur_time = time();
        $time_elapsed = $cur_time - $time_ago;
        $seconds = $time_elapsed;
        $minutes = round($time_elapsed / 60);
        $hours = round($time_elapsed / 3600);
        $days = round($time_elapsed / 86400);
        $weeks = round($time_elapsed / 604800);
        $months = round($time_elapsed / 2600640);
        $years = round($time_elapsed / 31207680);
        // Seconds
        if ($seconds <= 60) {
            return "just now";
        } //Minutes
        else {
            if ($minutes <= 60) {
                if ($minutes == 1) {
                    return "one minute ago";
                } else {
                    return "$minutes minutes ago";
                }
            } //Hours
            else {
                if ($hours <= 24) {
                    if ($hours == 1) {
                        return "an hour ago";
                    } else {
                        return "$hours hrs ago";
                    }
                } //Days
                else {
                    if ($days <= 7) {
                        if ($days == 1) {
                            return "yesterday";
                        } else {
                            return "$days days ago";
                        }
                    } //Weeks
                    else {
                        if ($weeks <= 4.3) {
                            if ($weeks == 1) {
                                return "a week ago";
                            } else {
                                return "$weeks weeks ago";
                            }
                        } //Months
                        else {
                            if ($months <= 12) {
                                if ($months == 1) {
                                    return "a month ago";
                                } else {
                                    return "$months months ago";
                                }
                            } //Years
                            else {
                                if ($years == 1) {
                                    return "one year ago";
                                } else {
                                    return "$years years ago";
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}