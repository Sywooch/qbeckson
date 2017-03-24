<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\models\Organization;
use app\models\Programs;
use app\models\ProgrammeModule;
use kartik\widgets\DepDrop;
use kartik\slider\Slider;

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */

$program = Programs::findOne($model->program_id);
$year = ProgrammeModule::findOne($model->year_id);

$this->title = 'Оценить программу: ' . $program->name;
$this->params['breadcrumbs'][] = ['label' => 'Договоры', 'url' => ['/personal/certificate-contracts']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="contracts-create col-md-offset-1 col-md-10">

    <h2><?= Html::encode($this->title) ?></h2>

    <?php $form = ActiveForm::begin(); ?>

    <div class="well">
        <p>Организацией, реализующей программу, заявлены следующие ее цели и задачи:
            <br/>
            <br/>
            <strong>Цели и задачи:</strong> <?= Html::encode($program->task) ?>.
            <br/>
            <br/>
            <?php if (!empty($model->year->results)): ?>
                <strong>Ожидаемые результаты освоения модуля:</strong> <?= Html::encode($model->year->results) ?>.
                <br/>
                <br/>
            <?php endif; ?>
            Оцените, пожалуйста, насколько, по Вашему мнению организация стремится при реализации программы достигнуть указанные цели, задачи и ожидаемые результаты.
        </p>
        <br>
        <br>
        <div class="text-center">
            <?= $form->field($model, 'ocen_fact')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_INFO,
                'pluginOptions' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'tooltip' => 'always',
                    'formatter' => new yii\web\JsExpression("function(val) { 
                return 'на '+val+'%'; 
        }")
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="well">
        <p>Организацией, реализующей программу, заявлено, что она обеспечит привлечение для реализации программы
            педагогических работников, обладающих следующей квалификацией:
            <br><br>
            <?php
            if ($year->hoursdop == 0) {
                echo "<strong>Квалификация педагога:</strong> " . $year->kvfirst . '<br><br>';
            } else {
                echo "<strong>Квалификация основного педагога:</strong> " . $year->kvfirst . '<br>';
                echo "<strong>Квалификация дополнительного педагога:</strong> " . $year->kvdop . '<br><br>';
            }

            ?>

            Кроме того, организация взяла на себя обязательство реализации программы в группе с максимальной
            наполняемостью <?= $year->maxchild ?> человек(а).
            <br><br>
            Оцените, пожалуйста, насколько, по Вашему мнению организация выполняет указанные обязательства при
            реализации программы.
        </p>
        <br>
        <br>
        <div class="text-center">
            <?= $form->field($model, 'ocen_kadr')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_INFO,
                'pluginOptions' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'tooltip' => 'always',
                    'formatter' => new yii\web\JsExpression("function(val) { 
                return 'на '+val+'%';
        }")
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="well">
        <p>
            Организацией, реализующей программу, заявлено, что она обеспечит использование при реализации программы
            следующих средств обучения, а также создание следующих <strong>материально-технических условий:</strong>
            <br>
            <br>
            <?= $program->norm_providing ?>
            <br>
            <br>
            Оцените, пожалуйста, насколько, по Вашему мнению организация выполняет указанные обязательства при
            реализации программы.
        </p>
        <br>
        <br>
        <div class="text-center">
            <?= $form->field($model, 'ocen_mat')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_INFO,
                'pluginOptions' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'tooltip' => 'always',
                    'formatter' => new yii\web\JsExpression("function(val) { 
                return 'на '+val+'%';
        }")
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="well">
        <p>В конечном итоге, у Вас есть свои критерии оценки удовлетворенности теми или иными программами и Вы сами
            понимаете, что для Вас важно при получении обучения. Оцените, пожалуйста, насколько, Вы в целом
            удовлетворены программой.</p>
        <br>
        <br>
        <div class="text-center">
            <?= $form->field($model, 'ocen_obch')->widget(Slider::classname(), [
                'sliderColor' => Slider::TYPE_INFO,
                'pluginOptions' => [
                    'min' => 0,
                    'max' => 100,
                    'step' => 10,
                    'tooltip' => 'always',
                    'formatter' => new yii\web\JsExpression("function(val) { 
                return 'на '+val+'%';
        }")
                ]
            ])->label(false) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::a('Отменить', Url::to(['/contracts/view', 'id' => $model->id]), ['class' => 'btn btn-danger']); ?>
        &nbsp;
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
