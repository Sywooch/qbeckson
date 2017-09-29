<?php

namespace app\models\search;

use app\models\Contracts;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * ContractsSearch represents the model behind the search form about `app\models\search\Contracts`.
 */
class ContractsSearch extends Contracts
{
    public $programMunicipality;
    public $childFullName;
    public $moduleName;
    public $certificateNumber;
    public $programName;
    public $organizationName;
    public $payerName;

    public $modelName;

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return array_merge(parent::attributeLabels(), [
            'programMunicipality' => 'Муниципалитет',
            'childFullName' => 'ФИО ребёнка',
            'moduleName' => 'Модуль',
            'certificateNumber' => 'Сертификат',
            'programName' => 'Программа',
            'organizationName' => 'Организация',
            'payerName' => 'Плательщик',
        ]);
    }

    /**
     * @return string
     */
    public function formName()
    {
        return $this->modelName;
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 'certificate_id', 'payer_id', 'program_id', 'year_id', 'organization_id', 'group_id', 'status',
                'status_year', 'funds_gone', 'sposob', 'prodolj_d', 'prodolj_m', 'prodolj_m_user', 'ocen_fact',
                'ocen_kadr', 'ocen_mat', 'ocen_obch', 'ocenka', 'wait_termnate', 'terminator_user'
            ], 'integer'],
            [[
                'number', 'date', 'status_termination', 'status_comment', 'link_doc', 'link_ofer', 'start_edu_programm',
                'stop_edu_contract', 'start_edu_contract', 'change1', 'change2', 'change_org_fio', 'org_position',
                'org_position_min', 'change_doctype', 'change_fioparent', 'change6', 'change_fiochild', 'change8',
                'change9', 'change10', 'date_termnate', 'all_parents_funds'
            ], 'safe'],
            [[
                'all_funds', 'funds_cert', 'first_m_price', 'other_m_price', 'first_m_nprice',
                'other_m_nprice', 'cert_dol', 'payer_dol', 'fontsize'
            ], 'number'],
            [[
                'programMunicipality', 'childFullName', 'moduleName', 'certificateNumber', 'programName',
                'organizationName', 'payerName', 'paid', 'rezerv'
            ], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     * @param array $params
     * @param int   $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = Contracts::find()
            ->joinWith([
                'payer',
                'program',
                'organization',
                'certificate',
                'module'
            ]);
        $query->andWhere(['payers.operator_id' => Yii::$app->operator->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'defaultPageSize' => false,
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'contracts.id' => $this->id,
            'contracts.date' => $this->date,
            'contracts.certificate_id' => $this->certificate_id,
            'contracts.payer_id' => $this->payer_id,
            'contracts.program_id' => $this->program_id,
            'contracts.year_id' => $this->year_id,
            'contracts.organization_id' => $this->organization_id,
            'contracts.group_id' => $this->group_id,
            'contracts.status' => $this->status,
            'contracts.status_termination' => $this->status_termination,
            'contracts.status_year' => $this->status_year,
            'contracts.all_funds' => $this->all_funds,
            'contracts.funds_cert' => $this->funds_cert,
            'contracts.start_edu_programm' => $this->start_edu_programm,
            'contracts.funds_gone' => $this->funds_gone,
            'contracts.stop_edu_contract' => $this->stop_edu_contract,
            'contracts.start_edu_contract' => $this->start_edu_contract,
            'contracts.sposob' => $this->sposob,
            'contracts.prodolj_d' => $this->prodolj_d,
            'contracts.prodolj_m' => $this->prodolj_m,
            'contracts.prodolj_m_user' => $this->prodolj_m_user,
            'contracts.first_m_price' => $this->first_m_price,
            'contracts.other_m_price' => $this->other_m_price,
            'contracts.first_m_nprice' => $this->first_m_nprice,
            'contracts.other_m_nprice' => $this->other_m_nprice,
            'contracts.ocen_fact' => $this->ocen_fact,
            'contracts.ocen_kadr' => $this->ocen_kadr,
            'contracts.ocen_mat' => $this->ocen_mat,
            'contracts.ocen_obch' => $this->ocen_obch,
            'contracts.ocenka' => $this->ocenka,
            'contracts.wait_termnate' => $this->wait_termnate,
            'contracts.date_termnate' => $this->date_termnate,
            'contracts.cert_dol' => $this->cert_dol,
            'contracts.payer_dol' => $this->payer_dol,
            'contracts.terminator_user' => $this->terminator_user,
            'contracts.fontsize' => $this->fontsize,
            'programs.mun' => $this->programMunicipality,
        ]);
        if (!empty($this->all_parents_funds) && $this->all_parents_funds !== '0,10000') {
            $all_parents_funds = explode(',', $this->all_parents_funds);
            $query->andWhere(['and', ['>=', 'all_parents_funds', (int)$all_parents_funds[0]], ['<=', 'all_parents_funds', (int)$all_parents_funds[1]]]);
        }
        $query->andFilterWhere(['like', 'contracts.number', $this->number])
            ->andFilterWhere(['like', 'contracts.status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'contracts.link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'contracts.link_ofer', $this->link_ofer])
            ->andFilterWhere(['like', 'contracts.change1', $this->change1])
            ->andFilterWhere(['like', 'contracts.change2', $this->change2])
            ->andFilterWhere(['like', 'contracts.change_org_fio', $this->change_org_fio])
            ->andFilterWhere(['like', 'contracts.org_position', $this->org_position])
            ->andFilterWhere(['like', 'contracts.org_position_min', $this->org_position_min])
            ->andFilterWhere(['like', 'contracts.change_doctype', $this->change_doctype])
            ->andFilterWhere(['like', 'contracts.change_fioparent', $this->change_fioparent])
            ->andFilterWhere(['like', 'contracts.change6', $this->change6])
            ->andFilterWhere(['like', 'contracts.change_fiochild', $this->change_fiochild])
            ->andFilterWhere(['like', 'contracts.change8', $this->change8])
            ->andFilterWhere(['like', 'contracts.change9', $this->change9])
            ->andFilterWhere(['like', 'contracts.change10', $this->change10])
            ->andFilterWhere(['like', 'certificates.fio_child', $this->childFullName])
            ->andFilterWhere(['like', 'years.name', $this->moduleName])
            ->andFilterWhere(['like', 'certificates.number', $this->certificateNumber])
            ->andFilterWhere(['like', 'programs.name', $this->programName])
            ->andFilterWhere(['like', 'organization.name', $this->organizationName])
            ->andFilterWhere(['like', 'payers.name', $this->payerName]);

        if (!empty($this->paid) && $this->paid !== '0,150000') {
            $paid = explode(',', $this->paid);
            $query->andWhere(['and', ['>=', 'contracts.paid', (int)$paid[0]], ['<=', 'nominal', (int)$paid[1]]]);
        }

        if (!empty($this->rezerv) && $this->rezerv !== '0,150000') {
            $rezerv = explode(',', $this->rezerv);
            $query->andWhere(['and', ['>=', 'contracts.rezerv', (int)$rezerv[0]], ['<=', 'nominal', (int)$rezerv[1]]]);
        }

        $query->groupBy(['contracts.id']);

        return $dataProvider;
    }
}
