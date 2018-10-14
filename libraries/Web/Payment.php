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

    public $freelancer_count;


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
        $this->freelancer_count = $row['task_freelancer_count'];
        return $this;
    }

    /**
     * @param int $id
     * @param string $url
     * @return $this
     */
    public function checkOut(int $id, string $url)
    {
        $credit = $this->getUserCredit($this->getCustomerId($id));
        if($credit >= $this->price){
            $price = 0;
            $label = "data-panel-label=\"Free\"";
        } elseif ($credit < $this->price){
            $price = $this->price - $credit;
        }
        $url = str_replace("&","%26", $url);
        $url = str_replace("'", "%27", $url);
            echo "
                <form action='process_payment.php?id=$id&url=$url' method='post'>
                    <script
                        src='https://checkout.stripe.com/checkout.js' class='stripe-button'
                        data-key= " . Data_Constants::STRIPE_PUBLIC . "
                        data-amount= '$price' 
                        data-name='FAVR Inc.'
                        $label
                        data-description='$this->description'
                        data-image='https://askfavr.com/favr-pwa/assets/brand/favicon.ico'
                        data-locale='auto'>
                    </script>
                </form>";
        return $this;
    }

    private function getUserCredit($user_id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE id=$user_id");
        $user = $sth->fetch(PDO::FETCH_ASSOC);
        if(!is_null($user['favr_credit']) && ($user['favr_credit'] > 0)){
            return $user['favr_credit']*100;
        }else{
            return 0;
        }
    }

    private function getCustomerId($request_id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id='$request_id'");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row['customer_id'];
    }

    private function processUpdateCredit($customer_id, $credit)
    {
        $credit = $credit/100;
        $this->db->query("UPDATE users SET favr_credit=$credit WHERE id=$customer_id");
    }


    public function charge(string $token, int $id, string $callback_url)
    {
        $credit = $this->getUserCredit($this->getCustomerId($id));
        if($credit >= $this->price){
            $credit = $credit - $this->price;
            $this->processUpdateCredit($this->getCustomerId($id), $credit);
            $this->addStripeToken("favr_credit", $id);
            $this->update($id, $callback_url);
        } elseif ($credit < $this->price){
            $price = $this->price - $credit;
            $this->processUpdateCredit($this->getCustomerId($id), 0);
            \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
            $charge = \Stripe\Charge::create(array(
                "amount" => $price,
                "currency" => "usd",
                "description" => $this->description,
                "source" => $token,
            ));
            $chargeToken = json_decode(json_encode($charge), true);
            $this->addStripeToken($chargeToken['id'], $id);
            $this->update($id, $callback_url);
        }
        return $this;
    }

    /**
     * @param int $id
     * @param string $callback_url
     * @return $this
     */
    private function update(int $id, string $callback_url)
    {
        $success = $this->db->query("UPDATE marketplace_favr_requests SET task_status='In Progress' WHERE id=$id");
        if($success)
        {
            header("location: $callback_url");
        }else{
            echo " Request Failure \n";
        }
        return $this;
    }

    public function addStripeToken(string $token , int $id)
    {
        $this->db->query("UPDATE marketplace_favr_requests SET task_stripe_token='$token' WHERE id=$id");
    }

    /**
     * @return $this
     */
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