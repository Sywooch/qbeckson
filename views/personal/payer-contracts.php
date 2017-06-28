<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use kartik\dialog\DialogAsset;
use app\models\ContractsPayerInvoiceSearch;
use app\models\Payers;

/* @var $this yii\web\View */

$this->title = 'Договоры';
$this->params['breadcrumbs'][] = $this->title;
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие <span
                    class="badge"><?= $Contracts1Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel2">Подтвержденные <span
                    class="badge"><?= $Contracts3Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel3">Ожидающие подтверждения <span
                    class="badge"><?= $Contracts0Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel4">Расторгнутые <span
                    class="badge"><?= $Contracts5Provider->getTotalCount() ?></span></a></li>
</ul>
<br>


<p>
    <?php
    $payers = new Payers();
    $payer = $payers->getPayer();

    $searchContracts = new ContractsPayerInvoiceSearch();
    $searchContracts->payer_id = $payer->id;
    $InvoiceProvider = $searchContracts->search(Yii::$app->request->queryParams);

    echo ExportMenu::widget([
        'dataProvider' => $InvoiceProvider,
        'target' => '_self',
        'showColumnSelector' => false,
        'filename' => date('d-m-Y'),
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Сформировать реестр договоров для субсидии',
            'icon' => false,
        ],
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            'number',
            'date:date',
            [
                'attribute' => 'certificatenumber',
                'label' => 'Сертификат',
                'format' => 'raw',
            ],
            [
                'attribute' => 'organizationname',
                'label' => 'Организация',
                'format' => 'raw',
            ],
            [
                'label' => 'К оплате',
                'value' => function ($model) {

                    $start_edu_contract = explode("-", $model->start_edu_contract);
                    $month = $start_edu_contract[1];

                    if ($month == date('m')) {
                        $price = $model->first_m_price * $model->payer_dol;
                    } else {
                        $price = $model->other_m_price * $model->payer_dol;
                    }

                    return $price;
                }
            ],
        ],
    ]);
    ?>
<p>

<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Contracts1Provider,
            'filterModel' => $searchContracts1,
            'pjax' => true,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate == 1) {
                    return ['class' => 'danger'];
                }
            },
            'columns' => [
                [
                    'attribute' => 'number',
                    'label' => 'Номер',
                ],
                [
                    'attribute' => 'date',
                    'format' => 'date',
                    'label' => 'Дата',
                ],
                [
                    'attribute' => 'certificatenumber',
                    'label' => 'Сертификат',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'programname',
                    'label' => 'Программа',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'organizationname',
                    'label' => 'Организация',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'rezerv',
                    'label' => 'Резерв',
                    'value' => function ($data) {
                        return abs(round($data->rezerv));
                    },
                ],
                [
                    'attribute' => 'paid',
                    'label' => 'Списано',
                    'value' => function ($data) {
                        return round($data->paid);
                    },
                ],

                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Действует до',
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>

    <div id="panel2" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts3Provider,
            'filterModel' => $search3Contracts,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate == 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'certificatenumber',
                    'label' => 'Сертификат',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'programname',
                    'label' => 'Программа',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'organizationname',
                    'label' => 'Организация',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'start_edu_contract',
                    'format' => 'date',
                    'label' => 'Начало обучения',
                ],
                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Конец обучения',
                ],


                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{dobr}',
                    'buttons' =>
                        [
                            'dobr' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/contracts/verificate', 'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'Ok')
                                ]);
                            },
                        ]
                ],
            ],
        ]); ?>
    </div>

    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'pjax' => true,
            'columns' => [
                [
                    'attribute' => 'certificatenumber',
                    'label' => 'Сертификат',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'programname',
                    'label' => 'Программа',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'organizationname',
                    'label' => 'Организация',
                    'format' => 'raw',
                ],
                [
                    'attribute' => 'start_edu_contract',
                    'format' => 'date',
                    'label' => 'Начало обучения',
                ],
                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Конец обучения',
                ],

                [
                    'attribute' => 'status',
                    'value' => function ($data) {
                        if ($data->status == 0) {
                            return 'Заявка ожидает подтверждения';
                        }
                        if ($data->status == 3) {
                            return 'Договор ожидает подписания';
                        }
                    },
                ],

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                    'template' => '{view}',
                    'buttons' =>
                        [
                            'permit' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/contracts/ok', 'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'Подтвердить создание договора')
                                ]);
                            },
                        ]
                ],
            ],
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts5Provider,
            'filterModel' => $searchContracts5,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate == 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => [
                [
                    'attribute' => 'number',
                    'label' => 'Номер',
                ],
                [
                    'attribute' => 'date',
                    'format' => 'date',
                    'label' => 'Дата',
                ],
                [
                    'attribute' => 'certificate',
                    'label' => 'Сертификат',
                    'format' => 'raw',
                    'value' => function ($data) {

                        $certificate = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('certificates')
                            ->where(['number' => $data->certificate->number])
                            ->one();


                        return Html::a($data->certificate->number, Url::to(['/certificates/view', 'id' => $certificate['id']]), ['class' => 'blue', 'target' => '_blank']);
                    }
                ],
                [
                    'attribute' => 'program',
                    'label' => 'Программа',
                    'format' => 'raw',
                    'value' => function ($data) {

                        $program = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('programs')
                            ->where(['name' => $data->program->name])
                            ->one();


                        return Html::a($data->program->name, Url::to(['/programs/view', 'id' => $program['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                ],
                [
                    'attribute' => 'payers',
                    'label' => 'Плательщик',
                    'format' => 'raw',
                    'value' => function ($data) {

                        $payer = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('payers')
                            ->where(['name' => $data->payers->name])
                            ->one();


                        return Html::a($data->payers->name, Url::to(['/payers/view', 'id' => $payer['id']]), ['class' => 'blue', 'target' => '_blank']);
                    },
                    'label' => 'Плательщик',
                ],
                [
                    'attribute' => 'year.year',
                    'value' => function ($data) {
                        return $data->year->fullname;
                    }
                ],
                'date_termnate:date',
                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
    <?php
    echo ExportMenu::widget([
        'dataProvider' => $ContractsallProvider,
        'target' => '_self',
        //'showConfirmAlert' => false,
        //'enableFormatter' => false,
        'showColumnSelector' => false,
        //'contentBefore' => [
        //    'value' => 123,
        //],
        'filename' => 'contracts',
        'dropdownOptions' => [
            'class' => 'btn btn-success',
            'label' => 'Договоры',
            'icon' => false,
        ],
        //'asDropdown' => false,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_PDF => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
            ExportMenu::FORMAT_EXCEL => false,
        ],
        'columns' => [
            'id',
            'number',
            'date',
            'certificate_id',
            'payer_id',
            'program_id',
            'year_id',
            'organization_id',
            'group_id',
            'status',
            'status_termination',
            'status_comment',
            'status_year',
            'link_doc',
            'link_ofer',
            'all_funds',
            'funds_cert',
            'all_parents_funds',
            'start_edu_programm',
            'funds_gone',
            'start_edu_contract',
            'month_start_edu_contract',
            'stop_edu_contract',
            'certnumber',
            'certfio',
            'sposob',
            'prodolj_d',
            'prodolj_m',
            'prodolj_m_user',
            'first_m_price',
            'other_m_price',
            'first_m_nprice',
            'other_m_nprice',
            'change1',
            'change2',
            'change_org_fio',
            'org_position',
            'org_position_min',
            'change_doctype',
            'change_fioparent',
            'change6',
            'change_fiochild',
            'change8',
            'change9',
            'change10',
            'ocen_fact',
            'ocen_kadr',
            'ocen_mat',
            'ocen_obch',
            'ocenka',
            'wait_termnate',
            'date_termnate',
            'cert_dol',
            'payer_dol',
            'rezerv',
            'paid',
            'terminator_user',
            'fontsize',
            'certificatenumber',
        ],

    ]); ?>
</div>
