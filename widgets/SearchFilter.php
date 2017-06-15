<?php
namespace app\widgets;

use yii;
use yii\base\Widget;

/**
 * Class SearchFilter
 * @package app\widgets
 */
class SearchFilter extends Widget
{
    const TYPE_INPUT = 'input';

    const TYPE_RANGE_SLIDER = 'range-slider';

    const TYPE_TOUCH_SPIN = 'touch-spin';

    const TYPE_DROPDOWN = 'drop-down';

    public $model;

    public $action;

    public $data;

    public function init()
    {
        foreach ($this->data as $index => $row) {
            if (!is_array($row)) {
                $this->data[$index] = ['attribute' => $row];
            }
            if (empty($row['type'])) {
                $this->data[$index]['type'] = self::TYPE_INPUT;
            }
        }
        parent::init();
    }

    /**
	 * @return string
	 */
	public function run()
	{
    	return $this->render('search-filter/_search', [
            'model' => $this->model,
            'action' => $this->action,
            'data' => $this->data,
		]);
	}
}