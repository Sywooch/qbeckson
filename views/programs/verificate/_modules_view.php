<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\Programs
 */

use app\components\widgets\ButtonWithInfo;
use yii\widgets\DetailView;

$items = array_map(
    function (\app\models\ProgrammeModule $module) {
        $table = DetailView::widget([
            'model' => $module,
            'attributes' => [
                'name',
                'month',
                'hours',
                'kvfirst',
                'hoursindivid',
                'hoursdop',
                'kvdop',
                'minchild',
                'maxchild',
                'results:ntext',
            ],
        ]);

        if ($module->needCertificate()) {
            $header =
                \yii\helpers\Html::tag(
                    'h4',
                    $module->year . ' модуль',
                    ['class' => ['pull-left']]
                );
            $button =
                ButtonWithInfo::widget([
                    'tagName' => 'a',
                    'label' => 'Верифицировать',
                    'options' => [
                        'class' => ['btn', 'btn-warning', 'pull-right'],
                        'href' => \yii\helpers\Url::to(['module/certificate-calc-normative', 'id' => $module->id])],
                    'message' => 'Произвести отдельную верификацю данного модуля, не затрагивая всю программу.'
                ]);
            $clearFix = \yii\helpers\Html::tag('div', '', ['class' => 'clearfix']);
            $label = $header . $button . $clearFix;
            $panelClass = 'panel-danger';
        } else {
            $label = \yii\helpers\Html::tag('h4', $module->year . ' модуль');
            $panelClass = 'panel-default';
        }

        return ['label' => $label, 'content' => $table, 'panelClass' => $panelClass];
    },
    $model->modules
);
foreach ($items as $item):
    ?>
    <div class="panel <?= $item['panelClass'] ?>">
        <div class="panel-heading">
            <?= $item['label'] ?>
        </div>
        <div class="panel-body">
            <?= $item['content'] ?>
        </div>
    </div>
<?php
endforeach;
?>


