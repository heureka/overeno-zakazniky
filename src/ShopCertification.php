<?php

namespace Heureka;

use Heureka\ShopCertification\ApiEndpoint;
use Heureka\ShopCertification\CurlNotInstalledException;
use Heureka\ShopCertification\InvalidArgumentException;
use Heureka\ShopCertification\IRequester;
use Heureka\ShopCertification\Response;

/**
 * @author Vladimír Kašpar <vladimir.kaspar@heureka.cz>
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class ShopCertification
{

    const HEUREKA_CZ = 0;
    const HEUREKA_SK = 1;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var IRequester
     */
    private $requester;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string[]
     */
    private $productItemIds = [];

    /**
     * Used to prevent double sending of the order
     *
     * @var bool
     */
    private $orderSent = false;

    /**
     * @param string          $apiKey
     * @param array           $options
     * @param IRequester|null $requester
     *
     * @throws CurlNotInstalledException
     */
    public function __construct($apiKey, array $options = [], IRequester $requester = null)
    {
        $this->apiKey = $apiKey;

        $defaultOptions = [
            'service' => self::HEUREKA_CZ,
        ];

        $this->options = array_merge($defaultOptions, $options);
        $apiEndpoint = new ApiEndpoint($this->options['service']);

        if ($requester === null) {
            if (!function_exists('curl_version')) {
                throw new CurlNotInstalledException(
                    'cURL extension is not installed. Either install the cURL extension or provide your own requester.'
                );
            }

            $requester = new ShopCertification\CurlRequester();
        }

        $requester->setApiEndpoint($apiEndpoint);

        $this->requester = $requester;
    }

    /**
     * @param string $email Customer's e-mail address
     *
     * @return self
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * @param int $orderId ID of the customer's order
     *
     * @return self
     * @throws InvalidArgumentException
     */
    public function setOrderId($orderId)
    {
        if (!is_int($orderId)) {
            throw new InvalidArgumentException(
                sprintf('OrderId must be an integer, "%s" given.', print_r($orderId, true))
            );
        }

        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @param string $productItemId Must be same as ITEM_ID provided in the Heureka XML feed.
     *
     * @return self
     */
    public function addProductItemId($productItemId)
    {
        $this->productItemIds[] = (string)$productItemId;

        return $this;
    }

    /**
     * Sends the data you set to the Heureka ShopCertification service.
     *
     * @return Response
     *
     * @throws ShopCertification\Exception
     */
    public function logOrder()
    {
        if ($this->orderSent) {
            throw new ShopCertification\Exception('You already sent one order. Please check your implementation.');
        }

        if (!$this->email) {
            throw new ShopCertification\MissingInformationException("Customer email address isn't set and is mandatory.");
        }

        $data['apiKey'] = $this->apiKey;
        $data['email'] = $this->email;

        if ($this->orderId) {
            $data['orderId'] = $this->orderId;
        }

        if ($this->productItemIds) {
            $data['productItemIds'] = $this->productItemIds;
        }

        $result = $this->requester->request(IRequester::ACTION_LOG_ORDER, $data);
        if ($result->code !== 200) {
            throw new ShopCertification\Exception("Unexpected response:\n" . print_r($result, true));
        }

        $this->orderSent = true;
    }

}
