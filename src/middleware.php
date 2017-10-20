<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

//Using session to hold oauth in cookie
$app->add(new \Slim\Middleware\Session([
  'name' => 'clover_session',
  'autorefresh' => true,
  'lifetime' => '1 hour'
]));
