<?php

use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Mun */

$isOperator = Yii::$app->user->can('operators');
$isApplication = $model->type === $model::TYPE_APPLICATION;
$this->title = $model->name;
$this->params['breadcrumbs'][] = [
    'label' => Yii::t('app', 'Муниципалитеты'),
    'url' => $isOperator ? ['index'] : null
];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mun-view col-md-offset-1 col-md-10">

    <h1>
        <?php if($isApplication) { ?>
            <small>Заявка на изменение муниципалитета</small>
            <br>
        <?php } ?>
        <?= Html::encode($this->title) ?>
    </h1>
    <?php if ($isApplication) { ?>
        <p class="text-right"><strong>После слеша указано текущее занчение, если оно отличается от
                предлагаемого.</strong></p>
    <?php } ?>
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
                    <td><?= $model->nopc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('nopc')) { ?>
                            / <?= $model->getMunValue('nopc') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->conopc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('conopc')) { ?>
                            / <?= $model->getMunValue('conopc') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Базовая потребность в приобретении услуг ПК</label></td>
                    <td><?= $model->pc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('pc')) { ?>
                            / <?= $model->getMunValue('pc') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->copc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('copc')) { ?>
                            / <?= $model->getMunValue('copc') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Средняя заработная плата педагогических работников в месяц на период</label></td>
                    <td><?= $model->zp; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('zp')) { ?>
                            / <?= $model->getMunValue('zp') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cozp; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cozp')) { ?>
                            / <?= $model->getMunValue('cozp') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент привлечения дополнительных педагогических работников</label></td>
                    <td><?= $model->dop; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('dop')) { ?>
                            / <?= $model->getMunValue('dop') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->codop; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('codop')) { ?>
                            / <?= $model->getMunValue('codop') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент увеличения на прочий персонал</label></td>
                    <td><?= $model->uvel; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('uvel')) { ?>
                            / <?= $model->getMunValue('uvel') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->couvel; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('couvel')) { ?>
                            / <?= $model->getMunValue('couvel') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент отчислений по оплате труда</label></td>
                    <td><?= $model->otch; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('otch')) { ?>
                            / <?= $model->getMunValue('otch') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cootch; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cootch')) { ?>
                            / <?= $model->getMunValue('cootch') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Коэффициент отпускных</label></td>
                    <td><?= $model->otpusk; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('otpusk')) { ?>
                            / <?= $model->getMunValue('otpusk') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cootpusk; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cootpusk')) { ?>
                            / <?= $model->getMunValue('cootpusk') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Полезное использование помещений в неделю, часов</label></td>
                    <td><?= $model->polezn; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('polezn')) { ?>
                            / <?= $model->getMunValue('polezn') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->copolezn; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('copolezn')) { ?>
                            / <?= $model->getMunValue('copolezn') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Среднее количество ставок на одного педагога</label></td>
                    <td><?= $model->stav; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('stav')) { ?>
                            / <?= $model->getMunValue('stav') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->costav; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('costav')) { ?>
                            / <?= $model->getMunValue('costav') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr class="active">
                    <td><h4>Базовая стоимость восполнения комплекта средств обучения</h4></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td><label class="control-label">Техническая (робототехника)</label></td>
                    <td><p></p><?= $model->rob; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('rob')) { ?>
                            / <?= $model->getMunValue('rob') ?>
                        <?php } ?>
                    </td>
                    <td><p></p><?= $model->corob; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('corob')) { ?>
                            / <?= $model->getMunValue('corob') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Техническая (иная)</label></td>
                    <td><?= $model->tex; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('tex')) { ?>
                            / <?= $model->getMunValue('tex') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cotex; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cotex')) { ?>
                            / <?= $model->getMunValue('cotex') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Естественно-научная</label></td>
                    <td><?= $model->est; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('est')) { ?>
                            / <?= $model->getMunValue('est') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->coest; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('coest')) { ?>
                            / <?= $model->getMunValue('coest') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Физкультурно-спортивная</label></td>
                    <td><?= $model->fiz; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('fiz')) { ?>
                            / <?= $model->getMunValue('fiz') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cofiz; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cofiz')) { ?>
                            / <?= $model->getMunValue('cofiz') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Художественная</label></td>
                    <td><?= $model->xud; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('xud')) { ?>
                            / <?= $model->getMunValue('xud') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->coxud; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('coxud')) { ?>
                            / <?= $model->getMunValue('coxud') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Туристско-краеведческая</label></td>
                    <td><?= $model->tur; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('tur')) { ?>
                            / <?= $model->getMunValue('tur') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cotur; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cotur')) { ?>
                            / <?= $model->getMunValue('cotur') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Социально-педагогическая</label></td>
                    <td><?= $model->soc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('soc')) { ?>
                            / <?= $model->getMunValue('soc') ?>
                        <?php } ?>
                    </td>
                    <td><?= $model->cosoc; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('cosoc')) { ?>
                            / <?= $model->getMunValue('cosoc') ?>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
        <div class="table-responsive">
        <table class="table  table-condensed">
            <tbody>
                <tr>
                    <td><label class="control-label">Число действующих в очередном учебном году сертификатов дополнительного образования</label></td>
                    <td><?= $model->deystv; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('deystv')) { ?>
                            / <?= $model->getMunValue('deystv') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Число действовавших в предыдущем учебном году сертификатов дополнительного образования</label></td>
                    <td><?= $model->lastdeystv; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('lastdeystv')) { ?>
                            / <?= $model->getMunValue('lastdeystv') ?>
                        <?php } ?>
                    </td>
                </tr>
                <tr>
                    <td><label class="control-label">Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на территории муниципального района (городского округа)</label></td>
                    <td><?= $model->countdet; ?>
                        <?php if ($isApplication && !$model->compareWithMunValue('countdet')) { ?>
                            / <?= $model->getMunValue('countdet') ?>
                        <?php } ?>
                    </td>
                </tr>
            </tbody>
        </table>

            <?php if ($model->file) {
                $fileTag = Html::tag('span', '', ['class' => 'glyphicon glyphicon-download-alt']);
                $link = Html::a($fileTag . ' Файл-подтвержение', $model->getFileUrl());
                echo Html::tag('h4', $link);
            } ?>

    </div>

    <?php if ($isOperator) { ?>
        <?= Html::a('Назад', Url::to(['/mun/index']), ['class' => 'btn btn-primary']); ?>
    <?php } ?>

    <?php if ($isApplication) { ?>
        <?php if ($isOperator) { ?>
            <?= Html::a('Одобрить', Url::to(['/mun/confirm', 'id' => $model->id]),
                ['class' => 'btn btn-success']); ?>
            <?= Html::a('Отказать', Url::to(['/mun/reject', 'id' => $model->id]),
                ['class' => 'btn btn-danger']); ?>
        <?php } elseif ($model->user_id == Yii::$app->user->id) { ?>
            <?= Html::a('Редактировать', Url::to(['/mun/update', 'id' => $model->mun_id]),
                ['class' => 'btn btn-primary']); ?>
        <?php } ?>
    <?php } else { ?>
        <?= Html::a('Редактировать', Url::to(['/mun/update', 'id' => $model->id]),
            ['class' => 'btn btn-primary']); ?>
        <?php if ($isOperator) { ?>
            <?= Html::a('Удалить', Url::to(['/mun/delete', 'id' => $model->id]), [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Вы уверены что хотите удалить этот муниципалитет?',
                    'method' => 'post'
                ]
            ]); ?>

        <?php } ?>
    <?php } ?>

</div>
