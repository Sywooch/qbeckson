<?php

namespace app\models\mailing\services;

use app\models\mailing\activeRecord\MailingList;
use app\models\mailing\activeRecord\MailTask;
use app\models\mailing\MailingStaticData;
use app\models\Operators;
use app\models\Organization;
use app\models\Payers;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\validators\InlineValidator;

/**
 * Class MailingBuilder
 * @package app\models\mailing\services
 * @property array $munsToSelect
 * @property array $targetsToSelect
 * @property Operators $operator
 */
class MailingBuilder extends MailingActions
{

    public $subject;
    public $message;
    public $target;
    public $mun;

    public $operator;

    /**
     * @param Operators $operators
     *
     * @return MailingBuilder
     */
    public static function getBuilderWithOperator(Operators $operators)
    {
        $instance = new self([
            'mailingList' => new MailingList(),
            'operator' => $operators,
        ]);
        $instance->message = $instance->getTemplateMessage();

        return $instance;
    }

    /**
     * @return string
     */
    public function getTemplateMessage()
    {
        return <<<TEXT
        
        
----------------
Пожалуйста, не отвечайте на данное информационное письмо, для обратной связи с оператором существуют другие способы.
TEXT;
    }

    /**
     * @return array
     */
    public function rules()
    {
        return array_merge(
            parent::rules(),
            [
                [['mun', 'target', 'message', 'subject'], 'required'],
                ['subject', 'string', 'max' => 40],
                ['mun', 'munValidate'],
            ]
        );
    }

    public function munValidate($attribute, $params, InlineValidator $validator)
    {
        $muns = $this->operator->getMun()->select(['id'])->column();
        if (array_diff($this->mun, $muns)) {
            $this->addError($attribute, 'Обнаружены чужие муниципалитеты');
        }
    }

    public function attributeLabels()
    {
        return MailingStaticData::attributeListLabels();
    }

    /**
     * @return array
     */
    public function getMunsToSelect(): array
    {
        return ArrayHelper::map($this->operator->mun, 'id', 'name');
    }

    public function getTargetsToSelect(): array
    {
        return [
            MailingStaticData::TARGET_ORGANIZATION => 'Организациям',
            MailingStaticData::TARGET_PAYER => 'Плательщикам',
        ];
    }

    /**
     * Все манипуляции внутри этой функции происходят в трансзакции, можно прервать трансзакцию из нутри.
     * для успешного завершения вернуть true
     *
     * @param \Closure $transactionTerminator
     * @param bool $validate
     *
     * @return bool
     */
    public function saveActions(\Closure $transactionTerminator, bool $validate): bool
    {

        return $this->fillMailingList()
            && $this->mailingList->save()
            && $this->createMultiMailingTask();
    }

    public function fillMailingList()
    {
        $this->mailingList->subject = $this->subject;
        $this->mailingList->message = $this->message;

        return true;
    }

    /**
     * Берет email  и user_id у плательщиков и организаций и создает строики в MailTask
     * @return int
     * @throws \yii\db\Exception
     */
    public function createMultiMailingTask()
    {
        $selectQuery = $this->getUnionQueryFromMunsTarget($this->target, null);

        return \Yii::$app->db->createCommand()
            ->insert(MailTask::tableName(), $selectQuery)->execute();
    }

    /**
     *
     * @param array $target ID типа источника данных, (плательщик или организация)
     * @param Query|null $query запрос из предыдущей итерации, с которым делается объединение
     *
     * @return Query
     */
    public function getUnionQueryFromMunsTarget(array $target, Query $query = null)
    {
        $currentTarget = array_shift($target);
        $addQuery = ((int)$currentTarget === MailingStaticData::TARGET_ORGANIZATION
            ? $this->getOrganisationQuery($this->mailingList->id, $this->mun)
            : $this->getPayerQuery($this->mailingList->id, $this->mun));

        if (is_null($query)) {
            $resultQuery = $addQuery;
        } else {
            $resultQuery = $query->union($addQuery);
        }
        if (count($target) > 0) {
            return $this->getUnionQueryFromMunsTarget($target, $resultQuery);
        }

        return $resultQuery;
    }

    /**
     * @param $mailing_list_id int сохраненый в текущей трансзакции id
     * @param $muns  array   массив муниципалитетов
     *
     * @return Query  подзапрос по организациям или часть подзапроса,
     * использущющийся в insert конструкции для построения списка задачь рассылки
     */
    public function getOrganisationQuery($mailing_list_id, $muns)
    {
        return Organization::find()
            ->select(
                $this->getFieldsForSelectQuery($mailing_list_id, MailingStaticData::TARGET_ORGANIZATION)
            )
            ->where(['mun' => $muns]);
    }

    /**
     * создает массив полей для select метода Query
     *
     * @param $mailing_list_id   int сохраненый в текущей трансзакции id
     * @param $targetType  int идентификатор типа источника данных, плательщик или организация
     *
     * @return array
     */
    public function getFieldsForSelectQuery($mailing_list_id, $targetType): array
    {
        return [
            'mailing_list_id' => (new Expression('"' . $mailing_list_id . '"')),
            'status' => (new Expression('"' . MailingStaticData::TASK_STATUS_CREATED . '"')),
            'target_user_id' => 'user_id',
            'updated_at' => new Expression('"' . time() . '"'),
            'email' => 'email',
            'target_type' => (new Expression('"' . $targetType . '"'))
        ];
    }

    /**
     * @param $mailing_list_id int сохраненый в текущей трансзакции id
     * @param $muns  array   массив муниципалитетов
     *
     * @return Query  подзапрос по плательщикам или часть подзапроса,
     * использущющийся в insert конструкции для построения списка задачь рассылки
     */
    public function getPayerQuery($mailing_list_id, $muns)
    {
        return Payers::find()
            ->select(
                $this->getFieldsForSelectQuery($mailing_list_id, MailingStaticData::TARGET_PAYER)
            )
            ->where(['mun' => $muns]);
    }

}
