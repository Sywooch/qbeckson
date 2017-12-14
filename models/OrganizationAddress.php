<?php

namespace app\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "organization_address".
 *
 * @property integer $id
 * @property integer $organization_id
 * @property string $address
 * @property string $lat
 * @property string $lng
 * @property integer $status
 *
 * @property Organization $organization
 * @property ProgramAddressAssignment[] $programAddressAssignments
 * @property Programs[] $programs
 */
class OrganizationAddress extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_HIDDEN = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization_address';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['address', 'organization_id'], 'required'],
            [['organization_id', 'status'], 'integer'],
            [['address'], 'string', 'max' => 255],
            [['lat', 'lng'], 'safe'],
            [
                ['organization_id', 'address'], 'unique',
                'targetAttribute' => ['organization_id', 'address'],
                'message' => 'Вы уже указали такой адрес.'
            ],
            [
                ['organization_id'], 'exist', 'skipOnError' => true,
                'targetClass' => Organization::class,
                'targetAttribute' => ['organization_id' => 'id']
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address' => 'Адрес',
            'status' => 'Статус',
            'lat' => 'Lat',
            'lng' => 'Lng',
        ];
    }

    /**
     * @return array
     */
    public static function getAllMarks()
    {
        $programsTable = Programs::tableName();
        $assignmentTable = ProgramAddressAssignment::tableName();
        $addressTable = self::tableName();
        $marks = self::find()
            ->select([
                'age_from' => $programsTable . '.[[age_group_min]]',
                'age_to' => $programsTable . '.[[age_group_max]]',
                'title' => $programsTable . '.[[name]]',
                'description' => $programsTable . '.[[annotation]]',
                $programsTable . '.[[direction_id]]',
                $assignmentTable . '.[[program_id]]',
                $addressTable . '.[[lat]]',
                $addressTable . '.[[lng]]',
                $addressTable . '.[[address]]',
            ])
            ->innerJoin(
                $assignmentTable,
                "$assignmentTable.[[organization_address_id]] = $addressTable.[[organization_id]]"
            )
            ->innerJoin(
                $programsTable,
                "$programsTable.[[id]] = $assignmentTable.[[program_id]]"
            )
            ->where([$addressTable . '.[[status]]' => self::STATUS_ACTIVE])
            ->andWhere([
                'AND',
                ['<>', $addressTable . '.[[lng]]', ''],
                ['<>', $addressTable . '.[[lat]]', ''],
            ])
            ->asArray()
            ->all();

        foreach ($marks as $key => $mark) {
            $marks[$key]['geo_code'] = [$mark['lat'], $mark['lng']];
        }

        return $marks;
    }
}
