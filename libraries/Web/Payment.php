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
use Stripe\Charge;
use Stripe\Stripe;


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
        Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
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
     * Favr Request Model.
     * @param $id
     * @return mixed
     */
    private function getFavrRequest($id)
    {
        $result = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Freelancer Id Model.
     * @param $id
     * @return mixed
     */
    private function getFreelancers($id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_freelancers WHERE request_id=$id AND approved=1");
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /** User Model
     * @param $user_id
     * @return mixed
     */
    private function getUser($user_id)
    {
        $result = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$user_id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Stripe Checkout Controller
     * @param int $id
     * @param string $url
     */
    public function processCheckOut(int $id, string $url)
    {
        $credit = $this->getUserCredit($this->getCustomerId($id));
        $marketplace = $this->getFavrRequest($id);
        $description = $marketplace['task_description'];
        if($credit >= $marketplace['task_price']){
            $price = 0;
            $label = "data-panel-label=\"Free\"";
        } elseif ($credit < $marketplace['task_price']){
            $price = $marketplace['task_price'] - $credit;
            $label = null;
        }
        $url = str_replace("&","%26", $url);
        $url = str_replace("'", "%27", $url);
        echo $this->renderCheckoutForm($id, $url, $price*100, $label, $description);
    }

    /** Stripe Payment Form View.
     * @param $id
     * @param $url
     * @param $price
     * @param $label
     * @param $description
     * @return string
     */
    private function renderCheckoutForm($id, $url, $price, $label, $description)
    {
        return "
            <form action='process_payment.php?id=$id&url=$url' method='post'>
                <script
                    src='https://checkout.stripe.com/checkout.js' class='stripe-button'
                    data-key= " . Data_Constants::STRIPE_PUBLIC . "
                    data-amount= '$price' 
                    data-name='FAVR Inc.'
                    $label
                    data-description='$description'
                    data-image='https://askfavr.com/favr-pwa/assets/brand/favicon.ico'
                    data-locale='auto'>
                </script>
            </form>";
    }

    /**
     * User Credit Model.
     * @param $user_id
     * @return float|int
     */
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

    /**
     * Update User Credit.
     * @param $customer_id
     * @param $credit
     */
    private function processUpdateCredit($customer_id, $credit)
    {
        $credit = $credit/100;
        $this->db->query("UPDATE users SET favr_credit=$credit WHERE id=$customer_id");
    }

    /**
     * @param string $token
     * @param int $id
     * @param string $callback_url
     */
    public function processCharge(string $token, int $id, string $callback_url)
    {
        $marketplace = $this->getFavrRequest($id);
        $price = $marketplace['task_price']*100;
        $credit = $this->getUserCredit($marketplace['customer_id']);
        if($credit >= $price){
            $credit = $credit - $price;
            $this->processUpdateCredit($marketplace['customer_id'], $credit);
            $this->processStripeTokenDB("favr_credit", $id);
            $this->redirect($callback_url);
        } elseif ($credit < $price){
            $price = $price - $credit;
            $this->processUpdateCredit($this->getCustomerId($id), 0);
            $charge = Charge::create(array(
                "amount" => $price,
                "currency" => "usd",
                "description" => $this->description,
                "source" => $token,
            ));
            $chargeToken = json_decode(json_encode($charge), true);
            $this->processStripeTokenDB($chargeToken['id'], $id);
            $this->createChat($id);
            $this->redirect($callback_url);
        }
    }

    /**
     * Redirect Back To Accepted Page.
     * @param string $callback_url
     */
    private function redirect(string $callback_url)
    {
        header("location: $callback_url");
    }

    /**
     * @param string $token
     * @param int $id
     */
    private function processStripeTokenDB(string $token , int $id)
    {
        $this->db->query("UPDATE marketplace_favr_requests SET task_stripe_token='$token' WHERE id=$id");
    }


    /**
     * Create Chat Room Controller.
     * @param $id
     */
    private function createChat($id)
    {
        $marketplace = $this->getFavrRequest($id);
        $freelancers = $this->getFreelancers($id);
        $chat = new Web_Chat();
        foreach ($freelancers as $freelancer) {
            $chat->processNewChatRoom($marketplace['customer_id'], $freelancer);
        }
    }
}