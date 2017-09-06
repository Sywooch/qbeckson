<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "contract_document".
 *
 * @property integer $id
 * @property string $contract_list
 * @property string $file
 * @property string $created_at
 *
 * @property Contracts $contract
 */
class ContractDocument extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'contract_document';
    }

    public function behaviors()
    {
        return [
            [
                'class' => TimestampBehavior::className(),
                'updatedAtAttribute' => false,
                'value' => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['contract_list', 'file'], 'string'],
            ['payer_id', 'integer'],
            [['created_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'file' => 'File',
            'contract_list' => 'Contract List',
            'created_at' => 'Created At',
        ];
    }

    public static function findByPayer($payer, $year, $month)
    {
        $query = static::find()
            ->where([
                'payer_id' => $payer->id,
                'YEAR(created_at)' => $year,
                'MONTH(created_at)' => $month,
            ]);

        return $query->one();
    }

    public static function createInstance($file)
    {
        $doc = new static([
            'payer_id' => Yii::$app->user->identity->payer->id,
            'file' => $file,
        ]);

        $searchContracts = new ContractsPayerInvoiceSearch(['payer_id' => Yii::$app->user->identity->payer->id]);
        $InvoiceProvider = $searchContracts->search(Yii::$app->request->queryParams);
        $doc->contract_list = join(',', ArrayHelper::map($InvoiceProvider->models, 'id', 'id'));

        return $doc->save();
    }
}
