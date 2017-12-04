<?php

use yii\db\Migration;

/**
 * Handles the creation of table `certificate_import_template`.
 */
class m171129_051146_create_certificate_import_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('certificate_import_template', [
            'id' => $this->primaryKey(),
            'path' => $this->string()->comment('путь к шаблону импорта списка сертификатов'),
            'base_url' => $this->string()->comment('ссылка к шаблону импорта списка сертификатов'),
        ], 'COMMENT "шаблон импорта сертификатов"');

        $this->createTable('certificate_import_registry', [
            'id' => $this->primaryKey(),
            'payer_id' => $this->integer(11)->notNull()->comment('id плательщика импортирующего список сертификатов'),
            'certificate_list_for_import_path' => $this->string(255)->comment('путь до файла со списком импортируемых сертификатов'),
            'certificate_list_for_import_base_url' => $this->string(255)->comment('ссылка для файла со списком импортируемых сертификатов'),
            'registry_path' => $this->string(255)->comment('путь до файла с реестром импортированных сертификатов и пользователей'),
            'registry_base_url' => $this->string(255)->comment('ссылка для файла с реестром импортированных сертификатов и пользователей'),
            'is_registry_downloaded' => $this->boolean()->defaultValue(0)->comment('был ли скачан реестр после импорта списка сертификатов и пользователей'),
            'registry_created_at' => $this->dateTime()->comment('дата и время создания файла реестра'),
        ], 'COMMENT "реестр в котором хранится список импортированных сертификатов и пользователей"');

        $this->addForeignKey('fk_certificate_import_buffer_payer', 'certificate_import_registry', 'payer_id', 'payers', 'id', 'CASCADE', 'CASCADE');
        
        $this->createTable('user_created_on_certificate_import_log', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(11)->notNull()->comment('id созданного пользователя'),
            'created_at' => $this->dateTime()->comment('дата и время создания пользователя'),
        ], 'COMMENT "таблица с id пользователей, созданных при импорте списка сертификатов."');

        $this->addForeignKey('fk_user_created_user', 'user_created_on_certificate_import_log', 'user_id', 'user', 'id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('user_created_on_certificate_import_log');
        $this->dropTable('certificate_import_registry');
        $this->dropTable('certificate_import_template');
    }
}
