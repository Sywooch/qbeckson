<?php

namespace app\components\behaviors;


use Intervention\Image\ImageManager;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\db\AfterSaveEvent;

class ResizeImageAfterSaveBehavior extends Behavior
{

    public $width;
    public $height;
    public $attribute;
    public $basePath;

    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'eventAfterSave',
            ActiveRecord::EVENT_AFTER_UPDATE => 'eventAfterSave',

        ];
    }


    public function eventAfterSave(AfterSaveEvent $event)
    {

        /** @var $model ActiveRecord */
        $model = $this->owner;
        if ($model->attributes[$this->attribute] &&
            ($model->isNewRecord
                || array_key_exists($this->attribute, $event->changedAttributes))) {
            $manager = new ImageManager(array('driver' => 'gd'));
            $path = $this->basePath . DIRECTORY_SEPARATOR . $model->{$this->attribute};
            \Yii::trace($path);
            $img = $manager->make($path);
            $img->fit($this->width, $this->height);
            $img->save($path);
        }
    }


}