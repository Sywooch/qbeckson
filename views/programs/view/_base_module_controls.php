<?php
/** @var $model \app\models\ProgrammeModule */

use app\components\widgets\modalCheckLink\ModalCheckLink;
use app\models\Programs;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="btn-row">

    <?php
    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && ($model->program->verification !== Programs::VERIFICATION_DENIED
            || $model->program->verification !== Programs::VERIFICATION_WAIT
        )
        && ($model->verification !== \app\models\ProgrammeModule::VERIFICATION_DENIED
            || $model->verification !== \app\models\ProgrammeModule::VERIFICATION_WAIT)
    ) {
        $message = '';
        $url = '#';
        $classButton = 'btn-theme';
        $active = false;

        /**@var $organization \app\models\Organization */
        $organization = Yii::$app->user->identity->organization;
        if ($organization->actual) {
            if ($model->price > 0) {
                if ($organization->type !== \app\models\Organization::TYPE_IP_WITHOUT_WORKERS) {
                    if ($organization->license_issued_dat
                        && $organization->fio
                        && $organization->position
                        && $organization->doc_type
                    ) {
                        if ($organization->doc_type === \app\models\Organization::DOC_TYPE_PROXY) {
                            if ($organization->date_proxy && $organization->number_proxy) {
                                $active = true;
                                if ($model->open) {
                                    $message = 'Закрыть зачисление';
                                    $url = Url::to(['years/stop', 'id' => $model->id]);
                                    $classButton = 'btn-danger';
                                } else {
                                    $message = 'Открыть зачисление';
                                    $url = Url::to(['years/start', 'id' => $model->id]);
                                }
                            } else {
                                $active = false;
                                $message = 'Заполните информацию "Для договора"';
                            }
                        } else {
                            $active = true;
                            if ($model->open) {
                                $message = 'Закрыть зачисление';
                                $url = Url::to(['years/stop', 'id' => $model->id]);
                                $classButton = 'btn-danger';
                            } else {
                                $message = 'Открыть зачисление';
                                $url = Url::to(['years/start', 'id' => $model->id]);
                            }
                        }
                    } else {
                        $active = false;
                        $message = 'Заполните информацию "Для договора"';
                    }
                } else {
                    $active = true;
                    if ($model->open) {
                        $message = 'Закрыть зачисление';
                        $url = Url::to(['years/stop', 'id' => $model->id]);
                        $classButton = 'btn-danger';
                    } else {
                        $message = 'Открыть зачисление';
                        $url = Url::to(['years/start', 'id' => $model->id]);
                    }
                }
            } else {
                $active = false;
                $message = 'Нет цены, нельзя открыть';
            }
        } else {
            $active = false;
            $message = 'Деятельность приостановлена';
        }

        /** Зачисление отккыть/закрыть */
        if ($model->program->verification == Programs::VERIFICATION_DONE) {
            if ($active) {
                echo Html::a($message, $url, ['class' => 'btn ' . $classButton]);
            } else {
                echo \app\components\widgets\ButtonWithInfo::widget([
                    'label' => 'Зачисление',
                    'message' => $message,
                    'options' => ['disabled' => 'disabled',
                        'class' => 'btn btn-theme',]
                ]);
            }
        }

        /** установка цены */
        if ($model->price) {
            if ($model->open && $model->price) {
                echo \app\components\widgets\ButtonWithInfo::widget([
                    'label' => 'Изменить цену',
                    'message' => 'Невозможно мзмнеть цену когда открыто зачисление',
                    'options' => ['disabled' => 'disabled',
                        'class' => 'btn btn-theme',]
                ]);
            } else {
                echo Html::a(
                    'Изменить цену',
                    Url::to(['years/update',
                        'id' => $model->id]),
                    ['class' => 'btn btn-theme']
                );
            }
        } else {
            echo Html::a(
                'Установить цену',
                Url::to(['years/update', 'id' => $model->id]),
                ['class' => 'btn btn-theme']
            );
        }

        /** предварительные записи */
        /*if ($model->previus) {
            echo Html::a(
                'Закрыть запись',
                Url::to(['years/prevstop',
                    'id' => $model->id]),
                ['class' => 'btn btn-theme']
            );
        } else {
            echo Html::a(
                'Открыть запись',
                Url::to(['years/prevstart',
                    'id' => $model->id]),
                ['class' => 'btn btn-theme']
            );
        }*//*отключено 110917*/

        /** Адреса */
        if (count($model->addresses)) {
            echo Html::a(
                'Изменить адреса модуля',
                ['years/add-addresses', 'id' => $model->id],
                ['class' => 'btn btn-theme']
            );
        } else {
            echo Html::a(
                'Указать адреса для модуля',
                ['years/add-addresses', 'id' => $model->id],
                ['class' => 'btn btn-theme']
            );
        }
        /** Группы*/
        echo Html::a(
            'Добавить группу',
            Url::to(['/groups/newgroup', 'id' => $model->id]),
            ['class' => 'btn btn-theme']
        );

        if ($model->canEdit()) {
            echo ModalCheckLink::widget([
                'link' => Html::a(
                    'Редактировать',
                    Url::to(['/module/update', 'id' => $model->id]),
                    ['class' => 'btn btn-theme']
                ),
                'buttonOptions' => ['label' => 'Редактировать', 'class' => 'btn btn-theme'],
                'content' => 'После редактирования модуль будет отправлен на сертификацию',
                'title' => 'Редактировать модуль ' . $model->name,
                'label' => 'Да, я уверен, что хочу внести изменения в модуль.',
            ]);
        } else {
            echo \app\components\widgets\ButtonWithInfo::widget([
                'label' => 'Редактировать',
                'message' => 'Невозможно, существуют контракты и/или открыто зачисление,'
                    . ' либо оператор запустил процедуру сертификации',
                'options' => ['disabled' => 'disabled',
                    'class' => 'btn btn-theme',]
            ]);
        }


        ?>

    <?php } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && ($model->program->verification === Programs::VERIFICATION_DENIED
            || $model->program->verification === Programs::VERIFICATION_WAIT)) {

        echo \app\components\widgets\ButtonWithInfo::widget([
            'label' => 'Действия',
            'message' => 'Недоступно по причине отказа',
            'options' => ['disabled' => 'disabled',
                'class' => 'btn btn-theme',]
        ]);
    } ?>
</div>
