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
$page->setTitle("Sign Up");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/signin.css' />");
$page->renderHeader(false);

// Script to process user sign up
if (isset($_POST['signUp'], $_POST['signUpUsername'], $_POST['signUpFirstName'], $_POST['signUpLastName'], $_POST['signUpEmail'], $_POST['signUpPass'], $_POST['signUpPassConfirm'])) {
    $signUpUsername = $_POST['signUpUsername'];
    $signUpEmail = $_POST['signUpEmail'];
    $signUpFirstName = $_POST['signUpFirstName'];
    $signUpLastName = $_POST['signUpLastName'];
    $signUpPass = $_POST['signUpPass'];
    $signUpPassConfirm = $_POST['signUpPassConfirm'];

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
$page->addScript("<!-- Hotjar Tracking Code for askfavr.com -->
    <script>
        (function(h,o,t,j,a,r){
            h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)};
            h._hjSettings={hjid:893054,hjsv:6};
            a=o.getElementsByTagName('head')[0];
            r=o.createElement('script');r.async=1;
            r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv;
            a.appendChild(r);
        })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv=');
    </script>
   
    <!-- Facebook Pixel Code -->
        <script>
        !function(f,b,e,v,n,t,s)
        {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
        n.callMethod.apply(n,arguments):n.queue.push(arguments)};
        if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
        n.queue=[];t=b.createElement(e);t.async=!0;
        t.src=v;s=b.getElementsByTagName(e)[0];
        s.parentNode.insertBefore(t,s)}(window, document,'script',
        'https://connect.facebook.net/en_US/fbevents.js');
        fbq('init', '1650241185015256');
        fbq('track', 'PageView');
        </script>
        <noscript>
            <img height=\"1\" width=\"1\" style=\"display:none\"
            src=\"https://www.facebook.com/tr?id=1650241185015256&ev=PageView&noscript=1\"/>
        </noscript>
        <!-- End Facebook Pixel Code -->
    <script>");
$page->renderFooter();

?>