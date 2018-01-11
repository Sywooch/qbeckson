<?php

namespace app\models;

use app\helpers\ArrayHelper;
use app\models\contracts\ContractAutoProlongedLog;
use app\models\contracts\ContractRequest;
use Box\Spout\Common\Exception\IOException;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Writer\WriterFactory;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;

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
     * id группы для поиска контрактов доступных для автопролонгации
     */
    private $groupId;

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
     * сообщение ошибки при автопролонгации
     *
     * @var string
     */
    public $errorMessage = '';

    /**
     * кол-во оставшихся контрактов для автопролонгации
     *
     * @var int
     */
    public $remainCount = null;

    /**
     * создать экземпляр класса
     * ---
     * аргументы указываются для уточнения поиска доступных контрактов для автопролонгации,
     * если ни один аргумент не указан, ищутся все контракты доступные для автопролонгации
     *
     * @param $organizationId - id организации
     * @param $certificateId - id сертификата
     * @param $programId - id программы
     * @param $groupId - id группы
     *
     * @return static
     */
    public static function make($organizationId = null, $certificateId = null, $programId = null, $groupId = null)
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

        if (!is_null($programId) && !Programs::find()->where(['id' => $programId])->exists()) {
            return null;
        }
        $autoProlongation->programId = $programId;

        if (!is_null($groupId) && !Groups::find()->where(['id' => $groupId])->exists()) {
            return null;
        }
        $autoProlongation->groupId = $groupId;

        return $autoProlongation;
    }

    /**
     * @return ActiveQuery
     */
    private function getQuery()
    {
        if (in_array(date('m'), [6, 7, 8, 9])) {
            $allowDatePeriod = date('Y-m-d', strtotime('-4 Month'));
        } else {
            $allowDatePeriod = date('Y-m-d', strtotime('-1 Month'));
        }

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $query = Contracts::find()
            ->distinct()
            ->leftJoin(Payers::tableName(), 'payers.id = contracts.payer_id')
            ->leftJoin(Programs::tableName(), 'programs.id = contracts.program_id')
            ->leftJoin(Groups::tableName(), 'groups.id = contracts.group_id')
            ->andWhere(['contracts.period' => [Contracts::CURRENT_REALIZATION_PERIOD, Contracts::PAST_REALIZATION_PERIOD]])
            ->andWhere(['groups.status' => Groups::STATUS_ACTIVE])
            ->andWhere(['not in', 'contracts.id', Contracts::getAutoProlongedParentContractIdList()])
            ->andWhere(['or',
                ['and',
                    ['contracts.status' => Contracts::STATUS_ACTIVE],
                    ['or',
                        ['contracts.wait_termnate' => null],
                        ['and',
                            ['contracts.wait_termnate' => 1],
                            'contracts.terminator_user = 0'
                        ]
                    ]
                ],
                ['and',
                    ['contracts.status' => Contracts::STATUS_CLOSED],
                    ['and',
                        'contracts.stop_edu_contract = contracts.date_termnate',
                        ['>', 'contracts.stop_edu_contract', $allowDatePeriod]
                    ],
                ]
            ])
            ->andWhere(['<', 'contracts.stop_edu_contract', date('Y-m-d', strtotime('+1 Month'))])
            ->andFilterWhere(['programs.organization_id' => $this->organizationId])
            ->andFilterWhere(['contracts.certificate_id' => $this->certificateId])
            ->andFilterWhere(['contracts.program_id' => $this->programId])
            ->andFilterWhere(['contracts.group_id' => $this->groupId]);

        if (is_null($this->groupId)) {
            $query->andWhere('contracts.stop_edu_contract < groups.datestop')
                ->andWhere(['or',
                    ['and',
                        ['and',
                            ['and',
                                ['>', 'groups.datestop', $operatorSettings->current_program_date_from],
                                ['<', 'groups.datestop', $operatorSettings->future_program_date_from],
                            ],
                            ['<', 'groups.datestart', $operatorSettings->current_program_date_from]
                        ],
                        ['or',
                            ['payers.certificate_can_use_current_balance' => 1],
                            ['and',
                                ['payers.certificate_can_use_current_balance' => 0],
                                ['>', 'payers.certificate_cant_use_current_balance_at', date('Y-m-d H:i:s')],
                            ],
                        ],
                    ],
                    ['and',
                        ['and',
                            ['<', 'groups.datestart', $operatorSettings->current_program_date_to],
                            ['>', 'groups.datestop', $operatorSettings->future_program_date_from],
                        ],
                        ['payers.certificate_can_use_future_balance' => 1],
                    ],
                ]);
        }

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
     * @param null $filterByAutoProlongationEnabled
     * @param null $limit
     * @param [] $exceptContractIdList
     *
     * @return integer[]
     */
    public function getContractIdList($filterByAutoProlongationEnabled = null, $limit = null, $exceptContractIdList = [])
    {
        $contractIdList = $this->getQuery()
            ->select('contracts.id')
            ->andWhere(['programs.auto_prolongation_enabled' => 1])
            ->andFilterWhere(['contracts.auto_prolongation_enabled' => $filterByAutoProlongationEnabled])
            ->andWhere(['not in', 'contracts.id', $exceptContractIdList])
            ->limit($limit)
            ->column();

        return $contractIdList;
    }

    /**
     * получить список сертификатов для пролонгации
     *
     * @param $exceptYearId
     * @param $filteredByAutoProlongationEnabled
     *
     * @return array
     */
    public function getContractIdListForAutoProlongationToNewGroup($exceptYearId = null, $filteredByAutoProlongationEnabled = false)
    {
        $contractIdListQuery = $this->getQuery()
            ->select('contracts.id');

        if ($exceptYearId) {
            $contractIdListQuery->andWhere(['not in', 'contracts.year_id', $exceptYearId]);
        }

        if ($filteredByAutoProlongationEnabled) {
            $contractIdListQuery->andWhere(['contracts.parent_id' => null]);
        }

        return $contractIdListQuery->column();
    }

    /**
     * получить список модулей программы не включающие указанную группу
     *
     * @param $programId - id программы
     * @param $exceptGroupId - id группы из которой осуществляется перевод
     *
     * @return array
     */
    public static function getModuleIdList($programId, $exceptGroupId)
    {
        $exceptYearId = Groups::find()->select(['groups.year_id'])->where(['id' => $exceptGroupId])->column();

        $moduleListId = ProgrammeModule::find()
            ->select(['id'])
            ->where(['program_id' => $programId])
            ->andWhere(['!=', 'years.id', $exceptYearId])
            ->column();

        return $moduleListId;
    }

    /**
     * получить список id групп, в которые возможен перевод при автопролонгации договоров
     *
     * @param $yearId
     * @param $groupId - id группы из которой переводятся
     *
     * @return array
     */
    public static function getGroupIdList($yearId, $groupId)
    {
        $groupIdList = Groups::find()
            ->select(['id', 'name'])
            ->where(['status' => Groups::STATUS_ACTIVE, 'year_id' => $yearId])
            ->andWhere(['>', 'datestart', (new Query())->select(['groups.datestop'])->from(Groups::tableName())->where(['groups.id' => $groupId])])
            ->asArray()->all();

        return $groupIdList;
    }

    /**
     * может ли контракты группы автопролонгироваться в другую группу
     *
     * @param $organizationId - id организации
     * @param $groupId - id группы для перевода
     *
     * @return bool
     */
    public static function canGroupBeAutoProlong($organizationId, $groupId)
    {
        $group = Groups::findOne($groupId);

        $self = self::make($organizationId, null, null, $groupId);
        $self->getContractIdListForAutoProlongationToNewGroup(null, true);

        if ($self->getContractIdListForAutoProlongationToNewGroup(null, true) && date('Y-m-d', strtotime('+1 Month')) > $group->datestop && (date('Y-m-d', strtotime('-1 Month')) < $group->datestop || in_array(date('m', strtotime($group->datestop)), [5, 6, 7, 8]) && date('Y-m-d', strtotime('-4 Month')) < $group->datestop)) {
            return true;
        }

        return false;
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
     * @param integer $groupId - id группы для автопролонгации в указанную группу
     * @param null $limit
     * @param bool $isNew
     * @param array $filterContractIdList - список допустимых id контрактов для автопролонгации
     *
     * @return bool
     */
    public function init($groupId = null, $limit = null, $isNew = true, $filterContractIdList = [])
    {
        $processedContractIdList = $this->getProcessedContractIdListFromRegistry();

        $filteredByAutoProlongationEnabled = true;
        if ($group = Groups::findOne($groupId)) {
            $contractIdList = $this->getContractIdListForAutoProlongationToNewGroup(null, $filteredByAutoProlongationEnabled);
        } else {
            $contractIdList = $this->getContractIdList($filteredByAutoProlongationEnabled, $limit, !$isNew ? $processedContractIdList : []);
        }

        if (count($filterContractIdList) > 0) {
            $contractIdList = array_intersect($contractIdList, $filterContractIdList);
        }

        if (!$isNew) {
            $contractIdList = array_diff($contractIdList, $processedContractIdList);

            $this->remainCount = count(array_diff($this->getContractIdList($filteredByAutoProlongationEnabled), $processedContractIdList));
        }

        $contractIdListWithActiveCooperate = $this->getContractIdListForActiveCooperate($contractIdList);

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $dataContractForAutoProlongationList = $this->getContractDataListForAutoProlongation($contractIdList);

        $registry = [];

        if (count($dataContractForAutoProlongationList) < 1) {
            foreach ($contractIdList as $contractId) {
                $registry[$contractId] = ['contractNumber' => '', 'date' => '', 'certificateNumber' => '', 'certificateBalance' => ''];
            }

            $this->writeToXlsx($isNew, $registry);

            return false;
        }

        $contractRequest = new ContractRequest();
        if ($group) {
            $contractRequest->setStartEduContract(date('d.m.Y', strtotime($group->datestart)));
        } else {
            $startEduContract = date_diff(new \DateTime(date($operatorSettings->current_program_date_from)), new \DateTime())->m > 0 ? $operatorSettings->future_program_date_from : $operatorSettings->current_program_date_from;
            $contractRequest->setStartEduContract(date('d.m.Y', strtotime($startEduContract)));
        }

        $contractNumber = 1;
        $organizationContractCount = Organization::findOne($this->organizationId)->getContracts()->where(['contracts.status' => [Contracts::STATUS_REQUESTED,Contracts::STATUS_ACTIVE,Contracts::STATUS_REFUSED,Contracts::STATUS_ACCEPTED,Contracts::STATUS_CLOSED,]])->count();
        $futurePeriodCertificateDataListRows = [];
        $currentPeriodCertificateDataListRows = [];
        $contractDataListRows = [];
        $contractsWithSameCertificateCount = 0;

        foreach ($dataContractForAutoProlongationList as $dataList) {
            if ($contractRequest->validate(
                $group ? $group->datestart : $dataList['groupDateStart'],
                $group ? $group->datestop : $dataList['groupDateStop'],
                $dataList['certificate_can_use_current_balance'],
                $dataList['certificate_can_use_future_balance']
            )) {
                $contractRequestData = $contractRequest->getData(
                    $group ? $group->datestart : $dataList['groupDateStart'],
                    $group ? $group->datestop : $dataList['groupDateStop'],
                    $group ? $group->module->price : $dataList['groupModulePrice'],
                    $group ? $group->module->normative_price : $dataList['groupModuleNormativePrice'],
                    $group ? $group->id : $dataList['groupId'],
                    $group ? $group->program_id : $dataList['groupProgramId'],
                    $group ? $group->year_id : $dataList['groupYearId'],
                    $group ? $group->organization_id : $dataList['groupOrganizationId'],
                    $dataList['certificateId'],
                    $dataList['certificatePayerId'],
                    $dataList['certificateNumber'],
                    $dataList['certificateBalance'],
                    $dataList['certificateBalanceF']
                );

                if (in_array($dataList['contractId'], $contractIdListWithActiveCooperate)) {
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
                    'number' => ($organizationContractCount + $contractNumber++) . ' - ПФ',
                    'date' => date('Y-m-d', strtotime($contractData['start_edu_contract'])),
                    'rezerv' => $dataList['fundsCert'],
                ];

                if (isset($contractData['period']) && in_array($contractData['period'], [Contracts::CURRENT_REALIZATION_PERIOD, Contracts::FUTURE_REALIZATION_PERIOD])) {
                    if ($contractData['balance'] - $dataList['fundsCert'] > 0) {
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

                    $registry[$dataList['contractId']] = [
                        'contractNumber' => $dataList['contractNumber'],
                        'date' => \Yii::$app->formatter->asDate($dataList['contractDate']),
                        'certificateNumber' => $dataList['certificateNumber'],
                        'certificateBalance' => $contractData['balance'],
                    ];
                }
            } else {
                $this->errorMessage = $contractRequest->errorMessage;

                return false;
            }
        }

        if (count($contractDataListRows) < 1) {
            $this->writeToXlsx($isNew, $registry);

            return true;
        }

        $oldProlongedContractIdList = Contracts::getAutoProlongedChildContractIdList();

        $transaction = Yii::$app->db->beginTransaction();

        try {
            $createdContractsCount = Yii::$app->db->createCommand()->batchInsert(Contracts::tableName(), array_keys($contractDataListRows[0]), $contractDataListRows)->execute();

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

            $this->writeLogs($contractDataListRows);

            $allProlongedContractIdList = Contracts::getAutoProlongedChildContractIdList();
            $newProlongedContractIdList = array_diff($allProlongedContractIdList, $oldProlongedContractIdList);

            foreach ($newProlongedContractIdList as $contractId) {
                $contract = Contracts::find()->where(['id' => $contractId, 'status' => Contracts::STATUS_ACCEPTED])->one();

                if ($contract) {
                    $contractRequest = new ContractRequest();
                    $mpdf = $contractRequest->makePdfForContract($contract);
                    $mpdf->Output(Yii::getAlias('@pfdoroot/uploads/contracts/') . $contract->url, 'F');
                }
            }

            foreach ($newProlongedContractIdList as $contractId) {
                $contract = Contracts::findOne($contractId);
                $registry[$contract->parent_id]['childContractId'] = $contract->id;
                $registry[$contract->parent_id]['childContractNumber'] = $contract->number;
                $registry[$contract->parent_id]['childContractDate'] = $contract->date;
            }

            $this->writeToXlsx($isNew, $registry);
        } catch (\Exception $e) {
            $transaction->rollBack();

            return false;
        }

        return true;
    }

    /**
     * получить список id контрактов для которых есть действующее соглашение между плательщиком и организацией
     *
     * @param $contractIdList
     *
     * @return array
     */
    private function getContractIdListForActiveCooperate($contractIdList)
    {

        /** @var \app\models\OperatorSettings $operatorSettings */
        $operatorSettings = Yii::$app->operator->identity->settings;

        $contractIdListForActiveCooperate = Contracts::find()
            ->select('contracts.id')
            ->leftJoin(Cooperate::tableName(), 'contracts.payer_id = cooperate.payer_id and contracts.organization_id = cooperate.organization_id')
            ->leftJoin(Groups::tableName(), 'groups.id = contracts.group_id')
            ->where(['cooperate.status' => Cooperate::STATUS_ACTIVE])
            ->andWhere(['contracts.id' => $contractIdList])
            ->andWhere(['or',
                ['and',
                    ['and',
                        ['>', 'groups.datestop', $operatorSettings->current_program_date_from],
                        ['<', 'groups.datestop', $operatorSettings->current_program_date_to]
                    ],
                    ['cooperate.status' => Cooperate::PERIOD_CURRENT]
                ],
                ['and',
                    ['and',
                        ['>', 'groups.datestop', $operatorSettings->future_program_date_from],
                        ['<', 'groups.datestop', $operatorSettings->future_program_date_to]
                    ],
                    ['cooperate.status' => Cooperate::PERIOD_FUTURE]
                ],
            ])
            ->column();

        return $contractIdListForActiveCooperate;
    }

    /**
     * записать в файл автопролонгированные контракты
     *
     * @param $isNew
     * @param $registry
     */
    private function writeToXlsx($isNew, $registry)
    {
        $filePath = 'organization-auto-prolongation-registry-' . Yii::$app->user->identity->organization->id . '.xlsx';

        if (!$isNew) {
            $oldRows = $this->readProcessedContractIdFromXlsx($filePath);
        }

        $writer = WriterFactory::create(Type::XLSX);
        $writer->openToFile(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $filePath);
        if ($isNew) {
            $writer->addRow(['id родительского договора', '№ родительского договора', 'дата родительского договора', 'Номер сертификата', 'Баланс сертификата', 'id дочернего договора', '№ дочернего договора', 'дата дочернего договора']);
        } else {
            $writer->addRows($oldRows);
        }

        foreach ($registry as $id => $item) {
            if (isset($item['childContractId']) && isset($item['childContractNumber']) && $item['childContractDate']) {
                $writer->addRow([$id, $item['contractNumber'], $item['date'], $item['certificateNumber'], $item['certificateBalance'], $item['childContractId'], $item['childContractNumber'], $item['childContractDate']]);
            } else {
                $writer->addRow([$id, $item['contractNumber'], $item['date'], $item['certificateNumber'], $item['certificateBalance'], 'договор продления обучения не создан, не достаточно баланса']);
            }
        }

        $writer->close();
    }

    /**
     * получить список автопролонгированных договоров с реестра
     *
     * @return array
     */
    public function getProcessedContractIdListFromRegistry()
    {
        $filePath = 'organization-auto-prolongation-registry-' . Yii::$app->user->identity->organization->id . '.xlsx';

        return ArrayHelper::getColumn($this->readProcessedContractIdFromXlsx($filePath), 0);
    }

    /**
     * считать все контракты обработанные для автопролонгации
     *
     * @param $filePath
     *
     * @return array
     */
    private function readProcessedContractIdFromXlsx($filePath)
    {
        $oldRows = [];

        if (file_exists(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $filePath)) {
            try {
                libxml_disable_entity_loader(false);
                $reader = ReaderFactory::create(Type::XLSX);
                $reader->open(Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . $filePath);

                foreach ($reader->getSheetIterator() as $sheet) {
                    foreach ($sheet->getRowIterator() as $row) {
                        if ($row[0] > 0 || $row[0] == 'id родительского договора') {
                            $oldRows[] = $row;
                        } else {
                            break;
                        }
                    }

                    break;
                }

                $reader->close();
            } catch (IOException $e) {
                return [];
            }
        }

        return $oldRows;
    }

    /**
     * записать логи автопролонгированных контрактов
     *
     * @param $contractDataListRows
     */
    private function writeLogs($contractDataListRows)
    {
        $contractAutoProlongedLogData = [];
        foreach ($contractDataListRows as $contractDataListRow) {
            $contractAutoProlongedLogData[] = [
                'organization_id' => $this->organizationId,
                'contract_parent_id' => $contractDataListRow['parent_id'],
                'contract_child_id' => Contracts::find()->select('id')->where(['parent_id' => $contractDataListRow['parent_id']])->one()->id,
                'group_id' => $contractDataListRow['group_id'] != $this->groupId ? $contractDataListRow['group_id'] : null,
                'auto_prolonged_at' => date('Y-m-d H:i:s'),
            ];
        }

        if (count($contractAutoProlongedLogData) < 1) {
            return;
        }

        Yii::$app->db->createCommand()->batchInsert(ContractAutoProlongedLog::tableName(), array_keys($contractAutoProlongedLogData[0]), $contractAutoProlongedLogData)->execute();
    }

    /**
     * получить кол-во автопролонгированных заявок или оферт
     *
     * @return int
     */
    public function getAutoProlongedCount()
    {
        $filePath = 'organization-auto-prolongation-registry-' . Yii::$app->user->identity->organization->id . '.xlsx';

        $childContractIdColumn = 4;
        $childContractList = ArrayHelper::getColumn($this->readProcessedContractIdFromXlsx($filePath), $childContractIdColumn);
        $count = 0;
        foreach ($childContractList as $childContract) {
            if (is_integer($childContract)) {
                $count++;
            }
        }

        return $count;
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
                'contractDate' => 'contracts.date',
                'contractNumber' => 'contracts.number',
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
     * получить путь к реестру созданных контрактов при автопролонгировании
     */
    public function getRegistryPath()
    {
        $filePath = Yii::$app->fileStorage->getFilesystem()->getAdapter()->getPathPrefix() . 'organization-auto-prolongation-registry-' . $this->organizationId . '.xlsx';

        if (file_exists($filePath)) {
            return $filePath;
        } else {
            return null;
        }
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