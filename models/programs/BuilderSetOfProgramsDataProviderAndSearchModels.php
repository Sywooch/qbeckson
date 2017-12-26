<?php

namespace app\models\programs;


use app\components\periodicField\PeriodicFieldAR;
use app\components\SearchBuilder;
use app\models\GroupsSearch;
use app\models\ProgrammeModuleSearch;
use app\models\Programs;
use app\models\search\ProgramsSearch;
use yii\db\Expression;
use yii\db\QueryInterface;

/**
 * Class BuilderSetOfProgramsDataProviderAndSearchModels
 * @package app\models\programs
 */
class BuilderSetOfProgramsDataProviderAndSearchModels extends SearchBuilder
{
    /**
     *
     */
    const DEFAULT_HOURS_RANGE = '0,2000';
    /**
     *
     */
    const DEFAULT_LIMIT = '0,10000';
    /**
     *
     */
    const DEFAULT_RATING = '0,100';
    /**
     *
     */
    const INFINITY_PAGE_SIZE = 999999;

    /**
     * @return array
     */
    public function getProvidersSetForPersonalOperator(): array
    {
        $searchYearsAll = new ProgrammeModuleSearch();
        $yearsAllProvider = $searchYearsAll->search($this->queryParams);
        $searchGroupsAll = new GroupsSearch();
        $groupsAllProvider = $searchGroupsAll->search($this->queryParams);

        return $this->addDataProvider('yearsAllProvider', $yearsAllProvider)
            ->addDataProvider('groupsAllProvider', $groupsAllProvider)
            ->needOpenProgramsWithAll()
            ->needWaitProgramsWithAll()
            ->needNewWaitProgramsWithAll()
            ->needAfterRefuseWaitProgramsWithAll()
            ->needClosedProgramsWithAll()
            ->getResult();
    }

    public function needClosedProgramsWithAll(): self
    {
        $searchClosedPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_DENIED],
            'hours' => self::DEFAULT_HOURS_RANGE,
            'modelName' => ProgramsSearch::MODEL_CLOSED,
        ]);
        $closedProgramsProvider = $searchClosedPrograms->search($this->queryParams);
        $allClosedProgramsProvider = $searchClosedPrograms->search(
            $this->queryParams,
            self::INFINITY_PAGE_SIZE
        );

        return $this->addDataProvider('closedProgramsProvider', $closedProgramsProvider)
            ->addDataProvider('allClosedProgramsProvider', $allClosedProgramsProvider)
            ->addSearchModel('searchClosedPrograms', $searchClosedPrograms);
    }

    /**
     * @return static
     */
    public function needAfterRefuseWaitProgramsWithAll(): self
    {
        $queryModifier = function (QueryInterface $query) {
            return $query->andWhere([
                'exists',
                PeriodicFieldAR::find()
                    ->andWhere(['table_name' => Programs::tableName()])
                    ->andWhere(['record_id' => new Expression(
                        Programs::getTableSchema()->fullName . '.`id`'
                    )])
                    ->andWhere(['field_name' => 'verification'])
                    ->andWhere(['value' => Programs::VERIFICATION_DENIED])
            ]);
        };

        $searchNewWaitPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'open' => 0,
            'hours' => self::DEFAULT_HOURS_RANGE,
            'modelName' => ProgramsSearch::MODEL_WAIT,
        ]);
        $waitNewProgramsProvider = $searchNewWaitPrograms->search($this->queryParams);
        $allNewWaitProgramsProvider = $searchNewWaitPrograms->search(
            $this->queryParams,
            self::INFINITY_PAGE_SIZE
        );

        $queryModifier($waitNewProgramsProvider->query);
        $queryModifier($allNewWaitProgramsProvider->query);

        return $this->addDataProvider('afterRefuseWaitProgramsProvider', $waitNewProgramsProvider)
            ->addDataProvider('allAfterRefuseWaitProgramsProvider', $allNewWaitProgramsProvider)
            ->addSearchModel('searchAfterRefuseWaitPrograms', $searchNewWaitPrograms);
    }


    /**
     * @return static
     */
    public function needNewWaitProgramsWithAll(): self
    {
        $queryModifier = function (QueryInterface $query) {
            return $query->andWhere([
                'not exists',
                PeriodicFieldAR::find()
                    ->andWhere(['table_name' => Programs::tableName()])
                    ->andWhere(['record_id' => new Expression(
                        Programs::getTableSchema()->fullName . '.`id`'
                    )])
                    ->andWhere(['field_name' => 'verification'])
                    ->andWhere(['value' => [
                        Programs::VERIFICATION_DONE,
                        Programs::VERIFICATION_DENIED]
                    ])
            ]);
        };

        $searchNewWaitPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'open' => 0,
            'hours' => self::DEFAULT_HOURS_RANGE,
            'modelName' => ProgramsSearch::MODEL_WAIT,
        ]);
        $waitNewProgramsProvider = $searchNewWaitPrograms->search($this->queryParams);
        $allNewWaitProgramsProvider = $searchNewWaitPrograms->search(
            $this->queryParams,
            self::INFINITY_PAGE_SIZE
        );

        $queryModifier($waitNewProgramsProvider->query);
        $queryModifier($allNewWaitProgramsProvider->query);

        return $this->addDataProvider('newWaitProgramsProvider', $waitNewProgramsProvider)
            ->addDataProvider('allNewWaitProgramsProvider', $allNewWaitProgramsProvider)
            ->addSearchModel('searchNewWaitPrograms', $searchNewWaitPrograms);
    }

    /**
     * @return static
     */
    public function needWaitProgramsWithAll(): self
    {
        $searchWaitPrograms = new ProgramsSearch([
            'verification' => [Programs::VERIFICATION_UNDEFINED, Programs::VERIFICATION_WAIT],
            'open' => 0,
            'hours' => self::DEFAULT_HOURS_RANGE,
            'modelName' => ProgramsSearch::MODEL_WAIT,
        ]);
        $waitProgramsProvider = $searchWaitPrograms->search($this->queryParams);
        $allWaitProgramsProvider = $searchWaitPrograms->search(
            $this->queryParams,
            self::INFINITY_PAGE_SIZE
        );

        return $this->addDataProvider('waitProgramsProvider', $waitProgramsProvider)
            ->addDataProvider('allWaitProgramsProvider', $allWaitProgramsProvider)
            ->addSearchModel('searchWaitPrograms', $searchWaitPrograms);
    }

    /**
     * @return static
     */
    public function needOpenProgramsWithAll(): self
    {

        $searchOpenPrograms = new ProgramsSearch([
            'verification' => Programs::VERIFICATION_DONE,
            'hours' => self::DEFAULT_HOURS_RANGE,
            'limit' => self::DEFAULT_LIMIT,
            'rating' => self::DEFAULT_RATING,
            'modelName' => ProgramsSearch::MODEL_OPEN,
        ]);
        $openProgramsProvider = $searchOpenPrograms->search($this->queryParams);
        $allOpenProgramsProvider = $searchOpenPrograms->search(
            $this->queryParams,
            self::INFINITY_PAGE_SIZE
        );

        return $this->addDataProvider('openProgramsProvider', $openProgramsProvider)
            ->addDataProvider('allOpenProgramsProvider', $allOpenProgramsProvider)
            ->addSearchModel('searchOpenPrograms', $searchOpenPrograms);
    }
}
