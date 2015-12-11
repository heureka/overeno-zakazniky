<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $testingApiKey = '9b011a7086cfc0210cccfbdb7e51aac8'; // USE your own API key!

    $language = \Heureka\Overeno::LANGUAGE_CZ; // you can use LANGUAGE_SK as well

    $overeno = new \Heureka\Overeno($testingApiKey, $language);

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
    $overeno->setOrderId(123456);

    // send request
    if ($overeno->send()) {
        print('Success');
    }

} catch (\Heureka\Overeno\Exception $e) {
    // handle errors
    print $e->getMessage();
}
