<?php

namespace Heureka\ShopCertification;

use Heureka\ShopCertification;

/**
 * @author Jakub Chábek <jakub.chabek@heureka.cz>
 */
class PhpRequester implements IRequester
{

    /**
     * @var ApiEndpoint
     */
    private $endpoint;

    /**
     * @param ApiEndpoint $endpoint
     */
    public function setApiEndpoint(ApiEndpoint $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @inheritdoc
     */
    public function request($action, array $data)
    {
        try {
            $json = json_encode($data, JSON_PRETTY_PRINT);
            if ($json === false) {
                throw new RequesterException('Failed to serialize data into JSON. Data: ' . var_export($data, true));
            }

            $options = [
                'http' => [
                    'method'        => 'POST',
                    'header'        => "Content-Type: application/json\r\n" . sprintf(
                            "Content-Length: %d\r\n",
                            strlen($json)
                        ),
                    'content'       => $json,
                    'ignore_errors' => true
                ]
            ];

            $context = stream_context_create($options);
            $fp = @fopen($this->endpoint->getUrl() . $action, 'r', false, $context);

            if ($fp === false) {
                $error = error_get_last();
                throw new RequesterException(sprintf('fopen failed: [%d] %s', $error['type'], $error['message']));
            }

            $result = stream_get_contents($fp);
            $metaData = stream_get_meta_data($fp);
            fclose($fp);

            $statusHeader = $metaData['wrapper_data'][0];
            list($version, $httpCode, $phrase) = explode(' ', $statusHeader, 3);
            $httpCode = (int)$httpCode;

        } catch (RequesterException $e) {
            throw new RequesterException($e->getMessage(), null, $e);
        } catch (\Exception $e) {
            $result = empty($result) ? '' :  ', result: ' . $result;
            $message = 'An error occurred during the transfer' . $result . "\n\n"
                     . "Please consider installing cURL and it's PHP extension - it is recommended.";
            throw new RequesterException($message, null, $e);
        }

        if ($httpCode !== 200) {
            throw new RequesterException(
                sprintf("Request resulted in HTTP code '%d'. Response result:\n%s", $httpCode, $result)
            );
        }

        return new Response($result);
    }

}
