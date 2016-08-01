<?php
namespace strong2much\facebook;

use Yii;
use yii\base\Component;
use yii\base\Exception;
use yii\helpers\Json;
use yii\httpclient\Client;
use yii\web\HttpException;

/**
 * Api class
 *
 * @author   Denis Tatarnikov <tatarnikovda@gmail.com>
 */
class Api extends Component
{
    const API_BASE_URL = 'https://graph.facebook.com';

    /**
     * @var string API version
     */
    public $version = '2.6';

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

        $result = $this->makeRequest($method, ['query' => $params]);
        return $result;
    }

    /**
     * Makes the curl request to the url.
     * @param string $url relative url to request.
     * @param array $options HTTP request options. Keys: query, data, options, headers.
     * @return array|mixed the response.
     * @throws Exception
     */
    protected function makeRequest($url, $options = [])
    {
        $request = $this->initRequest($url, $options);
        $response = $request->send();

        $data = $response->getData();
        $error = $this->fetchJsonError($data);
        if($error != null) {
            throw new Exception($error['message'], $error['code']);
        }

        if(!$response->isOk) {
            Yii::error(
                'Invalid response http code: ' . $response->getStatusCode() . '.' . PHP_EOL .
                'Headers: ' . Json::encode($response->getHeaders()->toArray()) . '.' . PHP_EOL .
                'URL: ' . $url . PHP_EOL .
                'Options: ' . Json::encode($options) . PHP_EOL .
                'Result: ' . (is_array($data) ? Json::encode($data) : var_export($data, true)),
                __METHOD__
            );
            throw new HttpException($response->getStatusCode());
        }

        return $data;
    }

    /**
     * Initializes a new request.
     * @param string $url relative url to request.
     * @param array $options HTTP request options. Keys: query, data, headers, options
     * @return \yii\httpclient\Request
     */
    protected function initRequest($url, $options = [])
    {
        $client = new Client([
            'baseUrl' => self::API_BASE_URL.'/v'.$this->version,
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);

        $request = $client->createRequest();
        if (isset($options['data'])) {
            $request->setMethod('post');
            $request->setData($options['data']);
        }
        if (!empty($options['headers'])) {
            $request->setHeaders($options['headers']);
        }
        if (!empty($options['options'])) {
            $request->setOptions($options['options']);
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
        $request->setUrl($url);

        return $request;
    }

    /**
     * Returns the error info from json.
     * @param array $json the json response.
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