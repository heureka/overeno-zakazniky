<?php

namespace Heureka\ShopCertification;

use Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ApiEndpoint
{

    const API_ENDPOINT_CZ = 'https://api.heureka.cz/shop-certification/v2/';
    const API_ENDPOINT_SK = 'https://api.heureka.sk/shop-certification/v2/';

    private static $knownServices = [
        ShopCertification::HEUREKA_CZ,
        ShopCertification::HEUREKA_SK,
    ];

    /**
     * @var int
     */
    private $service;

    /**
     * @param int $service
     *
     * @throws UnknownServiceException
     */
    public function __construct($service)
    {
        if (!in_array($service, self::$knownServices)) {
            throw new UnknownServiceException();
        }

        $this->service = $service;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        if ($this->service === ShopCertification::HEUREKA_CZ) {
            return self::API_ENDPOINT_CZ;
        }

        return $endpoint = self::API_ENDPOINT_SK;
    }

}

class UnknownServiceException extends Exception {}
