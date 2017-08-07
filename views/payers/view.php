<?php
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Organization;

/* @var $this yii\web\View */
/* @var $model app\models\Payers */

$this->title = $model->name;

$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/operator-payers']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']];
}
if (isset($roles['payer'])) {
    $this->params['breadcrumbs'][] = 'Плательщики';
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payers-view  col-md-offset-2 col-md-8">
    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'address_actual',
            [
                'attribute' => 'mun',
                'format' => 'raw',
                'value' => function ($model) {
                    /** @var \app\models\Payers $model */
                    return $model->municipality->name;
                },
            ],
            'phone',
            'email:email',
            'fio',
            'position',
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'address_legal',
            'OGRN',
            'INN',
            'KPP',
            'OKPO',
        ],
    ]) ?>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute'=> 'directionality_1rob_count',
                'value'=> $model->directionality1rob($model->id),
            ],
            [
                'attribute'=> 'directionality_1_count',
                'value'=> $model->directionality1($model->id),
            ],
            [
                'attribute'=> 'directionality_2_count',
                'value'=> $model->directionality2($model->id),
            ],
            [
                'attribute'=> 'directionality_3_count',
                'value'=> $model->directionality3($model->id),
            ],
            [
                'attribute'=> 'directionality_4_count',
                'value'=> $model->directionality4($model->id),
            ],
            [
                'attribute'=> 'directionality_5_count',
                'value'=> $model->directionality5($model->id),
            ],
            [
                'attribute'=> 'directionality_6_count',
                'value'=> $model->directionality6($model->id),
            ],
        ],
    ]) ?>
    <p>
    <?php if (isset($roles['operators'])) {
        $previus = (new \yii\db\Query())
            ->select(['id'])
            ->from('certificates')
            ->where(['payer_id' => $model->id])
            ->count();
    
        $cooperate = (new \yii\db\Query())
            ->select(['id'])
            ->from('cooperate')
            ->where(['payer_id' => $model->id])
            ->andWhere(['status' => 1])
            ->count();
    
        $display['previus'] = $previus;
        $display['cooperate'] = $cooperate;

        echo DetailView::widget([
            'model' => $display,
            'attributes' => [
                [
                    'label'=> Html::a(
                        'Число выданных сертификатов',
                        Url::to([
                            'personal/operator-certificates',
                            'CertificatesSearch[payer]' => $model->name,
                            'CertificatesSearch[payer_id]' => $model->id
                        ]),
                        ['class' => 'blue', 'target' => '_blank']
                    ),
                    'value'=> $display['previus'],
                ],
                [
                    'label'=> Html::a(
                        'Число заключенных соглашений',
                        Url::to([
                            'cooperate/index',
                            'CooperateSearch[payerName]' => $model->name,
                            'CooperateSearch[payer_id]' => $model->id
                        ]),
                        ['class' => 'blue', 'target' => '_blank']
                    ),
                    'value'=> $display['cooperate'],
                ],
            ],
        ]);

        echo Html::a('Назад', '/personal/operator-payers', ['class' => 'btn btn-primary']);
        echo '&nbsp;';
        echo Html::a('Редактировать', Url::to(['/payers/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);

        if (!$previus) {
            echo '&nbsp;';
            echo Html::a('Удалить', Url::to(['/payers/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить этого плательщика?',
                'method' => 'post']
            ]);
        }
    }
    if (isset($roles['payer'])) {
        echo Html::a('Назад', '/personal/payer-info', ['class' => 'btn btn-primary']);
    }
    if (isset($roles['organizations'])) {
        echo Html::a('Назад', '/personal/organization-payers', ['class' => 'btn btn-primary']);
        
         $organizations = new Organization();
        $organization = $organizations->getOrganization();
        
        $status = (new \yii\db\Query())
                    ->select(['status'])
                    ->from('cooperate')
                    ->where(['payer_id' => $model->id])
                    ->andWhere(['organization_id' => $organization['id']])
                    ->andWhere(['status' => 1])
                    ->column();
        if ($status) {
            $contracts = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['payer_id' => $model->id])
                    ->andWhere(['organization_id' => $organization['id']])
                    ->count();
            if ($contracts == 0) {
                echo '&nbsp';
                echo Html::a('Расторгнуть соглашение', Url::to(['/cooperate/decooperate', 'id' => $model->id]), ['class' => 'btn btn-danger', 'data' => [
                'confirm' => 'Вы действительно хотите расторгнуть соглашение с этим плательщиком?'], 'title' => Yii::t('yii', 'Расторгнуть соглашение')]);
            }
        } else {
            $status2 = (new \yii\db\Query())
                    ->select(['status'])
                    ->from('cooperate')
                    ->where(['payer_id' => $model->id])
                    ->andWhere(['organization_id' => $organization['id']])
                    ->andWhere(['status' => 0])
                    ->column();
            
            if ($status2) {
                echo '&nbsp';
                echo Html::a('Удалить соглашение', Url::to(['cooperate/delete', 'id' => $model->id]), ['class' => 'btn btn-danger', 'data' => [
                'confirm' => 'Вы действительно хотите удалить соглашение с этим плательщиком?', 'method' => 'post'], 'title' => Yii::t('yii', 'Расторгнуть соглашение')]);
            }
        }

        if (null !== ($cooperation = $model->getCooperation())) {
            if ($cooperation->status === \app\models\Cooperate::STATUS_REJECTED) {
                echo '&nbsp';
                echo Html::a(
                    'Подать жалобу',
                    Url::to(['cooperate/appeal-request', 'payerId' => $model->id]),
                    ['class' => 'btn btn-danger']
                );
            }
            if ($cooperation->status === \app\models\Cooperate::STATUS_CONFIRMED) {
                echo '&nbsp';
                echo Html::a(
                    'Ввести реквизиты',
                    Url::to(['cooperate/requisites', 'payerId' => $model->id]),
                    ['class' => 'btn btn-primary']
                );
            }
        } else {
            echo '&nbsp';
            echo Html::a(
                'Направить заявку на заключение соглашения с уполномоченной организацией',
                Url::to(['cooperate/request', 'payerId' => $model->id]),
                ['class' => 'btn btn-primary']
            );
        }
    }
    ?>
    </p>
</div>
