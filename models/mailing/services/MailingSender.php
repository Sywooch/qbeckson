<?php

namespace app\models\mailing\services;

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
        return;
    }
}
