<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 30.10.2017
 * Time: 17:05
 */

namespace app\tests\codeception\unit\models\mailing;

use app\models\mailing\services\MailingTaskGenerator;
use app\models\mailing\services\MailTaskInterface;
use Codeception\Test\Unit;

class MailingTaskGeneratorTest extends Unit
{
    protected $generator;

    public function _before()
    {
        $this->generator = new MailingTaskGenerator(MailTask::class);
    }

    public function testBaseGenerator()
    {
        $i = 0;
        foreach ($this->generator as $task) {
            $i++;
            $this->assertEquals(1, $task);
        }

        $this->assertEquals(4, $i);
    }
}

class MailTask implements MailTaskInterface
{
    public $senderEmail;
    public $recipientEmail;
    public $subject;
    public $bodyHtml;
    public $bodyText;
    public $attachments;

    public function getSenderEmail(): string
    {
        return $this->senderEmail;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function getBodyHtml(): string
    {
        return $this->bodyHtml;
    }

    public function getBodyText(): string
    {
        return $this->bodyText;
    }


    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function setError($message): void
    {
    }

    public function setSuccess(): void
    {
    }


}
