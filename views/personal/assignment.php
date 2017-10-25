<?php

/** @var $this View */
/** @var $dataProvider ActiveDataProvider */
/** @var $loginForm LoginForm */
/** @var $assignedMunList [] */

use app\models\LoginForm;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\bootstrap\Modal;
use yii\data\ActiveDataProvider;
use yii\web\View;

$this->title = 'Объединить личный кабинет';

$js = <<<js
$('.assign-mun').on('click', function() {
  $('#modal').modal();
  $('#personal-assign-form').data('yiiActiveForm').settings.validationUrl = '/personal/user-personal-assign?munId=' + $(this).data('mun-id');
  $('#personal-assign-form').attr('action', '/personal/user-personal-assign?munId=' + $(this).data('mun-id'));
})

$('.remove-assign-mun').on('click', function() {
    location.href = '/personal/remove-user-personal-assign?munId=' + $(this).data('mun-id');
})
js;

$this->registerJs($js, $this::POS_READY);

?>
<div class="row">
    <div class="col-md-6">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => [
                'name',
                [
                    'class' => ActionColumn::class,
                    'buttons' => [
                        'assign' => function ($url, $model, $key) use($assignedMunList) {
                            if (in_array($model->id, $assignedMunList)) {
                                return Html::button('удалить связь', ['class' => 'remove-assign-mun', 'data' => ['mun-id' => $model->id]]);
                            }

                            return Html::button('объединить', ['class' => 'assign-mun', 'data' => ['mun-id' => $model->id]]);
                        },
                    ],
                    'template' => '{assign}',
                ],
            ]
        ]) ?>

        <?php Modal::begin([
            'header' => 'Введите пароль для личного кабинета плательщика указанного муниципалитета',
            'id' => 'modal',
        ]) ?>
        <?php $form = ActiveForm::begin(['id' => 'personal-assign-form', 'enableAjaxValidation' => true]) ?>
        <?= $form->field($loginForm, 'password') ?>
        <?= Html::submitButton('объединить', ['class' => 'check-password-for-assign-personal']) ?>
        <?php $form->end() ?>
        <?php Modal::end() ?>
    </div>
</div>
