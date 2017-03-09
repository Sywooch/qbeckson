<?php
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Информация';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-1 well">
            <p><label class="control-label">Наименование организации</label> - <?= $payer['name'] ?></p>
            <p><label class="control-label">ИНН</label> - <?= $payer['INN'] ?></p>
            <p><label class="control-label">КПП</label> - <?= $payer['KPP'] ?></p>
            <p><label class="control-label">ОГРН</label> - <?= $payer['OGRN'] ?></p>
            <p><label class="control-label">ОКПО</label> - <?= $payer['OKPO'] ?></p>
            <p><label class="control-label">Юридический адрес</label> - <?= $payer['address_legal'] ?></p>
            <p><label class="control-label">Фактический адрес</label> - <?= $payer['address_actual'] ?></p>
            <p><label class="control-label">Представитель организации</label> - <?= $payer['fio'] ?></p>
            <p><label class="control-label">Номер телефона</label> - <?= $payer['phone'] ?></p>
            <p><label class="control-label">E-mail</label> - <?= $payer['email'] ?></p>
            <p>
              <?= Html::a('Редактировать', ['/payers/edit', 'id' => $payer['id']], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="col-md-5  col-md-offset-1 well">
            <p>Количество выданных сертификатов - <?= $count_certificates ?></p>
            <p>Общая сумма выданных сертификатов - <?= $sum_certificates ?></p>
            <p>Количество выданных сертификатов по которым заключены договора на обучение - <?= $count_certificates_contracts ?></p>
            <p>Количество детей обучающихся по одной образовательной программе с использованием выданных сертификатов - <?= $count_certificates_contracts_one ?></p>
            <p>Количество детей обучающихся по двум образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_two ?></p>
            <p>Количество детей обучающихся по трем и более образовательным программам с использованием выданных сертификатов - <?= $count_certificates_contracts_more ?></p>
            <p>Общее количество договоров обучающения заключенных с использованием выданных сертификатов - <?= $sum_contracts ?></p>
        </div>
    </div>
</div>