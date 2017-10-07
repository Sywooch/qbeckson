<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 07.10.2017
 * Time: 10:58
 */

namespace app\components\validators;

use Yii;
use yii\base\Model;
use yii\validators\Validator;

/**
 * Class RequiredXOR
 * @package app\components\validators
 */
class RequiredXOR extends Validator
{
    /**
     * @var bool whether to skip this validator if the value being validated is empty.
     */
    public $skipOnEmpty = false;
    /**
     * @var mixed the desired value that the attribute must have.
     * If this is null, the validator will validate that the specified attribute is not empty.
     * If this is set as a value that is not null, the validator will validate that
     * the attribute has a value that is the same as this property value.
     * Defaults to null.
     * @see strict
     */
    public $requiredValue;
    /**
     * @var bool whether the comparison between the attribute value and [[requiredValue]] is strict.
     * When this is true, both the values and types must match.
     * Defaults to false, meaning only the values need to match.
     * Note that when [[requiredValue]] is null, if this property is true, the validator will check
     * if the attribute value is null; If this property is false, the validator will call [[isEmpty]]
     * to check if the attribute value is empty.
     */
    public $strict = false;
    /**
     * @var string the user-defined error message. It may contain the following placeholders which
     * will be replaced accordingly by the validator:
     *
     * - `{attribute}`: the label of the attribute being validated
     * - `{value}`: the value of the attribute being validated
     * - `{requiredValue}`: the value of [[requiredValue]]
     */
    public $message;

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        if ($this->message === null) {
            $this->message = $this->requiredValue === null ? Yii::t('yii', 'Only {attribute} cannot be blank.')
                : Yii::t('yii', 'Only {attribute} must be "{requiredValue}".');
        }
    }

    /**
     * @param Model $model
     * @param null $attributes
     */
    public function validateAttributes($model, $attributes = null)
    {
        if (is_array($attributes)) {
            $newAttributes = [];
            foreach ($attributes as $attribute) {
                if (in_array($attribute, $this->getAttributeNames(), true)) {
                    $newAttributes[] = $attribute;
                }
            }
            $attributes = $newAttributes;
        } else {
            $attributes = $this->getAttributeNames();
        }

        $validAttributes = array_reduce($attributes, function ($acc, $val) use ($model)
        {
            return call_user_func([$this, 'validateAttributePrivate'], $acc, $val, $model);
        }, []);

        if (count($validAttributes) < 1) {
            $this->setErrs($model, $attributes);
        } elseif (count($validAttributes) > 1) {
            $this->setErrs($model, $validAttributes);
        }

    }

    private function setErrs(Model $model, $attributes)
    {
        array_map(function ($attribute) use ($model)
        {
            $this->addError($model, $attribute, $this->message, !is_null($this->requiredValue) ? ['attribute' => $attribute, 'requiredValue' => $this->requiredValue] : [$attribute]);
        }, $attributes);
    }


    private function validateAttributePrivate($acc, $attribute, Model $model)
    {
        $skip = $this->skipOnError && $model->hasErrors($attribute)
            || $this->skipOnEmpty && $this->isEmpty($model->$attribute);
        if (!$skip) {
            if ($this->when === null || call_user_func($this->when, $model, $attribute)) {
                if ($this->validateValue($model->$attribute)) {
                    $acc[] = $attribute;
                }
            }
        }

        return $acc;
    }

    /**
     * @inheritdoc
     */
    protected function validateValue($value)
    {
        if ($this->requiredValue === null) {
            if ($this->strict
                && $value !== null
                || !$this->strict
                && !$this->isEmpty(is_string($value) ? trim($value) : $value)) {
                return true;
            }
        } elseif (!$this->strict && $value == $this->requiredValue
            || $this->strict && $value === $this->requiredValue) {
            return true;
        }

        return false;
    }
}