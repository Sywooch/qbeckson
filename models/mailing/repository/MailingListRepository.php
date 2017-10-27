<?php

namespace app\models\mailing\repository;


use app\models\mailing\activeRecord\MailingList;
use yii\helpers\ArrayHelper;

class MailingListRepository extends MailingList
{

    public function attributeLabels()
    {
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'mun' => 'Мунициалитет',
                'target' => 'Тип получателя',
                'state' => 'Состояние',
            ]
        );
    }
}
