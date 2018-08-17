<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 7/31/18
 * Time: 1:16 PM
 *
 * @author solomonantoine
 */

//require '../Api/Stripe/init.php';



class Web_Payment
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
     * @var float
     */
    public $price;

    /**
     * @var string
     */
    public $description;

    /**
     * @var string
     */
    public $status;

    /**
     * @var int
     */
    public $customer_id;

    /**
     * @var int
     */
    public $freelancer_id;

    function __construct() {
        $this->db = $this->connect();
        if(isset($_GET['id'])){
            $this->select($_GET['id']);
        }
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
     * @param int $id
     * @return $this
     */
    public function select(int $id)
    {
//        $sth = $this->db->prepare("SELECT * FROM marketplace_favr_requests");
//        $sth->execute();
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $this->status = $row['task_status'];
        $this->price = $row['task_price']*100;
        $this->description = $row['task_description'];
        $this->customer_id = $row['customer_id'];
        $this->freelancer_id = $row['freelancer_id'];
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function checkOut(int $id)
    {
//        if($this->status == "Pending Approval")
//        {
            echo "
                <form action='process_payment?id=$id' method='post'>
                    <script
                        src='https://checkout.stripe.com/checkout.js' class='stripe-button'
                        data-key= " . Data_Constants::STRIPE_PUBLIC . "
                        data-amount= '$this->price' 
                        data-name='FAVR Inc.'
                        data-description='$this->description'
                        data-image='https://askfavr.com/favr-pwa/assets/brand/favicon.ico'
                        data-locale='auto'>
                    </script>
                </form>";

//        } else{
//            //header("location: http://localhost:1234/favr-pwa");
//        }
        return $this;
    }

    /**
     * @param string $token
     * @return $this
     */
    public function charge(string $token)
    {
        \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
        \Stripe\Charge::create($charge = array(
            "amount" => $this->price,
            "currency" => "usd",
            "description" => $this->description,
            "source" => $token,
        ));
        //print_r($charge);
        return $this;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function update(int $id)
    {
        $success = $this->db->query("UPDATE marketplace_favr_requests SET task_status='In Progress' WHERE id=$id");
        if($success)
        {
//            $text = new Web_Notification();
//            $sth = $this->db->query("SELECT * FROM users WHERE id=$this->freelancer_id");
//            $row = $sth->fetch(PDO::FETCH_ASSOC);
//            $text->sendNotification(1, "Here");
        }else{
            echo " Request Failure \n";
        }
        return $this;
    }

    public function createChat()
    {
        $message_file = "message_" . time() . ".txt";
        fopen("../../storage/$message_file", "x");
        $success = $this->db->query("INSERT INTO marketplace_favr_chat (message_file, customer_id, freelancer_id_1) 
                                    VALUES ('$message_file', $this->customer_id, $this->freelancer_id)");
        if($success)
        {
            echo "<script> 
                    alert('Chat Created.');
                    window.location.href = 'https://askfavr.com/favr-pwa/home/chat/?file=$message_file&customer=$this->customer_id&freelancer=$this->freelancer_id';
                </script> \n";
        }else{
            echo "Chat Unsuccessful \n";
        }
        return $this;
    }




    // select($id)->charge($_POST['token'], $this->price)->update($id);
}