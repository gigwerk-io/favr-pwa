<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 10/15/18
 * Time: 9:27 AM
 */
session_start();
include($_SERVER['DOCUMENT_ROOT'] . "/favr-pwa/include/autoload.php");

// component constants
$USER = "";
$ALERT_MESSAGE = "";

if (isset($_SESSION['user_info'])) {
    $USER = $_SESSION['user_info']['username']; // user is set from initial configuration
}
if(!isset($_GET['id'])){
    header("location: chat.php");
}


$page = new Web_Page($USER);
$page->setTitle("Chat");
$chat = new Web_Chat();
if(isset($_POST['message'])){
    $chat->processSendMessage($_GET['id'], $_SESSION['user_info']['id'], $_POST['message']);
    $_POST = array();
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
    <script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" type="text/css" rel="stylesheet">
    <style>
        body{

            background: #ddd;

        }

        a {

            text-decoration: none !important;

        }

        label {

            color: rgba(120, 144, 156,1.0) !important;

        }

        .btn:focus, .btn:active:focus, .btn.active:focus {

            outline: none !important;
            box-shadow: 0 0px 0px rgba(120, 144, 156,1.0) inset, 0 0 0px rgba(120, 144, 156,0.8);
        }


        textarea:focus,
        input[type="text"]:focus,
        input[type="password"]:focus,
        input[type="datetime"]:focus,
        input[type="datetime-local"]:focus,
        input[type="date"]:focus,
        input[type="month"]:focus,
        input[type="time"]:focus,
        input[type="week"]:focus,
        input[type="number"]:focus,
        input[type="email"]:focus,
        input[type="url"]:focus,
        input[type="search"]:focus,
        input[type="tel"]:focus,
        input[type="color"]:focus,
        .uneditable-input:focus {
            border-color: rgba(120, 144, 156,1.0); color: rgba(120, 144, 156,1.0); opacity: 0.9;
            box-shadow: 0 0px 0px rgba(120, 144, 156,1.0) inset, 0 0 10px rgba(120, 144, 156,0.3);
            outline: 0 none; }


        .card::-webkit-scrollbar {
            width: 1px;
        }

        ::-webkit-scrollbar-thumb {
            border-radius: 9px;
            background: rgba(96, 125, 139,0.99);
        }

        .balon1, .balon2 {

            margin-top: 5px !important;
            margin-bottom: 5px !important;

        }


        .balon1 a {

            background: #42a5f5;
            color: #fff !important;
            border-radius: 20px 20px 3px 20px;
            display: block;
            max-width: 75%;
            padding: 7px 13px 7px 13px;

        }

        .balon1:before {

            content: attr(data-is);
            position: absolute;
            right: 15px;
            bottom: -0.8em;
            display: block;
            font-size: .750rem;
            color: rgba(84, 110, 122,1.0);

        }

        .balon2 a {

            background: #f1f1f1;
            color: #000 !important;
            border-radius: 20px 20px 20px 3px;
            display: block;
            max-width: 75%;
            padding: 7px 13px 7px 13px;

        }

        .balon2:before {

            content: attr(data-is);
            position: absolute;
            left: 13px;
            bottom: -0.8em;
            display: block;
            font-size: .750rem;
            color: rgba(84, 110, 122,1.0);

        }

        .bg-sohbet:before {

            content: "";
            background-image: url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGcgdHJhbnNmb3JtPSJ0cmFuc2xhdGUoMTAgOCkiIGZpbGw9Im5vbmUiIGZpbGwtcnVsZT0iZXZlbm9kZCI+PGNpcmNsZSBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iMS4yNSIgY3g9IjE3NiIgY3k9IjEyIiByPSI0Ii8+PHBhdGggZD0iTTIwLjUuNWwyMyAxMW0tMjkgODRsLTMuNzkgMTAuMzc3TTI3LjAzNyAxMzEuNGw1Ljg5OCAyLjIwMy0zLjQ2IDUuOTQ3IDYuMDcyIDIuMzkyLTMuOTMzIDUuNzU4bTEyOC43MzMgMzUuMzdsLjY5My05LjMxNiAxMC4yOTIuMDUyLjQxNi05LjIyMiA5LjI3NC4zMzJNLjUgNDguNXM2LjEzMSA2LjQxMyA2Ljg0NyAxNC44MDVjLjcxNSA4LjM5My0yLjUyIDE0LjgwNi0yLjUyIDE0LjgwNk0xMjQuNTU1IDkwcy03LjQ0NCAwLTEzLjY3IDYuMTkyYy02LjIyNyA2LjE5Mi00LjgzOCAxMi4wMTItNC44MzggMTIuMDEybTIuMjQgNjguNjI2cy00LjAyNi05LjAyNS0xOC4xNDUtOS4wMjUtMTguMTQ1IDUuNy0xOC4xNDUgNS43IiBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iMS4yNSIgc3Ryb2tlLWxpbmVjYXA9InJvdW5kIi8+PHBhdGggZD0iTTg1LjcxNiAzNi4xNDZsNS4yNDMtOS41MjFoMTEuMDkzbDUuNDE2IDkuNTIxLTUuNDEgOS4xODVIOTAuOTUzbC01LjIzNy05LjE4NXptNjMuOTA5IDE1LjQ3OWgxMC43NXYxMC43NWgtMTAuNzV6IiBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iMS4yNSIvPjxjaXJjbGUgZmlsbD0iIzAwMCIgY3g9IjcxLjUiIGN5PSI3LjUiIHI9IjEuNSIvPjxjaXJjbGUgZmlsbD0iIzAwMCIgY3g9IjE3MC41IiBjeT0iOTUuNSIgcj0iMS41Ii8+PGNpcmNsZSBmaWxsPSIjMDAwIiBjeD0iODEuNSIgY3k9IjEzNC41IiByPSIxLjUiLz48Y2lyY2xlIGZpbGw9IiMwMDAiIGN4PSIxMy41IiBjeT0iMjMuNSIgcj0iMS41Ii8+PHBhdGggZmlsbD0iIzAwMCIgZD0iTTkzIDcxaDN2M2gtM3ptMzMgODRoM3YzaC0zem0tODUgMThoM3YzaC0zeiIvPjxwYXRoIGQ9Ik0zOS4zODQgNTEuMTIybDUuNzU4LTQuNDU0IDYuNDUzIDQuMjA1LTIuMjk0IDcuMzYzaC03Ljc5bC0yLjEyNy03LjExNHpNMTMwLjE5NSA0LjAzbDEzLjgzIDUuMDYyLTEwLjA5IDcuMDQ4LTMuNzQtMTIuMTF6bS04MyA5NWwxNC44MyA1LjQyOS0xMC44MiA3LjU1Ny00LjAxLTEyLjk4N3pNNS4yMTMgMTYxLjQ5NWwxMS4zMjggMjAuODk3TDIuMjY1IDE4MGwyLjk0OC0xOC41MDV6IiBzdHJva2U9IiMwMDAiIHN0cm9rZS13aWR0aD0iMS4yNSIvPjxwYXRoIGQ9Ik0xNDkuMDUgMTI3LjQ2OHMtLjUxIDIuMTgzLjk5NSAzLjM2NmMxLjU2IDEuMjI2IDguNjQyLTEuODk1IDMuOTY3LTcuNzg1LTIuMzY3LTIuNDc3LTYuNS0zLjIyNi05LjMzIDAtNS4yMDggNS45MzYgMCAxNy41MSAxMS42MSAxMy43MyAxMi40NTgtNi4yNTcgNS42MzMtMjEuNjU2LTUuMDczLTIyLjY1NC02LjYwMi0uNjA2LTE0LjA0MyAxLjc1Ni0xNi4xNTcgMTAuMjY4LTEuNzE4IDYuOTIgMS41ODQgMTcuMzg3IDEyLjQ1IDIwLjQ3NiAxMC44NjYgMy4wOSAxOS4zMzEtNC4zMSAxOS4zMzEtNC4zMSIgc3Ryb2tlPSIjMDAwIiBzdHJva2Utd2lkdGg9IjEuMjUiIHN0cm9rZS1saW5lY2FwPSJyb3VuZCIvPjwvZz48L3N2Zz4=');
            opacity: 0.06;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            height:100%;
            position: absolute;

        }
    </style>
    <?php
    $id = $_GET['id'];
    echo
    "<script>

        function ajax() {
            var req = new XMLHttpRequest();

            req.onreadystatechange = function () {
                if(req.readyState == 4 && req.status==200){
                    document.getElementById('content').innerHTML = req.responseText;
                }
            }
            req.open('GET', 'process.php?id=$id',true);
            req.send();
        }
        setInterval(function () {
            ajax();
            return false
        }, 100)
       
    </script>
";
    ?>
</head>
<body>
<div class="jumbotron m-0 p-0 bg-transparent">
    <div class="row m-0 p-0 position-relative">
        <div class="col-12 p-0 m-0 position-absolute" style="right: 0px;">
            <div class="card border-0 rounded" style="box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10), 0 6px 10px 0 rgba(0, 0, 0, 0.01); overflow: hidden; height: 100vh;">

                <?php
                $chat->processChatHeader($_GET['id']);
                //$chat->processChatMessages($_GET['id']);
                ?>
                <div class="card bg-sohbet border-0 m-0 p-0" style="height: 100vh;">
                    <div id="sohbet" class="card border-0 m-0 p-0 position-relative bg-transparent" style="overflow-y: auto; height: 100vh;" onload="ajax(); return false;">
                        <div id="content"></div>
                    </div>
                </div>

                <div class="w-100 card-footer p-0 bg-light border border-bottom-0 border-left-0 border-right-0">

                    <form class="m-0 p-0" action="room.php?id=<?php echo $_GET['id'];?>" method="POST" autocomplete="off">

                        <div class="row m-0 p-0">
                            <div class="col-9 m-0 p-1">

                                <input id="text" class="mw-100 border rounded form-control" type="text" name="message" title="Type a message..." placeholder="Type a message..." required>

                            </div>
                            <div class="col-3 m-0 p-1">

                                <button class="btn btn-outline-secondary rounded border w-100" title="Gönder!" style="padding-right: 16px;"><i class="fa fa-paper-plane" aria-hidden="true"></i></button>

                            </div>
                        </div>

                    </form>

                </div>

            </div>
        </div>

    </div>
</div>


<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<?php
$page->renderFooter(false);
?>
</body>
</html>
