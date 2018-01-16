<?php
/** @var $model \app\models\module\ModuleViewDecorator */

use app\components\widgets\modalCheckLink\ModalCheckLink;
use yii\helpers\Html;
use yii\helpers\Url;

?>
<div class="btn-row">
    <?php
    if ($model->haveAccess()) {
        /** Зачисление отккыть/закрыть */
        if ($model->isVerificate()) {
            if ($model->haveAccessToEnlistment()) {
                echo Html::a(
                    $model->getLabelEnlistment(),
                    $model->getEnlistmentActionUrl(),
                    ['class' => ['btn', $model->getClassButtonEnlistment()]]
                );
            } else {
                echo \app\components\widgets\ButtonWithInfo::widget([
                    'label' => 'Зачисление',
                    'message' => $model->getEnlistmentWarning(),
                    'options' => ['disabled' => 'disabled',
                        'class' => 'btn btn-theme',]
                ]);
            }
        }

        /** установка цены */
        if ($model->havePrice()) {
            if (!$model->canEditPrice()) {
                echo \app\components\widgets\ButtonWithInfo::widget([
                    'label' => 'Изменить цену',
                    'message' => 'Невозможно изменить цену когда открыто зачисление, либо существуют активные договора',
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
    } elseif ($model->havePermissions()) {
        echo \app\components\widgets\ButtonWithInfo::widget([
            'label' => 'Действия',
            'message' => $model->getNoAccessMessage(),
            'options' => ['disabled' => 'disabled',
                'class' => 'btn btn-theme',]
        ]);
    } ?>
</div>
