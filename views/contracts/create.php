<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use app\models\Payers;
use app\models\Certificates;
use kartik\widgets\DepDrop;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Создать договор';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create  col-md-10 col-md-offset-1">

    <h1><?= Html::encode($this->title) ?></h1>

     <?php $form = ActiveForm::begin(); ?>

    <?php // $form->field($model, 'certificate_id')->dropDownList(ArrayHelper::map(app\models\Certificates::find()->all(), 'id', 'number')) ?>
   
   
   <?php   
    
    $orgs = new Organization();
    $org = $orgs->getOrganization();
    
    $organization = Organization::findOne($org['id']);
    
    $count2 = (new \yii\db\Query())
                ->select(['id'])
                ->from('contracts')
                ->where(['status'=> [0,1,3,5]])
                ->andWhere(['organization_id' => $organization->id])
                ->count();
        
    $organization = Organization::findOne($organization->id);
    
    //echo var_dump($organization->max_child);
    
    $payer = Payers::findOne($model->payer_id);
    $certificate = Certificates::findOne($model->certificate_id);
        

if ($certificate->balance == 0) {
    echo '<h2>Нет свободных средств на сертификате.</h2>';
} else {
    if ($organization->actual == 0) {
        echo '<h2>Действие организации приостановленно.</h2>';
    }
    else {
        /*if ($count3 >= $limit_napr) {
             echo '<h2>Достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности.</h2>';
        }
        else { */

            if ($organization->max_child <= $count2) { 
                echo '<h2>Достигнут максимальный лимит зачисления в организацию. Свяжитесь с представителем организации.</h2>';
            }
            else {
                /* if ($program->limit <= $count1) {
                    echo '<h2>Достигнут максимальный лимит зачисления на обучение по программе. Свяжитесь с представителем организации.</h2>';
                }
                else { */
                
                    $programs = (new \yii\db\Query())
                        ->select(['id', 'name', 'directivity', 'limit'])
                        ->from('programs')
                        ->where(['organization_id' => $organization->id])
                        ->andwhere(['verification' => 2])
                        ->all();
                    
                    $val = array();
                    foreach ($programs as $program) {
                        
                        $count5 = (new \yii\db\Query())
                            ->select(['id'])
                            ->from('contracts')
                            ->where(['status'=> [0,1,3]])
                            ->andWhere(['certificate_id' => $certificate->id])
                            ->andWhere(['program_id' => $program['id']])
                            ->count();
                        if ($count5 == 0) { 
                        
                            $count3 = (new \yii\db\Query())
                                ->select(['id'])
                                ->from('contracts')
                                ->where(['status'=> [0,1,3]])
                                ->andWhere(['payer_id' => $certificate->payer_id])
                                ->andWhere(['program_id' => $program['id']])
                                ->count();

                            if ($program['directivity'] == 'Техническая (робототехника)') { 
                                    $directionality = explode(",", $payer->directionality);
                                    //return var_dump($directionality);
                                if (in_array('Техническая (робототехника)', $directionality)) {
                                    $limit_napr = $payer->directionality_1rob_count;
                                    if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                }
                                else {
                                    $limit_napr = 'nolimit';
                                }
                            }
                            if ($program['directivity'] == 'Техническая (иная)') {  
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Техническая (иная)', $directionality)) {
                                        $limit_napr = $payer->directionality_1_count;
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    }                                                 
                                }
                            if ($program['directivity'] == 'Естественнонаучная') { 
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Естественнонаучная', $directionality)) {
                                        $limit_napr = $payer->directionality_2_count; 
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    } 
                                 }
                            if ($program['directivity'] == 'Физкультурно-спортивная') { 
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Физкультурно-спортивная', $directionality)) {
                                        $limit_napr = $payer->directionality_3_count; 
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    } 
                                }
                            if ($program['directivity'] == 'Художественная') { 
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Художественная', $directionality)) {
                                        $limit_napr = $payer->directionality_4_count; 
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    } 
                                }
                            if ($program['directivity'] == 'Туристско-краеведческая') { 
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Туристско-краеведческая', $directionality)) {
                                        $limit_napr = $payer->directionality_5_count; 
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    }
                                }
                            if ($program['directivity'] == 'Социально-педагогическая') { 
                                    $directionality = explode(",", $payer->directionality);
                                    if (in_array('Художественная', $directionality)) {
                                        $limit_napr = $payer->directionality_6_count; 
                                        if ($limit_napr == 0 or empty($limit_napr)) {
                                           $limit_napr = 'nolimit'; 
                                        }
                                    }
                                    else {
                                        $limit_napr = 'nolimit';
                                    }
                            }

                            if ($limit_napr == 'nolimit') {
                                if ($count3 < $limit_napr) {
                                    $count1 = (new \yii\db\Query())
                                        ->select(['id'])
                                        ->from('contracts')
                                        ->where(['status'=> [0,1,3]])
                                        ->andWhere(['program_id' => $program['id']])
                                        ->count();
                                    if ($program['limit'] > $count1) {
                                        array_push($val, $program);
                                    }
                                }
                            }
                            else {
                                $count1 = (new \yii\db\Query())
                                        ->select(['id'])
                                        ->from('contracts')
                                        ->where(['status'=> [0,1,3]])
                                        ->andWhere(['program_id' => $program['id']])
                                        ->count();
                                if ($program['limit'] > $count1) {
                                    array_push($val, $program);
                                }
                            }
                        }
                    }
  

                    echo $form->field($model, 'program_id')->dropDownList(ArrayHelper::map($val, 'id', 'name'), ['id'=>'prog-id', 'prompt'=>'-- Не выбрана --',]);

                    echo $form->field($model, 'year_id')->widget(DepDrop::classname(), [
                        'options'=>['id'=>'year-id'],
                        'pluginOptions'=>[
                            'depends'=>['prog-id'],
                            'placeholder'=>'-- Не выбран --',
                            'url'=>Url::to(['/contracts/year'])
                        ]
                    ])->label('Модуль');

    
                    echo $form->field($model, 'group_id')->widget(DepDrop::classname(), [
                        'options'=>['id'=>'group-id'],
                        'pluginOptions'=>[
                            'depends'=>['prog-id', 'year-id'],
                            'placeholder'=>'-- Не выбрана --',
                            'url'=>Url::to(['/contracts/yeargroup'])
                        ]
                    ])->label('Группа');




                    echo '<div class="form-group">';
                        echo Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
                    echo '</div>';
                    //}
                }
           // }
        }
    }
    ?>

    <?php ActiveForm::end(); ?>

</div>
