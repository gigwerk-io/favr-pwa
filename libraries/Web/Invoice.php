<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/1/18
 * Time: 10:30 AM
 */
require '../Api/Sendgrid/vendor/autoload.php';

class Web_Invoice
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
     * @var int
     */
    public $request_id;

    /**
     * @var string
     */
    public $date;

    /**
     * @var string
     */
    public $task;

    /**
     * @var double
     */
    public $price;

    /**
     * @var string
     */
    public $complete;

    /**
     * @var int
     */
    public $customer_id;
    /**
     * @var string
     */
    public $customer;

    /**
     * @var string
     */
    public $email;

    /**
     * @var int
     */
    public $freelancer_id;
    /**
     * @var string
     */
    public $freelancer;



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
    public function selectRequest(int $id)
    {
        $result = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $this->date = $row['task_date'];
        $this->price = $row['task_price'];
        $this->customer_id = $row['customer_id'];
        $this->freelancer_id = $row['freelancer_id'];

        $this->task = $row['task_description'];
        $this->complete = $row['task_completion_time'];
    }

    /**
     * @param int $id
     */
    public function selectCustomer(int $id)
    {
        $result = $this->db->query("SELECT * FROM users WHERE id=$id");
        $row = $result->fetch(PDO::FETCH_ASSOC);

        $this->customer = $row['first_name'] . " " . $row['last_name'];
        $this->email = $row['email'];
    }

    /**
     * @param int $id
     */
    public function selectFreelancer(int $id)
    {
        $result = $this->db->query("SELECT * FROM users WHERE id=$id");
        $row = $result->fetch(PDO::FETCH_ASSOC);

        $this->freelancer = $row['first_name'] . " " . $row['last_name'];
    }

    /**
     * @param string $name
     * @param string $email
     * @param string $message
     */
    public function sendEmail(string $name, string $email, string $message)
    {
        $from = new SendGrid\Email("FAVR", "contact@askfavr.com");
        $subject = "Service Invoice";
        $to = new SendGrid\Email($name, $email);
        $content = new SendGrid\Content("text/html",  $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
    }

    //selectRequest($this->request_id)->selectCustomer($this->customer_id)->selectFreelancer($this->freelancer_id)->sendEmail($this->customer, $this->email, $this->message)
}