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
    const SCENARIO_GUEST = 'guest';

    const SCENARIO_MODERATOR = 'moderator';

    const TYPE_EDUCATION = 1;

    const TYPE_TRAINING = 2;

    const TYPE_IP_WITH_WORKERS = 3;

    const TYPE_IP_WITHOUT_WORKERS = 4;

    // Новая организация (не промодерированная)
    const STATUS_NEW = 10;

    // Активная организация
    const STATUS_ACTIVE = 20;

    // Отклоненная организация (можно редактировать заявителю)
    const STATUS_REFUSED = 30;

    // Забаненная организация
    const STATUS_BANNED = 40;

    public $cooperate;

    // Лицензия (документ)
    public $licenseDocument;

    // Иные документы
    public $commonDocuments;

    // Капча
    public $verifyCode;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'organization';
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_MODERATOR] = $scenarios[self::SCENARIO_DEFAULT];
        $scenarios[self::SCENARIO_GUEST] = ['name', 'full_name', 'organizational_form', 'type', 'license_date', 'license_number', 'license_issued', 'svidet', 'bank_name', 'bank_sity', 'bank_bik', 'korr_invoice', 'rass_invoice', 'phone', 'email', 'site', 'fio_contact', 'address_actual', 'address_legal', 'inn', 'KPP', 'OGRN', 'last', 'mun', 'licenseDocument', 'commonDocuments', 'anonymous_update_token', 'verifyCode'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'bank_name', 'bank_bik', 'korr_invoice', 'rass_invoice', 'fio_contact', 'address_actual', 'email'], 'required'],
            [['license_date', 'license_number', 'license_issued'], 'required', 
             'when' => function($model) {
                return $model->type != self::TYPE_IP_WITHOUT_WORKERS;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() != 4;
            }"],
            [['svidet'], 'required', 
             'when' => function($model) {
                return $model->type == self::TYPE_IP_WITH_WORKERS;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() == 3;
            }"],
            [['user_id', 'actual', 'type', 'bank_bik', 'korr_invoice', 'doc_type', 'max_child', 'amount_child', 'inn', 'KPP', 'OGRN', 'okopo', 'mun', 'last', 'last_year_contract', 'certprogram', 'status'], 'integer'],
            [['license_date', 'date_proxy', 'cratedate', 'accepted_date'], 'safe'],
            [['raiting'], 'number'],
            [['about', 'site', 'phone', 'refuse_reason', 'organizational_form', 'anonymous_update_token'], 'string'],
            [['email'], 'email'],
            [['name', 'license_number', 'license_issued', 'license_issued_dat', 'bank_name', 'bank_sity', 'fio_contact', 'fio', 'position', 'position_min', 'address_legal', 'address_actual', 'geocode', 'full_name'], 'string', 'max' => 255],
            [['rass_invoice', 'ground', 'number_proxy'], 'string', 'max' => 45],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            ['licenseDocument', 'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf', 'on' => self::SCENARIO_GUEST],
            ['commonDocuments', 'file', 'skipOnEmpty' => true, 'extensions' => 'doc, docx, pdf', 'maxFiles' => (!empty($this->documents) ? 3 - count($this->documents) : 3), 'on' => self::SCENARIO_GUEST],
            ['verifyCode', 'captcha', 'on' => self::SCENARIO_GUEST],
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
            'organizational_form' => 'Организационно-правовая форма',
            'type' => 'Тип поставщика',
            'typeLabel' => 'Тип поставщика',
            'name' => 'Наименование поставщика',
            'full_name' => 'Полное наименование поставщика',
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
            'fio' => 'ФИО представителя поставщика',
            'position' => 'Должность представителя поставщика',
            'position_min' => 'Должность представителя поставщика (кратко)',
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
            'ground' => 'Тип местности в котором расположена поставщик',
            'username' => 'Логин',
            'about' => 'Почему выбирают нас',
            'mun' => 'Муниципальный район',
            'last' => 'Число оказанных в последнем отчетном году услуг',
            'cratedate' => 'Дата добавления поставщика',
            'email' => 'E-mail',
            'site' => 'Сайт',
            'phone' => 'Телефон',
            'refuse_reason' => 'Причина отказа',
            'certprogram' => 'Число программ',
            'licenseDocument' => 'Лицензия',
            'commonDocuments' => 'Иной документ',
            'verifyCode' => 'Проверочный код',
        ];
    }

    public function getStatusName()
    {
        $title = '';
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                $title = 'Ваша заявка на включение в реестр поставщиков образовательных услуг одобрена, организация внесена в Реестр, и Вы уже даже должны были получить на указанную Вами при формировании заявки электронную почту логин и пароль для входа в личный кабинет. Если логин и пароль Вами не получен – посмотрите в папке «спам» почты, если и там нет письма – обратитесь к оператору.';
                break;
            case self::STATUS_NEW:
                $title = 'Ваша заявка на включение в реестр поставщиков образовательных услуг пока проходит рассмотрение оператором персонифицированного финансирования. Вы получите уведомление о результатах рассмотрения заявки на электронную почту, указанную для организации.';
                break;
            case self::STATUS_REFUSED:
                $title = 'Отказано. Вы можете исправить информацию и отправить заявку повторно.';
                break;
            case self::STATUS_BANNED:
                $title = 'Забанена.';
                break;
        }

        return $title;
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

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMunicipality()
    {
        return $this->hasOne(Mun::className(), ['id' => 'mun']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasOne(OrganizationDocument::className(), ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_LICENSE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(OrganizationDocument::className(), ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_COMMON]);
    }

    public function getUserName()
    {
        return $this->user->username;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Programs::className(), ['organization_id' => 'id']);
    }

    /**
     * DEPRECATED
     * Use relation in app\models\User instead
     */
    public function getOrganization()
    {

        $query = Organization::find();

        if (!Yii::$app->user->isGuest) {
            $query->where(['user_id' => Yii::$app->user->id]);
        }

        return $query->one();
    }

    public static function getCountOrganization()
    {
        $query = static::find()
            ->where(['actual' => 1]);

        return $query->count();
    }

    public function getActualOrganization()
    {
        $query = Organization::find();

        $query->Where(['actual' => 1]);

        return $query->column();
    }

    public function getTypeLabel()
    {
        $title = '';
        switch ($this->type) {
            case self::TYPE_EDUCATION:
                $title = 'Образовательная организация';
                break;
            case self::TYPE_TRAINING:
                $title = 'Организация, осуществляющая обучение';
                break;
            case self::TYPE_IP_WITH_WORKERS:
                $title = 'Индивидуальный предприниматель, оказывающий услуги с наймом работников';
                break;
            case self::TYPE_IP_WITHOUT_WORKERS:
                $title = 'Индивидуальный предприниматель, оказывающий услуги без найма работников';
        }

        return $title;
    }

    public function getCertprogram()
    {
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('programs')
            ->where(['organization_id' => $this->id])
            ->andWhere(['verification' => 2])
            ->count();

        return $rows;
    }

    public function munName($data)
    {
        $rows = (new \yii\db\Query())
            ->select(['name'])
            ->from('mun')
            ->where(['id' => $data])
            ->one();

        return $rows['name'];
    }

    public function invoiceCount($data, $id)
    {
        $rows = (new \yii\db\Query())
            ->select(['id'])
            ->from('invoices')
            ->where(['organization_id' => $data])
            ->where(['payers_id' => $id])
            ->count();

        return $rows;
    }

    public function setNew()
    {
        $this->status = self::STATUS_NEW;
        $this->actual = 0;
    }

    public function setActive()
    {
        $this->status = self::STATUS_ACTIVE;
        $this->actual = 1;
        $this->accepted_date = time();
    }

    public function setRefused()
    {
        $this->status = self::STATUS_REFUSED;
        $this->actual = 0;
    }

    public function sendRequestEmail()
    {
        $mail = Yii::$app->mailer
            ->compose(
                ['html' => 'organizationRequestNew-html', 'text' => 'organizationRequestNew-text'],
                ['model' => $this]
            )
            ->setTo($this->email)
            ->setFrom([Yii::$app->params['adminEmail'] => 'PFDO'])
            ->setSubject('Заявка на включение в реестр поставщиков зарегистрирована');

        if ($mail->send()) {
            return true;
        }

        return false;
    }

    public function sendModerateEmail($password = null)
    {
        $mail = Yii::$app->mailer
            ->compose(
                ['html' => $this->isRefused ? 'organizationRequestRefused-html' : 'organizationRequestAccepted-html', 'text' => $this->isRefused ? 'organizationRequestRefused-text' : 'organizationRequestAccepted-text'],
                [
                    'model' => $this,
                    'password' => $password,
                ]
            )
            ->setTo($this->email)
            ->setFrom([Yii::$app->params['adminEmail'] => 'PFDO'])
            ->setSubject($this->isRefused ? 'Заявка на включение в реестр поставщиков отклонена' : 'Заявка на включение в реестр поставщиков одобрена');

        if ($mail->send()) {
            return true;
        }

        return false;
    }

    public function getIsModerating()
    {
        return $this->scenario == self::SCENARIO_MODERATOR ? true : false;
    }

    public function getRequestCanBeUpdated()
    {
        return $this->scenario != self::SCENARIO_GUEST || $this->status == self::STATUS_REFUSED;
    }

    public function getIsRefused()
    {
        return $this->status == self::STATUS_REFUSED;
    }

    public function uploadDocuments()
    {
        if (!empty($this->licenseDocument)) {
            $licenseFilename = Yii::$app->security->generateRandomString(12) . '.' . $this->licenseDocument->extension;
            $this->licenseDocument->saveAs('uploads/organization/' . $licenseFilename);
            $model = new OrganizationDocument([
                'organization_id' => $this->id,
                'type' => OrganizationDocument::TYPE_LICENSE,
                'filename' => $licenseFilename,
            ]);
            $model->save();
        }

        if (!empty($this->commonDocuments)) {
            foreach ($this->commonDocuments as $commonDocument) {
                $commonFilename = Yii::$app->security->generateRandomString(12) . '.' . $commonDocument->extension;
                $commonDocument->saveAs('uploads/organization/' . $commonFilename);
                $model = new OrganizationDocument([
                    'organization_id' => $this->id,
                    'type' => OrganizationDocument::TYPE_COMMON,
                    'filename' => $commonFilename,
                ]);
                $model->save();
            }
        }

        return true;
    }
}
