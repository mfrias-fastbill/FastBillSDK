<a href="www.fastbill.com"><img src="http://www.trendlupe.de/uploads/Bilder/diverse/FastBill_Logo_250x80.png"/></a>
==========================

<h3>FastBill SDK </h3>PHP library for integrating FastBill API communication to your applications - http://www.fastbill.com
<br><b>Requires</b> the cURL PHP and the JSON PHP extensions.
<br>
<br>
<a name="docu_links"><b>API DOCUMENTATION SOURCES</b></a>
<br>FastBill - http://www.fastbill.com/docs/FastBill_API-Doku_V1-6.pdf?v=1
<br>FastBill Automatic - https://automatic.fastbill.com/docs/API-Doku_FBA_V1-11.pdf
<br>


<h2>Usage</h2>

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

Create an instance of the _Fastbill_ class with the configuration array as a parameter

```
$fb = new Fastbill($config);
```

<h3>Perform requests</h3>

Make your API calls directly from the instance and process the response as an array of values
<a href="#docu_links"><br>_See the API Documentation for the response structure_</a>

```
$response = $fb->getCustomers();

echo '<pre>';
print_r($response['RESPONSE']['CUSTOMERS']);
echo '</pre>';

```

Implement your personalized calls in the <b>fastbill.php</b> class to create the requests you need for your application.
<br>See the corresponding API documentation for the request structure.

<br><b>Example:</b> The following XML request is used for getting the invoice with the number 25

```
<?xml version="1.0" encoding="utf-8"?>
<FBAPI>
   <SERVICE>invoice.get</SERVICE>
   <FILTER>
       <INVOICE_NUMBER>25</INVOICE_NUMBER>
   </FILTER>
</FBAPI>
```
Create a function in the <b>fastbill.php</b> class and start from the <b>SERVICE</b> tag and work your way through the XML body to create an array with the <b>tags</b> as <b>keys</b> and setting their corresponding values

```
# (Example) Return the invoice with the given invoice number

function getInvoiceByNumber($invoiceNumber){

        $data = array('SERVICE' => 'invoice.get',
                      'FILTER' => array(
                          'INVOICE_NUMBER' => $invoiceNumber
                      ));
                      
        return $this->FB_APIRequest($data);        
    }
```
Don't forget to check the <b>My_FB_App_Sample.php</b> to see how this works


<h3>Additional info</h3>

<b>API DOCUMENTATION SOURCES</b>
<br>_FastBill_ - http://www.fastbill.com/docs/FastBill_API-Doku_V1-6.pdf?v=1
<br>_FastBill Automatic_ - https://automatic.fastbill.com/docs/API-Doku_FBA_V1-11.pdf
<br>
<br>
Contact:
<br>mfrias@fastbill.com
<br><b>Mauricio Frias</b>


