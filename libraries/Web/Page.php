<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 6/15/18
 * Time: 12:25 AM
 *
 * @author haronarama
 */

class Web_Page
{
    /**
     * String value to keep track and validate product version
     * @var string
     */
    public $product_version = "0.0.1";

    /**
     * Boolean to determine whether or not to render page main navigation/menu
     * @var boolean
     */
    public $render_main_navigation;

    /**
     * String value for page title default is "FAVR"
     * @var string
     */
    public $page_title;

    /**
     * Any style sheet that should be loaded in the page header
     * @var string
     */
    public $stylesheet = "";

    /**
     * Any javaScript that should be appended to the footer to load faster
     * @var string
     */
    public $script = "";

    /**
     * The database handler
     * @var PDO
     */
    public $db;

    /**
     * value to determine project root path
     * @var string
     */
    public $root_path = "http://localhost/favr-pwa";

    /**
     * Value to identify user
     * @var string
     */
    public $user;

    /**
     * User class: 0 - Admin, 1 - User, 2 - Freelancer, 3 - Guest
     * @var integer
     */
    public $user_class = 3;

    /**
     * Unique page id that will allow admin to set settings for potential additional users
     * @var integer 0 - Home, 1 - Products, 2 - Reports, 3 - Settings, 4 - Tenants
     */
    public $page_id;

    /**
     * Whether or not user is able to edit content
     * @var boolean
     */
    public $editor = false;

    /**
     * Constructor for the page. Sets up most of the properties of this object.
     *
     * @param $page_id
     * @param $page_title
     * @param $user
     * @param $render_main_navigation
     */
    function __construct($page_id, $user = "", $page_title = "FAVR", $render_main_navigation = true)
    {
        $this->page_title = $page_title;
        $this->render_main_navigation = $render_main_navigation;

        // Connect to database
        $this->db = $this->connect();

        //Set up $this->user

        $this->page_id = $page_id;

        $_SESSION['user'] = $this->user = $user;

        // not permitted user
        if (empty($_SESSION['user']) && empty($this->user)) {
            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/signin/");
        }
    }

    /**
     * Connect to the database.
     * @see PDO
     */
    function connect()
    {
        //Set up PDO connection
        try {
            $db = new PDO("mysql:host=localhost;dbname=local_favr", "haron", "Ha7780703");
            $db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
            return $db;
        } catch (PDOException $e) {
            echo "Error: Unable to load this page. Please contact arama006@umn.edu for assistance.";
            echo "<br/>Error: " . $e;
        }
    }

    /**
     * Sign out a user and return if the operation was successful or not
     *
     * @return boolean
     *
     * @author haronarama
     */
    function signOutUser()
    {
        unset($_SESSION);
        unset($_POST);
        unset($_GET);
        unset($_COOKIE);
        unset($this);

        if (session_destroy()) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Sign in a user and return if the operation was successful or not
     *
     * @param $signInUsernameEmail
     * @param $signInPass
     *
     * @return boolean
     *
     * @author haronarama
     */
    function signInUser($signInUsernameEmail, $signInPass)
    {
        $select_sign_in_query = "SELECT * 
                                 FROM users
                                 WHERE email='$signInUsernameEmail'
                                 OR username='$signInUsernameEmail'
                                 AND password='$signInPass'";
        $sign_in_result = $this->db->query($select_sign_in_query);
        $sign_in_row = $sign_in_result->fetch(PDO::FETCH_ASSOC);

        if (!empty($sign_in_row)) {
            // successful sign in
            $this->user = $sign_in_row['username'];

            return true;
        } else {
            // failed to sign in
            return false;
        }
    }

    /**
     * Sign up a user and return if the operation was successful or not
     *
     * @param $signUpUsername
     * @param $signUpEmail
     * @param $signUpFirstName
     * @param $signUpLastName
     * @param $signUpPass
     * @param $signUpPassConfirm
     *
     * @return boolean
     *
     * @author haronarama
     */
    function signUpUser($signUpUsername, $signUpEmail, $signUpFirstName, $signUpLastName, $signUpPass, $signUpPassConfirm)
    {
        if ($signUpPass != $signUpPassConfirm) {
            // error passwords don't match
            return false;
        } else {
            // success

            $insert_sign_up_query = "INSERT INTO users
                                    (username,
                                     password, 
                                     first_name, 
                                     last_name, 
                                     email)
                                 VALUES 
                                    ('$signUpUsername',
                                     '$signUpPassConfirm',
                                     '$signUpFirstName',
                                     '$signUpLastName',
                                     '$signUpEmail'
                                     )
            ";

            $this->db->query($insert_sign_up_query);

            return true;
        }
    }

    /**
     * Set the title of the page.
     * Must be called before renderHeader.
     * @param $page_title string The title of the page.
     */
    function setTitle($page_title)
    {
        if (trim($page_title) != "") {
            $this->page_title .= " - " . $page_title;
        }
    }

    /**
     * Set any stylesheets that need to be loaded in the header.
     * Must be called before renderHeader.
     * @param $add_stylesheet string The stylesheet to load.
     */
    function addStylesheet($add_stylesheet)
    {
        if (trim($add_stylesheet) != "") {
            $this->stylesheet = $add_stylesheet;
        }
    }

    /**
     * Set any stylesheets that need to be loaded in the header.
     * Must be called before renderHeader.
     * @param $add_script string The stylesheet to load.
     */
    function addScript($add_script)
    {
        if (trim($add_script) != "") {
            $this->script .= $add_script;
        }
    }

    /**
     * Render page header
     * @param $render_top_nav
     */
    function renderHeader($render_top_nav = true)
    {
        if (empty($_SESSION['user'])) {
            header("location: $this->root_path/signin/ ");
        }
        ?>
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <meta name="description" content="">
            <meta name="author" content="">
            <meta name="theme-color" content="#343a40"/>
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="theme-color" content="#f5f5f5">
            <link rel="icon" href="<?php echo $this->root_path; ?>/assets/brand/favicon.ico">

            <title><?php echo $this->page_title; ?></title>

            <!-- Manifest -->
            <link rel="manifest" href="<?php echo $this->root_path; ?>/manifest.json">

            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-status-bar-style" content="default">
            <meta name="apple-mobile-web-app-title" content="FAVR">

            <!-- iOS -->
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-640x1136.png"
                  media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-750x1294.png"
                  media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-1242x2148.png"
                  media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-1125x2436.png"
                  media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-1536x2048.png"
                  media="(min-device-width: 768px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-1668x2224.png"
                  media="(min-device-width: 834px) and (max-device-width: 834px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
            <link rel="apple-touch-startup-image"
                  href="<?php echo $this->root_path; ?>/assets/brand/splash/launch-2048x2732.png"
                  media="(min-device-width: 1024px) and (max-device-width: 1024px) and (-webkit-min-device-pixel-ratio: 2) and (orientation: portrait)">
            <link rel="apple-touch-icon" sizes="180x180"
                  href="<?php echo $this->root_path; ?>/assets/brand/apple-touch-icon.png">

            <link rel="icon" type="image/png" sizes="32x32"
                  href="<?php echo $this->root_path; ?>/assets/brand/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16"
                  href="<?php echo $this->root_path; ?>/assets/brand/favicon-16x16.png">

            <link rel="mask-icon" href="<?php echo $this->root_path; ?>/assets/brand/safari-pinned-tab.svg"
                  color="#343a40">


            <!-- Bootstrap core CSS -->
            <link rel="stylesheet" href="<?php echo $this->root_path; ?>/dist/css/bootstrap.min.css"/>

            <!-- Data tables CSS -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.10.18/css/dataTables.bootstrap4.min.css"/>

            <!-- Loader CSS -->
            <link rel="stylesheet" href="<?php echo $this->root_path; ?>/assets/css/loader.css"/>
            <!--                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"-->
            <!--                      integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm"-->
            <!--                      crossorigin="anonymous">-->

            <!-- Custom styles for this template -->
            <?php echo $this->stylesheet; ?>
            <link href="<?php echo $this->root_path; ?>/assets/css/main.css" rel="stylesheet">
        </head>

        <body class="bg-light" onload="pageLoader()">

        <div id="loader"></div>

        <?php
        $this->renderMainNavigation($this->page_id, $render_top_nav);
        ?>

        <main role="main" class="container animate-bottom">
        <?php
    }

    /**
     * Set permissions for the page. Will render Access Denied page and kill the page if needed.
     * @param $restrict_id integer The component to which access should be restricted.
     * @param $restrict_class integer The user class to which access should be restricted.
     * @param $restrict_user string Whether or not the page should be restricted to a certain user.
     */
    function setPermissions($restrict_id, $restrict_class, $restrict_user)
    {
        return;
    }


    /**
     * Render page main navigation
     * @param $page_id
     * @param $render_main_navigation
     */
    function renderMainNavigation($page_id, $render_main_navigation = true)
    {

        if ($render_main_navigation) {
            $active_home = "";
            $active_products = "";
            $active_reports = "";
            $active_settings = "";
            $active_tenants = "";
            $active_files = "";

            // Handle page navigation presentation logic
            switch ($page_id) {
                case 0:
                    $active_home = "active";
                    break;
                case 1:
                    $active_products = "active";
                    break;
                case 2:
                    $active_reports = "active";
                    break;
                case 3:
                    $active_settings = "active";
                    break;
                case 4:
                    $active_tenants = "active";
                    break;
                case 5:
                    $active_files = "active";
                default:
                    // none active
                    break;
            }
            ?>
            <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark">
                <div style="padding-top: 5px; padding-bottom: 5px">
                    <img src="<?php echo $this->root_path; ?>/assets/brand/favr_logo_rd.png" height="30" width="100"
                         class="navbar-brand" style="padding-top: 0; padding-bottom: 0">
                </div>

                <button class="request-favr p-0 border-0" type="button">
                    <a href="<?php echo $_SERVER['HTTP_HOST']; ?>/components/checkout/">
                        <i class="material-icons" style="color: red; font-size: xx-large">swap_vertical_circle</i>
                    </a>
                </button>

                <!--                <a class="navbar-brand" href="#">FAVR</a>-->
                <button class="navbar-toggler p-0 border-0" type="button" data-toggle="offcanvas">
                    <i class="material-icons" style="font-size: xx-large;color: var(--red)">menu</i>
                </button>

                <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item active">
                            <a class="nav-link d-inline-flex" href="#">
                                <i class="material-icons">shopping_cart</i>
                                Marketplace <span class="sr-only">(current)</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex" href="#">
                                <i class="material-icons">notifications_none</i>
                                Notifications
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex" href="#">
                                <i class="material-icons">person_outline</i>
                                Profile
                            </a>
                        </li>
                    </ul>
                    <!--                    <form class="form-inline my-2 my-lg-0">-->
                    <!--                        <input class="form-control mr-sm-2" type="text" placeholder="Search" aria-label="Search">-->
                    <!--                        <button class="btn btn-outline-danger my-2 my-sm-0" type="submit">Search</button>-->
                    <!--                    </form>-->
                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex" href="#">
                                <i class="material-icons">settings</i>
                                Settings
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/signin/?ALERT_MESSAGE=Signed off, comeback again :)">
                                <i class="material-icons">exit_to_app</i>
                                Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <div class="nav-scroller bg-white box-shadow">
                <nav class="nav nav-underline">
                    <a class="nav-link active" href="#">Marketplace</a>
                    <a class="nav-link" href="#">
                        Friends
                        <span class="badge badge-pill red-bubble-notification align-text-bottom">27</span>
                    </a>
                    <a class="nav-link" href="#">Explore</a>
                    <a class="nav-link" href="#">Suggestions</a>
                    <a class="nav-link" href="#">Link</a>
                    <a class="nav-link" href="#">Link</a>
                    <a class="nav-link" href="#">Link</a>
                    <a class="nav-link" href="#">Link</a>
                    <a class="nav-link" href="#">Link</a>
                </nav>
            </div>
            <?php
        }
    }

    /**
     * Render page footer at the end of the page
     */
    function renderFooter()
    {
        ?>
        </main>

        <!-- FOOTER -->
        <footer class="container" style="max-width: 90%">
            <hr>
            <p class="text-muted">&copy; 2018 Solken Technology, Inc</p>
        </footer>

        <!-- Loader -->
        <script>
            var timeOut;

            function pageLoader() {
                timeOut = setTimeout(showPage, 1500);
            }

            function showPage() {
                document.getElementById("loader").style.display = "none";
                // document.getElementById("myDiv").style.display = "block";
            }
        </script>

        <!-- Bootstrap core JavaScript
        ================================================== -->
        <!--                <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"-->
        <!--                        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"-->
        <!--                        crossorigin="anonymous"></script>-->

        <!-- Placed at the end of the document so the pages load faster -->
        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
                integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
                crossorigin="anonymous"></script>
        <!--                <script src="https://code.jquery.com/jquery-3.3.1.js"></script>-->
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/jquery-slim.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>
        <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?php echo $this->root_path; ?>/assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/popper.min.js"></script>
        <script src="<?php echo $this->root_path; ?>/dist/js/bootstrap.min.js"></script>
        <!--                <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"-->
        <!--                        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"-->
        <!--                        crossorigin="anonymous"></script>-->
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/holder.min.js"></script>

        <!-- Icons -->
        <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
        <script>
            feather.replace();

            $(function () {
                'use strict'

                $('[data-toggle="offcanvas"]').on('click', function () {
                    $('.offcanvas-collapse').toggleClass('open')
                })
            })
        </script>

        <!-- Page scripts -->
    <?php echo $this->script; ?>
        </body>
        </html>

        <?php
    }
}