<?php

function table($db)
{
    // admins_step
    $db->create('admins', [
        'user_id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
        ],
        'step' => [
            'TEXT',
        ],
        'data' => [
            'TEXT',
        ],
        'access' => [
            'TEXT',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // apis 
    $db->create('apis', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'smart_panel' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'name' => [
            'TEXT',
        ],
        'api_url' => [
            'TEXT',
        ],
        'api_key' => [
            'TEXT',
        ],
        'status' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'multi' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // categorys
    $db->create('categories', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'name' => [
            'varchar(2048)',
        ],
        'category_id' => [
            'INT',
            'NULL',
            'DEFAULT NULL',
        ],
        'status' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'ordering' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // chaneels
    $db->create('gift_code', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'status' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'type' => [
            'VARCHAR(200)',
            'NOT NULL',
        ],
        'count' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'amount' => [
            'TEXT',
        ],
        'code' => [
            'VARCHAR(200)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'max_amount' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'ids' => [
            'VARCHAR(8192)',
            'NOT NULL',
            "DEFAULT '[]'",
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // messages
    $db->create('jobs', [
        'id' => [
            'INT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'step' => [
            'TEXT',
        ],
        'info' => [
            'TEXT',
        ],
        'user' => [
            'BIGINT',
            'NOT NULL',
        ],
        'admin' => [
            'BIGINT',
            'NOT NULL',
        ],
        'lock_job' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'paused' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'last_job' => [
            'BIGINT',
            'NOT NULL',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // orders
    $db->create('orders', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT'
        ],
        'status' => [
            'VARCHAR(200)',
            'NOT NULL',
        ],
        'user_id' => [
            'BIGINT',
            'NOT NULL',
        ],
        'price' => [
            'float',
            'NOT NULL',
        ],
        'count' => [
            'INT',
            'NOT NULL',
        ],
        'product' => [
            'TEXT',
        ],
        'link' => [
            'TEXT',
        ],
        'date' => [
            'BIGINT',
            'NOT NULL',
        ],
        'api' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'noapi'",
        ],
        'code_api' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 0",
        ],
        'extra_data' => [
            'TEXT',
        ],
    ], [
        'AUTO_INCREMENT' => 1000,
        'CHARSET' => 'utf8mb4'
    ]);

    // pattern
    $db->create('pattern', [
        'id' => [
            'INT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT'
        ],
        'type' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'default'",
        ],
        'pattern' => [
            'TEXT',
        ],
        'text' => [
            'TEXT',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // payment_gateways
    $db->create('payment_gateways', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'name' => [
            'VARCHAR(500)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'code' => [
            'VARCHAR(500)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'file' => [
            'VARCHAR(500)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'type' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'IRT'",
        ],
        'status' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'ip' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'data' => [
            'TEXT',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);
    // products
    $db->create('products', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'category_id' => [
            'BIGINT',
            'NOT NULL',
        ],
        'name' => [
            'VARCHAR(2048)',
            'NOT NULL',
        ],
        'price' => [
            'float',
            'NOT NULL',
            "DEFAULT 0",
        ],
        'min' => [
            'BIGINT',
            'NOT NULL',
            "DEFAULT 1",
        ],
        'max' => [
            'BIGINT',
            'NOT NULL',
            "DEFAULT 1",
        ],
        'info' => [
            'text',
        ],
        'api' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'noapi'",
        ],
        'service' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 0",
        ],
        'type' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'default'",
        ],
        'pattern' => [
            'VARCHAR(500)',
            'NOT NULL',
            "DEFAULT 'default'",
        ],
        'status' => [
            'INT(1)',
            'NOT NULL',
            "DEFAULT 1",
        ],
        'discount' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'confirm' => [
            'INT(1)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'ordering' => [
            'INT',
            'NOT NULL',
            'DEFAULT 1',
        ],
        'price_usd' => [
            'FLOAT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'cost_price' => [
            'FLOAT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'is_usd' => [
            'TINYINT(1)',
            'NOT NULL',
            'DEFAULT 0',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);

    // settings
    $db->create('setting_options', [
        'id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'option_key' => [
            'TEXT',
        ],
        'option_value' => [
            'TEXT',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);

    // transactions_payments
    $db->create('transactions', [
        'id' => [
            'INT',
            'NOT NULL',
            'PRIMARY KEY',
            'AUTO_INCREMENT',
        ],
        'user_id' => [
            'BIGINT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'status' => [
            'INT(1)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'type' => [
            'VARCHAR(200)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'amount' => [
            'BIGINT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'data' => [
            'TEXT',
        ],
        'date' => [
            'BIGINT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        's_date' => [
            'BIGINT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'tracking_code' => [
            'varchar(1024)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'getway' => [
            'varchar(1024)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'INDEX idx_tracking_code (tracking_code(191))',
    ], [
        'AUTO_INCREMENT' => 1000,
        'CHARSET' => 'utf8mb4'
    ]);

    // users_information
    $db->create('users_information', [
        'user_id' => [
            'BIGINT',
            'NOT NULL',
            'PRIMARY KEY',
        ],
        'step' => [
            'TEXT',
        ],
        'balance' => [
            'float',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'number' => [
            'VARCHAR(200)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'payment_card' => [
            'VARCHAR(200)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'link' => [
            'TEXT',
        ],
        'data' => [
            'TEXT',
        ],
        'type' => [
            'VARCHAR(100)',
            'NOT NULL',
            "DEFAULT 'default'",
        ],
        'number_order' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'amount_paid' => [
            'FLOAT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'amount_spent' => [
            'FLOAT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'referral_id' => [
            'VARCHAR(512)',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'referral' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'gift_referral' => [
            'float',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'gift_payment' => [
            'float',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'gift' => [
            'float',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'ticket' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'block' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'block_bot' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'last_msg' => [
            'VARCHAR(200)',
            'NOT NULL',
            "DEFAULT '" . json_encode(["last_msg" => 0, "block" => 0]) . "'",
        ],
        'last_sms' => [
            'VARCHAR(200)',
            'NOT NULL',
            "DEFAULT '" . json_encode(["last_sms" => 0, "c" => 0]) . "'",
        ],
        'discount' => [
            'INT',
            'NOT NULL',
            'DEFAULT 0',
        ],
        'join_date' => [
            'BIGINT',
            'NOT NULL',
            'DEFAULT 0',
        ],
    ], [
        'CHARSET' => 'utf8mb4'
    ]);


    return true;
}

function first_data($db, $admin)
{
    $accessConfig = [
        "main" => array_fill_keys([
            "status",
            "sendall",
            "userinfo",
            "settings",
            "apis",
            "products",
            "payments",
            "channels",
            "referral",
            "text",
            "notification_price"
        ], 1),
        "sub" => array_fill_keys(["ch_order", "support", "card", "payout"], 1)
    ];
    $accessConfig["main"]["status"] = 1;

    $db->insert('admins', ['user_id' => $admin, 'access[JSON]' => $accessConfig]);

    $db->insert('pattern', [
        'type' => 'default',
        'pattern' => "/^[a-zA-Z0-9$-\/:-?@{-~!'^_`\[\] ]+$/",
        'text' => 'لینک مورد نظر خود را وارد کنید'
    ]);

    $db->insert('payment_gateways', [
        'name' => 'زرین پال',
        'code' => 'zarinpal',
        'file' => 'zarinpal',
        'status' => 1
    ]);

    $settings = [
        'display_category' => ['row' => [1], 'page' => 30, 'sort' => 'ASC', 'sort_by' => 'ordering'],
        'display_sub_category' => ['row' => [1], 'page' => 15, 'sort' => 'ASC', 'sort_by' => 'ordering'],
        'display_products' => ['row' => [2], 'page' => 15, 'sort' => 'ASC', 'sort_by' => 'price'],
        'section_status' => [
            'main' => array_fill_keys(['bot', 'buy', 'payment', 'member', 'free', 'support'], 0),
            'free' => array_fill_keys(['number', 'sms', 'gift_payment', 'gift_referral', 'gift_start', 'withdraw_balance', 'change_gift_balance'], 0),
            'payment' => array_fill_keys(['number', 'sms', 'offline_payment', 'verify_card', 'move_balance', 'online_payment', 'authentication', 'gift_code', 'gift_charge','crypto_payment','starz_payment'], 0),
            'buy' => ['order_msg' => 0]
        ]
    ];
    $settings['section_status']['main']['bot'] = 1;
    $settings['section_status']['payment']['online_payment'] = 1;

    foreach ($settings as $option_value => $option_key) {
        $db->insert('setting_options', ['option_key' => $option_value, 'option_value[JSON]' => $option_key]);
    }

    $staticSettings = [
        'text_start' => 'سلام خوش آمدید',
        'number_float' => 0,
        'last_cron_send' => 0,
        'last_cron_orders' => 0,
        'version' => '9.2',
        'sall' => 50,
        'fall' => 30,
        'DIFF_TIME' => 0,
        'ticket' => 3,
        'from_number' => 0,
        'pas_sms' => 0,
        'user_sms' => 0,
        'ptid_ref' => 0,
        'ptid_payment' => 0,
        's_spam' => 0.1,
        's_block' => 30,
        'limit' => 300,
        'limit_multi' => 100,
        'order_unknown_days' => 15,
        'min_deposit' => 1000,
        'max_deposit' => 500000,
        'min_crypto_deposit' => 10000,
        'max_crypto_deposit' => 1000000,
        'min_move_balance' => 1000,
        'min_kyc' => 100000,
        'daily_limit' => 400000,
        'channel_main' => 0,
        'channel_lock' => 0,
        'channel_transaction' => $admin,
        'channel_payment_offline' => $admin,
        'channel_ads' => 0,
        'channel_order_api' => $admin,
        'channel_order_noapi' => $admin,
        'channel_support' => $admin,
        'channel_kyc' => $admin,
        'channel_gift_transaction' => $admin,
        'channel_errors' => $admin,
        'kyc_media' => 'photo',
        'baner_tx' => '🤖 ربات خدمات مجازی (رایگان؛پولی)
	🔰 تو این ربات میتونید تمامی خدمات شبکه های اجتماعی مثل؛فالوور، ممبر، لایک، کامنت، ویو، و... رو دریافت کنید، اونهم به صورت رایگان!
	
	👇🏻 همین الان وارد این ربات فوق العاده شو',
        'gift_referral' => 1000,
        'gift_payment' => 10,
        'gift_start' => 5000,
        'min_payment_gift' => 10000,
        'min_move_gift' => 5000,
        'p2p' => '💳 درصورتی که امکان خرید به صورت آنلاین و با رمز دوم ندارید میتوانید پرداخت را آفلاین انجام دهید ! برای راهنمایی بیشتر به پشتیبانی مراجعه کنید.',
        'text_payment' => 'درصورت پرداخت موفق حساب شما به صورت خودکار شارژ میشود.',
        'text_payment_crypto' => 'پس از تأیید تراکنش ارز دیجیتال، حساب شما به صورت خودکار شارژ خواهد شد.',
        'text_order' => 'ممنون از اعتما شما. به منوی اصلی بازگشتید',
        'text_kyc' => 'دوست عزیز به دلیل بالا بودن مبلغ واریزی و امنیت بیشتر و جلوگیری از پرداخت های فیشینگ میبایست یک کارت را احراز کنید

لطفا عکس یک کارت بانکی که قصد دارید تراکنش های خودتون را با آن کارت انجام بدید ارسال کنید داخل عکس میتوانید موارد امنیتی از جمله CVV2 ، تاریخ انقضاح را بپوشانید',
        'usd_rate' => 0,
        'last_order_page' => 10,
        'last_transactions_page' => 10,
        'cron_order_lock' => 0,
        'delay_time_sms' => 300,
        'starz_rate' => 1000,
        'min_starz_deposit' => 1,
        'max_starz_deposit' => 2500,
        'usd_rate_mode' => 'manual',
        'usd_rate_interval' => 60,
        'usd_rate_last_update' => 0,
        'starz_rate_usd' => 0,
        'auto_starz_rate' => 0,
    ];


    foreach ($staticSettings as $key => $value) {
        $st = null;
        $st = ['option_key' => $key, 'option_value' => $value];
        $db->insert('setting_options', $st);
    }
}


function ping($host, $port = 80, $timeout = 30)
{
    $t = [];
    for ($i = 0; $i < 1; $i++) {
        $tB = microtime(true);
        $fP = fSockOpen($host, $port, $errno, $errstr, $timeout);
        if ($errstr == 'Connection refused') {
            $t[] = "down";
        } else {
            $tA = microtime(true);
            $t[] = round((($tA - $tB) * 1000), 0) . " ms";
        }
    }

    return $t;
}

function checktoken($token)
{
    $ex = explode(':', $token);
    if ($ex !== null and is_numeric($ex[0]) and strlen($ex[1]) < 40) {
        return 'OK';
    } else {
        return 'false';
    }
}
