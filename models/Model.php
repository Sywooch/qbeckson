<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class Model extends \yii\base\Model
{
    /**
     * Creates and populates a set of models.
     *
     * @param string $modelClass
     * @param array $multipleModels
     * @return array
     */
    public static function createMultiple($modelClass, $multipleModels = [], $scenario = null)
    {
        $model    = new $modelClass;
        $formName = $model->formName();
        $post     = Yii::$app->request->post($formName);
        $models   = [];

        if (! empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $temporary_model = $multipleModels[$item['id']];
                } else {
                    $temporary_model = new $modelClass;
                }
                if (!empty($scenario)) {
                    $temporary_model->scenario = $scenario;
                }
                $models[] = $temporary_model;
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

    public function getFirstErrorAsString()
    {
        $errors = $this->getFirstErrors();

        if ($this->hasErrors()) {
            return array_shift($errors);
        }

        return null;
    }
}
