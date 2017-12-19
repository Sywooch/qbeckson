<?php

namespace app\components;


use yii\data\ActiveDataProvider;

class ActiveDataProviderWithDecorator extends ActiveDataProvider
{
    /**
     * @var ModelDecoratorInterface | ModelDecoratorInterface[] класс декоратора, применяемый ко всем моделям
     */
    public $decoratorClass;

    protected function prepareModels()
    {
        $models = parent::prepareModels();

        if (is_array($this->decoratorClass)) {
            return array_reduce(
                $this->decoratorClass,
                function ($models, ModelDecoratorInterface $decoratorClassName) {
                    return $decoratorClassName::decorateMultiple($models);
                },
                $models
            );
        }

        return $this->decoratorClass::decorateMultiple($models);
    }
}
