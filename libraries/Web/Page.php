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
    public $dsn = Data_Constants::DB_DSN;

    /**
     * Backend username
     * @var string
     */
    public $username = Data_Constants::DB_USERNAME;

    /**
     * Backend password
     * @var string
     */
    public $password = Data_Constants::DB_PASSWORD;

    /**
     * String value to keep track and validate product version
     * @var string
     */
    public $product_version = Data_Constants::PRODUCT_VERSION;

    /**
     * value to determine project root path
     * @var string
     */
    public $root_path = Data_Constants::ROOT_PATH;

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
     * Whether or not user is able to edit content
     * @var boolean
     */
    public $editor = false;

    /**
     * Constructor for the page. Sets up most of the properties of this object.
     *
     * @param string $page_title
     * @param string $user
     * @param boolean $render_main_navigation
     */
    function __construct($user = "", $page_title = "FAVR", $render_main_navigation = true)
    {
        $this->page_title = $page_title;
        $this->render_main_navigation = $render_main_navigation;

        // Connect to database
        $this->db = $this->connect();

        $_SESSION['user'] = $user;
        $this->user = $user;

        // not permitted user
        if (empty($_SESSION['user']) && empty($this->user)) {
            $_SESSION['user_info'] = array(
                "id" => '-1'
            );
            header("Location: $this->root_path/signin/");
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

            if (isset($_GET['limit_marketplace_by'])) {
                $_SESSION['limit_marketplace_by'] = $_GET['limit_marketplace_by'];
            } else if (!isset($_SESSION['limit_marketplace_by'])) {
                $_SESSION['limit_marketplace_by'] = "LIMIT 3";
            }

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
            echo "Error: Unable to load this page. Please contact contact@askfavr.com for assistance.";
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
     * @param string $signInUsernameEmail
     * @param string $signInPass
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
                                 AND password='$signInPass'
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
     * @param string $signUpUsername
     * @param string $signUpEmail
     * @param string $signUpFirstName
     * @param string $signUpLastName
     * @param string $signUpPass
     * @param string $signUpPassConfirm
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
     * @param string $page_title // The title of the page.
     */
    function setTitle($page_title)
    {
        if (trim($page_title) != "") {
            if ($_SESSION['main_notifications'] > 0) {
                $this->page_title = "(" . $_SESSION['main_notifications'] . ") " . $this->page_title;
            }

            $this->page_title .= " - " . $page_title;
        }
    }

    /**
     * TODO: implement set permissions to lock certain aspects and functionality of FAVR to certain users only
     * Set permissions for the page. Will render Access Denied page and kill the page if needed.
     *
     * @param $restrict_id integer The component to which access should be restricted.
     * @param $restrict_class integer The user class to which access should be restricted.
     * @param $restrict_user string Whether or not the page should be restricted to a certain user.
     *
     */
    function setPermissions($restrict_id, $restrict_class, $restrict_user)
    {
        return;
    }

    /**
     * Check if these users are friends
     *
     * Users are friends if the friends_since column timestamp is not empty as it is only updated once a friendship is confirmed
     *
     * @param int $user_id
     * @param int $friend_id
     *
     * @return boolean // true if users are friends false otherwise
     */
    function assertFriends($user_id, $friend_id) {
        if (isset($user_id, $friend_id)) {
            $select_friend_query = "SELECT friends_since FROM friends WHERE user_id = $user_id AND friend_id = $friend_id";
            $result = $this->db->query($select_friend_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                if (!empty($row) && $row['friends_since'] != null) {
                    // these users are friends
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Get user info by id
     *
     * @param int $userID
     *
     * @return mixed // return array of userInfo if user exists NULL otherwise
     */
    function getUserInfo($userID)
    {
        $user_query = "SELECT * 
                       FROM users
                       WHERE id = '$userID'";
        $result = $this->db->query($user_query);
        $row = $result->fetch(PDO::FETCH_ASSOC);
        if (!empty($row)) {
            return $row;
        } else {
            return false;
        }
    }

    /**
     * Set any stylesheets that need to be loaded in the header.
     * Must be called before renderHeader.
     *
     * @param string $add_stylesheet string The stylesheet to load.
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
     *
     * @param string $add_script string The stylesheet to load.
     */
    function addScript($add_script)
    {
        if (trim($add_script) != "") {
            $this->script .= $add_script;
        }
    }

    /**
     * Render page header
     * @param boolean $render_top_nav
     * @param boolean $render_back_button
     */
    function renderHeader($render_top_nav = true, $render_back_button = false)
    {
        if (empty($_SESSION['user'])) {
            header("location: $this->root_path/signin/ ");
        }
        ?>
        <!--
        FFFFFFFFFFFFFFFFFFFFFF               AAA               VVVVVVVV           VVVVVVVV        RRRRRRRRRRRRRRRRR
        F::::::::::::::::::::F              A:::A              V::::::V           V::::::V        R::::::::::::::::R
        F::::::::::::::::::::F             A:::::A             V::::::V           V::::::V        R::::::RRRRRR:::::R
        FF::::::FFFFFFFFF::::F            A:::::::A            V::::::V           V::::::V        RR:::::R     R:::::R
          F:::::F       FFFFFF           A:::::::::A            V:::::V           V:::::V           R::::R     R:::::R
          F:::::F                       A:::::A:::::A            V:::::V         V:::::V            R::::R     R:::::R
          F::::::FFFFFFFFFF            A:::::A A:::::A            V:::::V       V:::::V             R::::RRRRRR:::::R
          F:::::::::::::::F           A:::::A   A:::::A            V:::::V     V:::::V              R:::::::::::::RR
          F:::::::::::::::F          A:::::A     A:::::A            V:::::V   V:::::V               R::::RRRRRR:::::R
          F::::::FFFFFFFFFF         A:::::AAAAAAAAA:::::A            V:::::V V:::::V                R::::R     R:::::R
          F:::::F                  A:::::::::::::::::::::A            V:::::V:::::V                 R::::R     R:::::R
          F:::::F                 A:::::AAAAAAAAAAAAA:::::A            V:::::::::V                  R::::R     R:::::R
        FF:::::::FF              A:::::A             A:::::A            V:::::::V                 RR:::::R     R:::::R
        F::::::::FF             A:::::A               A:::::A            V:::::V                  R::::::R     R:::::R
        F::::::::FF            A:::::A                 A:::::A            V:::V                   R::::::R     R:::::R
        FFFFFFFFFFF           AAAAAAA                   AAAAAAA            VVV                    RRRRRRRR     RRRRRRR
        © 2018 FAVR, Inc by Haron Arama and Solomon Antoine
                                                                                                                -->
        <!doctype html>
        <html lang="en">
        <head>
            <meta charset="utf-8">
            <!--            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">-->
            <meta name="viewport"
                  content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=5.0, shrink-to-fit=yes, user-scalable=no"/>
            <meta name="HandheldFriendly" content="true"/>
            <meta name="description"
                  content="Post job requests at your price and have access to verified freelancers for open tasks. Chat with friends and trade FAVRs.">
            <meta name="author"
                  content="Solken Technoloy LLC: Solomon Antoione, Haron Arama, D'Angelo Tines, and Ken Nguyen">
            <meta name="theme-color" content="#343a40"/>
            <meta name="msapplication-TileColor" content="#da532c">
            <meta name="theme-color" content="#f5f5f5">
            <link rel="icon" href="<?php echo $this->root_path; ?>/assets/brand/favicon.ico">

            <title><?php echo $this->page_title; ?></title>

            <!-- Manifest -->
            <link rel="manifest" href="<?php echo $this->root_path; ?>/manifest.json">

            <meta name="apple-mobile-web-app-capable" content="yes">
            <meta name="apple-mobile-web-app-status-bar-style" content="black">
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
        $this->renderMainNavigation($render_top_nav, $render_back_button);
        ?>
        <main role="main" class="container animate-bottom" style="max-width: 750px">
        <?php
    }

    /**
     * Render marketplace history for this specific user
     *
     * @param int $id
     * @param int $friend_id
     * @param string $orderBy
     * @param string $orientation
     *
     * @return boolean
     */
    function renderFavrProfileHistory($id, $friend_id = null, $orderBy = "task_date", $orientation = "DESC", $limit="")
    {
        if ($id == $_SESSION['user_info']['id']) {
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN marketplace_favr_freelancers mff
                                   ON mfr.id = mff.request_id
                                   INNER JOIN users u
                                   ON u.id = mfr.customer_id
                                   AND u.id = $id
                                   OR u.id = mff.user_id
                                   AND u.id = $id
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";

        } else if ($friend_id == $_SESSION['user_info']['id']) { // profile history viewing for other users
            $userInfo = $this->getUserInfo($id);
            if (!empty($userInfo)) {
                $scope = $userInfo['default_scope'];
                if ($scope == Data_Constants::DB_SCOPE_PUBLIC)
                {
                    // the target user has a public profile
                    $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN marketplace_favr_freelancers mff
                                   ON mfr.id = mff.request_id
                                   INNER JOIN users u
                                   ON u.id = mfr.customer_id
                                   AND u.id = $id
                                   OR u.id = mff.user_id
                                   AND u.id = $id
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
                    ";
                }
                else if ($scope == Data_Constants::DB_SCOPE_FRIENDS_OF_FRIENDS)
                {
                    $select_friends_query = "SELECT * 
                                             FROM friends 
                                             WHERE user_id = $id";

                    $result = $this->db->query($select_friends_query);
                    if ($result) {
                        $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                        if (!empty($rows)) {
                            foreach ($rows as $row) {
                                if ($friend_id == $row['friend_id']) {
                                    // if this is already a friend of the target stop the search
                                    $selectMarketplaceQuery = "
                                               SELECT *, mfr.id as mfrid
                                               FROM marketplace_favr_requests mfr
                                               INNER JOIN marketplace_favr_freelancers mff
                                               ON mfr.id = mff.request_id
                                               INNER JOIN users u
                                               ON u.id = mfr.customer_id
                                               AND u.id = $id
                                               OR u.id = mff.user_id
                                               AND u.id = $id
                                               ORDER BY $orderBy
                                               $orientation
                                               $limit
                                    ";
                                    break;
                                } else {
                                    $friend_of_id = $row['friend_id'];
                                    $select_friends_of_friends_query = "
                                        SELECT * 
                                        FROM friends
                                        WHERE user_id = $friend_of_id
                                        AND friend_id = $friend_id
                                    ";
                                    $result = $this->db->query($select_friends_of_friends_query);
                                    if ($result) {
                                        $ffrow = $result->fetch(PDO::FETCH_ASSOC);
                                        if (!empty($ffrow)) {
                                            // this is a friend of a friend of the target
                                            $selectMarketplaceQuery = "
                                               SELECT *, mfr.id as mfrid
                                               FROM marketplace_favr_requests mfr
                                               INNER JOIN marketplace_favr_freelancers mff
                                               ON mfr.id = mff.request_id
                                               INNER JOIN users u
                                               ON u.id = mfr.customer_id
                                               AND u.id = $id
                                               OR u.id = mff.user_id
                                               AND u.id = $id
                                               ORDER BY $orderBy
                                               $orientation
                                               $limit
                                            ";
                                            break;
                                        }
                                    }
                                }
                            }
                        }

                    }
                }
                else if ($scope == Data_Constants::DB_SCOPE_FRIENDS)
                {
                    $select_friends_query = "SELECT * 
                                             FROM friends 
                                             WHERE user_id = $id 
                                             AND friend_id = $friend_id";

                    $result = $this->db->query($select_friends_query);
                    if ($result) {
                        $row = $result->fetch(PDO::FETCH_ASSOC);
                        if (!empty($row)) {
                            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN marketplace_favr_freelancers mff
                                   ON mfr.id = mff.request_id
                                   INNER JOIN users u
                                   ON u.id = mfr.customer_id
                                   AND u.id = $id
                                   OR u.id = mff.user_id
                                   AND u.id = $id
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
                            ";
                        }
                    }
                }
                else if ($scope == Data_Constants::DB_SCOPE_PRIVATE)
                {
                    $selectMarketplaceQuery = "";
                }
                else
                {
                    $selectMarketplaceQuery = "";
                }
            }
        }

        if (!isset($selectMarketplaceQuery)) {
            $selectMarketplaceQuery = "";
        }

        $result = $this->db->query($selectMarketplaceQuery);

        if (!$result) {
            // failed to render user history
            if ($id == $_SESSION['user_info']['id']) {
                echo "<br>Something went wrong! :(";
            } else {
                echo "<br>This profile is private! :(";
            }
            return false;
        } else {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                $userInfo = $this->getUserInfo($id);
                $displayReceipts = $userInfo['display_receipts'];
                if ($displayReceipts == 1) {
                    foreach ($rows as $row) {
                        $freelancer_id = $row['user_id'];
                        $task_id = $row['mfrid'];
                        $customer_id = $row['customer_id'];
                        $customer = $this->getUserInfo($customer_id);
                        $customer_username = $customer['username'];
                        $customer_first_name = $customer['first_name'];
                        $task_description = $row['task_description'];
                        $task_date = date("n/j/Y", strtotime($row['task_date']));
                        $task_location = $row['task_location'];
                        $task_time_to_accomplish = $row['task_time_to_accomplish'];
                        $task_price = $row['task_price'];
                        $task_status = $row['task_status'];
                        $task_rating = $row['task_rating'];
                        $review = stripslashes($row['task_optional_service_review']);

                        $freelancerInfo = $this->getUserInfo($freelancer_id);
                        $customerInfo = $this->getUserInfo($customer_id);

                        $freelancer_username = $freelancerInfo['username'];

                        $customer_profile_img = unserialize($customerInfo['profile_picture_path']);
                        $freelancer_profile_img = unserialize($freelancerInfo['profile_picture_path']);

                        echo "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
                                <div class='pb-2 mb-0 border-bottom border-gray'>";

//                    if ($freelancer_id == $id) {
//                        if (!empty($freelancer_profile_img)) {
//                            $freelancer_profile_img_name = $freelancer_profile_img['name'];
//                            $freelancer_profile_img_type = $freelancer_profile_img['type'];
//                        } else {
//                            $freelancer_profile_img_name = "";
//                            $freelancer_profile_img_type = "";
//                        }
//
//                        echo "<a href='$this->root_path/components/profile/profile.php?id=$freelancer_id'>
//                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\"
//                                     data-src=\"$this->root_path/image.php?i=$freelancer_profile_img_name&i_t=$freelancer_profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
//                              </a>
//                              <strong style='font-size: 80%' class=\"d - block text - gray - dark\"><a href='$this->root_path/components/profile/profile.php?id=$freelancer_id'>@$freelancer_username</a></strong>
//                              ";
//                        echo "<div class='float-right small' style='color: var(--green)'>+ $$task_price</div>";
//                    } else if ($customer_id == $id) {
                        if (!empty($customer_profile_img)) {
                            $customer_profile_img_name = $customer_profile_img['name'];
                            $customer_profile_img_type = $customer_profile_img['type'];
                        } else {
                            $customer_profile_img_name = "";
                            $customer_profile_img_type = "";
                        }

                        echo "<a href='$this->root_path/components/profile/profile.php?id=$customer_id'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\"
                                     data-src=\"$this->root_path/image.php?i=$customer_profile_img_name&i_t=$customer_profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                              </a>
                              <strong style='font-size: 80%' class=\"d - block text - gray - dark\"><a href='$this->root_path/components/profile/profile.php?id=$customer_id'>@$customer_username</a></strong>
                              ";

                        if ($customer_id == $id) {
                            echo "<div class='float-right small' style='color: var(--red)'>- $$task_price</div>";
                        } else if ($freelancer_id == $id) {
                            echo "<div class='float-right small' style='color: var(--green)'>+ $$task_price</div>";
                        }

//                    }

                        echo "</div>
                        <div class=\"media text-muted pt-3\">
                            <div class='container'>
                                <p class=\"media-body text-dark pb-3 mb-0 small lh-125\">";

                        if (!empty($freelancer_profile_img)) {
                            $freelancer_profile_img_name = $freelancer_profile_img['name'];
                            $freelancer_profile_img_type = $freelancer_profile_img['type'];
                        } else {
                            $freelancer_profile_img_name = "";
                            $freelancer_profile_img_type = "";
                        }

                        echo "<p class='text-center'>";
                        echo "<a href='$this->root_path/components/profile/profile.php?id=$freelancer_id'>
                            <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\"
                                 data-src=\"$this->root_path/image.php?i=$freelancer_profile_img_name&i_t=$freelancer_profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded-circle\">
                          </a>";
                        echo "</p>";

                        echo "<p class='text-center'>";
                        if ($task_rating == 1) {
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"small material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                        } else if ($task_rating == 2) {
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                        } else if ($task_rating == 3) {
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                        } else if ($task_rating == 4) {
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                        } else if ($task_rating == 5) {
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                            echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                        }
                        echo "</p>";

                        // optional review preview
                        if (!empty($review)) {
                            echo "<div class='pt-1 text-center'>
                                    <p class='d-inline-flex text-lg-left'>$review</p> 
                                    <i class='material-icons text-muted text-lg-right'>format_quote</i> 
                                  </div>";
                        }

                        echo "    </p>
                             <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                            ";
                        if ($freelancer_id == $id) {
                            if ($task_status == Data_Constants::DB_TASK_STATUS_REQUESTED) {
                                echo "<p class='mb-0 d-inline-flex'>Accepted(Freelancer)</p>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL) {
                                echo "<p class='mb-0 d-inline-flex'>Pending Approval (Freelancer)</p>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_PAID) {
                                echo "<p class='mb-0 d-inline-flex'>Go to location</p>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                                echo "<p class='mb-0 d-inline-flex'>In Progress(Freelancer)</p>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_COMPLETED) {
                                echo "<p class='mb-0 d-inline-flex'>You Completed</p>";
                            }
                        } else if ($customer_id == $id) {
                            if ($task_status == Data_Constants::DB_TASK_STATUS_REQUESTED) {
                                echo "<p class='mb-0 d-inline-flex'>Requested</p> |";
                                echo "<a href=\"?navbar=active_profile&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                            Cancel Request</a>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL) {
                                echo "<p class='mb-0 d-inline-flex'>Pending approval</p> |";
                                echo "<a href=\"?navbar=active_profile&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                            Cancel Request</a>";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_PAID) {
                                echo "<p class='mb-0 d-inline-flex'>Help en-route</p> |";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                                echo "<p class='mb-0 d-inline-flex'>In Progress</p> |";
                            } else if ($task_status == Data_Constants::DB_TASK_STATUS_COMPLETED) {
                                echo "<p class='mb-0 d-inline-flex'>Completed</p>";
                            }

                            if ($task_status == Data_Constants::DB_TASK_STATUS_PAID || $task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                                echo "<div style='cursor: pointer;' class='text-danger d-inline' data-toggle=\"modal\" data-target=\"#cancelInProgressModal\">
                                    Cancel Request</div>
                                ";

                                echo "
                                    <!-- Modal -->
                                    <div class=\"modal fade\" id=\"cancelInProgressModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"cancelInProgressTitle\" aria-hidden=\"true\">
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">You are canceling an in progress request</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">By canceling a request that is in progress you're aware that this will incur a $5 cancellation fee.
                                                                Are you sure you wish to proceed with the cancellation of this request?
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <a href=\"$this->root_path/home/?navbar=active_home&d_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\"
                                               class='btn btn-primary'>
                                                Cancel Request
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";
                            }
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
                    echo "<br>Receipts turned off!";
                }
            } else {
                if ($id == $_SESSION['user_info']['id']) {
                    echo "<p class='p-3 text-muted'>You don't have a FAVR history! <a href='$this->root_path/home/?navbar=active_home&nav_scroller=active_marketplace'>Go to marketplace</a> and request FAVRs :)</p>";
                } else {
                    echo "<p class='p-3 text-muted'>
                            No FAVR history! <a href='$this->root_path/home/?navbar=active_home&nav_scroller=active_marketplace'>Go to marketplace</a> and request FAVRs :)</p>
                          </p>";
                }

                return false;
            }

            return true;
        }
    }

    /**
     * Render profile rating from an array of ratings
     *
     * @param array $ratings
     * @param boolean $renderTrend
     *
     */
    function renderFavrProfileRating($ratings, $renderTrend = true) {
        if (!empty($ratings)) {
            $count = count($ratings);
            $sum = array_sum($ratings);
            $trend = "<i style=\"position:relative;font-weight: lighter;font-size: medium;bottom:  1.2rem;left: .3rem;visibility: hidden\" class=\"material-icons\">
                        arrow_upward</i>";
            $avg = round($sum / $count, 3);
            $digits = strlen((string) $avg);

            if ($digits == 1) {
                $avg .= ".000";
            } else if ($digits == 3) {
                $avg .= "00";
            } else if ($digits == 4) {
                $avg .= "0";
            }

            if ($renderTrend) {
                if ($count >= 2) {
                    if ($ratings[$count - 1] < $ratings[$count - 2]) {
                        $trend = "<i style=\"position:relative;font-weight: lighter;font-size: medium;bottom:  1.2rem;left: .3rem;color:  var(--red);border: 1px solid;border-radius: 1rem;\" class=\"material-icons\">
                        arrow_downward</i>";
                    } else {
                        $trend = "<i style=\"position:relative;font-weight: lighter;font-size: medium;bottom:  1.2rem;left: .3rem;color:  var(--green);border: 1px solid;border-radius: 1rem;\" class=\"material-icons\">
                        arrow_upward</i>";
                    }
                } else {
                    $trend = "<i style=\"position:relative;font-weight: lighter;font-size: medium;bottom:  1.2rem;left: .3rem;color:  var(--green);border: 1px solid;border-radius: 1rem;\" class=\"material-icons\">
                        arrow_upward</i>";
                }
                $mantissa = substr($avg, 3, 5);
            } else {
                $mantissa = "";
            }

            $coefficient = substr($avg, 0, 3);


            echo "
            <p class=\"d-inline-flex mb-0\" style=\"font-size: -webkit-xxx-large;font-weight: lighter\">
                $coefficient
                <p class=\"row pl-3 d-inline-flex\">
                    $trend
                </p>
                <p class=\"row pl-2 mb-0 d-inline-flex\" style=\"font-weight: lighter;font-size: medium\">
                    $mantissa</p>
            </p>
            ";

            echo "<p class='text-center'>";
            if ($avg >= 1 && $avg < 1.5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"small material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 1.5 && $avg < 2) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star_half</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 2 && $avg < 2.5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 2.5 && $avg < 3) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star_half</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 3 && $avg < 3.5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 3.5 && $avg < 4) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star_half</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg == 4 && $avg < 4.5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--dark)\" class=\"material-icons\">star_border</i>";
            }
            else if ($avg >= 4.5 && $avg < 5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star_half</i>";
            }
            else if ($avg == 5) {
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
                echo "<i style=\"font-size: 20px!important;color: var(--yellow)\" class=\"material-icons\">star</i>";
            }
            echo "</p>";
        } else {
            echo "---";
        }
    }

    /**
     * Render profile from userID
     *
     * @param int $userID
     * @param int $friendID
     *
     */
    function renderFavrProfile($userID, $friendID = null) {
        if ($userID == $_SESSION['user_info']['id']) { // this user's profile
            $userInfo = $this->getUserInfo($userID);
            $id = md5($userID);
            $userRealName = $userInfo['first_name'] . " " . $userInfo['last_name'];
            $profile_img = unserialize($userInfo['profile_picture_path']);
            $profile_img_name = "";
            $profile_img_type = "";

            if (!empty($profile_img)) {
                $profile_img_name = $profile_img['name'];
                $profile_img_type = $profile_img['type'];
            }
            ?>
            <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 3rem;">
                <div class="row pb-2 mb-0">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4 text-center border-bottom border-gray">
                        <?php
                            echo " <div id='$id-profile-image' style='height: 4.5rem'>
                                        <img id='$id-profile-img' style='cursor: pointer;bottom: 3.5rem;width: 7rem!important;height: 7rem!important;position: relative;' 
                                            src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                            onclick=\"
                                                    var modal = document.getElementById('$id-profile-image-modal');
                                                    var img = document.getElementById('$id-profile-img');
                                                    var modalImg = document.getElementById('$id-profile-image-modal-content');
                                                    var captionText = document.getElementById('$id-profile-caption');
                                                    
                                                    modal.style.display = 'block';
                                                    modalImg.src = img.src;
                                                    captionText.innerHTML = img.alt;
                                                    \"
                                            data-src='$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true' class='rounded' alt='$userRealName'>
                                    </div>";

                        // Profile image modal
                        echo "
                                <div id=\"$id-profile-image-modal\" class=\"modal\">
                                  <span id='$id-profile-close' 
                                        class=\"modal-close\" 
                                        onclick=\"var modal = document.getElementById('$id-profile-image-modal');
                                                  modal.style.display = 'none';\">&times;</span>
                                  <img class=\"modal-content\" id=\"$id-profile-image-modal-content\">
                                  <div id=\"$id-profile-caption\" class='caption'></div>
                                </div>";

                        echo "  <button class='text-right btn text-muted bg-white' data-toggle='modal' data-target='#profileEditModal'>
                                        <i class='material-icons'>edit</i>
                                    </button>";

                        // profile edit modal
                        echo "    <div class=\"modal fade\" id=\"profileEditModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"profileEditTitle\" aria-hidden=\"true\">
                                    <form action='$this->root_path/components/profile/?navbar=active_profile' method='post' enctype='multipart/form-data'>
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"profileEditTitle\">Edit your profile</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">
                                            <label for='profileImage'>Upload a new profile picture</label>
                                            <input type='file' name='profile_image' class='form-control'>
                                            <br>
                                            <textarea name='profile_description' class='form-control' placeholder='Describe yourself and what you do...'>". $userInfo['profile_description'] ."</textarea>
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <input type=\"submit\" name='submit' class=\"btn btn-primary\" value='Save Changes'>
                                          </div>
                                        </div>
                                      </div>
                                  </form>
                                </div>";
                        ?>
                        <h3>
                            <?php
                                echo $userInfo['first_name'] . " " . $userInfo['last_name'];

                                if ($userInfo['class'] == Data_Constants::DB_USER_CLASS_VERIFIED) {
                                    echo "<i class=\"material-icons text-primary\">verified_user</i>";
                                }
                            ?>
                        </h3>
                    <?php
                        $ratings = unserialize($userInfo['rating']);
                        if ($userInfo['display_ratings'] == 1) {
                            $this->renderFavrProfileRating($ratings);
                        } else {
                            echo "<a href='$this->root_path/components/settings/?navbar=active_settings'>Turn on ratings in settings to view</a>";
                        }
                    ?>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <div class="pb-2 mb-0">
                    <p class="mr-3 text-center">
                        <?php
                        if ($userInfo['display_description'] == 1) {
                            if (!empty($userInfo['profile_description'])) {
                                echo $userInfo['profile_description'];
                            } else {
                                ?>
                                Describe yourself and what you do...
                                <?php
                            }
                        } else {
                            echo "<a href='$this->root_path/components/settings/?navbar=active_settings'>Turn on description in settings to view</a>";
                        }

                        ?>
                    </p>
                </div>
            </div>
            <div class="row m-3 pt-3">
            </div>
            <?php
        } else { // other user's profiles
            $userInfo = $this->getUserInfo($userID);
            $id = md5($userID);
            $userRealName = $userInfo['first_name'] . " " . $userInfo['last_name'];
            $profile_img = unserialize($userInfo['profile_picture_path']);
            $profile_img_name = "";
            $profile_img_type = "";
            if (!empty($profile_img)) {
                $profile_img_name = $profile_img['name'];
                $profile_img_type = $profile_img['type'];
            }
//            echo "<pre>";
//            print_r($userInfo);
//            echo "</pre>";
            ?>
            <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 3rem;">
                <div class="row pb-2 mb-0">
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4 text-center border-bottom border-gray">
                        <?php
                        echo " <div id='$id-profile-image' style='height: 4.5rem;'>
                                        <img id='$id-profile-img' style='cursor: pointer;bottom: 3.5rem;width: 7rem!important;height: 7rem!important;position: relative;' 
                                            src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                            onclick=\"
                                                    var modal = document.getElementById('$id-profile-image-modal');
                                                    var img = document.getElementById('$id-profile-img');
                                                    var modalImg = document.getElementById('$id-profile-image-modal-content');
                                                    var captionText = document.getElementById('$id-profile-caption');
                                                    
                                                    modal.style.display = 'block';
                                                    modalImg.src = img.src;
                                                    captionText.innerHTML = img.alt;
                                                    \"
                                            data-src='$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true' class='rounded' alt='$userRealName'>
                                    </div>";

                        // Profile image modal
                        echo "
                                <div id=\"$id-profile-image-modal\" class=\"modal\">
                                  <span id='$id-profile-close' 
                                        class=\"modal-close\" 
                                        onclick=\"var modal = document.getElementById('$id-profile-image-modal');
                                                  modal.style.display = 'none';\">&times;</span>
                                  <img class=\"modal-content\" id=\"$id-profile-image-modal-content\">
                                  <div id=\"$id-profile-caption\" class='caption'></div>
                                </div>";

                        $select_friends_query1 = "SELECT * 
                                                 FROM friends
                                                 WHERE user_id = $userID
                                                 AND friend_id = $friendID";

                        $select_friends_query2 = "SELECT * 
                                                  FROM friends
                                                  WHERE user_id = $friendID
                                                  AND friend_id = $userID";

                        $result1 = $this->db->query($select_friends_query1);
                        $row1 = $result1->fetch(PDO::FETCH_ASSOC);

                        $result2 = $this->db->query($select_friends_query2);
                        $row2 = $result2->fetch(PDO::FETCH_ASSOC);

                        $isFriend = (!empty($row1) && !empty($row2)) ? true : false;
                        if ($isFriend) {
                            $userInfo = $this->getUserInfo($row1['user_id']);
                            $userFirstName = $userInfo['first_name'];
                            $userFavrsGiven = $row1['given'];
                            $userFavrsReceived = $row1['received'];

                            if ($userFavrsGiven >= $userFavrsReceived) {
                                $askFavr = "<a class='small' href='$this->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends&ask_favr=true&id=$userID&last_url=profile'>Ask FAVR</a>";
                            } else {
                                $askFavr = "";
                            }

                            $friendInfo = $this->getUserInfo($row2['user_id']);
                            $friendFirstName = $friendInfo['first_name'];

                            $friendSinceThen = date("n/j/Y", strtotime($row1['friends_since']));
                            // TODO: give friendship information and ability to manage friendship in modal
                            echo "<button class='text-right btn text-muted bg-white' data-toggle='modal' data-target='#profileFriendInfoModal'>
                                    <i class='material-icons'>group</i>
                                  </button>";

                            // friends stats modal
                            echo "<div class=\"modal fade\" id=\"profileFriendInfoModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"profileFriendInfoTitle\" aria-hidden=\"true\">
                                    <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"profileFriendInfoTitle\">$userFirstName and $friendFirstName</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">
                                            <label for='friendInfo'>Friends Since</label>
                                            <p>$friendSinceThen</p>
                                            <label for='favrGiven'>FAVRs given</label>
                                            <p>$userFavrsGiven</p>
                                            <label for='favrsReceived'>FAVRs received</label>
                                            <p>$userFavrsReceived</p>
                                            $askFavr
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";

                        } else if (!empty($row1)) {
                            // a friend request has been sent to this user
                            echo "<a href='$this->root_path/components/profile/profile.php?id=$userID&add_friend=true&ALERT_MESSAGE=You are now friends on FAVR!'
                                    class='text-right btn text-muted bg-white'>
                                    <i class='material-icons'>person_add</i>
                                  </a>";
                        } else {
                            echo "<a href='$this->root_path/components/profile/profile.php?id=$userID&add_friend=true&ALERT_MESSAGE=Your friend request has been sent!'
                                    class='text-right btn text-muted bg-white'>
                                    <i class='material-icons'>person_add</i>
                                  </a>";
                        }

                        ?>
                        <h3>
                            <?php
                            echo $userInfo['first_name'] . " " . $userInfo['last_name'];

                            if ($userInfo['class'] == Data_Constants::DB_USER_CLASS_VERIFIED) {
                                echo "<i class=\"material-icons text-primary\">verified_user</i>";
                            }
                            ?>
                        </h3>
                        <?php
                        $ratings = unserialize($userInfo['rating']);
                        if ($userInfo['display_ratings'] == 1) {
                            $this->renderFavrProfileRating($ratings,false);
                        } else {
                            $this->renderFavrProfileRating(array(), false);
                        }

                        ?>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div>
                <div class="pb-2 mb-0">
                    <p class="mr-3 text-center">
                        <?php
                        if ($userInfo['display_description'] == 1) {
                            if (!empty($userInfo['profile_description'])) {
                                echo $userInfo['profile_description'];
                            } else {
                                echo "";
                            }
                        }
                        ?>
                    </p>
                </div>
            </div>
            <div class="row m-3 pt-3">
            </div>
            <?php
            if ($isFriend) {
                if ($userFavrsGiven >= $userFavrsReceived) {
                    echo "<a class='small' href='$this->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends&ask_favr=true&id=$userID&last_url=profile'>Ask FAVR</a>";
                }
            }
        }
    }

    /**
     * Render page main navigation
     *
     * @param boolean $render_main_navigation
     * @param boolean $render_back_button
     */
    function renderMainNavigation($render_main_navigation = true, $render_back_button = false)
    {
        if ($render_main_navigation) {
            $active_home = "";
            $active_categories = "";
            $active_notifications = "";
            $active_search = "";
            $active_profile = "";
            $active_settings = "";

            $last_url = "";

            // Handle page navigation presentation logic
            switch ($_SESSION['navbar']) {
                case "active_home":
                    $active_home = "active";
                    if (isset($_SESSION['nav_scroller'])) {
                        $nav_scroller = $_SESSION['nav_scroller'];
                        if ($nav_scroller == "active_friends") {
                            $last_url = "$this->root_path/home/friends/?navbar=active_home&navbar_scroller=$nav_scroller";
                        } else if ($nav_scroller == "active_chat") {
                            $last_url = "$this->root_path/home/chat/?navbar=active_home&navbar_scroller=$nav_scroller";
                        } else {
                            $last_url = "$this->root_path/home/?navbar=active_home";
                        }
                    } else {
                        $last_url = "$this->root_path/home/?navbar=active_home";
                    }
                    break;
                case "active_categories":
                    $active_categories = "active";
                    $last_url = "$this->root_path/components/categories/?navbar=active_categories";
                    break;
                case "active_notifications":
                    $active_notifications = "active";
                    $last_url = "$this->root_path/components/notifications/?navbar=active_notifications";
                    break;
                case "active_profile":
                    $active_profile = "active";
                    $last_url = "$this->root_path/components/profile/?navbar=active_profile";
                    break;
                case "active_search":
                    $active_search = "active";
                    $last_url = "$this->root_path/components/search/?navbar=active_search";
                    break;
                case "active_settings":
                    $active_settings = "active";
                    $last_url = "$this->root_path/components/settings/?navbar=active_settings";
                    break;
                case "friends_list":
                    $active_home = "active";
                    if (isset($_GET['friends_list']) && $_GET['friends_list'] == true) {
                        $nav_scroller = $_SESSION['nav_scroller'];
                        $last_url = "$this->root_path/home/friends/?navbar=active_home&nav_scroller=$nav_scroller";
                    } else {
                        $last_url = "$this->root_path/home/friends/?friends_list=true";
                    }
                    break;
                default:
                    // none active
                    break;
            }
            ?>
            <nav class="navbar navbar-mobile navbar-expand-md fixed-top navbar-dark bg-dark">
                <?php
                    if ($render_back_button) {
                        // back button
                        ?>
                        <a href="<?php echo $last_url; ?>" class="navbar-toggler pt-0 pb-2 border-0" style="height: 44px;">
                            <span class="sr-only">Toggle back navigate</span>
                            <i class="material-icons text-light">arrow_back</i>
                        </a>
                <?php
                    } else {
                ?>
                        <button class="navbar-toggler pb-2 border-0" type="button" data-toggle="offcanvas">
                            <span class="sr-only">Toggle navigation</span>
                            <span></span>
                            <span></span>
                            <span></span>
                        </button>
                <?php
                    }
                ?>
                <div class="request-favr pt-0 pr-2 pb-0 mr-0">
                    <?php
                    if ($_SESSION['nav_scroller'] != "active_marketplace" || $_SESSION['navbar'] != "active_home") {
                        echo "
                            <a href=\"$this->root_path/home/?navbar=active_home&nav_scroller=active_marketplace\">
                        ";
                    }
                    ?>
                    <img src="<?php echo $this->root_path; ?>/assets/brand/favr_logo_rd.png" height="21" width="70"
                         class="navbar-brand mr-0" style="padding-top: 0; padding-bottom: 0" alt="Logo">

                    <?php
                    if ($_SESSION['nav_scroller'] != "active_marketplace" || $_SESSION['navbar'] != "active_home") {
                        echo "
                            </a>
                        ";
                    }
                    ?>
                </div>


                    <?php
                    $userInfo = $this->getUserInfo($_SESSION['user_info']['id']);
                    $profile_image = unserialize($userInfo['profile_picture_path']);

                    if (isset($profile_image['name'], $profile_image['type'])) {
                        $profile_img_name = $profile_image['name'];
                        $profile_img_type = $profile_image['type'];
                        ?>
                        <button class="profile-button border-0 mr-0 pr-0" style="left: .1rem;padding-bottom: .569rem;" type="button">
                            <a href='<?php echo "$this->root_path/components/profile/?navbar=active_profile"; ?>'>
                                <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'
                                     data-src='<?php echo "$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true"; ?>' height='26' width='26' alt='Profile Image'
                                     style="border: 1px solid red;border-radius: 1rem;">
                            </a>
                        </button>
                        <?php
                    } else {
                        if ($_SESSION['navbar'] == "active_profile") {
                            echo "
                                <button class=\"profile-button border-0 pb-1 mr-0 pr-0\" style=\"left: .1rem;\" type=\"button\">
                                    <i class=\"material-icons\" style=\"color: red;border: 1px solid;border-radius: 1rem;\">person</i>
                                </button>
                            ";
                        } else {
                            echo "
                                <button class=\"profile-button border-0 pb-1 mr-0 pr-0\" style=\"left: .1rem;\" type=\"button\">
                                  <a href='$this->root_path/components/profile/?navbar=active_profile'>
                                     <i class=\"material-icons\" style=\"color: red;border: 1px solid;border-radius: 1rem;\">person_outline</i>
                                  </a>
                                </button>";
                        }
                    }
                    ?>


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
                                Welcome, <?php echo $_SESSION['user_info']['first_name']; ?>
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

<!--                     WEB ELEMENT ONLY-->
                    <form class="web-search form-inline my-2 my-lg-0">
                        <input style="border-radius: 5px 0 0 5px" class="form-control mr-sm-0" type="text" placeholder="Search" aria-label="Search">
                        <button style="border-radius: 0 5px 5px 0" class="btn btn-outline-danger my-2 my-sm-0" type="submit">Search</button>
                    </form>
<!--                     WEB ELEMENT ONLY-->

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
                        <li class="mobile-footer p-1 ml-1 text-white fixed-bottom small">
                            <div>&copy;2018 FAVR, Inc v<?php echo $this->product_version; ?> Beta</div>
                        </li>
                    </ul>
                </div>
            </nav>

            <?php
            if ($_SESSION['navbar'] == "active_home") {
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

                if (!$render_back_button) {
                    ?>
                    <div class="nav-scroller bg-white box-shadow">
                        <nav class="nav nav-underline">
                            <div class="col-sm-4 pl-0 pr-0" style="max-width: 170px">
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
                            </div>
                            <div class="col-sm-4 pl-0 pr-0" style="max-width: 170px;">
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
                            </div>
                            <div class="col-sm-4 pl-0 pr-0" style="max-width: 170px;">
                                <a class="nav-link <?php echo $active_chat; ?>"
                                   href="<?php echo $this->root_path; ?>/home/chat/?navbar=active_home&nav_scroller=active_chat">
                                    Chat
                                    <?php
                                    if ($active_chat) {
                                        echo "<i class=\"material-icons\" style='color: var(--red);font-size: 15px; padding-left: 2px;position:relative;top:.1rem;'>chat</i>";
                                    } else {
                                        echo "<i class=\"material-icons\" style='font-size: 15px; padding-left: 2px;position:relative;top:.1rem;'>chat</i>";
                                    }
                                    ?>
                                </a>
                            </div>
                            <!--                    <a id="suggestions" onclick="focusNoScrollMethod()" class="nav-link -->
                            <?php //echo  $active_suggestions; ?><!--" href="?nav_scroller=active_suggestions">Suggestions</a>-->
                        </nav>
                    </div>
                    <?php
                }
            }
        }

    }

    /**
     * Render notification count
     *
     * @param int $notificationCount
     *
     * @return boolean // true if there's notifications false otherwise
     */
    function renderNotificationCount($notificationCount)
    {
        if ($notificationCount > 0) {

            echo "<span id='notificationCount' style=\"height: 1rem\" class=\"badge badge-pill red-bubble-notification align-text-bottom\">$notificationCount</span>";

            return true;
        } else {
            return false;
        }
    }

    /**
     * Render main notifications
     *
     * @param array $userInfo
     *
     * @return boolean
     */
    function renderMainNotifications($userInfo)
    {
        $userID = $userInfo['id'];
        $completed = Data_Constants::DB_TASK_STATUS_COMPLETED;
        $notifications_request_query = "
                                SELECT *, mff.user_id AS mffuserid, mfr.id AS mfrid
                                FROM marketplace_favr_requests mfr 
                                JOIN marketplace_favr_freelancers mff 
                                ON mff.request_id = mfr.id
                                JOIN users u
                                ON u.id = mff.user_id
                                AND mff.user_id = $userID 
                                AND NOT mfr.task_status = '$completed'
                                OR mfr.customer_id = $userID 
                                AND u.id = mfr.customer_id
                                AND NOT mfr.task_status = '$completed'";

        $notifications_friend_request_query = "
                SELECT *, f.user_id as uid 
                FROM friends f
                JOIN users u
                ON u.id = f.friend_id
                WHERE u.id = $userID
                AND friends_since IS NULL 
        ";

        $notifications_friend_favr_request_query = "
                                SELECT *, ffr.id as ffrid
                                FROM friends_favr_requests ffr 
                                JOIN users u
                                ON u.id = ffr.customer_id
                                AND ffr.customer_id = $userID 
                                AND ffr.friend_id IS NOT NULL 
                                AND NOT ffr.task_status = '$completed'
                                OR ffr.friend_id = $userID 
                                AND u.id = ffr.friend_id
                                AND NOT ffr.task_status = '$completed'";

        $result = $this->db->query($notifications_request_query);
        $result1 = $this->db->query($notifications_friend_request_query);
        $result2 = $this->db->query($notifications_friend_favr_request_query);

        if (!$result || !$result1 || !$result2) {
            // failed to render notifications
            return false;
        } else {
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $rows1 = $result1->fetchAll(PDO::FETCH_ASSOC);
            $rows2 = $result2->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows) || !empty($rows1) || !empty($rows2)) {
                // There are results
                // request notifications
                foreach ($rows as $row) {
                    $freelancer_id = $row['mffuserid'];
                    $id = md5($row['mfrid'] . "-$freelancer_id"); // div id
                    $task_id = $row['mfrid'];
                    $freelancerInfo = $this->getUserInfo($freelancer_id);
                    $freelancer_username = $freelancerInfo['username'];
                    $freelancer_first_name = $freelancerInfo['first_name'];
                    $freelancer_accepted = $row['task_freelancer_accepted'];
                    $task_freelancer_count = $row['task_freelancer_count'];

                    $customer_id = $row['customer_id'];
                    $customerInfo = $this->getUserInfo($customer_id);
                    $customer_username = $customerInfo['username'];
                    $customer_first_name = $customerInfo['first_name'];
                    $customer_phone = $customerInfo['phone'];

                    $task_description = $row['task_description'];
                    $task_date = date("n/j/Y", strtotime($row['task_date']));
                    $task_location = $row['task_location'];
                    $task_time_to_accomplish = date('h:i A, l, n/j/Y', strtotime($row['task_date']));
                    $task_price = $row['task_price'];
                    $task_difficulty = $row['task_intensity'];
                    $task_status = $row['task_status'];

                    $profile_img_data_array = unserialize($customerInfo['profile_picture_path']);

                    if (isset($profile_img_data_array['name'], $profile_img_data_array['type'])) {
                        $profile_img_name = $profile_img_data_array['name'];
                        $profile_img_type = $profile_img_data_array['type'];
                    } else {
                        $profile_img_name = "";
                        $profile_img_type = "";
                    }

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
                            <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                data-src=\"$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                            </a>   
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                ";

                    if ($customer_id == $_SESSION['user_info']['id']) {
                        echo "<p class='font-weight-light text-muted d-inline-flex'>Accepted by</p> <a href='$this->root_path/components/profile/profile.php?id=$freelancer_id'>@$freelancer_username</a>";
                    } else {
                        echo "<a href='$this->root_path/components/profile/profile.php?id=$customer_id'>@$customer_username</a>";
                    }


                    echo "</strong>
                            ";
                    if (isset($_GET['request_completed_id']) && $task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                        echo "</div>";
                        echo "<div class='mt-3 text-center'>";
                        echo "    <p class=\"small d-inline-flex\">";

                        // rating system 1 - 5 stars + optional review
                        if (isset($_GET['stars'])) {
                            if ($_GET['stars'] == 1) {
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=2'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 2) {
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 3) {
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 4) {
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=3'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 5) {
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=3'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=4'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                            }
                        } else {
                            echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=1'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=2'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                        }

                        echo " </p>";
                        echo "</div>";

                        $review = isset($_POST['review']) ? $_POST['review'] : null;
                        $stars = isset($_GET['stars']) ? $_GET['stars'] : 3;
                        // optional review preview
                        if (isset($_POST['review'])) {
                            $review = $_POST['review'];
                            echo "<div class='mt-1 text-center'>
                                    <p class='d-inline-flex text-lg-left'>$review</p> 
                                    <i class='material-icons text-muted text-lg-right'>format_quote</i> 
                                  </div>";
                        }

                        echo "<div class='mt-2 p-2 text-center'>";
                        echo "<!-- Button trigger modal -->
                                <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\">
                                  Leave an optional review
                                </button>
                                
                                <!-- Modal -->
                                <div class=\"modal fade\" id=\"exampleModalCenter\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalCenterTitle\" aria-hidden=\"true\">
                                  <form action='$this->root_path/components/notifications/?navbar=active_notifications&request_completed_id=$task_id&stars=$stars' method='post'>
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">Leave a review for $freelancer_first_name</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">
                                            <textarea name='review' class='form-control' placeholder='Leave a review of your experience...'>$review</textarea>
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <input type=\"submit\" name='submit' class=\"btn btn-primary\" value='Save Changes'>
                                          </div>
                                        </div>
                                      </div>
                                  </form>
                                </div>";

                        echo "  <div class='mt-3 border-top border-gray pt-3'>
                                    <div class='d-inline-flex text-center'>
                                        <form action='$this->root_path/components/notifications/?navbar=active_notifications&completed_request_id=$task_id&stars=$stars' method='post'>
                                            <input type='hidden' name='request_rating' value='$stars' required>
                                            <input type='hidden' name='freelancer_id' value='$freelancer_id' required>
                                            <input type='hidden' name='customer_id' value='$customer_id' required>
                                            <input type='hidden' name='request_review' value='$review'>
                                            <input class='btn btn-sm btn-outline-success'
                                                type='submit'
                                                name='complete_request'
                                                value='Submit' />
                                        </form>
                                    </div>
                                </div>";
                        echo "</div>";
                    } else {

                        if (isset($task_difficulty) && $customer_id != $_SESSION['user_info']['id']) {
                            if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_EASY) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-success p-1 rounded\" style='opacity: .9' value=\"Easy\" disabled>Easy 👌</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_MEDIUM) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-warning p-1 rounded\" style='opacity: .9' value=\"Medium\" disabled>Medium 💪🏿</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_HARD) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-danger p-1 rounded\" style='opacity: .9' value=\"Hard\" disabled>Hard 🔥</button>";
                            }
                        }

                        if ($customer_id == $_SESSION['user_info']['id']) {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--red)'>- $$task_price</div>";
                        } else {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--green)'>+ $$task_price</div>";
                        }

                        echo "</div><div class=\"media text-muted pt-3\">
                        <div class='container'>
                            <p id='$id' class=\"media-body text-dark mb-0 small lh-125\">
                                $task_description
                                <div id='$id-location' class='pt-1 border-top small border-gray d-none'>
                                    <label for='location'>Location:</label>";

                        // TODO: add contact info for customer and freelancer
                        // share location of customer to freelancer if task is in progress or paid for
                        if ($task_status == Data_Constants::DB_TASK_STATUS_PAID || $task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                            echo "<p class='text-dark'>$task_location</p>";
                            echo "<div class='pt-1 border-top border-bottom border-gray'>
                                    <label for='contact'>Contact:</label>
                                    <p class='text-dark'>$customer_phone</p>
                                  </div>";
                        } else {
                            echo "<!-- TODO: calculate location distance by zipcode -->
                                    <p class='text-dark'>Rochester, MN</p>";
                        }

                        echo "
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
                                    <img id='$id-img1' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task1_img_name&i_t=$task1_img_type' height='30%' width='30%' alt='FAVR image 1'>
                                </div>";
                        // Image 1 modal
                        echo "
                            <div id=\"$id-image1-modal\" class=\"modal\">
                              <span id='$id-close1' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image1-modal-content\">
                              <div id=\"$id-caption1\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image2' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image2'>Attached Image 2:</label>
                                    <img id='$id-img2' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task2_img_name&i_t=$task2_img_type' height='30%' width='30%' alt='FAVR image 2'>
                                </div>";
                        // Image 2 modal
                        echo "
                            <div id=\"$id-image2-modal\" class=\"modal\">
                              <span id='$id-close2' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image2-modal-content\" alt='FAVR image 2'>
                              <div id=\"$id-caption2\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image3' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image3'>Attached Image 3:</label>
                                    <img id='$id-img3' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                    data-src='$this->root_path/image.php?i=$task3_img_name&i_t=$task3_img_type' height='30%' width='30%' alt='FAVR image 3'>
                                </div>";
                        // Image 3 modal
                        echo "
                            <div id=\"$id-image3-modal\" class=\"modal\">
                              <span id='$id-close3' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image3-modal-content\">
                              <div id=\"$id-caption3\" class='caption'></div>
                            </div>";
                        echo "
                            </p>
                            <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                        <div id='$id-expand' class='text-info d-inline-flex'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('#$id').animate({height: '4rem'});
                                              $('#$id-location').removeClass('d-none');
                                              $('#$id-freelancer-count').removeClass('d-none');
                                              $('#$id-collapse').removeClass('d-none');
                                              $('#$id-collapse').addClass('d-inline-flex');
                                              $('#$id-expand').removeClass('d-inline-flex');
                                              $('#$id-expand').addClass('d-none');";

                        for ($i = 1; $i <= Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                            echo "
                            var modal = document.getElementById('$id-image$i-modal');
                            var img = document.getElementById('$id-img$i');
                            var modalImg = document.getElementById('$id-image$i-modal-content');
                            var captionText = document.getElementById('$id-caption$i');
                            img.onclick = function(){
                                modal.style.display = 'block';
                                modalImg.src = this.src;
                                captionText.innerHTML = this.alt;
                            }
                            var span = document.getElementById('$id-close$i');
                            span.onclick = function() { 
                                modal.style.display = 'none';
                            }
                         ";
                        }

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
                                        \">Collapse</div>
                                        ";


                        echo "
                                    </div>
                                       ";

                        // Customer and freelancer request actions
                        if ($customer_id != $_SESSION['user_info']['id']) { // if not this user
                            $freelancerAccepted = false;
                            $freelancer_id = null;
                            $user_id = $_SESSION['user_info']['id'];
                            $select_freelancers_query = "SELECT * 
                                                     FROM marketplace_favr_freelancers
                                                     WHERE request_id = $task_id
                                                     AND user_id = $user_id";
                            $result = $this->db->query($select_freelancers_query);
                            if ($result) {
                                $row = $result->fetch(PDO::FETCH_ASSOC);
                                if (!empty($row)) {
                                    $freelancerAccepted = true;
                                    $freelancer_id = $row['user_id'];
                                }
                            }

                            if ($freelancerAccepted) {
                                if ($task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL || $task_status == Data_Constants::DB_TASK_STATUS_REQUESTED) {
                                    echo "<div class='float-right d-inline'>
                                    <p class='d-inline-flex mb-1'>
                                    Status: You Accepted</p>
                                  </div>";
                                } else {
                                    echo "<div class='float-right d-inline'>
                                        <p class='d-inline-flex mb-1'>
                                        ";
                                    // TODO: use class defined constants here to validate verified freelancers from regular customers
                                    if ($task_status == Data_Constants::DB_TASK_STATUS_PAID && $customer_id != $_SESSION['user_info']['id']) {
                                        echo "Status: Go to location";
                                    } else {
                                        echo "Status: $task_status";
                                    }

                                    echo "  </p>
                                      </div>";
                                }

                                if ($task_status == Data_Constants::DB_TASK_STATUS_PAID) {
                                    echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                            <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&freelancer_arrived=true&arrived_request_id=$task_id&ALERT_MESSAGE=You've arrived! Make sure you're at the correct location and that the customer is who they say they are!\" class='text-success'>
                                            Freelancer Arrived</a>
                                          </div>
                                        ";

                                    echo "<div class='d-block mt-2 pt-2 border-gray border-top text-center'>
                                        <a class='text-danger' href=\"$this->root_path/components/notifications/?navbar=active_notifications&withdraw_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE=You've withdrawn from this task: the customer has been notified!\">
                                        Withdraw From Task</a>
                                      </div>";
                                } else {
                                    echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                        <a class='text-danger' href=\"$this->root_path/components/notifications/?navbar=active_notifications&withdraw_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE=You've withdrawn from this task: the customer has been notified!\">
                                        Withdraw From Task</a>
                                      </div>";
                                }

                            } else {
                                echo "<div class='float-right d-inline'>
                                    <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_freelancer_request_id=$task_id&ALERT_MESSAGE=You've signed up to take this task! The task requester has been notified of your interest and is reviewing your offer to help: they can accept or reject your offer to help! You'll be notified of their decision; you can withdraw your offer to help before they decide. \">
                                    Accept Request</a>
                                  </div>";
                            }
                        } else {
                            $select_freelancers_query = "SELECT * 
                                                     FROM marketplace_favr_freelancers
                                                     WHERE request_id = $task_id
                                                     AND user_id = $freelancer_id";
                            $result = $this->db->query($select_freelancers_query);
                            $row = $result->fetch(PDO::FETCH_ASSOC);
                            if (!empty($row)) {
                                if ($row['approved'] == 1) { // this user is approved
                                    echo "<div class='float-right d-inline'>
                                        <p class='d-inline-flex mb-1'>
                                        ";
                                    // TODO: use class defined constants here to validate verified freelancers from regular customers
                                    if ($task_status == Data_Constants::DB_TASK_STATUS_PAID && $customer_id == $_SESSION['user_info']['id']) {
                                        echo "Status: Help en-route";
                                    } else {
                                        echo "Status: $task_status";
                                    }

                                    echo "  </p>
                                      </div>";

                                    if ($task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                                        echo "<div class='d-block mt-4 pt-2 mb-0 pt-2 border-gray border-top text-center'>
                                            <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&request_completed_id=$task_id&ALERT_MESSAGE=You've marked this request as completed now please rate your experience!\" class='text-success'>
                                                 Request Completed</a>
                                          </div>
                                    ";

                                        echo "<div class='d-block mt-2 pt-2 border-gray border-top text-center'>";
                                    } else {
                                        echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>";
                                    }


                                    if ($task_status == Data_Constants::DB_TASK_STATUS_REQUESTED || $task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL) {
                                        echo "<a href=\"$this->root_path/home/?navbar=active_home&d_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\" class='mt-0 text-danger'>
                                        Cancel Request</a>
                                        ";
                                    } else {
                                        echo "<div style='cursor: pointer;' class='text-danger d-inline' data-toggle=\"modal\" data-target=\"#cancelInProgressModal\">
                                            Cancel Request</div>
                                        ";
                                    }
                                    echo "</div>";

                                    echo "
                                    <!-- Modal -->
                                    <div class=\"modal fade\" id=\"cancelInProgressModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"cancelInProgressTitle\" aria-hidden=\"true\">
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">You are canceling an in progress request</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">By canceling a request that is in progress you're aware that this will incur a $5 cancellation fee.
                                                                Are you sure you wish to proceed with the cancellation of this request?
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <a href=\"$this->root_path/home/?navbar=active_home&d_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\"
                                               class='btn btn-primary'>
                                                Cancel Request
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";
                                } else { // user has not been approved yet
                                    //                        echo "<p>Respond</p>";
                                    echo "<!-- Button trigger modal -->
                                          <div class='float-right d-inline'>
                                            <div style='cursor: pointer;' class='text-success d-inline' data-toggle=\"modal\" data-target=\"#acceptModal\">
                                            Accept</div> | ";
                                    echo "<a href=\"$this->root_path/components/notifications/?navbar=active_notifications&reject_customer_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE=You've rejected this freelancer for this task! They've been notified!\" class='text-danger'>
                                            Reject</a></div>";

                                    echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                        <a href=\"$this->root_path/home/?navbar=active_home&d_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\" class='mt-3 text-danger'>
                                            Cancel Request</a>
                                      </div>
                                    ";

                                    echo "
                                    <!-- Modal -->
                                    <div class=\"modal fade\" id=\"acceptModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"acceptModalTitle\" aria-hidden=\"true\">
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">You are accepting $freelancer_first_name</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">By accepting this freelancer you are affirming that you have adequately reviewed their profile and qualifications. By accepting this freelancer you will also share sensitive information with them which may include location and contact information.</div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_customer_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE=You've approved this freelancer for this task! They're on their way to complete your FAVR!\"
                                               class='btn btn-primary'>
                                                Accept Freelancer
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";
                                }
                            }
                        }

                        echo "
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                    echo "</div>";
                }

                // friend request notifications
                foreach ($rows1 as $row1) {
                    $requesterID = $row1['uid'];
                    $requesterInfo = $this->getUserInfo($row1['uid']);
                    $requesterFullName = $requesterInfo['first_name'] . " " . $requesterInfo['last_name'];
                    $requesterUsername = $requesterInfo['username'];
                    $requesterPic = unserialize($requesterInfo['profile_picture_path']);
                    if (isset($requesterPic['name'], $requesterPic['type'])) {
                        $profile_img_type = $requesterPic['type'];
                        $profile_img_name = $requesterPic['name'];
                    } else {
                        $profile_img_name = "";
                        $profile_img_type = "";
                    }

                    echo "
                    <div class=\"my-3 p-3 bg-white rounded box-shadow\">
                        <div class='pb-2 mb-0 border-bottom border-gray'>
                            <a href='$this->root_path/components/profile/profile.php?id=$requesterID'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                data-src=\"$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                            </a>   
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                <a href='$this->root_path/components/profile/profile.php?id=$requesterID'>
                                    @$requesterUsername
                                </a>
                            </strong>
                            <div class=\"float-right text-muted small\" style=\"padding-top: .3rem;\">
                                <i class='material-icons'>person_add</i>
                            </div>
                        </div>
                        <div class='media text-muted pt-3'>
                            <div class='container'>
                                <p class='media-body text-dark small lh-125'>$requesterFullName is asking to be friends on FAVR.</p>
                                <div class='row p-0 border-top border-gray'>
                                    <div class='col-sm-12 small'>
                                        <div class='float-left d-inline'>
                                            <a href='$this->root_path/components/profile/profile.php?id=$requesterID'>
                                                View Profile
                                            </a>
                                        </div>
                                        <div class='float-right d-inline'>
                                            <a href='$this->root_path/components/notifications/?navbar=active_notifications&add_friend=true&id=$requesterID&ALERT_MESSAGE=You are now friends on FAVR!' class=\"text-success d-inline\">
                                            Accept</a> |
                                            <a href='$this->root_path/components/notifications/?navbar=active_notifications&add_friend=false&id=$requesterID&ALERT_MESSAGE=You have declined the friend request!' class=\"text-danger d-inline\">
                                            Decline</a> 
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>";
                }

                // friend favr request notifications
                foreach ($rows2 as $row2) {
                    $freelancer_id = $row2['friend_id'];
                    $id = md5($row2['customer_id'] . "-$freelancer_id"); // div id
                    $task_id = $row2['ffrid'];
                    $freelancerInfo = $this->getUserInfo($freelancer_id);
                    $freelancer_username = $freelancerInfo['username'];
                    $freelancer_first_name = $freelancerInfo['first_name'];
                    $freelancer_accepted = $row2['task_freelancer_accepted'];
                    $task_freelancer_count = $row2['task_freelancer_count'];

                    $customer_id = $row2['customer_id'];
                    $customerInfo = $this->getUserInfo($customer_id);
                    $customer_username = $customerInfo['username'];
                    $customer_first_name = $customerInfo['first_name'];
                    $customer_phone = $customerInfo['phone'];

                    $task_description = $row2['task_description'];
                    $task_date = date("n/j/Y", strtotime($row2['task_date']));
                    $task_location = $row2['task_location'];
                    $task_time_to_accomplish = date('h:i A, l, n/j/Y', strtotime($row2['task_date']));
                    $task_price = $row2['favr_price'];
                    $task_difficulty = $row2['task_intensity'];
                    $task_status = $row2['task_status'];

                    $profile_img_data_array = unserialize($customerInfo['profile_picture_path']);

                    if (isset($profile_img_data_array['name'], $profile_img_data_array['type'])) {
                        $profile_img_name = $profile_img_data_array['name'];
                        $profile_img_type = $profile_img_data_array['type'];
                    } else {
                        $profile_img_name = "";
                        $profile_img_type = "";
                    }

                    $task1_img_data_array = unserialize($row2['task_picture_path_1']);
                    $task1_img_name = $task1_img_data_array['name'];
                    $task1_img_type = $task1_img_data_array['type'];

                    $task2_img_data_array = unserialize($row2['task_picture_path_2']);
                    $task2_img_name = $task2_img_data_array['name'];
                    $task2_img_type = $task2_img_data_array['type'];

                    $task3_img_data_array = unserialize($row2['task_picture_path_3']);
                    $task3_img_name = $task3_img_data_array['name'];
                    $task3_img_type = $task3_img_data_array['type'];

                    // hide shrink button and non essential form information

                    echo "<div class=\"my-3 p-3 bg-white rounded box-shadow\">
                        <div class='pb-2 mb-0 border-bottom border-gray'>
                            <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                data-src=\"$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                            </a>   
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                ";

                    if ($customer_id == $_SESSION['user_info']['id']) {
                        echo "<p class='font-weight-light text-muted d-inline-flex'>Accepted by</p> <a href='$this->root_path/components/profile/profile.php?id=$freelancer_id'>@$freelancer_username</a>";
                    } else {
                        echo "<a href='$this->root_path/components/profile/profile.php?id=$customer_id'>@$customer_username</a>";
                    }


                    echo "</strong>
                            ";
                    if (isset($_GET['request_friend_completed_id']) && $task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                        echo "</div>";
                        echo "<div class='mt-3 text-center'>";
                        echo "    <p class=\"small d-inline-flex\">";

                        // rating system 1 - 5 stars + optional review
                        if (isset($_GET['stars'])) {
                            if ($_GET['stars'] == 1) {
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=2'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 2) {
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 3) {
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 4) {
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=3'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            } else if ($_GET['stars'] == 5) {
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=1'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=2'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=3'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=4'><i style=\"color: var(--yellow)\" class=\"material-icons\">star</i></a>";
                                echo "<i style=\"color: var(--yellow)\" class=\"material-icons\">star</i>";
                            }
                        } else {
                            echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=1'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=2'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=3'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=4'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                            echo "<a href='?navbar=active_notifications&request_friend_completed_id=$task_id&stars=5'><i style=\"color: var(--dark)\" class=\"material-icons\">star_border</i></a>";
                        }

                        echo " </p>";
                        echo "</div>";

                        $review = isset($_POST['review']) ? $_POST['review'] : null;
                        $stars = isset($_GET['stars']) ? $_GET['stars'] : 3;
                        // optional review preview
                        if (isset($_POST['review'])) {
                            $review = $_POST['review'];
                            echo "<div class='mt-1 text-center'>
                                    <p class='d-inline-flex text-lg-left'>$review</p> 
                                    <i class='material-icons text-muted text-lg-right'>format_quote</i> 
                                  </div>";
                        }

                        echo "<div class='mt-2 p-2 text-center'>";
                        echo "<!-- Button trigger modal -->
                                <button type=\"button\" class=\"btn btn-primary\" data-toggle=\"modal\" data-target=\"#exampleModalCenter\">
                                  Leave an optional review
                                </button>
                                
                                <!-- Modal -->
                                <div class=\"modal fade\" id=\"exampleModalCenter\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"exampleModalCenterTitle\" aria-hidden=\"true\">
                                  <form action='$this->root_path/components/notifications/?navbar=active_notifications&request_friend_completed_id=$task_id&stars=$stars' method='post'>
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">Leave a review for $freelancer_first_name</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">
                                            <textarea name='review' class='form-control' placeholder='Leave a review of your experience...'>$review</textarea>
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <input type=\"submit\" name='submit' class=\"btn btn-primary\" value='Save Changes'>
                                          </div>
                                        </div>
                                      </div>
                                  </form>
                                </div>";

                        echo "  <div class='mt-3 border-top border-gray pt-3'>
                                    <div class='d-inline-flex text-center'>
                                        <form action='$this->root_path/components/notifications/?navbar=active_notifications&completed_friend_request_id=$task_id&stars=$stars' method='post'>
                                            <input type='hidden' name='request_rating' value='$stars' required>
                                            <input type='hidden' name='friend_id' value='$freelancer_id' required>
                                            <input type='hidden' name='customer_id' value='$customer_id' required>
                                            <input type='hidden' name='request_review' value='$review'>
                                            <input class='btn btn-sm btn-outline-success'
                                                type='submit'
                                                name='complete_friend_request'
                                                value='Submit' />
                                        </form>
                                    </div>
                                </div>";
                        echo "</div>";
                    } else {

                        if (isset($task_difficulty) && $customer_id != $_SESSION['user_info']['id']) {
                            if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_EASY) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-success p-1 rounded\" style='opacity: .9' value=\"Easy\" disabled>Easy 👌</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_MEDIUM) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-warning p-1 rounded\" style='opacity: .9' value=\"Medium\" disabled>Medium 💪🏿</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_HARD) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-danger p-1 rounded\" style='opacity: .9' value=\"Hard\" disabled>Hard 🔥</button>";
                            }
                        }

                        if ($customer_id == $_SESSION['user_info']['id']) {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--red)'>- $task_price FAVR</div>";
                        } else {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--green)'>+ $task_price FAVR</div>";
                        }

                        echo "</div><div class=\"media text-muted pt-3\">
                        <div class='container'>
                            <p id='$id' class=\"media-body text-dark mb-0 small lh-125\">
                                $task_description
                                <div id='$id-location' class='pt-1 border-top small border-gray d-none'>
                                    <label for='location'>Location:</label>";

                        // TODO: add contact info for customer and freelancer
                        // share location of customer to freelancer if task is in progress or paid for
                        if ($task_status == Data_Constants::DB_TASK_STATUS_PAID || $task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                            echo "<p class='text-dark'>$task_location</p>";
                            echo "<div class='pt-1 border-top border-bottom border-gray'>
                                    <label for='contact'>Contact:</label>
                                    <p class='text-dark'>$customer_phone</p>
                                  </div>";
                        } else {
                            echo "<!-- TODO: calculate location distance by zipcode -->
                                    <p class='text-dark'>Rochester, MN</p>";
                        }

                        echo "
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
                                    <img id='$id-img1' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task1_img_name&i_t=$task1_img_type' height='30%' width='30%' alt='FAVR image 1'>
                                </div>";
                        // Image 1 modal
                        echo "
                            <div id=\"$id-image1-modal\" class=\"modal\">
                              <span id='$id-close1' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image1-modal-content\">
                              <div id=\"$id-caption1\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image2' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image2'>Attached Image 2:</label>
                                    <img id='$id-img2' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task2_img_name&i_t=$task2_img_type' height='30%' width='30%' alt='FAVR image 2'>
                                </div>";
                        // Image 2 modal
                        echo "
                            <div id=\"$id-image2-modal\" class=\"modal\">
                              <span id='$id-close2' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image2-modal-content\" alt='FAVR image 2'>
                              <div id=\"$id-caption2\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image3' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image3'>Attached Image 3:</label>
                                    <img id='$id-img3' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                    data-src='$this->root_path/image.php?i=$task3_img_name&i_t=$task3_img_type' height='30%' width='30%' alt='FAVR image 3'>
                                </div>";
                        // Image 3 modal
                        echo "
                            <div id=\"$id-image3-modal\" class=\"modal\">
                              <span id='$id-close3' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image3-modal-content\">
                              <div id=\"$id-caption3\" class='caption'></div>
                            </div>";
                        echo "
                            </p>
                            <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                        <div id='$id-expand' class='text-info d-inline-flex'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('#$id').animate({height: '4rem'});
                                              $('#$id-location').removeClass('d-none');
                                              $('#$id-freelancer-count').removeClass('d-none');
                                              $('#$id-collapse').removeClass('d-none');
                                              $('#$id-collapse').addClass('d-inline-flex');
                                              $('#$id-expand').removeClass('d-inline-flex');
                                              $('#$id-expand').addClass('d-none');";

                        for ($i = 1; $i <= Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                            echo "
                            var modal = document.getElementById('$id-image$i-modal');
                            var img = document.getElementById('$id-img$i');
                            var modalImg = document.getElementById('$id-image$i-modal-content');
                            var captionText = document.getElementById('$id-caption$i');
                            img.onclick = function(){
                                modal.style.display = 'block';
                                modalImg.src = this.src;
                                captionText.innerHTML = this.alt;
                            }
                            var span = document.getElementById('$id-close$i');
                            span.onclick = function() { 
                                modal.style.display = 'none';
                            }
                         ";
                        }

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
                                        \">Collapse</div>
                                        ";


                        echo "
                                    </div>
                                       ";

                        // Customer and freelancer request actions
                        if ($customer_id != $_SESSION['user_info']['id']) { // if not this user

                            if ($task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL || $task_status == Data_Constants::DB_TASK_STATUS_REQUESTED) {
                                echo "<div class='float-right d-inline'>
                                <p class='d-inline-flex mb-1'>
                                Status: You Accepted</p>
                              </div>";
                            } else {
                                echo "<div class='float-right d-inline'>
                                    <p class='d-inline-flex mb-1'>
                                    ";
                                // TODO: use class defined constants here to validate verified freelancers from regular customers
                                if ($task_status == Data_Constants::DB_TASK_STATUS_PAID && $customer_id != $_SESSION['user_info']['id']) {
                                    echo "Status: Go to location";
                                } else {
                                    echo "Status: $task_status";
                                }

                                echo "  </p>
                                  </div>";
                            }

                            if ($task_status == Data_Constants::DB_TASK_STATUS_PAID) {
                                echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                        <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&friend_arrived=true&arrived_friend_request_id=$task_id&ALERT_MESSAGE=You've arrived! Make sure you're at the correct location and that the customer is who they say they are!\" class='text-success'>
                                        Freelancer Arrived</a>
                                      </div>
                                    ";

                                echo "<div class='d-block mt-2 pt-2 border-gray border-top text-center'>
                                    <a class='text-danger' href=\"$this->root_path/components/notifications/?navbar=active_notifications&withdraw_friend_request_id=$task_id&friend_id=$freelancer_id&ALERT_MESSAGE=You've withdrawn from this task: the customer has been notified!\">
                                    Withdraw From Task</a>
                                  </div>";
                            } else {
                                echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                    <a class='text-danger' href=\"$this->root_path/components/notifications/?navbar=active_notifications&withdraw_friend_request_id=$task_id&friend_id=$freelancer_id&ALERT_MESSAGE=You've withdrawn from this task: the customer has been notified!\">
                                    Withdraw From Task</a>
                                  </div>";
                            }
                        } else {
                            $select_freelancers_query = "SELECT * 
                                                     FROM friends_favr_requests
                                                     WHERE id = $task_id
                                                     AND friend_id = $freelancer_id";
                            $result = $this->db->query($select_freelancers_query);
                            $row2 = $result->fetch(PDO::FETCH_ASSOC);
                            if (!empty($row2)) {
                                if ($row2['approved'] == 1) { // this user is approved
                                    echo "<div class='float-right d-inline'>
                                        <p class='d-inline-flex mb-1'>
                                        ";
                                    // TODO: use class defined constants here to validate verified freelancers from regular customers
                                    if ($task_status == Data_Constants::DB_TASK_STATUS_PAID && $customer_id == $_SESSION['user_info']['id']) {
                                        echo "Status: Help en-route";
                                    } else {
                                        echo "Status: $task_status";
                                    }

                                    echo "  </p>
                                      </div>";

                                    if ($task_status == Data_Constants::DB_TASK_STATUS_IN_PROGRESS) {
                                        echo "<div class='d-block mt-4 pt-2 mb-0 pt-2 border-gray border-top text-center'>
                                            <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&request_friend_completed_id=$task_id&ALERT_MESSAGE=You've marked this request as completed now please rate your experience!\" class='text-success'>
                                                 Request Completed</a>
                                          </div>
                                    ";

                                        echo "<div class='d-block mt-2 pt-2 border-gray border-top text-center'>";
                                    } else {
                                        echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>";
                                    }


                                    if ($task_status == Data_Constants::DB_TASK_STATUS_REQUESTED || $task_status == Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL) {
                                        echo "<a href=\"?navbar=active_home&nav_scroller=active_friends&d_friend_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
                                            Cancel Request</a>";
                                    } else {
                                        echo "<div style='cursor: pointer;' class='text-danger d-inline' data-toggle=\"modal\" data-target=\"#cancelInProgressModal\">
                                            Cancel Request</div>
                                        ";
                                    }
                                    echo "</div>";

                                    echo "
                                    <!-- Modal -->
                                    <div class=\"modal fade\" id=\"cancelInProgressModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"cancelInProgressTitle\" aria-hidden=\"true\">
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">You are canceling an in progress friend FAVR</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">By canceling a request that is in progress you're aware that this will invalidate any and all FAVRS owed to you by this individual.
                                                                Are you sure you wish to proceed with the cancellation of this request?
                                          </div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <a href=\"$this->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends&d_friend_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\"
                                               class='btn btn-primary'>
                                                Cancel Request
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";
                                } else { // user has not been approved yet
                                    //                        echo "<p>Respond</p>";
                                    echo "<!-- Button trigger modal -->
                                          <div class='float-right d-inline'>
                                            <div style='cursor: pointer;' class='text-success d-inline' data-toggle=\"modal\" data-target=\"#acceptModal\">
                                            Accept</div> | ";
                                    echo "<a href=\"$this->root_path/components/notifications/?navbar=active_notifications&reject_customer_friend_request_id=$task_id&friend_id=$freelancer_id&ALERT_MESSAGE=You've rejected this freelancer for this task! They've been notified!\" class='text-danger'>
                                            Reject</a></div>";

                                    echo "<div class='d-block mt-4 pt-2 border-gray border-top text-center'>
                                        <a href=\"$this->root_path/home/friends/?navbar=active_home&d_friend_request_id=$task_id&ALERT_MESSAGE=You've cancelled this request!\" class='mt-3 text-danger'>
                                            Cancel Request</a>
                                      </div>
                                    ";

                                    echo "
                                    <!-- Modal -->
                                    <div class=\"modal fade\" id=\"acceptModal\" tabindex=\"-1\" role=\"dialog\" aria-labelledby=\"acceptModalTitle\" aria-hidden=\"true\">
                                      <div class=\"modal-dialog\" role=\"document\">
                                        <div class=\"modal-content\">
                                          <div class=\"modal-header\">
                                            <h5 class=\"modal-title\" id=\"exampleModalLongTitle\">You are accepting $freelancer_first_name</h5>
                                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                              <span aria-hidden=\"true\">&times;</span>
                                            </button>
                                          </div>
                                          <div class=\"modal-body\">By accepting this freelancer you are affirming that you have adequately reviewed their profile and qualifications. By accepting this freelancer you will also share sensitive information with them which may include location and contact information.</div>
                                          <div class=\"modal-footer\">
                                            <button type=\"button\" class=\"btn btn-secondary\" data-dismiss=\"modal\">Close</button>
                                            <a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_customer_friend_request_id=$task_id&friend_id=$freelancer_id&ALERT_MESSAGE=You've approved this freelancer for this task! They're on their way to complete your FAVR!\"
                                               class='btn btn-primary'>
                                                Accept Freelancer
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>";
                                }
                            }
                        }

                        echo "
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                    echo "</div>";
                }
            } else {
                echo "<p class='p-3 text-muted'>No notifications at the moment!</p>";
                return false;
            }

            return true;
        }

    }

    /**
     * Render request favr web and mobile form
     *
     * @param boolean $render_favr_request_form
     *
     * @return boolean
     */
    function renderFavrRequestForm($render_favr_request_form = true)
    {
        if ($render_favr_request_form) {
            $userInfo = $this->getUserInfo($_SESSION['user_info']['id']);
            $userID = $userInfo['id'];
            $profile_img = unserialize($userInfo['profile_picture_path']);

            if (isset($profile_img['name'], $profile_img['type'])) {
                $profile_img_name = $profile_img['name'];
                $profile_img_type = $profile_img['type'];
            } else {
                $profile_img_name = "";
                $profile_img_type = "";
            }
            ?>
            <div class="p-3 text-center request-favr-web">
                <button class="btn btn-lg btn-primary" id="request-favr-web">
                    <div class="d-inline-flex">
                        <i class="material-icons">build</i>
                        Request FAVR
                    </div>
                </button>
            </div>

            <div class="favr-fab">
                <a class="favr-fab-fab favr-fab-btn-large text-center" id="favr-fabBtn">
                    <i style="padding: .8rem;background: transparent;color: var(--white);font-size: xx-large" class="material-icons">build</i>
                </a>
            </div>

            <form class="request-favr-mobile" action="" method="post" enctype="multipart/form-data">
                <div class="my-3 p-3 bg-white rounded box-shadow">
                    <h6 class="border-bottom border-gray pb-2 mb-0">Post a FAVR request in Marketplace</h6>
                    <div class="media text-muted pt-3">
                        <a href='<?php echo "$this->root_path/components/profile/profile.php?id=$userID"; ?>'>
                            <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'
                                 data-src='<?php echo "$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true"; ?>' height='32' width='32' alt='Profile Image' class='mr-2 rounded'>
                        </a>
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
                                       placeholder="When do you want your FAVR?" value="<?php echo date("Y-m-d\TH:i", time()); ?>" required="">
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
                            <label for="inputPricing">Price (for each freelancer)</label>
                            <div class="input-group pb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text" style="color: var(--green)">$</span>
                                </div>
                                <input type="number" name="requestPrice" id="inputPricing"
                                       class="form-control"
                                       style="border-radius: 0 5px 5px 0"
                                       placeholder="Set your price ..." min="0.50" max="250.00" step="0.01" required="">
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
     * Render request favr web and mobile form for friends sub section
     *
     * @param boolean $render_favr_request_form
     * @param int $targetUserID
     *
     * @return boolean
     */
    function renderFavrFriendsRequestForm($render_favr_request_form = true, $targetUserID = null)
    {
        if ($render_favr_request_form) {
            if ($targetUserID != null) {
                $user_id = $targetUserID;
                $friend_id = $_SESSION['user_info']['id'];
                $isFriends = $this->assertFriends($user_id, $friend_id);
                if ($isFriends) {
                    // allow this user to ask a direct FAVR to the friend
                    $friendInfo = $this->getUserInfo($user_id);
                    $friendFullName = $friendInfo['first_name'] . " " . $friendInfo['last_name'];

                    $userInfo = $this->getUserInfo($_SESSION['user_info']['id']);
                    $userID = $userInfo['id'];
                    $profile_img = unserialize($userInfo['profile_picture_path']);

                    if (isset($profile_img['name'], $profile_img['type'])) {
                        $profile_img_name = $profile_img['name'];
                        $profile_img_type = $profile_img['type'];
                    } else {
                        $profile_img_name = "";
                        $profile_img_type = "";
                    }
                    ?>
                    <form action="<?php echo "$this->root_path/components/notifications/?navbar=active_notifications&ask_favr=true&id=$targetUserID"; ?>" method="post" enctype="multipart/form-data">
                        <div class="my-3 p-3 bg-white rounded box-shadow">
                            <h6 class="border-bottom border-gray pb-2 mb-0">Asking <?php echo $friendFullName; ?> for a FAVR</h6>
                            <div class="media text-muted pt-3">
                                <a href='<?php echo "$this->root_path/components/profile/profile.php?id=$userID"; ?>'>
                                    <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'
                                         data-src='<?php echo "$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true"; ?>' height='32' width='32' alt='Profile Image' class='mr-2 rounded'>
                                </a>
                                <div class="media-body pb-3 mb-0 small lh-125">
                                    <strong class="d-block text-gray-dark">@<?php echo $_SESSION['user_info']['username']; ?></strong>
                                    <div class="form-label-group">
                                        <textarea name="requestTaskDescription" class="form-control" placeholder="What is your task?"></textarea>
                                    </div>
                                    <div class="form-label-group">
                                        <input type="datetime-local" name="requestDate" id="inputDate"
                                               class="form-control"
                                               placeholder="When do you want your FAVR?" value="<?php echo date("Y-m-d\TH:i", time()); ?>" required="">
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
                                    <label for="inputPricing">Price (A future FAVR instead of cash 😉)</label>
                                    <div class="input-group pb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" style="color: var(--red)">FAVR</span>
                                        </div>
                                        <input type="number" name="requestPrice" id="inputPricing"
                                               class="form-control"
                                               style="border-radius: 0 5px 5px 0"
                                               value="1" min="1" max="5" required="">
                                    </div>
                                    <label for="inputPictures">Only image files < 5 Mb can be attached</label>
                                    <div class="form-label-group">
                                        <input type="file" name="requestPictures[]" id="inputPictures"
                                               class="form-control"
                                               placeholder="Attach picture(s)" multiple>
                                        <label for="inputPictures">Attach picture(s): at most 3...</label>
                                    </div>
                                    <input type="submit" name="requestFavr" class="btn btn-lg btn-primary btn-block"
                                           value="Request FAVR">
                                </div>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            } else {
                $userInfo = $this->getUserInfo($_SESSION['user_info']['id']);
                $userID = $userInfo['id'];
                $profile_img = unserialize($userInfo['profile_picture_path']);

                if (isset($profile_img['name'], $profile_img['type'])) {
                    $profile_img_name = $profile_img['name'];
                    $profile_img_type = $profile_img['type'];
                } else {
                    $profile_img_name = "";
                    $profile_img_type = "";
                }
                ?>
                <div class="p-3 text-center request-favr-web">
                    <button class="btn btn-lg btn-dark" id="request-favr-web">
                        <div class="d-inline-flex">
                            <i class="material-icons">build</i>
                            Request FAVR
                        </div>
                    </button>
                </div>

                <div class="favr-fab">
                    <a class="favr-fab-fab favr-fab-btn-large bg-dark text-center" id="favr-fabBtn">
                        <i style="padding: .8rem;background: transparent;color: var(--white);font-size: xx-large" class="material-icons">build</i>
                    </a>
                </div>

                <form id="favr-friends" class="request-favr-mobile" action="" method="post" enctype="multipart/form-data">
                    <div class="my-3 p-3 bg-white rounded box-shadow">
                        <h6 class="border-bottom border-gray pb-2 mb-0">Post a FAVR request for help from Friends</h6>
                        <div class="media text-muted pt-3">
                            <a href='<?php echo "$this->root_path/components/profile/profile.php?id=$userID"; ?>'>
                                <img src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E'
                                     data-src='<?php echo "$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true"; ?>' height='32' width='32' alt='Profile Image' class='mr-2 rounded'>
                            </a>
                            <div class="media-body pb-3 mb-0 small lh-125">
                                <strong class="d-block text-gray-dark">@<?php echo $_SESSION['user_info']['username']; ?></strong>
                                <div class="form-label-group">
                                    <textarea name="requestTaskDescription" class="form-control" placeholder="What is your task?"></textarea>
                                </div>
                                <div class="form-label-group">
                                    <input type="datetime-local" name="requestDate" id="inputDate"
                                           class="form-control"
                                           placeholder="When do you want your FAVR?" value="<?php echo date("Y-m-d\TH:i", time()); ?>" required="">
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
                                <label for="inputPricing">Price (A future FAVR instead of cash 😉)</label>
                                <div class="input-group pb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text" style="color: var(--red)">FAVR</span>
                                    </div>
                                    <input type="number" name="requestPrice" id="inputPricing"
                                           class="form-control"
                                           style="border-radius: 0 5px 5px 0"
                                           value="1" min="1" max="5" required="">
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
        }

        return $render_favr_request_form;
    }

    /**
     * Render friends list
     *
     * @param int $userId
     *
     */
    function renderFriendList($userId)
    {
        if ($userId == $_SESSION['user_info']['id']) {
            $userInfo = $this->getUserInfo($userId);
            $userFullName = $userInfo['first_name'] . " " . $userInfo['last_name'];

            $select_friends_query = "SELECT *
                                     FROM friends
                                     WHERE user_id = $userId";
            $result = $this->db->query($select_friends_query);
            if ($result) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                if (!empty($rows)) {
                    $friend_count = count($rows);
                    ?>
                    <div class="my-3 p-3 bg-white rounded box-shadow">
                        <h6 class="border-bottom border-gray pb-2 mb-0">
                            Friends of <?php echo $userFullName; ?>
                            <p class="d-inline-flex small font-weight-light">(<?php echo $friend_count; ?>)</p>
                        </h6>
                        <?php
                            foreach ($rows as $row) {
                                $friend_id = $row['friend_id'];
                                $friendInfo = $this->getUserInfo($friend_id);
                                if (!empty($friendInfo)) {
                                    $friendFullName = $friendInfo['first_name'] . " " . $friendInfo['last_name'];
                                    $friendUsername = $friendInfo['username'];
                                    $friendProfilePicture = unserialize($friendInfo['profile_picture_path']);
                                    if (isset($friendProfilePicture['name'], $friendProfilePicture['type'])) {
                                        $friendPictureName = $friendProfilePicture['name'];
                                        $friendPictureType = $friendProfilePicture['type'];
                                    } else {
                                        $friendPictureName = "";
                                        $friendPictureType = "";
                                    }

                                    $check_friend_query = "SELECT given, received, COUNT(*)  
                                                           FROM friends 
                                                           WHERE user_id = $friend_id
                                                           AND friend_id = $userId";
                                    $result = $this->db->query($check_friend_query);
                                    $crow = $result->fetch(PDO::FETCH_ASSOC);
                                    ?>
                                    <div class="media text-muted pt-3">
                                        <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_id"; ?>">
                                            <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                                 data-src="<?php echo "$this->root_path/image.php?i=$friendPictureName&i_t=$friendPictureType&i_p=true"; ?>" height="32" width="32" alt="" class="mr-2 rounded">
                                        </a>
                                        <div class="media-body pb-3 mr-0 mb-0 small lh-125 border-bottom border-gray">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <strong class="text-gray-dark"><?php echo $friendFullName; ?></strong>
                                                <?php
                                                    if ($crow['COUNT(*)'] != 0) {
                                                        $userFavrGiven = $crow['given'];
                                                        $userFavrReceived = $crow['received'];

                                                        if ($userFavrGiven >= $userFavrReceived) {
                                                            echo "<a href=\"$this->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends&ask_favr=true&id=$friend_id\"
                                                                class=\"float-right\">Ask FAVR</a>";
                                                        }
                                                    }
                                                ?>
                                            </div>
                                            <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_id"; ?>">
                                                <span class="d-block">@<?php echo $friendUsername; ?></span>
                                            </a>
                                            <br>
                                            <div class="d-flex justify-content-center align-items-center w-100">
                                                <?php
                                                if ($crow['COUNT(*)'] != 0) {
                                                    ?>
<!--                                                    <a href="#"-->
<!--                                                       class="text-info mr-1 pr-1">Block</a> |-->
                                                    <a href="<?php echo "$this->root_path/home/friends/?friends_list=true&add_friend=false&id=$friend_id&ALERT_MESSAGE=You have unfriended!"; ?>"
                                                       class="text-danger">Unfriend</a>
                                                    <?php
                                                } else {
                                                    ?>
                                                    <a href="<?php echo "$this->root_path/home/friends/?friends_list=true&add_friend=false&id=$friend_id&ALERT_MESSAGE=You have cancelled your friend request!"; ?>"
                                                       class="text-danger">Cancel Friend Request</a>
                                                    <?php
                                                }
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        ?>
                    </div>
                    <?php
                } else {
                    // this user has no friends go ahead and suggest to them friends they can add
                    $this->renderFriendSuggestions($userId);
                }
            }
        }
    }

    /**
     * Render friends suggestions
     *
     * @param int $userId
     *
     */
    function renderFriendSuggestions($userId)
    {
        $select_friends_query = "SELECT * 
                                 FROM friends 
                                 WHERE user_id = $userId
                                 ORDER BY RAND()
                                 LIMIT 3";
        $result = $this->db->query($select_friends_query);
        if ($result) {
            ?>
            <div class="my-3 p-3 bg-white rounded box-shadow" style="width: 100%;">
                <h6 class="border-bottom border-gray pb-2 mb-0">Friendly Suggestions</h6>
            <?php
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            $count = count($rows);
            if (!empty($rows) && $count > 5) {
                foreach ($rows as $row) {
                    $friend_id = $row['friend_id'];
                    $select_friends_of_friend_query = "
                                                 SELECT * 
                                                 FROM friends
                                                 WHERE friend_id = $friend_id
                                                 AND NOT user_id = $userId
                                                 ORDER BY RAND()
                                                 LIMIT 1";
                    $result = $this->db->query($select_friends_of_friend_query);
                    if ($result) {
                        $ffrow = $result->fetch(PDO::FETCH_ASSOC);
                        if (!empty($ffrow)) {
                            $friend_of_friend_id = $ffrow['user_id'];

                            $check_friend_query = "SELECT COUNT(*) 
                                                   FROM friends
                                                   WHERE user_id = $userId
                                                   AND friend_id = $friend_of_friend_id";

                            $result = $this->db->query($check_friend_query);
                            if ($result) {
                                $crow = $result->fetch(PDO::FETCH_ASSOC);
                                if ($crow['COUNT(*)'] == 0) {
                                    // if these users are not already friends then suggest this friend
                                    $friendOfFriendInfo = $this->getUserInfo($friend_of_friend_id);
                                    $friendOfFriendFullName =  $friendOfFriendInfo['first_name'] . " " . $friendOfFriendInfo['last_name'];
                                    $friendOfFriendUsername = $friendOfFriendInfo['username'];
                                    $friendOfFriendProfilePicPath = $friendOfFriendInfo['profile_picture_path'];
                                    if (isset($friendOfFriendProfilePicPath['name'], $friendOfFriendProfilePicPath['type'])) {
                                        $picName = $friendOfFriendProfilePicPath['name'];
                                        $picType = $friendOfFriendProfilePicPath['type'];
                                    } else {
                                        $picName = "";
                                        $picType = "";
                                    }
                                    ?>
                                    <div class="media text-muted pt-3">
                                        <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_of_friend_id"; ?>">
                                            <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                                 data-src="<?php echo "$this->root_path/image.php?i=$picName&i_t=$picType&i_p=true"; ?>" height="32" width="32" alt="" class="mr-2 rounded">
                                        </a>
                                        <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <strong class="text-gray-dark"><?php echo $friendOfFriendFullName; ?></strong>
                                                <a href="#">Send Friend Request</a>
                                            </div>
                                            <span class="d-block">
                                                <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_of_friend_id"; ?>">
                                                    @<?php echo $friendOfFriendUsername; ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            } else {
                // No matches for friends were found or user has no friends suggest friends within same zip code hints for using favr
                $userInfo = $this->getUserInfo($userId);
                $userZip = $userInfo['zip'];
                // only public users are discoverable
                $public = Data_Constants::DB_SCOPE_PUBLIC;

                $select_friends_query = "SELECT *, u.id as uid
                                         FROM users u
                                         WHERE zip = '$userZip'
                                         AND NOT id = $userId
                                         AND default_scope = '$public'
                                         ORDER BY RAND()
                                         LIMIT 3
                                         ";
                $result = $this->db->query($select_friends_query);
                if ($result) {
                    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                    if (empty($rows)) {
                        ?>
                        <p>
                            You can trade FAVRs with friends instead of paying cash!
                        </p>
                        <?php
                    } else {
                        foreach ($rows as $row) {
                            // if these users are not already friends then suggest this friend
                            $friend_of_friend_id = $row['uid'];

                            $check_friend_query = "SELECT COUNT(*) 
                                               FROM friends
                                               WHERE user_id = $userId
                                               AND friend_id = $friend_of_friend_id
                                               OR user_id = $friend_of_friend_id
                                               AND friend_id = $userId";

                            $result = $this->db->query($check_friend_query);
                            if ($result) {
                                $crow = $result->fetch(PDO::FETCH_ASSOC);
                                if ($crow['COUNT(*)'] == 0) {
                                    $friendOfFriendFullName =  $row['first_name'] . " " . $row['last_name'];
                                    $friendOfFriendUsername = $row['username'];
                                    $friendOfFriendProfilePicPath = unserialize($row['profile_picture_path']);
                                    if (isset($friendOfFriendProfilePicPath['name'], $friendOfFriendProfilePicPath['type'])) {
                                        $picName = $friendOfFriendProfilePicPath['name'];
                                        $picType = $friendOfFriendProfilePicPath['type'];
                                    } else {
                                        $picName = "";
                                        $picType = "";
                                    }
                                    ?>
                                    <div class="media text-muted pt-3">
                                        <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_of_friend_id"; ?>">
                                            <img src="data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E"
                                                 data-src="<?php echo "$this->root_path/image.php?i=$picName&i_t=$picType&i_p=true"; ?>" height="32" width="32" alt="" class="mr-2 rounded">
                                        </a>
                                        <div class="media-body pb-3 mb-0 small lh-125 border-bottom border-gray">
                                            <div class="d-flex justify-content-between align-items-center w-100">
                                                <strong class="text-gray-dark"><?php echo $friendOfFriendFullName; ?></strong>
                                                <?php
                                                    if ($_SESSION['navbar'] == "friends_list") {
                                                        $last_url = "$this->root_path/home/friends/?friends_list=true&add_friend=true&id=$friend_of_friend_id&ALERT_MESSAGE=Your friend request has been sent!";
                                                    } else {
                                                        $last_url = "$this->root_path/home/friends/?navbar=active_home&nav_scroller=active_friends&add_friend=true&id=$friend_of_friend_id&ALERT_MESSAGE=Your friend request has been sent!";
                                                    }
                                                ?>
                                                <a href="<?php echo $last_url; ?>">Send Friend Request</a>
                                            </div>
                                            <span class="d-block">
                                                <a href="<?php echo "$this->root_path/components/profile/profile.php?id=$friend_of_friend_id"; ?>">
                                                    @<?php echo $friendOfFriendUsername; ?>
                                                </a>
                                            </span>
                                        </div>
                                    </div>
                                    <?php
                                }
                            }
                        }
                    }
                }
            }
            ?>
            </div>
            <?php
        }
    }

    /**
     * Render marketplace favr request feed to home
     *
     * @param mixed $scope // default string otherwise an int
     * @param string $orderBy
     * @param string $orientation
     * @param string $limit
     *
     * @return boolean
     */
    function renderFavrFriendsMarketplace($scope="global", $orderBy = "task_date", $orientation = "DESC", $limit="LIMIT 3")
    {
        if ($scope == $_SESSION['user_info']['id']) {
            $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
            $selectMarketplaceQuery = "
                                   SELECT *, ffr.id as ffrid
                                   FROM friends_favr_requests ffr
                                   INNER JOIN users u
                                   WHERE u.id = $scope
                                   AND u.id = ffr.customer_id
                                   AND ffr.task_status = '$requested'
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";
        } else if ($scope == "global") {
            $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
            $selectMarketplaceQuery = "
                                   SELECT *, ffr.id as ffrid
                                   FROM friends_favr_requests ffr 
                                   INNER JOIN users u
                                   WHERE u.id = ffr.customer_id
                                   AND ffr.task_status = '$requested'
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
                    $id = md5($row['ffrid']);
                    $task_id = $row['ffrid'];
                    $freelancer_accepted = $row['task_freelancer_accepted'];
                    $task_freelancer_count = $row['task_freelancer_count'];
                    $customer_id = $row['customer_id'];

                    $isFriends = $this->assertFriends($customer_id, $_SESSION['user_info']['id']);
                    if ($isFriends || $customer_id == $_SESSION['user_info']['id']) {

                        $customerInfo = $this->getUserInfo($customer_id);
                        $customer_username = $customerInfo['username'];
                        $customer_first_name = $customerInfo['first_name'];
                        $task_description = $row['task_description'];
                        $task_date = date("n/j/Y", strtotime($row['task_date']));
                        $task_location = $row['task_location'];
                        $task_time_to_accomplish = date('h:i A, l, n/j/Y', strtotime($row['task_date']));
                        $task_price = $row['favr_price'];
                        $task_difficulty = $row['task_intensity'];

                        $profile_img_data_array = unserialize($customerInfo['profile_picture_path']);

                        if (isset($profile_img_data_array['name'], $profile_img_data_array['type'])) {
                            $profile_img_name = $profile_img_data_array['name'];
                            $profile_img_type = $profile_img_data_array['type'];
                        } else {
                            $profile_img_name = "";
                            $profile_img_type = "";
                        }

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
                            <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                data-src=\"$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                            </a>
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>@$customer_username</a>
                            </strong>
                            ";

                        if (isset($task_difficulty)) {
                            if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_EASY) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-success p-1 rounded\" style='opacity: .9' value=\"Easy\" disabled>Easy 👌</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_MEDIUM) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-warning p-1 rounded\" style='opacity: .9' value=\"Medium\" disabled>Medium 💪🏿</button>";
                            } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_HARD) {
                                echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-danger p-1 rounded\" style='opacity: .9' value=\"Hard\" disabled>Hard 🔥</button>";
                            }
                        }

                        if ($customer_id == $_SESSION['user_info']['id']) {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--red)'>- $task_price FAVR</div>";
                        } else {
                            echo "<div class='float-right small' style='padding-top: .3rem;color: var(--dark)'>$task_price FAVR</div>";
                        }

                        echo "</div><div class=\"media text-muted pt-3\">
                        <div class='container'>
                            <p id='$id' class=\"media-body text-dark mb-0 small lh-125\">
                                $task_description
                                <div id='$id-location' class='pt-1 border-top small border-gray d-none'>
                                    <label for='location'>Location:</label>
                                    <!-- TODO: calculate location distance by zipcode -->
                                    <p class='text-dark'>Rochester, MN</p>
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
                                    <img id='$id-img1' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task1_img_name&i_t=$task1_img_type' height='30%' width='30%' alt='FAVR image 1'>
                                </div>";
                        // Image 1 modal
                        echo "
                            <div id=\"$id-image1-modal\" class=\"modal\">
                              <span id='$id-close1' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image1-modal-content\">
                              <div id=\"$id-caption1\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image2' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image2'>Attached Image 2:</label>
                                    <img id='$id-img2' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task2_img_name&i_t=$task2_img_type' height='30%' width='30%' alt='FAVR image 2'>
                                </div>";
                        // Image 2 modal
                        echo "
                            <div id=\"$id-image2-modal\" class=\"modal\">
                              <span id='$id-close2' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image2-modal-content\">
                              <div id=\"$id-caption2\" class='caption'></div>
                            </div>";

                        echo "
                                <div id='$id-image3' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image3'>Attached Image 3:</label>
                                    <img id='$id-img3' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                    data-src='$this->root_path/image.php?i=$task3_img_name&i_t=$task3_img_type' height='30%' width='30%' alt='FAVR image 3'>
                                </div>";
                        // Image 3 modal
                        echo "
                            <div id=\"$id-image3-modal\" class=\"modal\">
                              <span id='$id-close3' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image3-modal-content\">
                              <div id=\"$id-caption3\" class='caption'></div>
                            </div>";

                        echo "
                            </p>
                            <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                        <div id='$id-expand' class='text-info d-inline-flex'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('.favr-fab').fadeOut();
                                              $('#$id').animate({height: '4rem'});
                                              $('#$id-location').removeClass('d-none');
                                              $('#$id-freelancer-count').removeClass('d-none');
                                              $('#$id-collapse').removeClass('d-none');
                                              $('#$id-collapse').addClass('d-inline-flex');
                                              $('#$id-expand').removeClass('d-inline-flex');
                                              $('#$id-expand').addClass('d-none');";

                        for ($i = 1; $i <= Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                            echo "
                            var modal = document.getElementById('$id-image$i-modal');
                            var img = document.getElementById('$id-img$i');
                            var modalImg = document.getElementById('$id-image$i-modal-content');
                            var captionText = document.getElementById('$id-caption$i');
                            img.onclick = function(){
                                modal.style.display = 'block';
                                modalImg.src = this.src;
                                captionText.innerHTML = this.alt;
                            }
                            var span = document.getElementById('$id-close$i');
                            span.onclick = function() { 
                                modal.style.display = 'none';
                            }
                         ";
                        }

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
                                              
                                              $('.favr-fab').css({display: ''})
                                        \">Collapse</div> | $task_date
                                        ";

                        echo "
                                    </div>
                                    <div class='float-right d-inline'>
                                       ";

                        if ($customer_id != $_SESSION['user_info']['id']) { // if not this user
                            echo "<a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_friend_request_id=$task_id&ALERT_MESSAGE=You've signed up to take this task! The task requester has been notified of your interest and is reviewing your offer to help: they can accept or reject your offer to help! You'll be notified of their decision; you can withdraw your offer to help before they decide. \">
                            Accept Request</a>";
                        } else {
                            echo "<a href=\"?navbar=active_home&nav_scroller=active_friends&d_friend_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
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
                }
            } else {
                echo "<p class='p-3 text-muted'>No FAVR requests at the moment!</p>";
                return false;
            }

            return true;
        }
    }

    /**
     * Render marketplace favr request feed to home
     *
     * @param mixed $scope // default string otherwise an int
     * @param string $orderBy
     * @param string $orientation
     * @param string $limit
     *
     * @return boolean
     */
    function renderFavrMarketplace($scope="global", $orderBy = "task_date", $orientation = "DESC", $limit="LIMIT 3")
    {
        if ($scope == $_SESSION['user_info']['id']) {

            $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN users u
                                   WHERE u.id = $scope
                                   AND u.id = mfr.customer_id
                                   AND mfr.task_status = '$requested'
                                   ORDER BY $orderBy
                                   $orientation
                                   $limit
            ";

        } else if ($scope == "global") {

            $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
            $selectMarketplaceQuery = "
                                   SELECT *, mfr.id as mfrid
                                   FROM marketplace_favr_requests mfr
                                   INNER JOIN users u
                                   WHERE u.id = mfr.customer_id
                                   AND mfr.task_status = '$requested'
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
                    $freelancer_id = $row['freelancer_id'];
                    $freelancer_accepted = $row['task_freelancer_accepted'];
                    $task_freelancer_count = $row['task_freelancer_count'];
                    $customer_id = $row['customer_id'];
                    $customerInfo = $this->getUserInfo($customer_id);
                    $customer_username = $customerInfo['username'];
                    $customer_first_name = $customerInfo['first_name'];
                    $task_description = $row['task_description'];
                    $task_date = date("n/j/Y", strtotime($row['task_date']));
                    $task_location = $row['task_location'];
                    $task_time_to_accomplish = date('h:i A, l, n/j/Y', strtotime($row['task_date']));
                    $task_price = $row['task_price'];
                    $task_difficulty = $row['task_intensity'];

                    $profile_img_data_array = unserialize($customerInfo['profile_picture_path']);

                    if (isset($profile_img_data_array['name'], $profile_img_data_array['type'])) {
                        $profile_img_name = $profile_img_data_array['name'];
                        $profile_img_type = $profile_img_data_array['type'];
                    } else {
                        $profile_img_name = "";
                        $profile_img_type = "";
                    }

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
                            <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>
                                <img src=\"data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E\" 
                                data-src=\"$this->root_path/image.php?i=$profile_img_name&i_t=$profile_img_type&i_p=true\" height='32' width='32' alt=\"Profile Image\" class=\"mr-2 rounded\">
                            </a>
                            <strong style='font-size: 80%' class=\"d - block text - gray - dark\">
                                <a href='$this->root_path/components/profile/profile.php?id=$customer_id'>@$customer_username</a>
                            </strong>
                            ";

                    if (isset($task_difficulty)) {
                        if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_EASY) {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-success p-1 rounded\" style='opacity: .9' value=\"Easy\" disabled>Easy 👌</button>";
                        } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_MEDIUM) {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-warning p-1 rounded\" style='opacity: .9' value=\"Medium\" disabled>Medium 💪🏿</button>";
                        } else if ($task_difficulty == Data_Constants::DB_TASK_INTENSITY_HARD) {
                            echo "<button type=\"button\" class=\"ml-2 btn-sm btn btn-danger p-1 rounded\" style='opacity: .9' value=\"Hard\" disabled>Hard 🔥</button>";
                        }
                    }

                    if ($customer_id == $_SESSION['user_info']['id']) {
                        echo "<div class='float-right small' style='padding-top: .3rem;color: var(--red)'>- $$task_price</div>";
                    } else {
                        echo "<div class='float-right small' style='padding-top: .3rem;color: var(--dark)'>$$task_price</div>";
                    }

                    echo "</div><div class=\"media text-muted pt-3\">
                        <div class='container'>
                            <p id='$id' class=\"media-body text-dark mb-0 small lh-125\">
                                $task_description
                                <div id='$id-location' class='pt-1 border-top small border-gray d-none'>
                                    <label for='location'>Location:</label>
                                    <!-- TODO: calculate location distance by zipcode -->
                                    <p class='text-dark'>Rochester. MN</p>
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
                                    <img id='$id-img1' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task1_img_name&i_t=$task1_img_type' height='30%' width='30%' alt='FAVR image 1'>
                                </div>";
                    // Image 1 modal
                    echo "
                            <div id=\"$id-image1-modal\" class=\"modal\">
                              <span id='$id-close1' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image1-modal-content\">
                              <div id=\"$id-caption1\" class='caption'></div>
                            </div>";

                    echo "
                                <div id='$id-image2' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image2'>Attached Image 2:</label>
                                    <img id='$id-img2' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                        data-src='$this->root_path/image.php?i=$task2_img_name&i_t=$task2_img_type' height='30%' width='30%' alt='FAVR image 2'>
                                </div>";
                    // Image 2 modal
                    echo "
                            <div id=\"$id-image2-modal\" class=\"modal\">
                              <span id='$id-close2' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image2-modal-content\">
                              <div id=\"$id-caption2\" class='caption'></div>
                            </div>";

                    echo "
                                <div id='$id-image3' class='pt-1 border-top border-gray small d-none'>
                                    <label for='image3'>Attached Image 3:</label>
                                    <img id='$id-img3' style='cursor: pointer' src='data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22128%22%20height%3D%22128%22%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20viewBox%3D%220%200%20128%20128%22%20preserveAspectRatio%3D%22none%22%3E%3Cdefs%3E%3Cstyle%20type%3D%22text%2Fcss%22%3E%23holder_164a9f2d749%20text%20%7B%20fill%3A%23007bff%3Bfont-weight%3Abold%3Bfont-family%3AArial%2C%20Helvetica%2C%20Open%20Sans%2C%20sans-serif%2C%20monospace%3Bfont-size%3A6pt%20%7D%20%3C%2Fstyle%3E%3C%2Fdefs%3E%3Cg%20id%3D%22holder_164a9f2d749%22%3E%3Crect%20width%3D%22128%22%20height%3D%22128%22%20fill%3D%22%23007bff%22%3E%3C%2Frect%3E%3Cg%3E%3Ctext%20x%3D%2248.4296875%22%20y%3D%2266.7%22%3E128x128%3C%2Ftext%3E%3C%2Fg%3E%3C%2Fg%3E%3C%2Fsvg%3E' 
                                    data-src='$this->root_path/image.php?i=$task3_img_name&i_t=$task3_img_type' height='30%' width='30%' alt='FAVR image 3'>
                                </div>";
                    // Image 3 modal
                    echo "
                            <div id=\"$id-image3-modal\" class=\"modal\">
                              <span id='$id-close3' class=\"modal-close\">&times;</span>
                              <img class=\"modal-content\" id=\"$id-image3-modal-content\">
                              <div id=\"$id-caption3\" class='caption'></div>
                            </div>";

                    echo "
                            </p>
                            <div class='row p-0 border-top border-gray'>
                                <div class='col-sm-12 small'>
                                    <div class=\"float-left d-inline\">
                                        <div id='$id-expand' class='text-info d-inline-flex'
                                             style='cursor: pointer'
                                             onclick=\"
                                              $('.favr-fab').fadeOut();
                                              $('#$id').animate({height: '4rem'});
                                              $('#$id-location').removeClass('d-none');
                                              $('#$id-freelancer-count').removeClass('d-none');
                                              $('#$id-collapse').removeClass('d-none');
                                              $('#$id-collapse').addClass('d-inline-flex');
                                              $('#$id-expand').removeClass('d-inline-flex');
                                              $('#$id-expand').addClass('d-none');";

                    for ($i = 1; $i <= Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                        echo "
                            var modal = document.getElementById('$id-image$i-modal');
                            var img = document.getElementById('$id-img$i');
                            var modalImg = document.getElementById('$id-image$i-modal-content');
                            var captionText = document.getElementById('$id-caption$i');
                            img.onclick = function(){
                                modal.style.display = 'block';
                                modalImg.src = this.src;
                                captionText.innerHTML = this.alt;
                            }
                            var span = document.getElementById('$id-close$i');
                            span.onclick = function() { 
                                modal.style.display = 'none';
                            }
                         ";
                    }

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
                                              
                                              $('.favr-fab').css({display: ''})
                                        \">Collapse</div> | $task_date
                                        ";

                    echo "
                                    </div>
                                    <div class='float-right d-inline'>
                                       ";

                    if ($customer_id != $_SESSION['user_info']['id']) { // if not this user
                        $freelancerInfo = $this->getUserInfo($_SESSION['user_info']['id']);
                        if ($freelancerInfo['class'] == Data_Constants::DB_USER_CLASS_VERIFIED) {
                            $freelancerAccepted = false;
                            $freelancer_id = null;
                            $user_id = $_SESSION['user_info']['id'];
                            $select_freelancers_query = "SELECT * 
                                                     FROM marketplace_favr_freelancers
                                                     WHERE request_id = $task_id
                                                     AND user_id = $user_id";
                            $result = $this->db->query($select_freelancers_query);
                            if ($result) {
                                $row = $result->fetch(PDO::FETCH_ASSOC);
                                if (!empty($row)) {
                                    $freelancerAccepted = true;
                                    $freelancer_id = $row['user_id'];
                                }
                            }

                            if ($freelancerAccepted) {
                                echo "<a class='text-danger' href=\"$this->root_path/components/notifications/?navbar=active_notifications&withdraw_request_id=$task_id&freelancer_id=$freelancer_id&ALERT_MESSAGE=You've withdrawn from this task: the customer has been notified!\">
                                Withdraw</a>";
                            } else {
                                echo "<a href=\"$this->root_path/components/notifications/?navbar=active_notifications&accept_freelancer_request_id=$task_id&ALERT_MESSAGE=You've signed up to take this task! The task requester has been notified of your interest and is reviewing your offer to help: they can accept or reject your offer to help! You'll be notified of their decision; you can withdraw your offer to help before they decide. \">
                                Accept Request</a>";
                            }
                        }
                    } else {
                        echo "<a href=\"?navbar=active_home&d_request_id=$task_id&ALERT_MESSAGE=Your request has been deleted!\" class='text-danger'>
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
     * Render settings
     *
     * @param int $userID
     *
     */
    function renderSettings($userID) {
        $userInfo = $this->getUserInfo($userID);

        if ($userInfo['display_ratings'] == 1) {
            $displayMyRating = "checked";
        } else {
            $displayMyRating = "";
        }

        if ($userInfo['display_receipts'] == 1) {
            $displayMyReceipts = "checked";
        } else {
            $displayMyReceipts = "";
        }

        if ($userInfo['display_description'] == 1) {
            $displayMyDescription = "checked";
        } else {
            $displayMyDescription = "";
        }

        $private = "";
        $friends = "";
        $friendsOfFriends = "";
        $public = "";

        if ($userInfo['default_scope'] == "Private") {
            $private = "selected";
        } else if ($userInfo['default_scope'] == "Friends") {
            $friends = "selected";
        } else if ($userInfo['default_scope'] == "Friends of Friends") {
            $friendsOfFriends = "selected";
        } else if ($userInfo['default_scope'] == "Public") {
            $public = "selected";
        }

        ?>
        <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 1.2rem;">
            <div class="row pb-2 mb-0">
                <h3 style="width: 100%" class="text-center border-bottom border-gray">Account Settings</h3>
            </div>
            <form action="<?php echo "$this->root_path/components/settings/?navbar=active_settings"; ?>" method="post">
                <div class="row p-0 mb-0">
                    <div class="col-md-4">
                        <div class="form-group border-bottom border-gray">
                            <label class="small text-left pb-0">Display my ratings</label>
                            <span class="float-right switch switch-sm">
                                <input type="checkbox"
                                       name="display_ratings"
                                       <?php echo $displayMyRating; ?> class="switch" id="rating">
                                <label for="rating"></label>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group border-bottom border-gray">
                            <label class="small text-left pb-0">Display receipts</label>
                            <span class="float-right switch switch-sm">
                                <input type="checkbox"
                                       name="display_receipts"
                                    <?php echo $displayMyReceipts; ?> class="switch" id="receipts">
                                <label for="receipts"></label>
                            </span>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="small form-group border-bottom border-gray">
                            <label class="text-left pb-0">Display my description</label>
                            <span class="float-right switch switch-sm">
                                <input type="checkbox"
                                       name="display_description"
                                       <?php echo $displayMyDescription; ?> class="switch" id="description">
                                <label for="description"></label>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="row pb-0 pt-0 mb-0">
                    <div class="col-md-3 request-favr-web border-bottom border-gray"></div>
                    <div class="col-md-6 pl-2 pr-2 pt-0 pb-2 border-bottom border-gray">
                        <label for="scope">My default scope</label>
                        <select name="default_scope" class="form-control">
                            <option value="Private" <?php echo $private; ?>>Only me</option>
                            <option value="Friends" <?php echo $friends; ?>>Friends</option>
                            <option value="Friends of Friends" <?php echo $friendsOfFriends; ?>>Friends of friends</option>
                            <option value="Public" <?php echo $public; ?>>Public</option>
                        </select>
                    </div>
                    <div class="col-md-3 request-favr-web border-bottom border-gray"></div>
                </div>
                <div class="row pb-1 mb-0">
                    <div class="col-md-4 p-2 border-bottom border-gray">
                        <a href="<?php echo "$this->root_path/home/payments/"; ?>">Set up payments
                            <i class="mobile-footer float-right text-muted material-icons">chevron_right</i>
                        </a>
                    </div>
                    <div class="col-md-4 p-2 border-bottom border-gray">
                        <a href="#">Terms of Service and Conditions
                            <i class="mobile-footer float-right text-muted material-icons">chevron_right</i>
                        </a>
                    </div>
                    <div class="col-md-4 p-2 border-bottom border-gray">
                        <a href="#">Change password
                            <i class="mobile-footer float-right text-muted material-icons">chevron_right</i>
                        </a>
                    </div>
                </div>
                <div class="row pb-1 mb-0">
                    <div class="col-lg-12 text-center">
                        <input type="submit" name="submit_settings" class="btn btn-outline-info" value="Save changes">
                    </div>
                </div>
            </form>
            <div class="row pb-1 mb-0">
                <div class="col-lg-12 pt-2 text-center border-top border-gray">
                    <button class="btn btn-outline-danger" data-toggle="modal" data-target="#deleteModal">
                        Delete my account</button>

                     <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteAccountTitle" aria-hidden="true">
                        <form action='<?php echo "$this->root_path/components/settings/?navbar=active_settings"; ?>' method='post'>
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="deleteAccountTitle">Deleting account</h5>
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <div class="modal-body">
                                        <label for='exitReview'>Reason(s) for leaving</label>
                                        <textarea name='delete_review' class='form-control' placeholder="Please tell us why you are choosing to leave FAVR..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                        <input type="submit" name='submit_delete' class="btn btn-primary" value='Delete my account'>
                                    </div>
                                </div>
                            </div>
                        </form>
                     </div>
                </div>
            </div>
        </div>
        <div class="p-3 pb-0 rounded bg-white box-shadow" style="margin-top: 1.2rem;">
            <div class="row pb-2 mb-0">
                <h3 style="width: 100%" class="text-center border-bottom border-gray">Signed-In Sessions</h3>
            </div>
        </div>
        <?php
    }

    /**
     * Render page footer at the end of the page
     *
     * @param boolean $setFooter
     */
    function renderFooter($setFooter = true)
    {
        ?>
        </main>
        <?php
            if ($setFooter) {
        ?>
        <!-- FOOTER -->
        <footer class="container ml-5 pl-5" style="position: relative; float: bottom; max-width: 90%">
            <hr>
            <div class="row">
                <p class="col-md-10 text-muted">&copy; 2018 FAVR, Inc</p>
                <p class="col-md-2 float-right text-muted">v<?php echo $this->product_version; ?> Beta</p>
            </div>
        </footer>
        <?php
            }
        ?>
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

    //------------------------------------------------------------
    // From this point forward these are process functions
    //------------------------------------------------------------

    /**
     * Process settings changes
     *
     * @param boolean $display_ratings
     * @param boolean $display_receipts
     * @param boolean $display_description
     * @param string $default_scope
     *
     * @return boolean // successful change of settings
     */
    function processSettings($display_ratings, $display_receipts, $display_description, $default_scope) {
//        die(print_r($display_ratings));
        $userID = $_SESSION['user_info']['id'];
        $update_settings_query = "UPDATE users 
                                  SET default_scope = '$default_scope',
                                      display_ratings = $display_ratings,
                                      display_receipts = $display_receipts,
                                      display_description = $display_description
                                  WHERE id = $userID";

        $result = $this->db->query($update_settings_query);
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Process favr friend requests
     * this process functions from the perspective of the requester who owns the sessions always
     *
     * @param int $userID
     * @param int $requesterID
     * @param boolean $add_friend // if true user add friend else false user decline request
     *
     * @return boolean // successful process or not print error
     *
     */
    function processFavrFriendRequest($userID, $requesterID, $add_friend)
    {
        if (isset($userID, $requesterID, $add_friend)) {
            // check if users are already friends
            $select_friends_query1 = "SELECT * 
                                      FROM friends 
                                      WHERE user_id = $userID
                                      AND friend_id = $requesterID";

            $select_friends_query2 = "SELECT * 
                                      FROM friends
                                      WHERE user_id = $requesterID
                                      AND friend_id = $userID";

            $result1 = $this->db->query($select_friends_query1);
            $result2 = $this->db->query($select_friends_query2);

            if ($result1 && $result2) {
                $row1 = $result1->fetch(PDO::FETCH_ASSOC);
                $row2 = $result2->fetch(PDO::FETCH_ASSOC);

                if (!empty($row1) && !empty($row2)) {
                    // friendship already exists do not do anything unless unfriending
                    if ($add_friend == false) {
                        // unfriend this user or cancel this friendship
                        $delete_friends_query1 = "DELETE
                                                  FROM friends
                                                  WHERE user_id = $userID
                                                  AND friend_id = $requesterID";

                        $delete_friends_query2 = "DELETE
                                                  FROM friends
                                                  WHERE user_id = $requesterID
                                                  AND friend_id = $userID";

                        $result1 = $this->db->query($delete_friends_query1);
                        $result2 = $this->db->query($delete_friends_query2);
                        if ($result1 && $result2) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        // invalid input for add friends
                        return false;
                    }
                } else if (!empty($row1) && empty($row2)) {
                    // requester is accepting a friend request sent to them
                    if ($add_friend == true) {
                        $timestamp = date("Y-m-d h:i:s" , time());
                        $update_friend_query = "UPDATE friends 
                                            SET user_id = $userID,
                                                friend_id = $requesterID,
                                                friends_since = '$timestamp'
                                            WHERE user_id = $userID 
                                            AND friend_id = $requesterID";

                        $insert_friend_query = "INSERT 
                                            INTO friends 
                                            (user_id, friend_id, friends_since) 
                                            VALUES 
                                            ($requesterID, $userID, '$timestamp')";

                        $result1 = $this->db->query($update_friend_query);
                        $result2 = $this->db->query($insert_friend_query);
                        if ($result1 && $result2) {
                            return true;
                        } else {
                            return false;
                        }
                    } else if ($add_friend == false) {
                        // unfriend this user or cancel this friendship
                        $delete_friends_query1 = "DELETE
                                                  FROM friends
                                                  WHERE user_id = $userID
                                                  AND friend_id = $requesterID";

                        $delete_friends_query2 = "DELETE
                                                  FROM friends
                                                  WHERE user_id = $requesterID
                                                  AND friend_id = $userID";

                        $result1 = $this->db->query($delete_friends_query1);
                        $result2 = $this->db->query($delete_friends_query2);
                        if ($result1 && $result2) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        // invalid input for add friends
                        return false;
                    }
                } else if (empty($row1) && !empty($row2)) {
                    // requester has already sent a friend request
                    if ($add_friend == false) {
                        // unfriend this user or cancel this friendship
                        $delete_friends_query2 = "DELETE
                                                  FROM friends
                                                  WHERE user_id = $requesterID
                                                  AND friend_id = $userID";

                        $result2 = $this->db->query($delete_friends_query2);
                        if ($result2) {
                            return true;
                        } else {
                            return false;
                        }
                    } else {
                        // invalid input for add friends
                        return false;
                    }
                } else {
                    // friendship doesn't exist requester send friend request
                    $insert_friend_query = "INSERT 
                                            INTO friends 
                                            (user_id, friend_id) 
                                            VALUES 
                                            ($requesterID, $userID)";
                    $result = $this->db->query($insert_friend_query);
                    if ($result) {
                        return true;
                    } else {
                        return false;
                    }
                }
            }
        } else {
            return false;
        }
    }

    /**
     * Process favr request from form and serve into database to display in marketplace
     *
     * @param array $userInfo // array with user details such as location
     * @param string $inputDate
     * @param string $inputCategory
     * @param string $inputTaskDetails
     * @param int $inputFreelancerCount
     * @param double $inputPricing
     * @param string $inputLocation
     * @param string $inputDifficulty
     * @param array $inputPictures
     * @param mixed $inputScope // pre-alpha scope is public
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

                if (isset($inputPictures)) {
                    $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
                    $select_requested_task_query = "SELECT id 
                                                  FROM marketplace_favr_requests
                                                  WHERE customer_id = '$userId'
                                                  AND task_date = '$inputDate'
                                                  AND task_description = '$inputTaskDetails'
                                                  AND task_location = '$address'
                                                  AND task_category = '$inputCategory'
                                                  AND task_status = '$requested'
                                                  AND task_intensity = '$inputDifficulty'
                                                  AND task_price = '$inputPricing'
                                                  AND task_freelancer_count = '$inputFreelancerCount'";

                    $result = $this->db->query($select_requested_task_query);
                    if ($result) {
                        $row = $result->fetch(PDO::FETCH_ASSOC);
                        $task_id = $row['id'];

                        for ($i = 0; $i < Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                            // check if atleast one image is set
                            if (!empty($inputPictures['name'][$i])) {
                                // validate file is an image of type jpeg or png
                                if ($inputPictures['type'][$i] == "image/jpeg" || $inputPictures['type'][$i] == "image/png") {
                                    // continue validating file size is below allowed size of 5 MB
                                    if ($inputPictures['size'][$i] <=  Data_Constants::MAXIMUM_IMAGE_UPLOAD_SIZE) {
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

                                                $imageFilePath =  Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageFileName";

                                                // copy image into backend and execute update query on request to update file paths
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
     * Process favr friends request from form and serve into database to display in marketplace
     *
     * @param array $userInfo // array with user details such as location
     * @param string $inputDate
     * @param string $inputCategory
     * @param string $inputTaskDetails
     * @param int $inputFreelancerCount
     * @param double $inputPricing
     * @param string $inputLocation
     * @param string $inputDifficulty
     * @param array $inputPictures
     * @param mixed $inputScope // pre-alpha scope is public
     * @param int $friend_id
     *
     * @return boolean // successful process or not print error
     *
     */
    function processFavrFriendRequestToDB($userInfo, $inputDate, $inputCategory, $inputTaskDetails, $inputPricing, $inputFreelancerCount, $inputLocation, $inputDifficulty,  $inputPictures, $inputScope="public", $friend_id = null)
    {
        if (isset($userInfo, $inputDate, $inputFreelancerCount, $inputCategory, $inputTaskDetails, $inputPricing, $inputScope)) {
            $userId = $userInfo['id'];
            $address = $inputLocation;
            if ($friend_id != null) {
                $friend_id_column = "`friend_id`,";
                $approved_column = "`approved`,";
                $task_status_column = "`task_status`,";
                $task_accepted_column = "`task_freelancer_accepted`,";

                $task_status = Data_Constants::DB_TASK_STATUS_PAID;

                $friend_id_value = "'$friend_id',";
                $approved_value = "'1',";
                $task_status_value = "'$task_status',";
                $task_accepted_value = "'1',";
            } else {
                $friend_id_column = "";
                $approved_column = "";
                $task_status_column = "";
                $task_accepted_column = "";

                $friend_id_value = "";
                $approved_value = "";
                $task_status_value = "";
                $task_accepted_value = "";
            }

            $insert_request_query = "INSERT INTO `friends_favr_requests`
                                  (
                                    `customer_id`,
                                    `task_description`,
                                    `task_date`,
                                    `task_freelancer_count`,
                                    `task_location`,
                                    `task_category`,
                                    `task_intensity`,
                                    $friend_id_column
                                    $approved_column
                                    $task_status_column
                                    $task_accepted_column
                                    `favr_price`
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
                                    $friend_id_value
                                    $approved_value
                                    $task_status_value
                                    $task_accepted_value
                                    '$inputPricing'
                                  )
            ";

            $result = $this->db->query($insert_request_query);
//            $result = true; // for testing

            if ($result) {
                // process attached images logic

                if (isset($inputPictures)) {
                    $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
                    $select_requested_task_query = "SELECT id 
                                                  FROM friends_favr_requests
                                                  WHERE customer_id = '$userId'
                                                  AND task_date = '$inputDate'
                                                  AND task_description = '$inputTaskDetails'
                                                  AND task_location = '$address'
                                                  AND task_category = '$inputCategory'
                                                  AND task_status = '$requested'
                                                  AND task_intensity = '$inputDifficulty'
                                                  AND favr_price = '$inputPricing'
                                                  AND task_freelancer_count = '$inputFreelancerCount'";

                    $result = $this->db->query($select_requested_task_query);
                    if ($result) {
                        $row = $result->fetch(PDO::FETCH_ASSOC);
                        $task_id = $row['id'];

                        for ($i = 0; $i < Data_Constants::MAXIMUM_IMAGE_UPLOAD_COUNT; $i++) {
                            // check if atleast one image is set
                            if (!empty($inputPictures['name'][$i])) {
                                // validate file is an image of type jpeg or png
                                if ($inputPictures['type'][$i] == "image/jpeg" || $inputPictures['type'][$i] == "image/png") {
                                    // continue validating file size is below allowed size of 5 MB
                                    if ($inputPictures['size'][$i] <=  Data_Constants::MAXIMUM_IMAGE_UPLOAD_SIZE) {
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

                                                $imageFilePath =  Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageFileName";

                                                // copy image into backend and execute update query on request to update file paths
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

                                                    $update_path_query = "UPDATE friends_favr_requests 
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
     * TODO: needs logic development
     * Process cancel pending request
     *
     * @param int $requestID
     * @param int $freelancerID // user id of the freelancer
     * @param int $customerID // user id of the customer
     *
     * @return boolean
     */
    function processCancelPendingRequest($requestID,  $freelancerID = null, $customerID = null)
    {
        if (isset($requestID) && ($freelancerID != null || $customerID != null)) {
            $select_task_query = "SELECT freelancer_id, task_freelancer_accepted 
                                  FROM marketplace_favr_requests
                                  WHERE id = '$requestID'";
            $result = $this->db->query($select_task_query);
            if ($result) {
                if ($freelancerID != null) {
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    $freelancer_id = $row['freelancer_id'];
                    $freelancer_accepted = $row['task_freelancer_accepted'];

                    // delete request from freelancers table and decrement freelancers accepted count
                    $delete_freelancer_query = "DELETE 
                                                FROM marketplace_favr_freelancers
                                                WHERE request_id = $requestID
                                                AND user_id = $freelancerID";
                    $result = $this->db->query($delete_freelancer_query);
                    if ($result) {

                        // freelancer has been removed from task
                        $freelancer_accepted -= 1;
//                        $set_task_status = "";
                        $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
                        $set_task_status = ", task_status = '$requested'";

                        // update request to null if freelancers accepted is 0 and set status back to requested
                        if ($freelancer_accepted == 0) {
                            $freelancer_id = "NULL";
                        }

                        $update_request_query = "UPDATE marketplace_favr_requests
                                                 SET task_freelancer_accepted = $freelancer_accepted,
                                                     freelancer_id = $freelancer_id
                                                     $set_task_status
                                                 WHERE id = $requestID";

                        $result = $this->db->query($update_request_query);
                        if ($result) {
                            // successfully withdrew freelancer from task
                            return true;
                        } else {
                            // failure in withdrawal
                            return false;
                        }
                    } else {
                        // failure in withdrawal
                        return false;
                    }
                }

                if ($customerID != null) {
                    return true;
                }
                // TODO: javaScript validation warning user of action

                return false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process freelancer arrival and log the time
     *
     * @param boolean $freelancerArrived
     * @param int $timestamp
     * @param int $requestID
     * @param int $freelancerID // this user!
     *
     * @return mixed // timestamp of arrival or false
     */
    function processFreelancerArrived($freelancerArrived, $timestamp, $requestID, $freelancerID)
    {
        if (isset($freelancerArrived, $timestamp, $requestID, $freelancerID)) {
            if ($freelancerID == $_SESSION['user_info']['id']) { // must be this user
                // log time of arrival
                $inProgress = Data_Constants::DB_TASK_STATUS_IN_PROGRESS;
                $update_request_query = "UPDATE marketplace_favr_requests
                                         SET task_status = '$inProgress'
                                         WHERE id = $requestID";

                $result = $this->db->query($update_request_query);
                if ($result) {
                    $update_freelancers_query = "UPDATE marketplace_favr_freelancers
                                             SET arrival_time = '$timestamp'
                                             WHERE request_id = $requestID
                                             AND user_id = $freelancerID";

                    $result = $this->db->query($update_freelancers_query);
                    if ($result) {
                        return $timestamp;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process complete request
     *
     * @param int $requestID
     * @param int $customerID
     * @param int $freelancerID
     * @param int $requestRating
     * @param string $requestReview
     * @param int $timestamp // time of completion
     *
     * @return boolean
     */
    function processCompleteRequest($requestID, $customerID, $freelancerID, $requestRating, $requestReview, $timestamp)
    {
        if (isset($requestID, $customerID, $freelancerID, $requestRating, $timestamp)) {
            // complete request
            $complete = Data_Constants::DB_TASK_STATUS_COMPLETED;
            $requestReview = addslashes($requestReview);
            $update_request_query = "UPDATE marketplace_favr_requests 
                                     SET task_status = '$complete',
                                         task_rating = '$requestRating',
                                         task_optional_service_review = '$requestReview'
                                     WHERE id = $requestID
                                     AND customer_id = $customerID";

            $result = $this->db->query($update_request_query);
            if ($result) {
                // set rating for each freelancer on the task
                $select_freelancers_query = "SELECT * 
                                             FROM marketplace_favr_freelancers
                                             WHERE request_id = $requestID";

                $result = $this->db->query($select_freelancers_query);
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $userID = $row['user_id'];
                    $select_user_query = "SELECT rating
                                          FROM users
                                          WHERE id = $userID";
                    $result = $this->db->query($select_user_query);
                    if ($result) {
                        $row = $result->fetch(PDO::FETCH_ASSOC);
                        $ratings = unserialize($row['rating']);
                        if (count($ratings) == Data_Constants::DB_MAX_USER_RATING_COUNT) {
                            $ratings = array_reverse($ratings);
                            array_pop($ratings);
                            $ratings = array_reverse($ratings);
                            array_push($ratings, $requestRating);
                        } else {
                            array_push($ratings, $requestRating);
                        }

                        $ratings = serialize($ratings);
                        $update_user_query = "UPDATE users 
                                              SET rating = '$ratings' 
                                              WHERE id = $userID";
                        $result = $this->db->query($update_user_query);
                        if (!$result) {
                            return false;
                        }
                    }
                }

                // log time of completion
                $update_freelancers_query = "UPDATE marketplace_favr_freelancers
                                             SET completion_time = '$timestamp'
                                             WHERE request_id = $requestID";

                $result = $this->db->query($update_freelancers_query);
                if (!$result) {
                    return false;
                }
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
     * @param int $requestID
     * @param int $customerID
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

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task2_image_array)) {
                    $imageName = $task2_image_array['name'];

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task3_image_array)) {
                    $imageName = $task3_image_array['name'];

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
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
            $delete_freelancer_query = "DELETE FROM marketplace_favr_freelancers
                                        WHERE request_id = '$requestID'";
            $result = $this->db->query($delete_request_query);
            if ($result) {
                $result = $this->db->query($delete_freelancer_query);
                if ($result) {
                    // successfully deleted
                    return true;
                } else {
                    return false;
                }
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
     * Process accept a friend's request
     *
     * Flow: Marketplace -> freelancer accepts -> Notify customer -> customer accepts -> Notify freelancer -> Change status of request to pending job
     *                                                      |-> freelancer or customer rejects -> Marketplace
     *
     * @param int $requestID
     * @param int $freelancerID
     *
     * @return mixed
     */
    function processFreelancerAcceptRequest($requestID, $freelancerID)
    {
        if (isset($requestID, $freelancerID)) {
            // freelancer has accepted
            $select_request_query = "SELECT * 
                                     FROM marketplace_favr_requests
                                     WHERE id = $requestID";
            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_accepted = $row['task_freelancer_accepted'];
                $freelancer_count = $row['task_freelancer_count'];
                $freelancer_id = $row['freelancer_id'];
                $request_id = $row['id'];
                $setTaskStatusPending = ""; // still need more freelancers

                // ensure there's not already enough freelancers signed up for this job
                if ($freelancer_accepted < $freelancer_count) {
                    $select_freelancers_query = "INSERT INTO marketplace_favr_freelancers
                                                 (
                                                    request_id, 
                                                    user_id
                                                 ) 
                                                 VALUES
                                                 (
                                                    $request_id,
                                                    $freelancerID
                                                 )";

                    $result = $this->db->query($select_freelancers_query);
                    if ($result) {
                        $freelancer_accepted += 1; // add user to freelancer queue

                        $update_request_query = "UPDATE marketplace_favr_requests
                                                 SET task_freelancer_accepted = $freelancer_accepted,
                                                     freelancer_id = $request_id
                                                 WHERE id = $request_id";

                        $result = $this->db->query($update_request_query);
//                        die(print_r($result));
                        if (!$result) {
                            return false;
                        }

                    } else {
                        return false;
                    }
                }

                if ($freelancer_accepted == $freelancer_count) {
                    $setTaskStatusPending = Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL;

                    $update_request_query = "UPDATE marketplace_favr_requests
                                             SET task_status = '$setTaskStatusPending'
                                             WHERE id = $request_id";

                    $result = $this->db->query($update_request_query);
                    if ($result) {
                        return $freelancerID;
                    } else {
                        return false;
                    }
                }

                if ($freelancer_accepted > $freelancer_count) {
                    // impossible
                    return false;
                }

                return $freelancerID;
            }
        } else {
            // not set
            return false;
        }
    }

    /**
     * Process accept request
     *
     * Flow: Marketplace -> Verified freelancer accepts -> Notify customer -> customer accepts -> Notify freelancer -> Change status of request to paid job
     *                                                      |-> freelancer or customer rejects -> Marketplace
     *
     * @param int $requestID
     * @param int $freelancerID
     * @param int $customerID
     *
     * @return mixed
     */
    function processCustomerAcceptRequest($requestID, $freelancerID, $customerID)
    {
        if (isset($requestID, $freelancerID, $customerID)) {
            // customer has approved of freelancer
            $select_request_query = "SELECT * 
                                     FROM marketplace_favr_requests
                                     WHERE id = $requestID";
            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_accepted = $row['task_freelancer_accepted'];
                $freelancer_count = $row['task_freelancer_count'];
                $freelancer_id = $row['freelancer_id'];
                $request_id = $row['id'];

                // set freelancer to approved
                if ($freelancer_accepted <= $freelancer_count) {

                    // approve freelancer set approved to true
                    $update_freelancer_query = "UPDATE marketplace_favr_freelancers 
                                                SET approved = 1
                                                WHERE request_id = $requestID
                                                AND user_id = $freelancerID";

                    $result = $this->db->query($update_freelancer_query);
                    if ($result) {
                        // set status of task to paid if enough help has been found and approved
                        if ($freelancer_accepted == $freelancer_count) {
                            $select_freelancer_query = "SELECT COUNT(*)
                                                        FROM marketplace_favr_freelancers
                                                        WHERE request_id = $requestID
                                                        AND approved = 1";
                            $result = $this->db->query($select_freelancer_query);
                            if ($result) {
                                $row = $result->fetch(PDO::FETCH_ASSOC);
                                $approvedCount = $row['COUNT(*)'];
                                if ($approvedCount == $freelancer_count) { // user has approved all freelancers
                                    $paid = Data_Constants::DB_TASK_STATUS_PAID;
                                    $update_request_query = "UPDATE marketplace_favr_requests
                                                             SET task_status = '$paid'
                                                             WHERE id = $request_id";

                                    $result = $this->db->query($update_request_query);
                                    if ($result) {
                                        $checkout = new Web_Payment();
                                        $checkout->select($requestID)->checkOut($requestID); //redirects to payment process
                                        return $customerID;
                                    } else {
                                        return false;
                                    }
                                }
                            } else {
                                return false;
                            }
                        }
                    } else {
                        // error
                        return false;
                    }

                    return $customerID;
                }

                if ($freelancer_accepted > $freelancer_count) {
                    // impossible
                    return false;
                }
            }
        } else {
            // not set
            return false;
        }
    }

    /**
     * Process accept a friend's request
     *
     * Flow: Friends -> friend accepts -> Notify customer -> customer accepts -> Notify freelancer -> Change status of request to pending job
     *                                                      |-> freelancer or customer rejects -> Marketplace
     *
     * @param int $requestID
     * @param int $freelancerID
     *
     * @return mixed
     */
    function processFriendFreelancerAcceptRequest($requestID, $freelancerID)
    {
        if (isset($requestID, $freelancerID)) {
            // freelancer has accepted
            $select_request_query = "SELECT * 
                                     FROM friends_favr_requests
                                     WHERE id = $requestID";
            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_accepted = $row['task_freelancer_accepted'];
                $freelancer_count = $row['task_freelancer_count'];
                $freelancer_id = $row['friend_id'];
                $request_id = $row['id'];
                $setTaskStatusPending = ""; // still need more freelancers

                // ensure there's not already enough freelancers signed up for this job
                if ($freelancer_accepted < $freelancer_count) {
                    $freelancer_accepted += 1; // add user to freelancer queue

                    $update_request_query = "UPDATE friends_favr_requests
                                             SET task_freelancer_accepted = $freelancer_accepted,
                                                 friend_id = $freelancerID
                                             WHERE id = $request_id";

                    $result = $this->db->query($update_request_query);
//                        die(print_r($result));
                    if (!$result) {
                        return false;
                    }
                }

                if ($freelancer_accepted == $freelancer_count) {
                    $setTaskStatusPending = Data_Constants::DB_TASK_STATUS_PENDING_APPROVAL;

                    $update_request_query = "UPDATE friends_favr_requests
                                             SET task_status = '$setTaskStatusPending'
                                             WHERE id = $request_id";

                    $result = $this->db->query($update_request_query);
                    if ($result) {
                        return $freelancerID;
                    } else {
                        return false;
                    }
                }

                if ($freelancer_accepted > $freelancer_count) {
                    // impossible
                    return false;
                }

                return $freelancerID;
            }
        } else {
            // not set
            return false;
        }
    }

    /**
     * Process accept request
     *
     * Flow: Friends -> Verified freelancer accepts -> Notify customer -> customer accepts -> Notify freelancer -> Change status of request to paid job
     *                                                      |-> freelancer or customer rejects -> Marketplace
     *
     * @param int $requestID
     * @param int $freelancerID
     * @param int $customerID
     *
     * @return mixed
     */
    function processFriendCustomerAcceptRequest($requestID, $freelancerID, $customerID)
    {
        if (isset($requestID, $freelancerID, $customerID)) {
            // customer has approved of freelancer
            $select_request_query = "SELECT * 
                                     FROM friends_favr_requests
                                     WHERE id = $requestID";
            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $freelancer_accepted = $row['task_freelancer_accepted'];
                $freelancer_count = $row['task_freelancer_count'];
                $freelancer_id = $row['friend_id'];
                $request_id = $row['id'];

                // set freelancer to approved
                if ($freelancer_accepted <= $freelancer_count) {

                    // approve freelancer set approved to true
                    $update_freelancer_query = "UPDATE friends_favr_requests 
                                                SET approved = 1
                                                WHERE id = $requestID
                                                AND friend_id = $freelancerID";

                    $result = $this->db->query($update_freelancer_query);
                    if ($result) {
                        // set status of task to paid if enough help has been found and approved
                        if ($freelancer_accepted == $freelancer_count) {
                            $select_freelancer_query = "SELECT COUNT(*)
                                                        FROM friends_favr_requests
                                                        WHERE id = $requestID
                                                        AND approved = 1";
                            $result = $this->db->query($select_freelancer_query);
                            if ($result) {
                                $row = $result->fetch(PDO::FETCH_ASSOC);
                                $approvedCount = $row['COUNT(*)'];
                                if ($approvedCount == $freelancer_count) { // user has approved all freelancers
                                    $paid = Data_Constants::DB_TASK_STATUS_PAID;
                                    $update_request_query = "UPDATE friends_favr_requests
                                                             SET task_status = '$paid'
                                                             WHERE id = $request_id";

                                    $result = $this->db->query($update_request_query);
                                    if ($result) {
                                        return $customerID;
                                    } else {
                                        return false;
                                    }
                                }
                            } else {
                                return false;
                            }
                        }
                    } else {
                        // error
                        return false;
                    }

                    return $customerID;
                }

                if ($freelancer_accepted > $freelancer_count) {
                    // impossible
                    return false;
                }
            }
        } else {
            // not set
            return false;
        }
    }

    /**
     * TODO: needs logic development
     * Process cancel pending friend request
     *
     * @param int $requestID
     * @param int $freelancerID // user id of the freelancer
     * @param int $customerID // user id of the customer
     *
     * @return boolean
     */
    function processFriendCancelPendingRequest($requestID,  $freelancerID = null, $customerID = null)
    {
        if (isset($requestID) && ($freelancerID != null || $customerID != null)) {
            $select_task_query = "SELECT friend_id, task_freelancer_accepted 
                                  FROM friends_favr_requests
                                  WHERE id = '$requestID'";
            $result = $this->db->query($select_task_query);
            if ($result) {
                if ($freelancerID != null) {
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    $freelancer_id = $row['friend_id'];
                    $freelancer_accepted = $row['task_freelancer_accepted'];

                    // delete request from freelancers table and decrement freelancers accepted count

                    // freelancer has been removed from task
                    $freelancer_accepted -= 1;
//                        $set_task_status = "";
                    $requested = Data_Constants::DB_TASK_STATUS_REQUESTED;
                    $set_task_status = ", task_status = '$requested'";

                    // update request to null if freelancers accepted is 0 and set status back to requested
                    if ($freelancer_accepted == 0) {
                        $freelancer_id = "NULL";
                    }

                    $update_request_query = "UPDATE friends_favr_requests
                                             SET task_freelancer_accepted = $freelancer_accepted,
                                                 friend_id = $freelancer_id,
                                                 arrival_time = NULL,
                                                 approved = 0
                                                 $set_task_status
                                             WHERE id = $requestID";

                    $result = $this->db->query($update_request_query);
                    if ($result) {
                        // successfully withdrew freelancer from task
                        return true;
                    } else {
                        // failure in withdrawal
                        return false;
                    }
                }

                if ($customerID != null) {
                    return true;
                }
                // TODO: javaScript validation warning user of action

                return false;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process friend freelancer arrival and log the time
     *
     * @param boolean $freelancerArrived
     * @param int $timestamp
     * @param int $requestID
     * @param int $freelancerID // this user!
     *
     * @return mixed // timestamp of arrival or false
     */
    function processFriendFreelancerArrived($freelancerArrived, $timestamp, $requestID, $freelancerID)
    {
        if (isset($freelancerArrived, $timestamp, $requestID, $freelancerID)) {
            if ($freelancerID == $_SESSION['user_info']['id']) { // must be this user
                // log time of arrival
                $inProgress = Data_Constants::DB_TASK_STATUS_IN_PROGRESS;
                $update_request_query = "UPDATE friends_favr_requests
                                         SET task_status = '$inProgress'
                                         WHERE id = $requestID";

                $result = $this->db->query($update_request_query);
                if ($result) {
                    $update_freelancers_query = "UPDATE friends_favr_requests
                                             SET arrival_time = '$timestamp'
                                             WHERE id = $requestID
                                             AND friend_id = $freelancerID";

                    $result = $this->db->query($update_freelancers_query);
                    if ($result) {
                        return $timestamp;
                    } else {
                        return false;
                    }
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Process complete friend request
     *
     * @param int $requestID
     * @param int $customerID
     * @param int $freelancerID
     * @param int $requestRating
     * @param string $requestReview
     * @param int $timestamp // time of completion
     *
     * @return boolean
     */
    function processFriendCompleteRequest($requestID, $customerID, $freelancerID, $requestRating, $requestReview, $timestamp)
    {
        if (isset($requestID, $customerID, $freelancerID, $requestRating, $timestamp)) {
            // complete request
            $complete = Data_Constants::DB_TASK_STATUS_COMPLETED;
            $requestReview = addslashes($requestReview);
            $update_request_query = "UPDATE friends_favr_requests 
                                     SET task_status = '$complete',
                                         task_rating = '$requestRating',
                                         task_optional_service_review = '$requestReview'
                                     WHERE id = $requestID
                                     AND customer_id = $customerID";

            $result = $this->db->query($update_request_query);
            if ($result) {
                // TODO: Should user ratings count from friend interactions
                // set rating for each freelancer on the task
//                $select_freelancers_query = "SELECT *
//                                             FROM friends_favr_requests
//                                             WHERE id = $requestID";
//
//                $result = $this->db->query($select_freelancers_query);
//                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
//                foreach ($rows as $row) {
//                    $userID = $row['user_id'];
//                    $select_user_query = "SELECT rating
//                                          FROM users
//                                          WHERE id = $userID";
//                    $result = $this->db->query($select_user_query);
//                    if ($result) {
//                        $row = $result->fetch(PDO::FETCH_ASSOC);
//                        $ratings = unserialize($row['rating']);
//                        if (count($ratings) == Data_Constants::DB_MAX_USER_RATING_COUNT) {
//                            $ratings = array_reverse($ratings);
//                            array_pop($ratings);
//                            $ratings = array_reverse($ratings);
//                            array_push($ratings, $requestRating);
//                        } else {
//                            array_push($ratings, $requestRating);
//                        }
//
//                        $ratings = serialize($ratings);
//                        $update_user_query = "UPDATE users
//                                              SET rating = '$ratings'
//                                              WHERE id = $userID";
//                        $result = $this->db->query($update_user_query);
//                        if (!$result) {
//                            return false;
//                        }
//                    }
//                }

                $select_request_query = "SELECT * 
                                         FROM friends_favr_requests 
                                         WHERE id = $requestID";

                $result = $this->db->query($select_request_query);
                if ($result) {
                    $row = $result->fetch(PDO::FETCH_ASSOC);
                    $requestPrice = $row['favr_price'];
                    // log time of completion
                    $update_freelancers_query = "UPDATE friends_favr_requests
                                             SET completion_time = '$timestamp'
                                             WHERE id = $requestID";

                    // credit friends with favr credit
                    $update_customer_credit_query = "UPDATE friends 
                                                 SET given = $requestPrice
                                                 WHERE user_id = $customerID
                                                 AND friend_id = $freelancerID";

                    $update_friend_credit_query = "UPDATE friends 
                                               SET received = $requestPrice
                                               WHERE user_id = $freelancerID
                                               AND friend_id = $customerID";

                    $result = $this->db->query($update_freelancers_query);
                    $result1 = $this->db->query($update_customer_credit_query);
                    $result2 = $this->db->query($update_friend_credit_query);
                    if ($result && $result1 && $result2) {
                        // successfully completed
                        return true;
                    } else {
                        return false;
                    }
                } else {
                    // task doesn't exist
                    return false;
                }
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
     * @param int $requestID
     * @param int $customerID
     *
     * @return boolean
     */
    function processFriendDeleteRequest($requestID, $customerID)
    {
        if (isset($requestID, $customerID)) {
            // Delete images
            $select_request_query = "SELECT *
                                     FROM friends_favr_requests
                                     WHERE id = $requestID
                                     AND customer_id = $customerID";

            $result = $this->db->query($select_request_query);
            if ($result) {
                $row = $result->fetch(PDO::FETCH_ASSOC);
                $task1_image_array = unserialize($row['task_picture_path_1']);
                $task2_image_array = unserialize($row['task_picture_path_2']);
                $task3_image_array = unserialize($row['task_picture_path_3']);

                if (!empty($task1_image_array)) {
                    $imageName = $task1_image_array['name'];

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task2_image_array)) {
                    $imageName = $task2_image_array['name'];

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }

                if (!empty($task3_image_array)) {
                    $imageName = $task3_image_array['name'];

                    if (file_exists(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName")) {
                        $removeImage = unlink(Data_Constants::IMAGE_UPLOAD_FILE_PATH . "$imageName");
                        if (!$removeImage) {
                            return false;
                        }
                    }
                }
            }

            // Delete request
            $delete_request_query = "DELETE FROM friends_favr_requests
                                     WHERE id = '$requestID'
                                     AND customer_id = '$customerID'";
            $result = $this->db->query($delete_request_query);
            if ($result) {
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
     * Process notifications
     *
     * @param array $userInfo
     *
     * @return integer // return notification count
     */
    function processNotifications($userInfo)
    {
        $userID = $userInfo['id'];
        $completed = Data_Constants::DB_TASK_STATUS_COMPLETED;
        $notifications_query = "SELECT COUNT(*)
                                FROM marketplace_favr_requests mfr 
                                JOIN marketplace_favr_freelancers mff 
                                ON mff.request_id = mfr.id
                                JOIN users u
                                ON u.id = mff.user_id
                                AND mff.user_id = $userID 
                                AND NOT mfr.task_status = '$completed'
                                OR mfr.customer_id = $userID 
                                AND u.id = mfr.customer_id
                                AND NOT mfr.task_status = '$completed'
                                ";

        $notifications_friend_request_query = "
                SELECT COUNT(*)
                FROM friends f
                JOIN users u
                ON u.id = f.friend_id
                WHERE u.id = $userID
                AND friends_since IS NULL 
        ";

        $notifications_friend_favr_request_query = "
                                SELECT COUNT(*)
                                FROM friends_favr_requests ffr 
                                JOIN users u
                                ON u.id = ffr.customer_id
                                AND ffr.customer_id = $userID 
                                AND ffr.friend_id IS NOT NULL 
                                AND NOT ffr.task_status = '$completed'
                                OR ffr.friend_id = $userID 
                                AND u.id = ffr.friend_id
                                AND NOT ffr.task_status = '$completed'";

        $result = $this->db->query($notifications_query);
        $result1 = $this->db->query($notifications_friend_request_query);
        $result2 = $this->db->query($notifications_friend_favr_request_query);
        if ($result || $result1 || $result2) {
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $row1 = $result1->fetch(PDO::FETCH_ASSOC);
            $row2 = $result2->fetch(PDO::FETCH_ASSOC);

            $_SESSION['main_notifications'] = $row['COUNT(*)'] + $row1['COUNT(*)'] + $row2['COUNT(*)'];
        } else {
            $_SESSION['main_notifications'] = 0;
        }

        return $_SESSION['main_notifications'];
    }

    /**
     * Process user account deletion
     * TODO: collect exit informational data on reason of leaving
     *
     * @param int $userID // must verify that this param is the session ID
     *
     *
     * @return boolean // return successful account removal and sign out
     */
    function processAccountDelete($userID)
    {
        if (isset($userID) && $userID == $_SESSION['user_info']['id']) {
            // delete friendships associated with this user
            $delete_friends_query = "DELETE
                                     FROM friends
                                     WHERE user_id = $userID
                                     OR friend_id = $userID";
            $result = $this->db->query($delete_friends_query);

            // delete pending requests by this user
            $complete = Data_Constants::DB_TASK_STATUS_COMPLETED;
            $delete_requests_query = "SELECT * 
                                      FROM marketplace_favr_requests
                                      WHERE customer_id = $userID
                                      AND NOT task_status = '$complete'";
            $result = $this->db->query($delete_requests_query);
            if ($result) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $task_id = $row['id'];
                    $this->processDeleteRequest($task_id, $userID);
                }
            }
            // delete pending friend requests by this user
            $delete_friends_requests_query = "SELECT * 
                                              FROM friends_favr_requests
                                              WHERE customer_id = $userID
                                              AND NOT task_status = '$complete'";
            $result = $this->db->query($delete_friends_requests_query);
            if ($result) {
                $rows = $result->fetchAll(PDO::FETCH_ASSOC);
                foreach ($rows as $row) {
                    $task_id = $row['id'];
                    $this->processDeleteRequest($task_id, $userID);
                }
            }
            // delete images associated with this user
            $userInfo = $this->getUserInfo($userID);
            $profile_image_array = unserialize($userInfo['profile_picture_path']);
            if (!empty($profile_image_array)) {
                if ($profile_image_array['name'] != "placeholder.png") {
                    if (file_exists(Data_Constants::IMAGE_UPLOAD_PROFILE_IMAGE_FILE_PATH . $profile_image_array['name'])) {
                        unlink(Data_Constants::IMAGE_UPLOAD_PROFILE_IMAGE_FILE_PATH . $profile_image_array['name']);
                    }
                }
            }

            // delete user account
            $delete_user_query = "DELETE FROM users WHERE id = $userID";
            $result = $this->db->query($delete_user_query);
            if ($result) {
                return true;
            } else {
                return false;
            }
        }
    }
}