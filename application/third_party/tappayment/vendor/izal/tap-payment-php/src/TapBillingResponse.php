<?php
namespace IZaL\Tap;

use IZaL\Tap\Exceptions\InvalidResponseException;

class TapBillingResponse extends TapBilling
{
    public $response;

    public function __construct($response)
    {
        if(!is_object($response)) {
            throw new InvalidResponseException('Invalid Response. Response must be an object');
        }
        $this->response = $response;
    }

    public function getRawResponse()
    {
        return json_decode($this->response->getBody());
    }

}