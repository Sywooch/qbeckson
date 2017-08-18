<?php

namespace app\components;

/**
 * Class GoogleCoordinates
 * @package app\components
 */
class GoogleCoordinates
{
    const DEFAULT_URL = 'http://maps.google.com/maps/api/geocode/json?address={{ADDRESS}}&sensor=false';

    private $address;
    private $response;

    /**
     * GoogleCoordinates constructor.
     * @param string $address
     */
    public function __construct($address = null)
    {
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getLat()
    {
        return isset($this->getResponse()->results[0]->geometry->location->lat) ?
            $this->getResponse()->results[0]->geometry->location->lat : '';
    }

    /**
     * @return string
     */
    public function getLng()
    {
        return isset($this->getResponse()->results[0]->geometry->location->lng) ?
            $this->getResponse()->results[0]->geometry->location->lng : '';
    }

    /**
     * @return string
     */
    private function getPreparedUrl()
    {
        $address = urlencode($this->address);

        return str_replace('{{ADDRESS}}', $address, self::DEFAULT_URL);
    }

    /**
     * @return bool|mixed
     */
    private function getResponse()
    {
        $ch = curl_init();
        $options = [
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_URL => $this->getPreparedUrl(),
            CURLOPT_HEADER => false,
        ];
        curl_setopt_array($ch, $options);
        $response = curl_exec($ch);
        curl_close($ch);
        if (!$response) {
            return false;
        }
        $response = json_decode($response);
        $this->response = $response;
        if ($response->status !== 'OK') {
            $this->response = false;
        }

        return $this->response;
    }

    /**
     * @param null|string $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }
}
