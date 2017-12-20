<?php

use app\models\Organization;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */

$date = explode(".", date("d.m.Y"));
switch ($date[1] != 12 ? $date[1] - 1 : $date[1]) {
    case 1:
        $m = 'январе';
        break;
    case 2:
        $m = 'феврале';
        break;
    case 3:
        $m = 'марте';
        break;
    case 4:
        $m = 'апреле';
        break;
    case 5:
        $m = 'мае';
        break;
    case 6:
        $m = 'июне';
        break;
    case 7:
        $m = 'июле';
        break;
    case 8:
        $m = 'августе';
        break;
    case 9:
        $m = 'сентябре';
        break;
    case 10:
        $m = 'октябре';
        break;
    case 11:
        $m = 'ноябре';
        break;
    case 12:
        $m = 'декабре';
        break;
}

$this->title = 'Полнота оказаных услуг в ' . $m;

$this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
$this->params['breadcrumbs'][] = $this->title;
?>


<?php
$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
$organization = Yii::$app->user->identity->organization;
/**@var $organization Organization */
?>
<div class="col-md-10 col-md-offset-1">
    <h1><?= Html::encode($this->title) ?></h1>
    <?php if (!$GroupsProvider->getTotalCount()): ?>
        <p>Создать счет невозможно. В месяце <?= $m ?> не было действующих договоров, по которым предусматривалась
            оплата за счет сертификата.</p>
    <?php else: ?>
        <?= GridView::widget([
            'dataProvider' => $GroupsProvider,
            'filterModel' => $searchGroups,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'organization_id',
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

                        if (date('m') == 12) {
                            $completeness = (new \yii\db\Query())
                                ->select(['completeness', 'id'])
                                ->from('completeness')
                                ->where(['group_id' => $data->id])
                                ->andWhere(['month' => 12])
                                ->andWhere(['preinvoice' => 0])
                                ->one();
                        } else {
                            $completeness = (new \yii\db\Query())
                                ->select(['completeness', 'id'])
                                ->from('completeness')
                                ->where(['group_id' => $data->id])
                                ->andWhere(['month' => date('m') - 1])
                                ->andWhere(['preinvoice' => 0])
                                ->one();
                        }

                        return Html::a(($completeness['completeness'] ?? 0) . ' %', Url::to(['/completeness/update', 'id' => $completeness['id']]), ['class' => 'btn btn-primary']);
                    }
                ],

            ],
        ]); ?>
    <?php endif; ?>


    <?= Html::a('Назад', ['/personal/organization-invoices'], ['class' => 'btn btn-primary']) ?>
    &nbsp;
    <?php
    if ($GroupsProvider->getTotalCount() > 0) {

        echo Html::a('Продолжить', ['contracts/invoice'], ['class' => 'btn btn-success']);
    }
    ?>
</div>