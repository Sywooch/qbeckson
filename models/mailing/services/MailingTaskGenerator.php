<?php

namespace app\models\mailing\services;

use app\models\mailing\activeRecord\MailTask;
use app\models\mailing\repository\MailTaskRepository;

class MailingTaskGenerator implements \IteratorAggregate
{
    private $transportClass;

    public function __construct(string $transportClass)
    {
        $this->transportClass = $transportClass;
    }

    public static function getGenerator(
        string $transportClass = \app\models\mailing\services\MailTask::class
    )
    {
        return new self($transportClass);
    }

    /**
     * **Внимание** Эта функция запускает потенциально бесконечную рекурсию,
     *  У обработанной задачи в обязательном порядке состояние должно отличаться от TASK_STATUS_CREATED - 1
     *  И должно быть сохранено в текущем источнике данных.
     *  Возможно переполнение стека, при нгрузке > 10 000 заданий за раз
     * @inheritDoc
     */
    public function getIterator()
    {
        $dataSource = MailTaskRepository::getRepository()
            ->getCreatedTasksWithMailingList();
        if (count($dataSource) < 1) {
            return;
        }
        foreach ($dataSource as $mailTaskAR) {
            yield $this->buildTransportObject($mailTaskAR);
        }
        $this->getIterator();
    }

    public function buildTransportObject(MailTask $mailTaskAR)
    {
        return \Yii::createObject($this->transportClass, ['model' => $mailTaskAR]);
    }


}
