<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $certificate \app\models\Certificates */

$this->params['breadcrumbs'][] = ['label' => 'Персональная информация'];
$js = <<<'JS'
    $('.change-tab').click(function(e) {
        e.preventDefault();
        $('.tab').addClass('hide');
        $('#' + $(this).data('next')).removeClass('hide');
    })
JS;
$this->registerJs($js, $this::POS_READY);
?>
<br>
<div class="container-fluid col-md-10 col-md-offset-1">
    <div class="row">
        <div class="col-md-7">
            <h2><?= $certificate->fio_child ?></h2>
            <p class="biglabel">Номер сертификата <strong><?= $certificate->number ?></strong></p>
            <p class="biglabel">ФИО законного представителя <strong ><?= $certificate->fio_parent ?></strong></p>
            <br>
            <br>
            <p>
                <?= Html::a(
                    'Редактировать',
                    ['/certificates/edit','id' => $certificate->id],
                    ['class' => 'btn btn-success']
                ) ?>
                <?= Html::a(
                    'Изменить пароль',
                    ['/certificates/password'],
                    ['class' => 'btn btn-success']
                ) ?>
            </p>
        </div>
        <div class="well col-md-5 text-center">
            <div id="current" class="tab">
                <p class="lead">Номинал сертификата<br>
                    <strong class="bignumbers"><?= $certificate->nominal ?></strong>
                </p>
                <p class="lead">Осталось средств<br>
                    <strong class="bignumbers"><?= $certificate->balance ?></strong>
                </p>
                <p class="lead">Зарезервировано на оплату договоров<br>
                    <strong class="bignumbers"><?= $certificate->rezerv ?></strong>
                </p>
                <a data-next="next" class="change-tab pull-right well margin-bottom-0" href="">
                    <i class="glyphicon glyphicon-menu-right"></i>
                </a>
            </div>
            <div id="next" class="tab hide">
                <p class="lead">Номинал сертификата на будущий период<br>
                    <strong class="bignumbers"><?= $certificate->nominal_f ?></strong>
                </p>
                <p class="lead">Осталось средств на будущий период<br>
                    <strong class="bignumbers"><?= $certificate->balance_f ?></strong>
                </p>
                <p class="lead">Зарезервировано на оплату договоров на будущий период<br>
                    <strong class="bignumbers"><?= $certificate->rezerv_f ?></strong>
                </p>
                <a data-next="current" class="change-tab pull-right well margin-bottom-0" href="">
                    <i class="glyphicon glyphicon-menu-left"></i>
                </a>
            </div>
        </div>
    </div>
</div>
