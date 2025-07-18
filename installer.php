<?php
if (!file_exists('config-new.php')) {
    exit('<h1 style="text-align: center;margin-top:30px">فایل config-new.php یافت نشد</h1>');
}

define('DIR', __DIR__);
require DIR . '/config-new.php';
require ROOTPATH . '/include/Medoo.php';
require ROOTPATH . '/include/hkbot.php';
require ROOTPATH . '/include/install.php';
require ROOTPATH . '/bot_file/function/function.php';
require ROOTPATH . '/bot_file/function/function_tel.php';

function getDomain()
{
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    return dirname(preg_replace('/^https?:\/\//', '', preg_replace('/\?.*$/', '', $url)));
}

function connectDatabase($config)
{
    return new Medoo\Medoo([
        'type' => 'mysql',
        'host' => $config['host'],
        'database' => $config['database'],
        'username' => $config['username'],
        'password' => $config['password'],
        'charset' => 'utf8mb4',
        'collation' => 'utf8mb4_general_ci',
        'prefix' => $config['prefix'],
        'option' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_CASE => PDO::CASE_NATURAL,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false
        ]
    ]);
}

function installBot($db, $bot, $admin, $lic, $type_license)
{
    if (!table($db)) {
        exit('<h1 style="text-align: center;margin-top:30px">در ساخت دیتابیس خطا رخ داده است</h1>');
    }
    first_data($db, $admin);
}

function setupWebhook($bot, $ip, $random_code)
{
    $url = 'https://' . getDomain() . '/index.php';
    return $bot->bot('setwebhook', [
        'url' => $url,
        'drop_pending_updates' => true,
        'secret_token' => $random_code,
        'ip_address' => $ip,
        'allowed_updates' => json_encode([
            "message",
            "edited_message",
            "channel_post",
            "edited_channel_post",
            "callback_query",
            "chat_member",
            "my_chat_member"
        ])
    ]);
}

function validateLicense($idbot, $license, $type_license, $host)
{
    $response = verifyLicense($idbot, $license, $type_license, $host);
    if ($response !== 'verified') {
        exit('<h1 style="text-align: center;margin-top:30px">کد لایسنس وارد شده معتبر نیست</h1>');
    }
}

if (isset($_GET['install']) || ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['install']))) {
    function input($data)
    {
        return htmlspecialchars(stripslashes(trim($data)));
    }

    $dbConfig = [
        'host' => localhost,
        'database' => isset($_POST['db_name']) ? input($_POST['db_name']) : db_name,
        'username' => isset($_POST['db_username']) ? input($_POST['db_username']) : db_username,
        'password' => isset($_POST['db_password']) ? input($_POST['db_password']) : db_password,
        'prefix' => isset($_POST['BOT']) ? input(strtolower($_POST['BOT'])) . '_' : prefix
    ];

    $Token = isset($_POST['Token']) ? input($_POST['Token']) : Token;
    $admin = isset($_POST['admin']) ? input($_POST['admin']) : admins[0];
    $license = isset($_POST['lic']) ? input($_POST['lic']) : license;
    $idbot = isset($_POST['BOT']) ? input(strtolower($_POST['BOT'])) : prefix;

    try {
        $db = connectDatabase($dbConfig);
        $db->query("SELECT 1");

        if ($db->query("SHOW TABLES LIKE '" . $dbConfig['prefix'] . "users_information'")->fetch()) {
            exit('<h1 style="text-align: center;margin-top:30px">ربات قبلا نصب شده است</h1>');
        }

        $type_license = strpos($license, '-') !== false ? 2 : 1;
        $bot = new hkbot($Token);
        $getBotInfo = $bot->bot('getMe');

        if (checktoken($Token) !== 'OK' || !is_numeric($admin) || !$getBotInfo['ok']) {
            exit('<h1 style="text-align: center;margin-top:30px">توکن یا ایدی مدیریت نادرست است</h1>');
        }

        validateLicense($getBotInfo['result']['username'], $license, $type_license, $_SERVER['HTTP_HOST']);
        installBot($db, $bot, $admin, $license, $type_license);

        $random_code = random_code(rand(32, 64));
        $hash_random_code = md5($random_code);
        setupWebhook($bot, $_SERVER['SERVER_ADDR'], $random_code);

        $config = file_get_contents(ROOTPATH . '/config-new.php');
        if (isset($_POST)) {
            file_put_contents(ROOTPATH . '/config.php', str_replace([
                '*SEC*',
                '*TYPELICENCE*',
                '*LIC*',
                '*ADMIN*',
                '*TOKEN*',
                '*DBNAME*',
                '*DBUSERNAME*',
                '*DBPASSWORD*',
                '*PERFIX*',
            ], [
                $hash_random_code,
                $type_license,
                $license,
                $admin,
                $Token,
                $dbConfig['database'],
                $dbConfig['username'],
                $dbConfig['password'],
                $dbConfig['prefix'],
            ], $config));
        } else {
            file_put_contents(ROOTPATH . '/config.php', str_replace(['*SEC*', '*TYPELICENCE*'], [$hash_random_code, $type_license], $config));
        }
        $bot->sm($admin, "ربات با موفقیت نصب شد\n/start را ارسال کنید");
        unlink(ROOTPATH . '/config-new.php');
        unlink(ROOTPATH . '/installer.php');
        echo '<h1 style="text-align: center;margin-top:30px">ربات با موفقیت نصب شد</h1>';
    } catch (PDOException $e) {
        exit('<h1 style="text-align: center;margin-top:30px">مشکل در اتصال به دیتابیس</h1><h3>' . $e->getMessage() . '</h3>');
    } catch (Exception $e) {
        exit('<h1 style="text-align: center;margin-top:30px">خطای غیرمنتظره</h1><h3>' . $e->getMessage() . '</h3>');
    }
} elseif (!file_exists('license.txt')) {
    $ping = ping("api.telegram.org", 80, 10);
    $ping = implode(' - ', $ping);
    $domin = getDomain();
?>
    <!DOCTYPE html>
    <html>

    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <style>
            .background {
                position: fixed;
                z-index: -1;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                object-fit: cover;
                height: 100%;
                width: 100%;
            }

            .form-btn,
            .form-btn-cancel,
            .form-btn-error {
                background: transparent;
                font-size: 1rem;
                color: #fff;
                cursor: pointer;
                border: 1px solid transparent;
                padding: 5px 24px;
                margin-top: 2.25rem;
                position: relative;
                z-index: 0;
                transition: transform 0.28s ease;
                will-change: transform;
            }

            .form-btn::before,
            .form-btn::after,
            .form-btn-cancel::before,
            .form-btn-cancel::after,
            .form-btn-error::before,
            .form-btn-error::after {
                position: absolute;
                content: "";
                top: -1px;
                left: -1px;
                right: -1px;
                bottom: -1px;
            }

            .form-btn::before,
            .form-btn-cancel::before,
            .form-btn-error::before {
                background: #920dbb;
                z-index: -2;
            }

            .form-btn::after,
            .form-btn-cancel::after,
            .form-btn-error::after {
                background: #000;
                z-index: -1;
                opacity: 0;
                transition: opacity 0.28s ease;
                will-change: opacity;
            }

            .form-btn:focus,
            .form-btn-cancel:focus,
            .form-btn-error:focus {
                outline: none;
            }

            .form-btn:focus::after,
            .form-btn:hover::after,
            .form-btn-cancel:focus::after,
            .form-btn-cancel:hover::after,
            .form-btn-error:focus::after,
            .form-btn-error:hover::after {
                opacity: 0.3;
            }

            .form-btn:active,
            .form-btn-cancel:active,
            .form-btn-error:active {
                transform: translateY(1px);
            }

            .form-btn-error::before {
                background: #dd0000;
            }

            .form-btn-cancel {
                transition: color 0.28s ease, transform 0.28s ease;
                color: #ff0000;
                border-color: currentColor;
                will-change: color, transform;
            }

            .form-btn-cancel.-nooutline {
                border-color: transparent;
            }

            .form-btn-cancel::before {
                background: #b52b27;
                opacity: 0;
                transition: opacity 0.28s ease;
                will-change: opacity;
            }

            .form-btn-cancel::after {
                display: none;
            }

            .form-btn-cancel:focus,
            .form-btn-cancel:hover {
                color: rgb(255, 255, 255);
            }

            .form-btn-cancel:focus::before,
            .form-btn-cancel:hover::before {
                opacity: 1;
            }


            .form-has-error .form-checkbox-button,
            .form-has-error .form-radio-button {
                color: #d9534f;
            }

            .form-card {
                border-radius: 2px;
                background: #fff;
                box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
                transition: all 0.56s cubic-bezier(0.25, 0.8, 0.25, 1);
                max-width: 500px;
                padding: 0;
                margin: 50px auto;
            }

            .form-card:hover,
            .form-card:focus {
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            }

            .form-card:focus-within {
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
            }

            .form-actions {
                position: relative;
                display: -ms-flexbox;
                display: flex;
                margin-top: 2.25rem;
            }

            .form-actions .form-btn-cancel {
                -ms-flex-order: -1;
                order: -1;
            }

            .form-actions::before {
                content: "";
                position: absolute;
                width: 100%;
                height: 1px;
                background: #999;
                opacity: 0.3;
            }

            .form-actions>* {
                -ms-flex: 1;
                flex: 1;
                margin-top: 0;
            }

            .form-fieldset {
                padding: 30px;
                border: 0;
            }

            .form-fieldset+.form-fieldset {
                margin-top: 15px;
            }

            .form-legend {
                padding: 1em 0 0;
                margin: 0 0 -0.5em;
                font-size: 1.5rem;
                text-align: center;
            }

            .form-legend+p {
                margin-top: 1rem;
            }

            .form-element {
                position: relative;
                margin-top: 2.25rem;
                margin-bottom: 2.25rem;
            }

            .form-element-hint {
                font-weight: 400;
                font-size: 0.6875rem;
                color: #ff0000;
                display: block;
            }

            .form-element-bar {
                position: relative;
                height: 1px;
                background: #999;
                display: block;
            }

            .form-element-bar::after {
                content: "";
                position: absolute;
                bottom: 0;
                left: 0;
                right: 0;
                background: #337ab7;
                height: 2px;
                display: block;
                transform: rotateY(90deg);
                transition: transform 0.28s ease;
                will-change: transform;
            }

            .form-element-label {
                position: absolute;
                top: 0.75rem;
                line-height: 1.5rem;
                pointer-events: none;
                padding-left: 0.125rem;
                z-index: 1;
                font-size: 1rem;
                font-weight: normal;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                margin: 0;
                color: #1a1a1a;
                transform: translateY(-50%);
                transform-origin: left center;
                transition: transform 0.28s ease, color 0.28s linear, opacity 0.28s linear;
                will-change: transform, color, opacity;
            }

            .form-element-field {
                outline: none;
                height: 1.5rem;
                display: block;
                background: none;
                padding: 0.125rem 0.125rem 0.0625rem;
                font-size: 1rem;
                border: 0 solid transparent;
                line-height: 1.5;
                width: 100%;
                color: #1a1a1a;
                box-shadow: none;
                opacity: 100;
                transition: opacity 100s ease;
                will-change: opacity;
            }

            .form-element-field:-ms-input-placeholder {
                color: #1a1a1a;
                transform: scale(0.9);
                transform-origin: left top;
            }

            .form-element-field::placeholder {
                color: #1a1a1a;
                transform: scale(0.9);
                transform-origin: left top;
            }

            .form-element-field:focus~.form-element-bar::after {
                transform: rotateY(0deg);
            }

            .form-element-field:focus~.form-element-label {
                color: #337ab7;
            }

            .form-element-field.-hasvalue,
            .form-element-field:focus {
                opacity: 1;
            }

            .form-element-field.-hasvalue~.form-element-label,
            .form-element-field:focus~.form-element-label {
                transform: translateY(-100%) translateY(-0.5em) translateY(-2px) scale(0.9);
                cursor: pointer;
                pointer-events: auto;
            }

            .form-has-error .form-element-label.form-element-label,
            .form-has-error .form-element-hint {
                color: #d9534f;
            }

            .form-has-error .form-element-bar,
            .form-has-error .form-element-bar::after {
                background: #d9534f;
            }

            .form-is-success .form-element-label.form-element-label,
            .form-is-success .form-element-hint {
                color: #259337;
            }

            .form-is-success .form-element-bar::after {
                background: #259337;
            }

            input.form-element-field:not(:placeholder-shown),
            textarea.form-element-field:not(:placeholder-shown) {
                opacity: 1;
            }

            input.form-element-field:not(:placeholder-shown)~.form-element-label,
            textarea.form-element-field:not(:placeholder-shown)~.form-element-label {
                transform: translateY(-100%) translateY(-0.5em) translateY(-2px) scale(0.9);
                cursor: pointer;
                pointer-events: auto;
            }

            textarea.form-element-field {
                height: auto;
                min-height: 3rem;
            }

            select.form-element-field {
                -webkit-appearance: none;
                -moz-appearance: none;
                appearance: none;
                cursor: pointer;
            }

            .form-select-placeholder {
                color: #1a1a1a;
                display: none;
            }

            .form-select .form-element-bar::before {
                content: "";
                position: absolute;
                height: 0.5em;
                width: 0.5em;
                border-bottom: 1px solid #999;
                border-right: 1px solid #999;
                display: block;
                right: 0.5em;
                bottom: 0;
                transition: transform 0.28s ease;
                transform: translateY(-100%) rotateX(0deg) rotate(45deg);
                will-change: transform;
            }

            .form-select select:focus~.form-element-bar::before {
                transform: translateY(-50%) rotateX(180deg) rotate(45deg);
            }

            .form-element-field[type="number"] {
                -moz-appearance: textfield;
            }

            .form-element-field[type="number"]::-webkit-outer-spin-button,
            .form-element-field[type="number"]::-webkit-inner-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            body {
                margin: 40px auto;
                background: rgb(63, 94, 251);
                background: radial-gradient(circle, rgba(63, 94, 251, 1) 0%, rgba(252, 70, 107, 1) 100%);
            }
        </style>
        <title>نصب ربات</title>
    </head>

    <body dir="rtl">
        <form class="form-card" method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <fieldset class="form-fieldset">
                <legend class="form-legend">راه اندازی ربات</legend>
                <legend class="form-legend"><a href="tg://resolve?domain=SourceHK">@SourceHK</a>
                    <br />برای دریافت اخرین اخبار و اپدیت ها داخل کانال عضو شوید
                </legend>
                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" value="<?php echo $ping; ?>" readonly />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">پینگ هاست شما به سرور های تلگرام</label>
                </div>
                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" required name="Token" />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">توکن ربات</label>
                </div>

                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" required name="BOT" />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">آیدی ربات</label>
                </div>

                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" required name="admin" />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">آیدی عددی ادمین</label>
                </div>

                <div class="form-element form-input">
                    <input class="form-element-field" placeholder=" " type="text" required name="lic" />
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">لایسنس</label>
                    <small class="form-element-hint">برای دریافت لایسنس تیکت ارسال کنید</small>
                </div>
                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" value="localhost" name="localhost" readonly />
                    <div class="form-element-bar"></div>
                    <!--<label class="form-element-label">localhost</label>-->
                    <small class="form-element-hint">درصورت نیاز داخل فایل config.php خط 10 تغییر دهید</small>
                </div>
                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" required name="db_name" />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">نام دیتابیس</label>
                </div>
                <div class="form-element">
                    <input class="form-element-field" placeholder=" " type="text" required name="db_username" />
                    <div class="form-element-bar"></div>
                    <div class="form-element-bar"></div>
                    <label class="form-element-label">یوزر دیتابیس</label>
                    <div class="form-element">
                        <input class="form-element-field" placeholder=" " type="text" required name="db_password" />
                        <div class="form-element-bar"></div>
                        <label class="form-element-label">رمز دیتابیس</label>
                    </div>
                    <div class="form-element">
                        <div class="form-element">
                            <input class="form-element-field" placeholder=" " type="text" value="<?php echo $domin ?>" readonly />
                            <div class="form-element-bar"></div>
                            <label class="form-element-label">ادرس سورس</label>
                            <small class="form-element-hint">درصورت نیاز به تغییر در فایل domin.php تغییر دهید</small>
                        </div>
            </fieldset>
            <div class="form-element-field">درصورت نیاز به راهنمایی تیکت ارسال کنید</div>
            <div class="form-element-field">پس از نصب حتما فایل installer.php را پاک کنید.</div>
            <div class="form-actions">
                <input type="hidden" name="install" value="install">
                <button class="form-btn" type="submit">ثبت اطلاعات</button>
                <button class="form-btn-cancel -nooutline" type="reset">پاک کردن اطلاعات</button>
            </div>
        </form>
    </body>

    </html>
<?php
} else {
    echo 'ربات نصب شده است';
}
?>