<?php

/**
 * страница изменение запрета доступа к сайту
 *
 * @var View $this
 * @var SiteRestriction $siteRestriction
 */

use app\models\siteRestriction\SiteRestriction;
use app\models\siteRestriction\SiteRestrictionStatus;
use app\models\siteRestriction\SiteRestrictionType;
use kartik\helpers\Html;
use kartik\widgets\ActiveForm;
use yii\web\View;

$this->title = 'изменение запрета доступа к сайту';

?>

<?php $form = ActiveForm::begin(['id' => 'site-access-create-form']) ?>

<?= $form->field($siteRestriction, 'type')->dropDownList(SiteRestrictionType::getLabelList()) ?>
<?= $form->field($siteRestriction, 'message')->textInput() ?>
<?= $form->field($siteRestriction, 'status')->dropDownList(SiteRestrictionStatus::getLabelList()) ?>

<?= Html::submitButton('Изменить', ['class' => 'btn btn-success']) ?>

<?php $form->end() ?>
