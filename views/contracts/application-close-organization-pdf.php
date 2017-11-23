<?php

/* @var $this yii\web\View */
/* @var $model app\models\Contracts */
?>
<br />
<div class="certificates-view">
    <table>
        <tr>
            <td>&nbsp;</td>
            <td width="28%">
                <p>____________________________</p>
                <p>____________________________</p><br />
                <p>От <?= $model->organization->name ?></p>
                <p>в лице ____________________</p>
                <p>____________________________</p>
            </td>
        </tr>
    </table>

    <br />
    <h3 align="center">Уведомление</h3>

    <p>В соответствии с пунктом 6.4 Договора-оферты №<?= $model->number ?> от <?= Yii::$app->formatter->asDate($model->date) ?> уведомляем Вас, что вышеуказанный договор расторгается с <?= Yii::$app->formatter->asDate(strtotime('first day of next month', strtotime($model->termination_initiated_at))) ?> по причине:<br />
    <i><?= $model->status_comment ?>.</i>
    </p>

    <table>
        <tr>
            <td>
                <?= Yii::$app->formatter->asDate($model->termination_initiated_at) ?>
            </td>
            <td width="55%">
<pre style="border: 0; background-color: transparent;">
___________________/______________________ /
     подпись             расшифровка
</pre>
            </td>
        </tr>
    </table>
</div>
