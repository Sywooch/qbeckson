<?php

use yii\grid\ActionColumn;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\statics\DirectoryProgramDirection */
/* @var $searchModel app\models\search\DirectoryProgramActivitySearch */
/* @var $activityProvider \yii\data\ActiveDataProvider */

$this->title = 'Редактирование направленности: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Направленности программ', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="directory-program-direction-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>
    <hr>
    <p class="lead">Виды деятельности:</p>
    <p>
        <?php echo Html::a(
            'Создать новый вид деятельности',
            ['admin/directory-program-activity/create', 'directionId' => $model->id],
            ['class' => 'btn btn-success']
        ) ?>
    </p>
    <?php Pjax::begin() ?>
    <?php echo GridView::widget([
        'dataProvider' => $activityProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'name',
            [
                'attribute' => 'status',
                'value' => function ($model) {
                    /** @var $model app\models\statics\DirectoryProgramActivity */
                    return $model::statuses()[$model->status];
                },
                'filter' => $searchModel::statuses(),
            ],
            [
                'attribute' => 'user_id',
                'label' => 'Добавил пользоваль',
                'value' => function ($model) {
                    /** @var $model app\models\statics\DirectoryProgramActivity */
                    return $model->user ? $model->user->getUserName() : 'Администратор';
                }
            ],
            [
                'class' => ActionColumn::class,
                'controller' => 'admin/directory-program-activity',
                'template' => '{update} {delete}'
            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>
