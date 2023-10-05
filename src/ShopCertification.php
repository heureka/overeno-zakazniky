<?php

namespace Heureka;

use Heureka\ShopCertification\ApiEndpoint;
use Heureka\ShopCertification\DuplicateProductItemIdException;
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
     * @var int|string
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
            if (function_exists('curl_version')) {
                $requester = new ShopCertification\CurlRequester();
            } else {
                $requester = new ShopCertification\PhpRequester();
            }
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
     * @param int|string $orderId ID of the customer's order
     *
     * @return self
     * @throws InvalidArgumentException
     */
    public function setOrderId($orderId)
    {
        if (strlen($orderId) > 255) {
            throw new InvalidArgumentException(
                sprintf('OrderId must be a string limited to 255 characters, "%s" given.', print_r($orderId, true))
            );
        }

        $this->orderId = $orderId;

        return $this;
    }

    /**
     * @param string $productItemId Must be same as ITEM_ID provided in the Heureka XML feed.
     *
     * @return ShopCertification
     * @throws DuplicateProductItemIdException
     */
    public function addProductItemId($productItemId)
    {
        $productItemId = (string)$productItemId;

        if (array_search($productItemId, $this->productItemIds) !== false) {
            throw new DuplicateProductItemIdException(
                sprintf('The productItemId "%s" was already added. Please check the implementation.', $productItemId)
            );
        }

        $this->productItemIds[] = $productItemId;

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

        $postData['apiKey'] = $this->apiKey;
        $postData['email'] = $this->email;

        if ($this->orderId) {
            $postData['orderId'] = $this->orderId;
        }

        if ($this->productItemIds) {
            $postData['productItemIds'] = $this->productItemIds;
        }

        $result = $this->requester->request(IRequester::ACTION_LOG_ORDER, [], $postData);

        $this->orderSent = true;

        return $result;
    }

}
