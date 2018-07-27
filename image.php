<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/27/18
 * Time: 1:08 AM
 */

$imageType = $_GET['i_t'];
$image = $_GET['i'];

if (isset($image, $imageType)) {
    header("Content-Type: $imageType");
    readfile("../../favr-request-images/$image");
} else {
    header("Location: index.php");
}
