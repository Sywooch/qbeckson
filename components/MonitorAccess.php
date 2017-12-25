<?php

namespace app\components;

use yii\base\Component;
use app\helpers\PermissionHelper;
use yii\web\ForbiddenHttpException;

class MonitorAccess extends Component
{
    public function init() {
        parent::init();
        if (!PermissionHelper::checkMonitorUrl()) {
            throw new ForbiddenHttpException('В доступе отказано.');
        }
    }
}
