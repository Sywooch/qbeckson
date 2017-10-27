<?php

namespace app\models\mailing;

/**
 * This is the ActiveQuery class for [[MailingAttachment]].
 *
 * @see MailingAttachment
 */
class MailingAttachmentQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return MailingAttachment[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return MailingAttachment|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
