<?php

require_once 'HeurekaOvereno.php';

try {
    $overeno = new HeurekaOvereno('xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx' /* USE your own API key */);
    // SK shops should use $overeno = new HeurekaOvereno('9b011a7086cfc0210cccfbdb7e51aac8', HeurekaOvereno::LANGUAGE_SK);
    
    // set customer email - MANDATORY
    $overeno->setEmail('jan.novak@example.com');

    /**
     * Products names should be provided in UTF-8 encoding. The service can handle
     * WINDOWS-1250 and ISO-8859-2 if necessary             
     */
    $overeno->addProduct('Nokia N95');

    /**
     * And/or add products using item ID
     */
    $overeno->addProductItemId('B1234');

    // add order ID - BIGINT (0 - 18446744073709551615)
    $overeno->addOrderId(123456);

    // send request
    $overeno->send();
} catch (HeurekaOverenoException $e) {
    // handle errors
    print $e->getMessage();
}
