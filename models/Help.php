<?php

namespace app\models;

use app\behaviors\ArrayOrStringBehavior;
use app\models\search\HelpSearch;
use mPDF;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "help".
 *
 * @property integer $id
 * @property string $name
 * @property string $body
 * @property string $applied_to
 * @property integer $for_guest
 *
 * @property HelpUserAssignment[] $helpUserAssignments
 * @property User[] $users
 * @property int    $order_id [int(11)]  идентификатор для сортировки
 */
class Help extends \yii\db\ActiveRecord
{
    const SCENARIO_CHECK = 'check';
    const FOR_GUEST_YES = 1;
    const FOR_GUEST_NO = 0;

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
            [['order_id', 'for_guest'], 'integer'],
            ['for_guest', 'in', 'range' => [self::FOR_GUEST_YES, self::FOR_GUEST_NO]],
            ['checked', 'safe'],
            ['checked', 'required', 'on' => self::SCENARIO_CHECK, 'requiredValue' => 1, 'message' => false],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        $checkedLabel = Yii::$app->user->can('certificate') ? 'буду его учитывать в работе с ЛК.' : 'специалисты будут учитывать.';

        return [
            'id'         => 'ID',
            'order_id'   => 'номер для сортировки',
            'name'       => 'Название',
            'body'       => 'Текст',
            'applied_to' => 'Адресаты инструкций',
            'for_guest' => 'Не авторизованный',
            'checked'    => 'C разделом «<a target="_blank" href="' . \yii\helpers\Url::to(['site/manual', 'id' => $this->id]) . '">' . $this->name . '</a>» ознакомлен, ' . $checkedLabel,
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

    /**
     * @return bool
     */
    public function isOrderMin()
    {
        $orderMin = Help::find()->min('order_id');

        return $this->order_id == $orderMin;
    }

    /**
     * @return bool
     */
    public function isOrderMax()
    {
       $orderMax = Help::find()->max('order_id');

        return $this->order_id == $orderMax;
    }

    /**
     * получить список инструкций для указанной роли
     *
     * @param $userRole
     *
     * @return Help[]
     */
    public static function getListForUserRole($userRole)
    {
        $searchModel = new HelpSearch(['role' => $userRole]);
        if (Yii::$app->user->isGuest) {
            $searchModel->for_guest = $searchModel::FOR_GUEST_YES;
        }

        return $searchModel->search(null)->models;
    }

    /**
     * получить список инструкций для указанной роли в формате pdf
     *
     * @param $userRole
     *
     * @return mPDF
     */
    public static function getPdfForUserRole($userRole)
    {
        $helpList = self::getListForUserRole($userRole);

        $html = '';
        foreach ($helpList as $help) {
            $html .= '<h2>' . $help->name . '</h2><p>' . $help->body . '</p>';
        }
        $mpdf = new mPDF();
        $mpdf->WriteHTML($html);

        return $mpdf;
    }
}
