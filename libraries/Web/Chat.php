<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/13/2018
 * Time: 7:16 AM
 *
 * @author Solomon Antoine
 */

class Web_Chat
{

    /**
     * @var PDO
     */
    public $db;

    /**
     * Data source name
     * @var string
     */
    public $dsn = Data_Constants::DB_DSN;

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
     * @var int
     */
    public $freelancer_id;

    /**
     * @var int
     */
    public $customer_id;

    /**
     * @var string
     */
    public $message;

    /**
     * Web_Chat constructor.
     */
    function __construct() {
        $this->db = $this->connect();
        $this->id = $_SESSION['user_info']['id'];
    }

    function connect()
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
     * @return $this
     */
    public function listChat()
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_chat WHERE customer_id=$this->id or freelancer_id_1=$this->id");
        while($row = $sth->fetch(PDO::FETCH_ASSOC))
        {
            $this->freelancer_id = $row['freelancer_id_1'];
            $this->customer_id = $row['customer_id'];
            if($this->id = $this->customer_id)
            {
                $name = $this->getName($this->freelancer_id);
            } else{
                $name = $this->getName($this->customer_id);
            }
            if($this->id = $row['customer_id'])
            {
                $to = $row['freelancer_id_1'];
            } else {
                $to = $row['customer_id'];
            }
            $message = $row['message_file'];
            //$date = date("Y-m-d H:i:s", $row['updated_at']);
            $date = $row['updated_at'];
            $new_date = date_format(new DateTime($date),"m/d/Y H:i:s");
            //echo "<a href='?file=$message'>" . $name . "</a>";
            echo "
                   <div class=\"inbox_chat\">
                        <div class=\"chat_list\">
                        <a href='?file=$message&to=$to'>
                            <div class=\"chat_people\">
                                <div class=\"chat_img\"><img src=\"https://ptetutorials.com/images/user-profile.png\"
                                                           alt=\"sunil\"></div>
                                <div class=\"chat_ib\">
                                    <h5>$name<span class=\"chat_date\">$new_date</span></h5>
                                </div>
                            </div>
                           </a>
                        </div>
                   </div>
                ";
        }
        return $this;
    }

    /**
     * @param string $file
     * @return $this
     */
    public function displayMessage(string $file)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_chat WHERE message_file='$file'");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
//        if($this->id = $row['customer_id'])
//        {
//            $from = $this->getName($row['customer_id']);
//            $to = $this->getName($row['freelancer_id_1']);
//        } else {
//            $to = $this->getName($row['customer_id']);
//            $from = $this->getName($row['freelancer_id_1']);
//        }
        $convo = file_get_contents("../../storage/$file");
        $split = explode("\n", $convo);
        $keys = array();
        $values = array();
        $this->message = array();
        foreach ($split as $line)
        {
            $header = explode("|", $line);
            foreach ($header as $meta)
            {
                $item = explode(":", $meta);
                $keys[] = $item[0];
                $values[] = $item[1];
            }
            $this->message = array_combine($keys, $values);
            $text = $this->message['Message'];
            $time = date('F j,g:i a', $this->message['Time']);
            if($this->id != $this->message['From']){
                echo "<div class=\"incoming_msg\">
                        <div class=\"incoming_msg_img\"><img src=\"https://ptetutorials.com/images/user-profile.png\"
                                                           alt=\"sunil\"></div>
                        <div class=\"received_msg\">
                            <div class=\"received_withd_msg\">
                                <p>$text</p>
                                <span class=\"time_date\"> $time</span></div>
                        </div>
                  </div>";
            } else {
                echo "<div class=\"outgoing_msg\">
                        <div class=\"sent_msg\">
                            <p>$text</p>
                            <span class=\"time_date\"> $time</span></div>
                    </div>";
            }

        }
        return $this;

    }

    /**
     * @param int $id
     * @return string
     */
    public function getName(int $id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE id=$id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $name = $row['first_name'] . " " . $row['last_name'];
        return $name;
    }

    public function updateMessage(array $message)
    {
        $to = $message['To'];
        $text = $message['Text'];
        $text = str_replace(array("\r\n","\r","\n"),"<br/>", $text);
        $time = $message['Time'];
        $file = fopen("../../storage/" . $message['File'], "a");
        $data = "\nFrom:$this->id|To:$to|Message:$text|Time:$time";
        fwrite($file, "$data");
        fclose($file);
        $location = $message['File'];
        echo "<script> window.location.href = 'http://192.168.64.2/favr-pwa/home/chat/?file=$location&to=$to'; </script>";
    }
}