<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/6/18
 * Time: 11:42 PM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// constants
$PAGE_ID = 0;
$USER = "guest";
$ALERT_MESSAGE = "";
if (isset($_GET['ALERT_MESSAGE'])) {
    $ALERT_MESSAGE = $_GET['ALERT_MESSAGE'];
    $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Success!</strong> $ALERT_MESSAGE
            </div>
        ";
}

$page = new Web_Page($PAGE_ID, $USER);

// Script to process user sign in
if (isset($_POST['signIn'], $_POST['signInUsernameEmail'], $_POST['signInPass'])) {
    $signInUsernameEmail = $_POST['signInUsernameEmail'];
    $signInPass = md5($_POST['signInPass']);

    $signInSuccessful = $page->signInUser($signInUsernameEmail, $signInPass);

    if ($signInSuccessful) {
        // successful signin with redirect
        $_SESSION['user'] = $signInUsernameEmail;
        header("Location: ../");
    } else {
        // failure
        $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-danger alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Error!</strong> Ope, Better check yourself, your credentials don't seem to match anything in our system.
            </div>
        ";
    }
}

$page->setTitle("Sign In");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/signin.css' />");
$page->renderHeader(false);

echo $ALERT_MESSAGE;
?>
    <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="pb-2 mb-0">
            <div class="text-center">
                <img src="<?php echo $page->root_path; ?>/assets/brand/favr_logo_rd.png" class="img-fluid" height="30%"
                     width="30%">
            </div>
        </h6>
        <form class="form-signin" method="post">
            <div class="row">
                <label for="inputUsernameEmail" class="sr-only">Email or Username</label>
                <input type="text" name="signInUsernameEmail" id="inputUsernameEmail" class="form-control" placeholder="Email or Username" required="">
            </div>
            <div class="row">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" name="signInPass" id="inputPassword" class="form-control" placeholder="Password" required="">
            </div>
            <div class="row">
                <div class="d-inline-flex">
                    <label>
                        <a href="#">Forgot password</a>
                    </label>
                    <label class="ml-2">
                        <a href="<?php echo $page->root_path; ?>/signup/">New account</a>
                    </label>
                </div>
                <input type="submit" name="signIn" class="btn btn-lg btn-primary btn-block" value="Sign In">
            </div>
        </form>
    </div>
<?php
$page->renderFooter();
?>