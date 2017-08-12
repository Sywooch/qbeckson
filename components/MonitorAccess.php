<?php

namespace app\components;

use Yii;
use yii\base\Component;
use yii\base\Event;
use yii\web\BadRequestHttpException;
use app\helpers\PermissionHelper;

class MonitorAccess extends Component
{
    public function init() {
        parent::init();
        if (!PermissionHelper::checkMonitorUrl()) {
            throw new BadRequestHttpException('В доступе отказано.');
        }
    }
}
