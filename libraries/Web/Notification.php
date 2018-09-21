<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/1/18
 * Time: 1:16 PM
 */

use Twilio\Rest\Client;
class Web_Notification
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
     * Web_Notification constructor.
     */
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
     * @param string $phone
     * @param string $message
     * @return array
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function sendNotification(string $phone, string $message)
    {
        $phone = "+1" . str_replace("-", "", $phone);
        $client = new Client(Data_Constants::TWILIO_SID, Data_Constants::TWILIO_API);
        //send message to customer
        $client->messages->create(
            $phone,
            $arr = array(
                //trial number
                'from' => '+15074734314',
                'body' => $message,
            )
        );
        return $arr;
    }

    public function emailAllFreelancers($request_id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE email_notifications_enabled=1");
        $request = $this->processRecieveRequest($request_id);
        $message = $this->renderMessage(
            $request['task_description'],
            $request['task_date'],
            "Rochester, MN",
            $request['task_price'],
            $request['task_intensity']
        );
        while($user = $sth->fetch(PDO::FETCH_ASSOC)){
            $this->processEmailNotification($user['first_name'] . " " . $user['last_name'], $user['email'], $message);
        }

    }

    public function smsAllFreelancers($request_id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE sms_notifications_enabled=1");
        $request = $this->processRecieveRequest($request_id);
        $date = date("m/d/Y",strtotime($request['task_date']));
        while($user = $sth->fetch(PDO::FETCH_ASSOC)){
            $message = "Hey " . $user['first_name'] . ", a new FAVR request has been made for $" . $request['task_price'] . ". The job is expected to be complete by $date. Be the first to finish it within the app!";
            $this->sendNotification($user['phone'], $message);
        }
    }

    public function processCustomerSmsNotification($request_id)
    {
        $user_id = $this->processRecieveRequest($request_id);
        $customer = $this->processGetCustomerInfo($user_id['customer_id']);
        $this->sendNotification($customer['phone'], "Hey " . $customer['first_name'] . ", a freelancer has responded to your FAVR request. Please accept or deny them within the app!");
    }

    private function processEmailNotification($name, $email, $message)
    {

        $from = new SendGrid\Email("FAVR", "contact@askfavr.com");
        $subject = "New FAVR Alert";
        $to = new SendGrid\Email($name, $email);
        $content = new SendGrid\Content("text/html", $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
    }

    private function renderMessage($description, $date, $location, $price, $difficulty)
    {
        $date = date("m/d/Y",strtotime($date));
        return "<html>
                <head>
                  <title>FAVR Alert</title>
                  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
                </head>
                
                <body style=\"background: #F8F9FB; padding-top: 36px; padding-bottom: 52px;\">
                  
                  <img src=\"https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png\" alt=\"\" style=\"margin: auto; margin-bottom: 28px; display: block; height: 36px;\">
                
                  <div class=\"container\" style=\"background: #FFFFFF; box-shadow: 0 2px 10px 0 rgba(43,44,46,0.15); max-width: 520px; margin: auto; font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; box-sizing: border-box;\">
                    <div style=\"padding: 40px 10%;\">
                    
                      <img src=\"https://www.k-rem.com/wp-content/uploads/sites/7017/2018/05/lawnmower.png\" alt=\"\" style=\"margin: auto; margin-bottom: 16px; display: block; width: 80px;\">
                      <h1 style=\"text-align: center; font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 500; font-size: 24px; color: #314853; line-height: 24px; margin: 8px 0;\">FAVR Alert</h1>
                    <p style=\"text-align: center; font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 400; font-size: 13px; color: #333333; line-height: 18px; margin-bottom: 24px;\">Complete Before: $date</p>
                    
                    <h2 style=\"font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 600; font-size: 18px; color: #314853; line-height: 24px; margin-bottom: 16px; margin-top: 24px;\">Description</h2>
                    
                    <div style=\"margin-bottom: 16px;\">
                    <h3 style=\"font-weight: 400; font-size: 16px; color: #555555; line-height: 18px; margin-bottom: 8px;\">$description</h3>

                    </div>
                    
                 
                    
                    
                    <h2 style=\"font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 600; font-size: 18px; color: #314853; line-height: 24px; margin-bottom: 16px; margin-top: 24px;\">Location</h2>
                    
                    <div style=\"margin-bottom: 16px;\">
                    <h3 style=\"font-weight: 400; font-size: 16px; color: #555555; line-height: 18px; margin-bottom: 8px;\">$location</h3>
                    </div>
                    
                    
                      <h2 style=\"font-family: 'Helvetica Neue', Helvetica, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; font-weight: 600; font-size: 18px; color: #314853; line-height: 24px; margin-bottom: 16px; margin-top: 24px;\">Price</h2>
                    
                    <div style=\"margin-bottom: 16px;\">
                    <h3 style=\"font-weight: 400; font-size: 16px; color: #555555; line-height: 18px; margin-bottom: 8px;\">$$price- $difficulty</h3>
                    <br>
                    <br>
                    <small> Disabled Notifications Within Your Account Settings</small>
                    </div>   
                    </div>
                  </div>
                </body>
                </html>";
    }

    private function processRecieveRequest($id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row;
    }

    private function processGetCustomerInfo($customer_id)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE id=$customer_id");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row;
    }




}