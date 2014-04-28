<!DOCTYPE html>
<!--

-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        try {
            require_once "FastbillSDK/fastbill.php";

            /*
             * Configuration values 
             */
            $config = array();
            
            $config['accountType'] = FASTBILL;
            $config['userName'] = '';  // Write your FastBill user name / email here
            $config['apiKey'] = '';    // Write the API Key found in your FastBill account                    
            $config['debug'] = true;
                        
            /*
             * Create your Fastbill instance with the set configurations 
             */
            $fb = new Fastbill($config);   
            
            /*
             * (EXAMPLE) Perform your request.
             *  To create new functions, see the class Fastbill
             */
            $response = $fb->getCustomers();

            /*
             * Display the response 
             */
            echo '<br>------------<br>';
            echo '<pre>';
            print_r($response);
            echo '</pre>';
            echo '<br>------------<br>';
            
        } catch (Exception $e) {
            echo $e;
        }
        ?>
    </body>
</html>
