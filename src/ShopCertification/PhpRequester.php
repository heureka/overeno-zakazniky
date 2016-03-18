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

            $urlParts = parse_url($this->endpoint->getUrl() . $action);
            $scheme = isset($urlParts['scheme']) && $urlParts['scheme'] === 'https' ? 'ssl://' : '';
            $port = isset($urlParts['port']) ? $urlParts['port'] : ($urlParts['scheme'] === 'https' ? 443 : 80);
            $fp = fsockopen($scheme . $urlParts['host'], $port, $errorNo, $errorMessage);
            if ($fp === false) {
                throw new RequesterException(sprintf('fsockopen failed: [%d] %s', $errorNo, $errorMessage));
            }

            $path = $urlParts['path'] . (isset($urlParts['query']) ? '?' . $urlParts['query'] : '');
            fwrite($fp, sprintf("POST %s HTTP/1.1\r\n", $path));
            fwrite($fp, sprintf("Host: %s\r\n", $urlParts['host']));
            fwrite($fp, "Content-Type: application/json\r\n");
            fwrite($fp, sprintf("Content-Length: %d\r\n", strlen($json)));
            fwrite($fp, "Connection: close\r\n");
            fwrite($fp, "\r\n");
            fwrite($fp, $json);

            $response = stream_get_contents($fp);
            fclose($fp);

            list($headers, $result) = explode("\r\n\r\n", $response, 2);
            list($statusHeader, $_) = explode("\r\n", $headers, 2);
            list($version, $httpCode, $phrase) = explode(' ', $statusHeader, 3);
            $httpCode = (int)$httpCode;
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
