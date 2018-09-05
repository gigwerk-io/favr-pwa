<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/13/2018
 * Time: 7:16 AM
 *
 * @author Solomon Antoine
 */
date_default_timezone_set("America/Chicago");
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
     * Chat List View
     * @param int $id
     * @param string $name
     * @param string $date
     * @param string $message
     */
    private function displayList(int $id, string $name, string $date, string $message)
    {

        echo "
                    <div class=\"chat_list\">
                        <a href='?chat_room=$id'>
                            <div class=\"chat_people\">
                                <div class=\"chat_img\"><img src=\"https://ptetutorials.com/images/user-profile.png\"
                                                           alt=\"sunil\"></div>
                                <div class=\"chat_ib\">
                                    <h5>$name<span class=\"chat_date\">$date</span></h5>
                                    <p>$message</p>
                                </div>
                            </div>
                         </a>
                     </div>
                ";
    }

    /**
     * List Controller
     * @return $this
     */
    public function getChatList()
    {
        $query = $this->db->query("SELECT * FROM marketplace_favr_chat_rooms WHERE customer_id=$this->id or freelancer_id=$this->id");
        while($chat_room = $query->fetch(PDO::FETCH_ASSOC)) {
            $data = $this->getLastMessage($chat_room['id']);
            if ($this->id == $chat_room['customer_id']) {
                $this->displayList(
                    $chat_room['id'],
                    $this->getUser(
                        $chat_room['freelancer_id']
                    ),
                    $data['date'],
                    $data['message']
                );
            } else {
                $this->displayList(
                    $chat_room['id'],
                    $this->getUser(
                        $chat_room['customer_id']
                    ),
                    $data['date'],
                    $data['message']
                );
            }
        }
        return $this;
    }

    /**
     * Message View
     * @param int $sender_id
     * @param string $message
     * @param string $date
     */
    private function displayChat(int $sender_id,string $message, string $date)
    {
        if($this->id == $sender_id){
            $this->outgoingMessage($message, $date);
        }else{
            $this->incomingMessage($message, $date);
        }
    }

    /**
     * Message Controller
     * @param int $id
     * @return $this
     */
    public function getAllMessages(int $id)
    {
        $query = $this->db->query("SELECT * FROM marketplace_favr_messages WHERE chat_room_id=$id");
        while($messages = $query->fetch(PDO::FETCH_ASSOC)){
            $this->displayChat(
              $messages['sender_id'],
              $messages['message'],
              $this->timeAgo($messages['created_at'])
            );
        }
        return $this;
    }

    private function verifyUser(int $chat_room)
    {

    }

    public function sendMessage(int $chat_room, int $sender_id, string $message)
    {
        $recipient_id = $this->getRecipient($chat_room, $sender_id);
        $insert_sign_up_query = $this->db->query("INSERT INTO marketplace_favr_messages
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
        if($insert_sign_up_query){
            $this->messagePing();
        }
    }

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


    private function outgoingMessage(string $message, string $date)
    {
        echo "<div class=\"outgoing_msg\" id='incoming'>
                    <div class=\"sent_msg\">
                        <p>$message</p>
                        <span class=\"time_date\"> $date</span>
                    </div>
              </div>";
    }

    private function incomingMessage(string $message, string $date)
    {
        echo "<div class=\"incoming_msg\">
                <div class=\"incoming_msg_img\"><img src=\"https://ptetutorials.com/images/user-profile.png\"
                                               alt=\"sunil\"></div>
                <div class=\"received_msg\">
                    <div class=\"received_withd_msg\">
                        <p>$message</p>
                        <span class=\"time_date\"> $date</span>
                    </div>
                </div>
              </div>";
    }

    public function messagePing()
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