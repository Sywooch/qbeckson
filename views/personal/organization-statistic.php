<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'Информация';
   $this->params['breadcrumbs'][] = 'Информация';
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

<div class="col-md-10 col-md-offset-1 well">
<p>Количество сертифицированных программ образовательной организации - <?= $count_programs ?></p>
<p>Количество програм  образовательной организации ожидающих сертификации - <?= $count_wait_programs ?></p>
<p>Максимально допустимое количество детей для обучения по системе персонифицированного финансирования - <?= $organization['max_child'] ?></p>
<p>Количество детей обучающихся по системе персонифицированного финансирования - <?php
    $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $organization['id']])
                        ->andWhere(['status' => 1])
                        ->column();
                $cert = array_unique($cert);
                $cert = count($cert);
    echo $cert;
    ?></p>
<p>Количество мест по которым могут быть заключены договора по системе персонифицированного финансирования - <?=  $organization['max_child'] - $cert ?></p>
<p>Количество заявок на заключение договоров по системе персонифицированного финансирования - <?= $contract_wait ?></p>
</div>