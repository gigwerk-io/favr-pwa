<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/16/18
 * Time: 2:17 PM
 */

namespace connectionTest;

use PDO;
use PHPUnit\Framework\TestCase;
use PHPUnit\DbUnit\TestCaseTrait;

class Web_PageConnectTest extends TestCase
{
    use TestCaseTrait;

    public function getConnection() {
        $dsn = 'mysql:dbname=local_favr;host=localhost';
        $username = 'haron';
        $password = 'Ha7780703';

        $pdo = new PDO($dsn, $username, $password);

        return $this->createDefaultDBConnection($pdo, 'local_favr');
    }

    public function getDataSet()
    {
        // TODO: Implement getDataSet() method.
    }

    public function testTest()
    {
        $this->assertTrue(1 + 1 == 2);
    }
}
