<?php

use RuntimeException;

/**
 * Uses Mcrypt to encrypt/decrypt data and cURL to send/receive POST data
 * across the World Wide Web.
 */
class EnCurl
{
    /** Mcrypt cipher */
    const CIPHER = MCRYPT_RIJNDAEL_128;

    /** Mcrypt cipher mode */
    const MODE = MCRYPT_MODE_CFB;

    /**
     * The pre-shared key for encrypting and decrypting. It can be anything you
     * like but needs to match when sending and receiving.
     * @var string The pre-shared key.
     */
    private static $key = null;

    /**
     * A 'packet' that stores data to be encrypted.
     * @var array The 'packet' used for storing data to be encrypted.
     */
    private static $packet = array();

    /**
     * The default name for the POST variable.
     * @var string The POST variable name.
     */
    private static $postName = 'encurl';

    /**
     * Generates an Initialization Vector to randomly seed the encryption.
     * This I.V. must match when decrypting the data.
     */
    private static function makeIv()
    {
        return mcrypt_create_iv(
            mcrypt_get_iv_size(self::CIPHER, self::MODE),
            MCRYPT_RAND
        );
    }

    /**
     * Checks if the pre-shared key has been set. Either returns true or
     * throws a Runtime Exception.
     * @return boolean True if the pre-shared key has been set (is not null).
     * @throws RuntimeException If the pre-shared key has not been set.
     */
    private static function isKeyOrFail()
    {
        if (is_null(self::$key)) {
            throw new RuntimeException('No pre-shared key found.');
        }

        return true;
    }

    /**
     * Set the pre-shared key.
     * @param mixed $key The pre-shared key. Must match at the receiving end.
     */
    public static function setKey($key)
    {
        self::$key = $key;
    }

    /**
     * Choose the variable name used for POST data.
     * @param string $postName The variable name used for sending POST data.
     *                         Must match when sending and receiving.
     */
    public static function setPostName($postName)
    {
        self::$postName = $postName;
    }

    /**
     * Adds data to the 'packet' that will be encrypted and sent.
     * @param mixed $data The data added.
     * @param string $key (Optional) A key for the data. Default: array length.
     */
    public static function add($data, $key = null)
    {
        if (is_null($key)) {
            $key = count(self::$packet);
        }

        self::$packet[$key] = $data;
    }

    /**
     * Uses Mcrypt to encrypt data. If data is passed, it is encrypted and
     * returned. If no data is passed, EnCurl's 'packet' data is encrypted.
     * @param mixed $data (Optional) The data to be encrypted.
     * @return string An encrypted string.
     */
    public static function encrypt($data = null)
    {
        self::isKeyOrFail();

        if (is_null($data)) {
            $data = self::$packet;
        }

        $iv = self::makeIv();

        $encrypted = mcrypt_encrypt(
            self::CIPHER,
            md5(self::$key),
            json_encode($data),
            self::MODE,
            $iv
        );

        /*
         * The I.V. does not need to be secret, but does need to match when
         * decrypting so it is prepended to the encrypted data.
         */
        return $iv . $encrypted;
    }

    /**
     * Uses Mcrypt to decrypt data.
     * @param string $data The data to be decrypted.
     * @return mixed The decrypted data.
     */
    public static function decrypt($data)
    {
        self::isKeyOrFail();

        $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        $iv = substr($data, 0, $ivSize);

        $decrypted = mcrypt_decrypt(
            self::CIPHER,
            md5(self::$key),
            substr($data, $ivSize),
            self::MODE,
            $iv
        );

        return json_decode($decrypted, true);
    }

    /**
     * Uses cURL to send an encrypted string to a remote URL as POST data.
     * @param string $url The full URL of the page to receive the POST data.
     * @return string The response from the receiving URL.
     */
    public static function send($url)
    {
        self::isKeyOrFail();

        $postData = array(
            self::$postName => rawurlencode(self::encrypt())
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        return curl_exec($ch);
    }

    /**
     * Uses cURL to receive an encrypted string either from a remote URL or
     * from POST data if no URL is given.
     * @param string $url (Optional) A URL of a page with an encrypted string.
     * @return string The decrypted data.
     */
    public static function receive($url = null)
    {
        self::isKeyOrFail();

        if (is_null($url)) {
            return self::decrypt(
                rawurldecode(filter_input(INPUT_POST, self::$postName))
            );
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        return self::decrypt(
            rawurldecode(curl_exec($ch))
        );
    }

} // class EnCurl
