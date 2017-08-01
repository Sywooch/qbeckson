<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = 'Персональная информация';
$this->params['breadcrumbs'][] = $this->title;

/* @var $this yii\web\View */
?>
<div class="container-fluid col-md-10 col-md-offset-1">
    <div class="row">
        <div class="col-md-7 ">
            <h2><?= $certificate['fio_child'] ?></h2>

            <p class="biglabel">Номер сертификата <strong><?= $certificate['number'] ?></strong></p>

            <p class="biglabel">ФИО законного представителя <strong><?= $certificate['fio_parent'] ?></strong></p>
            <br/>
            <br/>
            <p>
                <?= Html::a('Редактировать', ['/certificates/edit', 'id' => $certificate['id']], ['class' => 'btn btn-success']) ?>
                <?= Html::a('Изменить пароль', ['/certificates/password'], ['class' => 'btn btn-success']) ?>
            </p>
        </div>
        <div class="well col-md-5 text-center">
            <div>
                <p class="biglabel">Номинал сертификата<br><strong
                            class="bignumbers"><?= $certificate['nominal'] ?></strong></p>
                <p class="biglabel">Осталось средств<br><strong
                            class="bignumbers"><?= $certificate['balance'] ?></strong></p>
                <p class="biglabel">Зарезервировано на оплату договоров<br><strong
                            class="bignumbers"><?= $certificate['rezerv'] ?></strong></p>
            </div>
            <div>
                <?php
                if (Yii::$app->user->can('certificate') && $certificate->canChangeGroup && !empty($certificate->getCertificateGroupQueues($certificate->id)->count())) {
                    echo '<div class="alert alert-danger" role="alert">Вы находитесь в очереди на смену группы сертификата. Пожалуйста, подождите.</div>';
                } elseif (Yii::$app->user->can('certificate') && $certificate->canChangeGroup) {
                    echo '<br />';
                    echo '<a href="javascropt:void(0);" onClick="$(this).hide();$(\'#cert-form-container\').slideToggle(\'slow\');" class="btn btn-primary">Сменить сертификат</a><div style="display:none;" id="cert-form-container">';
                    $form = ActiveForm::begin();
                    echo $form->field($certificate, 'cert_group')->dropDownList(ArrayHelper::map($certificate->possibleGroupList, 'id', 'group'), ['prompt' => 'Выберите группу...']);
                    echo Html::submitButton('Обновить', ['class' => 'btn btn-primary']);
                    ActiveForm::end();
                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>
</div>