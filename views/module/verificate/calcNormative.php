<?php
/**
 * @var $this \yii\web\View
 * @var $model \app\models\ProgrammeModule
 */

use kartik\editable\Editable;
use yii\helpers\Html;
use yii\helpers\Url;

?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $data = [1 => 'Выше среднего', 2 => 'Средняя', 3 => 'Ниже среднего'];
            echo Html::label($model->getAttributeLabel('p21z') . ': ');
            echo Editable::widget([
                'model' => $model,
                'additionalData' => ['id' => $model->id],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => $data,
                'displayValueConfig' => $data,
                'format' => Editable::FORMAT_BUTTON,
                'attribute' => "p21z",
                'formOptions' => [
                    'action' => Url::to(['module/normpricesave']),
                ],
            ]);
            ?><?php
            echo Html::label($model->getAttributeLabel('p22z') . ': ');
            echo Editable::widget([
                'model' => $model,
                'additionalData' => ['id' => $model->id],
                'inputType' => Editable::INPUT_DROPDOWN_LIST,
                'data' => $data,
                'displayValueConfig' => $data,
                'format' => Editable::FORMAT_BUTTON,
                'attribute' => "p22z",
                'formOptions' => [
                    'action' => Url::to(['module/normpricesave']),
                ],
            ]);
            ?>
            <?php
            echo Html::label($model->getAttributeLabel('normative_price') . ': ');
            echo Editable::widget([
                'model' => $model,
                'additionalData' => ['id' => $model->id],
                'attribute' => "normative_price",
                'format' => Editable::FORMAT_BUTTON,
                'formOptions' => [
                    'action' => Url::to(['module/normpricesave']),
                ],
            ]);
            ?>
        </div>
    </div><!-- .row -->
<?php
echo Html::a('Назад', Url::to(['/module/view', 'id' => $model->id]), ['class' => 'btn btn-primary']);
echo '&nbsp;';
echo Html::a(
    'Пересчитать нормативную стоимость',
    Url::to(['/module/certificate-calc-normative', 'id' => $model->id]),
    [
        'class' => 'btn btn-primary',
        'data' => [
            'method' => 'post',
        ],
    ]
);
echo Html::a(
    'Cертифицировать',
    Url::to(['save', 'id' => $model->id]),
    ['class' => 'btn btn-primary', 'data' => ['method' => 'post']]
);
