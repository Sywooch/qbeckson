<?php


/* @var $this yii\web\View */
/* @var $model app\models\Mun */

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
            <td>
                <?php if (!$model->compareWithMunValue('nopc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('nopc') ?></s>
                    <span class="bg-success"><?= $model->nopc ?></span>
                <?php } else { ?>
                    <?= $model->nopc; ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('conopc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('conopc') ?></s>
                    <span class="bg-success"><?= $model->conopc ?></span>
                <?php } else { ?>
                    <?= $model->conopc ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Базовая потребность в приобретении услуг ПК и медицинских услуг</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('pc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('pc') ?></s>
                    <span class="bg-success"><?= $model->pc ?></span>
                <?php } else { ?>
                    <?= $model->pc ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('copc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('copc') ?></s>
                    <span class="bg-success"><?= $model->copc ?></span>
                <?php } else { ?>
                    <?= $model->copc ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Средняя заработная плата педагогических работников в месяц на
                    период</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('zp')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('zp') ?></s>
                    <span class="bg-success"><?= $model->zp ?></span>
                <?php } else { ?>
                    <?= $model->zp ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cozp')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cozp') ?></s>
                    <span class="bg-success"><?= $model->cozp ?></span>
                <?php } else { ?>
                    <?= $model->cozp ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент привлечения дополнительных педагогических работников</label>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('dop')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('dop') ?></s>
                    <span class="bg-success"><?= $model->dop ?></span>
                <?php } else { ?>
                    <?= $model->dop ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('codop')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('codop') ?></s>
                    <span class="bg-success"><?= $model->codop ?></span>
                <?php } else { ?>
                    <?= $model->codop ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент увеличения на прочий персонал</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('uvel')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('uvel') ?></s>
                    <span class="bg-success"><?= $model->uvel ?></span>
                <?php } else { ?>
                    <?= $model->uvel ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('couvel')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('couvel') ?></s>
                    <span class="bg-success"><?= $model->couvel ?></span>
                <?php } else { ?>
                    <?= $model->couvel ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отчислений по оплате труда</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('otch')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('otch') ?></s>
                    <span class="bg-success"><?= $model->otch ?></span>
                <?php } else { ?>
                    <?= $model->otch ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cootch')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cootch') ?></s>
                    <span class="bg-success"><?= $model->cootch ?></span>
                <?php } else { ?>
                    <?= $model->cootch ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Коэффициент отпускных</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('otpusk')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('otpusk') ?></s>
                    <span class="bg-success"><?= $model->otpusk ?></span>
                <?php } else { ?>
                    <?= $model->otpusk ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cootpusk')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cootpusk') ?></s>
                    <span class="bg-success"><?= $model->cootpusk ?></span>
                <?php } else { ?>
                    <?= $model->cootpusk ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Полезное использование помещений в неделю, часов</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('polezn')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('polezn') ?></s>
                    <span class="bg-success"><?= $model->polezn ?></span>
                <?php } else { ?>
                    <?= $model->polezn ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('copolezn')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('copolezn') ?></s>
                    <span class="bg-success"><?= $model->copolezn ?></span>
                <?php } else { ?>
                    <?= $model->copolezn ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Среднее количество ставок на одного педагога</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('stav')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('stav') ?></s>
                    <span class="bg-success"><?= $model->stav ?></span>
                <?php } else { ?>
                    <?= $model->stav ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('costav')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('costav') ?></s>
                    <span class="bg-success"><?= $model->costav ?></span>
                <?php } else { ?>
                    <?= $model->costav ?>
                <?php }?>
            </td>
        </tr>
        <tr class="active">
            <td><h4>Базовая стоимость восполнения комплекта средств обучения</h4></td>
            <td></td>
            <td></td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (робототехника)</label></td>
            <td><p></p>
                <?php if (!$model->compareWithMunValue('rob')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('rob') ?></s>
                    <span class="bg-success"><?= $model->rob ?></span>
                <?php } else { ?>
                    <?= $model->rob ?>
                <?php }?>
            </td>
            <td><p></p>
                <?php if (!$model->compareWithMunValue('corob')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('corob') ?></s>
                    <span class="bg-success"><?= $model->corob ?></span>
                <?php } else { ?>
                    <?= $model->corob ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Техническая (иная)</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('tex')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('tex') ?></s>
                    <span class="bg-success"><?= $model->tex ?></span>
                <?php } else { ?>
                    <?= $model->tex ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cotex')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cotex') ?></s>
                    <span class="bg-success"><?= $model->cotex ?></span>
                <?php } else { ?>
                    <?= $model->cotex ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Естественно-научная</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('est')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('est') ?></s>
                    <span class="bg-success"><?= $model->est ?></span>
                <?php } else { ?>
                    <?= $model->est ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('coest')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('coest') ?></s>
                    <span class="bg-success"><?= $model->coest ?></span>
                <?php } else { ?>
                    <?= $model->coest ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Физкультурно-спортивная</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('fiz')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('fiz') ?></s>
                    <span class="bg-success"><?= $model->fiz ?></span>
                <?php } else { ?>
                    <?= $model->fiz ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cofiz')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cofiz') ?></s>
                    <span class="bg-success"><?= $model->cofiz ?></span>
                <?php } else { ?>
                    <?= $model->cofiz ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Художественная</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('xud')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('xud') ?></s>
                    <span class="bg-success"><?= $model->xud ?></span>
                <?php } else { ?>
                    <?= $model->xud ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('coxud')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('coxud') ?></s>
                    <span class="bg-success"><?= $model->coxud ?></span>
                <?php } else { ?>
                    <?= $model->coxud ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Туристско-краеведческая</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('tur')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('tur') ?></s>
                    <span class="bg-success"><?= $model->tur ?></span>
                <?php } else { ?>
                    <?= $model->tur ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cotur')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cotur') ?></s>
                    <span class="bg-success"><?= $model->cotur ?></span>
                <?php } else { ?>
                    <?= $model->cotur ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Социально-педагогическая</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('soc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('soc') ?></s>
                    <span class="bg-success"><?= $model->soc ?></span>
                <?php } else { ?>
                    <?= $model->soc ?>
                <?php }?>
            </td>
            <td>
                <?php if (!$model->compareWithMunValue('cosoc')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('cosoc') ?></s>
                    <span class="bg-success"><?= $model->cosoc ?></span>
                <?php } else { ?>
                    <?= $model->cosoc ?>
                <?php }?>
            </td>
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
            <td>
                <?php if (!$model->compareWithMunValue('deystv')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('deystv') ?></s>
                    <span class="bg-success"><?= $model->deystv ?></span>
                <?php } else { ?>
                    <?= $model->deystv ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Число действовавших в предыдущем учебном году сертификатов дополнительного
                    образования</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('lastdeystv')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('lastdeystv') ?></s>
                    <span class="bg-success"><?= $model->lastdeystv ?></span>
                <?php } else { ?>
                    <?= $model->lastdeystv ?>
                <?php }?>
            </td>
        </tr>
        <tr>
            <td><label class="control-label">Общее число детей в возрасте от 5-ти до 18-ти лет, проживающее на
                    территории муниципального района (городского округа)</label></td>
            <td>
                <?php if (!$model->compareWithMunValue('countdet')) { ?>
                    <s class="bg-danger"><?= $model->getMunValue('countdet') ?></s>
                    <span class="bg-success"><?= $model->countdet ?></span>
                <?php } else { ?>
                    <?= $model->countdet ?>
                <?php } ?>
            </td>
        </tr>
        </tbody>
    </table>
</div>
<p>
    Пример:
</p>
<p>
    <s class="bg-danger">1234</s> &ndash; Старое значение.
</p>
<p>
    <span class="bg-success">5678</span> &ndash; Новое значение.
</p>
<br>
