<?php

namespace app\models;

use Yii;
use app\behaviors\ArrayOrStringBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "help".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 * @property string $applied_to
 *
 * @property HelpUserAssignment[] $helpUserAssignments
 * @property User[] $users
 */
class Help extends \yii\db\ActiveRecord
{
    const SCENARIO_CHECK = 'check';

    public $checked = 0;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'help';
    }

    public function behaviors()
    {
        return [
            'array2string' => [
                'class' => ArrayOrStringBehavior::className(),
                'attributes1' => ['applied_to'],
                'attributes2' => ['applied_to'],
                'serialize' => false,
            ],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CHECK] = ['checked'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['body', 'name'], 'required'],
            [['body', 'applied_to'], 'string'],
            [['name'], 'string', 'max' => 255],
            ['checked', 'safe'],
            ['checked', 'required', 'on' => self::SCENARIO_CHECK, 'requiredValue' => 1, 'message' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'body' => 'Текст',
            'applied_to' => 'Кто должен поставить "галочки" о прочтении',
            'checked' => 'Раздел «<a href="/">' . $this->name . '</a>» прочитан. Специалисты, имеющие доступ в личный кабинет, учитывают его содержание при работе с системой.',
        ];
    }

    public function saveCheckes()
    {
        if ($this->checked > 0 && empty($this->user)) {
            $this->link('users', Yii::$app->user->identity);
        }
    }

    public function getCheckes()
    {
        if (!empty($this->user)) {
            $this->checked = 1;
        }
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelpUserAssignments()
    {
        return $this->hasMany(HelpUserAssignment::className(), ['help_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])
            ->viaTable('help_user_assignment', ['help_id' => 'id'])
            ->andWhere(['`user`.id' => Yii::$app->user->id])
            ->orderBy('`user`.id');
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable('help_user_assignment', ['help_id' => 'id']);
    }

    /*public static function getCountUncheckedMans($role)
    {
        $query = static::find()
            ->joinWith('helpUserAssignments')
            ->andWhere('`help_user_assignment`.user_id IS NULL')
            ->andWhere('`help_user_assignment`.user_id != ' . Yii::$app->user->id)
            ->andWhere(new Expression('FIND_IN_SET(:role, applied_to)'))
            ->addParams([':role' => $role->name]);
        print_r($query->prepare(Yii::$app->db->queryBuilder)->createCommand()->rawSql);exit;
        return $query->count();
    }*/

    public static function getCountUncheckedMans($role)
    {
        $subQuery = (new \yii\db\Query())
            ->select('help_id')
            ->from('help_user_assignment')
            ->where(['user_id' => Yii::$app->user->id])
            ->andWhere('`help_user_assignment`.help_id = `help`.id');

        $query = static::find()
            ->andWhere(['not exists', $subQuery])
            ->andWhere(new Expression('FIND_IN_SET(:role, applied_to)'))
            ->addParams([':role' => $role->name]);

        return $query->count();
    }
}
