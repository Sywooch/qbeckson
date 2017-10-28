<?php

namespace app\models\mailing;


class MailingStaticData
{
    const TARGET_ORGANIZATION = 10;
    const TARGET_PAYER = 20;

    const TASK_STATUS_CREATED = 1;
    const TASK_STATUS_IN_QUEUE = 10;
    const TASK_STATUS_FINISH = 30;
    const TASK_STATUS_HAS_ERRORS = 40;

    public static function getTaskStateLabels(): array
    {
        return [
            self::TASK_STATUS_CREATED => 'Создано',
            self::TASK_STATUS_IN_QUEUE => 'Выполняется',
            self::TASK_STATUS_FINISH => 'Успешно завершено',
            self::TASK_STATUS_HAS_ERRORS => 'Ошибка',
        ];
    }

    public static function attributeListLabels()
    {
        return [
            'id' => 'ID',
            'created_by' => 'Автор',
            'created_at' => 'Создано',
            'subject' => 'Тема письма',
            'message' => 'Сообщение',
            'mun' => 'Мунициалитет',
            'target' => 'Тип получателя',
            'state' => 'Состояние',
        ];
    }
}
