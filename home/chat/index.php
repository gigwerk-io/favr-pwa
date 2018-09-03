<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/13/18
 * Time: 11:37 AM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$PAGE_ID = 5;
$USER = "";
$list = new Web_Chat();

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}

$page = new Web_Page($PAGE_ID, $USER);

//header("Location: ../../assets/css/chat.css");

$page->setTitle("Chat");
$page->addStylesheet("<link rel='stylesheet' href='$page->root_path/assets/css/chat.css'>");
$page->renderHeader();
if (!empty($_POST['Text']))
{
    $message = array(
        "File" => $_GET['file'],
        "To" => $_GET['to'],
        "Text" => $_POST['Text'],
        "Time" => time()
    );
    $list->updateMessage($message);
}
?>
    <!--    <meta http-equiv="refresh" content="30"/>-->

    <div class="messaging m-0 p-0">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h4>Recent</h4>
                    </div>
                    <div class="srch_bar">
                        <div class="stylish-input-group">
                            <input type="text" class="search-bar" placeholder="Search">
                            <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span></div>
                    </div>
                </div>
                <?php
                echo "<div class=\"inbox_chat\">";
                $list->listChat();
                echo "</div>";
                echo "<div id=\"status\">
                </div>";
                ?>
            </div>
            <div class="web-messaging-contact mesgs p-0">
                <div class="msg_history p-2 pt-5">
                    <?php
                    if(isset($_GET['file']))
                    {
                        $list->displayMessage($_GET['file']);
                    }
                    ?>
                </div>
                <div class="type_msg m-0">
                    <div class="input_msg_write">
                        <form action="<?php echo "?file=" . $_GET['file'] . "&to=" . $_GET['to']; ?>" method="post" id="Message">
                            <textarea onkeyup="resetTimer = true" style="border-radius: 0;" type="text" class="form-control m-0 pt-2" name="Text" placeholder="Type a message..."></textarea>
                        </form>
                        <button class="msg_send_btn text-center p-1" form="Message" type="submit"><i class="material-icons"
                                                                                                     aria-hidden="true">send</i></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        // function refreshPageUnlessFocusedOn (el) {
        //
        //     setInterval(function () {
        //         if(el !== document.activeElement) {
        //             document.location.reload();
        //         }
        //     }, 12500)
        //
        // }
        //
        // refreshPageUnlessFocusedOn(document.querySelector('textarea'));
    </script>
<?php
$page->addScript("
<script>
//    window.location.hash = '#recent-15';
    if (screen.width <= 767.98) {
        window.location.hash = '#recent-15';
    }
</script>
<!-- Hotjar Tracking Code for askfavr.com -->
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
$page->renderFooter(false);
?>