<?php

namespace app\widgets;

use app\models\SettingsSearchFilters;
use app\models\UserSearchFiltersAssignment;
use yii\base\Exception;
use yii\base\Widget;
use yii\db\ActiveRecord;

/**
 * Class SearchFilter
 * @package app\widgets
 * @property  ActiveRecord $model
 */
class SearchFilter extends Widget
{
    const TYPE_INPUT = 'input';
    const TYPE_RANGE_SLIDER = 'range-slider';
    const TYPE_TOUCH_SPIN = 'touch-spin';
    const TYPE_DROPDOWN = 'drop-down';
    const TYPE_DROPDOWN_MULTIPLE = 'drop-down-multiple';
    const TYPE_SELECT2 = 'select2';
    const TYPE_SELECT2SINGLE = 'select2single';
    const TYPE_HIDDEN = 'hiddenInput';

    /** @var array */
    public $data;
    /** @var ActiveRecord */
    public $model;
    public $action;
    public $type;
    public $role;
    public $customizable = true;

    /**
     * @inheritdoc
     */
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
        if (null === $this->role) {
            throw new \DomainException('Role must be set!');
        }
        if (null === $this->data) {
            throw new \DomainException('Data must be set!');
        }
        if (null === $this->model) {
            throw new \DomainException('Model must be set!');
        }
        if (null === $this->action) {
            throw new \DomainException('Action must be set!');
        }
        parent::init();
    }

    /**
     * @return string
     */
    public function run()
    {
        $table = $this->model->getTableSchema()->fullName;
        $filter = SettingsSearchFilters::find()
            ->andWhere(['>', 'is_active', 0])
            ->andWhere([
                'table_name' => $table,
                'role' => $this->role
            ]);

        if (is_null($this->type) || strlen($this->type) < 1) {
            $filter->andWhere(['OR', [
                'type' => null
            ], [
                'type' => ''
            ]]);
        } else {
            $filter->andWhere(['type' => $this->type]);
        }
        $result = $filter->one();
        $userFilter = null;
        if (is_null($result) && defined('YII_ENV') && YII_ENV === 'dev') {
            $template = 'нужны настройки фильтра, либо фильтр не активен: role: %s; table: %s; type: %s';
            $message = sprintf($template, $this->role, $table, $this->type);
            throw new Exception($message);
        } else {
            $userFilter = UserSearchFiltersAssignment::findByFilter($result);
        }

        return $this->render('search-filter/_search', [
            'model' => $this->model,
            'action' => $this->action,
            'data' => $this->data,
            'userFilter' => $userFilter,
            'customizable' => $this->customizable,
        ]);
    }
}
