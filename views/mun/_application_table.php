<?php


/* @var $this yii\web\View */
/* @var $model app\models\Mun */

?>
<div class="table-responsive">
    <table class="table  table-condensed">
        <thead>
        <tr>
            <th></th>
            <th colspan="2">Городская местность</th>
            <th colspan="2">Сельская местность</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><label class="control-label">Базовая потребность в приобретении услуг (кроме ПК)</label></td>
            <td class="bg-success text-success"><?= $model->nopc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('nopc') ?></td>
            <td class="bg-success text-success"><?= $model->conopc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('conopc') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Базовая потребность в приобретении услуг ПК</label></td>
            <td class="bg-success text-success"><?= $model->pc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('pc') ?></td>
            <td class="bg-success text-success"><?= $model->copc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('copc') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Средняя заработная плата педагогических работников в месяц на
                    период</label></td>
            <td class="bg-success text-success"><?= $model->zp; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('zp') ?></td>
            <td class="bg-success text-success"><?= $model->cozp; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cozp') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент привлечения дополнительных педагогических работников</label>
            </td>
            <td class="bg-success text-success"><?= $model->dop; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('dop') ?></td>
            <td class="bg-success text-success"><?= $model->codop; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('codop') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент увеличения на прочий персонал</label></td>
            <td class="bg-success text-success"><?= $model->uvel; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('uvel') ?></td>
            <td class="bg-success text-success"><?= $model->couvel; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('couvel') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отчислений по оплате труда</label></td>
            <td class="bg-success text-success"><?= $model->otch; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('otch') ?></td>
            <td class="bg-success text-success"><?= $model->cootch; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cootch') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отпускных</label></td>
            <td class="bg-success text-success"><?= $model->otpusk; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('otpusk') ?></td>
            <td class="bg-success text-success"><?= $model->cootpusk; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cootpusk') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Полезное использование помещений в неделю, часов</label></td>
            <td class="bg-success text-success"><?= $model->polezn; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('polezn') ?></td>
            <td class="bg-success text-success"><?= $model->copolezn; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('copolezn') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Среднее количество ставок на одного педагога</label></td>
            <td class="bg-success text-success"><?= $model->stav; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('stav') ?></td>
            <td class="bg-success text-success"><?= $model->costav; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('costav') ?></td>
        </tr>
        <tr class="active">
            <td><h4>Базовая стоимость восполнения комплекта средств обучения</h4></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (робототехника)</label></td>
            <td class="bg-success text-success"><p></p><?= $model->rob; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('rob') ?></td>
            <td class="bg-success text-success"><p></p><?= $model->corob; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('corob') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (иная)</label></td>
            <td class="bg-success text-success"><?= $model->tex; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('tex') ?></td>
            <td class="bg-success text-success"><?= $model->cotex; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cotex') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Естественно-научная</label></td>
            <td class="bg-success text-success"><?= $model->est; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('est') ?></td>
            <td class="bg-success text-success"><?= $model->coest; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('coest') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Физкультурно-спортивная</label></td>
            <td class="bg-success text-success"><?= $model->fiz; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('fiz') ?></td>
            <td class="bg-success text-success"><?= $model->cofiz; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cofiz') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Художественная</label></td>
            <td class="bg-success text-success"><?= $model->xud; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('xud') ?></td>
            <td class="bg-success text-success"><?= $model->coxud; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('coxud') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Туристско-краеведческая</label></td>
            <td class="bg-success text-success"><?= $model->tur; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('tur') ?></td>
            <td class="bg-success text-success"><?= $model->cotur; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cotur') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Социально-педагогическая</label></td>
            <td class="bg-success text-success"><?= $model->soc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('soc') ?></td>
            <td class="bg-success text-success"><?= $model->cosoc; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('cosoc') ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="table-responsive">
    <table class="table  table-condensed">
        <tbody>
        <tr>
            <td><label class="control-label">Число действующих в очередном учебном году сертификатов дополнительного
                    образования</label></td>
            <td class="bg-success text-success"><?= $model->deystv; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('deystv') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Число действовавших в предыдущем учебном году сертификатов дополнительного
                    образования</label></td>
            <td class="bg-success text-success"><?= $model->lastdeystv; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('lastdeystv') ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на
                    территории муниципального района (городского округа)</label></td>
            <td class="bg-success text-success"><?= $model->countdet; ?></td>
            <td class="bg-danger text-danger"><?= $model->getMunValue('countdet') ?></td>
        </tr>
        </tbody>
    </table>
</div>
<div class="row">
    <div class="col-sm-2">
        <div class="table-responsive">
            <table class="table  table-condensed">
                <tbody>
                <tr>
                    <th colspan="2">Примеры:</th>
                </tr>
                <tr>
                    <td class="bg-success text-success">123</td>
                    <td>Новое значение</td>
                </tr>
                <tr>
                    <td class="bg-danger text-danger">456</td>
                    <td>Старое значение</td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
