<?php

namespace app\models\mailing\repository;

use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\MailingStaticData;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class MailingListWithTasks extends MailingList
{

    private $state;

    /**
     * Подсчитать статус рассылки исходя из количества и состояний его задачь по рассылке
     * @return string  текст статуса, получаемый из MailingStaticData::getTaskStateLabels()
     * @throws Exception Задача принимает не более 3 состояний иначе ошибка
     */
    public function getState()
    {
        if ($this->state) {
            return $this->state;
        }

        $stateTotals = $this->getCountByStatuses();
        if (count($notValidStates = $this->getNotValidState($stateTotals)) > 0) {
            throw new Exception('среди задач рассылки обнаружены задачи с неожиданными статусами:'
                . print_r($notValidStates, true));
        }
        if (count($stateTotals) === 1) {
            $this->state = MailingStaticData::getTaskStateLabels()[array_shift($stateTotals)['status']];
        } else {
            $this->state = $this->getComplexState($stateTotals);
        }

        return $this->state;
    }

    /**
     * Возвращает количество задач группированых по их состояниям
     * @return array
     */
    public function getCountByStatuses(): array
    {
        return $this
            ->getMailTasks()
            ->select(
                [
                    'status' => 'status',
                    'count' => new Expression('COUNT([[status]])')
                ]
            )
            ->groupBy(['status'])->asArray()->all();
    }

    /**
     * Возвращает массив неожиданных состояний
     *
     * @param array $statuses
     *
     * @return array
     */
    private function getNotValidState(array $statuses): array
    {
        return array_filter(
            $statuses,
            function ($val) {
                return (int)$val['status'] !== MailingStaticData::TASK_STATUS_FINISH
                    && (int)$val['status'] !== MailingStaticData::TASK_STATUS_CREATED
                    && (int)$val['status'] !== MailingStaticData::TASK_STATUS_HAS_ERRORS;
            }
        );
    }

    /**
     * возвращает строчное представление состояния рассылки на основании состояний ее задачь задачь
     *
     * @param $stateTotals array
     *
     * @return string
     */
    private function getComplexState(array $stateTotals)
    {
        $resultStatusTemplate = 'Выполнено %d из %d, ошибок %d';
        $map = ArrayHelper::map($stateTotals, 'status', 'count');

        return sprintf(
            $resultStatusTemplate,
            $map[MailingStaticData::TASK_STATUS_FINISH] ?? 0,
            $map[MailingStaticData::TASK_STATUS_FINISH] ?? 0
            + $map[MailingStaticData::TASK_STATUS_CREATED] ?? 0,
            $map[MailingStaticData::TASK_STATUS_HAS_ERRORS] ?? 0
        );
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getErrorTasks()
    {
        return $this->getMailTasks()->andWhere(['status' => MailingStaticData::TASK_STATUS_HAS_ERRORS]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinishedTasks()
    {
        return $this->getMailTasks()->andWhere(['status' => MailingStaticData::TASK_STATUS_FINISH]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCreatedTasks()
    {
        return $this->getMailTasks()->andWhere(['status' => MailingStaticData::TASK_STATUS_CREATED]);
    }

    public function attributeLabels()
    {
        return MailingStaticData::attributeListLabels();
    }
}
