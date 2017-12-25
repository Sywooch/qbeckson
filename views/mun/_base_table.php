<?php

/** @var $this yii\web\View */
/** @var $model app\models\Mun */

?>
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
            <td><?= $model->nopc; ?></td>
            <td><?= $model->conopc; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Базовая потребность в приобретении услуг ПК</label></td>
            <td><?= $model->pc; ?></td>
            <td><?= $model->copc; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Средняя заработная плата педагогических работников в месяц на
                    период</label></td>
            <td><?= $model->zp; ?></td>
            <td><?= $model->cozp; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент привлечения дополнительных педагогических работников</label>
            </td>
            <td><?= $model->dop; ?></td>
            <td><?= $model->codop; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент увеличения на прочий персонал</label></td>
            <td><?= $model->uvel; ?></td>
            <td><?= $model->couvel; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отчислений по оплате труда</label></td>
            <td><?= $model->otch; ?></td>
            <td><?= $model->cootch; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отпускных</label></td>
            <td><?= $model->otpusk; ?></td>
            <td><?= $model->cootpusk; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Полезное использование помещений в неделю, часов</label></td>
            <td><?= $model->polezn; ?></td>
            <td><?= $model->copolezn; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Среднее количество ставок на одного педагога</label></td>
            <td><?= $model->stav; ?></td>
            <td><?= $model->costav; ?></td>
        </tr>
        <tr class="active">
            <td><h4>Базовая стоимость восполнения комплекта средств обучения</h4></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (робототехника)</label></td>
            <td><p></p><?= $model->rob; ?></td>
            <td><p></p><?= $model->corob; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (иная)</label></td>
            <td><?= $model->tex; ?></td>
            <td><?= $model->cotex; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Естественно-научная</label></td>
            <td><?= $model->est; ?></td>
            <td><?= $model->coest; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Физкультурно-спортивная</label></td>
            <td><?= $model->fiz; ?></td>
            <td><?= $model->cofiz; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Художественная</label></td>
            <td><?= $model->xud; ?></td>
            <td><?= $model->coxud; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Туристско-краеведческая</label></td>
            <td><?= $model->tur; ?></td>
            <td><?= $model->cotur; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Социально-педагогическая</label></td>
            <td><?= $model->soc; ?></td>
            <td><?= $model->cosoc; ?></td>
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
            <td><?= $model->deystv; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Число действовавших в предыдущем учебном году сертификатов дополнительного
                    образования</label></td>
            <td><?= $model->lastdeystv; ?></td>
        </tr>
        <tr>
            <td><label class="control-label">Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на
                    территории муниципального района (городского округа)</label></td>
            <td><?= $model->countdet; ?></td>
        </tr>
        </tbody>
    </table>
</div>

