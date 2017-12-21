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
            switch ($date[1] != 12 ? $date[1] - 1 : $date[1]){
            case 1: $m='январе'; break;
            case 2: $m='феврале'; break;
            case 3: $m='марте'; break;
            case 4: $m='апреле'; break;
            case 5: $m='мае'; break;
            case 6: $m='июне'; break;
            case 7: $m='июле'; break;
            case 8: $m='августе'; break;
            case 9: $m='сентябре'; break;
            case 10: $m='октябре'; break;
            case 11: $m='ноябре'; break;
            case 12: $m='декабре'; break;
            }

$this->title = 'Выберите плательщика';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в '.$m , 'url' => ['/groups/invoice']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="completeness-update col-md-10 col-md-offset-1">
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>

   <?php
    $cooperates = new Cooperate();
    $cooperate = $cooperates->getInvoiceCooperatePayers();
   ?>

    <?= $form->field($payers, 'payer_id')->dropDownList(ArrayHelper::map(app\models\Payers::find()->where(['id' => $cooperate])->all(), 'id', 'name'), ['id'=>'prog-id'])->label('Плательщик') ?>


    <div class="form-group">
       <?= Html::a('Назад', ['/groups/invoice'], ['class' => 'btn btn-primary']) ?>
&nbsp;
        <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>