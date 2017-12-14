<?php

use app\components\widgets\postButtonWithModalConfirm\PostButtonWithModalConfirm;
use app\models\UserIdentity;
use yii\bootstrap\Alert;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Groups */

$isOperator = Yii::$app->user->can(\app\models\UserIdentity::ROLE_OPERATOR);
$this->title = 'Просмотр группы: ' . $model->name;
if (!$isOperator) {
    $this->params['breadcrumbs'][] = ['label' => 'Группы', 'url' => ['/personal/organization-groups']];
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-view col-md-10 col-md-offset-1">
    <?php
    if (!$model->isActive) {
        Alert::begin([
            'options' => [
                'class' => 'alert-danger',
            ],
        ]);
        echo 'Данная группа находится в архиве.';
        Alert::end();
    }
    $contract1 = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['status' => 1])
        ->andWhere(['group_id' => $model->id])
        ->count();

    $contract2 = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['status' => [0, 3]])
        ->andWhere(['group_id' => $model->id])
        ->count();

    $contract3 = (new \yii\db\Query())
        ->select(['id'])
        ->from('contracts')
        ->where(['status' => [0, 1, 3]])
        ->andWhere(['group_id' => $model->id])
        ->count();

    $years = (new \yii\db\Query())
        ->select(['maxchild'])
        ->from('years')
        ->where(['id' => $model->year_id])
        ->one();
    ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'name',
            [
                'attribute' => 'program.name',
                'format' => 'raw',
                'value' => Html::a($model->program->name,
                    Url::to(['/programs/view', 'id' => $model->program->id]),
                    ['class' => 'blue', 'target' => '_blank']),
            ],

            'fullSchedule:raw',
            'datestart:date',
            'datestop:date',
            [
                'label' => 'Обучающихся',
                'value' => $contract1,
            ],
            [
                'label' => 'Заявок',
                'value' => $contract2,
            ],
            [
                'label' => 'Мест',
                'value' => $years['maxchild'] - $contract3,
            ],
        ],
    ]);

    if ($ContractsProvider->getTotalCount() > 0) {
        echo GridView::widget([
            'dataProvider' => $ContractsProvider,
            'summary' => false,
            'columns' => [
                'certificate.number',
                'certificate.fio_child',
                'date:date',
                'number',
                'start_edu_contract:date',
                ['class'    => 'yii\grid\ActionColumn',
                 'template' => '{newgroup}',
                 'buttons'  =>
                     [
                         'newgroup' => function ($url, $model)
                         {
                             return Html::a('Сменить группу', Url::to(['/contracts/newgroup', 'id' => $model->id]), ['class' => 'btn btn-primary']);
                         },
                     ],
                'visible' => Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION),
                ],
            ],
        ]);
        $del = 0;
    } else {
        echo "<h3>В этой группе нет обучающихся</h3>";
        $del = 1;
    }
    if ($isOperator) {
        echo Html::a('Назад', ['/programs/view', 'id' => $model->program_id], ['class' => 'btn btn-primary']);
    } else {
        echo Html::a('Назад', '/personal/organization-groups', ['class' => 'btn btn-primary']);
    }

    if (Yii::$app->user->can(UserIdentity::ROLE_ORGANIZATION)) {
        echo Html::a('Редактировать',
            ($model->isActive ? ['/groups/update', 'id' => $model->id] : '#'),
            ['class' => 'btn btn-primary ' . ($model->isActive ? '' : 'disabled')]);
        echo '<div class="pull-right">';
        if ($del && $model->isActive) {
            echo PostButtonWithModalConfirm::widget([
                'title' => 'Удалить группу',
                'url' => Url::to(['/groups/delete', 'id' => $model->id]),
                'confirm' => 'Вы действительно хотите удалить эту группу?',
                'toggleButton' => ['class' => 'btn btn-danger', 'label' => 'Удалить']
            ]);

        } else {
            echo \yii\bootstrap\Button::widget([
                'label' => 'Удалить группу нельзя',
                'options' => [
                    'class' => 'btn btn-danger',
                    'disabled' => 'disabled'
                ],
            ]);
        }
        echo '</div>';
    }
    ?>
</div>
