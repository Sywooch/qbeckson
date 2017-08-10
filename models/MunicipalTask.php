<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "programs".
 *
 */
class MunicipalTask extends Programs
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return array_merge(parent::rules(), []);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), []);
    }
}
