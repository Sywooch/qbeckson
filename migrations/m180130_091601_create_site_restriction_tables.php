<?php

use yii\db\Migration;

/**
 * Handles the creation of table `site_access`.
 */
class m180130_091601_create_site_restriction_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('site_restriction', [
            'id' => $this->primaryKey(),
            'type' => $this->string(50)->notNull()->comment('тип запрета доступа к сайту {@see SiteRestrictionType}'),
            'message' => $this->string()->notNull()->comment('сообщение причины запрета'),
            'status' => $this->boolean()->notNull()->comment('статус запрета {@see SiteRestrictionStatus}'),
        ], 'comment "ограничение доступа к сайту"');

        $this->createTable('site_restriction_cron_status', [
            'id' => $this->primaryKey(),
            'active' => $this->boolean()->notNull()->defaultValue(false)->comment('активен ли крон'),
        ], 'comment "статус работы крона для запрета доступа к сайту"');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('site_restriction_cron_status');
        $this->dropTable('site_restriction');
    }
}
