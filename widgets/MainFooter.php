<?php
namespace app\widgets;

use yii;
use yii\base\Widget;
use app\models\Operators;

/**
 * Class MainFooter
 * @package app\widgets
 */
class MainFooter extends Widget
{
	/**
	 * @return string
	 */
	public function run()
    {
        $operator = Operators::find()
            ->where(['id' => Yii::$app->operator->identity->id])
            ->asArray()
            ->one();

    	return $this->render('main-footer/view', [
            'operator' => $operator,
		]);
	}
}