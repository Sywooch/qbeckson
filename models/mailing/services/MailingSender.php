<?php

namespace app\models\mailing\services;

use Yii;

class MailingSender
{
    private $dataSource;

    public function __construct(\Traversable $dataSource)
    {
        $this->dataSource = $dataSource;
    }

    public static function createInstance(\Traversable $dataSource): self
    {
        return new self($dataSource);
    }

    public function run()
    {
        foreach ($this->dataSource as $task) {
            $this->send($task);
        }
    }

    public function send(MailTaskInterface $task)
    {
        $mail = Yii::$app->mailer
            ->compose(
                ['html' => 'operatorMailing-html', 'text' => 'operatorMailing-text'],
                ['htmlMessage' => $task->getBodyHtml(), 'textMessage' => $task->getBodyText()]
            )
            ->setTo($task->getRecipientEmail())
            ->setFrom([$task->getSenderEmail() => $task->getSenderName()])
            ->setSubject($task->getSubject());
        try {
            $result = $mail->send();
        } catch (\Exception $exception) {
            $task->setError($exception->getCode());

            return;
        }
        if ($result) {
            $task->setSuccess();
        } else {
            $task->setError('не удалось отправить');
        }
    }
}
