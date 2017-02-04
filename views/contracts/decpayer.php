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

$this->title = 'Выберите плательщика';
  $this->params['breadcrumbs'][] = ['label' => 'Счета', 'url' => ['/personal/organization-invoices']];
  $this->params['breadcrumbs'][] = ['label' => 'Полнота оказанных услуг в декабре', 'url' => ['/groups/dec']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="completeness-update col-md-10 col-md-offset-1">
<h1><?= Html::encode($this->title) ?></h1>
<?php $form = ActiveForm::begin(); ?>

   <?php
    $cooperates = new Cooperate();
    $cooperate = $cooperates->getDecinvoiceCooperatePayers();
   ?>

    <?= $form->field($payers, 'payer_id')->dropDownList(ArrayHelper::map(app\models\Payers::find()->where(['id' => $cooperate])->all(), 'id', 'name'), ['id'=>'prog-id'])->label('Плательщик') ?>


    <div class="form-group">
       <?= Html::a('Назад', ['/groups/dec'], ['class' => 'btn btn-primary']) ?>
&nbsp;
        <?= Html::submitButton('Продолжить', ['class' => 'btn btn-primary']) ?>
    </div>

<?php ActiveForm::end(); ?>
</div>