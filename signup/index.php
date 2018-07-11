<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/7/18
 * Time: 1:53 AM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

//constants
$PAGE_ID = 2;
$USER = "guest";
$ALERT_MESSAGE = "";

$page = new Web_Page($PAGE_ID, $USER);
$page->setTitle("Sign In");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/signin.css' />");
$page->renderHeader(false);

// Script to process user sign up
if (isset($_POST['signUp'], $_POST['signUpUsername'], $_POST['signUpFirstName'], $_POST['signUpLastName'], $_POST['signUpEmail'], $_POST['signUpPass'], $_POST['signUpPassConfirm'])) {
    $signUpUsername = $_POST['signUpUsername'];
    $signUpEmail = $_POST['signUpEmail'];
    $signUpFirstName = $_POST['signUpFirstName'];
    $signUpLastName = $_POST['signUpLastName'];
    $signUpPass = md5($_POST['signUpPass']);
    $signUpPassConfirm = md5($_POST['signUpPassConfirm']);

    $signUpSuccessful = $page->signUpUser($signUpUsername, $signUpEmail, $signUpFirstName, $signUpLastName, $signUpPass, $signUpPassConfirm);

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
            <div class="row">
                <label for="inputUsername" class="sr-only">Username</label>
                <input type="text" name="signUpUsername" id="inputUsername" class="form-control" placeholder="Username" required="">
            </div>
            <div class="row">
                <label for="inputFirstName" class="sr-only">First name</label>
                <input type="text" name="signUpFirstName" id="inputFirstName" class="form-control" placeholder="First name" required="">
            </div>
            <div class="row">
                <label for="inputLastName" class="sr-only">Last name</label>
                <input type="text" name="signUpLastName" id="inputLastName" class="form-control" placeholder="Last name" required="">
            </div>
            <div class="row">
                <label for="inputEmail" class="sr-only">Email address</label>
                <input type="email" name="signUpEmail" id="inputEmail" class="form-control" placeholder="Email address" required="">
            </div>
            <div class="row">
                <label for="inputPassword" class="sr-only">Password</label>
                <input type="password" name="signUpPass" id="inputPassword" class="form-control" placeholder="Password" required="">
            </div>
            <div class="row">
                <label for="inputConfirmPassword" class="sr-only">Password</label>
                <input type="password" name="signUpPassConfirm" id="inputConfirmPassword" class="form-control" placeholder="Re-enter Password" required="">
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