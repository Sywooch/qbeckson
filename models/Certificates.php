<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "certificates".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $number
 * @property integer $payer_id
 * @property integer $actual
 * @property string $fio_child
 * @property string $fio_parent
 * @property integer $nominal
 * @property integer $balance
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
 * @property Contracts[] $contracts0
 */
class Certificates extends \yii\db\ActiveRecord
{
    const FLAG_GROUP_HAS_NOT_BEEN_CHANGED = 10;

    const FLAG_GROUP_HAS_BEEN_CHANGED = 20;

    const TYPE_PF = 10;

    const TYPE_ACCOUNTING = 20;

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

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'soname'], 'required'],
            [['fio_parent'], 'required'],
            [['user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6', 'cert_group', 'pasport_s', 'pasport_n', 'pasport_v', 'phone', 'possible_cert_group'], 'integer'],
            [['nominal'], 'number', 'max' => 100000],
            [['number'], 'string', 'length' => [10, 10]],
            [['balance', 'rezerv'], 'number'],
            [['number'], 'unique'],
            [['fio_child', 'fio_parent', 'birthday', 'address'], 'string', 'max' => 255],
            [['name', 'soname', 'phname'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['contractCount'], 'safe'],
            [['selectCertGroup', 'possible_cert_group'], 'required'],
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
            'balance' => 'Остаток сертификата ',
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
            'cert_group' => 'Группа сертификата',
            'selectCertGroup' => 'Группа сертификата',
        ];
    }

    public function beforeSave($insert)
    {
        if ($this->selectCertGroup == self::TYPE_PF) {
            $this->cert_group = $this->possible_cert_group;
        } elseif ($certGroup = $this->payers->getCertGroups(1)->one()) {
            $this->cert_group = $certGroup->id;
        }

        return parent::beforeSave($insert);
    }

    public function getCertGroupTypes()
    {
        return [
            self::TYPE_PF => 'Сертификат ПФ',
            self::TYPE_ACCOUNTING => 'Сертификат учёта',
        ];
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
        if ($this->nominal == $this->balance) {
            return true;
        }

        return false;
    }

    public function getPossibleGroupList()
    {
        return [$this->possibleCertGroup, $this->payers->getCertGroups(1)->one()];
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

        if (CertGroup::hasVacancy($this->certGroup)) {
            $this->nominal = $this->certGroup->nominal;
            $this->balance = $this->nominal;

            return self::FLAG_GROUP_HAS_BEEN_CHANGED;
        } else {
            $this->insertIntoGroupQueue();
            $this->cert_group = $this->oldAttributes['cert_group'];
        }

        return self::FLAG_GROUP_HAS_NOT_BEEN_CHANGED;
    }

    public static function updateGroupQueue($certGroup) {
        $model = CertificateGroupQueue::find()
            ->where([
                'cert_group_id' => $certGroup->id,
            ])
        ->one();

        if ($model) {
            $certificate = $model->certificate;
            $certificate->nominal = $certGroup->nominal;
            $certificate->balance = $certGroup->nominal;
            $certificate->cert_group = $certGroup->id;
            if ($certificate->save()) {
                $model->delete();

                return true;
            }
        }

        return false;
    }

    public function insertIntoGroupQueue()
    {
        if (!$this->getCertificateGroupQueues($this->id, $this->cert_group)->count()) {
            $this->link('certGroupsQueue', $this->certGroup);
            Yii::$app->session->setFlash('warning', 'Свободных мест в группе нет, поэтому вы успешно поставлены в очередь на зачисление.');
        }
    }

    public static function getCountCertificates($payerId = null) {
        $query = static::find()
            ->joinWith(['payers'])
            ->where(['actual' => 1])
            ->andWhere('`payers`.operator_id = ' . Yii::$app->operator->identity->id);

        $query->andFilterWhere(['payer_id' => $payerId]);

        return $query->count();
    }

    public static function getSumCertificates($payerId = null) {
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
     * DEPRECATED
     */
    public function getSumContractes($payer_id) {
        $query = Certificates::find();

        $query->where(['payer_id' => $payer_id]);

        return $query->sum('contracts');
    }

    /**
     * DEPRECATED
     * Use relation in app\models\UserIdentity instead
     */
    public function getCertificates() {

        $query = Certificates::find();

        if(!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }
}
