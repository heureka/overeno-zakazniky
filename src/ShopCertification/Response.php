<?php

namespace Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class Response
{

    /**
     * @var int
     */
    public $code;

    /**
     * @var string
     */
    public $message;

    /**
     * @var string|null
     */
    public $description;

    /**
     * @var string|null
     */
    public $resourceId;

    /**
     * @param string $json
     *
     * @throws JsonException
     * @throws InvalidResponseException
     */
    public function __construct($json)
    {
        $response = json_decode($json);
        if (($response instanceof \stdClass) === false) {
            throw new JsonException(sprintf(
                'Unexpected response "%s" returned. JSON error: [%d] %s',
                $json,
                json_last_error(),
                function_exists('json_last_error_msg') ? json_last_error_msg() : 'json_decode see documentation'
            ));
        }

        if (!isset($response->code) || !isset($response->message)) {
            throw new InvalidResponseException('Missing code or message in the response: ' . $json);
        }

        foreach ($response as $key => $value) {
            $this->$key = $value;
        }
    }

}

class JsonException extends Exception {}
class InvalidResponseException extends Exception {}
