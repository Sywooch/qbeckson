<?php

namespace app\commands;

use yii;
use yii\console\Controller;
use app\models\UserIdentity;

class AdminController extends Controller
{
    public function actionSetPassword($password = '123456')
    {
        $adminIds = Yii::$app->authManager->getUserIdsByRole('admins');
        if ($admin = UserIdentity::findIdentity($adminIds[0])) {
            $admin->setPassword($password);
            if ($admin->save()) {
                echo "Success.\n";

                return 0;
            }
        }

        return 1;
    }
}
