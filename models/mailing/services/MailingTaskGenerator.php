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
     * @see \IteratorAggregate
     * Возвращает объекты MailTaskInterface, до тех пор пока есть данные в источнике
     */
    public function getIterator()
    {
        while (count(($dataSource = MailTaskRepository::getRepository()
                ->getCreatedTasksWithMailingList())) > 0) {
            foreach ($dataSource as $mailTaskAR) {
                yield $this->buildTransportObject($mailTaskAR);
            }
        }
    }

    public function buildTransportObject(MailTask $mailTaskAR): MailTaskInterface
    {
        return new $this->transportClass($mailTaskAR);
    }


}
