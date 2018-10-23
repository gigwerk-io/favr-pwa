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
     * Web_Invoice constructor.
     */
    public function __construct() {
        $this->db = $this->connect();
    }

    /**
     * PDO Connection.
     * @return PDO|string
     */
    private function connect()
    {
        //Set up PDO connection
        try {
            $db = new PDO($this->dsn, $this->username, $this->password);
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            return $db;
        } catch (PDOException $e) {
            echo "Error: Unable to load this page. Please contact arama006@umn.edu for assistance.";
            return "<br/>Error: " . $e;
        }
    }


    /**
     * Get Favr Request model.
     * @param $id
     * @return mixed
     */
    private function getFavrRequest($id)
    {
        $result = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE id=$id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }

    private function renderCustomerHTML($id, $task_date, $customer, $email, $task, $freelancer, $price)
    {
        return "<html>
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
                                        <b>Date: $task_date</b><br/>
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
                                        $customer<br/>
                                        $email
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
                            $task
                        </td>
                        <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                            $freelancer
                        </td>
                        <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold\">
                           Total: $$price
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
        </html>";
    }

    /**
     * Freelancer Email View.
     * @param $id
     * @param $task_date
     * @param $freelancer
     * @param $freelancer_email
     * @param $task
     * @param $customer
     * @param $price
     * @param $net_total
     * @return string
     */
    private function renderFreelancerHTML($id, $task_date, $freelancer, $freelancer_email, $task, $customer, $price, $net_total)
    {
        return"<html>
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
                                <b>Date: $task_date</b><br/>
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
                                $freelancer<br/>
                                $freelancer_email
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
                    $task
                </td>
                <td style=\"padding: 5px;vertical-align: top;border-bottom: 1px solid #eee\">
                    $customer
                </td>
                <td style=\"padding: 5px;vertical-align: top;text-align: right;border-top: 2px solid #eee;font-weight: bold\">
                   $$price
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
</html>";
    }


    /**
     * Get Customer Model.
     * @param $id
     * @return mixed
     */
    private function getCustomer($id)
    {
        $result = $this->db->query("SELECT * FROM users WHERE id=$id");
        return $result->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Get Freelancer Model.
     * @param int $id
     * @return mixed
     */
    private function getFreelancers(int $id)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_freelancers WHERE request_id=$id AND approved=1");
        return $sth->fetch(PDO::FETCH_ASSOC);
    }


    /**
     * Freelancer Email Controller.
     * @param $id
     */
    public function processFreelancerInvoice($id)
    {
        $marketplace = $this->getFavrRequest($id);
        $freelancers = $this->getFreelancers($id);
        $customer = $this->getCustomer($id);
        $price = $marketplace['task_price'];
        $net_total = $price * 0.8;
        $net_total = number_format((float)$net_total, 2, '.', '');
        foreach ($freelancers as $freelancer){
            $this->renderFreelancerHTML(
                $id,
                date('F j, g:i a', strtotime($marketplace['task_date'])),
                $freelancer['first_name'] . " ". $freelancer['lat_name'],
                $freelancer['email'],
                $marketplace['task_description'],
                $customer['first_name'] . " " . $customer['last_name'],
                $price,
                $net_total
            );
            $from = new SendGrid\Email("FAVR", "invoice@askfavr.com");

            $subject = "Service Invoice ";
            $to = new SendGrid\Email($freelancer['first_name'] . " " . $freelancer['last_name'], $freelancer['email']);
            $content = new SendGrid\Content("text/html",  $message);
            $mail = new SendGrid\Mail($from, $subject, $to, $content);
            $sg = new \SendGrid(\Data_Constants::SG_API);
            $sg->client->mail()->send()->post($mail);
        }
    }

    /**
     * Customer Email Controller.
     * @param $id
     * @return $this
     */
    public function processCustomerInvoice($id)
    {
        $marketplace = $this->getFavrRequest($id);
        $freelancers = $this->getFreelancers($id);
        $customer = $this->getCustomer($id);
        $message= $this->renderCustomerHTML(
            "$id",
            date('F j, g:i a', strtotime($marketplace['task_date'])),
            $customer['first_name'] . " " . $customer['last_name'],
            $customer['email'],
            $marketplace['task_description'],
            $this->printFreelancers($freelancers),
            $marketplace['task_price']
        );
        $from = new SendGrid\Email("FAVR", "invoice@askfavr.com");

        $subject = "Service Receipt ";
        $to = new SendGrid\Email($customer['first_name'] . " " . $customer['last_name'], $customer['email']);
        $content = new SendGrid\Content("text/html",  $message);
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
        return $this;
    }

    /**
     * List of Freelancers for customer email.
     * @param $rows
     */
    private function printFreelancers($rows)
    {
        foreach($rows as $row){
            echo $row['first_name'] . " " . $row['last_name'] . " <br/>";
        }
    }

}