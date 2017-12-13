<?php

namespace app\components;

use yii\base\Event;
use yii\base\InvalidCallException;
use yii\base\Model;


/**
 *
 * Class ModelDecorator
 * @package app\components
 * @property Model $entity
 * @method save($validation = true) @see yii\db\ActiveRecord
 */
class ModelDecorator extends Model
{
    public $entity;

    public static function decorate($entity)
    {
        return new static(['entity' => $entity]);
    }

    /**
     * @param Model[] $models
     *
     * @return static[]
     */
    public static function decorateMultiple(array $models): array
    {
        return array_map(
            function (Model $module) {
                return static::decorate($module);
            },
            $models
        );
    }

    /**
     * @inheritDoc
     */
    public function extraFields()
    {
        return $this->entity->extraFields();
    }

    /**
     * @inheritDoc
     */
    public function toArray(array $fields = [], array $expand = [], $recursive = true)
    {
        return $this->entity->toArray($fields, $expand, $recursive);
    }

    /**
     * @inheritDoc
     */
    public function hasProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return $this->entity->hasProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * @inheritDoc
     */
    public function canGetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return $this->entity->canGetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * @inheritDoc
     */
    public function canSetProperty($name, $checkVars = true, $checkBehaviors = true)
    {
        return $this->entity->canSetProperty($name, $checkVars, $checkBehaviors);
    }

    /**
     * @inheritDoc
     */
    public function hasMethod($name, $checkBehaviors = true)
    {
        return $this->entity->hasMethod($name, $checkBehaviors);
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return $this->entity->behaviors();
    }

    /**
     * @inheritDoc
     */
    public function hasEventHandlers($name)
    {
        return $this->entity->hasEventHandlers($name);
    }

    /**
     * @inheritDoc
     */
    public function on($name, $handler, $data = null, $append = true)
    {
        return $this->entity->on($name, $handler, $data, $append);
    }

    /**
     * @inheritDoc
     */
    public function off($name, $handler = null)
    {
        return $this->entity->off($name, $handler);
    }

    /**
     * @inheritDoc
     */
    public function trigger($name, Event $event = null)
    {
        return $this->entity->trigger($name, $event);
    }

    /**
     * @inheritDoc
     */
    public function getBehavior($name)
    {
        return $this->entity->getBehavior($name);
    }

    /**
     * @inheritDoc
     */
    public function getBehaviors()
    {
        return $this->entity->getBehaviors();
    }

    /**
     * @inheritDoc
     */
    public function attachBehavior($name, $behavior)
    {
        return $this->entity->attachBehavior($name, $behavior);
    }

    /**
     * @inheritDoc
     */
    public function attachBehaviors($behaviors)
    {
        return $this->entity->attachBehaviors($behaviors);
    }

    /**
     * @inheritDoc
     */
    public function detachBehavior($name)
    {
        return $this->entity->detachBehavior($name);
    }

    /**
     * @inheritDoc
     */
    public function detachBehaviors()
    {
        return $this->entity->detachBehaviors();
    }

    /**
     * @inheritDoc
     */
    public function ensureBehaviors()
    {
        return $this->entity->ensureBehaviors();
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return $this->entity->rules();
    }

    /**
     * @inheritDoc
     */
    public function scenarios()
    {
        return $this->entity->scenarios();
    }

    /**
     * @inheritDoc
     */
    public function formName()
    {
        return $this->entity->formName();
    }

    /**
     * @inheritDoc
     */
    public function attributes()
    {
        return $this->entity->attributes();
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return $this->entity->attributeLabels();
    }

    /**
     * @inheritDoc
     */
    public function attributeHints()
    {
        return $this->entity->attributeHints();
    }

    /**
     * @inheritDoc
     */
    public function validate($attributeNames = null, $clearErrors = true)
    {
        return $this->entity->validate($attributeNames, $clearErrors);
    }

    /**
     * @inheritDoc
     */
    public function beforeValidate()
    {
        return $this->entity->beforeValidate();
    }

    /**
     * @inheritDoc
     */
    public function afterValidate()
    {
        return $this->entity->afterValidate();
    }

    /**
     * @inheritDoc
     */
    public function getValidators()
    {
        return $this->entity->getValidators();
    }

    /**
     * @inheritDoc
     */
    public function getActiveValidators($attribute = null)
    {
        return $this->entity->getActiveValidators($attribute);
    }

    /**
     * @inheritDoc
     */
    public function createValidators()
    {
        return $this->entity->createValidators();
    }

    /**
     * @inheritDoc
     */
    public function isAttributeRequired($attribute)
    {
        return $this->entity->isAttributeRequired($attribute);
    }

    /**
     * @inheritDoc
     */
    public function isAttributeSafe($attribute)
    {
        return $this->entity->isAttributeSafe($attribute);
    }

    /**
     * @inheritDoc
     */
    public function isAttributeActive($attribute)
    {
        return $this->entity->isAttributeActive($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeLabel($attribute)
    {
        return $this->entity->getAttributeLabel($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getAttributeHint($attribute)
    {
        return $this->entity->getAttributeHint($attribute);
    }

    /**
     * @inheritDoc
     */
    public function hasErrors($attribute = null)
    {
        return $this->entity->hasErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getErrors($attribute = null)
    {
        return $this->entity->getErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function getFirstErrors()
    {
        return $this->entity->getFirstErrors();
    }

    /**
     * @inheritDoc
     */
    public function getFirstError($attribute)
    {
        return $this->entity->getFirstError($attribute);
    }

    /**
     * @inheritDoc
     */
    public function addError($attribute, $error = '')
    {
        $this->entity->addError($attribute, $error);
    }

    /**
     * @inheritDoc
     */
    public function addErrors(array $items)
    {
        $this->entity->addErrors($items);
    }

    /**
     * @inheritDoc
     */
    public function clearErrors($attribute = null)
    {
        $this->entity->clearErrors($attribute);
    }

    /**
     * @inheritDoc
     */
    public function generateAttributeLabel($name)
    {
        return $this->entity->generateAttributeLabel($name);
    }

    /**
     * @inheritDoc
     */
    public function getAttributes($names = null, $except = [])
    {
        return $this->entity->getAttributes($names, $except);
    }

    /**
     * @inheritDoc
     */
    public function setAttributes($values, $safeOnly = true)
    {
        $this->entity->setAttributes($values, $safeOnly);
    }

    /**
     * @inheritDoc
     */
    public function onUnsafeAttribute($name, $value)
    {
        $this->entity->onUnsafeAttribute($name, $value);
    }

    /**
     * @inheritDoc
     */
    public function getScenario()
    {
        return $this->entity->getScenario();
    }

    /**
     * @inheritDoc
     */
    public function setScenario($value)
    {
        $this->entity->setScenario($value);
    }

    /**
     * @inheritDoc
     */
    public function safeAttributes()
    {
        return $this->entity->safeAttributes();
    }

    /**
     * @inheritDoc
     */
    public function activeAttributes()
    {
        return $this->entity->activeAttributes();
    }

    /**
     * @inheritDoc
     */
    public function load($data, $formName = null)
    {
        return $this->entity->load($data, $formName);
    }


    /**
     * @inheritDoc
     */
    public function fields()
    {
        return $this->entity->fields();
    }

    /**
     * @inheritDoc
     */
    public function getIterator()
    {
        return $this->entity->getIterator();
    }

    /**
     * @inheritDoc
     */
    public function offsetExists($offset)
    {
        return $this->entity->offsetExists($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetGet($offset)
    {
        return $this->entity->offsetGet($offset);
    }

    /**
     * @inheritDoc
     */
    public function offsetSet($offset, $item)
    {
        $this->entity->offsetSet($offset, $item);
    }

    /**
     * @inheritDoc
     */
    public function offsetUnset($offset)
    {
        $this->entity->offsetUnset($offset);
    }


    public function __get($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter();
        }

        return $this->entity->$name;
    }

    public function __set($name, $value)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter($value);
        } else {
            $this->entity->$name = $value;
        }
    }

    public function __call($name, $arguments)
    {
        if (method_exists($this, $name)) {
            return $this->$name(...$arguments);
        } else {
            return $this->entity->$name(...$arguments);
        }
    }

    /**
     * Checks if a property is set, i.e. defined and not null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `isset($object->property)`.
     *
     * Note that if the property is not defined, false will be returned.
     *
     * @param string $name the property name or the event name
     *
     * @return bool whether the named property is set (not null).
     * @see http://php.net/manual/en/function.isset.php
     */
    public function __isset($name)
    {
        $getter = 'get' . $name;
        if (method_exists($this, $getter)) {
            return $this->$getter() !== null;
        } else {
            return false;
        }
    }

    /**
     * Sets an object property to null.
     *
     * Do not call this method directly as it is a PHP magic method that
     * will be implicitly called when executing `unset($object->property)`.
     *
     * Note that if the property is not defined, this method will do nothing.
     * If the property is read-only, it will throw an exception.
     *
     * @param string $name the property name
     *
     * @throws InvalidCallException if the property is read only.
     * @see http://php.net/manual/en/function.unset.php
     */
    public function __unset($name)
    {
        $setter = 'set' . $name;
        if (method_exists($this, $setter)) {
            $this->$setter(null);
        } elseif (method_exists($this, 'get' . $name)) {
            throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
        }
    }

    /**
     * @inheritDoc
     */
    protected function resolveFields(array $fields, array $expand)
    {
        return $this->entity->resolveFields($fields, $expand);
    }


}
