<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 7/31/18
 * Time: 1:16 PM
 *
 * @author solomonantoine
 */

require '../Api/Stripe/init.php';



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
    public $username = Data_Constants::DB_USERNAME;

    /**
     * Backend password
     * @var string
     */
    public $password = Data_Constants::DB_PASSWORD;

    /**
     * @var float
     */
    public $price;

    function __construct() {
        $this->db = $this->connect();
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
     */
    public function select(int $id)
    {
        $result = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $this->price = $row['task_price'];
    }

    /**
     * @param string $token
     * @param float $price
     */
    public function charge(string $token,float $price)
    {
        \Stripe\Stripe::setApiKey(\Data_Constants::STRIPE_SECRET);
        \Stripe\Charge::create(array(
            "amount" => $price,
            "currency" => "usd",
            "description" => "Fulfilled FAVR",
            "source" => $token,
        ));
    }

    /**
     * @param int $id
     */
    public function update(int $id)
    {
        $this->db->query("UPDATE marketplace_favr_requests SET task_status='In Progress' WHERE id=$id");
    }

    // select($id)->charge($_POST['token'], $this->price)->update($id);
}