<?php
/**
 * Created by PhpStorm.
 * User: gluck
 * Date: 10.01.18
 * Time: 15:38
 */

namespace app\components\imagemanager;


class ImageManagerGetPath extends \noam148\imagemanager\components\ImageManagerGetPath
{
    public function init()
    {
        parent::init();
        $this->mediaPath = \Yii::getAlias($this->mediaPath);
    }
}
