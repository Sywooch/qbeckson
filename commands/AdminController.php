<?php

namespace app\commands;

use app\models\Operators;
use yii;
use yii\console\Controller;
use app\models\UserForm;
use app\models\UserIdentity;

class AdminController extends Controller
{
    /**
     * @param null|string $username
     * @param null|string $password
     * @return void
     */
    public function actionCreateOperator($username = null, $password = null)
    {
        $form = new UserForm();
        $form->username = $username;
        $form->password = $password;
        if ($user = $form->create()) {
            Yii::$app->authManager->assign(Yii::$app->authManager->getRole(UserIdentity::ROLE_OPERATOR), $user->id);
            $operator = new Operators([
                'user_id' => $user->id,
                'name' => $user->username,
                'OGRN' => '0',
                'INN' => '0',
                'KPP' => '0',
                'OKPO' => '0',
                'address_legal' => '-',
                'address_actual' => '-',
                'phone' => '-',
                'email' => '-',
                'position' => '-',
                'fio' => '-',
            ]);
            $operator->save();
            echo 'Логин: ' . $user->username . PHP_EOL;
            echo 'Пароль: ' . $form->password . PHP_EOL;

            return;
        }

        print_r($form->errors);
    }

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
