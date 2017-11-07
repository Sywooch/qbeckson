<?php

namespace app\commands;

use app\models\mailing\services\MailingSender;
use app\models\mailing\services\MailingTaskGenerator;
use yii\console\Controller;

class MailingController extends Controller
{
    /**
     * Запускает Email рассылку из текущего списка задачь,
     * @return void
     */
    public function actionRun()
    {
        MailingSender::createInstance(
            MailingTaskGenerator::getGenerator()
        )
            ->run();
    }
}
