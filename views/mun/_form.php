<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Mun */
/* @var $form yii\widgets\ActiveForm */

$isPayer = Yii::$app->user->can('payer');
?>

<div class="mun-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => $isPayer]) ?>

    <div class="table-responsive">
        <table class="table  table-condensed">
            <thead>
                <tr>
                    <th></th>
                    <th>Городская местность</th>
                    <th>Сельская местность</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><label class="control-label">Базовая потребность в приобретении услуг (кроме ПК)</label></td>
                    <td><?= $form->field($model, 'nopc')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'conopc')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Базовая потребность в приобретении услуг ПК</label></td>
                    <td><?= $form->field($model, 'pc')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'copc')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Средняя заработная плата педагогических работников в месяц на период</label></td>
                    <td><?= $form->field($model, 'zp')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cozp')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент привлечения дополнительных педагогических работников</label></td>
                    <td><?= $form->field($model, 'dop')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'codop')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент увеличения на прочий персонал</label></td>
                    <td><?= $form->field($model, 'uvel')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'couvel')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент отчислений по оплате труда</label></td>
                    <td><?= $form->field($model, 'otch')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cootch')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент отпускных</label></td>
                    <td><?= $form->field($model, 'otpusk')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cootpusk')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Полезное использование помещений в неделю, часов</label></td>
                    <td><?= $form->field($model, 'polezn')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'copolezn')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Среднее количество ставок на одного педагога</label></td>
                    <td><?= $form->field($model, 'stav')->textInput()->label(false)?></td>
                    <td><?= $form->field($model, 'costav')->textInput()->label(false) ?></td>
                </tr>
                <tr class="active">
                    <td><h4>Базовая стоимость восполнения комплекта средств обучения</h4></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><label class="control-label">Техническая (робототехника)</label></td>
                    <td><p></p><?= $form->field($model, 'rob')->textInput()->label(false) ?></td>
                    <td><p></p><?= $form->field($model, 'corob')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Техническая (иная)</label></td>
                    <td><?= $form->field($model, 'tex')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cotex')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Естественно-научная</label></td>
                    <td><?= $form->field($model, 'est')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'coest')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Физкультурно-спортивная</label></td>
                    <td><?= $form->field($model, 'fiz')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cofiz')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Художественная</label></td>
                    <td><?= $form->field($model, 'xud')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'coxud')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Туристско-краеведческая</label></td>
                    <td><?= $form->field($model, 'tur')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cotur')->textInput()->label(false) ?></td>
                </tr>
                <tr>
                    <td><label class="control-label">Социально-педагогическая</label></td>
                    <td><?= $form->field($model, 'soc')->textInput()->label(false) ?></td>
                    <td><?= $form->field($model, 'cosoc')->textInput()->label(false) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <?php

    echo $form->field($model, 'deystv')->textInput();

    echo $form->field($model, 'lastdeystv')->textInput();

    echo $form->field($model, 'countdet')->textInput();

    if ($isPayer) {
        echo $form->field($model, 'confirmationFile')->widget(\trntv\filekit\widget\Upload::class, [
            'url' => ['file-storage/upload'],
            'maxFileSize' => 10 * 1024 * 1024,
            'acceptFileTypes' => new \yii\web\JsExpression('/(\.|\/)(pdf)$/i'),
        ]);
    }
    ?>

    <div class="form-group">
       <?= Html::a('Отмена',
           Url::to($isPayer ? ['/mun/view', 'id' => $model->id] : ['/mun/index']),
           ['class' => 'btn btn-danger']); ?>
     &nbsp;
        <?= Html::submitButton($isPayer ? 'Отправить заявку на изменение' : 'Сохранить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
