<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use app\models\Certificates;
use app\models\Payers;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Выберете группу';
$this->params['breadcrumbs'][] = ['label' => 'Поиск программ', 'url' => ['/programs/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create">

   

    <?= DetailView::widget([
        'model' => $program,
        'attributes' => [
            'name',
            'directivity',
            'annotation:ntext',
            [
                'attribute'=>'link',
                'format'=>'raw',
                'value'=>Html::a('<span class="glyphicon glyphicon-download-alt"></span>', $program->programFile),
            ],
        ],
    ])
    ?>
    <?= DetailView::widget([
        'model' => $year,
        'attributes' => [
            'month',
            'hours',
            'kvfirst',
            'kvdop',
            'hoursindivid',
            'hoursdop',
            'minchild',
            'maxchild',
            'price',
            'normative_price',
            //'rating',
            //'limits',
            'open',
            'quality_control',

        ],
    ])
    ?>
    
 <?php   
        
    $count1 = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['status'=> [0,1,3]])
        ->andWhere(['program_id' => $program->id])
                ->count();
    
    $count2 = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['status'=> [0,1,3,5]])
                ->andWhere(['organization_id' => $program->organization_id])
                ->count();
        
    $organization = Organization::findOne($program->organization_id);
    
    //echo var_dump($organization->max_child);
    
    $certificates = new Certificates();
    $certificate = $certificates->getCertificates();
    
    $programscolumn = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['directivity' => $program->directivity])
                ->column();
    
        
    $count3 = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['status'=> [0,1,3]])
                ->andWhere(['payer_id' => $certificate->payer_id])
                ->andWhere(['program_id' => $programscolumn])
                ->count();
    
    $payer = Payers::findOne($certificate->payer_id);
        
    if ($program->directivity == 'Техническая (робототехника)') { 
        $limit_napr = $payer->directionality_1rob_count;
    }
    if ($program->directivity == 'Техническая (иная)') { $limit_napr = $payer->directionality_1_count; }
    if ($program->directivity == 'Естественнонаучная') { $limit_napr = $payer->directionality_2_count; }
    if ($program->directivity == 'Физкультурно-спортивная') { $limit_napr = $payer->directionality_3_count; }
    if ($program->directivity == 'Художественная') { $limit_napr = $payer->directionality_4_count; }
    if ($program->directivity == 'Туристско-краеведческая') { $limit_napr = $payer->directionality_5_count; }
    if ($program->directivity == 'Социально-педагогическая') { $limit_napr = $payer->directionality_6_count; }
        
    //echo $count3;
    //echo $limit_napr;

if ($certificate->balance == 0) {
    echo '<h2>Вы не можете записаться на программу. Нет свободных средств на сертификате.</h2>';
} else {
    if ($organization->actual == 0) {
        echo '<h2>Вы не можете записаться на программу. Действие организации приостановленно.</h2>';
    }
    else {
        if ($count3 >= $limit_napr) {
             echo '<h2>Вы не можете записаться на программу. Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h2>';
        }
        else {

            if ($organization->max_child <= $count2) { 
                echo '<h2>Вы не можете записаться на программу. Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h2>';
            }
            else {
                if ($program->limit <= $count1) {
                    echo '<h2>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h2>';
                }
                else {

                  echo '<h2>Вы можете записаться на программу. Выберете группу:</h2>';

                    echo GridView::widget([
                            'dataProvider' => $GroupsProvider,
                            'columns' => [

                                //'id',
                                //'organization_id',
                                //'program_id',
                               // 'year_id',
                                'name',
                                'address',
                                'schedule',
                                'datestart:date',
                                'datestop:date',
                                [
                                    'label' => 'Количество свободных мест',
                                    'value'=> function ($model) {

                                        $contract = (new \yii\db\Query())
                                            ->select(['id'])
                                            ->from('contracts')
                                            ->where(['status' => [0,1,3]])
                                            ->andWhere(['group_id' => $model->id])
                                            ->count();

                                        $years = (new \yii\db\Query())
                                            ->select(['maxchild'])
                                            ->from('years')
                                            ->where(['id' => $model->year_id])
                                            ->one();

                                    return $years['maxchild'] - $contract;
                                    }
                                ],

                                ['class' => 'yii\grid\ActionColumn',
                                    'template' => '{permit}',
                                     'buttons' =>
                                         [
                                             'permit' => function ($url, $model) {

                                                 $contract = (new \yii\db\Query())
                                                    ->select(['id'])
                                                    ->from('contracts')
                                                    ->where(['status' => [0,1,3]])
                                                    ->andWhere(['group_id' => $model->id])
                                                    ->count();

                                                $years = (new \yii\db\Query())
                                                    ->select(['maxchild'])
                                                    ->from('years')
                                                    ->where(['id' => $model->year_id])
                                                    ->one();

                                                  //$certificates = new Certificates();
                                                  //  $cert = $certificates->getCertificates();

                                                 if ($years['maxchild'] - $contract = 0) {
                                                    return Html::a('Выбрать', Url::to(['/contracts/new', 'id' => $model->id]), [
                                                         'class' => 'btn btn-success',
                                                         'title' => Yii::t('yii', 'Выбрать')
                                                     ]); 
                                                 }
                                             },
                                         ]
                                 ],
                            ],
                        ]);
                    }
            }
        }
}
    }
?>
</div>
