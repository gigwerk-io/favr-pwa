<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 7/31/18
 * Time: 1:16 PM
 */

require '../Api/Stripe/init.php';



class Payment
{
    /**
     * @param $id
     */
    public function select($id)
    {
        //select specific request based off of the id
    }

    /**
     * @param $token
     * @param $price
     */
    public function charge($token,$price)
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
     * @param $id
     */
    public function update($id)
    {
        //update status from pending approval to in progress based off the id
    }

}