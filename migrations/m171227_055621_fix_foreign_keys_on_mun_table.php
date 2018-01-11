<?php

use yii\db\Migration;

/**
 * Class m171227_055621_fix_foreign_keys_on_mun_table
 * В таблице `mun` хрянятся муниципалитеты и заявки на изменение муниципалитетов, по упущению, на заявки были
 * добавлены связи некотороых записей в других таблицах. Миграция меняет связь с записью заявки на связь с самим муниципалитетом
 */
class m171227_055621_fix_foreign_keys_on_mun_table extends Migration
{
    public function up()
    {
        // изменяем связи user
        $userRows = $this->db->createCommand('UPDATE `user` 
            LEFT JOIN `mun` ON `user`.`mun_id` = `mun`.`id`
            SET `user`.`mun_id`= `mun`.`mun_id`
            WHERE ((`user`.`mun_id`= `mun`.`id`) AND NOT (`mun`.`mun_id` IS NULL))')
            ->execute();
        echo "Updated $userRows rows in user table\n";

        // изменяем связи organization
        $orgRows = $this->db->createCommand('UPDATE `organization` 
            LEFT JOIN `mun` ON `organization`.`mun` = `mun`.`id`
            SET `organization`.`mun`= `mun`.`mun_id`
            WHERE ((`organization`.`mun`= `mun`.`id`) AND NOT (`mun`.`mun_id` IS NULL))')
            ->execute();
        echo "Updated $orgRows rows in organization table\n";

        // изменяем связи payers
        $payersRows = $this->db->createCommand('UPDATE `payers` 
            LEFT JOIN `mun` ON `payers`.`mun` = `mun`.`id`
            SET `payers`.`mun`= `mun`.`mun_id`
            WHERE ((`payers`.`mun`= `mun`.`id`) AND NOT (`mun`.`mun_id` IS NULL))')
            ->execute();
        echo "Updated $payersRows rows in payers table\n";

        // изменяем связи programs
        $programsRows = $this->db->createCommand('UPDATE `programs` 
            LEFT JOIN `mun` ON `programs`.`mun` = `mun`.`id`
            SET `programs`.`mun`= `mun`.`mun_id`
            WHERE ((`programs`.`mun`= `mun`.`id`) AND NOT (`mun`.`mun_id` IS NULL))')
            ->execute();
        echo "Updated $programsRows rows in programs table\n";
    }

    public function down()
    {
        echo "m171227_055621_fix_foreign_keys_on_mun_table cannot be reverted. Skipped.\n";
    }
}
