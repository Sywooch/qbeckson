<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 08.10.2017
 * Time: 9:37
 */

namespace app\components\widgets;


use yii\bootstrap\Button;
use yii\helpers\Html;

class ButtonWithInfo extends Button
{

    public $message;


    /**
     * Renders the widget.
     */
    public function run()
    {
        $this->registerPlugin('button');
        $label = $this->encodeLabel ? Html::encode($this->label) : $this->label;

        $span = Html::tag('span', $label, [
            'data' => [
                'toggle' => 'tooltip',
                'placement' => 'top',

            ],
            'title' => $this->message,
        ]);

        return Html::tag($this->tagName, $span, $this->options);
    }


}