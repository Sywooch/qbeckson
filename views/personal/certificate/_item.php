<?php
/* @var $this yii\web\View */
/* @var $model \app\models\Programs */

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

$fStrings['costFirstModule'] = Yii::t('app', 'Заявленная: {formattedValue}',
    ['formattedValue' => Yii::$app->formatter->asCurrency($model->getModules()->one()->price),]);

$fStrings['costFirstModuleNotmativ'] = Yii::t('app', 'Нормативная: {formattedValue}',
    ['formattedValue' => Yii::$app->formatter->asCurrency($model->getModules()->one()->normative_price),]);
?>
<div class="row">
    <div class="col-xs-12">
        <div class="card">
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
                            <div><?= $model->organization->name ?></div>
                            <div><?= $model->organization->address_legal ?></div>
                        </div>
                        <div class="card-info-paragraph card-info-paragraph_mh38">
                            <div>Стоимость одного модуля</div>
                            <div class="adaptive-ib"><?= $fStrings['costFirstModule'] ?></div>
                            <div class="adaptive-ib"><?= $fStrings['costFirstModuleNotmativ'] ?></div>
                        </div><?= \yii\helpers\Html::a('Просмотр', ['programs/view', 'id' => $model->id], ['class' => 'btn btn-theme btn-block']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>