<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Payers;
use yii\helpers\ArrayHelper;
use app\models\Mun;
use app\models\Cooperate;

/* @var $this yii\web\View */
/* @var $model app\models\Organization */

$this->title = $model->name;


$roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
if (isset($roles['operators'])) {
    $this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['/personal/operator-organizations']];
}
if (isset($roles['organizations'])) {
    $this->params['breadcrumbs'][] = 'Организации';
}
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="organization-view col-md-8 col-md-offset-2">

    <?php
    if ($model->raiting) {
        echo '<h1 class="pull-right">' . $model->raiting . '%</h1>';
    } else {
        echo '<h4 class="pull-right">Рейтинга нет</h4>';
    }
    ?>

    <h3><?= Html::encode($this->title) ?></h3>

    <?php
    if (isset($roles['payer'])) {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $cooperates = (new \yii\db\Query())
            ->select(['id'])
            ->from('cooperate')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => [0, 1]])
            ->andWhere(['payer_id' => $payer])
            ->one();

        if ($cooperates['id']) {

            $cooperate = Cooperate::findOne($cooperates['id']);
            $cooperatedate = explode('-', $cooperate->date);

            echo DetailView::widget([
                'model' => $cooperate,
                'attributes' => [
                    [
                        'label' => 'Соглашение',
                        'value' => 'от ' . $cooperatedate[2] . '.' . $cooperatedate[1] . '.' . $cooperatedate[0] . ' № ' . $cooperate->number,
                    ],
                ],
            ]);
        }
    }

    $license_date = explode('-', $model->license_date);
    ?>

    <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'full_name',
                [
                    'attribute'=>'type',
                    'value' => $model::types()[$model->type],
                ],
                'address_actual',
                [
                    'attribute' => 'mun',
                    'label' => 'Основной район (округ)',
                    'value' => function ($model) {
                        /** @var \app\models\Organization $model */
                        return Html::a(
                            $model->municipality->name,
                            ['mun/view', 'id' => $model->municipality->id],
                            ['target' => '_blank', 'data-pjax' => '0']
                        );
                    },
                    'format' => 'raw',
                ],
                'phone',
                [
                    'attribute' => 'email',
                    'format' => 'email',
                ],
                [
                    'attribute' => 'site',
                    'format' => 'url',
                ],
                'fio_contact',
                [
                  'label' => 'Лицензия',
                    'value' => 'Лицензия от '.$license_date[2].'.'.$license_date[1].'.'.$license_date[0].' №'.$model->license_number.' выдана '.$model->license_issued.'.',
                ],
                [
                    'attribute'=>'actual',
                    'value'=>$model->actual == 1 ? 'Осуществляет деятельность в рамках системы' : 'Деятельность в рамках системы приостановлена',
            ],
        ],
    ])
    ?>
    
    <?php
    if (isset($roles['operators'])) {
        
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'inn',
            'KPP',
            'OGRN',
            'okopo',
            'address_legal',
            'last_year_contract',
            'cratedate:date',
        ],
    ]);
            
        
    echo DetailView::widget([
        'model' => $model,
        'attributes' => [
            'bank_name',
            'bank_bik',
            'bank_sity',
            'korr_invoice',
            'rass_invoice',
        ],
    ]);
    }
    ?>

    <?php
    $cooperate = (new \yii\db\Query())
        ->select(['id'])
        ->from('cooperate')
        ->where(['organization_id' => $model->id])
        ->andWhere(['status' => 1])
        ->count();

    $model['cooperate'] = $cooperate;

    if (isset($roles['payer'])) {
        $payers = new Payers();
        $payer = $payers->getPayer();

        $amount = (new \yii\db\Query())
            ->select(['certificate_id'])
            ->from('contracts')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => 1])
            ->andWhere(['payer_id' => $payer])
            ->column();

        $amount = array_unique($amount);
        $amount = count($amount);

        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => 1])
            ->andWhere(['payer_id' => $payer])
            ->column();

        $contracts = array_unique($contracts);
        $contracts = count($contracts);
    } else {

        $amount = (new \yii\db\Query())
            ->select(['certificate_id'])
            ->from('contracts')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => 1])
            ->column();

        $amount = array_unique($amount);
        $amount = count($amount);

        $contracts = (new \yii\db\Query())
            ->select(['id'])
            ->from('contracts')
            ->where(['organization_id' => $model->id])
            ->andWhere(['status' => 1])
            ->column();

        $contracts = array_unique($contracts);
        $contracts = count($contracts);
    }
    ?>

    <?php
    if (isset($roles['certificate'])) {
        echo DetailView::widget([
        'model' => $model,
        'attributes' => [   
            'max_child',
            [
                'label' => 'Число обучающихся',
                'value' => $amount,
            ],
            [
                'label' => Html::a(
                    'Сертифицированных программ',
                    Url::to(['/programs/search', 'org' => $model->id]),
                    ['class' => 'blue', 'target' => '_blank']
                ),
                'value'=>$model->getCertprogram(),
            ],
        ],
    ]);
        }
        if (isset($roles['payer'])) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'max_child',
                    [
                        'label' => 'Число обучающихся',
                        'value' => $amount,
                    ],
                    [
                        'value' => $contracts,
                        'label'=> Html::a(
                            'Число договоров',
                            Url::to([
                                'personal/payer-contracts',
                                'SearchActiveContracts[organizationName]' => $model->name,
                                'SearchActiveContracts[organization_id]' => $model->id,
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                    [
                        'value' => $model->getCertprogram(),
                        'label' => Html::a(
                            'Сертифицированных программ',
                            Url::to([
                                'personal/payer-programs',
                                'SearchPrograms[organization]' => $model->name,
                                'SearchPrograms[organization_id]' => $model->id
                            ]),
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                ],
            ]);
        }
    
        if (isset($roles['operators'])) {
            echo DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'max_child',
                    [
                        'label' => 'Число обучающихся',
                        'value' => $amount,
                    ],
                    [
                        'value' => $contracts,
                        'label'=> Html::a(
                            'Число договоров',
                            [
                                'personal/operator-contracts',
                                'SearchActiveContracts[organizationName]' => $model->name,
                                'SearchActiveContracts[organization_id]' => $model->id
                            ],
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                    [
                        'value' => $model->getCertprogram(),
                        'label' => Html::a(
                            'Сертифицированных программ',
                            [
                                'personal/operator-programs',
                                'SearchOpenPrograms[organization]' => $model->name,
                                'SearchOpenPrograms[organization_id]' => $model->id,
                            ],
                            ['class' => 'blue', 'target' => '_blank']
                        ),
                    ],
                ],
            ]);
        }
        ?>
    
    <?php
    if (isset($roles['payer'])) {

        $payers = new Payers();
        $payer = $payers->getPayer();

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                [
                    'label' => Html::a(
                        'Выставлено счетов и авансов',
                        [
                            '/personal/payer-invoices',
                            'InvoicesSearch[organization]' => $model->name,
                            'InvoicesSearch[organization_id]' => $model->id
                        ],
                        ['class' => 'blue', 'target' => '_blank']
                    ),
                    'value' => $model->invoiceCount($model->id, $payer->id),
                ],
            ],
        ]);
    }
    ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'about:ntext',
        ],
    ]) ?>
    <p>
        <?php
        if (isset($roles['operators'])) {
            if ($model->actual === 0) {
                echo '<div class="pull-right">';
                echo Html::a('Разрешить деятельность', Url::to(['/organization/actual', 'id' => $model->id]), ['class' => 'btn btn-success']);
                echo '</div>';
            } else {
                $previus = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('contracts')
                    ->where(['organization_id' => $model->id])
                    ->andWhere(['status' => 1])
                    ->count();
                if (!$previus) {
                    echo '<div class="pull-right">';
                    echo Html::a('Приостановить', Url::to(['/organization/noactual', 'id' => $model->id]), ['class' => 'btn btn-danger  text-right']);
                    echo '</div>';
                }
            }

            echo Html::a('Пересчитать лимит', Url::to(['/organization/newlimit', 'id' => $model->id]), ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Пересчитать рейтинг', Url::to(['/organization/raiting', 'id' => $model->id]), ['class' => 'btn btn-primary']);

            echo '<br><br>';
            echo Html::a('Назад', '/personal/operator-organizations', ['class' => 'btn btn-primary']);
            echo '&nbsp;';
            echo Html::a('Редактировать', Url::to(['/organization/update', 'id' => $model->id]), ['class' => 'btn btn-primary']);

            $previus = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['organization_id' => $model->id])
                ->count();
            if (!$previus) {
                echo '&nbsp;';
                echo Html::a('Удалить', Url::to(['/organization/delete', 'id' => $model->id]), ['class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Are you sure you want to delete this item?',
                        'method' => 'post']]);
            }
        }
        if (isset($roles['organizations'])) {
            echo Html::a('Назад', '/personal/organization-info', ['class' => 'btn btn-primary']);
        }

        if (isset($roles['payer'])) {








            echo Html::a('Назад', ['personal/payer-organizations'], ['class' => 'btn btn-primary']);
            if (null !== ($cooperation = $model->getCooperation())) {
                if (null !== $cooperation->getDocumentUrl()) {
                    echo '<hr><div class="panel panel-default">
                        <div class="panel-body">' .
                        Html::a('Текст договора/соглашения', $cooperation->getDocumentUrl())
                        . ' </div>
                    </div>';
                }
                if ($cooperation->status === Cooperate::STATUS_NEW) {
                    echo ' ';
                    echo $this->render(
                        '../cooperate/confirm-request',
                        ['cooperation' => $cooperation]
                    );
                    echo $this->render(
                        '../cooperate/reject-request',
                        ['model' => $cooperation]
                    );
                }

                if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                    null === $cooperation->number &&
                    null === $cooperation->date
                ) {
                    echo '<hr><p class="lead">Реквизиты договора/соглашения не указаны</p>';
                    echo $this->render(
                        '../cooperate/requisites',
                        [
                            'model' => $cooperation,
                            'label' => 'Сведения о реквизитах соглашения/договора не внесены',
                        ]
                    );
                } else if (($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                    null !== $cooperation->number &&
                    null !== $cooperation->date) ||
                    $cooperation->status === Cooperate::STATUS_ACTIVE
                ) {
                    echo '<hr><p class="lead">Реквизиты договора/соглашения не указаны</p>';
                    echo $this->render(
                        '../cooperate/requisites',
                        [
                            'model' => $cooperation,
                            'label' => 'Реквизиты соглашения: от ' . $cooperation->date . ' №' . $cooperation->number,
                        ]
                    );
                }

                if ($cooperation->status === Cooperate::STATUS_CONFIRMED &&
                    null !== $cooperation->number &&
                    null !== $cooperation->date
                ) {
                    echo ' ';
                    echo Html::a(
                        'Одобрить',
                        ['cooperate/confirm-contract', 'id' => $cooperation->id],
                        ['class' => 'btn btn-primary']
                    );
                }
            }






        }
        ?>
    </p>
</div>
