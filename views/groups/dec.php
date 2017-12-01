<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;

/* @var $this yii\web\View */

$this->title = 'Полнота оказаных услуг в декабре';

$this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
/**@var $organization Organization */
$organization = Yii::$app->user->identity->organization;
?>
<div class="col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= GridView::widget([
        'dataProvider' => $GroupsProvider,
        'filterModel' => $searchGroups,
        'columns' => [
            'program.name',
            [
                'attribute' => 'name',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a($data->name, Url::to(['/groups/contracts', 'id' => $data->id]), ['class' => 'blue', 'target' => '_blank']);
                },
            ],
            [
                'format' => 'raw',
                'value' => function ($data) {
                    $completeness = (new \yii\db\Query())
                        ->select(['completeness', 'id'])
                        ->from('completeness')
                        ->where(['group_id' => $data->id])
                        ->andWhere(['month' => Yii::$app->params['decemberNumber']])
                        ->andWhere(['preinvoice' => 0])
                        ->one();

                    return Html::a(($completeness['completeness'] ?? 0) . ' %', Url::to(['/completeness/update', 'id' => $completeness['id']]), ['class' => 'btn btn-primary']);
                }
            ],

        ],
    ]); ?>


    <?= Html::a('Назад', ['/personal/organization-invoices'], ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?php
    if ($GroupsProvider->getTotalCount() > 0) {
        echo Html::a('Продолжить', ['contracts/dec'], ['class' => 'btn btn-success']);
    }
    ?>
</div>