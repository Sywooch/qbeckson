<?php

namespace app\models\search;

use app\models\OrganizationPayerAssignment;
use app\models\UserIdentity;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Organization;

/**
 * OrganizationSearch represents the model behind the search form about `app\models\Organization`.
 */
class OrganizationSearch extends Organization
{
    public $orgtype;
    public $programs;
    public $children;
    public $modelName;
    public $possibleForSuborder = false;
    public $subordered = false;
    public $statusArray = [];
    public $cooperateStatus;
    public $cooperatePayerId;

    /**
     * @return string
     */
    public function formName()
    {
        return $this->modelName ?: '';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[
                'id', 'user_id', 'actual', 'type', 'license_number', 'bank_bik', 'korr_invoice',
                'inn', 'KPP', 'OGRN', 'okopo', 'mun', 'cooperatePayerId'
            ], 'integer'],
            [[
                'name', 'license_date', 'license_issued', 'bank_name', 'bank_sity', 'rass_invoice', 'fio',
                'position', 'address_legal', 'address_actual', 'geocode', 'raiting', 'ground', 'orgtype', 'statusArray',
                'children', 'programs', 'amount_child', 'fio_contact', 'email', 'max_child', 'subordered', 'cooperateStatus'
            ], 'safe'],
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
     * @param array $params
     * @param integer $pageSize
     * @return ActiveDataProvider
     */
    public function search($params, $pageSize = 50)
    {
        $query = Organization::find()
            ->select([
                'organization.*',
                'COUNT(programs.id) as programsCount',
                'COUNT(contracts.id) as childrenCount'
            ])
            ->joinWith([
                'municipality',
                'programs',
                'contracts',
            ]);

        $query->andWhere('mun.operator_id = ' . Yii::$app->operator->identity->id);

        if ($this->possibleForSuborder === true) {
            $query->andWhere('mun.id = ' . Yii::$app->user->identity->payer->municipality->id)
                ->joinWith('suborderPayer')
                ->andWhere('(organization_payer_assignment.payer_id IS NULL) OR (organization_payer_assignment.status = ' . OrganizationPayerAssignment::STATUS_REFUSED . ' AND organization_payer_assignment.payer_id != ' . Yii::$app->user->identity->payer->id . ')');
        }

        if ($this->subordered === true) {
            $query->innerJoinWith('suborderPayer')
                ->andWhere('organization_payer_assignment.payer_id = ' . Yii::$app->user->identity->payer->id)
                ->andWhere('organization_payer_assignment.status = ' . OrganizationPayerAssignment::STATUS_ACTIVE . ' OR organization_payer_assignment.status = ' . OrganizationPayerAssignment::STATUS_PENDING);
        }

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSizeLimit' => false,
                'pageSize' => $pageSize,
            ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            $query->where('0=1');

            return $dataProvider;
        }

        $query->andFilterWhere([
            'organization.id' => $this->id,
            'organization.user_id' => $this->user_id,
            'organization.actual' => $this->actual,
            'organization.type' => $this->type,
            'organization.license_date' => $this->license_date,
            'organization.license_number' => $this->license_number,
            'organization.bank_bik' => $this->bank_bik,
            'organization.korr_invoice' => $this->korr_invoice,
            'organization.inn' => $this->inn,
            'organization.KPP' => $this->KPP,
            'organization.OGRN' => $this->OGRN,
            'organization.okopo' => $this->okopo,
            'organization.mun' => $this->mun,
            'organization.status' => $this->statusArray
        ]);

        if (null !== $this->cooperateStatus) {
            $query->joinWith(['cooperates'])
                ->andWhere(['cooperate.status' => $this->cooperateStatus])
                ->andFilterWhere(['cooperate.payer_id' => $this->cooperatePayerId])
                ->groupBy(['cooperate.id']);
        } else {
            $query->groupBy(['organization.id']);
        }

        $query
            ->andFilterWhere(['like', 'organization.name', $this->name])
            ->andFilterWhere(['like', 'organization.license_issued', $this->license_issued])
            ->andFilterWhere(['like', 'organization.bank_name', $this->bank_name])
            ->andFilterWhere(['like', 'organization.bank_sity', $this->bank_sity])
            ->andFilterWhere(['like', 'organization.rass_invoice', $this->rass_invoice])
            ->andFilterWhere(['like', 'organization.fio', $this->fio])
            ->andFilterWhere(['like', 'organization.position', $this->position])
            ->andFilterWhere(['like', 'organization.address_legal', $this->address_legal])
            ->andFilterWhere(['like', 'organization.address_actual', $this->address_actual])
            ->andFilterWhere(['like', 'organization.geocode', $this->geocode])
            ->andFilterWhere(['like', 'organization.type', $this->orgtype])
            ->andFilterWhere(['like', 'organization.fio_contact', $this->fio_contact])
            ->andFilterWhere(['like', 'organization.email', $this->email])
            ->andFilterWhere(['like', 'organization.ground', $this->ground]);

        if (Yii::$app->user->can(UserIdentity::ROLE_CERTIFICATE)) {
            /** @var UserIdentity $identity */
            $identity = Yii::$app->user->getIdentity();
            if (null !== $identity->mun_id) {
                $query->andFilterWhere([
                    'OR',
                    ['organization.mun' => $identity->mun_id],
                    [
                        'AND',
                        ['programs.mun' => $identity->mun_id],
                        ['programs.verification' => 2],
                    ]
                ]);
            }
        }

        if (!empty($this->programs) && $this->programs !== '0,1000') {
            $programsCount = explode(',', $this->programs);
            $query->andHaving([
                'AND',
                ['>=', 'programsCount', (int)$programsCount[0]],
                ['<=', 'programsCount', (int)$programsCount[1]]
            ]);
        }

        if (!empty($this->children) && $this->children !== '0,10000') {
            $childrenCount = explode(',', $this->children);
            $query->andHaving([
                'AND',
                ['>=', 'childrenCount', (int)$childrenCount[0]],
                ['<=', 'childrenCount', (int)$childrenCount[1]]
            ]);
        }

        if (!empty($this->amount_child) && $this->amount_child !== '0,10000') {
            $childCount = explode(',', $this->amount_child);
            $query->andHaving([
                'AND',
                ['>=', 'organization.amount_child', (int)$childCount[0]],
                ['<=', 'organization.amount_child', (int)$childCount[1]]
            ]);
        }

        if (!empty($this->max_child) && $this->max_child !== '0,10000') {
            $max_child = explode(',', $this->max_child);
            $query->andWhere([
                'AND',
                ['>=', 'organization.max_child', (int)$max_child[0]],
                ['<=', 'organization.max_child', (int)$max_child[1]]
            ]);
        }

        if (!empty($this->raiting) && $this->raiting !== '0,100') {
            $raiting = explode(',', $this->raiting);
            $query->andWhere([
                'AND',
                ['>=', 'organization.raiting', (int)$raiting[0]],
                ['<=', 'organization.raiting', (int)$raiting[1]]
            ]);
        }

        return $dataProvider;
    }
}
