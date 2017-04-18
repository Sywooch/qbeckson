<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
?>
<h3 class="center">Личный кабинет организации "<?= $organization['name'] ?>"</h3>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Статическая информация</a></li>
    <li><a data-toggle="tab" href="#panel2">Сведения об организации</a></li>
    <li><a data-toggle="tab" href="#panel3">Программы обучения</a></li>
    <li><a data-toggle="tab" href="#panel4">Договоры</a></li>
    <li><a data-toggle="tab" href="#panel5">Счета</a></li>
    <li><a data-toggle="tab" href="#panel6">Плательщики</a></li>
    <li><a data-toggle="tab" href="#panel7">Группы</a></li>
    <li><a data-toggle="tab" href="#panel8">В избранном</a></li>
</ul>
<br>

<?php if ($informsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $informsProvider,
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
        <p>Количество сертифицированных программ образовательной организации - <?= $count_programs ?></p>
        <p>Количество програм  образовательной организации ожидающих сертификации - <?= $count_wait_programs ?></p>
        <p>Максимально допустимое количество детей для обучения по системе персонифицированного финансирования - <?= $organization['max_child'] ?></p>
        <p>Количество детей обучающихся по системе персонифицированного финансирования - <?= $organization['amount_child'] ?></p>
        <p>Количество мест по которым могут быть заключены договора по системе персонифицированного финансирования - <?=  $organization['max_child'] - $organization['amount_child'] ?></p>
        <p>Количество заявок на заключение договоров по системе персонифицированного финансирования - <?= $contract_wait ?></p>
    </div>
    <div id="panel2" class="tab-pane fade">
        <p>Наименование организации - <?= $organization['name'] ?></p>
        <p>ИНН - <?= $organization['inn'] ?></p>
        <p>КПП - <?= $organization['KPP'] ?></p>
        <p>ОГРН - <?= $organization['OGRN'] ?></p>
        <p>ОКПО - <?= $organization['okopo'] ?></p>
        <p>Юридический адрес - <?= $organization['address_legal'] ?></p>
        <p>Фактический адрес - <?= $organization['address_actual'] ?></p>
        <p>Наименование банка - <?= $organization['bank_name'] ?></p>
        <p>Расчетный счет банка - <?= $organization['rass_invoice'] ?></p>
        <p>БИК Банка - <?= $organization['bank_bik'] ?></p>
        <p>Корр/Счет - <?= $organization['korr_invoice'] ?></p>
        <p>Город банка - <?= $organization['bank_sity'] ?></p>
        <p>Представитель организации - <?= $organization['fio'] ?></p>
        <p>
          <?= Html::a('Редактировать', ['/organization/update', 'id' => $organization['id']], ['class' => 'btn btn-success']) ?>
        </p>

        <?php $form = ActiveForm::begin(); ?>

            <?= $form->field($model, 'about')->textarea(['rows' => 6]) ?>

            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Добавить "Почему выбирают нас"' : 'Редактировать "Почему выбирают нас"', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>

        <?php ActiveForm::end(); ?>

    </div>
    <div id="panel3" class="tab-pane fade">

        <p>
            <?= Html::a('Отправить программу на сертификацию', ['programs/create'], ['class' => 'btn btn-success']) ?>
        </p>

        <h2>Ожидающие сертификации</h2>
        <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'program_id',
            //'organization_id',
            //'verification',
             'name',
             'directivity',
             'open',
            // 'normative_price',
             'price',
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
            ],
        ],
    ]); ?>

    <h2>Сертифицированные</h2>
        <?= GridView::widget([
        'dataProvider' => $programcertProvider,
        'filterModel' => $programcertModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'program_id',
            //'organization_id',
            //'verification',
             'name',
             'directivity',
             'open',
            // 'normative_price',
             'price',
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
                'template' => '{permit}{view}{update}{delete}',
                 'buttons' =>
                     [
                         'permit' => function ($url, $model) {
                             return Html::a('<span class="glyphicon glyphicon-ok"></span>', Url::to(['/programs/open', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Опубликовать программу')
                             ]); },
                     ]
             ],
        ],
    ]); ?>

    </div>
    <div id="panel4" class="tab-pane fade">
        <p>
            <?= Html::a('Создать новый договор', ['contracts/create'], ['class' => 'btn btn-success']) ?>
        </p>
       <h2>Ожидающие подтверждения</h2>
        <?= GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                    //'id',
                    'number',
                    'date',
                    'certificate.number',
                    'program.name',
                    //'status',
                    //'status_termination',
                    // 'status_comment:ntext',
                    // 'status_year',
                    // 'link_doc',
                    // 'link_ofer',
                    // 'start_edu_programm',
                    // 'start_edu_contract',
                    // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {terminate}',
                 'buttons' =>
 [
                         'terminate' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/disputes/terminate', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Расторгнуть контракт')
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

                    //'id',
                    'number',
                    'date',
                    'certificate.number',
                    'program.name',
                    //'status',
                    //'status_termination',
                    // 'status_comment:ntext',
                    // 'status_year',
                    // 'link_doc',
                    // 'link_ofer',
                    // 'start_edu_programm',
                    // 'start_edu_contract',
                    // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {terminate}',
                 'buttons' =>
                    [
                         'terminate' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/disputes/terminate', 'id' => $model->id]), [
                                 'title' => Yii::t('yii', 'Расторгнуть контракт')
                             ]); },
                     ],
                 'controller' => 'contracts',
                ],
            ],
        ]); ?>
    </div>
    <div id="panel5" class="tab-pane fade">
        <p>
            <?= Html::a('Создать новый счет', ['contracts/invoice'], ['class' => 'btn btn-success']) ?>
        </p>
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
        <p>
            <?= Html::a('Выбрать плательщиков', ['payers/index'], ['class' => 'btn btn-success']) ?>
        </p>
        <h2>Действующие соглашения</h2>
        <?= GridView::widget([
            'dataProvider' => $PayersProvider,
            'filterModel' => $searchPayers,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'user_id',
                'name',
                'OGRN',
                'INN',
                // 'KPP',
                // 'OKPO',
                // 'address_legal',
                // 'address_actual',
                // 'phone',
                // 'email:email',
                // 'position',
                // 'fio',
                // 'directionality',
                // 'directionality_1_count',
                // 'directionality_2_count',
                // 'directionality_3_count',
                // 'directionality_4_count',
                // 'directionality_5_count',
                // 'directionality_6_count',

                ['class' => 'yii\grid\ActionColumn',
                'controller' => 'payers',
                 'template' => '{view} {terminate}',
                 'buttons' => [
                        'terminate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/payers/decooperate', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Расторгнуть соглашение')
                         ]); },
                    ],
                ],
            ],
        ]); ?>

        <h2>Ожитается подтверждение</h2>
        <?= GridView::widget([
            'dataProvider' => $PayersWaitProvider,
            'filterModel' => $searchPayersWait,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'user_id',
                'name',
                'OGRN',
                'INN',
                // 'KPP',
                // 'OKPO',
                // 'address_legal',
                // 'address_actual',
                // 'phone',
                // 'email:email',
                // 'position',
                // 'fio',
                // 'directionality',
                // 'directionality_1_count',
                // 'directionality_2_count',
                // 'directionality_3_count',
                // 'directionality_4_count',
                // 'directionality_5_count',
                // 'directionality_6_count',

                ['class' => 'yii\grid\ActionColumn',
                'controller' => 'payers',
                 'template' => '{view} {terminate}',
                 'buttons' => [
                        'terminate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['/payers/decooperate', 'id' => $model->id]), [
                             'title' => Yii::t('yii', 'Расторгнуть соглашение')
                         ]); },
                    ],
                ],
            ],
        ]); ?>
    </div>
    <div id="panel7" class="tab-pane fade">
        <p>
            <?= Html::a('Добавить группу', ['/groups/create'], ['class' => 'btn btn-success']) ?>
        </p>
        <?= GridView::widget([
        'dataProvider' => $GroupsProvider,
        'filterModel' => $searchGroups,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            //'organization_id',
            'program_id',
            'name',

            ['class' => 'yii\grid\ActionColumn',
                'controller' => 'groups',
                'template' => '{view} {update} {delete} {completeness}',
                'buttons' => [
                    'completeness' => function ($url, $model) {
                        return Html::a('Полнота оказанных услуг', Url::to(['/completeness/create', 'id' => $model->id]), ['title' => Yii::t('yii', 'Полнота оказанных услуг')]);
                    },
                ]
            ],
        ],
    ]); ?>
    </div>
    <div id="panel8" class="tab-pane fade">
            <?= GridView::widget([
        'dataProvider' => $FavoritesProvider,
        'filterModel' => $searchFavorites,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'id',
            'certificate_id',
            'program_id',
            'organization_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    </div>
</div>
