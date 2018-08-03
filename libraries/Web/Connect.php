<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/2/2018
 * Time: 8:49 PM
 * @author Solomon Antoine
 */

require '../Api/Stripe/init.php';

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
    private $code;
    /**
     * @var string
     */
    private $payment_id;

    function __construct() {
        $this->db = $this->connect();
        $this->code = $_GET['code'];
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
     * @param string $code
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

        curl_close ($ch);
    }
    /**
     * @param int $id
     */
    public function update(int $id)
    {
        $this->db->query("UPDATE users SET payment_id='$this->payment_id' WHERE id=$id");
        //$this->savePaymentAccount($this->code)->update($this->id);
    }


}
