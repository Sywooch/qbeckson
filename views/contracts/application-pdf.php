<?php

/* @var $this yii\web\View */
/* @var $model \app\models\Contracts */
?>
<br />
<div class="certificates-view" style="font-size: 11px;">
    <table>
        <tr>
            <td>&nbsp;</td>
            <td width="28%">
                <p><?= $model->organization->name ?></p><br />
                <p>От _______________________________</p>
                <p>_______________________________</p>
            </td>
        </tr>
    </table>

    <br />
    <h3 align="center" style="font-size: 16px;">Заявление о зачислении</h3>

    <p>Прошу оказать образовательную услугу по реализации  дополнительной общеобразовательной программы
        <?= app\helpers\FormattingHelper::directivityForm($model->program->directivity); ?> направленности "<?= $model->program->name ?>" модуля: "<?= $model->module->year ?>" (далее – Программа)
        Обучающемуся, сведения о котором указаны ниже, в соответствии с договором-офертой №<?= $model->number ?> (с <?= \Yii::$app->formatter->asDate($model->start_edu_contract) ?> по <?= \Yii::$app->formatter->asDate($model->stop_edu_contract) ?>),
        а также прочими договорами-офертами, предлагаемыми мне к заключению, предусматривающими оказание услуг по реализации иных частей Программы, выставляемыми Вами (при необходимости).</p>


    <p>Сведения о Заказчике:</p>
    <p>Фамилия, имя и отчество Заказчика _________________________________________________________________________________________________</p>
    <p>Телефон Заказчика ____________________________</p>
    <p>Место жительства Заказчика ________________________________________________________________________________________________________</p>
    <p>________________________________________________________________________________________________________________________________________</p>
    <p>Сведения об Обучающемся:</p>
    <p>Фамилия, имя и отчество Обучающегося ____________________________________________________________________________________________</p>
    <p>Телефон Обучающегося _________________________</p>
    <p>Место жительства Обучающегося ___________________________________________________________________________________________________</p>
    <p>________________________________________________________________________________________________________________________________________</p>

    <p>Я ознакомлен с условиями договора-оферты №<?= $model->number ?>, представленной в сети Интернет по адресу <?= $model->fullUrl ?> и полностью и безоговорочно принимаю их.
        Обязуюсь самостоятельно отслеживать в личном кабинете сертификата «<?= $model->certificate->number ?>» информационной системы «ПФДО» предложения (оферты)
        к заключению договоров-оферт, предусматривающих оказание услуг по реализации иных частей Программы, выставляемые Вами, и знакомиться с ними.</p>

    <pre style="border: 0; background-color: transparent; font-size: 11px;">
___________________/______________________ /
     подпись             расшифровка
</pre>

    <p>С дополнительной общеобразовательной программой, свидетельством о государственной регистрации, уставом,
        лицензией на осуществление образовательной деятельности, другими документами, регламентирующими организацию
        и осуществление образовательной деятельности <?= $model->organization->name ?> ознакомлен.</p>

    <pre style="border: 0; background-color: transparent; font-size: 11px;">
___________________/______________________ /
     подпись             расшифровка
</pre>

    <p>Даю согласие на обработку предоставленных в настоящем заявлении моих персональных данных и персональных данных моего ребенка в порядке, установленном Федеральным законом от 27 июля 2006 г. №152-ФЗ «О персональных данных».</p>

    <pre style="border: 0; background-color: transparent; font-size: 11px;">
___________________/______________________ /
     подпись             расшифровка
</pre>

    <table>
        <tr>
            <td style="font-size: 11px;">
                <?= Yii::$app->formatter->asDate((strtotime($model->start_edu_contract) < time()) ? $model->start_edu_contract : time()) ?>
            </td>
            <td width="55%">
<pre style="border: 0; background-color: transparent; font-size: 11px;">
___________________/______________________ /
     подпись             расшифровка
</pre>
            </td>
        </tr>
    </table>
</div>
