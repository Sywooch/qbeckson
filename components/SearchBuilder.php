<?php

namespace app\components;


use yii\base\Model;
use yii\data\BaseDataProvider;

/**
 * Class SearchBuilder
 * @package app\components
 */
class SearchBuilder
{
    public $queryParams;

    public $resultSet = [];

    /**
     * SearchBuilder constructor.
     *
     * @param $queryParams
     */
    public function __construct($queryParams)
    {
        $this->queryParams = $queryParams;
    }

    /**
     * @param $queryParams
     *
     * @return static
     */
    public static function create($queryParams)
    {
        return new static($queryParams);
    }

    /**
     * @param string $key
     * @param BaseDataProvider $dataProvider
     *
     * @return static
     */
    public function addDataProvider(string $key, BaseDataProvider $dataProvider): self
    {
        $this->resultSet[$key] = $dataProvider;

        return $this;
    }

    /**
     * @param string $key
     * @param Model $model
     *
     * @return static
     */
    public function addSearchModel(string $key, Model $model): self
    {
        $this->resultSet[$key] = $model;

        return $this;
    }

    /**
     * @return array
     */
    public function getResult(): array
    {
        return $this->resultSet;
    }
}
