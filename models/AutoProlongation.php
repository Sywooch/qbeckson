<?php

namespace app\models;

use app\models\contracts\ContractRequest;
use app\models\forms\ContractRequestForm;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\ArrayHelper;

/**
 * авто пролонгация договоров
 */
class AutoProlongation
{
    /**
     * id организации для которой осуществляется автопролонгация контрактов
     *
     * @var integer
     */
    private $organizationId;

    /**
     * количество автопролонгированных заявок
     *
     * @var integer
     */
    private $contractRequestedAutoProlongedCount = 0;

    /**
     * количество автопролонгированных оферт
     *
     * @var integer
     */
    private $contractAcceptedAutoProlongedCount = 0;

    /**
     * создать экземпляр класса для указанной организации
     *
     * @param $organizationId
     *
     * @return static
     */
    public static function makeForOrganization($organizationId)
    {
        $autoProlongation = new static;

        $autoProlongation->organizationId = $organizationId;

        return !is_null(Organization::findOne($organizationId)) ? $autoProlongation : null;
    }

    /**
     * получить запрос выбора списка id сертификатов которые были пролонгированы
     *
     * @return integer[]
     */
    private function getAutoProlongedCertificateIdList()
    {
        return ArrayHelper::getColumn(Contracts::find()
            ->select(['certificateId' => 'contracts.certificate_id'])
            ->andWhere('creation_status != ' . Contracts::CREATION_STATUS_PROLONGED)
            ->all(),
            'certificateId'
        );
    }

    /**
     * @return ActiveQuery
     */
    private function getQuery()
    {
        $query = Contracts::find()
            ->distinct()
            ->leftJoin(Programs::tableName(), 'programs.id = contracts.program_id')
            ->leftJoin(Groups::tableName(), 'groups.id = contracts.group_id')
            ->andWhere(['contracts.status' => Contracts::STATUS_ACTIVE, 'contracts.period' => Contracts::CURRENT_REALIZATION_PERIOD])
            ->andWhere('contracts.stop_edu_contract < groups.datestop')
            ->andWhere(['programs.organization_id' => $this->organizationId])
            ->andWhere(['groups.status' => Groups::STATUS_ACTIVE])
            ->andWhere(['not in', 'contracts.certificate_id', $this->getAutoProlongedCertificateIdList()]);

        return $query;
    }

    /**
     * получить список программ, плательщики муниципалитетов которых имеют действующие соглашения с текущей организацией
     *
     * @return integer[]
     */
    public function getProgramIdList()
    {
        $programIdList = ArrayHelper::getColumn(
            $this->getQuery()
                ->select('programs.id')
                ->asArray()->all(),
            'id'
        );

        return $programIdList;
    }

    /**
     * получить список идентификаторов контрактов для автопролонгации
     *
     * @param array $excludedContractIdList - список id контрактов исключенных из списка автопролонгации
     *
     * @return integer[]
     */
    public function getContractIdList($excludedContractIdList = [])
    {
        $contractIdList = ArrayHelper::getColumn(
            $this->getQuery()
                ->select('contracts.id')
                ->andWhere(['programs.auto_prolongation_enabled' => 1])
                ->andWhere(['not in', 'contracts.id', $excludedContractIdList])
                ->asArray()->all(),
            'id'
        );

        return $contractIdList;
    }

    /**
     * установить/убрать автоматическую пролонгацию для всех программ, плательщики муниципалитетов которых имеют действующие соглашения с текущей организацией
     *
     * @param boolean $enabled
     *
     * @return bool
     */
    public function changeAutoProlongationForAllProgramsWithActiveCooperate($enabled)
    {
        $programIdList = $this->getProgramIdList();

        $count = Yii::$app->db->createCommand()->update(Programs::tableName(), ['auto_prolongation_enabled' => $enabled ? 1 : 0], ['id' => $programIdList])->execute();

        return $count ? 1 : 0;
    }

    /**
     * запустить авто пролонгацию договоров
     *
     * @param array $excludedContractIdList - список id контрактов исключенных из списка автопролонгации
     *
     * @return boolean
     */
    public function init($excludedContractIdList = [])
    {
        $contractIdList = $this->getContractIdList($excludedContractIdList);

        $contractWithFutureCooperateIdList = ArrayHelper::getColumn(Contracts::find()
            ->select('contracts.id')
            ->leftJoin(Cooperate::tableName(), 'contracts.payer_id = cooperate.payer_id and contracts.organization_id = cooperate.organization_id')
            ->where(['cooperate.status' => Cooperate::STATUS_ACTIVE, 'cooperate.period' => Cooperate::PERIOD_FUTURE])
            ->andWhere(['contracts.id' => $contractIdList])
            ->asArray()->all(),
            'id'
        );

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $dataContractForAutoProlongationList = $this->getContractDataListForAutoProlongation($contractIdList);
        $contractRequest = new ContractRequest();
        $contractRequest->setStartEduContract(date('d.m.Y', strtotime($operatorSettings->future_program_date_from)));
        $contractDataListRows = [];

        foreach ($dataContractForAutoProlongationList as $dataList) {
            if ($contractRequest->validate(
                $dataList['groupDateStart'],
                $dataList['groupDateStop'],
                $dataList['certificate_can_use_current_balance'],
                $dataList['certificate_can_use_future_balance']
            )) {
                $contractRequestData = $contractRequest->getData(
                    $dataList['groupDateStart'],
                    $dataList['groupDateStop'],
                    $dataList['groupModulePrice'],
                    $dataList['groupModuleNormativePrice'],
                    $dataList['groupId'],
                    $dataList['groupProgramId'],
                    $dataList['groupYearId'],
                    $dataList['groupOrganizationId'],
                    $dataList['certificateId'],
                    $dataList['certificatePayerId'],
                    $dataList['certificateNumber'],
                    $dataList['certificateBalance'],
                    $dataList['certificateBalanceF']
                );

                if (in_array($dataList['contractId'], $contractWithFutureCooperateIdList)) {
                    $contractData = array_merge($contractRequestData, [
                        'status' => Contracts::STATUS_ACCEPTED,
                        'cooperate_id' => $dataList['cooperateId'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'requested_at' => date('Y-m-d H:i:s'),
                        'accepted_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $contractData = array_merge($contractRequestData, [
                        'status' => Contracts::STATUS_REQUESTED,
                        'cooperate_id' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'requested_at' => date('Y-m-d H:i:s'),
                        'accepted_at' => null,
                    ]);
                }

                $contractData += ['creation_status' => Contracts::CREATION_STATUS_PROLONGED];

                $contractDataListRows[] = $contractData;
            }
        }

        $columns = array_keys($contractDataListRows[0]);

        $oldProlongedContractList = $this->getAutoProlongedCertificateIdList();

        Yii::$app->db->createCommand()->batchInsert(Contracts::tableName(), $columns, $contractDataListRows)->execute();
        $newProlongedContractList = ArrayHelper::getColumn(
            Contracts::find()
                ->select(['contractId' => 'contracts.id'])
                ->andWhere(['not in', 'certificate_id', $oldProlongedContractList])
                ->andWhere(['contracts.creation_status' => Contracts::CREATION_STATUS_PROLONGED])
                ->asArray()->all(),
            'contractId'
        );
        $this->contractRequestedAutoProlongedCount = Contracts::find()->where(['id' => $newProlongedContractList, 'status' => Contracts::STATUS_REQUESTED])->count();
        $this->contractAcceptedAutoProlongedCount = Contracts::find()->where(['id' => $newProlongedContractList, 'status' => Contracts::STATUS_ACCEPTED])->count();

        if (0 == $this->contractRequestedAutoProlongedCount && 0 == $this->contractAcceptedAutoProlongedCount) {
            return false;
        }

        return true;
    }

    /**
     * получить список данных контрактов для автопролонгации
     *
     * @param $contractIdList - список id контрактов для автопролонгации
     *
     * @return Contracts[]|array|\yii\db\ActiveRecord[]
     */
    private function getContractDataListForAutoProlongation($contractIdList)
    {
        return Contracts::find()
            ->select([
                'contractId' => 'contracts.id',
                'groupDateStart' => 'groups.datestart',
                'groupDateStop' => 'groups.datestop',
                'payers.certificate_can_use_current_balance',
                'payers.certificate_can_use_future_balance',
                'groupModulePrice' => 'years.price',
                'groupModuleNormativePrice' => 'years.normative_price',
                'groupId' => 'contracts.group_id',
                'groupProgramId' => 'contracts.program_id',
                'groupYearId' => 'contracts.year_id',
                'groupOrganizationId' => 'contracts.organization_id',
                'certificateId' => 'contracts.certificate_id',
                'certificatePayerId' => 'certificates.payer_id',
                'certificateNumber' => 'certificates.number',
                'certificateBalance' => 'certificates.balance',
                'certificateBalanceF' => 'certificates.balance_f',
                'cooperateId' => 'cooperate.id',
            ])
            ->leftJoin(Groups::tableName(), 'groups.id = contracts.group_id')
            ->leftJoin(ProgrammeModule::tableName(), 'years.id = groups.year_id')
            ->leftJoin(Payers::tableName(), 'payers.id = contracts.payer_id')
            ->leftJoin(Certificates::tableName(), 'certificates.id = contracts.certificate_id')
            ->leftJoin(Cooperate::tableName(), 'cooperate.organization_id = contracts.organization_id and cooperate.payer_id = contracts.payer_id and cooperate.period = ' . Cooperate::PERIOD_FUTURE)
            ->where(['contracts.id' => $contractIdList])
            ->asArray()->all();
    }

    /**
     * получить количество автопролонгированных заявок
     *
     * @return integer
     */
    public function getContractRequestedAutoProlongedCount()
    {
        return $this->contractRequestedAutoProlongedCount;
    }

    /**
     * получить количество автопролонгированных оферт
     *
     * @return integer
     */
    public function getContractAcceptedAutoProlongedCount()
    {
        return $this->contractAcceptedAutoProlongedCount;
    }
}