<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 30.10.2017
 * Time: 11:18
 */

namespace app\models\mailing\services;


use app\models\mailing\activeRecord\MailTask as MailTaskActiveRecord;
use app\models\mailing\MailingStaticData;
use Html2Text\Html2Text;
use yii\base\Exception;
use yii\log\Logger;

/**
 * Class MailTask
 * @package app\models\mailing\services
 */
class MailTask implements MailTaskInterface
{
    private $model;

    public function __construct(MailTaskActiveRecord $model)
    {
        $this->model = $model;
    }

    public function build(MailTaskActiveRecord $model): self
    {
        return new self($model);
    }

    public function getSenderEmail(): string
    {
        return \Yii::$app->params['sendEmail'];
    }

    public function getSenderName(): string
    {
        return 'PFDO';
    }


    public function getRecipientEmail(): string
    {
        return $this->getModel()->email;
    }

    private function getModel(): MailTaskActiveRecord
    {
        return $this->model;
    }

    public function getSubject(): string
    {
        return $this->getModel()->mailingList->subject;
    }

    public function getBodyHtml(): string
    {
        return $this->getModel()->mailingList->message;
    }

    public function getBodyText(): string
    {
        return (new Html2Text(
            $this->getModel()->mailingList->message
        ))
            ->getText();
    }

    public function getAttachments(): array
    {
        return []; /*todo implements later*/
    }

    public function setError($message): void
    {
        $this->getModel()->status = MailingStaticData::TASK_STATUS_HAS_ERRORS;
        $this->getModel()->log_message = $message;
        $this->modelSaveState();
    }

    private function modelSaveState()
    {
        if (!$this->getModel()->save()) {
            $errorMessage = 'id: "' . $this->getModel()->id
                . '". Не удалось сохранить статус задачи рассылки!'
                . print_r($this->getModel()->getErrors(), true);
            \Yii::getLogger()
                ->log(
                    $errorMessage,
                    Logger::LEVEL_ERROR
                );

            throw new Exception($errorMessage);
        }
    }

    public function setSuccess(): void
    {
        $this->getModel()->status = MailingStaticData::TASK_STATUS_FINISH;
        $this->modelSaveState();
    }

}
