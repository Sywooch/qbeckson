<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h3 class="center">Личный кабинет плательщика "<?= $payer['name'] ?>"</h3>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Статическая информация</a></li>
    <li><a data-toggle="tab" href="#panel2">Сведения об организации</a></li>
    <li><a data-toggle="tab" href="#panel3">Сертификаты</a></li>
    <li><a data-toggle="tab" href="#panel4">Договоры</a></li>
    <li><a data-toggle="tab" href="#panel5">Счета</a></li>
    <li><a data-toggle="tab" href="#panel6">Обучающие организации</a></li>
    <li><a data-toggle="tab" href="#panel7">Программы</a></li>
</ul>
<br>

<?php /* if ($dataProviderInforms->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $dataProviderInforms,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                    // 'id',
                    // 'contract_id',
                    // 'from',
                    'date',
                    'text:ntext',
                    'program_id',
                    // 'read',

                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{permit} {view}',
                         'buttons' =>
                             [
                                 'permit' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/informs/read', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отметить как прочитанное'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } */ ?>


<?php if ($dataProviderCooperate->getTotalCount() > 0) { ?>
    <div class="modal fade modal-auto-popup">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Новые организации</h4>
          </div>
          <div class="modal-body">
           <p>Эти организации желают с вами сотрудничать</p>
            <?= GridView::widget([
                'dataProvider' => $dataProviderCooperate,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                     'organization_id',

                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{permit} {terminate} &nbsp; {read}',
                         'buttons' =>
                             [
                                 'permit' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/payers/cooperateok', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Одобрить'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },

                                'terminate' => function ($url, $model) {
                                    return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['/payers/cooperateno', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отказать'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },

                                'read' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/payers/cooperateread', 'id' => $model->id]), [
                                         'title' => Yii::t('yii', 'Отметить как прочитанное'),
                                        'data-toggle' => 'tooltip',
                                        'data-placement' => 'top'
                                     ]); },
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } ?>


<div class="tab-content">
    <div id="panel1" class="tab-pane fade in active">
        <p>Количество выданных сертификатов - <?= $count_certificates ?></p>
        <p>Общая сумма выданных сертификатов - <?= $sum_certificates ?></p>
        <p>Количество выданных сертификатов по которым заключены договора на обучение - <?= $count_certificates_contracts ?></p>
        <p>Количество детей обучающихся по одной образовательной программе с использованием выданных сертификатов - <?= $count_certificates_contracts_one ?></p>
        <p>Количество детей обучающихся по двум образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_two ?></p>
        <p>Количество детей обучающихся по трем и более образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_more ?></p>
        <p>Общее количество договоров обучающения заключенных с использованием выданных сертификатов - <?= $sum_contracts ?></p>
    </div>
    <div id="panel2" class="tab-pane fade">
        <p>Наименование организации - <?= $payer['name'] ?></p>
        <p>ИНН - <?= $payer['INN'] ?></p>
        <p>КПП - <?= $payer['KPP'] ?></p>
        <p>ОГРН - <?= $payer['OGRN'] ?></p>
        <p>ОКПО - <?= $payer['OKPO'] ?></p>
        <p>Юридический адрес - <?= $payer['address_legal'] ?></p>
        <p>Фактический адрес - <?= $payer['address_actual'] ?></p>
        <p>Представитель организации - <?= $payer['fio'] ?></p>
        <p>Номер телефона - <?= $payer['phone'] ?></p>
        <p>E-mail - <?= $payer['email'] ?></p>
        <p>
          <?= Html::a('Редактировать', ['/payers/update', 'id' => $payer['id']], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $CertificatesDataProvider,
            'filterModel' => $searchCertificates,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'number',
                    'fio_child',
                    'nominal',
                    'balance',


                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
        <p>
            <?= Html::a('Добавить сертификат', ['certificates/create'], ['class' => 'btn btn-success']) ?>
        </p>
    </div>
    <div id="panel4" class="tab-pane fade">
       <h2>Ожидающие подтверждения</h2>
        <?= GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'certificate_id',
                    // 'certificates.fio_child',
                    'date',
                    'number',
                    'status',
                    //'status_termination',
                    // 'status_comment:ntext',
                    // 'status_year',
                    // 'link_doc',
                    // 'link_ofer',
                    // 'start_edu_programm',
                    // 'start_edu_contract',
                    // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{permit}{view}{update}{delete}',
                     'buttons' =>
                         [
                             'permit' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/contracts/ok', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Подтвердить создание договора')
                                 ]); },
                         ]
                 ],
            ],
        ]); ?>

        <h2>Подтвержденные</h2>
        <?= GridView::widget([
            'dataProvider' => $Contracts1Provider,
            'filterModel' => $searchContracts1,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    // 'id',
                    'certificate_id',
                    // 'certificates.fio_child',
                    'date',
                    'number',
                    'status',
                    //'status_termination',
                    // 'status_comment:ntext',
                    // 'status_year',
                    // 'link_doc',
                    // 'link_ofer',
                    // 'start_edu_programm',
                    // 'start_edu_contract',
                    // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{view}{update}{delete}',
                 ],
            ],
        ]); ?>
    </div>
    <div id="panel5" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $InvoicesDataProvider,
            'filterModel' => $searchInvoices,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'contract_id',
                    'number',
                    'date',
                    'payers.name',
                    //'status',
                    //'status_termination',
                    // 'status_comment:ntext',
                    // 'status_year',
                    // 'link_doc',
                    // 'link_ofer',
                    // 'start_edu_programm',
                    // 'start_edu_contract',
                    // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
    <div id="panel6" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $OrganizationDataProvider,
            'filterModel' => $searchOrganization,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    //'user_id',
                    //'actual',
                    //'type',
                    'name',
                    // 'license_date',
                    // 'license_number',
                    // 'license_issued',
                    // 'requisites',
                    // 'representative',
                    'address_legal',
                    //'geocode',
                    //'max_child',
                    'amount_child',
                    'inn',
                    'okopo',
                    'raiting',
                    // 'ground',
                    //'cooperates.status',
                    //'user.username',
                    //'user.password',

                ['class' => 'yii\grid\ActionColumn',
                 'template' => '{view} {permit} {terminate}',
                 'buttons' =>
                         [
                        'permit' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/payers/cooperateokpayer', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Одобрить'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top'
                        ]); },

                        'terminate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-remove"></span>', Url::to(['/payers/cooperatenopayer', 'id' => $model->id]), [
                            'title' => Yii::t('yii', 'Отказать'),
                            'data-toggle' => 'tooltip',
                            'data-placement' => 'top'
                        ]); },
                        ],
                ],
            ],
        ]); ?>
    </div>
    <div id="panel7" class="tab-pane fade">
        <?= GridView::widget([
                'dataProvider' => $ProgramsProvider,
                'filterModel' => $searchPrograms,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    //'program_id',
                    //'organization_id',
                    //'verification',
                    'name',
                     'directivity',
                     'price',
                     'normative_price',
                     'rating',
                     'limit',
                    // 'study',
                    // 'open',
                    // 'goal:ntext',
                    // 'task:ntext',
                    // 'annotation:ntext',
                    // 'hours',
                    // 'ovz',
                    // 'quality_control',
                    // 'link',
                    // 'certification_date',

                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => 'programs',
                        'template' => '{view}{update}{delete}',
                     ],
                ],
            ]); ?>
    </div>
</div>
