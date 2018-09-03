<?php
/**
 * Created by PhpStorm.
 * User: Dell
 * Date: 8/5/2018
 * Time: 10:00 PM
 * @author Solomon
 */
//echo '<pre>';
include '../../include/autoload.php';
include '../Api/Twilio/twilio-php-master/Twilio/autoload.php';
$notify = new Web_Notification();
$mysqli = NEW MySQLi('favr.cgfeyejwt7qv.us-east-2.rds.amazonaws.com', 'Solomon04', 'Nomolos.99', 'favr_pwa_schema');

error_reporting(E_ERROR);
$res = $mysqli->query("SELECT *FROM users WHERE id=85");
echo '<pre>';
while ($rows = $res->fetch_assoc()) {
//    echo $name = $rows['first_name'];
//    $message = $notify->sendNotification($rows['phone'], "Hi $name, someone has responded to your FAVR! Please accept or deny them within the application. \n \n- FAVR Inc.");
//    print_r($message);die;
}
//$invoice = new Web_Invoice();
//echo '<pre>';
//var_dump($invoice->sendCustomerInvoice(78)->sendFreelancerInvoice(78));
//$test_payment = new Web_Payment();
//if(isset($_GET['id']))
//{
//    $results = $test_payment->select($_GET['id'])->checkOut($_GET['id']);
//}
//if(isset($_POST['stripeToken']))
//{
//    $test_payment->charge($_POST['stripeToken'])->update($_GET['id'])->createChat();
//}
//print_r($results);
//$notification = new Web_Notification();
//$text = $notification->sendNotification("5074407130", "Hello Solomon");
//$payment = new Web_Payment();
//
//if(isset($_GET['id']))
//{
//    $payment->select($_GET['id'])->checkOut($_GET['id']);
//    if(isset($_POST['stripeToken']))
//    {
//        $payment->charge($_POST['stripeToken'])->update($_GET['id']);
//    }
//
//} else {
//    print("Hello World");
//}
//echo $message = "
//<html>
//  <head/>
//  <body style='margin: 0;padding: 0;mso-line-height-rule: exactly;min-width: 100%;background-color: #fff'>
//    <center class='wrapper' style='display: table;table-layout: fixed;width: 100%;min-width: 620px;-webkit-text-size-adjust: 100%;-ms-text-size-adjust: 100%;background-color: #fff'>
//    <table class='top-panel center' width='602' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse;border-spacing: 0;margin: 0 auto;width: 602px'>
//        <tbody>
//        <tr>
//            <td class='title' width='300' style='padding: 8px 0;vertical-align: top;text-align: left;width: 300px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 14px'><b>FAVR Inc.</b></td>
//            <td class='subject' width='300' style='padding: 8px 0;vertical-align: top;text-align: right;width: 300px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 14px'><a class='strong' href='https://askfavr.com' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>https://askfavr.com</a></td>
//        </tr>
//        <tr>
//            <td class='border' colspan='2' style='padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;background-color: #e0e0e0;width: 1px'> </td>
//        </tr>
//        </tbody>
//    </table>
//
//    <div class='spacer' style='font-size: 1px;line-height: 16px;width: 100%'> </div>
//
//    <table class='main center' width='602' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse;border-spacing: 0;margin: 0 auto;width: 602px;-webkit-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);-moz-box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24);box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.12), 0 1px 2px 0 rgba(0, 0, 0, 0.24)'>
//        <tbody>
//        <tr>
//            <td class='column' style='padding: 0;vertical-align: top;text-align: left;background-color: #fff;font-size: 14px'>
//                <div class='column-top' style='font-size: 24px;line-height: 24px'> </div>
//                <table class='content' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse;border-spacing: 0;width: 100%'>
//                    <tbody>
//                    <tr>
//                        <td class='padded' style='padding: 0 24px;vertical-align: top'>
//                          <h1 style='margin-top: 0;margin-bottom: 16px;color: #212121;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 32px;line-height: 28px'> <b>FAVR</b> Invoice</h1>
//                          <p style='margin-top: 0;margin-bottom: 16px;color: #212121;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 24px;line-height: 24px'>Invoice #{id} </p>
//                        </td>
//                    </tr>
//                    </tbody>
//                </table>
//               <table style=\"font-family: arial, sans-serif;border-collapse: collapse;width: 100%\">
//                  <tr>
//                    <th style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Key</th>
//                    <th style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Value</th>
//                  </tr>
//                  <tr style=\"background-color: #ddd\">
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Date Completed:</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">#{date}</td>
//                  </tr>
//                  <tr>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Completed By:</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">#{freelancer}</td>
//                  </tr>
//                  <tr style=\"background-color: #ddd\">
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Price:</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">#{Money}</td>
//                  </tr>
//                  <tr>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Task</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">#{Task}</td>
//                  </tr>
//                  <tr style=\"background-color: #ddd\">
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Laughing Bacchus Winecellars</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Yoshi Tannamuri</td>
//                  </tr>
//                  <tr>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Magazzini Alimentari Riuniti</td>
//                    <td style=\"border: 1px solid #ddd;text-align: left;padding: 8px\">Giovanni Rovelli</td>
//                  </tr>
//                </table>
//
//                <div class='column-bottom' style='font-size: 8px;line-height: 8px'> </div>
//            </td>
//        </tr>
//        </tbody>
//    </table>
//
//    <div class='spacer' style='font-size: 1px;line-height: 16px;width: 100%'> </div>
//
//    <table class='footer center' width='602' border='0' cellspacing='0' cellpadding='0' style='border-collapse: collapse;border-spacing: 0;margin: 0 auto;width: 602px'>
//        <tbody>
//        <tr>
//            <td class='border' colspan='2' style='padding: 0;vertical-align: top;font-size: 1px;line-height: 1px;background-color: #e0e0e0;width: 1px'> </td>
//        </tr>
//        <tr>
//            <td class='signature' width='300' style='padding: 0;vertical-align: bottom;width: 300px;padding-top: 8px;margin-bottom: 16px;text-align: left'>
//                <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
//                    With best regards,<br/>
//                    FAVR<br/>
//                    +1 (507) 440-7130, FAVR Inc. <br/>
//                    </p>
//                <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
//                    Support: <a class='strong' href='mailto:<contact@askfavr.com>' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>contact@askfavr.com</a>
//                </p>
//            </td>
//            <td class='subscription' width='300' style='padding: 0;vertical-align: bottom;width: 300px;padding-top: 8px;margin-bottom: 16px;text-align: right'>
//                <div class='logo-image' style=''>
//                    <a href='https://askfavr.com' target='_blank' style='text-decoration: none;color: #616161'><img src='https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png' alt='logo-alt' width='120' height='55' style='border: 0;-ms-interpolation-mode: bicubic'/></a>
//                </div>
//                <p style='margin-top: 0;margin-bottom: 8px;color: #616161;font-family: Roboto, Helvetica, sans-serif;font-weight: 400;font-size: 12px;line-height: 18px'>
//                    <a class='strong block' href='#' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>
//
//                    </a>
//                    <span class='hide'>  |  </span>
//                    <a class='strong block' href='#' target='_blank' style='font-weight: 700;text-decoration: none;color: #616161'>
//
//                    </a>
//                </p>
//            </td>
//        </tr>
//        </tbody>
//    </table>
//</center>
//  </body>
//</html>";

//";