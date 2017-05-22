<?php

namespace PDFsquid\Exceptions;

use Exception;

/**
 * Class PDFsquidException
 * @author Paweł Lange <plange@pdfsquid.com>
 * @package PDFsquid\Exceptions
 */
class PDFsquidException extends Exception
{
    private $http_code = null;
    private $errors = null;

    public function __construct($message = '', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $http_code
     */
    public function setHttpCode($http_code)
    {
        $this->http_code = $http_code;
    }

    /**
     * @return mixed
     */
    public function getHttpCode()
    {
        return $this->http_code;
    }

    /**
     * @param $errors
     */
    public function setError($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}