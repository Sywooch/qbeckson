<?php

use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use app\models\Certificates;
use app\models\Payers;
use kartik\datecontrol\DateControl;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$this->title = 'Генерировать договор';
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/organization-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create col-md-10 col-md-offset-1">
   
   <?php $form = ActiveForm::begin(); ?>

   <h3>Уточните «шапку» для договора. Обратите, пожалуйста, особое внимание на падежи.</h3>
   <br>
   <div class="form-group-inline">
   <?php
    if ($organization->type != 4) {
        $license_date  = explode("-", $organization->license_date);
        $date_proxy  = explode("-", $organization->date_proxy);
        //$month_text = \Yii::t('app', 'Today is {0,date}', time());
        
        if ($organization->doc_type == 1) {
            $doc_type = "доверенности от ".$date_proxy[2].".".$date_proxy[1].".".$date_proxy[0]." № ".$organization->number_proxy;
        }
        if ($organization->doc_type == 2) {
            $doc_type = "устава";
        }
        if ($organization->doc_type == 3) {
            $doc_type = "свидельства о государственной регистрации";
        }
        
        $cert = Certificates::findOne($model->certificate_id);
        
        $model->change_org_fio = $organization->fio;
        $model->change_doctype = $doc_type;
        $model->change_fioparent = $cert->fio_parent;
        $model->change_fiochild = $cert->fio_child;
        $model->org_position = $organization->position;
        
        
        echo $organization->full_name.', осуществляющ'.
            $form->field($model, 'change1')->textInput(['style' => 'width:4em'])->label(false)
            .' образовательную  деятельность на основании лицензии от '.$license_date[2].'.'.$license_date[1].'.'.$license_date[0].' г. № '.$organization->license_number.', выданной '.$organization->license_issued_dat.', <br>именуем'.
            $form->field($model, 'change2')->textInput(['style' => 'width:4em'])->label(false)
            .' в дальнейшем "Исполнитель", в лице '.$model->org_position.' '.
            $form->field($model, 'change_org_fio')->textInput(['style' => 'width:20em'])->label(false)
            .', действующе'.
            $form->field($model, 'change10')->textInput(['style' => 'width:4em'])->label(false)
            .' на основании '.
            $form->field($model, 'change_doctype')->textInput(['style' => 'width:20em'])->label(false)
            .', и '.
            $form->field($model, 'change_fioparent')->textInput(['style' => 'width:20em'])->label(false)
            .', именуем'.
            $form->field($model, 'change6')->textInput(['style' => 'width:4em'])->label(false)
            .' в   дальнейшем    "Заказчик",    действующ'.
            $form->field($model, 'change9')->textInput(['style' => 'width:4em'])->label(false)
            .'  в  интересах несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата № '.$cert->number.', '.
            $form->field($model, 'change_fiochild')->textInput(['style' => 'width:20em'])->label(false)
            .', именуем'.
            $form->field($model, 'change8')->textInput(['style' => 'width:4em'])->label(false)
            .' в дальнейшем "Обучающийся», совместно   именуемые   Стороны,   заключили   настоящий    Договор    о нижеследующем:';
    }
    else {
        
        if ($organization->doc_type == 1) {
            $doc_type = "доверенности от ".$date_proxy[2].".".$date_proxy[1].".".$date_proxy[0]." № ".$organization->number_proxy;
        }
        if ($organization->doc_type == 2) {
            $doc_type = "устава";
        }
        if ($organization->doc_type == 3) {
            $doc_type = "свидельства о государственной регистрации";
        }
        
        $cert = Certificates::findOne($model->certificate_id);
        
        $model->change_org_fio = $organization->fio;
        $model->change_doctype = $doc_type;
        $model->change_fioparent = $cert->fio_parent;
        $model->change_fiochild = $cert->fio_child;
        $model->org_position = $organization->position;
        
        
        echo  $organization->full_name.', именуемый в дальнейшем "Исполнитель", и '.
            $form->field($model, 'change_fioparent')->textInput()->label(false)
            .', именуем'.
            $form->field($model, 'change6')->textInput()->label(false)
            .' в   дальнейшем    "Заказчик",    действующий  в  интересах несовершеннолетнего, включенного в систему персонифицированного финансирования дополнительного образования на основании сертификата № '.$cert->number.', '.$cert->fio_child.', именуем'.
            $form->field($model, 'change8')->textInput()->label(false)
            .' в дальнейшем "Обучающийся», совместно   именуемые   Стороны,   заключили   настоящий    Договор    о нижеследующем:';
    }
    
echo '</div><br><br>';
    echo $form->field($model, 'sposob')->dropDownList([1 => 'за наличный расчет', 2 => 'в безналичном порядке на счет Организации']);
    
    echo $form->field($model, 'fontsize')->dropDownList([
    '11.5' => '11.5',
    '12' => '12',
    '12.5' => '12.5',
    '13' => '13',
    '13.5' => '13.5',
    '14' => '14',
    ]);
       
    $cooperate = (new \yii\db\Query())
            ->select(['number', 'date'])
            ->from('cooperate')
            ->where(['organization_id' => $model->organization_id])
            ->andWhere(['payer_id' => $model->payer_id])
            ->andWhere(['status' => 1])
            ->one();
    $date_cooperate = explode("-", $cooperate['date']);

    $payer = Payers::findOne($model->payer_id);
    ?>
    
    <h3>Порядок оплаты договора:</h3>
    
    Полная стоимость образовательной услуги за период обучения по Договору составляет <?= floor($model->all_funds) ?> руб. 
    <?= round(($model->all_funds - floor($model->all_funds)) * 100, 0) ?> коп., в том числе:<br>
    <ul>
        <li>Будет оплачено за счет средств сертификата дополнительного образования Обучающегося - <?= floor($model->funds_cert) ?> руб. 
    <?= round(($model->funds_cert - floor($model->funds_cert)) * 100, 0) ?> коп.</li>
    
       <?php 
        if ($model->cert_dol != 0) {
            echo "<li>Будет оплачено за счет средств Заказчика - ". floor($model->all_parents_funds) ." руб. ". round(($model->all_parents_funds - floor($model->all_parents_funds)) * 100, 0) ." коп.</li>";
        }
        ?>
        
    </ul>
    Оплата за счет средств сертификата осуществляется в рамках договора об оплате обучения № <?= $cooperate['number'] ?> от <?= $date_cooperate[2] ?>.<?= $date_cooperate[1] ?>.<?= $date_cooperate[0] ?> заключенного между Исполнителем и <?= $payer->name_dat ?> (далее – Соглашение, Уполномоченная организация) ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:
    <ul>
        <li><?= floor($model->first_m_price * $model->payer_dol) ?> руб. 
    <?= round((($model->first_m_price * $model->payer_dol) - floor($model->first_m_price * $model->payer_dol)) * 100, 0) ?> коп. - за первый месяц периода обучения по Договору</li>
        <li><?= floor($model->other_m_price * $model->payer_dol) ?> руб. 
    <?= round((($model->other_m_price * $model->payer_dol) - floor($model->other_m_price * $model->payer_dol)) * 100, 0) ?> коп. - за каждый последующий месяц периода обучения по Договору</li>
    </ul>
    
        <?php 
        if ($model->cert_dol != 0) {
            echo "
            Заказчик осуществляет оплату ежемесячно не позднее 10-го числа месяца, следующего за месяцем оплаты в размере:
            <ul>
                <li>". floor($model->first_m_price * $model->cert_dol) ." руб. 
            ". round((($model->first_m_price * $model->cert_dol) - floor($model->first_m_price * $model->cert_dol)) * 100, 0) ." коп. - за первый месяц периода обучения по Договору</li>
                <li>". floor($model->other_m_price * $model->cert_dol) ." руб. 
            ". round((($model->other_m_price * $model->cert_dol) - floor($model->other_m_price * $model->cert_dol)) * 100, 0) ." коп. - за каждый последующий месяц периода обучения по Договору</li>
            </ul>
            ";
        }
        ?>
    
    Оплата за счет средств сертификата
       <?php 
        if ($model->cert_dol != 0) {
            echo " и Заказчика";
        }
    ?> 
    за месяц периода обучения по Договору осуществляется в полном объеме при условии, если по состоянию на первое число соответствующего месяца действие настоящего Договора не прекращено, независимо от фактического посещения Обучающимся занятий, предусмотренных учебным планом Программы в соответствующем месяце.
    
    <br><br>
    <?php // Html::a('Договор <span class="glyphicon glyphicon-download-alt"></span>', Url::to(['/contracts/mpdf', 'id' => $model->id]), ['class' => 'btn btn-primary']) ?>
    
    <div class="form-group">
       <?= Html::a('Отмена', Url::to(['/contracts/verificate', 'id' => $model->id]), ['class' => 'btn btn-danger']); ?>
        <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary']) ?>
    </div>
    
    <?php /* if (!$model->isNewRecord) {
        echo Html::a('Подтвердить заявку', Url::to(['/contracts/ok', 'id' => $model->id]), ['class' => 'btn btn-primary']); 
    } */ ?>  
    
       <?php ActiveForm::end(); ?>  

</div>
