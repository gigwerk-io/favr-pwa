<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 12:48 PM
 */

session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/include/favr-pwa/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($USER);

// TODO: put this in a process function
if (isset($_FILES['profile_image']) ||  isset($_POST['profile_description'])) {
    $profile_image = $_FILES['profile_image'];
    $profile_description = $_POST['profile_description'];
    $userID = $_SESSION['user_info']['id'];

    if ($profile_image['type'] == 'image/jpeg' || $profile_image['type'] == 'image/png') {
        if ($profile_image['size'] <= Data_Constants::MAXIMUM_IMAGE_UPLOAD_SIZE) {
            if ($profile_image['type'] == "image/jpeg") {
                $profile_image_name = md5($userID) . "-profile.jpg";
            } else {
                $profile_image_name = md5($userID) . "-profile.png";
            }

            $profile_image_path = Data_Constants::IMAGE_UPLOAD_PROFILE_IMAGE_FILE_PATH;

            if (copy($profile_image['tmp_name'], "$profile_image_path" . $profile_image_name)) {

                $imageFileName = $profile_image_name;
                $imageFileType = $profile_image['type'];
                $imageFileSize = $profile_image['size'];
                $imageFileID = $userID;

                $imageDataArray = array('name' => "$imageFileName",
                                        'type' => "$imageFileType",
                                        'size' => $imageFileSize,
                                        'task_id' => $imageFileID);

                $imageDataArray = serialize($imageDataArray);
                // TODO: validation of successsful upload/error reporting
                $update_profile_query = "UPDATE users 
                                         SET profile_picture_path = '$imageDataArray',
                                             profile_description = '$profile_description'
                                         WHERE id = $userID";
                $result = $page->db->query($update_profile_query);
            }
        }
    } else {
        $update_profile_query = "UPDATE users 
                                 SET profile_description = '$profile_description'
                                 WHERE id = $userID";
        $result = $page->db->query($update_profile_query);
    }
}

if (isset($_GET['d_request_id'])) {
    $page->processDeleteRequest($_GET['d_request_id'], $_SESSION['user_info']['id']);
}

if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}

$page->setTitle("". $_SESSION['user_info']['username'] ."");
$page->renderHeader();
echo $ALERT_MESSAGE;
$page->renderFavrProfile($_SESSION['user_info']['id']);
echo "<a class='small' href='$page->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends'>Go to friends</a>";
$page->renderFavrProfileHistory($_SESSION['user_info']['id']);
$page->addScript("
<script> 
    
//    window.setInterval(function(){
//      // call your function here
//        $('#notifications').load('notifications.php')
//    }, 5000);
</script>
");
$page->renderFooter();