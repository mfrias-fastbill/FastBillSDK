<?php

/**
 * Copyright 2014 FastBill, GmbH.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */


if (!function_exists('curl_init')) {
    throw new Exception('FastBill needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
    throw new Exception('FastBill needs the JSON PHP extension.');
}

define('FASTBILL' , 0 );
define('AUTOMATIC', 1 );

/**
 * Class for managing our own Exception for better understanding and 
 * handling of errors
 */
class FastBillSDKException extends Exception {

    /**
     * Holds the details of the error
     * 
     * @var array with the error_code and the error_msg 
     */
    protected $result;

    /**
     * Accessor method to the Result property
     * 
     * @return array the $result property
     */
    public function getResult() {
        return $this->result;
    }

    /**
     * Creates a new Exception of FastBillSDK based on the information passed in the 
     * $result variable
     * 
     * @param array $result with the error_code and the error_msg
     */
    public function __construct($result) {
        $this->result = $result;

        $code = 0;
        if (isset($result['error_code']) && is_int($result['error_code'])) {
            $code = $result['error_code'];
        }
       
        if (isset($result['error_msg'])) {
            $msg = $result['error_msg'];
        } else {
            $msg = 'Unknown Error';
        }

        parent::__construct($msg, $code);
    }

    /**
     * String representation of the FastbillSDKException object
     * 
     * @return string code and message of the exception
     */
    public function __toString() {
        $str = '';
        if ($this->code != 0) {
            $str = $this->code . ' : ';
        }
        return $str . $this->message;
    }

}

/**
 * Encryption class with functions used to code and decode messages
 * Used for the FastBill user credentials when sent from the system.
 * NOTE: Do not modify this class. 
 */
class Encryption {

    public static function safe_b64encode($string) {

        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

    public static function safe_b64decode($string) {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }

    public static function encode($value, $sKey) {

        if (!$value) {
            return false;
        }
        $text = $value;
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $crypttext = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $sKey, $text, MCRYPT_MODE_ECB, $iv);
        return trim(self::safe_b64encode($crypttext));
    }

    public static function decode($value, $sKey) {

        if (!$value) {
            return false;
        }
        $crypttext = self::safe_b64decode($value);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);
        $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
        $decrypttext = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $sKey, $crypttext, MCRYPT_MODE_ECB, $iv);
        return trim($decrypttext);
    }

}

/**
 * Provides access to the FastBill Platform.  This class provides
 * a majority of the functionality needed, but the class is abstract
 * because it is designed to be sub-classed.  
 *
 */
abstract class BaseFastBill {

    /**
     * Version of the FastBill SDK
     */
    const VERSION = '0.9';   

    /**
     * Predefined CURL options used for the requests
     * 
     * @var array with predefined CURL options
     */
    public static $CURL_OPTS = array(
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 60,
    );

    /**
     * 'My FastBill' API URL address 
     */
    const FB_API_URL = 'https://my.fastbill.com/api/1.0/api.php';
    
    /**
     * 'FastBill Automatic' API URL address
     */
    const AUTOMATIC_API_URL = 'https://automatic.fastbill.com/api/1.0/api.php';
    
    /*
     * Variable that stores the selected API URL depending on the account
     * type configured ( 'accountType' FASTBILL or AUTOMATIC )
     */
    protected $API_URL;
    /**
     * App ID given by us (FastBill) to you when your app is in the listing
     * of FastBill Apps and available for other users to connect to.
     * This ID is also used for decoding the user credentials sent to your app.
     * 
     * @var string value of your app ID
     */
    protected $appId = '';
    
    /**
     * The FastBill user API Key
     * 
     * @var string value of the API key 
     */
    protected $apiKey = '';
    
    /**
     * The FastBill user name (email)
     * 
     * @var string value of the user name / email 
     */
    protected $userName = '';
    
    /**
     * The account type of the FastBill user. Default FASTBILL
     * - Options : FASTBILL for my.fastbill accounts
     *             AUTOMATIC for automatic.fastbill accounts
     * 
     * @var int value defined constants FASTBILL / AUTOMATIC 
     */
    protected $accountType = FASTBILL;
    
    /**
     * Config value for the Debug mode. This enables the visibility of the error messages
     * thrown by the FastBill SDK exceptions.
     * 
     * @var boolean value to activate the error messages1
     */
    protected $debug = true;

    /**
     * Accessor function to SET the App ID
     * 
     * @param string $appId
     * @return \BaseFastBill
     */
    public function setAppId($appId) {
        $this->appId = $appId;
        return $this;
    }

    /**
     * Accessor function to GET the App ID
     * 
     * @return string $appId    
     */
    public function getAppId() {
        return $this->appId;
    }

    /**
     * Accessor function to SET the FastBill user API KEY
     * 
     * @param string $apiKey
     * @return \BaseFastBill
     */
    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * Accessor function to GET the FastBill user API KEY
     * 
     * @return string $apiKey
     */
    public function getApiKey() {
        return $this->apiKey;
    }

    /**
     * Accessor function to SET the FastBill user name / email
     * 
     * @param string $userName
     * @return \BaseFastBill
     */
    public function setUserName($userName) {
        $this->userName = $userName;
        return $this;
    }

    /**
     * Accessor function to GET the FastBill user name / email
     * 
     * @return string $userName
     */
    public function getUserName() {
        return $this->userName;
    }
    
    /**
     * Accessor function to SET the FastBill account type
     * 
     * @param constant int $accountType value FASTBILL(0) or AUTOMATIC(1)
     * @return \BaseFastBill
     */
    public function setAccountType($accountType){
        $this->accountType = $accountType;
        if($this->accountType == 0 or $this->accountType == FASTBILL)
            $this->API_URL = self::FB_API_URL ;
        if($this->accountType == 1 or $this->accountType == AUTOMATIC)
            $this->API_URL = self::AUTOMATIC_API_URL ;
        return $this;
    }
    
    /**
     * Accessor function to GET the FastBill account type
     * 
     * @return const int FASTBILL(0) or AUTOMATIC(1)
     */
    public function getAccountType(){
        return $this->accountType;
    }    
    
    /**
     * Accessor function to SET the Debug mode for the execution of the FastBill SDK
     * 
     * @param boolean $debug
     * @return \BaseFastBill
     */
    public function setDebug($debug) {
        $this->debug = $debug;
        return $this;
    }

    /**
     * Accessor function to GET the Debug mode for the execution of the FastBill SDK
     * 
     * @return boolean $debug
     */
    public function getDebug() {
        return $this->debug;
    }

    /**
     * Initialize a FastBill Application.
     *
     * The configuration array with the values for:
     * - appId: (optional) the application ID given by FastBill.
     * - accountType : FASTBILL for my.fastbill accounts and AUTOMATIC for FastBill Automatic
     * - userName : (optional) the FastBill user name / email
     * - apiKey : (optional) the FastBill user API Key
     * - debug: (optional) boolean indicating the debug mode of the SDK (see line 186 $debug)
     *                     default value TRUE
     *
     * @param array $config The application configuration values
     */
    public function __construct($config) {
        
        if (isset($config['appId'])) {
            $this->setAppId($config['appId']);
        }
        
        if (isset($config['accountType'])) {
            $this->setAccountType($config['accountType']);
        }
        
        if (isset($config['userName'])) {
            $this->setUserName($config['userName']);
        }        
        
        if (isset($config['apiKey'])) {
            $this->setApiKey($config['apiKey']);
        }
        
        if (isset($config['debug'])) {
            $this->setDebug($config['debug']);
        }
    }
    
        
    /*
     * EncondeMessage for testing purposes
     */
    function encodeMessage($message) {
        return Encryption::encode($message, $this->getAppId());
    }

    /**
     * Gets an encrypted message and resolves the FastBill credentials 
     * to set them to their corresponding properties of the class.
     * Format of the resolved credentials 'username:API_Key'.
     *
     * @param string $message the encoded string with the credentials separated by ':'
     * @return array with the decoded username and key 
     */
    function decodeAndSetCredentials($message) {
            $c = Encryption::decode($message, $this->getAppId());
            $cr = explode(':', $c);
            
            if (sizeof($cr) != 2)
                return $this->throwSDKException(51);
            
            $creds = array('userName' => $cr[0],
                            'apiKey' => $cr[1]);

            if ($this->setCredentials($creds['userName'], $creds['apiKey']))
                return $creds;
            else
                return $this->throwSDKException(50);
    }

    /**
     * Sets the credential values to the properties of the class
     * 
     * @param sting $userName email of the FastBill user
     * @param string $apiKey the key from the FastBill user
     */
    function setCredentials($userName, $apiKey) {
        if ($userName != null and $apiKey != null) {
            $this->setUserName($userName);
            $this->setApiKey($apiKey);
            return true;
        }
        else
            return false;
    }

    /**
     * Makes an HTTP request. This method can be overridden by subclasses if
     * developers want to do fancier things or use something other than curl to
     * make the request.
     *
     * @param array $data The parameters to use for the POST body
     * @param file $file The file to attach to the request
     *
     * @return string The response text
     */
    protected function request($data, $file = NULL) {
        if ($data) {
            if ($this->userName != '' && $this->apiKey != '' && $this->API_URL != '') {

                $ch = curl_init();

                $data_string = json_encode($data);
                if(!$data_string)
                    return $this->throwSDKException (40);
                
                if ($file != NULL) {
                    $bodyStr = array("document" => "@" . $file, "httpbody" => $data_string);
                } else {
                    $bodyStr = array("httpbody" => $data_string);
                }
                
                curl_setopt($ch, CURLOPT_URL, $this->API_URL);

                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, $this->userName . ':' . $this->apiKey);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyStr);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_ENCODING, 'gzip');

                $exec = curl_exec($ch);

                if (!$exec) {
                    return $this->throwSDKException(20);
                }

                $result = json_decode($exec, true);

                curl_close($ch);

                return $result;
            } else {
                return $this->throwSDKException(10);
            }
        } else {
            return $this->throwSDKException(41);
        }
    }

    /**
     * Analyzes the supplied error code to create the appropriate SDK exception
     * and then it throws it. It also verifies the Debug mode.
     *
     * @param int $code Error code number.
     */
    public function throwSDKException($code) {
        if ($this->debug == true) {
            switch ($code) {
                case 10: $msg = 'ERROR : THERE IS A PROBLEM WITH THE CREDENTIALS';
                    break;                
                case 20: $msg = 'ERROR : CURL CONNECTION FAILED';
                    break;
                case 41: $msg = 'ERROR : THE DATA FOR THE REQUEST IS EMPTY';
                    break;
                case 40: $msg = 'ERROR : THE DATA FOR THE REQUEST IS NOT VALID';
                    break;
                case 50: $msg = 'ERROR : AN ERROR OCCURRED WHEN DECODING AND SETTINGS THE CREDENTIALS';
                    break;
                case 51: $msg = 'ERROR : THE MESSAGE DECODED IS NOT FORMATTED CORRECTLY';
                    break;
                default: $msg = 'ERROR : UNKNOWN';
            }

            $error = array('error_code' => $code, 'error_msg' => $msg);

            $e = new FastBillSDKException($error);
            throw $e;
        } else {
            return false;
        }
    }

}
