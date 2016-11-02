<?php

namespace Heureka\ShopCertification;

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
    public function request($action, array $getData = [], array $postData = [])
    {
        try {
            $options = [
                'http' => [
                    'method'        => 'GET',
                    'ignore_errors' => true,
                ]
            ];

            if ($postData) {
                $json = json_encode($postData, JSON_PRETTY_PRINT);
                if ($json === false) {
                    throw new RequesterException(
                        'Failed to serialize data into JSON. Data: ' . var_export($postData, true)
                    );
                }

                $options['http'] = $options['http'] + [
                    'method'  => 'POST',
                    'header'  => sprintf(
                        "Content-Type: application/json\r\nContent-Length: %d\r\n",
                        strlen($json)
                    ),
                    'content' => $json,
                ];
            }

            $getParams = $getData ? '?' . http_build_query($getData) : '';
            $context = stream_context_create($options);
            $fp = @fopen($this->endpoint->getUrl() . $action . $getParams, 'r', false, $context); // @ intentionally
            if ($fp === false) {
                $error = error_get_last();
                throw new RequesterException(sprintf('fopen failed: [%d] %s', $error['type'], $error['message']));
            }

            $result = stream_get_contents($fp);
            $metadata = stream_get_meta_data($fp);
            fclose($fp);

            $httpCode = 0;
            foreach ($metadata['wrapper_data'] as $header) {
                if (strpos($header, 'HTTP') === 0) {
                    list($version, $httpCode, $phrase) = explode(' ', $header, 3);
                    $httpCode = (int)$httpCode;
                    break;
                }
            }

        } catch (RequesterException $e) {
            throw $e;
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
