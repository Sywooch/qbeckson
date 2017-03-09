<?php

use yii\helpers\Html;
use app\models\Contracts;
use app\models\Certificates;

/* @var $this yii\web\View */

$this->title = 'Информация';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4 col-md-offset-1 well">
            <p><label class="control-label">Наименование</label> - <?= $operator['name'] ?></p>
            <p><label class="control-label">ИНН</label> - <?= $operator['INN'] ?></p>
            <p><label class="control-label">КПП</label> - <?= $operator['KPP'] ?></p>
            <p><label class="control-label">ОГРН</label> - <?= $operator['OGRN'] ?></p>
            <p><label class="control-label">ОКПО</label> - <?= $operator['OKPO'] ?></p>
            <p><label class="control-label">Юридический адрес</label> - <?= $operator['address_legal'] ?></p>
            <p><label class="control-label">Фактический адрес</label> - <?= $operator['address_actual'] ?></p>
            <p><label class="control-label">Телефон</label> - <?= $operator['phone'] ?></p>
            <p><label class="control-label">Email</label> - <?= $operator['email'] ?></p>
            <p><label class="control-label">Должность ответственного лица</label> - <?= $operator['position'] ?></p>
            <p><label class="control-label">ФИО ответственного лица</label> - <?= $operator['fio'] ?></p>
            <p>
              <?= Html::a('Редактировать', ['/operators/update', 'id' => $operator['id']], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <?php if (/*$this->beginCache('operator-statistic', ['duration' => 3600])*/1): ?>
        <div class="col-md-5  col-md-offset-1 well">
            <p>Общее число детей в системе - <?= Certificates::getCountCertificates() ?></p>
            <p>Общее число детей, использующих свой сертификат - <?= Contracts::getCountUsedCertificates() ?></p>
            <p>Детей, использующих сертификат для освоения одной программы - <?= Contracts::getCountUsedCertificates('=1') ?></p>
            <p>Детей, использующих сертификат для освоения двух программ - <?= Contracts::getCountUsedCertificates('=2') ?></p>
            <p>Детей, использующих сертификат для освоения трех и более программ  - <?= Contracts::getCountUsedCertificates('>2') ?></p>
            <p>Общее число договоров - <?= Contracts::getCountContracts() ?></p>
            <p>Организаций в системе персонифицированного финансирования - <?= $count_organizations ?></p>
            <p>Программ, доступных в рамках системы - <?= $count_programs ?></p>
        </div>
        <?php //$this->endCache(); ?>
        <?php endif; ?>
    </div>
</div>