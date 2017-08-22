<?php

/* @var $this yii\web\View */
/* @var $model app\models\Certificates */
?>
<br />
<div class="certificates-view">
    <table>
        <tr>
            <td>&nbsp;</td>
            <td width="28%">
                <p><?= $model->organization->name ?></p><br />
                <p>От _________________________</p>
                <p>____________________________</p>
            </td>
        </tr>
    </table>

    <br />
    <h3 align="center">Уведомление</h3>

    <p>В соответствии с пунктом 6.5 Договора-оферты №<?= $model->number ?> от <?= Yii::$app->formatter->asDate($model->date) ?> я расторгаю вышеуказанный договор с <?= Yii::$app->formatter->asDate(strtotime('first day of next month', strtotime($model->date_initiate_termination))) ?>.</p>

    <table>
        <tr>
            <td>
                <?= Yii::$app->formatter->asDate($model->date_initiate_termination) ?>
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
