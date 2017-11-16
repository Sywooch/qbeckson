<?php
/** @var $model \app\models\ProgrammeModule */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="btn-row">

    <?php
    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && $model->program->verification == \app\models\Programs::VERIFICATION_DONE) {
        $message = '';
        $url = '#';
        $classButton = 'btn-theme';
        $active = false;

        /**@var $organization \app\models\Organization */
        $organization = Yii::$app->user->identity->organization;


        /** Адреса */
        if (count($model->addresses)) {
            echo Html::a(
                'Изменить адреса модуля',
                ['years/add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']
            );
        } else {
            echo Html::a(
                'Указать адреса для модуля',
                ['years/add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']
            );
        }
        /** Группы*/
        echo Html::a('Добавить группу', Url::to(['/groups/newgroup', 'id' => $model->id]), ['class' => 'btn btn-theme']);



        ?>

    <?php } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && ($model->program->verification == \app\models\Programs::VERIFICATION_DENIED
            || $model->program->verification == \app\models\Programs::VERIFICATION_WAIT)) {

        echo \app\components\widgets\ButtonWithInfo::widget([
            'label' => 'Действия',
            'message' => 'Недоступно по причине отказа',
            'options' => ['disabled' => 'disabled',
                'class' => 'btn btn-theme',]
        ]);
    } ?>
</div>
