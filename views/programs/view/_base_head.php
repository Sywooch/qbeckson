<?php
/* @var $this yii\web\View */
/* @var $model app\models\Programs */

use yii\helpers\Html;
use yii\helpers\Url;

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

$JS = <<<JS
 $('.js-ellipsis-title').dotdotdot({
          ellipsis: '... ',
          wrap: 'word',
          height: 60
      });
 const collapse = $('#prog-detail-1');
 const moreButton = $('#more-button');
 
 collapse.on('show.bs.collapse', function() {
   moreButton.text('Скрыть подробную информацию');
   moreButton.fadeTo( "slow", 0.6 );
 });
 collapse.on('hide.bs.collapse', function() {
   moreButton.text('Подробнее');
   moreButton.fadeTo( "slow", 1 );
 });
 
JS;

if (!$photo = $model->getPhoto()) {
    $photo = $this->getAssetManager()->getAssetUrl($this->assetBundles[\app\assets\programsAsset\ProgramsAsset::className()],
        $model->defaultPhoto);
}

$this->registerJs($JS, $this::POS_READY);
?>
<div class="panel">
    <div class="row">
        <div class="col-xs-12 col-sm-4 col-md-4 col-lg-3">
            <div class="program-img socped"><img src="<?= $photo ?>"/></div>
        </div>
        <div class="col-xs-12 col-sm-8 col-md-8 col-lg-9">
            <h2 class="card-title js-ellipsis-title"><?= $model->name ?></h2>
            <div class="card-badges">
                <div class="card-badges-item card-badges-item_violet" title="<?= $model->direction->name ?>"><span
                            class="large-size <?= $model->iconClass ?>"></span></div>
                <div class="card-badges-item card-badges-item_green" title="<?= $fStrings['ageGroupFull'] ?>">
                    <span><?= $fStrings['ageGroupShort'] ?></span>
                </div>
                <div class="card-badges-item card-badges-item_blue" title="<?= $fStrings['zabFull'] ?>">
                    <span><?= $fStrings['zabShort'] ?></span>
                </div>
                <?php if ($model->verification === \app\models\Programs::VERIFICATION_DONE && $model->rating): ?>
                    <div class="card-badges-item card-badges-item_star" title="<?= $fStrings['rateFull'] ?>"><i
                                class="icon-star-full"></i><span class="big-size"><?= $fStrings['rateShort'] ?></span>
                    </div>
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
                </div>
                <a id="more-button" class="btn btn-theme" href="#" data-toggle="collapse"
                   data-target="#prog-detail-1">Подробнее</a>
            </div>
        </div>
        <div class="col-xs-12">
            <div class="collapse pt-18 program-info-view" id="prog-detail-1">
                <?= \yii\widgets\DetailView::widget([
                    'options'    => [
                        'tag'   => 'ul',
                        'class' => 'text-info-lines'],
                    'template'   => '<li><strong>{label}:</strong>{value}</li>',
                    'model'      => $model,
                    'attributes' => [
                        'direction.name',
                        ['label' => 'Возраст детей',
                         'value' => $model->age_group_min . ' - ' . $model->age_group_max],
                        'zabAsString',
                        'limit',
                        ['label' => 'Число обучающихся',
                         'value' => $model->GetActiveContracts()->count(),],
                        ['label' => 'Число модулей',
                         'value' => $model->getModules()->count()],
                        [
                            'label'     => 'Общая продолжительность (часов)',
                            'attribute' => 'countHours',
                        ],
                        [
                            'label'     => 'Общая продолжительность (месяцев)',
                            'attribute' => 'countMonths',
                        ],
                        'municipality.name',
                        'groundName',
                    ]
                ]) ?>
                <div class="strong">Цели и задачи</div>
                <p class="text-justify"><?= $model->task; ?></p>
                <div class="strong">Аннотация программы</div>
                <p class="text-justify"><?= $model->annotation; ?></p>
                <div class="strong">Нормы оснащения</div>
                <p class="text-justify"><?= $model->norm_providing ?></p>
            </div>
        </div>
    </div>
</div>