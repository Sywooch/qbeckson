<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 06.12.17
 * Time: 16:25
 */

namespace app\models\programs;


use app\components\ModelDecorator;
use app\models\Programs;
use yii;
use yii\bootstrap\Alert;

/**
 * @property Programs $entity
 * @mixin Programs
 *  ***
 */
class ProgramViewDecorator extends ModelDecorator
{
    public static function illnesses()
    {
        return Programs::illnesses();
    }

    public function getAlert(): string
    {
        if ($this->verification === Programs::VERIFICATION_DRAFT) {
            return Alert::widget([
                'options' => ['class' => 'alert-info'],
                'body' => 'Черновик'
            ]);
        }

        return '';
    }

    public function getHeadTemplate(): string
    {
        $headTemplate = '_base_head';
        if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)) {
            $headTemplate = '_organisation_head';
        } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR)) {
            $headTemplate = '_operator_head';
        } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_CERTIFICATE)) {
            $headTemplate = '_certificate_head';
        }

        return $headTemplate;
    }

    public function attributeLabels()
    {
        return parent::attributeLabels();
    }
}
