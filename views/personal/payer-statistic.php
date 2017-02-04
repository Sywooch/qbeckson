<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;


$this->title = 'Информация';
   $this->params['breadcrumbs'][] = 'Информация';
/* @var $this yii\web\View */
?>

<?php /* if ($InformsProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Оповещения</h4>
          </div>
          <div class="modal-body">
            <?= GridView::widget([
                'dataProvider' => $InformsProvider,
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
<?php } ?>


<?php if ($CooperateProvider->getTotalCount() > 0) { ?>
    <div class="modal fade">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title">Новые организации</h4>
          </div>
          <div class="modal-body">
           <p>Эти организации желают с вами сотрудничать</p>
            <?= GridView::widget([
                'dataProvider' => $CooperateProvider,
                'summary' => false,
                'showHeader' => false,
                'columns' => [
                     'organization_id',

                    ['class' => 'yii\grid\ActionColumn',
                        'controller' => 'cooperate',
                        'template' => '{view} {read}',
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
                                     return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['/cooperate/read', 'id' => $model->id]), [
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


<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-1 well">
            <p><label class="control-label">Наименование организации</label> - <?= $payer['name'] ?></p>
            <p><label class="control-label">ИНН</label> - <?= $payer['INN'] ?></p>
            <p><label class="control-label">КПП</label> - <?= $payer['KPP'] ?></p>
            <p><label class="control-label">ОГРН</label> - <?= $payer['OGRN'] ?></p>
            <p><label class="control-label">ОКПО</label> - <?= $payer['OKPO'] ?></p>
            <p><label class="control-label">Юридический адрес</label> - <?= $payer['address_legal'] ?></p>
            <p><label class="control-label">Фактический адрес</label> - <?= $payer['address_actual'] ?></p>
            <p><label class="control-label">Представитель организации</label> - <?= $payer['fio'] ?></p>
            <p><label class="control-label">Номер телефона</label> - <?= $payer['phone'] ?></p>
            <p><label class="control-label">E-mail</label> - <?= $payer['email'] ?></p>
            <p>
              <?= Html::a('Редактировать', ['/payers/edit', 'id' => $payer['id']], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-md-5  col-md-offset-1 well">
            <p>Количество выданных сертификатов - <?= $count_certificates ?></p>
            <p>Общая сумма выданных сертификатов - <?= $sum_certificates ?></p>
            <p>Количество выданных сертификатов по которым заключены договора на обучение - <?= $count_certificates_contracts ?></p>
            <p>Количество детей обучающихся по одной образовательной программе с использованием выданных сертификатов - <?= $count_certificates_contracts_one ?></p>
            <p>Количество детей обучающихся по двум образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_two ?></p>
            <p>Количество детей обучающихся по трем и более образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_more ?></p>
            <p>Общее количество договоров обучающения заключенных с использованием выданных сертификатов - <?= $sum_contracts ?></p>
        </div>
    </div>
</div>