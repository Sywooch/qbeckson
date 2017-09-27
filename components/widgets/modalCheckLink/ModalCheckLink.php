<?php

namespace app\components\widgets\modalCheckLink;

class ModalCheckLink extends \yii\base\Widget
{
    public $link;
    public $title;
    public $content;
    public $label;
    public $buttonOptions = [];

    public function init()
    {
        parent::init();
    }

    public function run()
    {
        return $this->render('index', [
            'link'          => $this->link,
            'title'         => $this->title,
            'label'         => $this->label,
            'content'       => $this->content,
            'buttonOptions' => $this->buttonOptions
        ]);
    }

}