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
 * Форма валидации данных.
 *
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
     * Проверка возможности заключения договора:
     * 1) Сертификат имеет тип сертификата ПФ
     * 2) Есть средства на текущем балансе (если группа "захватывает" текущий период) или есть средства на будущем
     * балансе (при условии, что группа захватывает будущий период; дополнительное условие сдеоаем потом: разрешено
     * заключение договоров в будущем периоде)
     * 3) число договоров (со статусами 0, 1, 3) в группе не превышает максимальную наполняемость по модулю
     * 4) число договоров (со статусами 0, 1, 3) по программе не превышает установленный для программы лимит
     * 5) число договоров (со статусами 0, 1, 3) в организацию не преввшает установленный лимит для организации
     * 6) число договоров (со статусами 0, 1, 3) по всем программам аналогичной направленности, поданных детьми того
     * же плательщика, не превышает лимит направленности, установленный для плательщика
     * 7) у ребенка нет договора (со статусами 0, 1 или 3) на тот же модуль (на программу можно)
     * Соответственно, сначала проверяетсч ребенок (1), можно ли ему вообще зачислиться и лимит организации (5)
     * если достигнут лимит вывыдятся для выбора сначала программы, которые вообще могут
     * быть выбраны (по критериям 4 и 6)
     * После выбора программы вытягиваются модули, удовлетворяющие критерияю 7
     * После выбора модуля идёт возможность выбрать группу, исходя из критериев 2 и 3
     *
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
            $this->addError($attribute, 'Программа не найдена.');
            return;
        }

        $programContractsCount = Contracts::find()
            ->andWhere([
                'program_id' => $program->id,
                'status' => [0, 1, 3]
            ])
            ->count();

        if ($programContractsCount >= $program->limit) {
            $this->addError($attribute, 'Превышен лимит на зачисление в программу.');
            return;
        }

        // TODO: Как-то переделать кастыль
        switch ($program->direction_id) {
            case 1:
                $directionName = 'directionality_1rob_count';
                break;
            case 2:
                $directionName = 'directionality_1_count';
                break;
            case 3:
                $directionName = 'directionality_4_count';
                break;
            case 4:
                $directionName = 'directionality_2_count';
                break;
            case 5:
                $directionName = 'directionality_6_count';
                break;
            case 6:
                $directionName = 'directionality_5_count';
                break;
            case 7:
                $directionName = 'directionality_3_count';
                break;
        }

        $payerContractsCount = Contracts::find()
            ->innerJoinWith('program')
            ->andWhere([
                'payer_id'                => $this->getCertificate()->payer_id,
                '`programs`.direction_id' => $program->direction_id,
                'status'                  => [0, 1, 3]
            ])
            ->count();

        if ($payerContractsCount >= $this->getCertificate()->payer->$directionName) {
            $this->addError($attribute, 'Превышен лимит зачисления на программы аналогичной направленности, установленный программой ПФ.');
            return;
        }
    }

    /**
     * @param $attribute
     */
    public function validateModule($attribute)
    {
        if (null === ($module = $this->getModule())) {
            $this->addError($attribute, 'Модуль не найден.');
            return;
        }

        $isHasContract = Contracts::find()
            ->joinWith(['module'])
            ->andWhere([
                'certificate_id' => $this->getCertificate()->id,
                'years.id' => $module->id,
                'status' => [0, 1, 3],
            ])
            ->exists();

        if ($isHasContract) {
            $this->addError($attribute, 'Ребёнок уже записан на данный модуль.');
            return;
        }
    }

    /**
     * @param $attribute
     */
    public function validateGroup($attribute)
    {
        if (null === ($group = $this->getGroup())) {
            $this->addError($attribute, 'Группа не найдена.');
            return;
        }

        if ((int)$this->getCertificate()->balance === 0 || (int)$this->getCertificate()->balance_f === 0) {
            $this->addError($attribute, 'Недостаточно средств на счету сертификата.');
            return;
        }

        $groupContractsCount = Contracts::find()
            ->andWhere([
                'group_id' => $group->id,
                'status' => [0, 1, 3]
            ])
            ->count();

        if ($groupContractsCount >= $this->getModule()->maxchild) {
            $this->addError($attribute, 'Превышен лимит на зачисление в группу.');
            return;
        }
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
