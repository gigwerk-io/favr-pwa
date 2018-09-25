<?php
/**
 * Created by PhpStorm.
 * User: solomonantoine
 * Date: 9/12/18
 * Time: 9:56 AM
 */

class Web_Confirm
{
    /**
     * Data source name
     * @var string
     */
    private $dsn = Data_Constants::DB_DSN;

    /**
     * Backend username
     * @var string
     */
    private $username = Data_Constants::DB_USERNAME;

    /**
     * Backend password
     * @var string
     */
    private $password = Data_Constants::DB_PASSWORD;

    public function __construct() {
        $this->db = $this->connect();
    }

    private function connect()
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

    public function sendConfirmationEmail(string $email, string $password)
    {
        $from = new SendGrid\Email("FAVR", "contact@askfavr.com");

        $subject = "Confirm Account ";
        $to = new SendGrid\Email($this->getUser($email), $email);
        $content = new SendGrid\Content("text/html",  $this->setMessage($this->getUser($email), $this->createLink($email, $password)));
        $mail = new SendGrid\Mail($from, $subject, $to, $content);
        $sg = new \SendGrid(\Data_Constants::SG_API);
        $sg->client->mail()->send()->post($mail);
        return $this;
    }

    private function createLink(string $email, string $password)
    {
        return Data_Constants::ROOT_PATH . "/home/confirm/?src=" . $this->encrypt_decrypt('encrypt', $email) . "&auth=" . $this->encrypt_decrypt('encrypt', $password);
    }

    private function getUser(string $email)
    {
        $sth = $this->db->query("SELECT * FROM users WHERE email='$email'");
        $row = $sth->fetch(PDO::FETCH_ASSOC);
        return $row['first_name'] . " ". $row['last_name'];
    }

    public function confirmAccount(string $encryptedEmail)
    {
        $email = $this->encrypt_decrypt('decrypt', $encryptedEmail);
        $this->db->query("UPDATE users SET confirmed =1 WHERE email = '$email'");
    }

    private function encrypt_decrypt($action, $string) {
        $output = false;
        $encrypt_method = "AES-256-CBC";
        $secret_key = 'This is my secret key';
        $secret_iv = 'This is my secret iv';
        // hash
        $key = hash('sha256', $secret_key);

        // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
        $iv = substr(hash('sha256', $secret_iv), 0, 16);
        if ( $action == 'encrypt' ) {
            $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
            $output = base64_encode($output);
        } else if( $action == 'decrypt' ) {
            $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
        }
        return $output;
    }

    private function setMessage(string $name, string $link)
    {
        return "<head>
  <title></title>
  <!--[if !mso]><!-- -->
  <meta http-equiv=\"X-UA-Compatible\" content=\"IE=edge\">
  <!--<![endif]-->
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=UTF-8\">
<style type=\"text/css\">
  #outlook a { padding: 0; }
  .ReadMsgBody { width: 100%; }
  .ExternalClass { width: 100%; }
  .ExternalClass * { line-height:100%; }
  body { margin: 0; padding: 0; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
  table, td { border-collapse:collapse; mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
  img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; -ms-interpolation-mode: bicubic; }
  p { display: block; margin: 13px 0; }
</style>
<!--[if !mso]><!-->
<style type=\"text/css\">
  @media only screen and (max-width:480px) {
    @-ms-viewport { width:320px; }
    @viewport { width:320px; }
  }
</style>
<!--<![endif]-->
<!--[if mso]>
<xml>
  <o:OfficeDocumentSettings>
    <o:AllowPNG/>
    <o:PixelsPerInch>96</o:PixelsPerInch>
  </o:OfficeDocumentSettings>
</xml>
<![endif]-->
<!--[if lte mso 11]>
<style type=\"text/css\">
  .outlook-group-fix {
    width:100% !important;
  }
</style>
<![endif]-->

<!--[if !mso]><!-->
    <link href=\"https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700\" rel=\"stylesheet\" type=\"text/css\">
    <style type=\"text/css\">

        @import url(https://fonts.googleapis.com/css?family=Ubuntu:300,400,500,700);

    </style>
  <!--<![endif]--><style type=\"text/css\">
  @media only screen and (min-width:480px) {
    .mj-column-per-100, * [aria-labelledby=\"mj-column-per-100\"] { width:100%!important; }
  }
</style>
</head>
<body style=\"background: #F9F9F9;\">
  <div style=\"background-color:#F9F9F9;\"><!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]-->
  <style type=\"text/css\">
    html, body, * {
      -webkit-text-size-adjust: none;
      text-size-adjust: none;
    }
    a {
      color:#1EB0F4;
      text-decoration:none;
    }
    a:hover {
      text-decoration:underline;
    }
  </style>
<div style=\"margin:0px auto;max-width:640px;background:transparent;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:0px;width:100%;background:transparent;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:40px 0px;\"><!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align:top;width:640px;\">
      <![endif]--><div aria-labelledby=\"mj-column-per-100\" class=\"mj-column-per-100 outlook-group-fix\" style=\"vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr><td style=\"word-break:break-word;font-size:0px;padding:0px;\" align=\"center\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:collapse;border-spacing:0px;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"width:138px;\"><a href=\"https://askfavr.com/\" target=\"_blank\"><img alt=\"\" title=\"\" height=\"38px\" src=\"https://askfavr.com/favr-pwa/assets/brand/favr_logo_rd.png\" style=\"border:none;border-radius:;display:block;outline:none;text-decoration:none;width:100%;height:38px;\" width=\"138\"></a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]--><div style=\"max-width:640px;margin:0 auto;box-shadow:0px 1px 5px rgba(0,0,0,0.1);border-radius:4px;overflow:hidden\"><div style=\"margin:0px auto;max-width:640px;background:#7289DA url(https://cdn.discordapp.com/email_assets/f0a4cc6d7aaa7bdf2a3c15a193c6d224.png) top center / cover no-repeat;\"><!--[if mso | IE]>
      <v:rect xmlns:v=\"urn:schemas-microsoft-com:vml\" fill=\"true\" stroke=\"false\" style=\"width:640px;\">
        <v:fill origin=\"0.5, 0\" position=\"0.5,0\" type=\"tile\" src=\"https://cdn.discordapp.com/email_assets/f0a4cc6d7aaa7bdf2a3c15a193c6d224.png\" />
        <v:textbox style=\"mso-fit-shape-to-text:true\" inset=\"0,0,0,0\">
      <![endif]-->
      <![endif]--></td></tr></tbody></table><!--[if mso | IE]>
        </v:textbox>
      </v:rect>
      <![endif]--></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]--><div style=\"margin:0px auto;max-width:640px;background:#ffffff;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:0px;width:100%;background:#ffffff;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:40px 70px;\"><!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align:top;width:640px;\">
      <![endif]--><div aria-labelledby=\"mj-column-per-100\" class=\"mj-column-per-100 outlook-group-fix\" style=\"vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr><td style=\"word-break:break-word;font-size:0px;padding:0px 0px 20px;\" align=\"left\"><div style=\"cursor:auto;color:#737F8D;font-family:Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-size:16px;line-height:24px;text-align:left;\">
            <p></p>

  <h2 style=\"font-family: Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-weight: 500;font-size: 20px;color: #4F545C;letter-spacing: 0.27px;\">Hey $name,</h2>
<p>Wowwee! Thanks for registering an account with FAVR! You're the coolest person in all the land (and I've met a lot of really cool people).</p>
<p>Before we get started, we'll need to verify your email.</p>

          </div></td></tr><tr><td style=\"word-break:break-word;font-size:0px;padding:10px 25px;\" align=\"center\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"border-collapse:separate;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"border:none;border-radius:3px;color:white;cursor:auto;padding:15px 19px;\" align=\"center\" valign=\"middle\" bgcolor=\"#FE2E2E\"><a href=\"$link\" style=\"text-decoration:none;line-height:100%;background:#FE2E2E;color:white;font-family:Ubuntu, Helvetica, Arial, sans-serif;font-size:15px;font-weight:normal;text-transform:none;margin:0px;\" target=\"_blank\">
            Verify Email
          </a></td></tr></tbody></table></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]--></div><div style=\"margin:0px auto;max-width:640px;background:transparent;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:0px;width:100%;background:transparent;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:0px;\"><!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align:top;width:640px;\">
      <![endif]--><div aria-labelledby=\"mj-column-per-100\" class=\"mj-column-per-100 outlook-group-fix\" style=\"vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr><td style=\"word-break:break-word;font-size:0px;\"><div style=\"font-size:1px;line-height:12px;\">&nbsp;</div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]--><div style=\"margin:0 auto;max-width:640px;background:#ffffff;box-shadow:0px 1px 5px rgba(0,0,0,0.1);border-radius:4px;overflow:hidden;\"><table cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:0px;width:100%;background:#ffffff;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"text-align:center;vertical-align:top;font-size:0px;padding:0px;\"><!--[if mso | IE]>
      <table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align:top;width:640px;\">
      <![endif]--><!--[if mso | IE]>
      </td></tr></table>
      <![endif]-->
      <!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" width=\"640\" align=\"center\" style=\"width:640px;\">
        <tr>
          <td style=\"line-height:0px;font-size:0px;mso-line-height-rule:exactly;\">
      <![endif]--><div style=\"margin:0px auto;max-width:640px;background:transparent;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" style=\"font-size:0px;width:100%;background:transparent;\" align=\"center\" border=\"0\"><tbody><tr><td style=\"text-align:center;vertical-align:top;direction:ltr;font-size:0px;padding:20px 0px;\"><!--[if mso | IE]>
      <table role=\"presentation\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\"><tr><td style=\"vertical-align:top;width:640px;\">
      <![endif]--><div aria-labelledby=\"mj-column-per-100\" class=\"mj-column-per-100 outlook-group-fix\" style=\"vertical-align:top;display:inline-block;direction:ltr;font-size:13px;text-align:left;width:100%;\"><table role=\"presentation\" cellpadding=\"0\" cellspacing=\"0\" width=\"100%\" border=\"0\"><tbody><tr><td style=\"word-break:break-word;font-size:0px;padding:0px;\" align=\"center\"><div style=\"cursor:auto;color:#99AAB5;font-family:Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-size:12px;line-height:24px;text-align:center;\">
      Sent by FAVR • <a href=\"https://www.facebook.com/FAVR-1932902273417961/\" style=\"color:#FE2E2E;text-decoration:none;\" target=\"_blank\">check our Facebook</a> • <a href=\"https://www.instagram.com/askfavr/\" style=\"color:#FE2E2E;text-decoration:none;\" target=\"_blank\">@askfavr</a>
    </div></td></tr><tr><td style=\"word-break:break-word;font-size:0px;padding:0px;\" align=\"center\"><div style=\"cursor:auto;color:#99AAB5;font-family:Whitney, Helvetica Neue, Helvetica, Arial, Lucida Grande, sans-serif;font-size:12px;line-height:24px;text-align:center;\">
      14 4th St SW #203, Rochester, MN 55902
    </div></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></td></tr></tbody></table></div><!--[if mso | IE]>
      </td></tr></table>
      <![endif]--></div>

</body>";
    }
}
