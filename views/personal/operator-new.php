<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;

/* @var $this yii\web\View */
?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Статическая информация</a></li>
    <li><a data-toggle="tab" href="#panel2">Сведения об операторе</a></li>
    <li><a data-toggle="tab" href="#panel3">Установка коэффициентов</a></li>
    <li><a data-toggle="tab" href="#panel4">Плательщики</a></li>
    <li><a data-toggle="tab" href="#panel5">Обучающие организации</a></li>
    <li><a data-toggle="tab" href="#panel6">Сертификаты</a></li>
    <li><a data-toggle="tab" href="#panel7">Договоры</a></li>
    <li><a data-toggle="tab" href="#panel8">Образовательные программы</a></li>
</ul>
<br>

<?php if ($dataProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
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

                                'view' => function ($url, $model) {
                                     return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['/programs/view', 'id' => $model->program_id]), [
                                         'title' => Yii::t('yii', 'Просмотреть программу'),
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
        <p>Общее число детей в системе - <?= $operator['id'] ?></p>
        <p>Общее число детей, использующих свой сертификат - <?= $operator['id'] ?></p>
        <p>Число детей, использующих сертификат для освоения одной программы - <?= $operator['id'] ?></p>
        <p>Число детей, использующих сертификат для освоения двух программ - <?= $operator['id'] ?></p>
        <p>Число детей, использующих сертификат для освоения трех и более программ  - <?= $operator['id'] ?></p>
        <p>Общее число договоров - <?= $operator['id'] ?></p>
        <p>Число организаций в системе персонифицированного финансирования - <?= $operator['id'] ?></p>
        <p>Число программ, доступных в рамках системы - <?= $operator['id'] ?></p>
    </div>

    <div id="panel2" class="tab-pane fade">
        <p>Наименование - <?= $operator['name'] ?></p>
        <p>ИНН - <?= $operator['INN'] ?></p>
        <p>КПП - <?= $operator['KPP'] ?></p>
        <p>ОГРН - <?= $operator['OGRN'] ?></p>
        <p>ОКПО - <?= $operator['OKPO'] ?></p>
        <p>Юридический адрес - <?= $operator['address_legal'] ?></p>
        <p>Фактический адрес - <?= $operator['address_actual'] ?></p>
        <p>Телефон - <?= $operator['phone'] ?></p>
        <p>Email - <?= $operator['email'] ?></p>
        <p>Должность ответственного лица - <?= $operator['position'] ?></p>
        <p>ФИО ответственного лица - <?= $operator['fio'] ?></p>
        <p>
          <?= Html::a('Редактировать', ['/operators/update', 'id' => $operator['id']], ['class' => 'btn btn-success']) ?>
        </p>
    </div>

    <div id="panel3" class="tab-pane fade">
    </div>

    <div id="panel4" class="tab-pane fade">
        <p>
            <?= Html::a('Добавить плательщика', ['payers/create'], ['class' => 'btn btn-success']) ?>
        </p>

         <?= GridView::widget([
        'dataProvider' => $PayersProvider,
        'filterModel' => $searchPayers,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'user.username',
            'name',
            //'OGRN',
            //'INN',
            //'KPP',
            //'OKPO',
            'address_legal',
            // 'address_actual',
             'phone',
             'email:email',
             //'position',
             'fio',
             'directionality',
            // 'directionality_1_count',
            // 'directionality_2_count',
            // 'directionality_3_count',
            // 'directionality_4_count',
            // 'directionality_5_count',
            // 'directionality_6_count',

            ['class' => 'yii\grid\ActionColumn',
                'controller' => 'payers',
            ],
        ],
    ]); ?>

    <?= ExportMenu::widget([
            'dataProvider' => $PayersProvider,
            'columns' => [
                'id',
                'user_id',
                'name',
                'OGRN',
                'INN',
                'KPP',
                'OKPO',
                'address_legal',
                'address_actual',
                'phone',
                'email:email',
                'position',
                'fio',
                'directionality',
                'directionality_1_count',
                'directionality_2_count',
                'directionality_3_count',
                'directionality_4_count',
                'directionality_5_count',
                'directionality_6_count',
            ],
        ]); ?>
    </div>

    <div id="panel5" class="tab-pane fade">
        <p>
            <?= Html::a('Добавить организацию', ['organization/create'], ['class' => 'btn btn-success']) ?>
        </p>
         <?= GridView::widget([
            'dataProvider' => $OrganizationProvider,
            'filterModel' => $searchOrganization,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'user_id',
                'actual',
                'type',
                'name',
                //'license_date',
                'license_number',
                 //'license_issued',
                // 'requisites',
                // 'representative',
                'address_legal',
                //'geocode',
                //'max_child',
                'amount_child',
                //'inn',
                //'okopo',
                'raiting',
                // 'ground',
                //'user.id',
                //'user.username',
                //'user.password',

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'organization',
                ],
            ],
        ]); ?>
        <?= ExportMenu::widget([
            'dataProvider' => $PayersProvider,
            'columns' => [
                'id',
                'user_id',
                'actual',
                'type',
                'name',
                'license_date',
                'license_number',
                'license_issued',
                'requisites',
                'representative',
                'address_legal',
                'geocode',
                'max_child',
                'amount_child',
                'inn',
                'okopo',
                'raiting',
                'ground',
                'user.id',
                'user.username',
                'user.password',
            ],
        ]); ?>
    </div>

    <div id="panel6" class="tab-pane fade">
        <?= GridView::widget([
                'dataProvider' => $CertificatesProvider,
                'filterModel' => $searchCertificates,
                'columns' => [
                    // ['class' => 'yii\grid\SerialColumn'],

                        //'id',
                        'number',
                        'fio_child',
                        'actual',
                        'nominal',
                        'balance',


                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => 'certificates',
                    ],
                ],
            ]); ?>
        <?= ExportMenu::widget([
            'dataProvider' => $PayersProvider,
            'columns' => [
                'id',
                'number',
                'fio_child',
                'actual',
                'nominal',
                'balance',
            ],
        ]); ?>
    </div>

    <div id="panel7" class="tab-pane fade">
        <h2>Ожидающие подтверждения</h2>
         <?= GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'columns' => [
               // ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'number',
                'date',
                'status',
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                 'link_doc',
                 'link_ofer',
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
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
        <?= ExportMenu::widget([
            'dataProvider' => $PayersProvider,
            'columns' => [
                'id',
                'number',
                'date',
                'status',
                'status_termination',
                'status_comment:ntext',
                'status_year',
                'link_doc',
                'link_ofer',
                'start_edu_programm',
                'start_edu_contract',
                'stop_edu_contract',
            ],
        ]); ?>

        <h2>Подтвержденные</h2>
        <?= GridView::widget([
            'dataProvider' => $Contracts1Provider,
            'filterModel' => $searchContracts1,
            'columns' => [
               // ['class' => 'yii\grid\SerialColumn'],

                //'id',
                'number',
                'date',
                'status',
                //'status_termination',
                //'status_comment:ntext',
                //'status_year',
                 'link_doc',
                 'link_ofer',
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                ],
            ],
        ]); ?>
        <?= ExportMenu::widget([
            'dataProvider' => $PayersProvider,
            'columns' => [
                'id',
                'number',
                'date',
                'status',
                'status_termination',
                'status_comment:ntext',
                'status_year',
                'link_doc',
                'link_ofer',
                'start_edu_programm',
                'start_edu_contract',
                'stop_edu_contract',
            ],
        ]); ?>
    </div>
    <div id="panel8" class="tab-pane fade">
        <h2>Ожидающие сертификации</h2>
        <?= GridView::widget([
            'dataProvider' => $Programs0Provider,
            'filterModel' => $searchPrograms0,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'organization_id',
                //'verification',
                'name',
                 'directivity',
                 'price',
                 'normative_price',
                 'rating',
                 //'limit',
                 'study',
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
                    'template' => '{permit}{view}{update}{delete}',
                     'buttons' =>
                         [
                             'permit' => function ($url, $model) {
                                 return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/programs/verificate', 'id' => $model->id]), [
                                     'title' => Yii::t('yii', 'Сертифицировать программу')
                                 ]); },
                         ]
                 ],
            ],
        ]); ?>
        <?= ExportMenu::widget([
            'dataProvider' => $Programs0Provider,
            'columns' => [
                 'id',
                 'organization_id',
                 'verification',
                 'name',
                 'directivity',
                 'price',
                 'normative_price',
                 'rating',
                 'limit',
                 'study',
                 'open',
                 'goal:ntext',
                 'task:ntext',
                 'annotation:ntext',
                 'hours',
                 'ovz',
                 'quality_control',
                 'link',
                 'certification_date',
            ],
        ]); ?>

        <h2>Сертифицированные</h2>
        <?= GridView::widget([
            'dataProvider' => $Programs1Provider,
            'filterModel' => $searchPrograms1,
            'columns' => [
                //['class' => 'yii\grid\SerialColumn'],

                //'id',
                //'organization_id',
                //'verification',
                'name',
                 'directivity',
                 'price',
                 'normative_price',
                 'rating',
                 //'limit',
                 'study',
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
            <?= ExportMenu::widget([
            'dataProvider' => $Programs0Provider,
            'columns' => [
                 'id',
                 'organization_id',
                 'verification',
                 'name',
                 'directivity',
                 'price',
                 'normative_price',
                 'rating',
                 'limit',
                 'study',
                 'open',
                 'goal:ntext',
                 'task:ntext',
                 'annotation:ntext',
                 'hours',
                 'ovz',
                 'quality_control',
                 'link',
                 'certification_date',
            ],
        ]); ?>
    </div>
</div>
