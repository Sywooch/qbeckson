<?php

namespace app\models\statics;

use app\models\UserIdentity;
use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "directory_program_activity".
 *
 * @property integer $id
 * @property integer $direction_id
 * @property integer $user_id
 * @property string $name
 * @property integer $status
 *
 * @property DirectoryProgramDirection $direction
 * @property UserIdentity $user
 */
class DirectoryProgramActivity extends ActiveRecord
{
    const STATUS_ACTIVE = 1;
    const STATUS_NEW = 2;
    const STATUS_DELETED = 3;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'directory_program_activity';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['direction_id'], 'required'],
            [['direction_id', 'user_id', 'status'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [
                ['direction_id'], 'exist', 'skipOnError' => true,
                'targetClass' => DirectoryProgramDirection::class,
                'targetAttribute' => ['direction_id' => 'id']
            ],
            [
                ['user_id'], 'exist', 'skipOnError' => true,
                'targetClass' => UserIdentity::class,
                'targetAttribute' => ['user_id' => 'id']
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
            'direction_id' => 'Direction ID',
            'user_id' => 'User ID',
            'name' => 'Name',
            'status' => 'Status',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDirection()
    {
        return $this->hasOne(DirectoryProgramDirection::class, ['id' => 'direction_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(UserIdentity::class, ['id' => 'user_id']);
    }

    /**
     * @param $directionId
     * @return array|ActiveRecord[]|DirectoryProgramActivity[]
     */
    public static function findAllActiveActivitiesByDirection($directionId)
    {
        $query = static::find()->andWhere([
            'direction_id' => $directionId
        ]);

        if (!Yii::$app->user->isGuest) {
            $query->andWhere([
                'status' => DirectoryProgramActivity::STATUS_ACTIVE
            ]);
        } else {
            $query->andWhere([
                'OR',
                ['status' => DirectoryProgramActivity::STATUS_ACTIVE],
                ['user_id' => Yii::$app->user->id]
            ]);
        }

        return $query->all();
    }
}
