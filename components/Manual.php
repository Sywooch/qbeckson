<?php

namespace app\components;

use app\models\Help;
use Yii;
use yii\base\Component;

class Manual extends Component
{
    public function init()
    {
        parent::init();

        $userRoles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if (!strpos(Yii::$app->request->url, 'site/manual') && $mans = Help::getCountUncheckedMans(array_shift($userRoles))) {
            Yii::$app->response->redirect(Yii::$app->urlManager->createUrl('/site/manuals-required'));
        }
    }
}
