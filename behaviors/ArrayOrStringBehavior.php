<?php
namespace app\behaviors;

use yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use function \Opis\Closure\{serialize as srls, unserialize as unsrls};

class ArrayOrStringBehavior extends Behavior
{
    public $attributes1 = [];
    public $attributes2 = [];
    public $serialize = true;
    public $useClosureSerializator = false;

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
            $this->owner->{$attr} = $this->serialize === true ? ($this->useClosureSerializator === false ? serialize($this->owner->{$attr}) : srls($this->owner->{$attr})) : join(',', $this->owner->{$attr});
        }
    }

    public function convertString($event)
    {
        foreach ($this->attributes2 as $attr) {
            if ($this->owner->{$attr}) {
                $this->owner->{$attr} = $this->serialize === true ? ($this->useClosureSerializator === false ? unserialize($this->owner->{$attr}) : unsrls($this->owner->{$attr})) : explode(',', $this->owner->{$attr});
            }
        }
    }
}
