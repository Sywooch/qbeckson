<?php

use yii\db\Migration;

class m170305_113160_auth_item_childDataInsert extends Migration
{

    public function init()
    {
        $this->db = 'db';
        parent::init();
    }

    public function safeUp()
    {
        if (!(defined('YII_ENV') && YII_ENV === 'test')) {
            return true;  //только для инициализации тестовой БД
        }

        $this->batchInsert('{{%auth_item_child}}',
            ["parent", "child"],
            [
                [
                    'parent' => 'payer',
                    'child' => 'cert-group',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'certificates',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'certificates/edit',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'certificates/import',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'certificates/verificate',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'certificates/view',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'certificates/view',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'coefficient',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'completeness',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'contracts',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'contracts',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'contracts',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/animport',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/back',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/cancel',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/complete',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/decper',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/delete',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/dubles2',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/good',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/group',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/import',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/mpdf',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/new',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/ocenka',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/terminate',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/updatescert',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/updatesparent',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'contracts/view',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'contracts/waitterm',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'cooperate/create',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'cooperate/decooperate',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'cooperate/delete',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'cooperate/index',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'cooperate/nopayer',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'cooperate/okpayer',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'cooperate/read',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'cooperate/view',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'cooperate/views',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'disputes',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'disputes',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'favorites',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'favorites',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'gii',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'groups',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'groups/contracts',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'groups/fgroup',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'informs/read',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'informs/read',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'informs/read',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'informs/read',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'invoices',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'invoices',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'invoices/complete',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'invoices/view',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'invoices/work',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'mun',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'organization',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'organization',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'organization',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'organization/actual',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'organization/noactual',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'organization/view',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'organization/view',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'payers',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'payers',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'payers',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'permit/access',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'permit/user',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'personal',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-archive',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-contracts',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-favorites',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-info',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-organizations',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-previus',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-programs',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-statistic',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-wait-contract',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'personal/certificate-wait-request',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-certificates',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-coefficient',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-contracts',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-info',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-organizations',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-payers',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-programs',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'personal/operator-statistic',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'personal/operator-statistic',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-contracts',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-favorites',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-groups',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-info',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-invoices',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-payers',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-programs',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'personal/organization-statistic',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-certificates',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-contracts',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-info',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-invoices',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-organizations',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-programs',
                ],
                [
                    'parent' => 'payer',
                    'child' => 'personal/payer-statistic',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'programs',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'programs',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'programs/previus',
                ],
                [
                    'parent' => 'certificate',
                    'child' => 'programs/search',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'rbac-access',
                ],
                [
                    'parent' => 'admins',
                    'child' => 'user',
                ],
                [
                    'parent' => 'organizations',
                    'child' => 'years',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'years/allnormprice',
                ],
                [
                    'parent' => 'operators',
                    'child' => 'years/import',
                ],
            ]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%auth_item_child}} CASCADE');
    }
}
