<?php

namespace app\models;

/*
 * Генерирует главное меню для разных ролей, а так же сообщения.
 * */
use Yii;
use yii\db\Expression;

class Menu
{
    public static function getByCurrentUser(): array
    {
        $webUser = \Yii::$app->user;
        if ($webUser->isGuest) {
            self::emitFoGuest();

            return self::getFoGuest();
        } elseif ($webUser->can(UserIdentity::ROLE_ADMINISTRATOR)) {
            self::emitFoAdmin();

            return array_merge(self::getFoAdmin(), [self::exitButton()]);
        } elseif ($webUser->can(UserIdentity::ROLE_OPERATOR)) {
            self::emitFoOperator();

            return array_merge(self::getFoOperator(), [self::exitButton()]);
        } elseif ($webUser->can(UserIdentity::ROLE_PAYER)) {
            self::emitFoPayer();

            return array_merge(self::getFoPayer(), [self::exitButton()]);
        } elseif ($webUser->can(UserIdentity::ROLE_ORGANIZATION)) {
            self::emitFoOrganization();

            return array_merge(self::getFoOrganization(), [self::exitButton()]);
        } elseif ($webUser->can(UserIdentity::ROLE_CERTIFICATE)) {
            self::emitFoCerificate();

            return array_merge(self::getFoCertificate(), [self::exitButton()]);
        }

        return null;
    }

    public static function emitFoGuest()
    {

    }

    public static function getFoGuest(): array
    {
        return [];
    }

    public static function emitFoAdmin()
    {

    }

    public static function getFoAdmin(): array
    {
        return [
            ['label' => 'Главная', 'url' => ['/personal/index']],
            [
                'label' => 'Ролевая система',
                'url'   => ['/personal/index'],
                'items' => [
                    ['label' => 'Правила доступа', 'url' => ['/permit/access/permission']],
                    ['label' => 'Управление ролями', 'url' => ['/rbac-access/role']],
                    ['label' => 'Список пользователей', 'url' => ['/user/index']],
                ],
            ],
            [
                'label' => 'Импорт данных',
                'url'   => ['/import/index'],
                'items' => [
                    ['label' => 'Дети (сертификаты)', 'url' => ['/import/children']],
                    ['label' => 'Шаблон импорта списка сертификатов', 'url' => ['/import/upload-certificate-import-template']],
                    ['label' => 'Корректировка паролей', 'url' => ['/import/children-password']],
                ],
            ],
            [
                'label' => 'Чистка данных',
                'url'   => ['#'],
                'items' => [
                    ['label' => 'Удаление договоров', 'url' => ['/admin/cleanup/contract']],
                    ['label' => 'Удаление сертификатов', 'url' => ['/admin/cleanup/certificate']],
                ],
            ],
            [
                'label' => 'Другое',
                'url'   => ['#'],
                'items' => [
                    [
                        'label' => 'Направленности программ',
                        'url'   => ['/admin/directory-program-direction/index']
                    ],
                    [
                        'label' => 'Настройки фильтров',
                        'url'   => ['/admin/search-filters/index']
                    ],
                    [
                        'label' => 'Руководство пользователей',
                        'url'   => ['/admin/help/index']
                    ],
                    [
                        'label' => 'Отчеты',
                        'url' => ['/reports']
                    ],
                    [
                        'label' => 'Запрет доступа к сайту',
                        'url' => ['/admin/site-restriction/list']
                    ]
                ],

            ]
        ];
    }

    public static function exitButton(): array
    {
        if (!Yii::$app->user->isGuest) {

            return [
                'label'       => 'Выйти(' . Yii::$app->user->identity->username . ')',
                'url'         => ['site/logout'],
                'linkOptions' => [
                    'data-method' => 'post',
                    'class'       => 'visible-sm visible-xs'
                ],
            ];

        } else {
            return null;
        }
    }

    public static function emitFoOperator()
    {
        $fields = ['p21v', 'p21s', 'p21o', 'p22v', 'p22s', 'p22o', 'p3v', 'p3s', 'p3n', 'blimrob', 'blimtex', 'blimest', 'blimfiz', 'blimxud', 'blimtur', 'blimsoc', 'minraiting', 'weekyear', 'weekmonth', 'pk', 'norm', 'potenc', 'ngr', 'sgr', 'vgr', 'chr1', 'zmr1', 'chr2', 'zmr2', 'ngrp', 'sgrp', 'vgrp', 'ppchr1', 'ppzm1', 'ppchr2', 'ppzm2', 'ocsootv', 'ocku', 'ocmt', 'obsh', 'ktob', 'vgs', 'sgs', 'pchsrd', 'pzmsrd'];
        /** @var $operator Operators */
        $operator = Yii::$app->operator->identity;
        $res = $operator->getCoefficient()
            ->select(['composition' => new Expression('(' . implode(' * ', $fields) . ')')])
            ->having(['composition' => 0])->exists();

        if ($res) {
            Yii::$app->session->setFlash('warning', 'Необходимо выставить корректные коэффициенты');
        }
    }

    public static function getFoOperator(): array
    {
        return [
            [
                'label' => 'Система',
                'items' => [
                    ['label' => 'Информация', 'url' => ['personal/operator-statistic']],
                    ['label' => 'Параметры системы', 'url' => ['operator/operator-settings']],
                    ['label' => 'Информационная рассылка', 'url' => ['/mailing']],

                ]
            ],
            ['label' => 'Коэффициенты', 'items' => [
                ['label' => 'Муниципалитеты', 'url' => ['/mun/index']],
                ['label' => 'Общие параметры', 'url' => ['/coefficient/update']],
                //['label' => 'Настройки системы', 'url' => ['/operators/params']],
            ]
            ],
            [
                'label' => 'Плательщики',
                'items' => [
                    [
                        'label' => 'Уполномоченные Организации',
                        'url'   => ['/personal/operator-payers']
                    ],
                    ['label' => 'Соглашения',
                     'url'   => ['/personal/operator-cooperates'],
                    ],
                    [
                        'label' => 'Счета',
                        'url'   => ['/personal/operator-invoices'],
                    ]

                ]
            ],
            ['label' => 'Организации', 'url' => ['/personal/operator-organizations']],
            ['label' => 'Сертификаты', 'url' => ['/personal/operator-certificates']],
            ['label' => 'Договоры', 'url' => ['/personal/operator-contracts']],
            ['label' => 'Программы', 'url' => ['/personal/operator-programs']],
            ['label' => 'Поддержка', 'items' => [
                [
                    'label' => 'Удаление договоров',
                    'url' => ['/operator/cleanup/contract'],
                ],
            ]],
        ];
    }

    public static function emitFoPayer()
    {
        Yii::$app->user->identity->payer->findUnconfirmedCooperates()
        && Yii::$app->session->setFlash(
            'error',
            'У Вас есть просроченные заявки на заключение соглашения/договора с поставщиком образовательных услуг. Пожалуйста, отработайте заявку для получения доступа к полному функционалу системы.'
        );
    }

    public static function getFoPayer(): array
    {
        if (Yii::$app->user->identity->payer->findUnconfirmedCooperates()) {
            return [
                ['label' => 'Организации', 'items' => [
                    ['label' => 'Реестр ПФДО', 'url' => ['/personal/payer-organizations']],
                    ['label' => 'Инструкции по работе в личном кабинете', 'url' => ['/site/manuals']],
                    //['label' => 'Подведомственные организации', 'url' => ['/personal/payer-suborder-organizations']],
                ]],
            ];
        } else {
            return \app\helpers\PermissionHelper::getMenuItems();
        }

    }

    public static function emitFoOrganization()
    {
        !Yii::$app->user->identity->organization->actual
        && Yii::$app->session->setFlash('warning', 'Ваша деятельность приостановлена, обратитесь к оператору, причина:' . Yii::$app->user->identity->organization->refuse_reason);

        Yii::$app->user->identity->organization->hasEmptyInfo()
        && Yii::$app->session->setFlash(
            'danger',
            Yii::$app->user->identity->organization->type === Organization::TYPE_IP_WITHOUT_WORKERS ? 'Внимание! Перед выставлением первой оферты (подтверждения первой заявки) необходимо заполнить информацию "сведения об организации". Зайдите в указанный раздел меню и нажмите большую синюю кнопку "Создать шапку для договоров-оферт", заполните окончания в предлагаемом диалоговом окне. Без этой процедуры формируемые оферты будут некорректны!'
                : 'Внимание! Перед выставлением первой оферты (подтверждения первой заявки) необходимо заполнить информацию "сведения об организации". Введите все параметры, нажмите сохранить и заполните окончания в предлагаемом диалоговом окне. Без этой процедуры формируемые оферты будут некорректны!'
        );
    }

    public static function getFoOrganization(): array
    {
        return [
            ['label' => 'Информация', 'items' => [
                [
                    'label' => 'Статистическая информация',
                    'url'   => ['/personal/organization-statistic']
                ],
                [
                    'label' => 'Сведения об организации',
                    'url'   => ['/personal/organization-info']
                ],
                [
                    'label' => 'Адреса реализации образовательных программ',
                    'url'   => ['/organization/address/index']
                ],
                [
                    'label' => 'Инструкции по работе в личном кабинете',
                    'url' => ['/site/manuals']
                ],
            ]],
            ['label' => 'Программы', 'items' => [
                [
                    'label' => 'Реестр программ',
                    'url'   => ['/personal/organization-programs']
                ],
                [
                    'label' => 'Программы по муниципальному заданию',
                    'url'   => ['/personal/organization-municipal-task']
                ],
            ]],
            ['label' => 'Договоры', 'items' => [
                [
                    'label' => 'Реестр договоров',
                    'url'   => ['/personal/organization-contracts']
                ],
                [
                    'label' => 'Договоры - муниципальное задание',
                    'url'   => ['/personal/organization-municipal-task-contracts']
                ],
            ]],
            ['label' => 'Счета', 'url' => ['/personal/organization-invoices']],
            ['label' => 'Плательщики', 'items' => [
                ['label' => 'Плательщики', 'url' => ['/personal/organization-payers']],
                ['label' => 'Подведомственность', 'url' => ['/personal/organization-suborder']],
            ]],
            ['label' => 'Группы', 'url' => ['/personal/organization-groups']],
            ['label' => 'Поддержка', 'items' => [
                [
                    'label' => 'Удаление договоров',
                    'url' => '#',
                    'linkOptions' => [
                        'class' => 'text-red-muted',
                        'id' => 'organization-menu-delete-order-link',
                        'data' => [
                            'url' => '/',
                            'toggle' => 'modal',
                            'target' => '#organization-menu-delete-order-modal',
                        ],
                    ],
                ],
            ]],
        ];
    }

    public static function emitFoCerificate()
    {

    }

    public static function getFoCertificate(): array
    {
        return [];
    }
}
