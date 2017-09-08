<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "certificates".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property string $name
 * @property string $soname
 * @property string $phname
 * @property integer $payer_id
 * @property integer $actual
 * @property string $fio_child
 * @property string $fio_parent
 * @property integer $nominal_f
 * @property integer $balance_f
 * @property integer $rezerv_f
 * @property integer $nominal
 * @property integer $balance
 * @property integer $rezerv
 * @property integer $contracts
 * @property integer $directivity1
 * @property integer $directivity2
 * @property integer $directivity3
 * @property integer $directivity4
 * @property integer $directivity5
 * @property integer $directivity6
 *
 * @property User $user
 * @property Payers $payers
 * @property Payers $payer
 * @property Contracts[] $contracts0
 */
class Certificates extends \yii\db\ActiveRecord
{
    const FLAG_GROUP_HAS_NOT_BEEN_CHANGED = 10;

    const FLAG_GROUP_HAS_BEEN_CHANGED = 20;

    const TYPE_PF = 10;

    const TYPE_ACCOUNTING = 20;

    const SCENARIO_CERTIFICATE = 10;

    public $birthday;

    public $address;

    public $pasport_s;

    public $pasport_n;

    public $pasport_v;

    public $phone;

    public $contractCount;

    public $selectCertGroup;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'certificates';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_CERTIFICATE] = ['cert_group'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'soname', 'possible_cert_group',], 'required'],
            [['user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6', 'cert_group', 'pasport_s', 'pasport_n', 'pasport_v', 'phone', 'possible_cert_group', 'updated_cert_group'], 'integer'],
            [['nominal', 'nominal_f'], 'number', 'max' => 100000],
            [['number'], 'string', 'length' => [10, 10]],
            [['balance', 'balance_f', 'rezerv', 'rezerv_f'], 'number'],
            [['number'], 'unique'],
            [['fio_child', 'fio_parent', 'birthday', 'address'], 'string', 'max' => 255],
            [['name', 'soname', 'phname'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['contractCount'], 'safe'],
            [['selectCertGroup', 'possible_cert_group'], 'required', 'on' => self::SCENARIO_DEFAULT],
            ['selectCertGroup', 'in', 'range' => [self::TYPE_PF, self::TYPE_ACCOUNTING]],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'Сертификат',
            'user_id' => 'User ID',
            'number' => 'Номер сертификата',
            'payer_id' => 'Плательщик',
            'actual' => 'Актуальность',
            'fio_child' => 'ФИО ребенка',
            'name' => 'Имя',
            'soname' => 'Фамилия',
            'phname' => 'Отчество',
            'fio_parent' => 'ФИО родителя (законного представителя)',
            'nominal' => 'Номинал сертификата',
            'nominal_f' => 'Номинал сертификата на будущий период',
            'nominal_p' => 'Номинал сертификата на прошлый период',
            'balance' => 'Остаток сертификата',
            'balance_f' => 'Остаток сертификата на будущий период',
            'balance_p' => 'Остаток сертификата на прошлый период',
            'contracts' => 'Число заключенных договоров',
            'contractCount' => 'Число заключенных договоров',
            'directivity1' => 'Программ в "Техническая" направленность',
            'directivity2' => 'Программ в "Естественнонаучная" направленность',
            'directivity3' => 'Программ в "Физкультурно-спортивная" направленность',
            'directivity4' => 'Программ в "Художественная" направленность',
            'directivity5' => 'Программ в "Туристско-краеведческая" направленность',
            'directivity6' => 'Программ в "Социально-педагогическая" направленность',
            'certGroup.group' => 'Группа сертификата',
            'birthday' => 'Дата рождения',
            'address' => 'Адрес места жительства',
            'pasport_s' => 'серия',
            'pasport_n' => 'номер',
            'pasport_v' => 'выдан',
            'phone' => 'Телефон',
            'rezerv' => 'Зарезервированно на оплату программ',
            'rezerv_f' => 'Зарезервированно на оплату программ на будущий период',
            'rezerv_p' => 'Зарезервированно на оплату программ на прошлый период',
            'cert_group' => 'Группа сертификата',
            'payer' => 'Плательщик',
            'selectCertGroup' => 'Тип сертификата',
            'possible_cert_group' => 'Группа сертификата',
        ];
    }

    public function afterFind()
    {
        parent::afterFind();
        if ($this->certGroup->is_special > 0) {
            $this->selectCertGroup = self::TYPE_ACCOUNTING;
        } else {
            $this->selectCertGroup = self::TYPE_PF;
        }
    }

    public function setNominals()
    {
        if (!empty($this->selectCertGroup) && $this->selectCertGroup == self::TYPE_PF) {
            $this->cert_group = $this->possible_cert_group;
            $this->nominal = $this->possibleCertGroup->nominal;
            $this->nominal_f = $this->possibleCertGroup->nominal_f;
            $this->balance = $this->nominal;
            $this->balance_f = $this->nominal_f;
        } elseif (!empty($this->selectCertGroup) && $certGroup = $this->payers->getCertGroups(1)->one()) {
            $this->cert_group = $certGroup->id;
            $this->nominal = 0;
            $this->nominal_f = 0;
            $this->balance = 0;
            $this->balance_f = 0;
        }
    }

    public function changeBalance($contract)
    {
        if ($contract->period === Contracts::CURRENT_REALIZATION_PERIOD) {
            $this->balance += $contract->rezerv;
            $this->rezerv -= $contract->rezerv;
        } elseif ($contract->period === Contracts::FUTURE_REALIZATION_PERIOD) {
            $this->balance_f += $contract->rezerv;
            $this->rezerv_f -= $contract->rezerv;
        } elseif ($contract->period === Contracts::PAST_REALIZATION_PERIOD) {
            $this->balance_p += $contract->rezerv;
            $this->rezerv_p -= $contract->rezerv;
        }

        return $this->save();
    }

    public function getCertGroupTypes()
    {
        return [
            self::TYPE_PF => 'Сертификат ПФ',
            self::TYPE_ACCOUNTING => 'Сертификат учёта',
        ];
    }

    public function getCountActiveContracts()
    {
        $query = Contracts::find()
            ->where(['certificate_id' => $this->id])
            ->andWhere(['=', 'status', Contracts::STATUS_ACTIVE]);

        return $query->count();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
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
    public function getPayers()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts0()
    {
        return $this->hasMany(Contracts::className(), ['certificate_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertGroup()
    {
        return $this->hasOne(CertGroup::className(), ['id' => 'cert_group']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPossibleCertGroup()
    {
        return $this->hasOne(CertGroup::className(), ['id' => 'possible_cert_group']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorites::className(), ['certificate_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreviuses()
    {
        return $this->hasMany(Previus::className(), ['certificate_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertificateGroupQueues($certificateId = null, $certGroupId = null)
    {
        $relation = $this->hasMany(CertificateGroupQueue::className(), ['certificate_id' => 'id']);

        $relation->andFilterWhere([
            'certificate_id' => $certificateId,
            'cert_group_id' => $certGroupId,
        ]);

        return $relation;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCertGroupsQueue()
    {
        return $this->hasMany(CertGroup::className(), ['id' => 'cert_group_id'])->viaTable('certificate_group_queue', ['certificate_id' => 'id']);
    }

    public function getCanChangeGroup()
    {
        if ($this->nominal == $this->balance && $this->nominal_f == $this->balance_f) {
            return true;
        }

        return false;
    }

    public function getPossibleGroupList()
    {
        return [$this->possibleCertGroup, $this->payers->getCertGroups(1)->one()];
    }

    public function getTextType($viceVersa = false)
    {
        $arrayTypes = ['Сертификат учёта', $this->possibleCertGroup->group];
        if ($viceVersa === true) {
            $arrayTypes = array_reverse($arrayTypes);
        }

        return $this->certGroup->is_special > 0 ? $arrayTypes[0] : $arrayTypes[1];
    }

    public function changeCertGroup()
    {
        if ($this->certGroup->is_special > 0) {
            $certGroup = CertGroup::findOne($this->oldAttributes['cert_group']);
            self::updateGroupQueue($certGroup);
            $this->nominal = 0;
            $this->balance = 0;

            return self::FLAG_GROUP_HAS_NOT_BEEN_CHANGED;
        }

        if ($this->certGroup->hasVacancy()) {
            $this->nominal = $this->certGroup->nominal;
            $this->balance = $this->nominal;
            $this->updated_cert_group = time();

            return self::FLAG_GROUP_HAS_BEEN_CHANGED;
        } else {
            $this->insertIntoGroupQueue();
            $this->cert_group = $this->oldAttributes['cert_group'];
        }

        return self::FLAG_GROUP_HAS_NOT_BEEN_CHANGED;
    }

    public static function updateGroupQueue($certGroup)
    {
        $model = CertificateGroupQueue::find()
            ->where([
                'cert_group_id' => $certGroup->id,
            ])
            ->orderBy('created_at ASC')
            ->one();

        if ($model) {
            return $model->removeFromCertQueue();
        }

        return false;
    }

    public function insertIntoGroupQueue()
    {
        if (!$this->getCertificateGroupQueues($this->id, $this->cert_group)->count()) {
            $this->link('certGroupsQueue', $this->certGroup, ['created_at' => time()]);
            $number = CertificateGroupQueue::getCountByCertGroup($this->cert_group, time()) + 1;
            Yii::$app->session->setFlash('danger', 'К сожалению, на текущий момент достигнут лимит предоставления действующих сертификатов персонифицированного финансирования. Ваш сертификат будет переведен в вид сертификата персонифицированного финансирования в порядке "живой" очереди, после того, как число доступных сертификатов увеличится. Ваш номер в очереди - ' . $number);
        }
    }

    public static function getCountCertGroup($certGroupId)
    {
        $query = static::find()
            ->where(['cert_group' => $certGroupId]);

        return $query->count();
    }

    public static function getCountCertificates($payerId = null)
    {
        $query = static::find()
            ->joinWith(['payers'])
            ->where(['actual' => 1])
            ->andWhere('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

        $query->andFilterWhere(['payer_id' => $payerId]);

        return $query->count();
    }

    public static function getSumCertificates($payerId = null)
    {
        $query = static::find()
            ->joinWith(['payers'])
            ->where(['actual' => 1])
            ->andWhere('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

        if (!empty($payerId)) {
            $query->andWhere(['payer_id' => $payerId]);
        }

        return $query->sum('nominal');
    }

    /**
     * @deprecated
     */
    public function getSumContractes($payer_id)
    {
        $query = Certificates::find();

        $query->where(['payer_id' => $payer_id]);

        return $query->sum('contracts');
    }

    /**
     * @deprecated
     * Use relation in app\models\UserIdentity instead
     */
    public function getCertificates()
    {

        $query = Certificates::find();

        if (!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }
}
