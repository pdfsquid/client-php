<?php

namespace PDFsquid;

use PDFsquid\Exceptions\PDFsquidException;

/**
 * PDFsquid zoneApi API client
 * @author Paweł Lange <plange@pdfsquid.com>
 * @see  https://docs.pdfsquid.com/
 * @package PDFsquid
 */
class ZoneApi
{
    /**
     * API client version
     */
    const CLIENT_VERSION = '0.9.2';

    /**
     * API key
     */
    private $api_key = null;

    /**
     * API secret
     */
    private $api_secret = null;

    /**
     * API zone
     */
    private $zone = null;

    /**
     * Use https in API
     * @var bool
     */
    private $ssl = true;

    /**
     * Use this API version
     * @var string
     */
    private $api_version = 'v1';

    /**
     * Set custom file name when downloading
     * @var
     */
    private $file_name = null;

    /**
     * ZoneApi constructor.
     * @param $api_key
     * @param $api_secret
     * @param $zone - API zone
     */
    public function __construct($api_key, $api_secret, $zone)
    {
        $this->api_key = $api_key;
        $this->api_secret = $api_secret;
        $this->zone = $zone;
    }

    /**
     * Set custom file name
     * @param $file_name
     */
    public function setFileName($file_name)
    {
        $this->file_name = $file_name;
    }

    /**
     * SSL options
     * @param $bool
     */
    public function setSsl($bool)
    {
        $this->ssl = $bool;
    }

    /**
     * Sets API version
     * @param $version
     */
    public function setApiVersion($version)
    {
        $this->api_version = $version;
    }

    /**
     * Convert HTML to PDF synchronously
     * @param $html
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return ResponseFile
     * @throws PDFsquidException
     */
    public function html2pdf($html, $params = array())
    {
        $this->checkParams($params);

        $params['html'] = $html;

        return $this->call('POST', '/html/pdf', true, $params, true);
    }

    /**
     * Convert HTML to Image (PNG, JPG) synchronously
     * @param $html
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return mixed
     * @throws PDFsquidException
     */
    public function html2img($html, $params = array())
    {
        $this->checkParams($params);

        $params['html'] = $html;

        return $this->call('POST', '/html/img', true, $params, true);
    }

    /**
     * Convert URL to PDF synchronously
     * @param $url
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return ResponseFile
     * @throws PDFsquidException
     */
    public function url2pdf($url, $params = array())
    {
        $this->checkParams($params);

        $params['url'] = $url;

        return $this->call('POST', '/url/pdf', true, $params, true);
    }


    /**
     * Convert URL to Image (PNG, JPG) synchronously
     * @param $url
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return ResponseFile
     * @throws PDFsquidException
     */
    public function url2img($url, $params = array())
    {
        $this->checkParams($params);

        $params['url'] = $url;

        return $this->call('POST', '/url/img', true, $params, true);
    }

    /**
     * Convert HTML to PDF asynchronously
     * @param $html
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return mixed
     * @throws PDFsquidException
     */
    public function html2pdfAsync($html, $params = array())
    {
        $this->checkParams($params);

        $params['html'] = $html;

        return $this->call('POST', '/html/pdf', false, $params, false);
    }


    /**
     * Convert HTML to Image (PNG, JPG) asynchronously
     * @param $html
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return mixed
     * @throws PDFsquidException
     */
    public function html2imgAsync($html, $params = array())
    {
        $this->checkParams($params);

        $params['html'] = $html;

        return $this->call('POST', '/html/img', false, $params, false);
    }

    /**
     * Convert URL to PDF asynchronously
     * @param $url
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return mixed
     * @throws PDFsquidException
     */
    public function url2pdfAsync($url, $params = array())
    {
        $this->checkParams($params);

        $params['url'] = $url;

        return $this->call('POST', '/url/pdf', false, $params, false);
    }


    /**
     * Convert URL to Image (PNG, JPG) asynchronously
     * @param $url
     * @param array $params - Additional parameters for API (https://docs.pdfsquid.com)
     * @return mixed
     * @throws PDFsquidException
     */
    public function url2imgAsync($url, $params = array())
    {
        $this->checkParams($params);

        $params['url'] = $url;

        return $this->call('POST', '/url/img', false, $params, false);
    }

    /**
     * Check ping to Zone API
     * Returns true if API is accessible
     * @return bool
     */
    public function isApiAccessible()
    {
        try {
            $this->call('GET', 'ping', false, array(), false);
            return true;
        } catch (\PDFsquid\Exceptions\PDFsquidException $ex) {
            return false;
        }
    }


    /**
     * Check if credentials are valid
     * Returns true if API is accessible
     * @return bool
     */
    public function isAuthCorrect()
    {
        try {
            $this->call('GET', '', false, array(), false);
            return true;
        } catch (\PDFsquid\Exceptions\PDFsquidException $ex) {
            return false;
        }
    }

    /**
     * Download file from asynchronous conversion
     * @param $id
     * @return ResponseFile
     */
    public function getFile($id)
    {
        return $this->call('GET', '/getfile/' . $id, false, array(), true);
    }

    /**
     * Perform request to API
     * @param $method
     * @param $route
     * @param $synchronous
     * @param $params
     * @param $download
     * @return mixed|string
     * @throws PDFsquidException
     */
    private function call($method, $route, $synchronous, $params, $download)
    {
        $url = ($this->ssl ? 'https://' : 'http://') . $this->zone . '.pdfsquid.com/';

        if ($route !== 'ping')
            $url .= $this->api_version;

        $url .= $route . ($synchronous ? '/sync' : '');

        $ch = curl_init();

        // Set url
        curl_setopt($ch, CURLOPT_URL, $url);

        // Set method
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        // Set options
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($ch, CURLOPT_HEADER, 1);

        // Set headers
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                "X-API-KEY: " . $this->api_key,
                "X-API-SECRET: " . $this->api_secret,
                "Content-Type: application/x-www-form-urlencoded; charset=utf-8"
            )
        );

        // Create body
        $body = http_build_query($params);

        // Set body
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);

        // Send the request
        $response = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $body = substr($response, $header_size);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        $error = curl_error($ch);

        if ($error != '') {
            $ex = new \PDFsquid\Exceptions\PDFsquidException('HTTP error: ' . $error);
            $ex->setHttpCode($http_code);
            throw $ex;
        } else {

            if($http_code != 200) {
                $ex = new \PDFsquid\Exceptions\PDFsquidException('API error');
                $ex->setHttpCode($http_code);
                try {
                    if ($body) {
                        $errors = json_decode($body, true);
                        $ex->setError(@$errors['error'] ? $errors['error'] : null, @$errors['error_code'] ? $errors['error_code'] : null);
                    }
                } catch(\Exception $ex) {

                }
                throw $ex;
            }
            // parse response headers to array
            $headers = array();

            $header_text = substr($response, 0, $header_size);

            foreach (explode("\r\n", $header_text) as $i => $line) {
                if (stristr($line, 'HTTP/'))
                    $headers['http_code'] = $line;
                else {
                    if ($line == '')
                        continue;

                    list ($key, $value) = explode(': ', $line);

                    $headers[strtolower($key)] = $value;
                }
            }
        }

        curl_close($ch);

        if ($download) {
            return new ResponseFile($headers, $body);
        } elseif (stristr($headers['content-type'], 'application/json'))
            return json_decode($body);
        else
            return $body;
    }

    private function checkParams(&$params)
    {
        if (!is_array($params))
            throw new \PDFsquid\Exceptions\PDFsquidException('Params is not an array');
    }
}
