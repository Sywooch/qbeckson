<?php

namespace app\models\mailing\repository;

use app\models\mailing\activeRecord\MailTask;
use app\models\mailing\MailingStaticData;

/**
 * Class MailTaskRepository
 * @package app\models\mailing\repository
 */
class MailTaskRepository
{
    public static function getRepository(): self
    {
        return new self();
    }

    /**
     * @param $limit int
     *
     * @return MailTask[]
     */
    public function getCreatedTasksWithMailingList(int $limit = 50)
    {
        return MailTask::find()
            ->where(['status' => MailingStaticData::TASK_STATUS_CREATED])
            ->limit($limit)
            ->with('mailingList')
            ->all();
    }
}
