<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 7/31/18
 * Time: 1:16 PM
 */

require '../Api/Stripe/init.php';



class Web_Payment
{
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
        //select specific request based off of the id
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
        //update status from pending approval to in progress based off the id
    }

}