<?php
/**
 * Created by PhpStorm.
 * User: student4
 * Date: 27.10.2017
 * Time: 16:56
 */

namespace app\models\mailing\services;

use app\models\mailing\MailingStaticData;
use app\models\mailing\repository\MailingListRepository;
use app\models\Operators;
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

    public static function getBuilderWithOperator(Operators $operators)
    {
        $instance = new self([
            'mailingList' => new MailingListRepository(),
            'operator' => $operators,
        ]);
        $instance->message = $instance->getTemplateMessage();

        return $instance;
    }

    public function getTemplateMessage()
    {
        return <<<TEXT
        
        
----------------
Пожалуйста, не отвечайте на данное информационное письмо, для обратной связи с оператором существуют другие способы.
TEXT;
    }

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
        return array_merge(parent::attributeLabels(), [

        ]);
    }

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
        // TODO: Implement saveActions() method.
    }

}
