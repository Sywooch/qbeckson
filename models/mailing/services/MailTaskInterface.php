<?php

namespace app\models\mailing\services;


interface MailTaskInterface
{
    public function getSenderEmail(): string;  //отправитель Email

    public function getSenderName(): string;  //отправитель имя

    public function getRecipientEmail(): string; //получатель

    public function getSubject(): string;  //Тема

    public function getBodyHtml(): string;   //тело письма html версия

    public function getBodyText(): string;   //тело письма Текстовая версия

    public function getAttachments(): array;

    public function setError($message): void;  //установить ошибку и ее описание

    public function setSuccess(): void;        //отметить что готово
}
