<?php

use app\helpers\GridviewHelper;
use app\models\Contracts;
use app\models\ContractsPayerInvoiceSearch;
use app\models\Mun;
use app\models\UserIdentity;
use app\widgets\SearchFilter;
use kartik\export\ExportMenu;
use app\widgets\ExportDocs;
use kartik\grid\GridView;
use yii\grid\ActionColumn;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $activeContractsProvider \yii\data\ActiveDataProvider */
/* @var $confirmedContractsProvider \yii\data\ActiveDataProvider */
/* @var $pendingContractsProvider \yii\data\ActiveDataProvider */
/* @var $dissolvedContractsProvider \yii\data\ActiveDataProvider */
/* @var $ContractsallProvider \yii\data\ActiveDataProvider */
/* @var $searchActiveContracts \app\models\search\ContractsSearch */
/* @var $searchConfirmedContracts \app\models\search\ContractsSearch */
/* @var $searchPendingContracts \app\models\search\ContractsSearch */
/* @var $searchDissolvedContracts \app\models\search\ContractsSearch */

$this->title = 'Договоры';
$this->params['breadcrumbs'][] = $this->title;

$number = [
    'attribute' => 'number',
];
$date = [
    'attribute' => 'date',
    'format' => 'date',
];
$rezerv = [
    'attribute' => 'rezerv',
    'label' => 'Резерв',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$start_edu_contract = [
    'attribute' => 'start_edu_contract',
    'format' => 'date',
    'label' => 'Действует с',
];
$stop_edu_contract = [
    'attribute' => 'stop_edu_contract',
    'format' => 'date',
    'label' => 'Действует до',
];
$group_id = [
    'attribute' => 'group_id',
    'value' => 'group.name',
];
$programMunicipality = [
    'attribute' => 'programMunicipality',
    'value' => 'program.municipality.name',
    'label' => 'Муниципалитет',
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => ArrayHelper::map(Mun::find()->all(), 'id', 'name'),
];
$childFullName = [
    'attribute' => 'childFullName',
    'value' => 'certificate.fio_child',
    'label' => 'ФИО ребёнка'
];
$moduleName = [
    'attribute' => 'moduleName',
    'value' => 'year.fullname',
    'label' => 'Модуль'
];
$certificateNumber = [
    'attribute' => 'certificateNumber',
    'format' => 'raw',
    'label' => 'Сертификат',
    'value' => function ($data) {
        return Html::a(
            $data->certificate->number,
            Url::to(['certificates/view', 'id' => $data->certificate->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    }
];
$programName = [
    'attribute' => 'programName',
    'label' => 'Программа',
    'format' => 'raw',
    'value' => function ($data) {
        return Html::a(
            $data->program->name,
            Url::to(['programs/view', 'id' => $data->program->id]),
            ['class' => 'blue', 'target' => '_blank']
        );
    },
];
$organizationName = [
    'attribute' => 'organizationName',
    'label' => 'Организация',
    'format' => 'raw',
    'value' => function ($data) {
        return Html::a(
            $data->organization->name,
            Url::to(['/organization/view', 'id' => $data->organization->id]),
            ['target' => '_blank', 'data-pjax' => '0']
        );
    },
];
$status = [
    'attribute' => 'status',
    'value' => function ($data) {
        if ($data->status === 0) {
            return 'Заявка ожидает подтверждения';
        }
        if ($data->status === 3) {
            return 'Договор ожидает подписания';
        }
    },
    'type' => SearchFilter::TYPE_DROPDOWN,
    'data' => Contracts::statuses(),
];
$date_termnate = [
    'attribute' => 'date_termnate',
    'format' => 'date',
];
$paid = [
    'attribute' => 'paid',
    'type' => SearchFilter::TYPE_RANGE_SLIDER,
];
$actions = [
    'class' => ActionColumn::class,
    'controller' => 'contracts',
    'template' => '{view}',
    'searchFilter' => false,
];

$activeColumns = [
    $number,
    $date,
    $rezerv,
    $paid,
    $start_edu_contract,
    $stop_edu_contract,
    $group_id,
    $programMunicipality,
    $childFullName,
    $moduleName,
    $certificateNumber,
    $programName,
    $organizationName,
    [
        'attribute' => 'payer_id',
        'type' => SearchFilter::TYPE_HIDDEN,
    ],
    $actions,
];
$confirmedColumns = [
    $certificateNumber,
    $childFullName,
    $moduleName,
    $programMunicipality,
    $programName,
    $start_edu_contract,
    $stop_edu_contract,
    [
        'class' => ActionColumn::class,
        'template' => '{dobr}',
        'buttons' => [
            'dobr' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-check"></span>',
                    Url::to(['contracts/verificate', 'id' => $model->id]),
                    ['title' => 'Ok']
                );
            },
        ],
        'searchFilter' => false,
    ],
];
$pendingColumns = [
    $certificateNumber,
    $programName,
    $organizationName,
    $start_edu_contract,
    $stop_edu_contract,
    $status,
    $programMunicipality,
    $moduleName,
    [
        'class' => ActionColumn::class,
        'controller' => 'contracts',
        'template' => '{view}',
        'buttons' => [
            'permit' => function ($url, $model) {
                return Html::a(
                    '<span class="glyphicon glyphicon-ok"></span>',
                    Url::to(['contracts/ok', 'id' => $model->id]),
                    ['title' => 'Подтвердить создание договора']
                );
            },
        ],
        'searchFilter' => false,
    ],
];
$dissolvedColumns = [
    $number,
    $date,
    $certificateNumber,
    $programName,
    $moduleName,
    $date_termnate,
    $programMunicipality,
    $paid,
    $actions,
];

$preparedActiveColumns = GridviewHelper::prepareColumns('contracts', $activeColumns, 'active');
$preparedConfirmedColumns = GridviewHelper::prepareColumns('contracts', $confirmedColumns, 'confirmed');
$preparedPendingColumns = GridviewHelper::prepareColumns('contracts', $pendingColumns, 'pending');
$preparedDissolvedColumns = GridviewHelper::prepareColumns('contracts', $dissolvedColumns, 'dissolved');

?>
<ul class="nav nav-tabs">
    <li class="active">
        <a data-toggle="tab" href="#panel1">Действующие
            <span class="badge"><?= $activeContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel2">Подтвержденные
            <span class="badge"><?= $confirmedContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel3">Ожидающие подтверждения
            <span class="badge"><?= $pendingContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
    <li>
        <a data-toggle="tab" href="#panel4">Расторгнутые
            <span class="badge"><?= $dissolvedContractsProvider->getTotalCount() ?></span>
        </a>
    </li>
</ul>
<br>
<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <?php if ($searchActiveContracts->payer_id && $searchActiveContracts->programName) : ?>
            <p class="lead">Показаны результаты для программы: <?= $searchActiveContracts->programName; ?></p>
        <?php endif; ?>
        <?php if ($searchActiveContracts->certificate_id) : ?>
            <p class="lead">Показаны результаты для сертификата: <?= $searchActiveContracts->certificateNumber; ?></p>
        <?php endif; ?>
        <?php if ($searchActiveContracts->organization_id) : ?>
            <p class="lead">Показаны результаты для организации: <?= $searchActiveContracts->organizationName; ?></p>
        <?php endif; ?>
        <?= SearchFilter::widget([
            'model' => $searchActiveContracts,
            'action' => ['personal/payer-contracts#panel1'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $activeColumns,
                'active',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'active'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $activeContractsProvider,
            'filterModel' => null,
            'pjax' => true,
            'summary' => false,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'columns' => $preparedActiveColumns,
        ]); ?>
    </div>
    <div id="panel2" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchConfirmedContracts,
            'action' => ['personal/payer-contracts#panel2'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $confirmedColumns,
                'confirmed',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'confirmed'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $confirmedContractsProvider,
            'filterModel' => null,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedConfirmedColumns,
        ]); ?>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchPendingContracts,
            'action' => ['personal/payer-contracts#panel3'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $pendingColumns,
                'pending',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'pending'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $pendingContractsProvider,
            'filterModel'  => null,
            'rowOptions'   => function ($model, $index, $widget, $grid)
            {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                } elseif ($model->wait_termnate < 1 && in_array($model->status, [Contracts::STATUS_ACTIVE, Contracts::STATUS_CREATED, Contracts::STATUS_ACCEPTED]) && $model->all_parents_funds > 0) {
                    return ['class' => 'warning'];
                }
            },
            'pjax'         => true,
            'summary'      => false,
            'columns'      => $preparedPendingColumns,
        ]); ?>
    </div>
    <div id="panel4" class="tab-pane fade">
        <?= SearchFilter::widget([
            'model' => $searchDissolvedContracts,
            'action' => ['personal/payer-contracts#panel4'],
            'data' => GridviewHelper::prepareColumns(
                'contracts',
                $dissolvedColumns,
                'dissolved',
                'searchFilter',
                null
            ),
            'role' => UserIdentity::ROLE_PAYER,
            'type' => 'dissolved'
        ]); ?>
        <?= GridView::widget([
            'dataProvider' => $dissolvedContractsProvider,
            'filterModel' => null,
            'rowOptions' => function ($model, $index, $widget, $grid) {
                if ($model->wait_termnate === 1) {
                    return ['class' => 'danger'];
                }
            },
            'pjax' => true,
            'summary' => false,
            'columns' => $preparedDissolvedColumns,
        ]); ?>
    </div>
    <?php
    echo $this->render('/common/_export', [
        'dataProvider' => $ContractsallProvider,
        'columns' => $activeColumns,
        'type' => 'active',
        'group' => 'all-contracts',
        'table' => 'contracts',
    ]);
    ?>
    <br /><br />
    <p>
        <?php
        $payer = Yii::$app->user->identity->payer;

        if ($doc = \app\models\ContractDocument::findByPayer($payer, date('Y'), date('m'))) {
            echo Html::a('Скачать выписку от ' . Yii::$app->formatter->asDate($doc->created_at), Yii::getAlias('@pfdo/uploads/contracts/') . $doc->file, ['class' => 'btn btn-primary', 'id' => 'excel-download']);
        } else {
            $searchContracts = new ContractsPayerInvoiceSearch(['payer_id' => $payer->id]);
            $invoiceProvider = $searchContracts->search(Yii::$app->request->queryParams);

            $lastMonthDate = strtotime('first day of previous month');
            $docLastMonth = \app\models\ContractDocument::findByPayer($payer, date('Y', $lastMonthDate), date('m', $lastMonthDate));
            $searchLastMonthContracts = new ContractsPayerInvoiceSearch([
                'payer_id' => $payer->id,
                'lastMonth' => true,
                'excludeContracts' => $docLastMonth->contract_list,
            ]);
            $invoiceLastMonthProvider = $searchLastMonthContracts->search(Yii::$app->request->queryParams);

            echo '<div class="alert alert-warning">Внимание! После заказа реестра договоров для формирования заявки на субсидию в текущем месяце до его завершения новый реестр запросить уже не удастся. А это значит, что договоры, которые будут заключены после текущего момента будут включены в заявку уже в следующем месяце. Вы уверены, что сегодня тот самый день?</div>';

            $fileName = Yii::$app->user->id . '_' . date('d-m-Y');

            echo ExportMenu::widget([
                'dataProvider' => $invoiceProvider,
                'target' => ExportMenu::TARGET_SELF,
                'showColumnSelector' => false,
                'filename' => '_PART1_' . $fileName,
                'stream' => false,
                'deleteAfterSave' => false,
                'folder' => '@pfdoroot/uploads/contracts',
                'linkPath' => '@pfdo/uploads/contracts',
                'dropdownOptions' => [
                    'class' => 'btn btn-success',
                    'label' => 'Заказать реестр договоров для субсидии',
                    'icon' => false,
                ],
                'showConfirmAlert' => false,
                'afterSaveView' => false,
                'exportConfig' => [
                    ExportMenu::FORMAT_TEXT => false,
                    ExportMenu::FORMAT_CSV => false,
                    ExportMenu::FORMAT_HTML => false,
                    ExportMenu::FORMAT_PDF => false,
                    ExportMenu::FORMAT_EXCEL_X => false,
                ],
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    [
                        'attribute' => 'certificatenumber',
                        'label' => 'Номер сертификата дополнительного образования',
                        'format' => 'raw',
                    ],
                    [
                        'attribute' => 'number',
                        'label' => 'Реквизиты договора об обучении (твердой оферты)',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return '№' . $model->number . ' от ' . Yii::$app->formatter->asDate($model->date);
                        }
                    ],
                    [
                        'label' => 'Объем обязательств Уполномоченной организации за текущий месяц в соответствии с договорами об обучении (твердыми офертами)',
                        'value' => function ($model) {

                            $start_edu_contract = explode("-", $model->start_edu_contract);
                            $month = $start_edu_contract[1];

                            if ($month == date('m')) {
                                $price = $model->payer_first_month_payment;
                            } else {
                                $price = $model->payer_other_month_payment;
                            }

                            return $price;
                        }
                    ],
                ],
            ]);

            if (Yii::$app->request->isPost) {
                ExportDocs::widget([
                    'dataProvider' => $invoiceLastMonthProvider,
                    'target' => ExportMenu::TARGET_SELF,
                    'initDownloadOnStart' => true,
                    'showColumnSelector' => false,
                    'filename' => '_PART2_' . $fileName,
                    'stream' => false,
                    'deleteAfterSave' => false,
                    'folder' => '@pfdoroot/uploads/contracts',
                    'linkPath' => '@pfdo/uploads/contracts',
                    'dropdownOptions' => [
                        'class' => 'btn btn-success',
                        'label' => 'Заказать реестр договоров для субсидии',
                        'icon' => false,
                    ],
                    'showConfirmAlert' => false,
                    'afterSaveView' => false,
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_CSV => false,
                        ExportMenu::FORMAT_HTML => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_EXCEL_X => false,
                    ],
                    'columns' => [
                        ['class' => 'yii\grid\SerialColumn'],
                        [
                            'attribute' => 'certificatenumber',
                            'label' => 'Номер сертификата дополнительного образования',
                            'format' => 'raw',
                        ],
                        [
                            'attribute' => 'number',
                            'label' => 'Реквизиты договора об обучении (твердой оферты)',
                            'format' => 'raw',
                            'value' => function ($model) {
                                return '№' . $model->number . ' от ' . Yii::$app->formatter->asDate($model->date);
                            }
                        ],
                        [
                            'label' => 'Объем обязательств Уполномоченной организации за текущий месяц в соответствии с договорами об обучении (твердыми офертами)',
                            'value' => function ($model) {

                                $start_edu_contract = explode("-", $model->start_edu_contract);
                                $month = $start_edu_contract[1];

                                if ($month == date('m')) {
                                    $price = $model->payer_first_month_payment;
                                } else {
                                    $price = $model->payer_other_month_payment;
                                }

                                return $price;
                            }
                        ],
                    ],
                ]);
            }

            if (Yii::$app->request->isPost) {
                \app\widgets\MergeExcels::widget([
                    'fileName' => $fileName,
                    'provider' => $invoiceProvider,
                ]);
            }
        }
        ?>
</div>
