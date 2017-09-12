<?php
/* @var $this yii\web\View */

/* @var $model \app\models\Programs */

use app\models\Cooperate;
use yii\helpers\Html;
use yii\helpers\Url;

if (!$photo = $model->getPhoto()) {
    $photo = $this->getAssetManager()->getAssetUrl($this->assetBundles[\app\assets\programsAsset\ProgramsAsset::className()],
        $model->defaultPhoto);
}
$fStrings = [];
$fStrings['ageGroupShort'] = Yii::t('app', '{min}-{max} лет',
    ['min' => $model->age_group_min, 'max' => $model->age_group_max]);
$fStrings['ageGroupFull'] = Yii::t('app', 'Рекомендуемый возраст с {min} до {max} лет',
    ['min' => $model->age_group_min, 'max' => $model->age_group_max]);
if ($model->zab && mb_strlen($model->zab) > 0) {
    $fStrings['zabShort'] = 'С' . PHP_EOL . 'ОВЗ';
    $fStrings['zabFull'] = $model->zabAsString;
} else {
    $fStrings['zabShort'] = 'Без' . PHP_EOL . 'ОВЗ';
    $fStrings['zabFull'] = 'Не предусмотрено обучение учащихся с ОВЗ';
}
$fStrings['rateFull'] = Yii::t('app', 'Рейтинг программы: {rating}%',
    ['rating' => Yii::$app->formatter->asInteger($model->rating)]);

$fStrings['rateShort'] = Yii::t('app', '{rating}',
    ['rating' => Yii::$app->formatter->asInteger($model->rating)]);

$fStrings['costFirstModule'] = Yii::t('app', 'Заявленная: {formattedValue}*',
    ['formattedValue' => Yii::$app->formatter->asCurrency($model->getModules()->one()->price),]);

$fStrings['costFirstModuleNotmativ'] = Yii::t('app', 'Нормативная: {formattedValue}*',
    ['formattedValue' => Yii::$app->formatter->asCurrency($model->getModules()->one()->normative_price),]);
/** @var $certificate \app\models\Certificates */
$certificate = Yii::$app->user->identity->certificate;

/* вычисление доступности записи хотя бы на один модуль программы */
$available = $model->getGroups()->exists()  //есть группы
    && $model->open                                     //открыт набор в программу
    && Cooperate::find()->where([
        Cooperate::tableName() . '.payer_id'        => $user->getCertificate()->select('payer_id'),
        Cooperate::tableName() . '.organization_id' => $model->organization_id])->exists()   //есть соглашение с уполномоченой организацией
    && $model->getModules()->andWhere([\app\models\ProgrammeModule::tableName() . '.open' => 1])->exists()  //есть модули с открытым зачислением
    && (!(($certificate->balance < 1 && $certificate->payer->certificate_can_use_future_balance < 1) || ($certificate->balance < 1 && $certificate->payer->certificate_can_use_future_balance > 0 && $certificate->balance_f < 1)))  // есть средства на счету сертификата
    && $model->organization->actual // Организация программы действует
    && ($certificate->payer->getActiveContractsByProgram($model->id)->count()
        >= $certificate->payer->getDirectionalityCountByName($model->directivity))// Не достигнут максимальный предел числа одновременно оплачиваемых вашей уполномоченной организацией услуг по данной направленности
    && $model->organization->existsFreePlace() //Есть место в организации
    && $model->existsFreePlace() //В программе есть место
    && !$certificate->getActiveContractsByProgram($model->id)->exists() //Нет заключенных договоров на программу
;
?>
<div class="col-xs-12 col-lg-6">
    <div class="card" <?= ($available ? '' : 'style="background-color: bisque;" 
      data-placement="top" data-toggle="tooltip"
                             title="Вы не можете записаться на данную программу по ряду причин, нажмите \'подробнее\' что бы уточнить."') ?> >
        <div class="row">
            <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
                <div class="program-img socped"><img src="<?= $photo ?>"/></div>
            </div>
            <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
                <h2 class="card-title js-ellipsis-title"><?= $model->name ?></h2>
                <div class="card-badges">
                    <div class="card-badges-item card-badges-item_violet" title="<?= $model->direction->name ?>">
                        <span class="large-size <?= $model->iconClass ?>"></span></div>
                    <div class="card-badges-item card-badges-item_green" title="<?= $fStrings['ageGroupFull'] ?>">
                        <span><?= $fStrings['ageGroupShort'] ?></span></div>
                    <div class="card-badges-item card-badges-item_blue" title="<?= $fStrings['zabFull'] ?>">
                        <span><?= $fStrings['zabShort'] ?></span></div>
                    <?php if ($model->open): ?>
                        <div class="card-badges-item card-badges-item_green"
                             title="Зачисление открыто, есть свободные места"><span
                                    class="large-size icon-study"></span></div>
                    <?php endif; ?>
                    <?php if ($model->rating): ?>

                        <div class="card-badges-item card-badges-item_star" title="<?= $fStrings['rateFull'] ?>"><i
                                    class="icon-star-full"></i><span
                                    class="big-size"><?= $fStrings['rateShort'] ?></span></div>
                    <?php endif; ?>
                </div>
                <div class="card-info">
                    <div class="card-info-paragraph card-info-paragraph_mh50">
                        <div><?= Html::a($model->organization->name, Url::to(['/organization/view',
                                'id' => $model->organization->id]),
                                ['target' => '_blank']); ?></div>
                        <div><?= ($model->mainAddress ? $model->mainAddress->address : $model->organization->address_legal) ?></div>
                    </div>
                    <div class="card-info-paragraph card-info-paragraph_mh38">
                        <div>Стоимость одного модуля</div>
                        <div class="adaptive-ib" data-placement="top" data-toggle="tooltip"
                             title="Стоимость модуля и нормативная стоимость модуля (НС) для программ с несколькими модулями указаны в таблице для первого модуля.">
                            <?= $fStrings['costFirstModule'] ?>
                        </div>
                        <div class="adaptive-ib" data-placement="top" data-toggle="tooltip"
                             title="Стоимость модуля и нормативная стоимость модуля (НС) для программ с несколькими модулями указаны в таблице для первого модуля."><?= $fStrings['costFirstModuleNotmativ'] ?></div>
                    </div><?= \yii\helpers\Html::a('Просмотр', ['programs/view', 'id' => $model->id], ['class' => 'btn btn-theme btn-block']) ?>
                </div>
            </div>
        </div>
    </div>
</div>
