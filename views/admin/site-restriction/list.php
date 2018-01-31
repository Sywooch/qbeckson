<?php

/**
 * страница отображения запрета доступа к сайту
 *
 * @var View $this
 * @var SiteRestriction $siteRestriction
 * @var ActiveDataProvider $siteRestrictionDataProvider
 */

use app\models\siteRestriction\SiteRestriction;
use app\models\siteRestriction\SiteRestrictionStatus;
use app\models\siteRestriction\SiteRestrictionType;
use kartik\grid\ActionColumn;
use kartik\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;

$this->title = 'Запрет доступа к сайту';

?>

<?php if($siteRestriction): ?>
    <?= GridView::widget([
        'dataProvider' => $siteRestrictionDataProvider,
        'summary' => false,
        'columns' => [
            [
                'attribute' => 'type',
                'value' =>
                    function ($site_restriction) {
                    /** @var SiteRestriction $site_restriction */
                    return SiteRestrictionType::getLabel($site_restriction->type);
                }
            ],
            [
                'attribute' => 'message',
            ],
            [
                'attribute' => 'status',
                'value' =>
                    function ($site_restriction) {
                        /** @var SiteRestriction $site_restriction */
                        return SiteRestrictionStatus::getLabel($site_restriction->status);
                    }
            ],
            [
                'class' => ActionColumn::className(),
                'template' => '{update}'
            ]
        ]
    ]) ?>
<?php else: ?>
    <?= Html::a('Создать запрет', Url::to('/admin/site-restriction/create'), ['class' => 'btn btn-primary']) ?>
    <h1 class="text-center">Нет запретов доступа к сайту.</h1>
<?php endif; ?>
