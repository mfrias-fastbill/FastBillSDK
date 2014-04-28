<?php

/**
 * Copyright 2014 FastBill GmbH.
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
 *
 * @category   FastBill SDK
 * @version    0.9
 * @author     Mauricio Frias <mfrias@fastbill.com>
 * @copyright  2014 FastBill GmbH
 */
require_once "base_fastbill.php";

class Fastbill extends BaseFastBill {

    /* EXAMPLES
     *      
     * Implement here your functions to create the requests 
     * for the API setting the data body in an array.
     * 
     * i.e: getCustomers()
     * @return array with the response from the server with the list
     * of customers in the account
     * 
     * (see API Documentation http://www.fastbill.com/api/)
     */
    function getCustomers() {
        $data = array('SERVICE' => 'customer.get');
        return $this->FB_APIRequest($data);
    }
    
    function getInvoices() {
        $data = array('SERVICE' => 'invoice.get');
        return $this->FB_APIRequest($data);
    }
    
    function getInvoiceByNumber($invoiceNumber){
        $data = array('SERVICE' => 'invoice.get',
                      'FILTER' => array(
                          'INVOICE_NUMBER' => $invoiceNumber
                      ));
        return $this->FB_APIRequest($data);        
    }
    
    function createRevenue($customerId, $subTotal, $file){
        $data = array('SERVICE' => 'revenue.create',
                      'DATA' => array(
                          'CUSTOMER_ID' => $customerId,
                          'SUB_TOTAL' => $subTotal,
                          'INVOICE_DATE' => '2014-04-28'
                      ));
        return $this->FB_APIRequest($data,$file);        
    }
    
        
     /**
     * Makes a CURL request to the FastBill API with the data
     * 
     * @param array $data structure of the request to the FastBill API 
     * (see API Documentation http://www.fastbill.com/api/)     
      *@param $file (optional) File path of the document to attach for the upload     
     * @return string JSON encoded string with the result from the server.
     */
    function FB_APIRequest($data , $file=null) {
        return $this->request($data , $file);
    }

}
