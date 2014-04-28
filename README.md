fastbillSDK
===========

FastBill SDK for integrating API communication to php applications


Usage
=====

<h3>Reference</h3>


Include the fastbill.php file 


```
require_once "FastbillSDK/fastbill.php";
```

<h3>Configuration</h3>

Create an array and set the configuration values for the account to use with the API

```
$config = array();
            
$config['accountType'] = FASTBILL;
$config['userName']    = 'some_user@some_domain.com';
$config['apiKey']      = '123456FASTBILL789API456KEY741852';                        
$config['debug']       = true;
```

_NOTE:_ The account type corresponds to the FastBill product where the account is registered to. It also sets the corresponding API URL for you.<br>
<b>FASTBILL</b> for my.fastbill accounts - https://my.fastbill.com/api/1.0/api.php<br>
<b>AUTOMATIC</b> for FastBill Automatic accounts - https://automatic.fastbill.com/api/1.0/api.php<br>

<h3>Initialization</h3>

Create an instance of the Fastbill class with the configuration array as a parameter

```
$fb = new Fastbill($config);
```

<h3>Perform requests<h3>

Make your API calls directly from the instance by implementing personalized calls in th Fastbill.php class

```
$response = $fb->getCustomers();
```


