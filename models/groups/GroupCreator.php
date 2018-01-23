<?php

namespace app\models\groups;

use app\helpers\ArrayHelper;
use app\models\GroupClass;
use app\models\Groups;
use app\models\Model;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use Yii;

/**
 * класс для создания группы
 *
 * @property Groups $group
 * @property GroupClass[] $groupClasses
 * @property array $programModuleAddresses
 * @property array $programList
 */
class GroupCreator extends Model
{
    /**
     * создаваемая группа
     *
     * @var Groups
     */
    private $group = null;

    /**
     * список занятий группы
     *
     * @var GroupClass[]
     */
    private $groupClasses = [];

    /**
     * адреса организаций программы
     *
     * @var array
     */
    private $programModuleAddresses = [];

    /**
     * список программ организации для создания группы
     *
     * @var array
     */
    private $programList = [];

    /**
     * необходимо ли указать программу и модуль в которой будет создаваться группа
     *
     * @var bool
     */
    public $needModuleSet = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['group', 'validateGroup'],
        ];
    }

    /**
     * создать экземпляр класса
     *
     * @param ProgrammeModule $module - модуль в который добавляется группа
     * @param Organization $organization - организация
     *
     * @return static
     */
    public static function make($organization, $module = null)
    {
        $static = new static();

        $group = new Groups();

        if ($module) {
            $group->year_id = $module->id;
            $group->program_id = $module->program_id;
        } else {
            $static->needModuleSet = true;
        }

        $group->organization_id = $organization->id;

        $groupClasses = [];
        foreach (GroupClass::weekDays() as $key => $day) {
            $groupClasses[$key] = new GroupClass([
                'week_day' => $day
            ]);
        }

        $static->groupClasses = $groupClasses;
        $static->group = $group;

        return $static;
    }

    /**
     * @inheritdoc
     */
    public function load($data, $formName = null)
    {
        Model::loadMultiple($this->groupClasses, $data);

        return $this->group->load($data, $formName);
    }

    /**
     * @inheritdoc
     */
    public function validateGroup()
    {
        $result = $this->group->validate() && Model::validateMultiple($this->groupClasses);

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function save()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            if ($this->group->save()) {
                foreach ($this->groupClasses as $classModel) {
                    if ($classModel->status) {
                        $classModel->group_id = $this->group->id;
                        if (!($classModel->save())) {

                            $transaction->rollBack();

                            return false;
                        }
                    }
                }

                $transaction->commit();

                return true;
            } else {
                $transaction->rollBack();

                return false;
            }
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }
    }

    /**
     * проверить продолжительность программы
     */
    public function validateProgramDuration()
    {
        $month = date_diff((new \DateTime($this->group->datestart)), (new \DateTime($this->group->datestop)))->m;

        if (!($this->group->module->month < $month - 1 || $this->group->module->month > $month + 1)) {
            return true;
        } else {
            $this->group->addError('datestart', 'Продолжительность программы должна быть ' . $this->group->module->month . ' месяцев.');

            return false;
        }

    }

    /**
     * получить адреса организаций программы
     */
    public function getProgramModuleAddresses()
    {
        if ([] == $this->programModuleAddresses) {
            $this->programModuleAddresses = ArrayHelper::map($this->group->module->addresses, 'address', 'address');
        }

        return $this->programModuleAddresses;
    }

    /**
     * получить создаваемую группу
     *
     * @return Groups
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * получить список занятий группы
     *
     * @return GroupClass[]
     */
    public function getGroupClasses()
    {
        return $this->groupClasses;
    }

    /**
     * получить список программ организации для создания группы
     *
     * @return array
     */
    public function getProgramList()
    {
        if (!$this->programList) {
            $this->programList = ArrayHelper::map(
                Programs::find()->select(['id', 'name'])
                    ->where(['organization_id' => $this->group->organization_id])
                    ->andWhere(['verification' => Programs::VERIFICATION_DONE])
                    ->asArray()->all(),
                'id',
                'name'
            );
        }

        return $this->programList;
    }
}