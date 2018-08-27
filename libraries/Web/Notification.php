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
//        header("location: http://localhost:1234/favr-pwa/components/notifications/?navbar=active_notifications");
    }


}