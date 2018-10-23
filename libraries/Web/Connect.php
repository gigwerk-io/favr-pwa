<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/2/2018
 * Time: 8:49 PM
 * @author Solomon Antoine
 */


class Web_Connect{
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
    public $username = Data_Constants::DB_USERNAME;

    /**
     * Backend password
     * @var string
     */
    public $password = Data_Constants::DB_PASSWORD;

    /**
     * @var int
     */
    public $id;


    /**
     * @var string
     */
    public $payment_id;

    function __construct() {
        $this->db = $this->connect();
        if(!empty($_SESSION['user_info'])){
            $this->id = $_SESSION['user_info']['id'];
            $sth = $this->db->query("SELECT payment_id FROM users WHERE id=$this->id");
            $row = $sth->fetch(PDO::FETCH_ASSOC);
            $this->payment_id = $row['payment_id'];
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
     * @param string $code
     * @return $this
     */
    function savePaymentAccount(string $code)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://connect.stripe.com/oauth/token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "client_secret=" .\Data_Constants::STRIPE_SECRET . "&code=$code&grant_type=authorization_code");
        curl_setopt($ch, CURLOPT_POST, 1);

        $headers = array();
        $headers[] = "Content-Type: application/x-www-form-urlencoded";
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            echo 'Error:' . curl_error($ch);
        }


        $obj = json_decode($result,true);
        $this->payment_id = $obj['stripe_user_id'];
        $sth = $this->db->prepare("UPDATE users SET payment_id='$this->payment_id' WHERE id=$this->id");
        if ($sth->execute()){
            $this->stripeLogin();
        } else {
            echo "<script> alert('Failure!'); </script>";
        }
        curl_close ($ch);
        return $this;
    }

    public function stripeLogin()
    {
        \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
        $account = \Stripe\Account::retrieve($this->payment_id);
        $link = $account->login_links->create();
        $url = $link->url;
        header("location: $url");
    }

    /**
     * @param string $account_id
     * @return mixed
     */
    public function viewBalance(string $account_id)
    {
        \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
        $balance = \Stripe\Balance::retrieve(
            array("stripe_account" => $account_id)
        );
        $arr = json_decode(json_encode($balance), true);
        return $arr['available'][0]['amount'];
    }

    /**
     * @param $id
     */
    public function payoutFundsToFreelancer($id)
    {
        \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
        $freelancers = $this->getFreelancers($id);
        $price = $this->getPrice($id);
        foreach ($freelancers as $freelancer){
            \Stripe\Transfer::create(array(
                "amount" => $price,
                "currency" => "usd",
                "source_transaction" => null,
                "destination" => $this->getStripeAccount($freelancer['user_id']),
            ));
        }
    }

    /**
     * @param int $id
     * @return mixed
     */
    private function getStripeToken(int $id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $stripeToken = $row['task_stripe_token'];

        return $stripeToken;
    }

    /**
     * @param int $user_id
     * @return mixed
     */
    private function getStripeAccount(int $user_id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE id=$user_id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $stripeAccount =  $row['payment_id'];
        return $stripeAccount;
    }

    /**
     * @param int $id
     * @return mixed
     */
    private function getFreelancers(int $id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_freelancers WHERE request_id=$id AND approved=1");
        return $sth->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * @param int $id
     * @return float|int
     */
    private function getPrice(int $id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        $price = $row['task_price'] * 100 * 0.8;

        return $price;
    }

}
