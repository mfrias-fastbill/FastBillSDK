<a href="www.fastbill.com"><img src="http://www.fastbill.com/firma-de/img/fb_logo.svg"/></a>
==========================

<h3>FastBill SDK </h3>PHP library for integrating FastBill API communication to your applications - http://www.fastbill.com
<br><b>Requires</b> the cURL PHP and the JSON PHP extensions.
<br>
<br>
<b>API DOCUMENTATION SOURCES</b>
<br>FastBill - http://www.fastbill.com/api
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
<br> _See the API Documentation for the response structure_

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
Create a function in the <b>fastbill.php</b> class and start from the <b>SERVICE</b> tag and work your way through the XML body to create an array with the <b>tags</b> as <b>keys</b> and setting their corresponding values. Send all this data as a parameter to the ```FB_APIResquest($data)``` function.

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

<br><b>Example:</b> A function that creates an invoice with items

```
function createInvoice( $customerId , $date ){
         $data = array('SERVICE' => 'invoice.create',
                      'DATA' => array(
                          'CUSTOMER_ID' => $customerId,
                          'INVOICE_DATE' => $date,
                          'ITEMS' => array(
                              0 => array(
                                  'ARTICLE_NUMBER' => 1,
                                  'UNIT_PRICE' => 15
                              ),
                              1 => array(
                                  'ARTICLE_NUMBER' => 2,
                                  'UNIT_PRICE' => 20
                              )
                          )
                      ));   
         return $this->FB_APIRequest($data);
    }

```
<b>Note:</b> the ```<ITEM>``` tag from the documentation has been replaced with numerical indexes ```0 , 1 ``` in the code.


<h4>Uploading files</h4>
Some FastBill API services allow you to upload files to your account (i.e: revenue.create). All you need for this, is to provide the <b>file path</b> of the document to upload as a parameter in the ```FB_APIResquest($data , $file)``` function 

```
# (Example) Create a new revenue entry and attach a document

function createRevenue($customerId, $subTotal, $file){
        $data = array('SERVICE' => 'revenue.create',
                      'DATA' => array(
                          'CUSTOMER_ID' => $customerId,
                          'SUB_TOTAL' => $subTotal,
                          'INVOICE_DATE' => '2014-04-28'
                      ));
        return $this->FB_APIRequest($data,$file);        
    }
```

Don't forget to check the <b>My_FB_App_Sample.php</b> to see how this works


<h3>Additional info</h3>

<b>API DOCUMENTATION SOURCES</b>
<br>_FastBill_ - http://www.fastbill.com/api
<br>_FastBill Automatic_ - https://automatic.fastbill.com/docs/API-Doku_FBA_V1-11.pdf
<br>
<br>
Contact:
<br>mfrias@fastbill.com
<br><b>Mauricio Frias</b>


