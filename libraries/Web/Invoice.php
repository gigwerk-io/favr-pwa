<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/1/18
 * Time: 10:30 AM
 */
//require '../Api/Sendgrid/vendor/autoload.php';

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

    /**
     * @var string
     */
    public $task_date;

    /**
     * @var string
     */
    public $freelancer_email;

    /**
     * Web_Invoice constructor.
     */
    function __construct() {
        $this->db = $this->connect();
    }

    /**
     * @return PDO
     */
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
        $this->task_date = date('F j, g:i a', strtotime($row['task_date']));

        $this->task = $row['task_description'];
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
        $this->freelancer_email = $row['email'];
    }

    /**
     * @param int $id
     * @return $this
     */
    public function sendCustomerInvoice(int $id)
    {
        $this->selectRequest($id);
        $this->selectCustomer($this->customer_id);
        $this->selectFreelancer($this->freelancer_id);

        $message = "<html>
<head>
    <meta charset=\"utf-8\"/>
    <title>Email</title>
    
    </head>

<body>
    <div class=\"invoice-box\" style=\"max-width: 800px;margin: auto;padding: 30px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);font-size: 16px;line-height: 24px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #555\">
        <table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%;line-height: inherit;text-align: left\">
            <tr class=\"top\">
                <td colspan=\"2\" style=\"padding: 5px;vertical-align: top\">
                    <table style=\"width: 100%;line-height: inherit;text-align: left\">
                        <tr>
                            <td class=\"title\" style=\"padding: 5px;vertical-align: top;padding-bottom: 20px;font-size: 45px;line-height: 45px;color: #333\">
                                <img src=\"https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png\" style=\"width:100%; max-width:300px;\"/>
                            </td>
                            
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 20px\">
                                <b>Request #: $id</b><br/>
                                <b>Date: $this->task_date</b><br/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class=\"information\">
                <td colspan=\"2\" style=\"padding: 5px;vertical-align: top\">
                    <table style=\"width: 100%;line-height: inherit;text-align: left\">
                        <tr>
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 40px\">
                                FAVR Inc.<br/>
                                14 4th St SW #203<br/>
                                Rochester, MN 55902
                            </td>
                            
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 40px\">
                                $this->customer<br/>
                                $this->email
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class=\"heading\">
                <td style=\"padding: 5px;vertical-align: top;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Task Description:
                </td>
                <td style=\"padding: 5px;vertical-align: top;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Completed By:
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Price:
                </td>
            </tr>
            
          
            
            <tr class=\"item\">
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                    $this->task
                </td>
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                    $this->freelancer
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold\">
                   Total: $$this->price
                </td>
            </tr>
            <tr class=\"total\">
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #fff\">
                   
                </td>
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #fff\">
                    
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #fff;font-weight: bold\">
                   
                </td>
            </tr>
        </table>
        <br>
        <br>
        <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
            With best regards,<br/>
            +1 (507) 440-7130, FAVR Inc. <br/>
        </p>
        <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
            Support: <a class='strong' href='mailto:<contact@askfavr.com>' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>contact@askfavr.com</a>
        </p>
    </div>
</body>
</html>
";
        $from = new SendGrid\Email("FAVR", "contact@askfavr.com");

        $subject = "Service Receipt ";
        $to = new SendGrid\Email($this->customer, $this->email);
        $content = new SendGrid\Content("text/html",  $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
        return $this;
    }

    /**
     * @param $id
     * @return $this
     */
    public function sendFreelancerInvoice($id)
    {
        $this->selectRequest($id);
        $this->selectCustomer($this->customer_id);
        $this->selectFreelancer($this->freelancer_id);
        $net_total = $this->price * 0.8;
        $net_total = number_format((float)$net_total, 2, '.', '');
        $message = "<html>
<head>
    <meta charset=\"utf-8\"/>
    <title>Email</title>
    
    </head>

<body>
    <div class=\"invoice-box\" style=\"max-width: 800px;margin: auto;padding: 30px;border: 1px solid #eee;box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);font-size: 16px;line-height: 24px;font-family: &quot;Helvetica Neue&quot;, &quot;Helvetica&quot;, Helvetica, Arial, sans-serif;color: #555\">
        <table cellpadding=\"0\" cellspacing=\"0\" style=\"width: 100%;line-height: inherit;text-align: left\">
            <tr class=\"top\">
                <td colspan=\"2\" style=\"padding: 5px;vertical-align: top\">
                    <table style=\"width: 100%;line-height: inherit;text-align: left\">
                        <tr>
                            <td class=\"title\" style=\"padding: 5px;vertical-align: top;padding-bottom: 20px;font-size: 45px;line-height: 45px;color: #333\">
                                <img src=\"https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png\" style=\"width:100%; max-width:300px;\"/>
                            </td>
                            
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 20px\">
                                <b>Request #: $id</b><br/>
                                <b>Date: $this->task_date</b><br/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class=\"information\">
                <td colspan=\"2\" style=\"padding: 5px;vertical-align: top\">
                    <table style=\"width: 100%;line-height: inherit;text-align: left\">
                        <tr>
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 40px\">
                                FAVR Inc.<br/>
                                14 4th St SW #203<br/>
                                Rochester, MN 55902
                            </td>
                            
                            <td style=\"padding: 5px;vertical-align: top;padding-bottom: 40px\">
                                $this->freelancer<br/>
                                $this->freelancer_email
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            
            <tr class=\"heading\">
                <td style=\"padding: 5px;vertical-align: top;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Task Description:
                </td>
                <td style=\"padding: 5px;vertical-align: top;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Requested By:
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;background: #FF6F6F ;border-bottom: 1px solid #ddd;font-weight: bold\">
                    Amount:
                </td>
            </tr>
            
          
            
            <tr class=\"item\">
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                    $this->task
                </td>
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                    $this->freelancer
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold\">
                   $$this->price
                </td>
            </tr>
            <tr class=\"total\">
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #fff\">
                   
                </td>
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #fff\">
                    
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #fff;font-weight: bold\">
                   Net Total: $$net_total
                </td>
            </tr>
        </table>
        <br>
        <br>
        <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
            With best regards,<br/>
            +1 (507) 440-7130, FAVR Inc. <br/>
        </p>
        <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
            Support: <a class='strong' href='mailto:<contact@askfavr.com>' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>contact@askfavr.com</a>
        </p>
    </div>
</body>
</html>
";
        $from = new SendGrid\Email("FAVR", "contact@askfavr.com");

        $subject = "Service Receipt ";
        $to = new SendGrid\Email($this->freelancer, $this->freelancer_email);
        $content = new SendGrid\Content("text/html",  $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
        return $this;
    }

    //selectRequest($this->request_id)->selectCustomer($this->customer_id)->selectFreelancer($this->freelancer_id)->sendEmail($this->customer, $this->email, $this->message)
}