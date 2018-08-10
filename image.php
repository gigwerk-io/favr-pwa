<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/27/18
 * Time: 1:08 AM
 *
 * @author haronarama
 */

$imageType = $_GET['i_t'];
$image = $_GET['i'];
$notImageProfile = true;

if (isset($_GET['i_p'])) {
    $notImageProfile = false;
}

if (isset($image, $imageType)) {

    header("Content-Type: $imageType");
    if (!empty($image) && !empty($imageType) && $notImageProfile) {
        readfile("../../favr-request-images/$image");
    } else if (!empty($image) && !empty($imageType) && !$notImageProfile) {
        readfile("../../favr-profile-images/$image");
    } else {
        readfile("../../favr-profile-images/placeholder.png");
    }
} else {
    header("Location: index.php");
}
