<?php

/*
 * This page outputs an encrypted string that anyone could view.
 */

include_once 'EnCurl.php';

EnCurl::setKey('This is the pre-shared key. You can set this to whatever you wish!');
//EnCurl::setPostName('somePostVarName');

$string = "This is a string for testing a broadcast encrypted message.";

echo EnCurl::encrypt($string);

/*
 * Do not output anything after the echo of the encrypted string
 */