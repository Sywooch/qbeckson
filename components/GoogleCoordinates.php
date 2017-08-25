<?php

namespace app\components;

/**
 * Class GoogleCoordinates
 * @package app\components
 */
class GoogleCoordinates
{
    const DEFAULT_URL = 'https://maps.google.com/maps/api/geocode/json?address={{ADDRESS}}';

    private $address;
    private $response;
    public $sessionValues = [];
    private $key = 0;

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
    public function getLat(): string
    {
        return $this->getResponse()->results[0]->geometry->location->lat ?? '';
    }

    /**
     * @return string
     */
    public function getLng(): string
    {
        return $this->getResponse()->results[0]->geometry->location->lng ?? '';
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
        if (null === $this->address) {
            throw new \DomainException('Address must be set');
        }

        foreach ($this->sessionValues as $record) {
            if ($record['key'] === $this->address) {
                return $record['value'];
            }
        }

        $ch = curl_init();
        $options = [
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

        $this->sessionValues[$this->key]['key'] = $this->address;
        $this->sessionValues[$this->key]['value'] = $this->response;
        $this->key++;

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
