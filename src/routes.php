<?php

use Slim\Http\Request;
use Slim\Http\Response;

// Routes
$app->get('/', function (Request $request, Response $response, array $args) use ($app) {
    //If access token doesn't exist in session
    if(!isset($this->session->oauth) || !isset($this->session->mid)) {
      //Grab params from URL
      $params = $request->getParams();

      // If params to call OAuth exist, get access token
      if(array_key_exists("code", $params)) {
        $mid = $params["merchant_id"];
        $appSecret = $_ENV['app_secret']; // You'll find this in your app's settings page.
        $ch = curl_init();
        $vars = array(
            '{$appId}'=> $params["client_id"],
            '{$appSecret}'=> $appSecret,
            '{$codeUrlParam}'=> $params['code']
        );
        $url = strtr('https://sandbox.dev.clover.com/oauth/token?client_id={$appId}&client_secret={$appSecret}&code={$codeUrlParam}', $vars);
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $result = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpcode == 200) {
                $oauth = json_decode($result, true)['access_token'];
                try{
                  //Store oauth/merchantId in session for 1hr. (in cookie)
                  $this->session->oauth = $oauth;
                  $this->session->mid = $mid;
                }catch (Exception $e) {
                  print_r("failed to insert into session");
                }
            }else {
              return $response->withRedirect("https://sandbox.dev.clover.com/oauth/authorize?client_id=" . $_ENV['client_id'] . "&redirect_uri=https://clover-php-example.herokuapp.com/", 301);
            }
        } catch (HttpException $ex) {
            echo $ex;
        } finally {
            curl_close($ch);
        }
      }else {
        try{
            return $this->renderer->render($response, 'redirect.phtml', $args);
        }catch(Exception $e) {
            print_r("Error, could not redirect to www.clover.com");
        }
      }
    }
    return $this->renderer->render($response, 'index.phtml', $args);
});

$app->get('/redirect', function (Request $req, Response $resp, array $args) use ($app) {
  return $resp->withRedirect("https://sandbox.dev.clover.com/oauth/authorize?client_id=" . $_ENV['client_id'] . "&redirect_uri=https://clover-php-example.herokuapp.com/", 301);
});

$app->get('/orders', function (Request $req, Response $resp, array $args) use ($app) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/orders';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth
  ));
  curl_exec($ch);
});

$app->get('/items', function (Request $req, Response $resp, array $args) use ($app) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/items';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth
  ));
  curl_exec($ch);
});

$app->get('/employees', function (Request $req, Response $resp, array $args) use ($app) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/employees';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth
    ));
  curl_exec($ch);
});

$app->get('/customers', function (Request $req, Response $resp, array $args) use ($app) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/customers';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth
  ));
  curl_exec($ch);
});

$app->post('/orders', function (Request $request, Response $response) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $body = json_encode(array('state' => 'open'));
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/orders';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, count($body));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth,
    'Content-Type: application/json'
  ));
  curl_exec($ch);
});

$app->post('/items', function (Request $request, Response $response) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $body = json_encode(array('name' => urlencode($_POST['name']), 'price' => urlencode(((Integer)$_POST['price'])*100)));
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/items';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, count($body));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth,
    'Content-Type: application/json'
  ));
  curl_exec($ch);
});

$app->post('/employees', function (Request $request, Response $response) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $body = json_encode(array('name' => urlencode($_POST['name'])));
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/employees';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, count($body));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth,
    'Content-Type: application/json'
  ));
  curl_exec($ch);
});

$app->post('/customers', function (Request $request, Response $response) {
  $oauth = $this->session->oauth;
  $mid = $this->session->mid;
  $body = json_encode(array('firstName' => urlencode($_POST['fname']), 'lastName' => urlencode($_POST['lname'])));
  $ch = curl_init();
  $url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/customers';
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch,CURLOPT_POST, count($body));
  curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Authorization: Bearer ' . $oauth,
    'Content-Type: application/json'
  ));
  curl_exec($ch);
});