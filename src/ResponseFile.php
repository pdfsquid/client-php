<?php

namespace PDFsquid;

/**
 * Helper class for handling file downloads
 * Class ResponseFile
 * @package PDFsquid
 */
class ResponseFile
{
    private $headers;

    private $body;

    public function __construct(&$headers, &$body)
    {
        $this->headers = $headers;
        $this->body = $body;
    }

    /**
     * Set headers and output file as attachment
     * @param null $file_name
     */
    public function downloadAsAttachment($file_name = null)
    {
        $this->setHeaders($file_name, true);
        echo $this->body;
    }

    /**
     * Set headers and output file to show in browser
     * @param null $file_name
     */
    public function downloadInline($file_name = null)
    {
        $this->setHeaders($file_name, false);
        echo $this->body;
    }

    /**
     * Get raw file data
     * @return mixed
     */
    public function getRaw()
    {
        return $this->body;
    }

    /**
     * Get file extension in lowercase (pdf, jpg, png)
     * @return mixed
     */
    public function getFileFormat()
    {
        return strtolower($this->headers['output-format']);
    }

    /**
     * Save file on server
     * @param null $file_name
     */
    public function saveFile($dir, $file_name = null)
    {
        file_put_contents($dir . ($file_name ? $file_name : $this->headers['conversion-id']) . '.' . $this->headers['output-format'], $this->body);
    }

    private function setHeaders($file_name, $attachment = true)
    {
        header("pragma: public");
        header("expires: 0");
        header("cache-control: must-revalidate, post-check=0, pre-check=0");
        header("cache-control: private", false);
        header("content-type: " . $this->headers['content-type']);

        if ($attachment)
            header("content-disposition: " . 'attachment; filename=' . ($file_name ? $file_name : $this->headers['conversion-id']) . '.' . $this->headers['output-format']);
        else
            header("content-disposition: " . 'inline; filename=' . ($file_name ? $file_name : $this->headers['conversion-id']) . '.' . $this->headers['output-format']);

        header("content-transfer-encoding: binary");
        header("content-length: " . strlen($this->body));
    }
}