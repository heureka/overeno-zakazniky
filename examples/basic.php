<?php

require_once __DIR__ . '/../vendor/autoload.php';

try {
    // Use your own API key here. And keep it secret!
    $apiKey = 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
    $options = [
        // Use \Heureka\ShopCertification::HEUREKA_SK if your e-shop is on heureka.sk
        'service' => \Heureka\ShopCertification::HEUREKA_CZ,
    ];

    $shopCertification = new \Heureka\ShopCertification($apiKey, $options);

    // Set customer email - it is MANDATORY.
    $shopCertification->setEmail('jan.novak@example.com');

    // Set order ID - it helps you track your customers' orders in Heureka shop administration.
    $shopCertification->setOrderId(1597884);

    // Add products using ITEM_ID (your products ID) - you HAVE TO put in all the products which the customer ordered.
    $shopCertification->addProductItemId('165899412');
    $shopCertification->addProductItemId('998884614');

    // And finally send the order to our service.
    $shopCertification->logOrder();

    // Everything went well - we are done here.
    // You can redirect the customer to some nice page and thank him for the order. :-)

} catch (\Heureka\ShopCertification\Exception $e) {
    // Something unexpected happened.
    // We can print the message for debug purposes only,
    // DO NOT ever do that on your production environment.
    var_dump($e->getMessage());
}
