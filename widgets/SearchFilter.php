<?php
namespace app\widgets;

use app\models\SettingsSearchFilters;
use app\models\UserSearchFiltersAssignment;
use yii\base\Widget;
use yii\db\ActiveRecord;

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
    const TYPE_SELECT2 = 'select2';

    /** @var array */
    public $data;
    /** @var ActiveRecord */
    public $model;
    public $action;
    public $type;
    public $role;

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
        $filter = SettingsSearchFilters::find()
            ->andWhere(['>', 'is_active', 0])
            ->andWhere([
                'table_name' => $this->model->tableName(),
                'type' => $this->type,
                'role' => $this->role
            ])
            ->one();

        $userFilter = UserSearchFiltersAssignment::findByFilter($filter);

        return $this->render('search-filter/_search', [
            'model' => $this->model,
            'action' => $this->action,
            'data' => $this->data,
            'userFilter' => $userFilter,
        ]);
    }
}
