<?php


namespace app\models\contracts;


use app\models\Completeness;
use app\models\Groups;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;

/**
 * Class GroupSwitcher
 * @package app\models\groups
 *
 * @property Groups[] $contractGroups
 * @property Completeness $preinvoice
 * @property Groups $group
 *
 */
class GroupSwitcher extends ContractsActions
{
    public $preinvoice;

    public $group_id;

    public function init()
    {
        parent::init();
        $this->fill();
    }

    public function fill()
    {
        if ($this->contract) {
            $this->preinvoice = Completeness::findPreinvoiceByContract($this->contract->id, date('n'), date('Y'));
            $this->group_id = $this->contract->group_id;
        }
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'group_id' => 'Группа'
        ]);
    }

    /**
     * @return array
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(),
            [
                ['group_id', 'required'],
                ['group_id', 'groupValidator'],
            ]
        );

    }

    public function getContractGroups()
    {

        return Groups::find()
            ->where(['organization_id' => $this->contract->organization_id])
            ->andwhere(['program_id' => $this->contract->program_id])
            ->andwhere(['year_id' => $this->contract->year_id])
            ->all();
    }

    public function groupValidator($attribute, $params, InlineValidator $validator)
    {
        if ($params && array_key_exists('message', $params) && $params['message']) {
            $message = $params['message'];
        } else {
            $message = 'В группе нет свободных мест.';
        }

        if ($this->group_id == $this->contract->group_id) {

            return;
        }

        if (!$this->group->freePlaces) {
            $this->addError('group_id', $message);
        }

    }


    public function setContract($contract)
    {
        parent::setContract($contract);
        $this->fill();
    }

    public function getGroup()
    {
        if ($this->group_id) {
            return Groups::findOne($this->group_id);
        } else {
            return null;
        }
    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию изнутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {
        if ($this->group_id == $this->contract->group_id) {

            return true;
        }

        if ($this->preinvoice) {
            $this->preinvoice->group_id = $this->group_id;
            if (!$this->preinvoice->save(false, ['group_id'])) {

                return $transactionTerminator();
            }
        }
        $this->contract->group_id = $this->group_id;

        return true;
    }

}