<?php

namespace app\components\widgets\postButtonWithModalConfirm;


use app\models\User;
use yii\base\Widget;

class PostButtonWithModalConfirm extends Widget
{
    public $url;
    public $confirm;
    public $userModel = null;
    public $title = 'Удалить плательщика';
    public $toggleButton = [];

    public function init()
    {
        parent::init();
        if (is_null($this->userModel)) {
            $this->userModel = (new User())->setShortLoginScenario();
        }
    }

    public function run()
    {
        return $this->render('index', ['model'        => $this->userModel,
                                       'title'        => $this->title,
                                       'confirm'      => $this->confirm,
                                       'toggleButton' => $this->toggleButton,
                                       'url'          => $this->url]);
    }

}