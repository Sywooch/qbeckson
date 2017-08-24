<?php

namespace app\models;

use Yii;
use app\behaviors\UploadBehavior;

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
 * @property integer $mun
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
 * @property string $fio_contact
 * @property string $email
 * @property string $full_name
 * @property string $number_proxy
 * @property string $license_issued_dat
 * @property integer $contracts_count
 *
 * @property Contracts[] $contracts
 * @property Cooperate[] $cooperates
 * @property Invoices[] $invoices
 * @property Payers $payer
 * @property User $user
 * @property mixed $certprogram
 * @property mixed $actualOrganization
 * @property \yii\db\ActiveQuery $statement
 * @property string $statusName
 * @property mixed $organization
 * @property bool $isModerating
 * @property \yii\db\ActiveQuery $children
 * @property \yii\db\ActiveQuery $operators
 * @property \yii\db\ActiveQuery $license
 * @property \yii\db\ActiveQuery $operator
 * @property string $userName
 * @property \yii\db\ActiveQuery $groups
 * @property Mun $municipality
 * @property \yii\db\ActiveQuery $charter
 * @property \yii\db\ActiveQuery $documents
 * @property bool $requestCanBeUpdated
 * @property bool $isRefused
 * @property \yii\db\ActiveQuery $favorites
 * @property Programs[] $programs
 * @property OrganizationAddress[] $addresses
 * @property OrganizationContractSettings $contractSettings
 */
class Organization extends \yii\db\ActiveRecord
{
    const SCENARIO_GUEST = 'guest';

    const SCENARIO_MODERATOR = 'moderator';

    const SCENARIO_PAYER = 'payer';

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

    // Устав
    public $charterDocument;

    // Выписка
    public $statementDocument;

    // Иные документы
    public $commonDocuments = [];

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
        $scenarios[self::SCENARIO_GUEST] = ['charterDocument', 'statementDocument', 'name', 'full_name', 'organizational_form', 'type', 'license_date', 'license_number', 'license_issued', 'svidet', 'bank_name', 'bank_sity', 'bank_bik', 'korr_invoice', 'rass_invoice', 'phone', 'email', 'site', 'fio_contact', 'address_actual', 'address_legal', 'inn', 'KPP', 'OGRN', 'last', 'mun', 'licenseDocument', 'commonDocuments', 'anonymous_update_token', 'verifyCode'];
        $scenarios[self::SCENARIO_PAYER] = ['certificate_accounting_limit'];

        return $scenarios;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'bank_name', 'bank_bik', 'korr_invoice', 'rass_invoice', 'fio_contact', 'address_actual', 'email', 'full_name', 'type', 'phone', 'address_legal', 'mun'], 'required'],
            [['organizational_form', 'last', 'bank_sity', 'inn', 'KPP', 'OGRN'], 'required'],
            [['svidet'], 'required',
             'when' => function($model) {
                return $model->type == self::TYPE_IP_WITH_WORKERS;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() == 3;
            }"],
            [['license_date', 'license_number', 'license_issued'], 'required',
             'when' => function($model) {
                return $model->type != self::TYPE_IP_WITHOUT_WORKERS;
            },
             'whenClient' => "function (attribute, value) {
                 return $('#organization-type').val() != 4;
            }"],
            [['user_id', 'actual', 'type', 'bank_bik', 'korr_invoice', 'doc_type', 'max_child', 'amount_child', 'inn', 'KPP', 'OGRN', 'okopo', 'mun', 'last', 'last_year_contract', 'certprogram', 'status', 'organizational_form', 'certificate_accounting_limit', 'contracts_count'], 'integer'],
            [['license_date', 'date_proxy', 'cratedate', 'accepted_date'], 'safe'],
            [['raiting'], 'number'],
            [['about', 'site', 'phone', 'refuse_reason', 'anonymous_update_token'], 'string'],
            [['email'], 'email'],
            [['name', 'license_number', 'license_issued', 'license_issued_dat', 'bank_name', 'bank_sity', 'fio_contact', 'fio', 'position', 'position_min', 'address_legal', 'address_actual', 'geocode', 'full_name'], 'string', 'max' => 255],
            [['rass_invoice', 'ground', 'number_proxy'], 'string', 'max' => 45],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [
                ['licenseDocument', 'charterDocument', 'statementDocument', 'commonDocuments'],
                'safe', 'on' => self::SCENARIO_GUEST
            ],
            ['verifyCode', 'captcha', 'on' => self::SCENARIO_GUEST],
        ];
    }

    /**
     * @return array
     */
    public function behaviors()
    {
        $defaultAttributes = [
            'class' => UploadBehavior::class,
            'multiple' => true,
            'pathAttribute' => 'path',
            'baseUrlAttribute' => 'base_url',
            'nameAttribute' => 'filename',
            'documentType' => 'type',
        ];
        return [
            array_merge($defaultAttributes, [
                'attribute' => 'licenseDocument',
                'uploadRelation' => 'license',
            ]),
            array_merge($defaultAttributes, [
                'attribute' => 'charterDocument',
                'uploadRelation' => 'charter',
            ]),
            array_merge($defaultAttributes, [
                'attribute' => 'statementDocument',
                'uploadRelation' => 'statement',
            ]),
            array_merge($defaultAttributes, [
                'attribute' => 'commonDocuments',
                'uploadRelation' => 'documents',
            ]),
        ];
    }

    /**
     * @inheritdoc
     */
    public function beforeValidate()
    {
        if (!empty($this->commonDocuments) && is_array($this->commonDocuments)) {
            foreach ($this->commonDocuments as $key => $document) {
                $this->commonDocuments[$key]['document_type'] = OrganizationDocument::TYPE_COMMON;
            }
        }
        if (!empty($this->licenseDocument) && is_array($this->licenseDocument)) {
            $this->licenseDocument[0]['document_type'] = OrganizationDocument::TYPE_LICENSE;
        }
        if (!empty($this->charterDocument) && is_array($this->charterDocument)) {
            $this->charterDocument[0]['document_type'] = OrganizationDocument::TYPE_CHARTER;
        }
        if (!empty($this->statementDocument) && is_array($this->statementDocument)) {
            $this->statementDocument[0]['document_type'] = OrganizationDocument::TYPE_STATEMENT;
        }

        return parent::beforeValidate();
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getContractSettings()
    {
        return $this->hasOne(OrganizationContractSettings::class, ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAddresses()
    {
        return $this->hasMany(OrganizationAddress::class, ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLicense()
    {
        return $this->hasMany(OrganizationDocument::class, ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_LICENSE]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getChildren()
    {
        return $this->hasMany(Contracts::class, ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCharter()
    {
        return $this->hasMany(OrganizationDocument::class, ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_CHARTER]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStatement()
    {
        return $this->hasMany(OrganizationDocument::className(), ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_STATEMENT]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDocuments()
    {
        return $this->hasMany(OrganizationDocument::className(), ['organization_id' => 'id'])
            ->andWhere(['type' => OrganizationDocument::TYPE_COMMON]);
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
            'charterDocument' => 'Устав',
            'statementDocument' => 'Выписка из ЕГРЮЛ/ЕГРИП',
            'commonDocuments' => 'Иные документы (не более трёх)',
            'verifyCode' => 'Проверочный код',
            'children' => 'Число обучающихся',
            'programs' => 'Количество программ',
            'certificate_accounting_limit' => 'Лимит зачисления',
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
     * @return Cooperate
     */
    public function getCooperation()
    {
        return $this->hasOne(Cooperate::class, ['organization_id' => 'id'])
            ->andWhere(['cooperate.payer_id' => Yii::$app->user->getIdentity()->payer->id])
            ->one();
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
     * @return string
     */
    public function getUserName()
    {
        return $this->user->username;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPrograms()
    {
        return $this->hasMany(Programs::class, ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperators()
    {
        return $this->hasMany(Operators::className(), ['id' => 'operator_id'])
            ->viaTable('organization_operator_assignment', ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOperator()
    {
        return $this->hasOne(Operators::className(), ['id' => 'operator_id'])
            ->viaTable('organization_operator_assignment', ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrganizationPayerAssignment()
    {
         return $this->hasOne(OrganizationPayerAssignment::className(), ['organization_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getSuborderPayer()
    {
        return $this->hasOne(Payers::className(), ['id' => 'payer_id'])->viaTable('organization_payer_assignment', ['organization_id' => 'id']);
    }

    public function canBeSubordered($payer)
    {
        $query = self::find()
            ->joinWith([
                'municipality',
                'suborderPayer',
            ])
            ->andWhere('`organization`.id = ' . $this->id)
            ->andWhere('`mun`.id = ' . $payer->municipality->id)
            ->andWhere('(`organization_payer_assignment`.payer_id IS NOT NULL) OR (`organization_payer_assignment`.status = ' . OrganizationPayerAssignment::STATUS_ACTIVE . ' AND `organization_payer_assignment`.payer_id = ' . Yii::$app->user->identity->payer->id . ') OR (`organization_payer_assignment`.status = ' . OrganizationPayerAssignment::STATUS_PENDING . ' AND `organization_payer_assignment`.payer_id = ' . Yii::$app->user->identity->payer->id . ')');

        if (!$query->count()) {
            return true;
        }

        return false;
    }

    public function hasEmptyInfo()
    {
        if (empty($this->contractSettings->organization_first_ending) || empty($this->contractSettings->organization_second_ending) || empty($this->contractSettings->director_name_ending)) {
            return true;
        }

        return false;
    }

    /**
     * @deprecated Use relation in app\models\UserIdentity instead
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
            ->joinWith(['municipality'])
            ->where([
                'actual' => 1,
                '`mun`.operator_id' => Yii::$app->operator->identity->id,
            ]);

        return $query->count();
    }

    public function getActualOrganization()
    {
        $query = Organization::find();

        $query->Where(['actual' => 1]);

        return $query->column();
    }

    public static function types()
    {
        return [
            self::TYPE_EDUCATION => 'Образовательная организация',
            self::TYPE_TRAINING => 'Организация, осуществляющая обучение',
            self::TYPE_IP_WITH_WORKERS => 'Индивидуальный предприниматель, оказывающий услуги с наймом работников',
            self::TYPE_IP_WITHOUT_WORKERS => 'Индивидуальный предприниматель, оказывающий услуги без найма работников',
        ];
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

    public static function findWithoutOperator($operatorId)
    {
        $query = static::find()
            ->leftJoin('organization_operator_assignment', 'organization.id = organization_operator_assignment.organization_id')
            ->where(['organization_operator_assignment.operator_id' => null]);
        
        return $query->all();
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

    const DOC_TYPE_CHARTER = 2;
    const DOC_TYPE_PROXY = 1;

    public static function docTypes()
    {
        return [
            self::DOC_TYPE_PROXY => 'доверенности',
            self::DOC_TYPE_CHARTER => 'Устава'
        ];
    }
}
