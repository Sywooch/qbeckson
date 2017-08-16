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
 * @property Contracts[] $contracts0
 */
class Certificates extends \yii\db\ActiveRecord
{
    public $birthday;

    public $address;

    public $pasport_s;

    public $pasport_n;

    public $pasport_v;

    public $phone;

    public $contractCount;
    
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
            [['nominal', 'cert_group', 'name', 'soname'], 'required'],
            [['fio_parent'], 'required'],
            [['user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6', 'cert_group', 'pasport_s', 'pasport_n', 'pasport_v', 'phone'], 'integer'],
            [['nominal', 'nominal_f'], 'number', 'max' => 100000],
            [['number'], 'string', 'length' => [10, 10]],
            [['balance', 'balance_f', 'rezerv', 'rezerv_f'], 'number'],
            [['number'], 'unique'],
            [['fio_child', 'fio_parent', 'birthday', 'address'], 'string', 'max' => 255],
            [['name', 'soname', 'phname'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
            [['contractCount'], 'safe'],
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
            'balance' => 'Остаток сертификата',
            'balance_f' => 'Остаток сертификата на будущий период',
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
            'cert_group' => 'Группа сертификата',
            'payer' => 'Плательщик'
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

        if(!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }
}
