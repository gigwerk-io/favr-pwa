<?php
/**
 * Created by PhpStorm.
 * User: haronarama
 * Date: 7/11/18
 * Time: 11:39 PM
 *
 * @author haronarama
 */

// include our OAuth2 Server object
require_once __DIR__.'/server.php';

// Handle a request for an OAuth2.0 Access Token and send the response to the client
$server->handleTokenRequest(OAuth2\Request::createFromGlobals())->send();