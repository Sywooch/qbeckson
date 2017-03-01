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
 * @property Payers $payer
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
            [['nominal', 'cert_group', 'name', 'soname', 'fio_parent'], 'required'],
            [['user_id', 'payer_id', 'actual', 'contracts', 'directivity1', 'directivity2', 'directivity3', 'directivity4', 'directivity5', 'directivity6', 'cert_group', 'pasport_s', 'pasport_n', 'pasport_v', 'phone'], 'integer'],
            [['nominal'], 'number', 'max' => 100000],
            [['balance', 'rezerv'], 'number'],
            [['number'], 'string', 'length' => [10, 10]],
            [['number'], 'unique'],
            [['fio_child', 'fio_parent', 'birthday', 'address'], 'string', 'max' => 255],
            [['name', 'soname', 'phname'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['payer_id'], 'exist', 'skipOnError' => true, 'targetClass' => Payers::className(), 'targetAttribute' => ['payer_id' => 'id']],
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
            'directivity1' => 'Программ в "Техническая" направленность',
            'directivity2' => 'Программ в "Естественнонаучная" направленность',
            'directivity3' => 'Программ в "Физкультурно-спортивная" направленность',
            'directivity4' => 'Программ в "Художественная" направленность',
            'directivity5' => 'Программ в "Туристско-краеведческая" направленность',
            'directivity6' => 'Программ в "Социально-педагогическая" направленность',
            'cert_group' => 'Группа сертификата',
            'birthday' => 'Дата рождения',
            'address' => 'Адрес места жительства',
            'pasport_s' => 'серия',
            'pasport_n' => 'номер',
            'pasport_v' => 'выдан',
            'phone' => 'Телефон',
            'rezerv' => 'Зарезервированно на оплату программ',
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
    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['certificate_id' => 'id']);
    }

    public function getCountCertificates($payer_id) {
        $query = Certificates::find();

        if($payer_id) {
            $query->where(['payer_id' => $payer_id]);
        }
        
        $query->andWhere(['actual' => 1]);

        return $query->count();
    }

    public function getCountCert($type) {
        $query = Certificates::find();

        if ($type == 'use') {
            $query->Where(['>', 'contracts', 0]);
        }

        if($type == 1) {
            $query->Where(['=', 'contracts', 1]);
        }

        if($type == 2) {
            $query->Where(['=', 'contracts', 2]);
        }

        if($type == 3) {
            $query->Where(['>', 'contracts', 2]);
        }

        $query->andWhere(['actual' => 1]);
        
        return $query->count();
    }
    

    public function getSumCertificates($payer_id) {
        $query = Certificates::find();

        $query->where(['payer_id' => $payer_id]);
        
        $query->andWhere(['actual' => 1]);

        return $query->sum('nominal');
    }

    public function getSumContractes($payer_id) {
        $query = Certificates::find();

        $query->where(['payer_id' => $payer_id]);

        return $query->sum('contracts');
    }

    /**
     * DEPRECATED
     * Use relation in app\models\User instead
     */
    public function getCertificates() {

        $query = Certificates::find();

        if(!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }
    
    public function payerName($data) {
         $rows = (new \yii\db\Query())
                ->select(['name'])
                ->from('payers')
                ->where(['id'=> $data])
                ->one();
        
        return $rows['name'];
    }
    
    public function certGroupName($data) {
         
        $rows = (new \yii\db\Query())
                ->select(['group'])
                ->from('cert_group')
                ->where(['id'=> $data])
                ->one();
        
        return $rows['group'];
    }
}
