<?php

/**
 * страница создания запрета доступа к сайту
 *
 * @var View $this
 * @var SiteRestriction $siteRestriction
 */

use app\models\siteRestriction\SiteRestriction;
use app\models\siteRestriction\SiteRestrictionStatus;
use app\models\siteRestriction\SiteRestrictionType;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

$this->title = 'Создать запрет доступа к сайту';

?>

<?php $form = ActiveForm::begin(['id' => 'site-restriction-create-form']) ?>

<?= $form->field($siteRestriction, 'type')->dropDownList(SiteRestrictionType::getLabelList()) ?>
<?= $form->field($siteRestriction, 'message')->textInput() ?>
<?= $form->field($siteRestriction, 'status')->dropDownList(SiteRestrictionStatus::getLabelList()) ?>

<?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>

<?php $form->end() ?>

