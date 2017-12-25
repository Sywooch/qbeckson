<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\models\contracts\ContractRequest;
use Yii;
use yii\db\ActiveQuery;

/**
 * автопролонгация договоров
 */
class AutoProlongation
{
    /**
     * id организации для поиска контрактов доступных для автопролонгации
     *
     * @var integer
     */
    private $organizationId;

    /**
     * id сертификата для поиска контрактов доступных для автопролонгации
     */
    private $certificateId;

    /**
     * id программы для поиска контрактов доступных для автопролонгации
     */
    private $programId;

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
     * создать экземпляр класса
     * ---
     * аргументы указываются для уточнения поиска доступных контрактов для автопролонгации,
     * если ни один аргумент не указан, поиск ведется всех контрактов доступных для автопролонгации
     *
     * @param $organizationId - id организации
     * @param $certificateId - id сертификата
     * @param $programId - id программы
     *
     * @return static
     */
    public static function make($organizationId = null, $certificateId = null, $programId = null)
    {
        $autoProlongation = new static;

        if (!is_null($organizationId) && !Organization::find()->where(['id' => $organizationId])->exists()) {
            return null;
        }

        $autoProlongation->organizationId = $organizationId;

        if (!is_null($certificateId) && !Certificates::find()->where(['id' => $certificateId])->exists()) {
            return null;
        }

        $autoProlongation->certificateId = $certificateId;

        if (!is_null($programId) && !Groups::find()->where(['id' => $programId])->exists()) {
            return null;
        }

        $autoProlongation->programId = $programId;

        return $autoProlongation;
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
            ->andWhere(['groups.status' => Groups::STATUS_ACTIVE])
            ->andWhere(['not in', 'contracts.id', Contracts::getAutoProlongedParentContractIdList()])
            ->andFilterWhere(['programs.organization_id' => $this->organizationId])
            ->andFilterWhere(['contracts.certificate_id' => $this->certificateId])
            ->andFilterWhere(['contracts.program_id' => $this->programId]);

        return $query;
    }

    /**
     * получить список программ, плательщики муниципалитетов которых имеют действующие соглашения с текущей организацией
     *
     * @return integer[]
     */
    public function getProgramIdList()
    {
        $programIdList = $this->getQuery()
            ->select('programs.id')
            ->column();

        return $programIdList;
    }

    /**
     * получить список идентификаторов контрактов для автопролонгации
     *
     * @param null $autoProlongationEnabled
     * @param null $limit
     *
     * @return integer[]
     */
    public function getContractIdList($autoProlongationEnabled = null, $limit = null)
    {
        $contractIdList = $this->getQuery()
            ->select('contracts.id')
            ->andWhere(['programs.auto_prolongation_enabled' => 1])
            ->andFilterWhere(['contracts.auto_prolongation_enabled' => $autoProlongationEnabled])
            ->limit($limit)
            ->column();

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
     * установить/убрать автоматическую пролонгацию для всех программ, плательщики муниципалитетов которых имеют действующие соглашения с текущей организацией
     *
     * @param boolean $enabled
     *
     * @return bool
     */
    public function changeAutoProlongationForAllContractsWithActiveCooperate($enabled)
    {
        $contractIdList = $this->getContractIdList();

        $count = Yii::$app->db->createCommand()->update(Contracts::tableName(), ['auto_prolongation_enabled' => $enabled ? 1 : 0], ['id' => $contractIdList])->execute();

        return $count ? 1 : 0;
    }

    /**
     * запустить авто пролонгацию договоров
     *
     * @param null $limit
     *
     * @return bool
     */
    public function init($limit = null)
    {
        $autoProlongationEnabled = true;
        $contractIdList = $this->getContractIdList($autoProlongationEnabled, $limit);

        $contractWithFutureCooperateIdList = Contracts::find()
            ->select('contracts.id')
            ->leftJoin(Cooperate::tableName(), 'contracts.payer_id = cooperate.payer_id and contracts.organization_id = cooperate.organization_id')
            ->where(['cooperate.status' => Cooperate::STATUS_ACTIVE, 'cooperate.period' => Cooperate::PERIOD_FUTURE])
            ->andWhere(['contracts.id' => $contractIdList])
            ->column();

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $dataContractForAutoProlongationList = $this->getContractDataListForAutoProlongation($contractIdList);

        if (count($dataContractForAutoProlongationList) < 1) {
            return false;
        }

        $contractRequest = new ContractRequest();
        $contractRequest->setStartEduContract(date('d.m.Y', strtotime($operatorSettings->future_program_date_from)));

        $contractNumberCount = 1;
        $futurePeriodCertificateDataListRows = [];
        $currentPeriodCertificateDataListRows = [];
        $contractDataListRows = [];
        $contractsWithSameCertificateCount = 0;
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
                    $this->contractAcceptedAutoProlongedCount += 1;

                    $contractData = array_merge($contractRequestData, [
                        'status' => Contracts::STATUS_ACCEPTED,
                        'cooperate_id' => $dataList['cooperateId'],
                        'created_at' => date('Y-m-d H:i:s'),
                        'requested_at' => date('Y-m-d H:i:s'),
                        'accepted_at' => date('Y-m-d H:i:s'),
                    ]);
                } else {
                    $this->contractRequestedAutoProlongedCount += 1;

                    $contractData = array_merge($contractRequestData, [
                        'status' => Contracts::STATUS_REQUESTED,
                        'cooperate_id' => null,
                        'created_at' => date('Y-m-d H:i:s'),
                        'requested_at' => date('Y-m-d H:i:s'),
                        'accepted_at' => null,
                    ]);
                }

                $contractData += [
                    'parent_id' => $dataList['contractId'],
                    'number' => ($dataList['organizationContractsCount'] + $contractNumberCount++) . ' - ПФ',
                    'date' => date('Y-m-d', strtotime($contractData['start_edu_contract'])),
                    'rezerv' => $dataList['fundsCert'],
                ];

                if ($contractData['balance'] - $dataList['fundsCert'] > 0 && isset($contractRequestData['period']) && in_array($contractRequestData['period'], [Contracts::CURRENT_REALIZATION_PERIOD, Contracts::FUTURE_REALIZATION_PERIOD])) {
                    if ($contractRequestData['period'] == Contracts::CURRENT_REALIZATION_PERIOD) {
                        if (in_array($dataList['certificateId'], ArrayHelper::getColumn($currentPeriodCertificateDataListRows, 'id'))) {
                            foreach ($currentPeriodCertificateDataListRows as &$currentPeriodCertificateData) {
                                if ($currentPeriodCertificateData['id'] == $dataList['certificateId']) {
                                    $contractsWithSameCertificateCount++;
                                    $currentPeriodCertificateData['balance_f'] = $currentPeriodCertificateData['balance_f'] - $dataList['fundsCert'];
                                    $currentPeriodCertificateData['rezerv_f'] = $currentPeriodCertificateData['rezerv_f'] + $dataList['fundsCert'];
                                }
                            }
                        } else {
                            $currentPeriodCertificateDataListRows[] = [
                                'id' => $dataList['certificateId'],
                                'user_id' => $dataList['certificateUserId'],
                                'number' => $dataList['certificateNumber'],
                                'payer_id' => $dataList['certificatePayerId'],
                                'cert_group' => $dataList['certificateCertGroup'],
                                'updated_cert_group' => $dataList['certificateUpdatedCertGroup'],
                                'balance' => $dataList['certificateBalance'] - $dataList['fundsCert'],
                                'rezerv' => $dataList['certificateRezerv'] + $dataList['fundsCert'],
                            ];
                        }
                    }

                    if ($contractRequestData['period'] == Contracts::FUTURE_REALIZATION_PERIOD) {
                        if (in_array($dataList['certificateId'], ArrayHelper::getColumn($futurePeriodCertificateDataListRows, 'id'))) {
                            foreach ($futurePeriodCertificateDataListRows as &$futurePeriodCertificateData) {
                                if ($futurePeriodCertificateData['id'] == $dataList['certificateId']) {
                                    $contractsWithSameCertificateCount++;
                                    $futurePeriodCertificateData['balance_f'] = $futurePeriodCertificateData['balance_f'] - $dataList['fundsCert'];
                                    $futurePeriodCertificateData['rezerv_f'] = $futurePeriodCertificateData['rezerv_f'] + $dataList['fundsCert'];
                                }
                            }
                        } else {
                            $futurePeriodCertificateDataListRows[] = [
                                'id' => $dataList['certificateId'],
                                'user_id' => $dataList['certificateUserId'],
                                'number' => $dataList['certificateNumber'],
                                'payer_id' => $dataList['certificatePayerId'],
                                'cert_group' => $dataList['certificateUpdatedCertGroup'],
                                'updated_cert_group' => $dataList['certificateCertGroup'],
                                'balance_f' => $dataList['certificateBalanceF'] - $dataList['fundsCert'],
                                'rezerv_f' => $dataList['certificateRezervF'] + $dataList['fundsCert'],
                            ];
                        }
                    }

                    $contractDataListRows[] = $contractData;
                }
            }
        }

        if (count($contractDataListRows) < 1) {
            return false;
        }

        $contractColumns = array_keys($contractDataListRows[0]);

        $oldProlongedContractIdList = Contracts::getAutoProlongedChildContractIdList();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $createdContractsCount = Yii::$app->db->createCommand()->batchInsert(Contracts::tableName(), $contractColumns, $contractDataListRows)->execute();

            $createdCurrentPeriodCertificatesCount = 0;
            $createdFuturePeriodCertificatesCount = 0;
            if ($currentPeriodCertificateDataListRows) {
                $currentPeriodCertificateColumns = array_keys($currentPeriodCertificateDataListRows[0]);
                $createdCurrentPeriodCertificatesCount = Yii::$app->db->createCommand(Yii::$app->db->createCommand()->batchInsert(Certificates::tableName(), $currentPeriodCertificateColumns, $currentPeriodCertificateDataListRows)->getRawSql() . ' ON DUPLICATE KEY UPDATE certificates.balance = values(balance), certificates.rezerv = values(rezerv)')->execute();
            }

            if ($futurePeriodCertificateDataListRows) {
                $futurePeriodCertificateColumns = array_keys($futurePeriodCertificateDataListRows[0]);
                $createdFuturePeriodCertificatesCount = Yii::$app->db->createCommand(Yii::$app->db->createCommand()->batchInsert(Certificates::tableName(), $futurePeriodCertificateColumns, $futurePeriodCertificateDataListRows)->getRawSql() . ' ON DUPLICATE KEY UPDATE certificates.balance_f = values(balance_f), certificates.rezerv_f = values(rezerv_f)')->execute();
            }

            // проверить соответствие кол-ва созданных контрактов кол-ву измененных сертификатов
            // (делится на 2 потому что используется команда mysql "ON DUPLICATE KEY UPDATE" при команде "INSERT" в результате получаем удвоенное кол-во затронутых строк)
            if (
                $createdContractsCount != ($createdCurrentPeriodCertificatesCount + $createdFuturePeriodCertificatesCount) / 2 + $contractsWithSameCertificateCount ||
                0 == $this->contractRequestedAutoProlongedCount && 0 == $this->contractAcceptedAutoProlongedCount
            ) {
                $transaction->rollBack();

                return false;
            }

            $transaction->commit();

            $newProlongedContractIdList = Contracts::getAutoProlongedChildContractIdList();

            foreach (array_diff($newProlongedContractIdList, $oldProlongedContractIdList) as $contractId) {
                $contract = Contracts::find()->where(['id' => $contractId, 'status' => Contracts::STATUS_ACCEPTED])->one();

                $contractRequest = new ContractRequest();
                $mpdf = $contractRequest->makePdfForContract($contract);
                $mpdf->Output(Yii::getAlias('@pfdoroot/uploads/contracts/') . $contract->url, 'F');
            }

        } catch (\Exception $e) {
            $transaction->rollBack();

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
                'fundsCert' => 'contracts.funds_cert',
                'certificateUserId' => 'certificates.user_id',
                'certificatePayerId' => 'certificates.payer_id',
                'certificateNumber' => 'certificates.number',
                'certificateCertGroup' => 'certificates.cert_group',
                'certificateUpdatedCertGroup' => 'certificates.updated_cert_group',
                'certificateBalance' => 'certificates.balance',
                'certificateBalanceF' => 'certificates.balance_f',
                'certificateRezerv' => 'certificates.rezerv',
                'certificateRezervF' => 'certificates.rezerv_f',
                'cooperateId' => 'cooperate.id',
                'organizationContractsCount' => 'organization.contracts_count',
            ])
            ->leftJoin(Groups::tableName(), 'groups.id = contracts.group_id')
            ->leftJoin(ProgrammeModule::tableName(), 'years.id = groups.year_id')
            ->leftJoin(Payers::tableName(), 'payers.id = contracts.payer_id')
            ->leftJoin(Certificates::tableName(), 'certificates.id = contracts.certificate_id')
            ->leftJoin(Cooperate::tableName(), 'cooperate.organization_id = contracts.organization_id and cooperate.payer_id = contracts.payer_id and cooperate.period = ' . Cooperate::PERIOD_FUTURE)
            ->leftJoin(Organization::tableName(), 'organization.id = contracts.organization_id')
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