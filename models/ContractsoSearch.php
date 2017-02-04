<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Contracts;
use app\models\Certificates;

/**
 * ContractsSearch represents the model behind the search form about `app\models\Contracts`.
 */
class ContractsoSearch extends Contracts
{
    public $payersname;
    public $organization;
    public $organizations_id;
    public $certificate;
    public $programname;
    public $certificatenumber;
    public $yearyear;
    
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'number', 'status', 'status_year', 'organizations_id'], 'integer'],
            [['date', 'status_termination', 'status_comment', 'link_doc', 'link_ofer', 'start_edu_programm', 'start_edu_contract', 'stop_edu_contract', 'payersname', 'organization', 'certificatenumber', 'programname', 'yearyear'], 'safe'],
            [['all_funds', 'funds_cert', 'all_parents_funds', 'first_m_price', 'other_m_price', 'first_m_nprice', 'other_m_nprice', 'cert_dol', 'payer_dol', 'rezerv', 'paid', 'fontsize'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Contracts::find();

        $query->joinWith(['program']);
        $query->joinWith(['payers']);
        $query->joinWith(['organization']);
        $query->joinWith(['certificate']);
        $query->joinWith(['year']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pagesize' => 50,
            ],
        ]);
        
        $dataProvider->setSort([
            'attributes' => [
                'payersname' => [
                    'asc' => ['payer_id' => SORT_ASC],
                    'desc' => ['payer_id' => SORT_DESC],
                    'label' => 'Плательщик',
                    'default' => SORT_ASC
                ],
                'programname' => [
                    'asc' => ['program_id' => SORT_ASC],
                    'desc' => ['program_id' => SORT_DESC],
                    'label' => 'Программа',
                    'default' => SORT_ASC
                ],
                'certificatenumber' => [
                    'asc' => ['certificate_id' => SORT_ASC],
                    'desc' => ['certificate_id' => SORT_DESC],
                    'label' => 'Номер сертификата',
                    'default' => SORT_ASC
                ],
                'yearyear' => [
                    'asc' => ['year_id' => SORT_ASC],
                    'desc' => ['year_id' => SORT_DESC],
                    'label' => 'Программа',
                    'default' => SORT_ASC
                ],
            ]
        ]);
        
        $dataProvider->sort->attributes['organization'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['organization.name' => SORT_ASC],
            'desc' => ['organization.name' => SORT_DESC],
        ];
        
        $dataProvider->sort->attributes['organizations_id'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
            'asc' => ['organization.id' => SORT_ASC],
            'desc' => ['organization.id' => SORT_DESC],
        ];
        
        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

      //  $certificates = new Certificates();
       // $certificate = $certificates->getCertificates();

      //  $organizations = new Organization();
      //  $organization = $organizations->getOrganization();

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'contracts.number' => $this->number,
            'date' => $this->date,
            'certificate_id' => $this->certificate_id,
            'organization_id' => $this->organization_id,
            'status' => 1,
            'status_termination' => $this->status_termination,
            'status_year' => $this->status_year,
            'start_edu_programm' => $this->start_edu_programm,
            'start_edu_contract' => $this->start_edu_contract,
            'stop_edu_contract' => $this->stop_edu_contract,
        ]);

        $query->andFilterWhere(['like', 'status_comment', $this->status_comment])
            ->andFilterWhere(['like', 'link_doc', $this->link_doc])
            ->andFilterWhere(['like', 'link_ofer', $this->link_ofer])
            ->andFilterWhere(['like', 'payers.name', $this->payersname])
            ->andFilterWhere(['like', 'years.year', $this->yearyear])
            ->andFilterWhere(['like', 'certificates.number', $this->certificatenumber])
            ->andFilterWhere(['like', 'programs.name', $this->programname])
            //->andFilterWhere(['like', 'organization.id', $this->organizations_id])
            ->andFilterWhere(['like', 'organization.name', $this->organization]);

        return $dataProvider;
    }
}
