<?php

namespace app\models\mailing\services;


use app\components\SingleModelActions;
use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\repository\MailingListRepository;

/**
 * Class MailingActions
 * @package app\models\mailing\services
 * @property MailingList $mailingList
 */
abstract class MailingActions extends SingleModelActions
{

    /** класс модели над которой производятся действия */
    public static function getTargetModelClass(): string
    {
        return MailingListRepository::className();
    }

    public function rules()
    {
        return array_merge(
            parent::rules(),
            [['mailingList', 'required']]
        );
    }

    public function getMailingList()
    {
        return parent::getTargetModel();
    }

    public function setMailingList($mailingList)
    {
        parent::setTargetModel($mailingList);
    }
}
