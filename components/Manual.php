<?php

namespace app\components;

use Yii;
use yii\base\Component;
use app\models\Help;

class Manual extends Component
{
    public function init()
    {
        parent::init();

        $userRoles = Yii::$app->authManager->getRolesByUser(Yii::$app->user->id);
        if (!strpos(Yii::$app->request->url, 'manuals-required') && $mans = Help::getCountUncheckedMans(array_shift($userRoles))) {
            Yii::$app->response->redirect(Yii::$app->urlManager->createUrl('/site/manuals-required'));
        }
    }
}
