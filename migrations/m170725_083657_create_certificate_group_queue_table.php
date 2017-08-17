<?php

use yii\db\Migration;

/**
 * Handles the creation of table `certificate_group_queue`.
 */
class m170725_083657_create_certificate_group_queue_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('certificate_group_queue', [
            'certificate_id' => $this->integer()->notNull(),
            'cert_group_id' => $this->integer()->notNull(),
        ], $tableOptions);

        $this->addPrimaryKey('pk-certificate-group', 'certificate_group_queue', ['certificate_id', 'cert_group_id']
        );

        $this->addForeignKey('fk-certificate-group-certificate', 'certificate_group_queue', 'certificate_id', 'certificates', 'id', 'cascade', 'cascade');
        $this->addForeignKey('fk-certificate-group--group', 'certificate_group_queue', 'cert_group_id', 'cert_group', 'id', 'cascade', 'cascade');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('certificate_group_queue');
    }
}
