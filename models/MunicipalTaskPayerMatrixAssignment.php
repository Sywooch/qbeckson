<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "municipal_task_payer_matrix_assignment".
 *
 * @property integer $payer_id
 * @property integer $matrix_id
 * @property integer $can_be_chosen
 * @property integer $number
 * @property integer $number_type
 *
 * @property Payers $payer
 * @property MunicipalTaskMatrix $matrix
 */
class MunicipalTaskPayerMatrixAssignment extends \yii\db\ActiveRecord
{
    const CERTIFICATE_TYPE_PF = 1;
    const CERTIFICATE_TYPE_AC = 2;

    const NUMBER_TYPE_SERVICE = 10;
    const NUMBER_TYPE_HOURS = 20;

    private $_id;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'municipal_task_payer_matrix_assignment';
    }

    public function init()
    {
        parent::init();
        $this->setId();
    }

    public function afterFind()
    {
        parent::afterFind();
        $this->setId();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['payer_id', 'matrix_id', 'certificate_type'], 'required'],
            [['payer_id', 'matrix_id', 'can_be_chosen', 'number', 'number_type', 'certificate_type'], 'integer'],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['matrix_id'], 'exist', 'skipOnError' => true, 'targetClass' => MunicipalTaskMatrix::className(), 'targetAttribute' => ['matrix_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'payer_id' => 'Payer ID',
            'matrix_id' => 'Matrix ID',
            'can_be_chosen' => 'Доступно для выбора',
            'number' => 'Количество',
            'number_type' => 'Услуги или часы',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPayer()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMatrix()
    {
        return $this->hasOne(MunicipalTaskMatrix::className(), ['id' => 'matrix_id']);
    }

    public function getId()
    {
        return $this->_id;
    }

    public function setId()
    {
        $this->_id = join('_', [$this->payer_id, $this->matrix_id, $this->certificate_type]);
    }

    public static function getTypes()
    {
        return [
            static::CERTIFICATE_TYPE_PF => 'Сертификаты ПФ',
            static::CERTIFICATE_TYPE_AC => 'Сертификаты учёта',
        ];
    }

    public static function getPrefixes()
    {
        return [
            static::CERTIFICATE_TYPE_PF => 'pf',
            static::CERTIFICATE_TYPE_AC => 'ac',
        ];
    }

    public static function getNumberTypes()
    {
        return [
            static::NUMBER_TYPE_SERVICE => 'Услуги',
            static::NUMBER_TYPE_HOURS => 'Часы',
        ];
    }

    public static function findByPayerId($payerId)
    {
        $models = static::find()
            ->where(['payer_id' => $payerId])
            ->all();

        return ArrayHelper::index($models, 'id');
    }
}
