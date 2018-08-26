<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/7/18
 * Time: 1:53 AM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

if (isset($_SESSION['user_info']) && $_SESSION['user_info']['id'] != -1) {
    header("Location: ../home/");
}
//constants
$USER = "guest";
$ALERT_MESSAGE = "";

$page = new Web_Page($USER);
$page->setTitle("FAVR | Sign Up");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/signin.css' />");
$page->renderHeader(false);

// Script to process user sign up
if (isset($_POST['signUp'], $_POST['signUpUsername'], $_POST['signUpFirstName'], $_POST['signUpLastName'], $_POST['signUpEmail'],  $_POST['signUpPhone'], $_POST['signUpPass'], $_POST['signUpPassConfirm'])) {
    $signUpUsername = $_POST['signUpUsername'];
    $signUpEmail = $_POST['signUpEmail'];
    $signUpPhone = $_POST['signUpPhone'];
    $signUpFirstName = $_POST['signUpFirstName'];
    $signUpLastName = $_POST['signUpLastName'];
    $signUpPass = $_POST['signUpPass'];
    $signUpPassConfirm = $_POST['signUpPassConfirm'];

    $signUpSuccessful = $page->signUpUser($signUpUsername, $signUpEmail, $signUpPhone, $signUpFirstName, $signUpLastName, $signUpPass, $signUpPassConfirm);

    if ($signUpSuccessful) {
        // successful sign up
        $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-success alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Congratulations!</strong> You've signed up and are now ready for FAVR: please sign in.
            </div>";
    } else {
        // error passwords don't match
        $ALERT_MESSAGE = "
            <div class=\"my-3 p-3 alert alert-danger alert-dismissible\" role=\"alert\">
                <button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\"><span aria-hidden=\"true\">&times;</span></button>
                <strong>Error!</strong> Ope, Better check yourself, your passwords don't seem to match.
            </div>
        ";
    }
}

echo $ALERT_MESSAGE;
?>
    <div class="my-3 p-3 bg-white rounded box-shadow">
        <h6 class="pb-2 mb-0">
            <div class="text-center">
                <img src="<?php echo $page->root_path; ?>/assets/brand/favr_logo_rd.png" class="img-fluid" height="30%"
                     width="30%">
            </div>
        </h6>
        <form class="form-signin" action="" method="post">
            <div class="form-label-group">
                <input type="text" name="signUpUsername" id="inputUsername" class="form-control" placeholder="Username" required="">
                <label for="inputUsername">Username</label>
            </div>
            <div class="form-label-group">
                <input type="text" name="signUpFirstName" id="inputFirstName" class="form-control" placeholder="First name" required="">
                <label for="inputFirstName">First name</label>
            </div>
            <div class="form-label-group">
                <input type="text" name="signUpLastName" id="inputLastName" class="form-control" placeholder="Last name" required="">
                <label for="inputLastName">Last name</label>
            </div>
            <div class="form-label-group">
                <input type="email" name="signUpEmail" id="inputEmail" class="form-control" placeholder="Email address" required="">
                <label for="inputEmail">Email address</label>
            </div>
            <div class="form-label-group">
                <input type="tel" name="signUpPhone" id="inputPhone" class="form-control" placeholder="Phone Number" required="">
                <label for="inputPhone">Phone Number</label>
            </div>
            <div class="form-label-group">
                <input type="password" name="signUpPass" id="inputPassword" class="form-control" placeholder="Password" required="">
                <label for="inputPassword">Password</label>
            </div>
            <div class="form-label-group">
                <input type="password" name="signUpPassConfirm" id="inputConfirmPassword" class="form-control" placeholder="Re-enter Password" required="">
                <label for="inputConfirmPassword">Re-enter Password</label>
            </div>
            <div class="row">
                <div class="d-inline-flex">
                    <label>
                        <a href="<?php echo $page->root_path; ?>/signin">Sign in</a>
                    </label>
                </div>
                <input type="submit" name="signUp" class="btn btn-lg btn-primary btn-block" value="Sign Up">
            </div>
        </form>
    </div>
<?php
$page->renderFooter();

?>