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

    public static function getTargetNames()
    {
        return [
            self::TARGET_ORGANIZATION => 'Организации',
            self::TARGET_PAYER => 'Плательщики',
        ];
    }

    /**
     * @return string
     */
    public static function getTemplateMessage()
    {
        return <<<HTML
<p style="text-align: left;">
    <br>    
    <br>
    <hr>
    <p>
Пожалуйста, не отвечайте на данное информационное письмо, для обратной связи с оператором существуют другие способы.
    </p>
</p>
HTML;
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
            'countAllTasks' => 'Всего получателей',
            'state' => 'Состояние',
            'targetsString' => 'Типы получателей',
            'munsString' => 'Муниципалитеты получателей',
            'lastActionTime' => 'Последняя активность',

        ];
    }
}
