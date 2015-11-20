<?php
namespace strong2much\facebook;

use Yii;
use yii\base\Component;
use yii\base\Exception;

/**
 * Api class
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class Api extends Component
{
    /**
     * @var string API version
     */
    public $version = '2.4';

    /**
     * @var array Maps aliases to Facebook domains.
     */
    protected static $domainMap = [
        'api' => 'https://graph.facebook.com/',
    ];


    /**
     * @param string $method method name
     * @param array $params params for method
     * @return array the response
     */
    public function call($method, $params=[])
    {
        if(!isset($params['locale'])) {
            $params['locale'] = Yii::$app->language;
        }

        $result = (array)$this->makeRequest(static::$domainMap['api'].'v'.$this->version.'/'.$method, ['query' => $params]);

        return $result;
    }

    /**
     * Makes the curl request to the url.
     * @param string $url url to request.
     * @param array $options HTTP request options. Keys: query, data, referer, headers.
     * @param boolean $parseJson Whether to parse response in json format.
     * @return array|mixed the response.
     * @throws Exception
     */
    protected function makeRequest($url, $options = [], $parseJson = true)
    {
        $ch = $this->initRequest($url, $options);

        $result = curl_exec($ch);
        $headers = curl_getinfo($ch);

        if (curl_errno($ch) > 0) {
            throw new Exception(curl_error($ch), curl_errno($ch));
        }

        if ($headers['http_code'] != 200) {
            Yii::error(
                'Invalid response http code: ' . $headers['http_code'] . '.' . PHP_EOL .
                'URL: ' . $url . PHP_EOL .
                'Options: ' . var_export($options, true) . PHP_EOL .
                'Result: ' . $result, 'application.extensions.facebook'
            );
            throw new Exception(Yii::t('facebook', 'Invalid response http code: {code}.', ['{code}' => $headers['http_code']]), $headers['http_code']);
        }

        curl_close($ch);

        if ($parseJson) {
            $result = $this->parseJson($result);
        }

        return $result;
    }

    /**
     * Initializes a new session and return a cURL handle.
     * @param string $url url to request.
     * @param array $options HTTP request options. Keys: query, data, referer, headers.
     * @return resource cURL handle.
     */
    protected function initRequest($url, $options = [])
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);

        if (isset($options['referer'])) {
            curl_setopt($ch, CURLOPT_REFERER, $options['referer']);
        }

        if (isset($options['headers'])) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $options['headers']);
        }

        if (isset($options['query'])) {
            $url_parts = parse_url($url);
            if (isset($url_parts['query'])) {
                $query = $url_parts['query'];
                if (strlen($query) > 0) {
                    $query .= '&';
                }
                $query .= http_build_query($options['query']);
                $url = str_replace($url_parts['query'], $query, $url);
            }
            else {
                $url_parts['query'] = $options['query'];
                $new_query = http_build_query($url_parts['query']);
                $url .= '?' . $new_query;
            }
        }

        if (isset($options['data'])) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $options['data']);
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        return $ch;
    }

    /**
     * Parse response from {@link makeRequest} in json format and check OAuth errors.
     * @param string $response Json string.
     * @return array result as associative array.
     * @throws Exception
     */
    protected function parseJson($response)
    {
        try {
            $result = json_decode($response, true);
            $error = $this->fetchJsonError($result);
            if (!isset($result)) {
                throw new Exception(Yii::t('facebook', 'Invalid response format.', []), 500);
            }
            else {
                if (isset($error) && !empty($error['message'])) {
                    throw new Exception($error['message'], $error['code']);
                }
                else {
                    return $result;
                }
            }
        } catch (Exception $e) {
            throw new Exception($e->getMessage(), $e->getCode());
        }
    }

    /**
     * Returns the error info from json.
     * @param \stdClass $json the json response.
     * @return array the error array with 2 keys: code and message. Should be null if no errors.
     */
    protected function fetchJsonError($json)
    {
        if (isset($json['error'])) {
            return [
                'code' => $json['error']['code'],
                'message' => $json['error']['message'],
            ];
        }
        else {
            return null;
        }
    }
}