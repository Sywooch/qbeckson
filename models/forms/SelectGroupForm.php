<?php

namespace app\models\forms;

use app\models\Certificates;
use app\models\Contracts;
use app\models\Groups;
use app\models\Organization;
use app\models\ProgrammeModule;
use app\models\Programs;
use Yii;
use yii\base\Model;

/**
 * Class SelectGroupForm
 * @package app\models\forms
 */
class SelectGroupForm extends Model
{
    public $programId;
    public $moduleId;
    public $groupId;

    private $organization;
    private $certificate;
    private $program;
    private $module;
    private $group;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['programId', 'moduleId', 'groupId'], 'required'],
            ['programId', 'validateProgram'],
            ['moduleId', 'validateModule'],
            ['groupId', 'validateGroup'],
        ];
    }

    /**
     * @param $attribute
     */
    public function validateProgram($attribute)
    {
        if (null === ($program = $this->getProgram())) {
            $this->addError($attribute, 'Программа не найдена');
            return;
        }

        $programContractsCount = Contracts::find()
            ->andWhere([
                'program_id' => $program->id,
                'status' => [0, 1, 3]
            ])
            ->all();

        if ($programContractsCount >= $program->limit) {
            $this->addError($attribute, 'Превышен лимит на зачисление.');
            return;
        }
    }

    /**
     * @param $attribute
     */
    public function validateModule($attribute)
    {
        if (null === ($module = $this->getModule())) {
            $this->addError($attribute, 'Модуль не найден');
            return;
        }
    }

    /**
     * @param $attribute
     */
    public function validateGroup($attribute)
    {
        if (null === ($group = $this->getGroup())) {
            $this->addError($attribute, 'Группа не найдена');
            return;
        }

        if ((int)$this->getCertificate()->balance === 0 || (int)$this->getCertificate()->balance_f === 0) {
            $this->addError($attribute, 'Недостаточно средств на счету сертификата');
            return;
        }

        $groupContractsCount = Contracts::find()
            ->andWhere([
                'group_id' => $group->id,
                'status' => [0, 1, 3]
            ])
            ->all();

        if ($groupContractsCount >= $this->getModule()->maxchild) {
            $this->addError($attribute, 'Превышен лимит на зачисление.');
            return;
        }

        $payerContractsCount = Contracts::find()
            ->andWhere([
                'payer_id' => $this->getCertificate()->payer_id,
                'status' => [0, 1, 3]
            ])
            ->all();

        $this->addError($attribute, 'Заглушка');
    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'programId' => 'Программа',
            'moduleId' => 'Модуль',
            'groupId' => 'Группа',
        ];
    }

    /**
     * @return Certificates
     */
    public function getCertificate(): Certificates
    {
        return $this->certificate;
    }

    /**
     * @param Certificates $certificate
     */
    public function setCertificate(Certificates $certificate)
    {
        $this->certificate = $certificate;
    }

    /**
     * @return Programs|null
     */
    public function getProgram()
    {
        if (null === $this->program) {
            $this->program = Programs::findOne([
                'id' => $this->programId,
                'organization_id' => $this->getOrganization()->id
            ]);
        }

        return $this->program;
    }

    /**
     * @return ProgrammeModule|null
     */
    public function getModule()
    {
        if (null === $this->module) {
            $this->module = ProgrammeModule::findOne([
                'id' => $this->moduleId,
                'program_id' => $this->getProgram()->id,
            ]);
        }

        return $this->module;
    }

    /**
     * @return Groups|null
     */
    public function getGroup()
    {
        if (null === $this->group) {
            $this->group = Groups::findOne([
                'id' => $this->groupId,
                'year_id' => $this->getModule()->id,
            ]);
        }

        return $this->group;
    }

    /**
     * @return Organization|null
     */
    public function getOrganization()
    {
        if (null === $this->organization) {
            $this->organization = Yii::$app->user->identity->organization;
        }

        return $this->organization;
    }
}
