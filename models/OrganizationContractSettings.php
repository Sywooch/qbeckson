<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "organization_contract_settings".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property string $organization_first_ending
 * @property string $organization_second_ending
 * @property string $director_name_ending
 * @property string $document_type
 */
class OrganizationContractSettings extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_contract_settings';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['organization_id'], 'required'],
            [['organization_id'], 'integer'],
            [
                ['organization_first_ending', 'organization_second_ending', 'director_name_ending'],
                'string', 'max' => 10
            ],
            [['document_type'], 'string', 'max' => 20],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'organization_id' => 'Organization ID',
            'organization_first_ending' => 'Organization First Ending',
            'organization_second_ending' => 'Organization Second Ending',
            'director_name_ending' => 'Director Name Ending',
            'document_type' => 'Document Type',
        ];
    }
}
