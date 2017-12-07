<?php

namespace app\components;


use app\helpers\ModelHelper;
use yii\db\ActiveRecord;
use yii\web\ForbiddenHttpException;

class EditableOperations
{
    private $params;
    private $attributes;
    private $model;
    private $class;
    private $formName;
    private $changed;

    public function __construct(array $post, string $class)
    {
        $this->params = $post;
        $this->class = $class;
        $this->attributes = [];

        if (!array_key_exists('hasEditable', $this->params)
            || !$this->params['hasEditable']) {
            throw new ForbiddenHttpException();
        }
    }

    public static function getInstance(array $post, string $class): self
    {
        return new self($post, $class);
    }

    public function setAttributes(...$attributes): self
    {
        $this->attributes = array_unique(array_merge($this->attributes, $attributes));

        return $this;
    }

    public function exec()
    {
        if (!$this->getModel()) {
            return false;
        }
        foreach ($this->attributes as $attribute) {
            if ($this->setAttribute($attribute)) {
                $this->changed = $attribute;

                break;
            }
        }
        if (!$this->changed) {
            return false;
        }

        $result = $this->getModel()->save();
        if ($result) {
            return $this->result($this->getModel()->{$this->changed});
        } else {
            return $this->result(null, ModelHelper::getFirstError($this->getModel()));
        }
    }

    /**
     * @return ActiveRecord | null
     */
    private function getModel()
    {
        if ($this->model) {
            return $this->model;
        }
        $class = $this->class;
        /**@var $model ActiveRecord */
        $model = new $class();
        $this->formName = $model->formName();

        if (array_key_exists($this->formName, $this->params)
            && array_key_exists('id', $this->params)) {
            $this->model = $model::findOne(['id' => $this->params['id']]);

            return $this->model;
        } else {
            return null;
        }
    }

    private function setAttribute($attribute): bool
    {

        if (array_key_exists($attribute, $this->params[$this->formName])) {
            $this->getModel()->{$attribute} = $this->params[$this->formName][$attribute];
            $this->changed = $attribute;

            return true;
        }

        return false;
    }

    private function result($value = null, $message = null)
    {
        return ['output' => $value, 'message' => $message];
    }
}
