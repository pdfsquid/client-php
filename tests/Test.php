<?php

use PDFsquid\ZoneApi;

class MerchantApiTest extends PHPUnit_Framework_TestCase
{

    public function testApiAccess()
    {
        $client = new ZoneApi(null, null , null);
        $this->assertTrue($client->isApiAccessible(), 'API is not accessible');
    }
}