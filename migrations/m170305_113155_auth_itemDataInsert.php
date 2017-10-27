<?php

use yii\db\Migration;

class m170305_113155_auth_itemDataInsert extends Migration
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

        $this->batchInsert('{{%auth_item}}',
            ["name", "type", "description", "rule_name", "data", "created_at", "updated_at"],
            [
                [
                    'name' => 'admins',
                    'type' => '1',
                    'description' => 'Администраторы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463588986',
                    'updated_at' => '1487591670',
                ],
                [
                    'name' => 'cert-group',
                    'type' => '2',
                    'description' => 'Список групп сертификатов',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468141531',
                    'updated_at' => '1468141704',
                ],
                [
                    'name' => 'certificate',
                    'type' => '1',
                    'description' => 'Дети',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464687922',
                    'updated_at' => '1474188176',
                ],
                [
                    'name' => 'certificates',
                    'type' => '2',
                    'description' => 'Grid Детей',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464683910',
                    'updated_at' => '1464683910',
                ],
                [
                    'name' => 'certificates/edit',
                    'type' => '2',
                    'description' => 'Ребенок - редактировать профиль',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467996914',
                    'updated_at' => '1467996914',
                ],
                [
                    'name' => 'certificates/import',
                    'type' => '2',
                    'description' => 'certificates/import',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1481901177',
                    'updated_at' => '1481901177',
                ],
                [
                    'name' => 'certificates/verificate',
                    'type' => '2',
                    'description' => 'Проверить сертификат',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1470144741',
                    'updated_at' => '1470144746',
                ],
                [
                    'name' => 'certificates/view',
                    'type' => '2',
                    'description' => 'Просмотр сертификата',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1472824033',
                    'updated_at' => '1472824033',
                ],
                [
                    'name' => 'coefficient',
                    'type' => '2',
                    'description' => 'Коэффициенты',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1469187306',
                    'updated_at' => '1469187309',
                ],
                [
                    'name' => 'completeness',
                    'type' => '2',
                    'description' => 'GRUD Полнота услуг оказанных организацией',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467161029',
                    'updated_at' => '1467161029',
                ],
                [
                    'name' => 'contracts',
                    'type' => '2',
                    'description' => 'Grid Договоров',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464710979',
                    'updated_at' => '1464710979',
                ],
                [
                    'name' => 'contracts/animport',
                    'type' => '2',
                    'description' => '/contracts/animport',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1482885855',
                    'updated_at' => '1482885855',
                ],
                [
                    'name' => 'contracts/back',
                    'type' => '2',
                    'description' => 'кнопка назад)',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1473673456',
                    'updated_at' => '1473673456',
                ],
                [
                    'name' => 'contracts/cancel',
                    'type' => '2',
                    'description' => 'кнопка отмены',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1473673843',
                    'updated_at' => '1473673843',
                ],
                [
                    'name' => 'contracts/complete',
                    'type' => '2',
                    'description' => 'Показ расчета',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471487247',
                    'updated_at' => '1471487247',
                ],
                [
                    'name' => 'contracts/decper',
                    'type' => '2',
                    'description' => 'Досоздать декабрьские комплитнес',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1481109631',
                    'updated_at' => '1481109631',
                ],
                [
                    'name' => 'contracts/delete',
                    'type' => '2',
                    'description' => 'Удалить договор',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1473597187',
                    'updated_at' => '1473597187',
                ],
                [
                    'name' => 'contracts/dubles2',
                    'type' => '2',
                    'description' => 'дубли',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1480786116',
                    'updated_at' => '1480786118',
                ],
                [
                    'name' => 'contracts/good',
                    'type' => '2',
                    'description' => 'Сохоранение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471496601',
                    'updated_at' => '1471496601',
                ],
                [
                    'name' => 'contracts/group',
                    'type' => '2',
                    'description' => 'Выбор группы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1469718200',
                    'updated_at' => '1469718200',
                ],
                [
                    'name' => 'contracts/import',
                    'type' => '2',
                    'description' => '/contracts/import',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1482796900',
                    'updated_at' => '1482796903',
                ],
                [
                    'name' => 'contracts/mpdf',
                    'type' => '2',
                    'description' => 'Формирование договора',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1472646085',
                    'updated_at' => '1472646085',
                ],
                [
                    'name' => 'contracts/new',
                    'type' => '2',
                    'description' => 'Создать договор',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464702167',
                    'updated_at' => '1464705497',
                ],
                [
                    'name' => 'contracts/ocenka',
                    'type' => '2',
                    'description' => 'Оценка программы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1472388306',
                    'updated_at' => '1472388306',
                ],
                [
                    'name' => 'contracts/terminate',
                    'type' => '2',
                    'description' => 'Расторгнуть договор',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1473592267',
                    'updated_at' => '1473592267',
                ],
                [
                    'name' => 'contracts/updatescert',
                    'type' => '2',
                    'description' => 'contracts/updatescert',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1487591601',
                    'updated_at' => '1487591602',
                ],
                [
                    'name' => 'contracts/updatesparent',
                    'type' => '2',
                    'description' => 'contracts/updatesparent',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1487591625',
                    'updated_at' => '1487591625',
                ],
                [
                    'name' => 'contracts/view',
                    'type' => '2',
                    'description' => 'Просмотр договора',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1472387759',
                    'updated_at' => '1472387759',
                ],
                [
                    'name' => 'contracts/waitterm',
                    'type' => '2',
                    'description' => 'contracts/waitterm',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1482131873',
                    'updated_at' => '1482131873',
                ],
                [
                    'name' => 'cooperate/create',
                    'type' => '2',
                    'description' => 'Создать соглашение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468080236',
                    'updated_at' => '1468080239',
                ],
                [
                    'name' => 'cooperate/decooperate',
                    'type' => '2',
                    'description' => 'Расторгнуть соглашение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468086613',
                    'updated_at' => '1468086613',
                ],
                [
                    'name' => 'cooperate/delete',
                    'type' => '2',
                    'description' => 'Удалить соглашение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468082258',
                    'updated_at' => '1468082258',
                ],
                [
                    'name' => 'cooperate/index',
                    'type' => '2',
                    'description' => 'Все соглашения',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1473843153',
                    'updated_at' => '1473843153',
                ],
                [
                    'name' => 'cooperate/nopayer',
                    'type' => '2',
                    'description' => 'Отказ в сотрудничестве',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468086943',
                    'updated_at' => '1468086943',
                ],
                [
                    'name' => 'cooperate/okpayer',
                    'type' => '2',
                    'description' => 'Подтвердить соглашение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468086386',
                    'updated_at' => '1468086386',
                ],
                [
                    'name' => 'cooperate/read',
                    'type' => '2',
                    'description' => 'Прочитать оповещение о новой организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468084539',
                    'updated_at' => '1468084539',
                ],
                [
                    'name' => 'cooperate/view',
                    'type' => '2',
                    'description' => 'Просмотр соглашения',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468084715',
                    'updated_at' => '1468084715',
                ],
                [
                    'name' => 'cooperate/views',
                    'type' => '2',
                    'description' => 'Просмотр соглашения по оператору',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468085571',
                    'updated_at' => '1468085571',
                ],
                [
                    'name' => 'disputes',
                    'type' => '2',
                    'description' => 'GRUD возражений',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1466972389',
                    'updated_at' => '1466972389',
                ],
                [
                    'name' => 'favorites',
                    'type' => '2',
                    'description' => 'ГРУД избранного',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467305760',
                    'updated_at' => '1467305760',
                ],
                [
                    'name' => 'gii',
                    'type' => '2',
                    'description' => 'Gii',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463591262',
                    'updated_at' => '1463591262',
                ],
                [
                    'name' => 'groups',
                    'type' => '2',
                    'description' => 'ГРУД группы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467156229',
                    'updated_at' => '1467156229',
                ],
                [
                    'name' => 'groups/contracts',
                    'type' => '2',
                    'description' => 'Просмотр группы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474101171',
                    'updated_at' => '1474101171',
                ],
                [
                    'name' => 'groups/fgroup',
                    'type' => '2',
                    'description' => 'первая группа',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1479733468',
                    'updated_at' => '1479733468',
                ],
                [
                    'name' => 'informs/read',
                    'type' => '2',
                    'description' => 'Прочитать оповещение',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1465411829',
                    'updated_at' => '1465411831',
                ],
                [
                    'name' => 'invoices',
                    'type' => '2',
                    'description' => 'GRUD счетов',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467168159',
                    'updated_at' => '1467168159',
                ],
                [
                    'name' => 'invoices/complete',
                    'type' => '2',
                    'description' => 'Счет оплачен',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471972975',
                    'updated_at' => '1471972975',
                ],
                [
                    'name' => 'invoices/view',
                    'type' => '2',
                    'description' => 'Просмотр счета',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471971493',
                    'updated_at' => '1471971495',
                ],
                [
                    'name' => 'invoices/work',
                    'type' => '2',
                    'description' => 'Счет в работу',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471972949',
                    'updated_at' => '1471972949',
                ],

                [
                    'name' => 'mun',
                    'type' => '2',
                    'description' => 'Муниципалитеты',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468935234',
                    'updated_at' => '1468935234',
                ],
                [
                    'name' => 'operators',
                    'type' => '1',
                    'description' => 'Операторы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463590427',
                    'updated_at' => '1479753006',
                ],
                [
                    'name' => 'organization',
                    'type' => '2',
                    'description' => ' Grid Организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463668940',
                    'updated_at' => '1463722776',
                ],
                [
                    'name' => 'organization/actual',
                    'type' => '2',
                    'description' => 'Актуальность открыть',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1470582957',
                    'updated_at' => '1470582957',
                ],
                [
                    'name' => 'organization/noactual',
                    'type' => '2',
                    'description' => 'Актуальность закрыть',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1470582989',
                    'updated_at' => '1470582989',
                ],
                [
                    'name' => 'organization/view',
                    'type' => '2',
                    'description' => 'Просмотр организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1468086157',
                    'updated_at' => '1468086157',
                ],
                [
                    'name' => 'organizations',
                    'type' => '1',
                    'description' => 'Образовательные организации',
                    'rule_name' => null,
                    'data' => 's:408:"<div class="list-group">
  <a href="#" class="list-group-item disabled">
    Видео-уроки
  </a>
  <a href="https://www.youtube.com/watch?v=RKKArdOHLIk" target="_blank" class="list-group-item">Как начать заключать договоры по сертификатам. Обзор работы поставщика услуг и уполномоченной организации.</a>";',
                    'created_at' => '1463669140',
                    'updated_at' => '1503823232',
                ],
                [
                    'name' => 'payer',
                    'type' => '1',
                    'description' => 'Управления образования',
                    'rule_name' => null,
                    'data' => 's:408:"<div class="list-group">
  <a href="#" class="list-group-item disabled">
    Видео-уроки
  </a>
  <a href="https://www.youtube.com/watch?v=RKKArdOHLIk" target="_blank" class="list-group-item">Как начать заключать договоры по сертификатам. Обзор работы поставщика услуг и уполномоченной организации.</a>";',
                    'created_at' => '1463722319',
                    'updated_at' => '1503823255',
                ],
                [
                    'name' => 'payers',
                    'type' => '2',
                    'description' => 'Grid Управления',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463722631',
                    'updated_at' => '1463722635',
                ],
                [
                    'name' => 'permit/access',
                    'type' => '2',
                    'description' => 'Правила доступа',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463588956',
                    'updated_at' => '1463588956',
                ],
                [
                    'name' => 'permit/user',
                    'type' => '2',
                    'description' => 'Права пользователей',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463589192',
                    'updated_at' => '1463589271',
                ],
                [
                    'name' => 'personal',
                    'type' => '2',
                    'description' => 'Личные Кабинеты',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464012524',
                    'updated_at' => '1464012524',
                ],
                [
                    'name' => 'personal/certificate-archive',
                    'type' => '2',
                    'description' => 'Ребенок - архив',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474099256',
                    'updated_at' => '1474099256',
                ],
                [
                    'name' => 'personal/certificate-contracts',
                    'type' => '2',
                    'description' => 'Ребенок - Договора',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467768720',
                    'updated_at' => '1467768732',
                ],
                [
                    'name' => 'personal/certificate-favorites',
                    'type' => '2',
                    'description' => 'Ребенок - Избранное',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467768749',
                    'updated_at' => '1467768749',
                ],
                [
                    'name' => 'personal/certificate-info',
                    'type' => '2',
                    'description' => 'Ребенок - Сведения о сертификате',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467877563',
                    'updated_at' => '1467877612',
                ],
                [
                    'name' => 'personal/certificate-organizations',
                    'type' => '2',
                    'description' => 'Ребенок - Организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474187728',
                    'updated_at' => '1474187728',
                ],
                [
                    'name' => 'personal/certificate-previus',
                    'type' => '2',
                    'description' => 'Ребенок - Предварительная запись ',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471188233',
                    'updated_at' => '1471188233',
                ],
                [
                    'name' => 'personal/certificate-programs',
                    'type' => '2',
                    'description' => 'Ребенок - программы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467768699',
                    'updated_at' => '1467768699',
                ],
                [
                    'name' => 'personal/certificate-statistic',
                    'type' => '2',
                    'description' => 'Ребенок - Статическая информация',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464688633',
                    'updated_at' => '1467768673',
                ],
                [
                    'name' => 'personal/certificate-wait-contract',
                    'type' => '2',
                    'description' => 'Ребенок - ожидающие договоры',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474098193',
                    'updated_at' => '1474099309',
                ],
                [
                    'name' => 'personal/certificate-wait-request',
                    'type' => '2',
                    'description' => 'Ребенок - Мои заявки',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474117380',
                    'updated_at' => '1474117380',
                ],
                [
                    'name' => 'personal/operator-certificates',
                    'type' => '2',
                    'description' => 'Оператор - Сертификаты',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738095',
                    'updated_at' => '1467738095',
                ],
                [
                    'name' => 'personal/operator-coefficient',
                    'type' => '2',
                    'description' => 'Оператор - Установка коэффициентов',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738017',
                    'updated_at' => '1467738017',
                ],
                [
                    'name' => 'personal/operator-contracts',
                    'type' => '2',
                    'description' => 'Оператор - Договоры',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738123',
                    'updated_at' => '1467738123',
                ],
                [
                    'name' => 'personal/operator-info',
                    'type' => '2',
                    'description' => 'Оператор - Сведения об операторе',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467737983',
                    'updated_at' => '1467737983',
                ],
                [
                    'name' => 'personal/operator-organizations',
                    'type' => '2',
                    'description' => 'Оператор - Организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738067',
                    'updated_at' => '1467738067',
                ],
                [
                    'name' => 'personal/operator-payers',
                    'type' => '2',
                    'description' => 'Оператор - Плательщики',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738040',
                    'updated_at' => '1467738040',
                ],
                [
                    'name' => 'personal/operator-programs',
                    'type' => '2',
                    'description' => 'Оператор - Порграммы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467738148',
                    'updated_at' => '1467738148',
                ],
                [
                    'name' => 'personal/operator-statistic',
                    'type' => '2',
                    'description' => 'Оператора - статическая информация',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463590535',
                    'updated_at' => '1467750156',
                ],
                [
                    'name' => 'personal/organization-contracts',
                    'type' => '2',
                    'description' => 'Организация - Договоры',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763879',
                    'updated_at' => '1467764252',
                ],
                [
                    'name' => 'personal/organization-favorites',
                    'type' => '2',
                    'description' => 'Организация - В избранном',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763809',
                    'updated_at' => '1467763809',
                ],
                [
                    'name' => 'personal/organization-groups',
                    'type' => '2',
                    'description' => 'Организация - Группы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763967',
                    'updated_at' => '1467763967',
                ],
                [
                    'name' => 'personal/organization-info',
                    'type' => '2',
                    'description' => 'Организация - Сведения об организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763769',
                    'updated_at' => '1467763769',
                ],
                [
                    'name' => 'personal/organization-invoices',
                    'type' => '2',
                    'description' => 'Организация - Счета',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763908',
                    'updated_at' => '1467763908',
                ],
                [
                    'name' => 'personal/organization-payers',
                    'type' => '2',
                    'description' => 'Организация - Плательщики',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763941',
                    'updated_at' => '1467763941',
                ],
                [
                    'name' => 'personal/organization-programs',
                    'type' => '2',
                    'description' => 'Организация - Программы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467763843',
                    'updated_at' => '1467763843',
                ],
                [
                    'name' => 'personal/organization-statistic',
                    'type' => '2',
                    'description' => 'Организация - Статическая информация',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464015460',
                    'updated_at' => '1467763703',
                ],
                [
                    'name' => 'personal/payer-certificates',
                    'type' => '2',
                    'description' => 'Плательщик - Сертификаты',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756775',
                    'updated_at' => '1467756994',
                ],
                [
                    'name' => 'personal/payer-contracts',
                    'type' => '2',
                    'description' => 'Плательщик - Договоры',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756807',
                    'updated_at' => '1467756982',
                ],
                [
                    'name' => 'personal/payer-info',
                    'type' => '2',
                    'description' => 'Плательщик - Сведения о плательщике',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756746',
                    'updated_at' => '1467756988',
                ],
                [
                    'name' => 'personal/payer-invoices',
                    'type' => '2',
                    'description' => 'Плательщик -Счета',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756831',
                    'updated_at' => '1467757002',
                ],
                [
                    'name' => 'personal/payer-organizations',
                    'type' => '2',
                    'description' => 'Плательщик - Организации',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756885',
                    'updated_at' => '1467756885',
                ],
                [
                    'name' => 'personal/payer-programs',
                    'type' => '2',
                    'description' => 'Плательщик - Программы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1467756920',
                    'updated_at' => '1467756920',
                ],
                [
                    'name' => 'personal/payer-statistic',
                    'type' => '2',
                    'description' => 'Плательщик - Статическая информация',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1464682941',
                    'updated_at' => '1467757006',
                ],
                [
                    'name' => 'programs',
                    'type' => '2',
                    'description' => 'Grid Программы',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463748638',
                    'updated_at' => '1463748638',
                ],
                [
                    'name' => 'programs/previus',
                    'type' => '2',
                    'description' => 'Поиск Предварительная запись ',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1471190753',
                    'updated_at' => '1471190753',
                ],
                [
                    'name' => 'programs/search',
                    'type' => '2',
                    'description' => 'Поиск программ',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1474032759',
                    'updated_at' => '1474032759',
                ],
                [
                    'name' => 'rbac-access',
                    'type' => '2',
                    'description' => 'Управление ролями',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1489833897',
                    'updated_at' => '1489833897',
                ],
                [
                    'name' => 'user',
                    'type' => '2',
                    'description' => 'Список пользователей',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1463589050',
                    'updated_at' => '1463589109',
                ],
                [
                    'name' => 'years',
                    'type' => '2',
                    'description' => 'Года',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1469454057',
                    'updated_at' => '1469454057',
                ],
                [
                    'name' => 'years/allnormprice',
                    'type' => '2',
                    'description' => 'Глобальный пересчет нормативной стоимости',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1476953766',
                    'updated_at' => '1476953766',
                ],
                [
                    'name' => 'years/import',
                    'type' => '2',
                    'description' => 'года изменить',
                    'rule_name' => null,
                    'data' => null,
                    'created_at' => '1479752983',
                    'updated_at' => '1479752983',
                ],
            ]
        );
    }

    public function safeDown()
    {
        //$this->truncateTable('{{%auth_item}} CASCADE');
    }
}
