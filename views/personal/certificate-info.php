<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

$this->params['breadcrumbs'][] = ['label' => 'Персональная информация'];

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
                             ]
                     ],
                ],
            ]); ?>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div>
<?php } */ ?>
<br>

<div class="container-fluid col-md-10 col-md-offset-1">
    <div class="row">
       <div class="col-md-7 ">
            <h2><?= $certificate['fio_child'] ?></h2>
            
            <p class="biglabel">Номер сертификата <strong><?= $certificate['number'] ?></strong><p>
            
            <p class="biglabel">ФИО законного представителя <strong ><?= $certificate['fio_parent'] ?></strong><p>
            <br>
            <br>
            <p>
              <?= Html::a('Редактировать', ['/certificates/edit', 'id' => $certificate['id']], ['class' => 'btn btn-success']) ?>
              <?= Html::a('Изменить пароль', ['/certificates/password'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="well col-md-5 text-center">
            <div>
                <p class="biglabel">Номинал сертификата<br><strong class="bignumbers"><?= $certificate['nominal'] ?></strong></p>
                <p class="biglabel">Осталось средств<br><strong class="bignumbers"><?= $certificate['balance'] ?></strong></p>
                <p class="biglabel">Зарезервированно на оплату договоров<br><strong class="bignumbers"><?= $certificate['rezerv'] ?></strong></p> 
            </div>
            <!-- <div class="col-md-4">
                <div class="nominal">
                   <div class="rezerv" style="height:<?php // ($certificate['rezerv']* 100 / $certificate['nominal'])?>%;">
                       <p class="text-center"><?php // ($certificate['nominal'] - $certificate['balance']) + $certificate['rezerv'] ?></p>
                       </div>
                    <div class="balance" style="height:<?php // 100 - ($certificate['balance']* 100 / $certificate['nominal']) ?>%;"></div>
                    <p class="text-center"><?php// $certificate['balance'] ?></p> -->
                    
                </div>
            </div>
        </div>
    </div>
</div>