<?php
use yii\helpers\Html;
use kartik\grid\GridView;
use app\models\Informs;
use yii\helpers\Url;
use kartik\export\ExportMenu;
//use kartik\grid\GridView;
use app\models\Certificates;

/* @var $this yii\web\View */
$this->title = 'Организации';
$this->params['breadcrumbs'][] = 'Организации';
?>
<?php if (Yii::$app->user->can('certificate')) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right">
                <?= $this->render('../common/_select-municipality-modal') ?>
            </div>
        </div>
    </div>
    <br>
<?php endif; ?>
<?= GridView::widget([
    'dataProvider' => $OrganizationProvider,
    'filterModel' => $searchOrganization,
    'pjax'=>true,
    'rowOptions' => function ($model, $index, $widget, $grid){
                  if($model){
                      $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();

                $rows = (new \yii\db\Query())
                    ->select(['id'])
                    ->from('cooperate')
                    ->where(['payer_id'=> $certificate['payer_id']])
                    ->andWhere(['organization_id' => $model['id']])
                    ->andWhere(['status'=> 1])
                    ->count();
                      
                      if ($rows == 0) {
                    return ['class' => 'danger'];
                          }
                  }
            },
    'summary' => false,
    'columns' => [
        //['class' => 'yii\grid\SerialColumn'],

        //'id',
        //'user_id',
      /*  ['attribute'=>'actual',
                  'format' => 'raw',
                  'value' => function($data){
                         if ($data->actual == 0) {
                            return Html::a('Разрешить деятельность', Url::to(['/organization/actual', 'id' => $data->id]), ['class' => 'btn btn-success']);
                         } else {
                        $previus = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['organization_id' => $data->id])
                            ->andWhere(['status' => 1])
                            ->count();
                            if (!$previus) {
                                return Html::a('Приостановить', Url::to(['/organization/noactual', 'id' => $data->id]), ['class' => 'btn btn-danger']);
                            }
                            else {
                                return '';
                            }
                        }
                  }
        ], */
        'name',
        //'type',
        ['attribute'=>'type',
            'value' => function($data){
                if ($data->type == 1) {
                    return 'Образовательная организация';
                }
                if ($data->type == 2) {
                    return 'Организация, осуществляющая обучение';
                }
                if ($data->type == 3) {
                    return 'Индивидуальный предприниматель (с наймом)';
                }
                if ($data->type == 4) {
                    return 'Индивидуальный предприниматель (без найма)';
                }
            }
        ],
        [
            'label' => 'Число программ',
            'value' => function($data){
            $programs = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('programs')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['verification' => 2])
                        ->count();
            
            return $programs;
            }
        ],
        //'license_date',
        //'license_number',
         //'license_issued',
        // 'requisites',
        // 'representative',
        //'address_legal',
        //'geocode',
        
        [
            'label' => 'Число обучающихся',
            'value' => function($data){
                $cert = (new \yii\db\Query())
                        ->select(['certificate_id'])
                        ->from('contracts')
                        ->where(['organization_id' => $data->id])
                        ->andWhere(['status' => 1])
                        ->all();
                $cert = array_unique($cert);
                $cert = count($cert);
            
            return $cert;
            }
        ],
        'max_child',
        //'inn',
        //'okopo',
        'raiting',
        // 'ground',
        //'user.id',
        //'user.username',
        //'user.password',
        ['attribute'=>'actual',
          'value' => function($data){
                 if ($data->actual == 0) {
                    return '-';
                 } else {
                    return '+';
                }
          }
        ],
        [
                     'label' => 'Соглашение',
                     'value' => function ($data) { 
                          $certificates = new Certificates();
                            $certificate = $certificates->getCertificates();
                         
                      $rows = (new \yii\db\Query())
                        ->select(['id'])
                        ->from('cooperate')
                        ->where(['payer_id'=> $certificate['payer_id']])
                        ->andWhere(['organization_id' => $data['id']])
                        ->andWhere(['status'=> 1])
                        ->count();

                          if ($rows == 0) {
                            return 'Нет';
                            }
                         else {
                            return 'Да';
                            }
                     },
                ],
        ['class' => 'yii\grid\ActionColumn',
            'controller' => 'organization',
         'template' => '{view}',
        ],
    ],
]); ?>

