<?php
/** @var $model \app\models\ProgrammeModule */

use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="btn-row">

    <?php
    if (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && $model->program->verification !== \app\models\Programs::VERIFICATION_DENIED) {
        $message = '';
        $url = '#';
        $classButton = 'btn-theme';
        $active = false;

        /**@var $organization \app\models\Organization */
        $organization = Yii::$app->user->identity->organization;
        if ($organization->actual) {
            if ($model->price > 0) {
                if ($organization->type !== \app\models\Organization::TYPE_IP_WITHOUT_WORKERS) {
                    if ($organization->license_issued_dat && $organization->fio && $organization->position && $organization->doc_type) {
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
        if ($active) {
            echo Html::a($message, $url, ['class' => 'btn ' . $classButton]);
        } else {
            echo \yii\bootstrap\Button::widget(['label'   => $message,
                                                'options' => ['class'    => 'btn btn-theme',
                                                              'disabled' => 'disabled'],]);
        }

        /** установка цены */
        if ($model->price) {
            if ($model->open && $model->price) {
                echo \yii\bootstrap\Button::widget(['label'   => 'Нельзя изменить цену',
                                                    'options' => ['class'    => 'btn btn-theme',
                                                                  'disabled' => 'disabled'],]);
            } else {
                echo Html::a('Изменить цену', Url::to(['years/update', 'id' => $model->id]), ['class' => 'btn btn-theme']);
            }
        } else {
            echo Html::a('Установить цену', Url::to(['years/update', 'id' => $model->id]), ['class' => 'btn btn-theme']);
        }

        /** предварительные записи */
        /*if ($model->previus) {
            echo Html::a('Закрыть запись', Url::to(['years/prevstop', 'id' => $model->id]), ['class' => 'btn btn-theme']);
        } else {
            echo Html::a('Открыть запись', Url::to(['years/prevstart', 'id' => $model->id]), ['class' => 'btn btn-theme']);
        }*//*отключено 110917*/

        /** Адреса */
        if (count($model->addresses)) {
            echo Html::a(
                'Изменить адреса модуля',
                ['years/add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']
            );
        } else {
            echo Html::a(
                'Указать адреса для модуля',
                ['years/add-addresses', 'id' => $model->id], ['class' => 'btn btn-theme']
            );
        }
        /** Группы*/
        echo Html::a('Добавить группу', Url::to(['/groups/newgroup', 'id' => $model->id]), ['class' => 'btn btn-theme']);



        ?>

    <?php } elseif (Yii::$app->user->can(\app\models\UserIdentity::ROLE_ORGANIZATION)
        && $model->program->verification === \app\models\Programs::VERIFICATION_DENIED) {
        echo \yii\bootstrap\Button::widget(['label'   => 'Недоступно по причине отказа',
                                            'options' => ['class'    => 'btn btn-theme',
                                                          'disabled' => 'disabled'],]);
    } ?>
    <?php /*
<div class="btn-row">
    <button class="btn btn-theme">Создать группу</button>
    <button class="btn btn-theme">Записаться</button>
    <button class="btn btn-theme">Установить цену</button>
</div>
*/
    ?>
</div>