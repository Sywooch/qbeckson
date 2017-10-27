<?php

namespace app\models\mailing;

/**
 * This is the ActiveQuery class for [[MailTask]].
 *
 * @see MailTask
 */
class MailTaskQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MailTask[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MailTask|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
