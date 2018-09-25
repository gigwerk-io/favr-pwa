<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/14/18
 * Time: 9:17 AM
 */

class Web_Financials
{
    /**
     * @var PDO
     */
    private $db;

    /**
     * Data source name
     * @var string
     */
    private $dsn = Data_Constants::DB_DSN;

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

    function __construct() {
        $this->db = $this->connect();
        if(isset($_GET['id'])){
            $this->select($_GET['id']);
        }
    }

    private function connect()
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
     * @param array $file
     */
    public function fileHandler(array $file)
    {
        if(!$file['type'] == 'text/csv'){
            die("Wrong File Type!");
        }
        if($file['error'] == 0){
            $tmpName = $file['tmp_name'];
            $csvAsArray = array_map('str_getcsv', file($tmpName));
            $count = count($csvAsArray);
            //print_r($csvAsArray);
            //die;
            for($i=1;$i<$count; $i++){
                $transactions = array_combine($csvAsArray[0], $csvAsArray[$i]);
                $this->databaseHandler(
                    $transactions['id'],
                    $transactions['Source'],
                    $this->getFreelancer($transactions['Source']),
                    $transactions['Amount'],
                    $transactions['Fee'],
                    $transactions['Amount']*0.8,
                    $transactions['Amount']*0.2 - $transactions['Fee'],
                    $transactions['Created (UTC)'],
                    $transactions['Available On (UTC)']
                );
            }
        }
    }

    public function transferFileHandler(array $file)
    {
        if(!$file['type'] == 'text/csv'){
            die("Wrong File Type!");
        }
        if($file['error'] == 0){
            $tmpName = $file['tmp_name'];
            $csvAsArray = array_map('str_getcsv', file($tmpName));
            $count = count($csvAsArray);
            for($i=1;$i<$count; $i++){
                $transfers = $csvAsArray[$i];
                $this->transferDatabaseHandler(
                    $transfers[1],
                    $transfers[14],
                    $transfers[5],
                    $transfers[0]
                );
            }
        }
    }

    private function transferDatabaseHandler($stripe_id, $account_id, $amount, $created_at)
    {
        $insert_db_query = $this->db->query("INSERT INTO stripe_transfers
                                    (stripe_id,
                                     account_id, 
                                     amount,
                                     created_at)
                                 VALUES 
                                    ('$stripe_id',
                                     '$account_id',
                                     $amount,
                                     '$created_at')
            ");
        if($insert_db_query) {
            echo "<script> window.location.href='http://test.askfavr.com/admin/cfo/transfers.php'; </script>";
        }
    }
    private function databaseHandler($stripe_id, $source_id, $freelancer_id, $amount, $stripe_fee, $freelancer_fee, $net_profit, $created_at, $available_at)
    {
        $insert_db_query = $this->db->query("INSERT INTO stripe_payments
                                    (stripe_id,
                                     source_id, 
                                     freelancer_id, 
                                     amount,
                                     stripe_fee,
                                     freelancer_fee,
                                     net_profit,
                                     created_at,
                                     available_at)
                                 VALUES 
                                    ('$stripe_id',
                                     '$source_id',
                                     $freelancer_id,
                                     $amount,
                                     $stripe_fee,
                                     $freelancer_fee,
                                     $net_profit,
                                     '$created_at',
                                     '$available_at')
            ");
        if($insert_db_query){
            echo "<script> window.location.href='http://192.168.64.2/favr-pwa/admin/cfo/payments.php'; </script>";
        }
    }

    private function getFreelancer($token)
    {
        $sth = $this->db->query("SELECT * FROM marketplace_favr_requests WHERE task_stripe_token='$token'");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row['freelancer_id'];
    }




}