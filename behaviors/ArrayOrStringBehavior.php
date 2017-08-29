<?php
namespace app\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;

class ArrayOrStringBehavior extends Behavior
{
    public $attributes1 = [];
    public $attributes2 = [];
    public $serialize = true;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_VALIDATE => 'convertArray',
            ActiveRecord::EVENT_AFTER_FIND => 'convertString'
        ];
    }

    public function convertArray($event)
    {
        foreach ($this->attributes1 as $attr) {
            if (is_array($this->owner->{$attr})) {
                $this->owner->{$attr} = $this->serialize === true ? serialize($this->owner->{$attr}) : join(',', $this->owner->{$attr});
            }
        }
    }

    public function convertString($event)
    {
        foreach ($this->attributes2 as $attr) {
            if ($this->owner->{$attr}) {
                $this->owner->{$attr} = $this->serialize === true ? unserialize($this->owner->{$attr}) : explode(',', $this->owner->{$attr});
            }
        }
    }
}
