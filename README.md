# Clover PHP Example

This application demonstrates how to complete OAuth through Clover to obtain an access token, then using the access token, retrieving merchant data using REST API.

## Quick Run
Download the example
Then run the following command on your terminal:
`cd [my-app-name]; php -S localhost:8080 -t public public/index.php`

## Clover OAuth Flow

In `/src/routes.php` you'll find where routes are defined.

`$params = $request->getParams();` will retrieve the URL params if present. eg. merchant_id, code, etc.

If no params are present, you will need to redirect to Clover with your app Id (client_id) and the redirect_uri as parameters.

`return $response->withRedirect("https://sandbox.dev.clover.com/oauth/authorize?client_id=Z58G1TDHEZRQG&redirect_uri=http://localhost:8080", 301);`

The merchant will be redirected to their Clover login page, where they can enter their login information.

Once they login, they will be returned to your webapp with params eg. merchant_id, code, etc. Grab those configs in addition to static `appSecret`, which you can find your app's settings.

`$vars = array(
                '{$appId}'=> $params["client_id"],
                '{$appSecret}'=> $appSecret,
                '{$codeUrlParam}'=> $params['code']
            );`

Using those params, make a call to Clover's OAuth endpoint:
` $url = strtr('https://sandbox.dev.clover.com/oauth/token?client_id={$appId}&client_secret={$appSecret}&code={$codeUrlParam}', $vars);`

In this example, this is done using cUrl:

`$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$oauth = curl_exec($ch);`

This will return an `access token` which can then be used to make calls to our REST endpoints: https://www.clover.com/api_docs/

## Clover Example REST Calls
