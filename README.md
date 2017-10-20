# Clover PHP Example

This application demonstrates how to complete OAuth through Clover to obtain an access token, then using the access token, retrieving merchant data using REST API.

## Live version: https://clover-php-example.herokuapp.com/
If you don't have the app installed, you will be prompted to install it onto your test merchant during the initial OAuth process. (PHPExample - Z58G1TDHEZRQG)

![Homepage](homepage.png?raw=true "Homepage")

## Run locally
Download the example
Then run the following command on your terminal:
`cd [my-app-name]; php -S localhost:8080 -t public public/index.php`
You will need to replace `$_ENV('app_secret')` with your own app's app secret in settings page of your app.

## Clover OAuth Flow

In `/src/routes.php` you'll find where routes are defined.

`$params = $request->getParams();` will retrieve the URL params if present. eg. merchant_id, code, etc.

If no params are present, you will need to redirect to Clover with your app Id (client_id) and the redirect_uri as parameters.

```php
return $response->withRedirect("https://sandbox.dev.clover.com/oauth/authorize?client_id={client_id}&redirect_uri={uri}", 301);
```

The merchant will be redirected to their Clover login page, where they can enter their login information.

Once they login, they will be returned to your webapp with params eg. merchant_id, code, etc. Grab those configs in addition to static `appSecret`, which you can find your app's settings.

```php
$vars = array(
    '{$appId}'=> $params["client_id"],
    '{$appSecret}'=> $appSecret,
    '{$codeUrlParam}'=> $params['code']
);
```

Using those params, make a call to Clover's OAuth endpoint:
```php
$url = strtr('https://sandbox.dev.clover.com/oauth/token?client_id={$appId}&client_secret={$appSecret}&code={$codeUrlParam}', $vars);
```

In this example, this is done using cUrl:

```php
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$oauth = curl_exec($ch);
```

This will return an `access token` which can then be used to make calls to our REST endpoints: https://www.clover.com/api_docs/

## Clover Example REST Calls

Using cUrl, making REST API requests.
More info can be found in our [API Reference Page](https://www.clover.com/api_docs/)

```php
$ch = curl_init();
$url = 'https://apisandbox.dev.clover.com/v3/merchants/' . $mid . '/orders';
curl_setopt($ch, CURLOPT_URL, $url);
// Setting the access token ($oauth) as the `Authorization`
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
  'Authorization: Bearer ' . $oauth
));
curl_exec($ch);
```
