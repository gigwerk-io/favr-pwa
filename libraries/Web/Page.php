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
     * String value to keep track of token response json
     * @var string
     */
    public $token_response_json;

    /**
     * Data source name
     * @var string
     */
    public $dsn = 'mysql:dbname=local_favr;host=favr.cgfeyejwt7qv.us-east-2.rds.amazonaws.com';

    /**
     * Backend username
     * @var string
     */
    public $username = 'Solomon04';

    /**
     * Backend password
     * @var string
     */
    public $password = 'Nomolos.99';

    /**
     * String value to keep track and validate product version
     * @var string
     */
    public $product_version = "0.1.1";

    /**
     * value to determine project root path
     * @var string
     */
    public $root_path = "https://askfavr.com/favr-pwa";

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

        $_SESSION['user'] = $user;
        $this->user = $user;

        // not permitted user
        if (empty($_SESSION['user']) && empty($this->user)) {
            $_SESSION['user_info'] = array(
                "id" => '-1'
            );
            header("Location: http://" . $_SERVER['HTTP_HOST'] . "/favr-pwa/signin/");
        } else {
            // permitted user
            if (empty($_SESSION['user_info'])) {
                $_SESSION['user_info'] = array(
                  "id" => '-1'
                );
            }

            // market filtering initial settings
            if (empty($_SESSION['filter_marketplace_by']) && empty($_GET['filter_marketplace_by'])) {
                $_SESSION['filter_marketplace_by'] = "task_date";
                $_GET['filter_marketplace_by'] = "task_date";
            } else {
                if (isset($_GET['filter_marketplace_by'])) {
                    $_SESSION['filter_marketplace_by'] = $_GET['filter_marketplace_by'];
                }
            }

            if (empty($_SESSION['orient_marketplace_by']) && empty($_GET['orient_marketplace_by'])) {
                $_SESSION['orient_marketplace_by'] = "DESC";
                $_GET['orient_marketplace_by'] = "DESC";
            } else {
                if (isset($_GET['orient_marketplace_by'])) {
                    $_SESSION['orient_marketplace_by'] = $_GET['orient_marketplace_by'];
                }
            }

//            if (empty($_SESSION['limit_marketplace_by'])) {
//                $_SESSION['limit_marketplace_by'] = "LIMIT 3";
//                $_GET['limit_marketplace_by'] = "LIMIT 3";
//            } else {
            if (isset($_GET['limit_marketplace_by'])) {
                $_SESSION['limit_marketplace_by'] = $_GET['limit_marketplace_by'];
            } else if (!isset($_SESSION['limit_marketplace_by'])) {
                $_SESSION['limit_marketplace_by'] = "LIMIT 3";
            }
//            }

            // navbar logic
            if (empty($_SESSION['navbar']) && empty($_GET['navbar'])) {
                $_SESSION['navbar'] = "active_home";
                $_GET['navbar'] = "active_home";
            } else {
                if (isset($_GET['navbar'])) {
                    $_SESSION['navbar'] = $_GET['navbar'];
                }
            }

            // nav scroller logic
            if (empty($_SESSION['nav_scroller']) && empty($_GET['nav_scroller'])) {
                $_SESSION['nav_scroller'] = "active_marketplace";
                $_GET['nav_scroller'] = "active_marketplace";
            } else {
                if (isset($_GET['nav_scroller'])) {
                    $_SESSION['nav_scroller'] = $_GET['nav_scroller'];
                }
            }

            // scope logic
            if (empty($_SESSION['scope']) && empty($_GET['scope'])) {
                $_SESSION['scope'] = "global";
                $_GET['scope'] = "global";
            } else {
                if (isset($_GET['scope'])) {
                    $_SESSION['scope'] = $_GET['scope'];
                }
            }

            // notification logic
            if (empty($_SESSION['main_notifications'])) {
                if ($_SESSION['user_info'] != null) {
                    $this->processNotifications($_SESSION['user_info']);
                }
            }
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
            $db = new PDO($this->dsn, $this->username, $this->password);
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
        // include our OAuth2 Server object
        // error reporting (this is a demo, after all!)
//        ini_set('display_errors',1);
//        error_reporting(E_ALL);
//
//        // Autoloading (composer is preferred, but for this example let's just do this)
//        require_once('../../oauth2-server-php/src/OAuth2/Autoloader.php');
//        OAuth2\Autoloader::register();
//
//        // $dsn is the Data Source Name for your database, for exmaple "mysql:dbname=my_oauth2_db;host=localhost"
//        $storage = new OAuth2\Storage\Pdo(array('dsn' => $this->dsn, 'username' => $this->username, 'password' => $this->password));
//
//        // Pass a storage object or array of storage objects to the OAuth2 server class
//        $server = new OAuth2\Server($storage);
//
//        // Add the "Client Credentials" grant type (it is the simplest of the grant types)
//        $server->addGrantType(new OAuth2\GrantType\ClientCredentials($storage));
//
//        // Add the "Authorization Code" grant type (this is where the oauth magic happens)
//        $server->addGrantType(new OAuth2\GrantType\AuthorizationCode($storage));
//
//        // Handle a request for an OAuth2.0 Access Token and send the response to the client
//        $this->token_response_json = $server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();

        $select_sign_in_query = "SELECT * 
                                 FROM users
                                 WHERE email='$signInUsernameEmail'
                                 OR username='$signInUsernameEmail'
                                 AND password='$signInPass'";

        $sign_in_result = $this->db->query($select_sign_in_query);
        $sign_in_row = $sign_in_result->fetch(PDO::FETCH_ASSOC);

        if (!empty($sign_in_row)) {
            // successful sign in
            $this->user = $sign_in_row;
            $_SESSION['user_info'] = $sign_in_row;

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
     * TODO: implement set permissions to lock certain aspects and functionality of FAVR to certain users only
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
     * Get user info by id
     *
     * @param $userID
     *
     * @return array // return array of userInfo if user exists NULL otherwise
     */
    function getUserInfo($userID)
    {
        $user_query = "SELECT * 
                       FROM users
                       WHERE id = '$userID'";
        $result = $this->db->query($user_query);
        $row = $result->fetch(PDO::FETCH_ASSOC);

        return $row;
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
            <!--            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
            <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
            <meta name="HandheldFriendly" content="true" />
            <meta name="description" content="Post job requests at your price and have access to verified freelancers for open tasks. Chat with friends and trade FAVRs.">
            <meta name="author" content="Solken Technoloy LLC: Solomon Antoione, Haron Arama, D'Angelo Tines, and Ken Nguyen">
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

        <main role="main" class="container animate-bottom" style="max-width: 750px">
        <?php
    }

    /**
     * Render marketplace history for this specific user
     *
     * @param $id
     * @param $orderBy
     * @param $orientation
     *
     * @return boolean
     */
    function renderFavrProfileHistory($id, $orderBy = "task_date", $orientation = "DESC", $limit="")
    {
        if ($id == $_SESSION['user_info']['id']) {
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN users u
                                   WHERE u.id = mfr.customer_id
                                   AND u.id = $id
                                   OR u.id = mfr.freelancer_id
                                   AND u.id = $id
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";

        } else {
            $selectMarketplaceQuery = "";
        }

        $result = $this->db->query($selectMarketplaceQuery);

        if (!$result) {
            // failed to render marketplace
            echo "Something went wrong! :(";
            return false;
        } else {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $freelancer_id = $row['freelancer_id'];
                    $task_id = $row['mfrid'];
                    $customer_id = $row['customer_id'];
                    $customer = $this->getUserInfo($customer_id);
                    $customer_username = $customer['username'];
                    $customer_first_name = $customer['first_name'];
                    $task_description = $row['task_description'];
                    $task_date = date("m/d/Y", strtotime($row['task_date']));
                    $task_location = $row['task_location'];
                    $task_time_to_accomplish = $row['task_time_to_accomplish'];
                    $task_price = $row['task_price'];
                    $task_status = $row['task_status'];

                    echo "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
                        <div class='pb-2 mb-0 border-bottom border-gray'>
                            <img data-src=\"holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1\" alt=\"\" class=\"mr-2 rounded\">
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">@$customer_username</strong>
                            ";

                    if ($freelancer_id == $id) {
                        echo "<div class='float-right small' style='color: var(--green)'>+ $$task_price</div>";
                    } else if ($customer_id == $id) {
                        echo "<div class='float-right small' style='color: var(--red)'>- $$task_price</div>";
                    }

                    echo "</div>
                        <div class=\"media text-muted pt-3\">
                            <div class='container'>
                                <p class=\"media-body text-dark pb-3 mb-0 small lh-125\">
                                    $task_description
                                </p>
                                <div class='row p-0 border-top border-gray'>
                                    <div class='col-sm-12 small'>
                                        <div class=\"float-left d-inline\">
                                            ";

                    if ($freelancer_id == $id && $task_status == "Requested") { // if not this user
                        echo "<p class='mb-0 d-inline-flex'>Accepted(Freelancer)</p>";
                    } else if ($freelancer_id == $id && $task_status == "In Progress") { // if not this user
                        echo "<p class='mb-0 d-inline-flex'>In Progress(Freelancer)</p>";
                    } else if ($freelancer_id == $id && $task_status == "Completed") { // if not this user
                        echo "<p class='mb-0 d-inline-flex'>You Completed</p>";
                    } else if ($customer_id == $id && $task_status == "Requested") { // if not this user
                        echo "<p class='mb-0 d-inline-flex'>Requested</p> |";
                        echo "<a href=\"?navbar=active_profile&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                            Cancel Request</a>";
                    } else if ($customer_id == $id && $task_status == "In Progress") { // if not this user
                        echo "<p class='mb-0 d-inline-flex'>In Progress</p> |";
                        echo "<a href=\"?navbar=active_profile&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                            Cancel Request</a>";
                    } else {
                        echo "<p class='mb-0 d-inline-flex'>Completed</p>";
                    }

                    echo "
                                    </div>
                                    <div class='float-right d-inline'>
                                        $task_date
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                    echo "</div>";

                }
            } else {
                echo "<p class='p-3 text-muted'>You don't have a FAVR history! <a href='$this->root_path/home/?navbar=active_home&nav_scroller=active_marketplace'>Go to marketplace</a> and request FAVRs :)</p>";
                return false;
            }

            return true;
        }
    }

    /**
     * Render profile from userID
     *
     * @param $userID
     *
     * @return boolean
     *
     * @TODO implement this function as a universal solution to rendering profiles
     * @TODO allow for image file upload but store image files in file system outside of document root
     *
     */
    function renderFavrProfile($userID) {
        return null;
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
            $active_categories = "";
            $active_notifications = "";
            $active_search = "";
            $active_profile = "";
            $active_settings = "";

            // Handle page navigation presentation logic
            switch ($_SESSION['navbar']) {
                case "active_home":
                    $active_home = "active";
                    break;
                case "active_categories":
                    $active_categories = "active";
                    break;
                case "active_notifications":
                    $active_notifications = "active";
                    break;
                case "active_profile":
                    $active_profile = "active";
                    break;
                case "active_search":
                    $active_search = "active";
                    break;
                case "active_settings":
                    $active_settings = "active";
                    break;
                default:
                    // none active
                    break;
            }
            ?>
            <nav class="navbar navbar-expand-md fixed-top navbar-dark bg-dark pb-2">
                <button class="navbar-toggler pb-2 border-0" type="button" data-toggle="offcanvas">
                    <!--                    <i class="material-icons" style="font-size: xx-large;color: var(--red)">menu</i>-->
                    <span class="sr-only">Toggle navigation</span>
                    <span></span>
                    <span></span>
                    <span></span>
                </button>

                <div class="request-favr pt-0 pr-0 pb-0 mr-0">
                    <?php
                    if ($_SESSION['nav_scroller'] != "active_marketplace" || $_SESSION['navbar'] != "active_home") {
                        echo "
                            <a href=\"$this->root_path/home/?navbar=active_home&nav_scroller=active_marketplace\">
                        ";
                    }
                    ?>
                    <img src="<?php echo $this->root_path; ?>/assets/brand/favr_logo_rd.png" height="21" width="70"
                         class="navbar-brand mr-0" style="padding-top: 0; padding-bottom: 0">

                    <?php
                    if ($_SESSION['nav_scroller'] != "active_marketplace" || $_SESSION['navbar'] != "active_home") {
                        echo "
                            </a>
                        ";
                    }
                    ?>
                </div>

                <button class="profile-button pb-0 border-0 mr-0 pr-0" style="left: .1rem" type="button">
                    <?php
                    if ($_SESSION['navbar'] == "active_profile") {
                        echo "
                               <i class=\"material-icons\" style=\"color: red;border: 1px solid;border-radius: 1rem;\">person</i>
                            ";
                    } else {
                        echo "
                                  <a href='$this->root_path/components/profile/?navbar=active_profile'>
                                     <i class=\"material-icons\" style=\"color: red;border: 1px solid;border-radius: 1rem;\">person_outline</i>
                                  </a>";
                    }
                    ?>
                </button>

                <!--                <a class="navbar-brand" href="#">FAVR</a>-->


                <div class="navbar-collapse offcanvas-collapse" id="navbarsExampleDefault">
                    <ul class="navbar-nav mr-auto">
                        <li class="nav-item <?php echo $active_home; ?>">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/home/?navbar=active_home&nav_scroller=active_marketplace">
                                <i class="material-icons">home</i>
                                Home
                                <?php
                                if (!empty($active_home)) {
                                    echo "<span class=\"sr-only\">(current)</span>";
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $active_categories; ?>">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/components/categories/?navbar=active_categories">
                                <i class="material-icons">layers</i>
                                Categories
                            </a>
                        </li>
                        <li class="nav-item <?php echo $active_notifications; ?>">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/components/notifications/?navbar=active_notifications">
                                <?php
                                if (!empty($active_notifications)) {
                                    echo "<i class=\"material-icons\">notifications</i>";
                                } else {
                                    echo "<i class=\"material-icons\">notifications_none</i>";
                                }
                                ?>
                                Notifications
                                <?php
                                $notificationCount = $this->processNotifications($_SESSION['user_info']);

                                $this->renderNotificationCount($notificationCount);

                                if (!empty($active_notifications)) {
                                    echo "<span class=\"sr-only\">(current)</span>";
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item <?php echo $active_profile; ?>">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/components/profile/?navbar=active_profile">
                                <?php
                                if (!empty($active_profile)) {
                                    echo "<i class=\"material-icons\">person</i>";
                                } else {
                                    echo "<i class=\"material-icons\">person_outline</i>";
                                }
                                ?>
                                Profile, Welcome: <?php echo $_SESSION['user_info']['first_name']; ?>
                                <?php
                                if (!empty($active_profile)) {
                                    echo "<span class=\"sr-only\">(current)</span>";
                                }
                                ?>
                            </a>
                        </li>
                        <li class="mobile-search nav-item <?php echo $active_search; ?>">
                            <a class="nav-link d-inline-flex" href="#">
                                <i class="material-icons">search</i>
                                Search
                                <?php
                                if (!empty($active_search)) {
                                    echo "<span class=\"sr-only\">(current)</span>";
                                }
                                ?>
                            </a>
                        </li>
                    </ul>

                    <!-- WEB ELEMENT ONLY -->
                    <form class="web-search form-inline my-2 my-lg-0">
                        <input style="border-radius: 5px 0 0 5px" class="form-control mr-sm-0" type="text" placeholder="Search" aria-label="Search">
                        <button style="border-radius: 0 5px 5px 0" class="btn btn-outline-danger my-2 my-sm-0" type="submit">Search</button>
                    </form>
                    <!-- WEB ELEMENT ONLY -->

                    <ul class="navbar-nav ml-auto">
                        <li class="nav-item <?php echo $active_settings; ?>">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/components/settings/?navbar=active_settings">
                                <i class="material-icons">settings</i>
                                Settings
                                <?php
                                if (!empty($active_settings)) {
                                    echo "<span class=\"sr-only\">(current)</span>";
                                }
                                ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link d-inline-flex" href="<?php echo $this->root_path; ?>/signin/?signout=true">
                                <i class="material-icons">exit_to_app</i>
                                Sign out
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <?php
            if ($_SESSION['navbar'] == "active_home" || $_GET['navbar'] == "active_home") {
                // Handle nav scroller presentation logic
                $active_marketplace = "";
                $active_friends = "";
                $active_chat = "";

                switch ($_SESSION['nav_scroller']) {
                    case "active_marketplace":
                        $active_marketplace = "active";
                        break;
                    case "active_friends":
                        $active_friends = "active";
                        break;
                    case "active_chat":
                        $active_chat = "active";
                        break;
                    default:
                        // none active
                        break;
                }
                ?>
                <div class="nav-scroller bg-white box-shadow">
                    <nav class="nav nav-underline">
                        <a class="nav-link <?php echo $active_marketplace; ?>"
                           href="<?php echo $this->root_path; ?>/home/?navbar=active_home&nav_scroller=active_marketplace">
                            Marketplace
                            <?php
                            if ($active_marketplace) {
                                echo "<i class=\"material-icons\" style='color: var(--red);font-size: 15px;position:relative;top:.2rem;padding-left: 2px;'>store</i>";
                            } else {
                                echo "<i class=\"material-icons\" style='font-size: 15px;position:relative;top:.2rem;padding-left: 2px;'>store</i>";
                            }
                            ?>
                        </a>
                        <a class="nav-link <?php echo $active_friends; ?>"
                           href="<?php echo $this->root_path; ?>/home/friends/?navbar=active_home&nav_scroller=active_friends">
                            Friends
                            <?php
                            if ($active_friends) {
                                echo "<i class=\"material-icons\" style='color: var(--red);font-size: 15px; padding-left: 2px;position:relative;top:.1rem;'>people</i>";
                            } else {
                                echo "<i class=\"material-icons\" style='font-size: 15px; padding-left: 2px;position:relative;top:.1rem;'>people_outline</i>";
                            }
                            ?>
                        </a>
                        <a class="nav-link d-inline-flex <?php echo $active_chat; ?>"
                           href="<?php echo $this->root_path; ?>/home/chat/?navbar=active_home&nav_scroller=active_chat">
                            Chat
                            <?php
                            if ($active_chat) {
                                echo "<i class=\"material-icons\" style='color: var(--red);font-size: 15px; padding-left: 2px;position:relative;top:.2rem;'>chat_bubble</i>";
                            } else {
                                echo "<i class=\"material-icons\" style='font-size: 15px; padding-left: 2px;position:relative;top:.2rem;'>chat_bubble_outline</i>";
                            }
                            ?>
                        </a>
                        <!--                    <a id="suggestions" onclick="focusNoScrollMethod()" class="nav-link -->
                        <?php //echo  $active_suggestions; ?><!--" href="?nav_scroller=active_suggestions">Suggestions</a>-->
                    </nav>
                </div>
                <?php
            }
        }

    }

    /**
     * Render notification count
     *
     * @param $notificationCount
     *
     * @return boolean // true if there's notifications false otherwise
     */
    function renderNotificationCount($notificationCount)
    {
        if ($notificationCount > 0) {

            echo "<span style=\"height: 1rem\" class=\"badge badge-pill red-bubble-notification align-text-bottom\">$notificationCount</span>";

            return true;
        } else {
            return false;
        }
    }

    /**
     * Render main notifications
     *
     * @param $userInfo
     *
     * @return boolean
     */
    function renderMainNotifications($userInfo)
    {
        $userID = $userInfo['id'];

        $notifications_query = "SELECT *
                                FROM marketplace_favr_requests mfr 
                                WHERE mfr.customer_id = '$userID' 
                                AND mfr.freelancer_id IS NOT NULL
                                AND NOT mfr.task_status = 'Completed'
                                OR mfr.freelancer_id = '$userID' 
                                AND NOT mfr.task_status = 'Completed'
                                ";

        $result = $this->db->query($notifications_query);
        $rows = $result->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            // There are results

            foreach ($rows as $row) {
                $freelancer_id = $row['freelancer_id'];
                $freelancer = $this->getUserInfo($freelancer_id);

                $customer_id = $row['customer_id'];
                $customer = $this->getUserInfo($customer_id);

                $task_id = $row['id'];
                $task_description = $row['task_description'];
                $task_date = date("m/d/Y", strtotime($row['task_date']));
                $task_location = $row['task_location'];
                $task_time_to_accomplish = $row['task_time_to_accomplish'];
                $task_price = $row['task_price'];
                $task_status = $row['task_status'];

                echo "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
                            <div class='pb-2 mb-0 border-bottom border-gray'>
                                <img data-src=\"holder.js/32x32?theme=thumb&bg=007bff&fg=007bff&size=1\" alt=\"\" class=\"mr-2 rounded\">
                                <strong style='font-size: 80%' class=\"d - block text - gray - dark\">@". $customer['username'] ."</strong>
                                ";

                if ($freelancer_id == $userID) {
                    echo "<div class='float-right small' style='color: var(--green)'>+ $$task_price</div>";
                } else if ($customer_id == $userID) {
                    echo "<div class='float-right small' style='color: var(--red)'>- $$task_price</div>";
                }

                echo "</div>
                        <div class=\"media text-muted pt-3\">
                            <div class='container'>
                                <p class=\"media-body pb-3 mb-0 small lh-125 text-dark\">
                                    <div class='small'>Task accepted by ". $freelancer['first_name'] ."</div>
                                    <br>
                                    $task_description
                                </p>
                                <div class='row p-0 border-top border-gray'>
                                    <div class='col-sm-12 small'>
                                        <div class=\"float-left d-inline\">
                                            <a href=\"?navbar=active_notifications&completed_request_id=$task_id&freelancer_id=$freelancer_id&customer_id=$customer_id&ALERT_MESSAGE=The FAVR has been completed and payment is now in the process of disbursal!\">
                                                Job is Done</a> | 
                                            <a href='#' onclick='alert(\"Cancellation of an already accepted FAVR will cause a $5 fee to be charged to you! Are you sure you wish to proceed?\")' class='text-danger'>
                                                Cancel Request</a>
                                        </div>
                                        <div class='float-right d-inline'>
                                            $task_date
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>";
                echo "</div>";
            }

            return true;
        } else {

            echo "<p class='p-3 text-muted'>No notifications at the moment!</p>";

            return false;
        }
    }

    /**
     * Render request favr web and mobile form
     *
     * @param $render_favr_request_form
     *
     * @return boolean
     */
    function renderFavrRequestForm($render_favr_request_form = true)
    {
        if ($render_favr_request_form) {
            ?>
            <div class="p-3 text-center request-favr-web">
                <button class="btn btn-lg btn-primary" id="request-favr-web">
                    <div class="d-inline-flex">
                        <i class="material-icons">create</i>
                        Request FAVR
                    </div>
                </button>
            </div>

            <form class="request-favr-mobile" action="" method="post" enctype="multipart/form-data">
                <div class="my-3 p-3 bg-white rounded box-shadow">
                    <h6 class="border-bottom border-gray pb-2 mb-0">Post a FAVR request in Marketplace</h6>
                    <div class="media text-muted pt-3">
                        <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                             alt="profile picture"
                             height="32"
                             width="32"
                             class="mr-2 rounded">
                        <div class="media-body pb-3 mb-0 small lh-125">
                            <strong class="d-block text-gray-dark">@<?php echo $_SESSION['user_info']['username']; ?></strong>
                            <div class="form-label-group">
                                <textarea name="requestTaskDescription" class="form-control" placeholder="What is your task?"></textarea>
                            </div>
                            <div class="form-label-group">
                                <input type="number" pattern="\d*" step="1" min="1" max="5" name="requestFreelancerCount" id="inputCount"
                                       class="form-control"
                                       placeholder="How many people do you need?" value="1" required="">
                                <label for="inputCount">How many people do you need?</label>
                            </div>
                            <div class="form-label-group">
                                <input type="datetime-local" name="requestDate" id="inputDate"
                                       class="form-control"
                                       placeholder="When do you want your FAVR done by?" value="<?php echo date("Y-m-d\TH:i", time()); ?>" required="">
                                <label for="inputDate">When do you want your FAVR?</label>
                            </div>

<!--                            TODO: Add informational popup telling the user we won't share sensitive information until freelancer accepts request -->

                            <div class="form-label-group">
                                <input type="text" name="requestStreetAddress" id="inputStreetAddress"
                                       class="form-control"
                                       placeholder="What's your street address?"
                                       value="<?php echo $_SESSION['user_info']['street'] . ", " . $_SESSION['user_info']['city'] . ", " . $_SESSION['user_info']['state_province'] . ", " . $_SESSION['user_info']['zip']; ?>"
                                       required="">
                                <label for="inputStreetAddress">What's your street address?</label>
                            </div>
                            <label for="inputCategory">What category do you want your FAVR listed?</label>
                            <div class="form-label-group">
                                <select name="requestCategory" id="inputCategory"
                                        class="form-control"
                                        required="">
                                    <option value="General Request" selected>General Request</option>
                                    <option value="Home Improvement">Home Improvement</option>
                                    <option value="Yard Work">Yard Work</option>
                                </select>
                            </div>
                            <label for="inputDifficulty">What difficulty is the task?</label>
                            <div class="form-label-group">
                                <button id="easy-button" type="button" class="btn btn-success p-2 rounded" value="Easy">Easy 👌</button>
                                <button id="medium-button" type="button" class="btn btn-warning p-2 rounded" value="Medium">Medium 💪🏿</button>
                                <button id="hard-button" type="button" class="btn btn-danger p-2 rounded" value="Hard">Hard 🔥</button>
                                <input id="difficulty" type="hidden" name="requestDifficulty">
                            </div>
                            <div class="input-group pb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="color: var(--green)">$</span>
                                </div>
                                <input type="number" name="requestPrice" id="inputPricing"
                                       class="form-control"
                                       style="border-radius: 0 5px 5px 0"
                                       placeholder="Set your price ..." min="0.50" max="250.00" step="0.01" required="">
<!--                                <label for="inputPricing">Set your price... </label>-->
                            </div>
                            <label for="inputPictures">Only image files < 5 Mb can be attached</label>
                            <div class="form-label-group">
                                <input type="file" name="requestPictures[]" id="inputPictures"
                                       class="form-control"
                                       placeholder="Attach picture(s)" multiple>
                                <label for="inputPictures">Attach picture(s): at most 3...</label>
                            </div>
                            <input type="submit" name="requestFavr" class="btn btn-lg btn-primary btn-block"
                                   value="Request FAVR" onclick="alert("This will be posted publically")">
                        </div>
                    </div>
                </div>
            </form>
            <?php
        }

        return $render_favr_request_form;
    }

    /**
     * Render marketplace favr request feed to home
     *
     * @param $scope
     * @param $orderBy
     * @param $orientation
     *
     * @return boolean
     */
    function renderFavrMarketplace($scope="global", $orderBy = "task_date", $orientation = "DESC", $limit="LIMIT 3")
    {
        if ($scope == $_SESSION['user_info']['id']) {
            // TODO: constant Requested used statically here
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN users u
                                   WHERE u.id = $scope
                                   AND u.id = mfr.customer_id
                                   AND mfr.task_status = 'Requested'
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";

        } else if ($scope == "global") {
            // TODO: constant Requested used statically here
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN users u
                                   WHERE u.id = mfr.customer_id
                                   AND mfr.task_status = 'Requested'
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";
        } else {
            $selectMarketplaceQuery = "";
        }

        $result = $this->db->query($selectMarketplaceQuery);

        if (!$result) {
            // failed to render marketplace
            return false;
        } else {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                foreach ($rows as $row) {
                    $id = md5($row['mfrid']);
                    $task_id = $row['mfrid'];
                    $freelancer_ids_array = unserialize($row['freelancer_id']);
                    $freelancer_accepted = count($freelancer_ids_array);
                    $task_freelancer_count = $row['task_freelancer_count'];
                    $customer_id = $row['customer_id'];
                    $customer_username = $row['username'];
                    $customer_first_name = $row['first_name'];
                    $task_description = $row['task_description'];
                    $task_date = date("m/d/Y", strtotime($row['task_date']));
                    $task_location = $row['task_location'];
                    $task_time_to_accomplish = date('h:i A, l, m/d/Y', strtotime($task_date));
                    $task_price = $row['task_price'];
                    $task_difficulty = $row['task_intensity'];

                    $task1_img_data_array = unserialize($row['task_picture_path_1']);
                    $task1_img_name = $task1_img_data_array['name'];
                    $task1_img_type = $task1_img_data_array['type'];

                    $task2_img_data_array = unserialize($row['task_picture_path_2']);
                    $task2_img_name = $task2_img_data_array['name'];
                    $task2_img_type = $task2_img_data_array['type'];

                    $task3_img_data_array = unserialize($row['task_picture_path_3']);
                    $task3_img_name = $task3_img_data_array['name'];
                    $task3_img_type = $task3_img_data_array['type'];

                    // hide shrink button and non essential form information

                    echo "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
                        <div class='pb-2 mb-0 border-bottom border-gray'>
                            <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                height='32' width='32' alt=\"\" class=\"mr-2 rounded\">
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                @$customer_username
                            </strong>
                            ";

                    // TODO: reference enum(Hard, Medium, Easy) as constants from Constants class
                    if (isset($task_difficulty)) {
                        if ($task_difficulty == "Easy") {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-success p-1 rounded\" style='opacity: .9' value=\"Easy\" disabled>Easy 👌</button>";
                        } else if ($task_difficulty == "Medium") {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-warning p-1 rounded\" style='opacity: .9' value=\"Medium\" disabled>Medium 💪🏿</button>";
                        } else if ($task_difficulty == "Hard") {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-danger p-1 rounded\" style='opacity: .9' value=\"Hard\" disabled>Hard 🔥</button>";
                        }
                    }

                    if ($customer_id == $_SESSION['user_info']['id']) {
                        echo "<div class='float-right small font-weight-bold' style='padding-top: .3rem;color: var(--red)'>- $$task_price</div>";
                    } else {
                        echo "<div class='float-right small font-weight-bold' style='padding-top: .3rem;color: var(--dark)'>$$task_price</div>";
                    }

                    echo "</div><div class=\"media text-muted pt-3\">
                        <div class='container'>
                            <p id='$id' class=\"media-body text-dark mb-0 small lh-125\">
                                $task_description
                                <div id='$id-location' class='pt-1 border-top small border-gray d-none'>
                                    <label for='location'>Location:</label>
                                    <!-- TODO: calculate location distance by zipcode -->
                                    <p class='text-dark'>Within 3 Miles of your location.</p>
                                    <div id='$id-completeby' class='pt-1 border-top border-bottom border-gray'>
                                        <label for='completeby'>Complete FAVR by:</label>
                                        <p class='text-dark'>$task_time_to_accomplish</p>
                                    </div>
                                </div>
                                <div id='$id-freelancer-count' class='pt-1 small d-none'>
                                    <label for='freelancer-count'>Amount of freelancer(s) wanted: (accepted/requested)</label>
                                    <p class='text-dark'>$freelancer_accepted/$task_freelancer_count</p>
                                </div>";
                    echo "
                                <div id='$id-image1' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image1'>Attached Image 1:</label>
                                    <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task1_img_name&i_t=$task1_img_type' height='30%' width='30%'>
                                </div>";
                    echo "
                                <div id='$id-image2' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image1'>Attached Image 2:</label>
                                    <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task2_img_name&i_t=$task2_img_type' height='30%' width='30%'>
                                </div>";
                    echo "
                                <div id='$id-image3' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image1'>Attached Image 3:</label>
                                    <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                    data-src='$this->root_path/image.php?i=$task3_img_name&i_t=$task3_img_type' height='30%' width='30%'>
                                </div>";

                    echo "
                            </p>
                            <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                        <div id='$id-expand' class='text-info d-inline-flex'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('.zoom').fadeOut();
                                              $('#$id').animate({height: '4rem'});
                                              $('#$id-location').removeClass('d-none');
                                              $('#$id-freelancer-count').removeClass('d-none');
                                              $('#$id-collapse').removeClass('d-none');
                                              $('#$id-collapse').addClass('d-inline-flex');
                                              $('#$id-expand').removeClass('d-inline-flex');
                                              $('#$id-expand').addClass('d-none');";

                    if (!empty($task1_img_data_array)) {
                        echo "$('#$id-image1').removeClass('d-none');";
                    }

                    if (!empty($task2_img_data_array)) {
                        echo "$('#$id-image2').removeClass('d-none');";
                    }

                    if (!empty($task3_img_data_array)) {
                        echo "$('#$id-image3').removeClass('d-none');";
                    }

                    echo "
                                              
                                        \">Details</div>
                                         <div id='$id-collapse' class='text-info d-none'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('#$id-location').addClass('d-none');
                                              $('#$id-freelancer-count').addClass('d-none');
                                              $('#$id').css({height: 'auto'});
                                              $('#$id-collapse').removeClass('d-inline-flex');
                                              $('#$id-collapse').addClass('d-none');
                                              $('#$id-expand').removeClass('d-none');
                                              $('#$id-expand').addClass('d-inline-flex');";

                    if (!empty($task1_img_data_array)) {
                        echo "$('#$id-image1').addClass('d-none');";
                    }

                    if (!empty($task2_img_data_array)) {
                        echo "$('#$id-image2').addClass('d-none');";
                    }

                    if (!empty($task3_img_data_array)) {
                        echo "$('#$id-image3').addClass('d-none');";
                    }

                    echo "
                                              
                                              $('.zoom').css({display: ''})
                                        \">Collapse</div> | $task_date
                                        ";

                    echo "
                                    </div>
                                    <div class='float-right d-inline'>
                                       ";

                    if ($customer_id != $_SESSION['user_info']['id']) { // if not this user
                        $freelancerAccepted = false;
                        $freelancer_id = null;
                        foreach ($freelancer_ids_array as $freelancerID) {
                            if ($_SESSION['user_info']['id'] == $freelancerID) {
                                $freelancerAccepted = true;
                                $freelancer_id = $freelancerID;
                                break;
                            }
                        }

                        if ($freelancerAccepted) {
                            echo "<a class='text-danger' href='$this->root_path/components/notifications/?navbar=active_notifications&withdraw_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE='>
                                Withdraw</a>";
                        } else {
                            echo "<a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_request_id=$task_id&ALERT_MESSAGE=You've signed up to take this task! The task requester has been notified of your interest and is reviewing your offer to help: they can accept or reject your offer to help! You'll be notified of their decision; you can withdraw your offer to help before they decide. \">
                                Accept Request</a>";
                        }
                    } else {
                        echo "<a href=\"?nav_bar=active_home&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                            Cancel Request</a>";
                    }

                    echo "
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                    echo "</div>";

                }
            } else {
                echo "<p class='p-3 text-muted'>No FAVR requests at the moment!</p>";
                return false;
            }

            return true;
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
        <footer class="container" style="position: relative; float: bottom; max-width: 90%">
            <hr>
            <div class="row">
                <p class="col-md-10 text-muted">&copy; 2018 Solken Technology, Inc</p>
                <p class="col-md-2 float-right text-muted">v<?php echo $this->product_version; ?> Beta</p>
            </div>
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
        <!--        <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"-->
        <!--                integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"-->
        <!--                crossorigin="anonymous"></script>-->
        <!--        <script src="https://cdn.datatables.net/1.10.18/js/jquery.dataTables.min.js"></script>-->
        <!--        <script src="https://cdn.datatables.net/1.10.18/js/dataTables.bootstrap4.min.js"></script>-->
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/jquery.min.js"></script>
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/popper.min.js"></script>
        <script src="<?php echo $this->root_path; ?>/dist/js/bootstrap.min.js"></script>
        <!--                <script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js"-->
        <!--                        integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb"-->
        <!--                        crossorigin="anonymous"></script>-->
        <script src="<?php echo $this->root_path; ?>/assets/js/vendor/holder.min.js"></script>

        <!-- Service Worker -->
        <script src="<?php echo $this->root_path; ?>/assets/js/src/pwa.js"></script>

        <!-- Icons -->
        <script src="https://unpkg.com/feather-icons/dist/feather.min.js"></script>
        <script>
            // feather.replace();

            focusNoScrollMethod = function getFocusWithoutScrolling() {
                document.getElementById("suggestions").focus({preventScroll:true});
            }

            function openRequestFromMobile() {
                $('.request-favr-mobile').show();
            }

            $(function () {
                'use strict'

                $('[data-toggle="offcanvas"]').on('click', function () {
                    $('.offcanvas-collapse').toggleClass('open');
                    $('.navbar-toggler').toggleClass('close')
                });
            })
        </script>

        <!-- Page scripts -->
    <?php echo $this->script; ?>
        </body>
        </html>

        <?php
    }

    /**
     * Process favr request from form and serve into database to display in marketplace
     *
     * @param $userInfo // array with user details such as location
     * @param $inputDate
     * @param $inputCategory
     * @param $inputTaskDetails
     * @param $inputFreelancerCount
     * @param $inputPricing
     * @param $inputLocation
     * @param $inputDifficulty
     * @param $inputPictures
     * @param $inputScope // pre-alpha scope is public
     *
     * @return boolean // successful process or not print error
     *
     */
    function processFavrRequestToDB($userInfo, $inputDate, $inputCategory, $inputTaskDetails, $inputPricing, $inputFreelancerCount, $inputLocation, $inputDifficulty,  $inputPictures, $inputScope="public")
    {
        if (isset($userInfo, $inputDate, $inputFreelancerCount, $inputCategory, $inputTaskDetails, $inputPricing, $inputScope)) {
            $userId = $userInfo['id'];
            $address = $inputLocation;

            $insert_request_query = "INSERT INTO `marketplace_favr_requests`
                                  (
                                    `customer_id`,
                                    `task_description`,
                                    `task_date`,
                                    `task_freelancer_count`,
                                    `task_location`,
                                    `task_category`,
                                    `task_intensity`,
                                    `task_price`
                                  )
                              VALUES
                                  (
                                    '$userId',
                                    '$inputTaskDetails',
                                    '$inputDate',
                                    '$inputFreelancerCount',
                                    '$address',
                                    '$inputCategory',
                                    '$inputDifficulty',
                                    '$inputPricing'
                                  )
            ";

            $result = $this->db->query($insert_request_query);
//            $result = true; // for testing

            if ($result) {
                // process attached images logic

                // TODO: process images into the server and backend
                if (isset($inputPictures)) {
                    $select_requested_task_query = "SELECT id 
                                                  FROM marketplace_favr_requests
                                                  WHERE customer_id = '$userId'
                                                  AND task_date = '$inputDate'
                                                  AND task_description = '$inputTaskDetails'
                                                  AND task_location = '$address'
                                                  AND task_category = '$inputCategory'
                                                  AND task_status = 'Requested'
                                                  AND task_intensity = '$inputDifficulty'
                                                  AND task_price = '$inputPricing'
                                                  AND task_freelancer_count = '$inputFreelancerCount'";

                    $result = $this->db->query($select_requested_task_query);
                    if ($result) {
                        $row = $result->fetch(PDO::FETCH_ASSOC);
                        $task_id = $row['id'];
                        // TODO: maximum images allowed constant statically used

                        for ($i = 0; $i < 3; $i++) {
                            // check if atleast one image is set
                            if (!empty($inputPictures['name'][$i])) {
                                // validate file is an image of type jpeg or png
                                if ($inputPictures['type'][$i] == "image/jpeg" || $inputPictures['type'][$i] == "image/png") {
                                    // continue validating file size is below allowed size of 5 MB
                                    // TODO: file size allowed should be a constant set in a "constants" class
                                    if ($inputPictures['size'][$i] <=  5242880) {
                                        // validate temp_name exists
                                        if (!empty($inputPictures['tmp_name'][$i])) {
                                            // validate no file errors were detected
                                            if ($inputPictures['error'][$i] == 0) {
                                                // hash the image file with the following convention: $task_id-$customer_id-request-image$i
                                                $imageFileName = md5("$task_id-$userId-request-image$i");
                                                if ($inputPictures['type'][$i] == 'image/jpeg') {
                                                    $imageFileName = "$imageFileName.jpeg";
                                                } else if ($inputPictures['type'][$i] == 'image/png') {
                                                    $imageFileName = "$imageFileName.png";
                                                }
                                                $imageFileType = $inputPictures['type'][$i];

                                                // TODO: constant values statically used root image file path
                                                $imageFilePath = "/Applications/XAMPP/xamppfiles/favr-request-images/$imageFileName";

                                                // copy image into backend and execute update query on request to update file paths
                                                // TODO: constant values statically used image file paths
                                                if (copy($inputPictures['tmp_name'][$i], $imageFilePath)) {
    //                                                    echo "<pre>";
    //                                                    die(print_r($inputPictures));
                                                    // file copied to destination
                                                    $x = $i + 1;
                                                    $task_picture_path_x = "task_picture_path_$x";
                                                    $imageDataArray = array('name' => "$imageFileName",
                                                                            'type' => "$imageFileType",
                                                                            'size' => $inputPictures['size'][$i],
                                                                            'task_id' => $task_id);

                                                    $imageDataArray = serialize($imageDataArray);

                                                    $update_path_query = "UPDATE marketplace_favr_requests 
                                                                          SET $task_picture_path_x = '$imageDataArray'
                                                                          WHERE id = '$task_id'";

                                                    $result = $this->db->query($update_path_query);
                                                    if (!$result) {
                                                        // unsuccessful
                                                        return false;
                                                    }
                                                } else {
                                                    // failed to copy file to destination
                                                    return false;
                                                }
                                            } else {
                                                // throw an error
                                                return false;
                                            }
                                        } else {
                                            // throw an error
                                            return false;
                                        }
                                    } else {
                                        // throw an error
                                        return false;
                                    }

                                } else {
                                    // throw an error
                                    // TODO: improve error reporting for FAVR requests
                                    return false;
                                }
                            }
                        }
                    } else {
                        return false;
                    }
                }
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * TODO: needs logic development and implementation
     * Process cancel pending request
     *
     * @param $requestID
     * @param $freelancerID
     * @param $customerID
     *
     * @return boolean
     */
    function processCancelPendingRequest($requestID,  $freelancerID = null, $customerID = null)
    {
        if (isset($requestID) && ($freelancerID != null || $customerID != null)) {
            $select_task_query = "SELECT freelancer_id 
                                  FROM marketplace_favr_requests
                                  WHERE id = '$requestID'";
            $result = $this->db->query($select_task_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_ids_array = unserialize($row['freelancer_id']);
                echo '<pre>';
                print_r($freelancer_ids_array);
                echo '</pre>';
                if (($key = array_search($freelancerID, $freelancer_ids_array)) !== false) {
                    unset($freelancer_ids_array[$key]); // remove the freelancer from the list of sign ups
                } else {
                    return false;
                }

                echo '<br><pre>';
                die(print_r($freelancer_ids_array));
            }
//            $update_task_query = "UPDATE marketplace_favr_requests
//                                  SET
//                                  WHERE id = '$requestID'";
            return true;
        } else {
            return false;
        }
    }

    /**
     * TODO: delete images from server corresponding to task id and this user, and keep data as virtual receipt in system
     * TODO: update database image columns to null after completion of task
     * Process complete request
     *
     * @param $requestID
     * @param $customerID
     * @param $freelancerID
     *
     * @return boolean
     */
    function processCompleteRequest($requestID, $customerID, $freelancerID)
    {
        if (isset($requestID, $customerID, $freelancerID)) {
            // complete request
            $update_request_query = "UPDATE marketplace_favr_requests 
                                     SET task_status = 'Completed'
                                     WHERE id = '$requestID'
                                     AND customer_id = '$customerID'
                                     AND freelancer_id = '$freelancerID'";

            $result = $this->db->query($update_request_query);

            if ($result) {
                // successfully completed
                return true;
            } else {
                // error when completing
                return false;
            }
        } else {
            // not set
            return false;
        }
    }

    /**
     * Image naming convention: task_id-customer_id-request-image#.x hashed by md5
     * Process delete request and task associated images
     *
     * @param $requestID
     * @param $customerID
     *
     * @return boolean
     */
    function processDeleteRequest($requestID, $customerID)
    {
        if (isset($requestID, $customerID)) {
            // Delete images
            $select_request_query = "SELECT *
                                     FROM marketplace_favr_requests
                                     WHERE id = '$requestID'
                                     AND customer_id = '$customerID'";

            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $task1_image_array = unserialize($row['task_picture_path_1']);
                $task2_image_array = unserialize($row['task_picture_path_2']);
                $task3_image_array = unserialize($row['task_picture_path_3']);

                if (!empty($task1_image_array)) {
                    $imageName = $task1_image_array['name'];

                    // TODO: constant used image folder path
                    if (file_exists("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName")) {
                        $removeImage = unlink("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task2_image_array)) {
                    $imageName = $task2_image_array['name'];

                    // TODO: constant used image folder path
                    if (file_exists("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName")) {
                        $removeImage = unlink("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task3_image_array)) {
                    $imageName = $task3_image_array['name'];

                    // TODO: constant used image folder path
                    if (file_exists("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName")) {
                        $removeImage = unlink("/Applications/XAMPP/xamppfiles/favr-request-images/$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }
            }

            // Delete request
            $delete_request_query = "DELETE FROM marketplace_favr_requests
                                     WHERE id = '$requestID'
                                     AND customer_id = '$customerID'";
            $result = $this->db->query($delete_request_query);

            if ($result) {
                // successfully deleted
                return true;
            } else {
                // error when deleting
                return false;
            }
        } else {
            // Not set
            return false;
        }
    }

    /**
     * Process accept request
     *
     * Flow: Marketplace -> Verified freelancer accepts -> Notify customer -> customer accepts -> Notify freelancer -> Change status of request to pending job
     *                                                      |-> freelancer or customer rejects -> Marketplace
     *
     * @param $requestID
     * @param $freelancerID
     *
     * @return boolean
     */
    function processFreelancerAcceptRequest($requestID, $freelancerID)
    {
        if (isset($requestID, $freelancerID)) {
            // freelancer has accepted
            $select_request_query = "SELECT * 
                                     FROM marketplace_favr_requests
                                     WHERE id = '$requestID'";
            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_count = $row['task_freelancer_count'];
                $freelancer_ids_array = unserialize($row['freelancer_id']);
                $setTaskStatusPending = ""; // still need more freelancers

                // ensure there's not already enough freelancers signed up for this job
                if (sizeof($freelancer_ids_array) < $freelancer_count) {
                    // ensure that this user has not already accepted to work this request
                    foreach ($freelancer_ids_array as $freelancer_id) {
                        if ($freelancer_id == $freelancerID) {
                            // This should never happen
                            // TODO: handle error reporting for this critical error
                            return false;
                        }
                    }
                    array_push($freelancer_ids_array, $freelancerID);
                } else if (sizeof($freelancer_ids_array) > $freelancer_count) { // This cannot ever happen
                    // TODO: handle error reporting for this critical error
                    return false;
                } else if (sizeof($freelancer_ids_array) == $freelancer_count) {
                    $setTaskStatusPending = ", task_status = 'Pending Approval' "; // Has enough freelancers notify customer pending their approval
                }

                $freelancer_ids_array = serialize($freelancer_ids_array);

                $update_request_query = "UPDATE marketplace_favr_requests 
                                         SET freelancer_id = '$freelancer_ids_array'
                                         $setTaskStatusPending
                                         WHERE id = '$requestID'";
                $result = $this->db->query($update_request_query);

                if ($result) {
                    // successfully accepted
                    return true;
                } else {
                    // failed to accept
                    return false;
                }
            }


        } else {
            // not set
            return false;
        }
    }

    /**
     * Process notifications
     *
     * @param $userInfo
     *
     * @return integer // return notification count
     */
    function processNotifications($userInfo)
    {
        $userID = $userInfo['id'];

        $notifications_query = "SELECT COUNT(*)
                                FROM marketplace_favr_requests mfr 
                                WHERE mfr.customer_id = '$userID' 
                                AND mfr.freelancer_id IS NOT NULL
                                AND NOT mfr.task_status = 'Completed'
                                OR mfr.freelancer_id = '$userID'
                                AND NOT mfr.task_status = 'Completed'
                                ";

        if ($this->db != null) {
            $result = $this->db->query($notifications_query);
            $row = $result->fetch(PDO::FETCH_ASSOC);

            $_SESSION['main_notifications'] = $row['COUNT(*)'];
        } else {
            $_SESSION['main_notifications'] = 0;
        }

        return $_SESSION['main_notifications'];
    }
}