<?php

namespace app\models\mailing\repository;

use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\MailingStaticData;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class MailingListWithTasks extends MailingList
{
    /**
     * Подсчитать статус рассылки исходя из количества и состояния его задачь по рассылке
     * @return string  текст статуса, получаемый из MailingStaticData::getTaskStateLabels()
     * @throws Exception Задача принимает не более 3 состояний иначе ошибка
     */
    public function getState()
    {
        if ($this->getErrorTasks()->exists()) {
            return MailingStaticData::getTaskStateLabels()[MailingStaticData::TASK_STATUS_HAS_ERRORS];
        }
        $statusTotals = $this->getCountByStatuses();
        if (!$this->statusesIsValid($statusTotals)) {
            throw new Exception('среди задач рассылки обнаружены задачи с неожиданными статусами:'
                . array_shift($statusTotals)['status']);
        }
        if (count($statusTotals) < 2) {
            return MailingStaticData::getTaskStateLabels()[array_shift($statusTotals)['status']];
        }
        $map = ArrayHelper::map($statusTotals, 'status', 'count');
        $resultStatusTemplate = 'Выполнено %d из %d';

        return sprintf(
            $resultStatusTemplate,
            $map[MailingStaticData::TASK_STATUS_FINISH],
            $map[MailingStaticData::TASK_STATUS_FINISH] + $map[MailingStaticData::TASK_STATUS_CREATED]
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

    private function statusesIsValid(array $statuses): bool
    {
        $result = array_filter(
            $statuses,
            function ($val) {
                return (int)$val['status'] !== MailingStaticData::TASK_STATUS_FINISH
                    && (int)$val['status'] !== MailingStaticData::TASK_STATUS_CREATED;
            }
        );
        if (count($result) > 0) {
            return false;
        }

        return true;
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
