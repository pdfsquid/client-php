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
     * @param $error
     * @param $error_code
     */
    public function setError($errors, $error_code)
    {
      $this->errors['error'] = $error;
      $this->errors['error_code'] = $error_code;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
