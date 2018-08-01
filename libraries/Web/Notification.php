<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 8/1/18
 * Time: 1:16 PM
 */

use Twilio\Rest\Client;
require '../Api/Twilio/twilio-php-master/Twilio/autoload.php';
class Web_Notification
{
    /**
     * @param string $phone
     * @param string $message
     * @throws \Twilio\Exceptions\ConfigurationException
     */
    public function sendNotification(string $phone, string $message)
    {
        $client = new Client(Data_Constants::TWILIO_SID, Data_Constants::TWILIO_API);
        //send message to customer
        $client->messages->create(
            $phone,
            array(
                //trial number
                'from' => '+15074734314',
                'body' => $message,
            )
        );
    }


}