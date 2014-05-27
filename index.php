<?php

include_once 'EnCurl.php';

/*
 * This sets the pre-shared key for the encryption process. It MUST be the same
 * in any send and receive files or the data can not be decrypted.
 */
EnCurl::setKey('This is the pre-shared key. You can set this to whatever you wish!');

/*
 * This is the name of the POST variable. If not manually set 'encurl' is used.
 * This also needs to match in the send and receive files.
 */
//EnCurl::setPostName('somePostVarName');

/*
 * Encrypt and decrypt
 */
$string = "This is the string to be encrypted!";

var_dump($string);

$encrypted = EnCurl::encrypt($string);

var_dump($encrypted);

$decrypted = EnCurl::decrypt($encrypted);

var_dump($decrypted);

echo "<hr />";

/*
 * Encrypt and send
 */
// Generate some data for sending
$someData = 12345;
$someOtherData = "This is someOtherData";
$someArray = array(
	'This is someArray 0',
	'This is someArray 1',
);
$someOtherArray = array(
	'zero' => 'This is someOtherArray zero',
	'one'  => 'This is someOtherArray one',
);

// Add the data to the 'packet'
EnCurl::add($someData, 'someName');
EnCurl::add($someOtherData);
EnCurl::add($someArray);
EnCurl::add($someOtherArray, 'someOtherName');

// The response is what is output by the receiving page
$response = EnCurl::send('http://localhost/encurl/receive.php');

var_dump($response);

echo "<hr />";

/*
 * Receive and decrypt
 */
$received = EnCurl::receive('http://localhost/encurl/send.php');

var_dump($received);
/* */