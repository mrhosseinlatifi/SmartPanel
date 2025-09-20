<?php
// ---------- تنظیمات متغیرها ---------- //
if (file_exists('config.php')) {
    require_once 'config.php';
    require ROOTPATH . "/include/hkbot.php";
    $bot = new hkbot(Token);
    $getBotInfo = $bot->bot('getMe');
    $idbot = $getBotInfo['result']['username'];

    $site_name     = $getBotInfo['result']['first_name'];
    $bot_link      = "https://t.me/" . $idbot;
    $support_link  = "https://t.me/" . $idbot;
    $channel_link  = "https://t.me/" . $idbot;
} else {
    $site_name     = "";
    $bot_link      = "";
    $support_link  = "";
    $channel_link  = "";
}
$enamad_code   = '';

$year          = date("Y");
?>
<!doctype html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>خدمات افزایش فالوور و ممبر | <?php echo $site_name; ?></title>
    <meta name="description" content="خدمات افزایش فالوور، ممبر، لایک و بازدید با کیفیت، سریع و پشتیبانی 24 ساعته. ثبت سفارش آسان از طریق ربات." />
    <style>
        :root {
            --bg: #0f1226;
            --card: #151939;
            --muted: #a9b0d2;
            --text: #f6f7fb;
            --primary: #6d5dfc;
            --primary-2: #00d4ff;
            --radius-xl: 22px;
            --radius-2xl: 28px;
            --shadow-sm: 0 6px 20px rgba(0, 0, 0, .15);
            --shadow-lg: 0 20px 60px rgba(24, 30, 75, .45);
            --container: 1100px;
        }

        *,
        *::before,
        *::after {
            box-sizing: border-box
        }

        html,
        body {
            height: 100%
        }

        body {
            margin: 0;
            font-family: Vazirmatn, IRANSans, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial;
            background: radial-gradient(1200px 800px at 80% -10%, rgba(109, 93, 252, .25), transparent 60%),
                radial-gradient(900px 600px at -10% 20%, rgba(0, 212, 255, .20), transparent 60%),
                var(--bg);
            color: var(--text);
            line-height: 1.8;
        }

        a {
            color: inherit;
            text-decoration: none
        }

        img {
            max-width: 100%;
            display: block
        }

        .container {
            max-width: var(--container);
            margin: 0 auto;
            padding: 0 20px
        }

        header {
            position: sticky;
            top: 0;
            z-index: 50;
            background: rgba(15, 18, 38, .7);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, .06)
        }

        .nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            font-weight: 800
        }

        .brand .logo {
            width: 42px;
            height: 42px;
            border-radius: 12px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            box-shadow: var(--shadow-sm)
        }

        .nav-links {
            display: flex;
            gap: 20px;
            align-items: center
        }

        .nav-links a {
            opacity: .8
        }

        .nav-links a:hover {
            opacity: 1
        }

        .cta-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 18px;
            border-radius: 14px;
            background: linear-gradient(135deg, var(--primary), var(--primary-2));
            color: #fff;
            font-weight: 700;
            box-shadow: var(--shadow-sm);
            transition: transform .15s ease, box-shadow .15s ease;
        }

        .cta-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--shadow-lg)
        }

        .hero {
            padding: 64px 0 36px
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1.2fr .8fr;
            gap: 28px;
            align-items: center
        }

        .hero .title {
            font-size: clamp(26px, 4vw, 44px);
            line-height: 1.3;
            margin: 0 0 12px
        }

        .hero .subtitle {
            color: var(--muted);
            margin: 0 0 22px
        }

        .pills {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 18px 0 26px
        }

        .pill {
            padding: 8px 12px;
            border-radius: 999px;
            font-size: 13px;
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08)
        }

        .hero-card {
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .03));
            border: 1px solid rgba(255, 255, 255, .09);
            padding: 22px;
            border-radius: var(--radius-2xl);
            box-shadow: var(--shadow-lg)
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            margin-top: 20px
        }

        .stat {
            background: rgba(255, 255, 255, .06);
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 16px;
            border-radius: 16px;
            text-align: center
        }

        .stat .num {
            font-size: 22px;
            font-weight: 800
        }

        .stat .label {
            font-size: 12px;
            color: var(--muted)
        }

        .section {
            padding: 36px 0
        }

        .section h2 {
            font-size: 24px;
            margin: 0 0 18px
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px
        }

        .card {
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, .06);
            padding: 18px;
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-sm)
        }

        .card h3 {
            margin: 0 0 8px;
            font-size: 18px
        }

        .card p {
            margin: 0;
            color: var(--muted)
        }

        .steps {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 14px
        }

        .step {
            background: linear-gradient(180deg, rgba(255, 255, 255, .05), rgba(255, 255, 255, .03));
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 16px;
            border-radius: 16px
        }

        .step .badge {
            display: inline-block;
            background: rgba(109, 93, 252, .25);
            border: 1px solid rgba(109, 93, 252, .4);
            color: #fff;
            font-weight: 700;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            margin-bottom: 10px
        }

        .prices {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px
        }

        .price {
            background: var(--card);
            border: 1px solid rgba(255, 255, 255, .08);
            padding: 20px;
            border-radius: 18px
        }

        .price .tag {
            font-size: 12px;
            color: var(--muted)
        }

        .price .amount {
            font-size: 26px;
            font-weight: 800;
            margin: 8px 0 12px
        }

        .trust {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: center
        }

        .trust .badge {
            width: 120px;
            height: 120px;
            border-radius: 16px;
            background: #fff;
            display: grid;
            place-items: center;
            overflow: hidden;
            border: 2px dashed rgba(0, 0, 0, .15);
        }

        footer {
            padding: 22px 0 40px;
            color: var(--muted)
        }

        footer .footer-grid {
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center
        }

        @media (max-width: 980px) {
            .hero-grid {
                grid-template-columns: 1fr
            }

            .grid {
                grid-template-columns: repeat(2, 1fr)
            }

            .steps {
                grid-template-columns: repeat(2, 1fr)
            }

            .prices {
                grid-template-columns: 1fr
            }
        }

        @media (max-width: 560px) {
            .grid {
                grid-template-columns: 1fr
            }

            .stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .nav-links {
                display: none
            }

            .trust .badge {
                width: 100px;
                height: 100px
            }
        }
    </style>
</head>

<body>
    <!-- ناوبری -->
    <header>
        <div class="container nav">
            <div class="brand">
                <div class="logo" aria-hidden="true"></div>
                <span><?php echo $site_name; ?></span>
            </div>
            <nav class="nav-links">
                <a href="#services">خدمات</a>
                <a href="#how">نحوه سفارش</a>
                <a href="#trust">نماد اعتماد</a>
            </nav>
            <a class="cta-btn" href="<?php echo $bot_link; ?>" target="_blank" rel="noopener">ورود به ربات سفارش</a>
        </div>
    </header>

    <!-- هرو -->
    <section class="hero">
        <div class="container hero-grid">
            <div>
                <h1 class="title">خدمات افزایش فالوور و ممبر با کیفیت، سریع و امن</h1>
                <p class="subtitle">فروش فالوور واقعی و فعال، ممبر تلگرام، لایک و بازدید برای رشد هدفمند شبکه‌های اجتماعی شما. ثبت سفارش در چند ثانیه از طریق ربات.</p>
                <div class="pills">
                    <span class="pill">اینستاگرام: فالوور، لایک، بازدید</span>
                    <span class="pill">تلگرام: ممبر و بازدید</span>
                    <span class="pill">تیک‌تاک، یوتیوب و بیشتر…</span>
                </div>
                <a class="cta-btn" href="<?php echo $bot_link; ?>" target="_blank" rel="noopener">شروع سفارش از طریق ربات</a>
                <div class="stats">
                    <div class="stat">
                        <div class="num">+120k</div>
                        <div class="label">سفارش تکمیل‌شده</div>
                    </div>
                    <div class="stat">
                        <div class="num">%99</div>
                        <div class="label">رضایت مشتریان</div>
                    </div>
                    <div class="stat">
                        <div class="num">24/7</div>
                        <div class="label">پشتیبانی آنلاین</div>
                    </div>
                </div>
            </div>
            <div class="hero-card">
                <h3 style="margin-top:0">ثبت سفارش خودکار</h3>
                <p style="color:var(--muted);margin:6px 0 16px">پس از پرداخت، سفارش شما به صورت خودکار در صف پردازش قرار می‌گیرد و نتایج را به سرعت مشاهده می‌کنید.</p>
                <ul style="margin:0; padding:0 18px">
                    <li>پیگیری وضعیت سفارش در ربات</li>
                    <li>لغو/تعویض سرویس قبل از شروع</li>
                    <li>گارانتی تکمیل تا سقف مشخص</li>
                </ul>
            </div>
        </div>
    </section>

    <!-- خدمات -->
    <section id="services" class="section">
        <div class="container">
            <h2>خدمات ما</h2>
            <div class="grid">
                <div class="card">
                    <h3>فالوور واقعی اینستاگرام</h3>
                    <p>افزایش فالوور با کیفیت، ماندگاری بالا و شروع سریع.</p>
                </div>
                <div class="card">
                    <h3>ممبر تلگرام</h3>
                    <p>افزایش ممبر کانال/گروه با تحویل سریع و قابلیت هدف‌گیری منطقه‌ای.</p>
                </div>
                <div class="card">
                    <h3>لایک و بازدید</h3>
                    <p>بالا بردن درگیری مخاطب با لایک و بازدید واقعی برای پست و استوری.</p>
                </div>
                <div class="card">
                    <h3>یوتیوب و تیک‌تاک</h3>
                    <p>سابسکرایب، ویو و واچ‌تایم برای رشد کانال و ویدیوها.</p>
                </div>
                <div class="card">
                    <h3>مدیریت کمپین</h3>
                    <p>طراحی پلن رشد، آنالیز نتایج و بهینه‌سازی کمپین‌ها.</p>
                </div>
                <div class="card">
                    <h3>API همکاری</h3>
                    <p>امکان اتصال همکاران از طریق API برای ثبت خودکار سفارش‌ها.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- مراحل کار -->
    <section id="how" class="section">
        <div class="container">
            <h2>نحوه سفارش</h2>
            <div class="steps">
                <div class="step"><span class="badge">۱</span>
                    <h3>ورود به ربات</h3>
                    <p style="color:var(--muted)">روی دکمه «ورود به ربات سفارش» کلیک کنید.</p>
                </div>
                <div class="step"><span class="badge">۲</span>
                    <h3>انتخاب سرویس</h3>
                    <p style="color:var(--muted)">سرویس موردنظر را انتخاب کنید.</p>
                </div>
                <div class="step"><span class="badge">۳</span>
                    <h3>وارد کردن لینک</h3>
                    <p style="color:var(--muted)">لینک پست/پیج/کانال خود را وارد کنید.</p>
                </div>
                <div class="step"><span class="badge">۴</span>
                    <h3>پرداخت امن</h3>
                    <p style="color:var(--muted)">پرداخت از طریق درگاه امن و شروع خودکار سفارش.</p>
                </div>
            </div>
        </div>
    </section>


    <!-- نماد اعتماد -->
    <section id="trust" class="section">
        <div class="container">
            <h2>نماد اعتماد</h2>
            <div class="trust">
                <?php echo $enamad_code; ?>
            </div>
        </div>
    </section>

    <footer>
        <div class="container footer-grid">
            <div>© <?php echo $year; ?> <?php echo $site_name; ?> — تمامی حقوق محفوظ است.</div>
            <div>
                <span style="opacity:.35">|</span>
                <a href="<?php echo $support_link; ?>" target="_blank" rel="noopener">پشتیبانی تلگرام</a>
            </div>
        </div>
    </footer>
</body>

</html>