<?php

namespace app\models\mailing\decorators;


use app\components\Decorator;
use app\models\mailing\activeRecord\MailingAttachment;
use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\activeRecord\MailTask;
use app\models\User;

/**
 * @property integer $id
 * @property integer $created_by
 * @property integer $created_at
 * @property string $subject
 * @property string $message
 *
 * @property MailTask[] $mailTasks
 * @property MailingAttachment[] $mailingAttachments
 * @property User $createdBy
 */
class MailingListDecorator extends Decorator
{
    public function __construct(MailingList $entity)
    {
        parent::__construct($entity);
    }

    public function getStatus()
    {
        return 'status';
    }

}
