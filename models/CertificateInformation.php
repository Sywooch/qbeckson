<?php

namespace app\models;

use trntv\filekit\behaviors\UploadBehavior;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "certificate_information".
 *
 * @property integer $id
 * @property integer $payer_id
 * @property string $children_category
 * @property string $organization_name
 * @property string $work_time
 * @property string $full_name
 * @property string $rules
 * @property string $statement_path
 * @property string $statement_base_url
 *
 * @property Payers $payer
 */
class CertificateInformation extends ActiveRecord
{
    public $statementFile;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificate_information';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['children_category', 'organization_name', 'work_time', 'full_name', 'rules'], 'required'],
            [['payer_id'], 'integer'],
            [['rules'], 'string'],
            [[
                'children_category', 'organization_name',
                'work_time', 'full_name', 'statement_path', 'statement_base_url'
            ], 'string', 'max' => 255],
            [
                ['payer_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']
            ],
            ['statementFile', 'safe'],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        return [
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => 'payer_id',
                    ActiveRecord::EVENT_BEFORE_UPDATE => 'payer_id',
                ],
                'value' => function () {
                    /** @var UserIdentity $user */
                    $user = Yii::$app->user->getIdentity();
                    return $user->payer->id;
                },
            ],
            [
                'class' => UploadBehavior::class,
                'multiple' => false,
                'pathAttribute' => 'statement_path',
                'baseUrlAttribute' => 'statement_base_url',
                'attribute' => 'statementFile',
            ]
        ];
    }

    /**
     * @return null|string
     */
    public function getStatementFile()
    {
        return $this->statement_base_url ? $this->statement_base_url . '/' . $this->statement_path : null;
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'payer_id' => 'Плательщик',
            'children_category' => 'Категория детей, получающих сертификаты',
            'organization_name' => 'Наименование организации/органа',
            'work_time' => 'Режима работы',
            'full_name' => 'ФИО ответственного лица',
            'rules' => 'Для получения сертификата необходимо(описание процедуры и документов)',
            'statement_path' => 'Statement Path',
            'statement_base_url' => 'Statement Base Url',
            'statementFile' => 'Пример заявления',
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
     * @param null $municipalityId
     * @return CertificateInformation[]|array|ActiveRecord[]
     */
    public static function findOneByMunicipality($municipalityId)
    {
        return CertificateInformation::find()
            ->joinWith([
                'payer'
            ])
            ->andWhere([
                'payers.mun' => $municipalityId
            ])
            ->one();
    }
}
