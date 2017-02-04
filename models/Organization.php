<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "organization".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $actual
 * @property integer $type
 * @property string $name
 * @property string $license_date
 * @property integer $license_number
 * @property string $license_issued
 * @property string $bank_name
 * @property integer $bank_bik
 * @property string $bank_sity
 * @property integer $korr_invoice
 * @property string $rass_invoice
 * @property string $fio
 * @property string $position
 * @property integer $doc_type
 * @property string $date_proxy
 * @property string $address_legal
 * @property string $address_actual
 * @property string $geocode
 * @property integer $max_child
 * @property integer $amount_child
 * @property integer $inn
 * @property integer $KPP
 * @property integer $OGRN
 * @property integer $okopo
 * @property string $raiting
 * @property string $ground
 * @property string $about
 *
 * @property Contracts[] $contracts
 * @property Cooperate[] $cooperates
 * @property Invoices[] $invoices
 * @property Payers $payer
 * @property User $user
 * @property Programs[] $programs
 */
class Organization extends \yii\db\ActiveRecord
{
    
    public $cooperate;
    //public $certprogram;
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'bank_name', 'bank_bik', 'korr_invoice', 'rass_invoice', 'fio_contact', 'address_actual'], 'required'],
            [['license_date', 'license_number', 'license_issued'], 'required', 
             'when' => function($model) {
                return $model->type != 4;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() != 4;
            }"],
            [['svidet'], 'required', 
             'when' => function($model) {
                return $model->type == 3;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() == 3;
            }"],
            [['user_id', 'actual', 'type', 'bank_bik', 'korr_invoice', 'doc_type', 'max_child', 'amount_child', 'inn', 'KPP', 'OGRN', 'okopo', 'mun', 'last', 'last_year_contract', 'certprogram'], 'integer'],
            [['license_date', 'date_proxy', 'cratedate'], 'safe'],
            [['raiting'], 'number'],
            [['about', 'site', 'phone'], 'string'],
            [['email'], 'email'],
            [['name', 'license_number', 'license_issued', 'license_issued_dat', 'bank_name', 'bank_sity', 'fio_contact', 'fio', 'position', 'position_min', 'address_legal', 'address_actual', 'geocode', 'full_name'], 'string', 'max' => 255],
            [['rass_invoice', 'ground', 'number_proxy'], 'string', 'max' => 45],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];

    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'actual' => 'Актуальность',
            'type' => 'Тип организации',
            'name' => 'Наименование организации',
            'full_name' => 'Полное наименование организации',
            'license_date' => 'Лицензия от',
            'license_number' => 'Номер лицензии',
            'license_issued' => 'Кем выдана лицензия',
            'license_issued_dat' => 'Кем выдана лицензия (в дательном падеже)',
            'svidet' => 'Cвидетельство о государственной регистрации',
            'bank_name' => 'Наименование банка',
            'bank_bik' => 'БИК Банка',
            'bank_sity' => 'Город банка',
            'korr_invoice' => 'Корр/Счет',
            'rass_invoice' => 'Расчетный счет',
            'fio_contact' => 'Контактное лицо',
            'fio' => 'ФИО представителя организации',
            'position' => 'Должность представителя организации',
            'position_min' => 'Должность представителя организации (кратко)',
            'doc_type' => 'Действующего на основании',
            'date_proxy' => 'Дата доверенности',
            'number_proxy' => 'Номер доверенности',
            'address_legal' => 'Юридический адрес',
            'address_actual' => 'Фактический адрес',
            'geocode' => 'Геокод',
            //'max_child' => 'Максимальное число детей для обучения (лимит обучения) в текущем году',
            'max_child' => 'Лимит обучения',
            'amount_child' => 'Число договоров',
            'last_year_contract' => 'Число обучающихся за прошлый год',
            'inn' => 'ИНН',
            'KPP' => 'КПП',
            'OGRN' => 'ОГРН',
            'okopo' => 'ОКПО',
            'raiting' => 'Рейтинг',
            'ground' => 'Тип местности в котором расположена организация',
            'username' => 'Логин',
            'about' => 'Почему выбирают нас',
            'mun' => 'Муниципальный район',
            'last' => 'Число оказанных в последнем отчетном году услуг',
            'cratedate' => 'Дата добавления организации',
            'email' => 'E-mail',
            'site' => 'Сайт',
            'phone' => 'Телефон',
            'certprogram' => 'Число программ',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContracts()
    {
        return $this->hasMany(Contracts::className(), ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCooperates()
    {
        return $this->hasMany(Cooperate::className(), ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFavorites()
    {
        return $this->hasMany(Favorites::className(), ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGroups()
    {
        return $this->hasMany(Groups::className(), ['organization_id' => 'id']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getInvoices()
    {
        return $this->hasMany(Invoices::className(), ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    public function getUserName() {
        return $this->user->username;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Programs::className(), ['organization_id' => 'id']);
    }
    
    
    public function getOrganization() {

        $query = Organization::find();

        if(!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }

      public function getCountOrganization() {
        $query = Organization::find();

        $query->Where(['actual' => 1]);

        return $query->count();
    }
    
    public function getActualOrganization() {
        $query = Organization::find();

        $query->Where(['actual' => 1]);

        return $query->column();
    }
    
    public function getOrgtype() {  
        if ($this->type == 1) { return 'Образовательная организация'; }
        if ($this->type == 2) { return 'Организация, осуществляющая обучение'; }
        if ($this->type == 3) { return 'Индивидуальный предприниматель, оказывающий услуги с наймом работников'; }
        if ($this->type == 4) { return 'Индивидуальный предприниматель, оказывающий услуги без найма работников'; }   
    }
    
    public function getCertprogram() {
         $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('programs')
                ->where(['organization_id'=> $this->id])
                ->andWhere(['verification'=> 2])
                ->count();
        
        return $rows;
    }
    
   
    
    
    public function munName($data) {
         $rows = (new \yii\db\Query())
                ->select(['name'])
                ->from('mun')
                ->where(['id'=> $data])
                ->one();
        
        return $rows['name'];
    }
    
     public function invoiceCount($data, $id) {
         $rows = (new \yii\db\Query())
                ->select(['id'])
                ->from('invoices')
                ->where(['organization_id'=> $data])
                ->where(['payers_id'=> $id])
                ->count();
        
        return $rows;
    }
    
    
}
