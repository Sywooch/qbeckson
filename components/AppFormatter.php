<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 30.11.17
 * Time: 19:06
 */

namespace app\components;


use app\models\UserIdentity;
use yii\helpers\Html;
use yii\helpers\StringHelper;
use yii\i18n\Formatter;

class AppFormatter extends Formatter
{
    public function asUserName($userId)
    {
        $identity = UserIdentity::findIdentity($userId);
        if (!$identity) {
            return 'не найден';
        } else {
            $authManager = \Yii::$app->getAuthManager();
            if ($authManager->checkAccess($userId, UserIdentity::ROLE_ORGANIZATION)) {
                return Html::tag('span', $identity->organization->name, [
                    'data' => [
                        'toggle' => 'tooltip',
                        'placement' => 'top',

                    ],
                    'title' => $identity->organization->full_name,
                ]);
            }
            return $identity->userName;
        }
    }

    public function asShortTextWithPopup($text)
    {
        return Html::tag('span', StringHelper::truncateWords($text, 2), [
            'data' => [
                'toggle' => 'tooltip',
                'placement' => 'top',

            ],
            'title' => $text,
        ]);
    }
}
