<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use kartik\export\ExportMenu;
use app\models\Organization;


$this->title = 'Договоры';
$this->params['breadcrumbs'][] = 'Договоры';
/* @var $this yii\web\View */
?>

<?php /* if ($informsProvider->getTotalCount() > 0) { ?>
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
<?php } */ ?>

<ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#panel1">Действующие <span class="badge"><?= $Contracts1Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel2">Подтвержденные <span class="badge"><?= $Contracts3Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel3">Ожидающие подтверждения <span class="badge"><?= $Contracts0Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel4">Заканчивающие действие <span class="badge"><?= $Contracts4Provider->getTotalCount() ?></span></a></li>
    <li><a data-toggle="tab" href="#panel5">Расторгнутые</a></li>
</ul>
<br>

<div class="tab-content">
   <?php
        $roles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);

        $organizations = new Organization();
        $organization = $organizations->getOrganization();

        if ($roles['organizations'] and $organization['actual'] != 0) {
            echo "<p>";
            echo Html::a('Создать новый договор', ['certificates/verificate'], ['class' => 'btn btn-success']); 
            echo "</p>";
        }
    ?>
    
    <div id="panel1" class="tab-pane fade in active">
        <?= GridView::widget([
            'dataProvider' => $Contracts1Provider,
            'filterModel' => $searchContracts1,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'pjax'=>true,
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
                    'attribute' => 'payersname',
                    'label' => 'Плательщик',
                    'format' => 'raw',                
                ],
                [
                    'attribute' => 'yearyear',
                    'label' => 'Год',
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
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'pjax'=>true,
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
                    'attribute' => 'payersname',
                    'label' => 'Плательщик',
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
                             ]); },
                     ]
                ],
            ],
        ]); ?>
    </div>
    
    <div id="panel3" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts0Provider,
            'filterModel' => $searchContracts0,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'pjax'=>true,
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
                    'attribute' => 'payersname',
                    'label' => 'Плательщик',
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
                             ]); },
                     ]
                ],
            ],
        ]); ?>
    </div>
    
    <div id="panel4" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts4Provider,
            'filterModel' => $searchContracts4,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'pjax'=>true,
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
                    'attribute' => 'payersname',
                    'label' => 'Плательщик',
                    'format' => 'raw',                
                ],
                [
                    'attribute' => 'yearyear',
                    'label' => 'год',
                ],
                [
                    'attribute' => 'stop_edu_contract',
                    'format' => 'date',
                    'label' => 'Действует до',
                ],
                 //'link_doc',
                 //'link_ofer',
                // 'start_edu_programm',
                // 'start_edu_contract',
                // 'stop_edu_contract',

                ['class' => 'yii\grid\ActionColumn',
                    'controller' => 'contracts',
                    'template' => '{view}',
                ],
            ],
        ]); ?>
    </div>
    
    <div id="panel5" class="tab-pane fade">
        <?= GridView::widget([
            'dataProvider' => $Contracts5Provider,
            'filterModel' => $searchContracts5,
            'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model->wait_termnate == 1){
                    return ['class' => 'danger'];
                  }
            },
            'pjax'=>true,
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
                    'attribute' => 'payersname',
                    'label' => 'Плательщик',
                    'format' => 'raw',                
                ],
                [
                    'attribute' => 'yearyear',
                    'label' => 'год',
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
