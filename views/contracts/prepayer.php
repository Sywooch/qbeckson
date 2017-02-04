<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use app\models\Organization;
use app\models\Payers;
use app\models\Cooperate;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */

$date=explode(".", date("d.m.Y"));
            switch ($date[1]){
           case 1: $m='январь'; break;
            case 2: $m='февраль'; break;
            case 3: $m='март'; break;
            case 4: $m='апрель'; break;
            case 5: $m='май'; break;
            case 6: $m='июнь'; break;
            case 7: $m='июль'; break;
            case 8: $m='август'; break;
            case 9: $m='сентябрь'; break;
            case 10: $m='октябрь'; break;
            case 11: $m='ноябрь'; break;
            case 12: $m='декабрь'; break;
            }

$this->title = 'Выберите плательщика';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Авансировать за '.$m , 'url' => ['/groups/preinvoice']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="completeness-update col-md-10 col-md-offset-1">
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>

   <?php
    $cooperates = new Cooperate();
    $cooperate = $cooperates->getPreInvoiceCooperatePayers();
   ?>

    <?= $form->field($payers, 'payer_id')->dropDownList(ArrayHelper::map(app\models\Payers::find()->where(['id' => $cooperate])->all(), 'id', 'name'), ['id'=>'prog-id'])->label('Плательщик') ?>


    <div class="form-group">
       <?= Html::a('Назад', ['/groups/preinvoice'], ['class' => 'btn btn-primary']) ?>
&nbsp;
        <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>