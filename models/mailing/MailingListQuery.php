<?php

namespace app\models\mailing;

/**
 * This is the ActiveQuery class for [[MailingList]].
 *
 * @see MailingList
 */
class MailingListQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MailingList[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MailingList|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
