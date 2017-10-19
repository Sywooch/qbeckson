<?php

use yii\db\Migration;

class m170205_113149_Mass extends Migration
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

        $tableOptions = 'ENGINE=InnoDB';

        $this->createTable('{{%auth_assignment}}', [
            'item_name' => $this->string(64)->notNull(),
            'user_id' => $this->integer(11)->notNull(),
            'created_at' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%auth_assignment}}', ['user_id'], false);
        $this->addPrimaryKey('pk_on_auth_assignment', '{{%auth_assignment}}', ['item_name', 'user_id']);

        $this->createTable('{{%auth_item}}', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->integer(11)->notNull(),
            'description' => $this->text()->null()->defaultValue(null),
            'rule_name' => $this->string(64)->null()->defaultValue(null),
            'data' => $this->text()->null()->defaultValue(null),
            'created_at' => $this->integer(11)->null()->defaultValue(null),
            'updated_at' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('rule_name', '{{%auth_item}}', ['rule_name'], false);
        $this->createIndex('idx-auth_item-type', '{{%auth_item}}', ['type'], false);
        $this->addPrimaryKey('pk_on_auth_item', '{{%auth_item}}', ['name']);

        $this->createTable('{{%auth_item_child}}', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ], $tableOptions);

        $this->createIndex('child', '{{%auth_item_child}}', ['child'], false);
        $this->addPrimaryKey('pk_on_auth_item_child', '{{%auth_item_child}}', ['parent', 'child']);

        $this->createTable('{{%auth_rule}}', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->text()->null()->defaultValue(null),
            'created_at' => $this->integer(11)->null()->defaultValue(null),
            'updated_at' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->addPrimaryKey('pk_on_auth_rule', '{{%auth_rule}}', ['name']);

        $this->createTable('{{%cert_group}}', [
            'id' => $this->primaryKey(11),
            'payer_id' => $this->integer(11)->notNull(),
            'group' => $this->string(255)->notNull(),
            'nominal' => $this->integer(),
        ], $tableOptions);

        $this->createIndex('payer_id', '{{%cert_group}}', ['payer_id'], false);

        $this->createTable('{{%certificates}}', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->notNull(),
            'number' => $this->string(45)->notNull(),
            'payer_id' => $this->integer(11)->notNull(),
            'actual' => $this->smallInteger(1)->null()->defaultValue(null),
            'fio_child' => $this->string(255)->null()->defaultValue(null),
            'name' => $this->string(50)->null()->defaultValue(null),
            'soname' => $this->string(50)->null()->defaultValue(null),
            'phname' => $this->string(50)->null()->defaultValue(null),
            'fio_parent' => $this->string(255)->null()->defaultValue(null),
            'nominal' => $this->decimal(19, 2)->null()->defaultValue(null),
            'balance' => $this->decimal(19, 2)->null()->defaultValue(null),
            'contracts' => $this->integer(11)->null()->defaultValue(null),
            'directivity1' => $this->integer(11)->null()->defaultValue(null),
            'directivity2' => $this->integer(11)->null()->defaultValue(null),
            'directivity3' => $this->integer(11)->null()->defaultValue(null),
            'directivity4' => $this->integer(11)->null()->defaultValue(null),
            'directivity5' => $this->integer(11)->null()->defaultValue(null),
            'directivity6' => $this->integer(11)->null()->defaultValue(null),
            'cert_group' => $this->integer(11)->notNull(),
            'rezerv' => $this->float()->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('number_UNIQUE', '{{%certificates}}', ['number'], true);
        $this->createIndex('payer_id_idx', '{{%certificates}}', ['payer_id'], false);
        $this->createIndex('user_id', '{{%certificates}}', ['user_id'], false);
        $this->createIndex('cert_group', '{{%certificates}}', ['cert_group'], false);

        $this->createTable('{{%coefficient}}', [
            'id' => $this->primaryKey(11),
            'p21v' => $this->float()->notNull(),
            'p21s' => $this->float()->notNull(),
            'p21o' => $this->float()->notNull(),
            'p22v' => $this->float()->notNull(),
            'p22s' => $this->float()->notNull(),
            'p22o' => $this->float()->notNull(),
            'p3v' => $this->float()->notNull(),
            'p3s' => $this->float()->notNull(),
            'p3n' => $this->float()->notNull(),
            'weekyear' => $this->float()->notNull(),
            'weekmonth' => $this->float()->notNull(),
            'pk' => $this->float()->notNull(),
            'norm' => $this->float()->notNull(),
            'potenc' => $this->float()->notNull(),
            'ngr' => $this->float()->notNull(),
            'sgr' => $this->float()->notNull(),
            'vgr' => $this->float()->notNull(),
            'chr1' => $this->float()->notNull(),
            'zmr1' => $this->float()->notNull(),
            'chr2' => $this->float()->notNull(),
            'zmr2' => $this->float()->notNull(),
            'blimrob' => $this->float()->notNull(),
            'blimtex' => $this->float()->notNull(),
            'blimest' => $this->float()->notNull(),
            'blimfiz' => $this->float()->notNull(),
            'blimxud' => $this->float()->notNull(),
            'blimtur' => $this->float()->notNull(),
            'blimsoc' => $this->float()->notNull(),
            'ngrp' => $this->float()->notNull(),
            'sgrp' => $this->float()->notNull(),
            'vgrp' => $this->float()->notNull(),
            'ppchr1' => $this->float()->notNull(),
            'ppzm1' => $this->float()->notNull(),
            'ppchr2' => $this->float()->notNull(),
            'ppzm2' => $this->float()->notNull(),
            'ocsootv' => $this->float()->notNull(),
            'ocku' => $this->float()->notNull(),
            'ocmt' => $this->float()->notNull(),
            'obsh' => $this->float()->notNull(),
            'ktob' => $this->float()->notNull(),
            'vgs' => $this->float()->notNull(),
            'sgs' => $this->float()->notNull(),
            'pchsrd' => $this->float()->notNull(),
            'pzmsrd' => $this->float()->notNull(),
            'minraiting' => $this->float()->notNull(),
        ], $tableOptions);

        $this->createTable('{{%completeness}}', [
            'id' => $this->primaryKey(11),
            'group_id' => $this->integer(11)->null()->defaultValue(null),
            'contract_id' => $this->integer(11)->null()->defaultValue(null),
            'month' => $this->integer(11)->notNull(),
            'year' => $this->integer(11)->notNull(),
            'completeness' => $this->integer(11)->notNull(),
            'sum' => $this->float()->null()->defaultValue(null),
            'preinvoice' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('group_id', '{{%completeness}}', ['group_id'], false);

        $this->createTable('{{%contracts}}', [
            'id' => $this->primaryKey(11),
            'number' => $this->string(11)->null()->defaultValue(null),
            'date' => $this->date()->null()->defaultValue(null),
            'certificate_id' => $this->integer(11)->notNull(),
            'payer_id' => $this->integer(11)->notNull(),
            'program_id' => $this->integer(11)->notNull(),
            'year_id' => $this->integer(11)->notNull(),
            'organization_id' => $this->integer(11)->notNull(),
            'group_id' => $this->integer(11)->notNull(),
            'status' => $this->integer(1)->null()->defaultValue(null),
            'status_termination' => $this->date()->null()->defaultValue(null),
            'status_comment' => $this->text()->null()->defaultValue(null),
            'status_year' => $this->smallInteger(1)->null()->defaultValue(null),
            'link_doc' => $this->string(255)->null()->defaultValue(null),
            'link_ofer' => $this->string(255)->null()->defaultValue(null),
            'all_funds' => $this->double()->null()->defaultValue(null),
            'funds_cert' => $this->double()->null()->defaultValue(null),
            'all_parents_funds' => $this->double()->null()->defaultValue(null),
            'start_edu_programm' => $this->date()->null()->defaultValue(null),
            'funds_gone' => $this->integer(11)->null()->defaultValue(null),
            'stop_edu_contract' => $this->date()->null()->defaultValue(null),
            'start_edu_contract' => $this->date()->null()->defaultValue(null),
            'sposob' => $this->integer(11)->null()->defaultValue(null),
            'prodolj_d' => $this->integer(11)->null()->defaultValue(null),
            'prodolj_m' => $this->integer(11)->null()->defaultValue(null),
            'prodolj_m_user' => $this->integer(11)->null()->defaultValue(null),
            'first_m_price' => $this->double()->null()->defaultValue(null),
            'other_m_price' => $this->double()->null()->defaultValue(null),
            'first_m_nprice' => $this->double()->null()->defaultValue(null),
            'other_m_nprice' => $this->double()->null()->defaultValue(null),
            'change1' => $this->string(8)->null()->defaultValue('ее'),
            'change2' => $this->string(8)->null()->defaultValue('ое'),
            'change_org_fio' => $this->string(255)->null()->defaultValue(null),
            'org_position' => $this->string(255)->null()->defaultValue(null),
            'org_position_min' => $this->string(255)->null()->defaultValue(null),
            'change_doctype' => $this->string(255)->null()->defaultValue(null),
            'change_fioparent' => $this->string(255)->null()->defaultValue(null),
            'change6' => $this->string(8)->null()->defaultValue('ая(ый)'),
            'change_fiochild' => $this->string(255)->null()->defaultValue(null),
            'change8' => $this->string(8)->null()->defaultValue('ого(ой)'),
            'change9' => $this->string(8)->null()->defaultValue('ая(ий)'),
            'change10' => $this->string(8)->null()->defaultValue('ей'),
            'ocen_fact' => $this->integer(2)->null()->defaultValue(null),
            'ocen_kadr' => $this->integer(2)->null()->defaultValue(null),
            'ocen_mat' => $this->integer(2)->null()->defaultValue(null),
            'ocen_obch' => $this->integer(2)->null()->defaultValue(null),
            'ocenka' => $this->integer(11)->null()->defaultValue(null),
            'wait_termnate' => $this->integer(11)->null()->defaultValue(null),
            'date_termnate' => $this->date()->null()->defaultValue(null),
            'cert_dol' => $this->double()->null()->defaultValue(null),
            'payer_dol' => $this->float()->null()->defaultValue(null),
            'rezerv' => $this->double()->null()->defaultValue(null),
            'paid' => $this->double()->null()->defaultValue(null),
            'terminator_user' => $this->integer(11)->null()->defaultValue(0),
            'fontsize' => $this->double()->notNull()->defaultValue("12"),
        ], $tableOptions);

        $this->createIndex('contracts_certificates_idx', '{{%contracts}}', ['certificate_id'], false);
        $this->createIndex('contracts_programs_idx', '{{%contracts}}', ['program_id'], false);
        $this->createIndex('organization_id', '{{%contracts}}', ['organization_id'], false);
        $this->createIndex('year_id', '{{%contracts}}', ['year_id'], false);
        $this->createIndex('group_id', '{{%contracts}}', ['group_id'], false);
        $this->createIndex('payer_id', '{{%contracts}}', ['payer_id'], false);

        $this->createTable('{{%cooperate}}', [
            'id' => $this->primaryKey(11),
            'organization_id' => $this->integer(11)->notNull(),
            'payer_id' => $this->integer(11)->notNull(),
            'number' => $this->string(255)->null()->defaultValue(null),
            'date' => $this->date()->null()->defaultValue(null),
            'date_dissolution' => $this->date()->null()->defaultValue(null),
            'status' => $this->smallInteger(1)->null()->defaultValue(null),
            'reade' => $this->smallInteger(1)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('organization_id', '{{%cooperate}}', ['organization_id'], false);
        $this->createIndex('payer_id', '{{%cooperate}}', ['payer_id'], false);

        $this->createTable('{{%disputes}}', [
            'id' => $this->primaryKey(11),
            'contract_id' => $this->integer(11)->null()->defaultValue(null),
            'date' => $this->date()->null()->defaultValue(null),
            'type' => $this->integer(1)->null()->defaultValue(null),
            'user_id' => $this->integer(11)->null()->defaultValue(null),
            'text' => $this->text()->null()->defaultValue(null),
            'display' => $this->integer(1)->null()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('disput_contract_idx', '{{%disputes}}', ['contract_id'], false);
        $this->createIndex('user_id', '{{%disputes}}', ['user_id'], false);

        $this->createTable('{{%favorites}}', [
            'id' => $this->primaryKey(11),
            'certificate_id' => $this->integer(11)->notNull(),
            'program_id' => $this->integer(11)->notNull(),
            'organization_id' => $this->integer(11)->notNull(),
            'type' => $this->integer(11)->notNull()->defaultValue(0),
        ], $tableOptions);

        $this->createIndex('certificate_id', '{{%favorites}}', ['certificate_id'], false);
        $this->createIndex('program_id', '{{%favorites}}', ['program_id'], false);
        $this->createIndex('organization_id', '{{%favorites}}', ['organization_id'], false);

        $this->createTable('{{%groups}}', [
            'id' => $this->primaryKey(11),
            'organization_id' => $this->integer(11)->notNull(),
            'program_id' => $this->integer(11)->notNull(),
            'year_id' => $this->integer(11)->null()->defaultValue(null),
            'name' => $this->string(255)->notNull(),
            'address' => $this->text()->null()->defaultValue(null),
            'schedule' => $this->text()->null()->defaultValue(null),
            'datestart' => $this->date()->notNull(),
            'datestop' => $this->date()->notNull(),
        ], $tableOptions);

        $this->createIndex('organization_id', '{{%groups}}', ['organization_id'], false);
        $this->createIndex('program_id', '{{%groups}}', ['program_id'], false);
        $this->createIndex('year_id', '{{%groups}}', ['year_id'], false);

        $this->createTable('{{%informs}}', [
            'id' => $this->primaryKey(11),
            'program_id' => $this->integer(11)->notNull(),
            'contract_id' => $this->integer(11)->null()->defaultValue(null),
            'from' => $this->integer(1)->null()->defaultValue(null),
            'prof_id' => $this->integer(11)->null()->defaultValue(null),
            'text' => $this->text()->null()->defaultValue(null),
            'date' => $this->date()->null()->defaultValue(null),
            'read' => $this->smallInteger(1)->null()->defaultValue(null),
            'status' => $this->integer(1)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('Inform_contract_idx', '{{%informs}}', ['contract_id'], false);
        $this->createIndex('program_id', '{{%informs}}', ['program_id'], false);

        $this->createTable('{{%invoices}}', [
            'id' => $this->primaryKey(11),
            'month' => $this->integer(2)->null()->defaultValue(null),
            'organization_id' => $this->integer(11)->notNull(),
            'payers_id' => $this->integer(11)->notNull(),
            'contracts' => $this->text()->null()->defaultValue(null),
            'sum' => $this->double()->null()->defaultValue(null),
            'number' => $this->integer(11)->null()->defaultValue(null),
            'date' => $this->date()->null()->defaultValue(null),
            'link' => $this->string(45)->null()->defaultValue(null),
            'prepayment' => $this->smallInteger(1)->null()->defaultValue(null),
            'completeness' => $this->integer(11)->null()->defaultValue(null),
            'status' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createIndex('Invoice_organization_idx', '{{%invoices}}', ['organization_id'], false);
        $this->createIndex('invoice_payer_idx', '{{%invoices}}', ['payers_id'], false);
        $this->createIndex('completeness', '{{%invoices}}', ['completeness'], false);


        $this->createTable('{{%mun}}', [
            'id' => $this->primaryKey(11),
            'name' => $this->string(255)->notNull(),
            'nopc' => $this->float()->notNull(),
            'conopc' => $this->float()->notNull(),
            'pc' => $this->float()->notNull(),
            'copc' => $this->float()->notNull(),
            'zp' => $this->float()->notNull(),
            'cozp' => $this->float()->notNull(),
            'dop' => $this->float()->notNull(),
            'codop' => $this->float()->notNull(),
            'uvel' => $this->float()->notNull(),
            'couvel' => $this->float()->notNull(),
            'otch' => $this->float()->notNull(),
            'cootch' => $this->float()->notNull(),
            'otpusk' => $this->float()->notNull(),
            'cootpusk' => $this->float()->notNull(),
            'polezn' => $this->float()->notNull(),
            'copolezn' => $this->float()->notNull(),
            'stav' => $this->float()->notNull(),
            'costav' => $this->float()->notNull(),
            'rob' => $this->integer(11)->notNull(),
            'corob' => $this->integer(11)->notNull(),
            'tex' => $this->integer(11)->notNull(),
            'cotex' => $this->integer(11)->notNull(),
            'est' => $this->integer(11)->notNull(),
            'coest' => $this->integer(11)->notNull(),
            'fiz' => $this->integer(11)->notNull(),
            'cofiz' => $this->integer(11)->notNull(),
            'xud' => $this->integer(11)->notNull(),
            'coxud' => $this->integer(11)->notNull(),
            'tur' => $this->integer(11)->notNull(),
            'cotur' => $this->integer(11)->notNull(),
            'soc' => $this->integer(11)->notNull(),
            'cosoc' => $this->integer(11)->notNull(),
            'deystv' => $this->integer(11)->notNull(),
            'countdet' => $this->integer(11)->notNull(),
            'lastdeystv' => $this->integer(11)->notNull(),
        ], $tableOptions);

        $this->createTable('{{%operators}}', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->notNull(),
            'name' => $this->string(255)->notNull(),
            'OGRN' => $this->string(255)->notNull(),
            'INN' => $this->string(255)->notNull(),
            'KPP' => $this->string(255)->notNull(),
            'OKPO' => $this->string(255)->notNull(),
            'address_legal' => $this->string(255)->notNull(),
            'address_actual' => $this->string(255)->notNull(),
            'phone' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'position' => $this->string(255)->notNull(),
            'fio' => $this->string(255)->notNull(),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%operators}}', ['user_id'], false);

        $this->createTable('{{%organization}}', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->notNull(),
            'actual' => $this->smallInteger(1)->null()->defaultValue(null),
            'type' => $this->integer(1)->null()->defaultValue(null),
            'name' => $this->string(255)->null()->defaultValue(null),
            'full_name' => $this->string(255)->null()->defaultValue(null),
            'license_date' => $this->date()->null()->defaultValue(null),
            'license_number' => $this->string(255)->null()->defaultValue(null),
            'license_issued' => $this->string(255)->null()->defaultValue(null),
            'license_issued_dat' => $this->string(255)->notNull(),
            'svidet' => $this->string(255)->null()->defaultValue(null),
            'bank_name' => $this->string(255)->null()->defaultValue(null),
            'bank_bik' => $this->string(255)->null()->defaultValue(null),
            'bank_sity' => $this->string(255)->null()->defaultValue(null),
            'korr_invoice' => $this->string(255)->null()->defaultValue(null),
            'rass_invoice' => $this->string(45)->null()->defaultValue(null),
            'fio_contact' => $this->string(255)->notNull(),
            'fio' => $this->string(255)->null()->defaultValue(null),
            'position' => $this->string(255)->null()->defaultValue(null),
            'position_min' => $this->string(255)->null()->defaultValue(null),
            'doc_type' => $this->smallInteger(1)->null()->defaultValue(null),
            'date_proxy' => $this->date()->null()->defaultValue(null),
            'number_proxy' => $this->string(255)->notNull(),
            'address_legal' => $this->string(255)->null()->defaultValue(null),
            'address_actual' => $this->string(255)->null()->defaultValue(null),
            'geocode' => $this->string(255)->null()->defaultValue(null),
            'max_child' => $this->integer(11)->null()->defaultValue(null),
            'amount_child' => $this->integer(11)->null()->defaultValue(0),
            'inn' => $this->string(255)->null()->defaultValue(null),
            'KPP' => $this->integer(11)->null()->defaultValue(0),
            'OGRN' => $this->string(255)->null()->defaultValue(null),
            'okopo' => $this->string(255)->null()->defaultValue(null),
            'raiting' => $this->float()->null()->defaultValue(null),
            'ground' => $this->string(45)->null()->defaultValue(null),
            'about' => $this->text()->null()->defaultValue(null),
            'mun' => $this->integer(11)->null()->defaultValue(null),
            'last' => $this->integer(11)->null()->defaultValue(0),
            'last_year_contract' => $this->integer(11)->null()->defaultValue(null),
            'cratedate' => $this->date()->null()->defaultValue(null),
            'email' => $this->string(255)->null()->defaultValue(null),
            'site' => $this->string(255)->null()->defaultValue(null),
            'phone' => $this->string(255)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%organization}}', ['user_id'], false);
        $this->createIndex('mun', '{{%organization}}', ['mun'], false);


        $this->createTable('{{%payers}}', [
            'id' => $this->primaryKey(11),
            'user_id' => $this->integer(11)->notNull(),
            'code' => $this->string(2)->notNull(),
            'name' => $this->string(255)->notNull(),
            'name_dat' => $this->string(255)->notNull(),
            'OGRN' => $this->string(255)->null()->defaultValue(null),
            'INN' => $this->string(255)->null()->defaultValue(null),
            'KPP' => $this->string(255)->null()->defaultValue(null),
            'OKPO' => $this->string(255)->null()->defaultValue(null),
            'address_legal' => $this->string(255)->null()->defaultValue(null),
            'address_actual' => $this->string(255)->null()->defaultValue(null),
            'phone' => $this->string(255)->null()->defaultValue(null),
            'email' => $this->string(255)->null()->defaultValue(null),
            'position' => $this->string(255)->null()->defaultValue(null),
            'fio' => $this->string(255)->null()->defaultValue(null),
            'mun' => $this->integer(11)->notNull(),
            'directionality' => "set('Техническая (робототехника)','Техническая (иная)','Естественнонаучная','Физкультурно-спортивная','Художественная','Туристско-краеведческая','Социально-педагогическая') NULL DEFAULT NULL",
            'directionality_1rob_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_1_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_2_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_3_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_4_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_5_count' => $this->integer(11)->null()->defaultValue(null),
            'directionality_6_count' => $this->integer(11)->null()->defaultValue(null),
        ], $tableOptions);

        $this->createIndex('user_id', '{{%payers}}', ['user_id'], false);
        $this->createIndex('mun', '{{%payers}}', ['mun'], false);

        $this->createTable('{{%previus}}', [
            'id' => $this->primaryKey(11),
            'certificate_id' => $this->integer(11)->notNull(),
            'year_id' => $this->integer(11)->notNull(),
            'organization_id' => $this->integer(11)->notNull(),
            'program_id' => $this->integer(11)->notNull(),
            'actual' => $this->integer(11)->null()->defaultValue(1),
        ], $tableOptions);

        $this->createIndex('certificate_id', '{{%previus}}', ['certificate_id'], false);
        $this->createIndex('year_id', '{{%previus}}', ['year_id'], false);
        $this->createIndex('organization_id', '{{%previus}}', ['organization_id'], false);
        $this->createIndex('program_id', '{{%previus}}', ['program_id'], false);

        $this->createTable('{{%programs}}', [
            'id' => $this->primaryKey(11),
            'organization_id' => $this->integer(11)->null()->defaultValue(null),
            'verification' => $this->integer(1)->null()->defaultValue(null),
            'name' => $this->string(255)->null()->defaultValue(null),
            'vid' => $this->string(255)->null()->defaultValue(null),
            'form' => $this->integer(11)->notNull(),
            'mun' => $this->integer(11)->null()->defaultValue(null),
            'ground' => $this->integer(11)->notNull(),
            'price' => $this->integer(11)->null()->defaultValue(null),
            'rating' => $this->float()->null()->defaultValue(null),
            'limit' => $this->integer(11)->null()->defaultValue(null),
            'study' => $this->integer(11)->null()->defaultValue(null),
            'last_contracts' => $this->integer(11)->null()->defaultValue(null),
            'last_s_contracts' => $this->integer(11)->null()->defaultValue(null),
            'last_s_contracts_rod' => $this->integer(11)->null()->defaultValue(null),
            'open' => $this->integer(1)->null()->defaultValue(null),
            'colse_date' => $this->date()->null()->defaultValue(null),
            'task' => $this->text()->null()->defaultValue(null),
            'annotation' => $this->text()->null()->defaultValue(null),
            'year' => $this->integer(11)->null()->defaultValue(null),
            'both_teachers' => $this->integer(11)->null()->defaultValue(null),
            'fullness' => $this->string(255)->null()->defaultValue(null),
            'complexity' => $this->string(255)->null()->defaultValue(null),
            'norm_providing' => $this->text()->null()->defaultValue(null),
            'ovz' => $this->integer(1)->null()->defaultValue(null),
            'zab' => $this->string(255)->notNull(),
            'age_group_min' => $this->integer(11)->null()->defaultValue(null),
            'age_group_max' => $this->integer(11)->notNull(),
            'quality_control' => $this->integer(11)->null()->defaultValue(null),
            'link' => $this->string(45)->null()->defaultValue(null),
            'certification_date' => $this->date()->null()->defaultValue(null),
            'p3z' => $this->integer(11)->null()->defaultValue(1),
            'ocen_fact' => $this->float()->null()->defaultValue(null),
            'ocen_kadr' => $this->float()->null()->defaultValue(null),
            'ocen_mat' => $this->float()->null()->defaultValue(null),
            'ocen_obch' => $this->float()->null()->defaultValue(null),
            'directivity' => $this->string()->null()
        ], $tableOptions);

        $this->createIndex('program_organization_idx', '{{%programs}}', ['organization_id'], false);
        $this->createIndex('mun', '{{%programs}}', ['mun'], false);

        $this->createTable('{{%user}}', [
            'id' => $this->primaryKey(11),
            'username' => $this->string(15)->notNull(),
            'password' => $this->string(64)->notNull(),
            'access_token' => $this->string(64)->notNull(),
            'auth_key' => $this->string(64)->notNull(),
        ], $tableOptions);

        $this->createIndex('username', '{{%user}}', ['username'], true);

        $this->createTable('{{%years}}', [
            'id' => $this->primaryKey(11),
            /*  'name'=> $this->string(255)->null()->defaultValue(null),*/
            'program_id' => $this->integer(11)->notNull(),
            'year' => $this->integer(11)->notNull(),
            'month' => $this->integer(11)->notNull(),
            'hours' => $this->integer(11)->notNull(),
            'kvfirst' => $this->string(255)->notNull(),
            'kvdop' => $this->string(255)->notNull(),
            'hoursindivid' => $this->integer(11)->notNull(),
            'hoursdop' => $this->integer(11)->notNull(),
            'minchild' => $this->integer(11)->notNull(),
            'maxchild' => $this->integer(11)->notNull(),
            'price' => $this->float()->notNull(),
            'normative_price' => $this->float()->notNull(),
            'rating' => $this->integer(11)->notNull(),
            'limits' => $this->integer(11)->notNull(),
            'open' => $this->integer(11)->notNull(),
            'previus' => $this->integer(11)->notNull()->defaultValue(1),
            'quality_control' => $this->integer(11)->notNull(),
            'p21z' => $this->integer(11)->null()->defaultValue(1),
            'p22z' => $this->integer(11)->null()->defaultValue(1),
        ], $tableOptions);

        $this->createIndex('program_id', '{{%years}}', ['program_id'], false);
        $this->addForeignKey(
            'fk_auth_assignment_item_name',
            '{{%auth_assignment}}', 'item_name',
            '{{%auth_item}}', 'name',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_assignment_user_id',
            '{{%auth_assignment}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_item_rule_name',
            '{{%auth_item}}', 'rule_name',
            '{{%auth_rule}}', 'name',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_item_child_parent',
            '{{%auth_item_child}}', 'parent',
            '{{%auth_item}}', 'name',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_auth_item_child_child',
            '{{%auth_item_child}}', 'child',
            '{{%auth_item}}', 'name',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_cert_group_payer_id',
            '{{%cert_group}}', 'payer_id',
            '{{%payers}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_certificates_cert_group',
            '{{%certificates}}', 'cert_group',
            '{{%cert_group}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_certificates_payer_id',
            '{{%certificates}}', 'payer_id',
            '{{%payers}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_certificates_user_id',
            '{{%certificates}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_certificate_id',
            '{{%contracts}}', 'certificate_id',
            '{{%certificates}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_group_id',
            '{{%contracts}}', 'group_id',
            '{{%groups}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_organization_id',
            '{{%contracts}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_payer_id',
            '{{%contracts}}', 'payer_id',
            '{{%payers}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_program_id',
            '{{%contracts}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_contracts_year_id',
            '{{%contracts}}', 'year_id',
            '{{%years}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_cooperate_organization_id',
            '{{%cooperate}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_cooperate_payer_id',
            '{{%cooperate}}', 'payer_id',
            '{{%payers}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_disputes_contract_id',
            '{{%disputes}}', 'contract_id',
            '{{%contracts}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_disputes_user_id',
            '{{%disputes}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_favorites_certificate_id',
            '{{%favorites}}', 'certificate_id',
            '{{%certificates}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_favorites_organization_id',
            '{{%favorites}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_favorites_program_id',
            '{{%favorites}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_groups_organization_id',
            '{{%groups}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_groups_program_id',
            '{{%groups}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_groups_year_id',
            '{{%groups}}', 'year_id',
            '{{%years}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_informs_contract_id',
            '{{%informs}}', 'contract_id',
            '{{%contracts}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_informs_program_id',
            '{{%informs}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_invoices_completeness',
            '{{%invoices}}', 'completeness',
            '{{%completeness}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_invoices_organization_id',
            '{{%invoices}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_invoices_payers_id',
            '{{%invoices}}', 'payers_id',
            '{{%payers}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_operators_user_id',
            '{{%operators}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_organization_mun',
            '{{%organization}}', 'mun',
            '{{%mun}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_organization_user_id',
            '{{%organization}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_payers_mun',
            '{{%payers}}', 'mun',
            '{{%mun}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_payers_user_id',
            '{{%payers}}', 'user_id',
            '{{%user}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_previus_certificate_id',
            '{{%previus}}', 'certificate_id',
            '{{%certificates}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_previus_organization_id',
            '{{%previus}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_previus_program_id',
            '{{%previus}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_previus_year_id',
            '{{%previus}}', 'year_id',
            '{{%years}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_programs_mun',
            '{{%programs}}', 'mun',
            '{{%mun}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_programs_organization_id',
            '{{%programs}}', 'organization_id',
            '{{%organization}}', 'id',
            'CASCADE', 'CASCADE'
        );
        $this->addForeignKey(
            'fk_years_program_id',
            '{{%years}}', 'program_id',
            '{{%programs}}', 'id',
            'CASCADE', 'CASCADE'
        );
    }

    public function safeDown()
    {
        return false;
    }
}
