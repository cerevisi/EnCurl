<?php

/*
 * This page receives POST data.
 */

include_once 'EnCurl.php';

EnCurl::setKey('This is the pre-shared key. You can set this to whatever you wish!');
//EnCurl::setPostName('somePostVarName');

/*
 * Anything output on this page is read by the sending page.
 */

echo "Hello from EnCurl receive!\n";

$response = EnCurl::receive();

print_r($response);
