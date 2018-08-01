<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/29/18
 * Time: 11:58 PM
 */

class Data_Constants
{
    // image upload constants
    const MAXIMUM_IMAGE_UPLOAD_COUNT = 3;
    const MAXIMUM_IMAGE_UPLOAD_SIZE = 5242880; // 5 Mb
    const IMAGE_UPLOAD_FILE_PATH = "/Applications/XAMPP/xamppfiles/favr-request-images/";

    // database marketplace task status constants
    const DB_TASK_STATUS_REQUESTED = "Requested";
    const DB_TASK_STATUS_PENDING_APPROVAL = "Pending Approval";
    const DB_TASK_STATUS_IN_PROGRESS = "In Progress";
    const DB_TASK_STATUS_COMPLETED = "Completed";

    // database marketplace task difficulty constants
    const DB_TASK_INTENSITY_HARD = "Hard";
    const DB_TASK_INTENSITY_MEDIUM = "Medium";
    const DB_TASK_INTENSITY_EASY = "Easy";

    // database connection constants
    const DB_DSN = "mysql:dbname=local_favr;host=localhost";
    const DB_USERNAME = "haron";
    const DB_PASSWORD = "Ha7780703";

    // Product version
    const PRODUCT_VERSION = "0.1.1";

    // Root path
    const ROOT_PATH = "http://localhost/favr-pwa";

    //Stipe API
    const STRIPE_PUBLIC = 'pk_test_WRhN4BKmkqctL2nrjCPJCTXi'; 
    const STRIPE_SECRET = 'sk_test_AfPrHBd85yRDmJmdW4uK3a9Y';

    //Twilio
    const TWILIO_SID = 'AC67883f19920b3894df25adca46a047f4';
    const TWILIO_API  = '43e7aaf5082f72883769685c809838cf'; 

    //Sendgrid
    const SG_API = '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';

}