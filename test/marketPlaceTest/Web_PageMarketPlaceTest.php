<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/16/18
 * Time: 11:06 AM
 */

namespace marketPlaceTest;
require __DIR__ . "/../../libraries/Web/Page.php";

use PHPUnit\Framework\TestCase;
use PDO;
use PDOException;
use Web_Page;

class Web_PageTest extends TestCase
{

//    public function testRenderFavrRequestForm()
//    {
//
//    }

    public function testRenderFavrMarketplace()
    {
        try {
            $page = new Web_Page(1, "test");

            //Set up PDO connection
            try {
                $db = new PDO($page->dsn, $page->username, $page->password);
                $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
                $page->db = $db;
            } catch (PDOException $e) {
                echo "Error: Unable to load this page. Please contact arama006@umn.edu for assistance.";
                echo "<br/>Error: " . $e;
            }

            $page->signInUser("test", md5("test"));
            // test global scope
            $this->assertTrue($page->renderFavrMarketplace("global"));
            $page->signOutUser();
        } catch (\Exception $e) {
            echo "Error";
        }
    }

//    public function testProcessFavrRequestToDB()
//    {
//
//    }
}
