<?php
use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;

/* @var $this yii\web\View */

$this->title = 'Информация';
   $this->params['breadcrumbs'][] = 'Информация';
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

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-1 well">
            <p><label class="control-label">Наименование</label> - <?= $operator['name'] ?></p>
            <p><label class="control-label">ИНН</label> - <?= $operator['INN'] ?></p>
            <p><label class="control-label">КПП</label> - <?= $operator['KPP'] ?></p>
            <p><label class="control-label">ОГРН</label> - <?= $operator['OGRN'] ?></p>
            <p><label class="control-label">ОКПО</label> - <?= $operator['OKPO'] ?></p>
            <p><label class="control-label">Юридический адрес</label> - <?= $operator['address_legal'] ?></p>
            <p><label class="control-label">Фактический адрес</label> - <?= $operator['address_actual'] ?></p>
            <p><label class="control-label">Телефон</label> - <?= $operator['phone'] ?></p>
            <p><label class="control-label">Email</label> - <?= $operator['email'] ?></p>
            <p><label class="control-label">Должность ответственного лица</label> - <?= $operator['position'] ?></p>
            <p><label class="control-label">ФИО ответственного лица</label> - <?= $operator['fio'] ?></p>
            <p>
              <?= Html::a('Редактировать', ['/operators/update', 'id' => $operator['id']], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-md-5  col-md-offset-1 well">
            <p>Общее число детей в системе - <?= $count_certificates ?></p>
            <p>Общее число детей, использующих свой сертификат - <?= $count_certificates_use ?></p>
            <p>Детей, использующих сертификат для освоения одной программы - <?= $count_certificates_one ?></p>
            <p>Детей, использующих сертификат для освоения двух программ - <?= $count_certificates_two ?></p>
            <p>Детей, использующих сертификат для освоения трех и более программ  - <?= $count_certificates_more ?></p>
            <p>Общее число договоров - <?= $count_contracts ?></p>
            <p>Организаций в системе персонифицированного финансирования - <?= $count_organizations ?></p>
            <p>Программ, доступных в рамках системы - <?= $count_programs ?></p>
        </div>
    </div>
</div>