#EnCurl

**Encrypt and send data across the World Wide Web.**

*EnCurl* is a simple static class that can be used to encrypt/decrypt data and send/receive encrypted data from a remote URL.

EnCurl is a *portmanteau* of 'encrypt' and 'cURL'.

##Use

###First thing's first...

The very first thing you need to do is include the file: **EnCurl.php** at the top of your script.

```php
include_once __DIR__ . "/path/to/EnCurl.php";
```
Then, you need to set the *pre-shared key*. Do this by adding the following line after the include:

```php
EnCurl::setKey('This is the pre-shared key. You can set this to whatever you wish!');
```

You can set the key to whatever you like. However, it needs to be exactly the same in any send and receive files that you use.

######(Optional)

The dafault name used when sending/receiving data is *encurl*. For the most part, this should be fine, however if you wish to set it to something of your own, this can be done with the following:

```php
EnCurl::setPostName('variable_name');
```

This name needs match on any send and receive files that you use.

Once you have set the key (and optionally the name), you can use EnCurl in a number of ways.

###Example 1a: Encrypt data

If you just wish to encrypt data, you can do it with the **EnCurl::encrypt()** method:

```php
$string = "This is the string to be encrypted!";
$encrypted = EnCurl::encrypt($string);
```

Now, if you were to output *$encrypted* you would see something like:

>^úÌf|…æý.{–ï)x3Q±²å›ËÖ5ÖŠÃCÐ/ÉÈš;3uçA«Ÿi{Ð“ºCg

######Note

Because the encryption process uses a randomly generated seed (known as an initialization vector) your encrypted string will not look like the one above.

###Example 1b: Decrypt data

If you wish to decrypt it again, this is done with the **EnCurl::decrypt()** method:

```php
$decrypted = EnCurl::decrypt($encrypted);
```

All being well, if you output *$decrypted* you should see:

>This is the string to be encrypted!

###Example 2: Encrypt and Send

You can encrypt and send data to a remote URL, which needs to be set up to receive and decrypt it. 

#####Send Page:

This is done by adding data to a 'packet' one at a time and then sending it to the URL. You can optionally give each item a name. If no name is given, then that item's order in the packet is used (0 for first, 1 for second, etc.).

```php
$someData = 12345;
$someOtherData = "This is someOtherData";

EnCurl::add($someData, 'someName');
EnCurl::add($someOtherData);
```

Once you've added everything to the packet, sent it to the URL. It is encrypted automatically.

```php
$response = EnCurl::send('http://www.your-url.com/receive.php');
```
#####Receive Page:

This receives the data as POST data and decrypts it. 

```php
$response = EnCurl::receive();
```

*$response* will contain the data in the packet.

######Note

Any output from this page is read by the sending page. You can use this to acknowledge receipt of the data (don't forget to encrypt it!)

###Example 3: Receive and Decrypt

You can set up a web page that contains a string of encrypted data that can be read by anyone. This page can be read and decrypted.

#####Send Page:

The send page outputs an encrypted string for everyone to see.

```php
$string = "This is a string for testing a broadcast encrypted message.";
echo EnCurl::encrypt($string);
```

#####Receive Page:

The receive page reads the send page remotely and decrypts it.

```php
$received = EnCurl::receive('http://www.your-url.com/encurl/send.php');
```
All being well, if you output *$received* you should see:

>This is a string for testing a broadcast encrypted message.
