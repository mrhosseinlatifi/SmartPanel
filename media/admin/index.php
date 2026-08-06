<?php

trait main_admin_text
{
    public function atext($tx, $data = null)
    {
        global $idbot;
        switch ($tx) {
            case 'text':
                $t = $data;
                break;
            case 'start_panel':
                $t = "👋 به مدیریت خوش آمدید! اینجا می‌توانید تنظیمات و آمار ربات خود را مدیریت کنید.";
                break;
            case 'stats':
                $number_user = $data[0];
                $number_order = $data[1];
                $users_balance = $data[2];
                $usd_rate = $data[3];
                $date = jdate('Y/m/d - H:i:s');
                $t = "📊 آمار ربات:
👥 تعداد کاربران: $number_user
💰 موجودی کاربران: $users_balance
📝 تعداد سفارشات ثبت شده: $number_order
💲 نرخ دلار: $usd_rate تومان
📅 تاریخ مشاهده: $date
برای دیدن آمار کامل، روی دکمه 'آمار کلی 📊' کلیک کنید.
برای دیدن دسترسی‌های خود، دستور /access را وارد کنید.";
                break;
            case 'more_stats':
                $number_user = $data[0];
                $today_member = $data[1];
                $yesterdate_member = $data[2];
                $number_user_block = $data[3];
                $number_order = $data[4];
                $order_cheack_api = $data[5];
                $order_cheack_no_api = $data[6];
                $number_payment = $data[7];
                $number_payment_creted = $data[8];
                $users_balance = $data[9];
                $users_gift_balance = $data[10];
                $amount_paid = $data[11];
                $usd_rate = $data[12];
                $usd_mode_label = $data[13] === 'auto' ? '🤖 خودکار' : '✍️ دستی';
                $version = $data[14];
                $date = jdate('Y/m/d - H:i:s');
                $ts = "\n📅 تاریخ مشاهده: $date";
                $t = "📊 آمار کامل ربات:
👥 تعداد کاربران: $number_user
📈 کاربران عضو شده امروز: $today_member
📉 کاربران عضو شده دیروز: $yesterdate_member
🚫 تعداد کاربران بلاک: $number_user_block
💰 موجودی کاربران: $users_balance
🎁 موجودی هدیه دریافتی استفاده نشده: $users_gift_balance
📝 تعداد سفارشات ثبت شده: $number_order
🔍 سفارشات درحال بررسی وب سرویس: $order_cheack_api
🔍 سفارشات درحال بررسی دستی: $order_cheack_no_api
💳 تعداد فاکتور ساخته شده: $number_payment_creted
💳 تعداد فاکتور پرداخت شده: $number_payment
💵 مبلغ کلی پرداخت شده: $amount_paid
💲 نرخ دلار: {$usd_rate} تومان ({$usd_mode_label})
🔄 ورژن ربات: {$version}\n" . $ts;
                break;
            case 'cron_status':
                $cron = $data[0] ? jdate('Y/m/d , H:i', $data[0]) : 'هنوز اجرا نشده';
                $cron_done = $data[1] ? jdate('Y/m/d , H:i', $data[1]) : 'هنوز اجرا نشده';
                $cron_auto = $data[2] ? jdate('Y/m/d , H:i', $data[2]) : 'هنوز اجرا نشده';
                $t = "⏰ زمان اجرای آخرین کرون‌جاب‌ها:

📤 کرون همگانی (send.php): $cron
📦 کرون سفارشات (orders.php): $cron_done
⚙️ کرون خودکار — نرخ دلار و... (cron.php): $cron_auto

برای دریافت لینک کرون‌ها: /cron_link";
                break;
            case 'status':
                $s = date('s');
                $t = "🔍 انتخاب کنید:
گزینه‌هایی که دارای (*) هستند، دارای گزینه‌های زیرمجموعه هستند. برای مشاهده، روی اسم آنها کلیک کنید. $s";
                break;
            case 'sendall_1':
                $t = "📤 انتخاب کنید:";
                if ($data) {
                    $t .= "\n\n⏳ یک ارسال همگانی در صف/در حال اجراست.\nبرای مشاهده وضعیت، لغو یا توقف آن، روی دکمه «📊 وضعیت ارسال همگانی» بزنید.";
                }
                break;
            case 'sendall_2':
                $t = "✉️ پیام خود را وارد کنید:";
                break;
            case 'sendall_3':
                $t = "🔄 پیام خود را فروارد کنید:";
                break;
            case 'sendall_4':
                $sall = $data[0];
                $fall = $data[1];
                $t =  "📤 تعداد پیام همگانی: {$sall}
🔄 تعداد فروارد همگانی: {$fall}
لطفاً انتخاب کنید:";
                break;
            case 'sendall_5':
                $t = "✅ پیام به صف ارسال اضافه شد.";
                break;
            case 'sendall_6':
                $t = "⏳ یک پیام در صف ارسال است. لطفاً دقایقی دیگر امتحان کنید.
برای مشاهده وضعیت، لغو یا توقف آن، روی دکمه «📊 وضعیت ارسال همگانی» بزنید.";
                break;
            case 'sendall_album_preview':
                $count = $data[0] ?? 0;
                $caption = $data[1] ?? '';
                $cap_line = $caption ? "\n📝 کپشن: $caption" : '';
                $t = "📸 آلبوم آماده ارسال:
🖼 تعداد عکس: {$count}{$cap_line}
عکس‌های بیشتر ارسال کنید یا دکمه تأیید را بزنید:";
                break;
            case 'sendall_7':
                $t = "🔢 عدد فعلی: {$data}
برای تغییر، عدد جدید را ارسال کنید.
محدودیت تلگرام برای ارسال پیام و غیره 30 درخواست در هر ثانیه است.";
                break;
            case 'sendall_8':
                $t = "🔢 عدد فعلی: {$data}
برای تغییر، عدد جدید را ارسال کنید.
محدودیت تلگرام برای فوروارد 2000 فوروارد در هر ساعت است.";
                break;
            case 'sendall_9':
                $t = "✅ {$data} ثبت شد.";
                break;
            case 'error_sendall':
                $t = "لطفا پیام مورد نظر خود را ارسال کنید";
                break;
            case 'sendall_status':
                $type_label = $data[0];
                $sent = $data[1];
                $total = $data[2];
                $remaining = $data[3];
                $status_label = $data[4];
                $last = $data[5] ? jdate('Y/m/d H:i', $data[5]) : '-';
                $percent = $total > 0 ? round(($sent / $total) * 100) : 0;
                $t = "📊 وضعیت ارسال همگانی

📨 نوع پیام: {$type_label}
📶 وضعیت: {$status_label}
✅ ارسال شده: {$sent} از {$total} نفر ({$percent}٪)
⏳ باقی‌مانده: {$remaining} نفر
🕐 آخرین فعالیت: {$last}";
                break;
            case 'sendall_status_empty':
                $t = "در حال حاضر هیچ ارسال همگانی در صف یا در حال اجرا نیست.";
                break;
            case 'sendall_preview_sent':
                $t = "✅ نمونه پیام برای شما ارسال شد.";
                break;
            case 'sendall_paused':
                $t = "⏸ ارسال متوقف شد. با دکمه «ادامه ارسال» می‌توانید آن را از سرجای متوقف‌شده ادامه دهید.";
                break;
            case 'sendall_resumed':
                $t = "▶️ ارسال ادامه یافت.";
                break;
            case 'sendall_cancelled':
                $t = "❌ ارسال همگانی به طور کامل لغو شد.";
                break;
            case 'sendall_album_added':
                $count = $data;
                $t = "✅ آلبوم {$count} عکسه به صف همگانی اضافه شد.";
                break;
            case 'sendall_photo_added':
                $t = "✅ عکس به صف همگانی اضافه شد.";
                break;
            case 'sendall_album_cancelled':
                $t = "❌ ارسال آلبوم لغو شد.";
                break;
            case 'userinfo_1':
                $t = "🔍 لطفاً آیدی شخص مورد نظر یا شماره همراه آن را وارد کنید:";
                break;
            case 'userinfo_2':
                $userinfo = $data[0];
                $id = $userinfo['user_id'];
                $name = $data[1];
                $full_name = $name['user']['first_name'];
                $name = type_text($full_name, 'm', $id);
                if ($userinfo['block']) {
                    $banres  = '🚫 مسدود است';
                } else {
                    $banres  = '✅ مسدود نیست';
                }
                if ($userinfo['referral_id']) {
                    $ref  = '👥 معرف: ' . $userinfo['referral_id'];
                } else {
                    $ref  = '❌ معرف ندارد';
                }
                if ($userinfo['payment_card'] > 0) {
                    $card  = $userinfo['payment_card'];
                } else {
                    $card  = '❌ کارتی ثبت نشده است';
                }
                if (text_contains($userinfo['number'], 'off')) {
                    $number = str_replace('off', '❌ شماره تایید نشده است', $userinfo['number']);
                } else {
                    if (!$userinfo['number']) {
                        $number = "❌ ثبت نشده است";
                    } else {
                        $number = $userinfo['number'];
                    }
                }
                $userinfo['last_msg'] = json_decode($userinfo['last_msg'], 1)['last_msg'];
                $lastmsg = jdate('Y/m/d - H:i:s', $userinfo['last_msg']);
                $date = jdate('Y/m/d - H:i', $userinfo['join_date']);
                if (isset($data[2]) && $data[2]) {
                    $now = "\n" . jdate('Y/m/d - H:i:s');
                } else {
                    $now = null;
                }
                $t = "👤 اطلاعات کاربر:
نام: $name
شناسه: <code>$id</code>
💰 موجودی: {$userinfo['balance']}
🎁 تخفیف شخصی: {$userinfo['discount']}
📝 تعداد سفارشات: {$userinfo["number_order"]}
💵 مبلغ پرداختی: {$userinfo["amount_paid"]}
💸 موجودی خرج شده: {$userinfo["amount_spent"]}
📞 شماره: {$number}
💳 کارت: $card
💰 درآمد: {$userinfo["gift"]}
👥 معرف: $ref
👥 تعداد زیرمجموعه: {$userinfo["referral"]}
🎁 هدیه زیرمجموعه‌گیری: {$userinfo["gift_referral"]}
💸 پورسانت خرید زیرمجموعه: {$userinfo['gift_payment']}
📅 تاریخ عضویت: $date
🚫 وضعیت بن: $banres
🕒 آخرین آنلاین: $lastmsg
@$idbot$now";
                break;
            case 'userinfo_3':
                $t = "🔍 انتخاب کنید:";
                break;
            case 'userinfo_4':
                $t = "❌ این کاربر عضو ربات نمی‌باشد.";
                break;
            case 'userinfo_5':
                $t = "✉️ پیام خود را ارسال کنید:";
                break;
            case 'userinfo_6':
                $t = "🔢 مقدار تخفیف مخصوص را وارد کنید. عدد صفر بدون تخفیف است.";
                break;
            case 'userinfo_7':
                $t = "💰 مقدار تومان برای افزایش را ارسال کنید:";
                break;
            case 'userinfo_8':
                $t = "💸 مقدار تومان برای کاهش را ارسال کنید:";
                break;
            case 'userinfo_9':
                $t = "💳 شماره کارت را به صورت عددی 16 الی 19 رقمی ارسال کنید:";
                break;
            case 'userinfo_10':
                $t = "📞 لطفاً شماره کاربر را وارد کنید. در صورت وارد کردن 0، مجدد احراز هویت انجام می‌گیرد.";
                break;
            case 'userinfo_11':
                $t = "👥 آیدی عددی معرف کاربر را وارد کنید. در صورت وارد کردن 0، کاربر بدون معرف محاسبه می‌شود.";
                break;
            case 'userinfo_12':
                $t = "🚫 مسدود شد.";
                break;
            case 'userinfo_13':
                $t = "✅ مسدودیت برداشته شد.";
                break;
            case 'userinfo_14':
                $t = "✅ پیام با موفقیت ارسال شد.";
                break;
            case 'userinfo_15':
                $t = "✅ $data% برای کاربر ثبت شد.";
                break;
            case 'userinfo_16':
                $t = "🔢 لطفاً یک عدد بالای صفر و کمتر از 100 ارسال کنید.";
                break;
            case 'userinfo_payments':
                $result = $data[0];
                $id = $data[1];
                $nex = $data[2];
                $page = $data[3];
                $c = $data[4];
                if ($result) {
                    $npage = $nex / $page;
                    $ppage = ceil($c / $page);
                    $tx = "💳 پرداخت‌های اخیر کاربر: $id
صفحه $npage/$ppage
";
                    foreach ($result as $row) {
                        $date = jdate('Y/m/d - H:i:s', $row['date']);
                        $decode_data = json_decode($row['data'], true);
                        $row['ip'] = isset($decode_data['ip']) ? $decode_data['ip'] : 0;
                        if ($row['status'] == 1) {
                            $tx .= "✅ موفق | مبلغ: {$row['amount']}
🔍 شماره پیگیری درگاه: {$row['tracking_code']} | شماره پیگیری ربات: {$row['id']}
🌐 درگاه: {$row['getway']}
🌐 IP: {$row['ip']}
📅 {$date}
-------
";
                        } else {
                            if ($row['type'] == 'payment') {
                                $stp = str_replace(['0', '2', '3'], ['❌ ناموفق', '⏳ منتظر پرداخت', '⏳ انتظار برگشت از درگاه'], $row['status']);
                                $tx .= "$stp | مبلغ: {$row['amount']}
🌐 درگاه: {$row['getway']}
🔍 شماره پیگیری ربات: {$row['id']}
🌐 IP: {$row['ip']}
📅 {$date}
-------
";
                            } elseif ($row['type'] == 'payment_offline') {
                                $stp = str_replace(['0', '1', '2'], ['❌ رد شده', '✅ تایید شده', '⏳ منتظر بررسی'], $row['status']);
                                $tx .= "💳 پرداخت آفلاین | $stp | مبلغ: {$row['amount']}
🔍 شماره پیگیری ربات: {$row['id']}
📅 {$date}
-------
";
                            }
                        }
                    }
                } else {
                    $tx = "❌ پرداختی وجود ندارد.";
                }
                $t = $tx;
                break;
            case 'userinfo_orders':
                $result = $data[0];
                $id = $data[1];
                $nex = $data[2];
                $page = $data[3];
                $c = $data[4];
                if ($result) {
                    $npage = $nex / $page;
                    $ppage = ceil($c / $page);
                    $tx = "🛒 سفارش‌های اخیر کاربر: $id
صفحه $npage/$ppage
";
                    foreach ($result as $row) {
                        $d = json_decode($row['product'], 1);
                        $cat = implode("\n", $d['category']) . "\n" . $d['product'];
                        $date = jdate('Y/m/d - H:i', $row['date']);
                        if ($row['api'] == 'noapi') {
                            $tx .= "🛍 سفارش:
{$cat}
#---------------------#
📋 وضعیت: {$row['status']}
🔍 کد پیگیری: {$row['id']}
🔗 اطلاعات: {$row['link']} | تعداد: {$row['count']}
📅 {$date}
-------
";
                        } else {
                            $tx .= "🛍 سفارش:
{$cat}
#---------------------#
📋 وضعیت: {$row['status']}
🔍 کد پیگیری: {$row['id']}
🔗 اطلاعات: {$row['link']} | تعداد: {$row['count']}
📅 {$date}
-------
";
                        }
                    }
                } else {
                    $tx = "❌ کاربر سفارشی ثبت نکرده است.";
                }
                $t = $tx;
                break;
            case 'userinfo_trs':
                $result = $data[0];
                $id = $data[1];
                $nex = $data[2];
                $page = $data[3];
                $c = $data[4];
                if ($result) {
                    $npage = $nex / $page;
                    $ppage = ceil($c / $page);
                    $tx = "🔄 تراکنش‌های اخیر کاربر: $id
صفحه $npage/$ppage
";
                    foreach ($result as $row) {
                        $d = json_decode($row['data'], 1);
                        $date = jdate('Y/m/d - H:i:s', $row['date']);
                        $tx .= "🔄 قبل: {$d['old']} | جدید: {$d['new']}
";
                        switch ($row['type']) {
                            case 'gift':
                                $gift_type_names = [
                                    'StartGift' => 'هدیه شروع',
                                    'GiftJoin' => 'هدیه معرفی',
                                    'GiftPayment' => 'هدیه پرداخت'
                                ];
                                $gift_type_display = $gift_type_names[$d['type']] ?? $d['type'];
                                $tx .= "🎁 هدیه | مبلغ: {$row['amount']} | نوع: {$gift_type_display}
📅 {$date}
-------
";
                                break;
                            case 'gift_move':
                                $tx .= "🔄 انتقال شارژ رایگان به کیف پول: {$row['amount']}
📅 {$date}
-------
";
                                break;
                            case 'gift_payout':
                                $st = str_replace([0, 1, 2], ['❌ رد شده', '✅ تایید شده', '⏳ منتظر'], $row['status']);
                                $tx .= "💸 درخواست برداشت شارژ رایگان: {$row['amount']} | وضعیت: $st
📅 {$date}
-------
";
                                break;
                            case 'managment':
                                $admin_action = $d['type'] == 'increase' ? 'افزایش' : 'کاهش';
                                $admin_id = isset($d['admin']) ? $d['admin'] : 'نامشخص';
                                $tx .= "🔧 مدیریت موجودی | نوع: {$admin_action} | مبلغ: {$row['amount']} | ادمین: {$admin_id}
📅 {$date}
-------
";
                                break;
                            case 'send_balance':
                                $receiver_id = $d['type'];
                                $tx .= "💸 ارسال موجودی | مبلغ: {$row['amount']} | به کاربر: {$receiver_id}
📅 {$date}
-------
";
                                break;
                            case 'receive_balance':
                                $sender_id = $d['type'];
                                $tx .= "💵 دریافت موجودی | مبلغ: {$row['amount']} | از کاربر: {$sender_id}
📅 {$date}
-------
";
                                break;
                            case 'orders':
                                $tx .= "🛒 خرید محصول | مبلغ: {$row['amount']} | کسر از موجودی
📅 {$date}
-------
";
                                break;
                            case 'orders_back':
                                $tx .= "🔄 بازگشت موجودی سفارش | مبلغ: {$row['amount']} | شماره سفارش: {$d['type']}
📅 {$date}
-------
";
                                break;
                            case 'gift_code':
                                $tx .= "🎫 استفاده از کد هدیه | مبلغ: {$row['amount']}
📅 {$date}
-------
";
                                break;
                            default:
                                $type_name = $row['type'];
                                $tx .= "❓ نوع تراکنش: {$type_name} | مبلغ: {$row['amount']}
📅 {$date}
-------
";
                                break;
                        }
                    }
                } else {
                    $tx = "❌ کاربر تراکنشی ندارد.";
                }
                $t = $tx;
                break;
            case 'userinfo_20':
                $t = "✅ ثبت شد.";
                break;
            case 'userinfo_21':
                $t = "💰 مقدار وارد شده: $data تومان
در صورت صحیح بودن، تایید کنید؟";
                break;
            case 'userinfo_22':
                $t = "📞 شماره وارد شده: $data
در صورت صحیح بودن، تایید کنید؟";
                break;
            case 'userinfo_23':
                $id = $data[0];
                $q = $data[1];
                $t = "💸 {$q} تومان از کاربر {$id} کم کردم.";
                break;
            case 'userinfo_24':
                $id = $data[0];
                $q = $data[1];
                $t = "💰 {$q} تومان به کاربر {$id} اضافه کردم.";
                break;
            case 'channels_panel':
                $t = "📋 <b>تنظیمات کانال‌ها</b>";
                $t .= "\nبرای ویرایش یا حذف از دکمه‌های زیر استفاده کنید.";
                $t .= "\n علامت ✅ به معنی تنظیم کانال و علامت ❌ به معنی تنظیم نبودن کانال می‌باشد.";
                break;

            case 'ch_lock_panel':
                $la = $data ?: [];
                if (empty($la)) {
                    $t = "🔒 <b>کانال‌های جوین اجباری</b>\n\nهیچ کانالی تنظیم نشده.\nبرای افزودن، دکمه ➕ را بزنید.";
                } else {
                    $t = "🔒 <b>کانال‌های جوین اجباری</b>\n\n";
                    foreach ($la as $i => $u) {
                        $t .= ($i + 1) . ". @{$u}\n";
                    }
                    $t .= "\nبرای حذف هر کانال روی ❌ بزنید.";
                }
                break;

            case 'ch_set_prompt':
                $ck = $data;
                $labels = [
                    'channel_main'            => '📢 کانال اصلی',
                    'channel_transaction'     => '💰 تراکنشات',
                    'channel_payment_offline' => '💳 کارت به کارت',
                    'channel_ads'             => '📢 تبلیغاتی',
                    'channel_order_api'       => '🛍 سفارشات API',
                    'channel_order_noapi'     => '🛍 سفارشات دستی',
                    'channel_support'         => '💬 پشتیبانی',
                    'channel_kyc'             => '🔐 احراز هویت',
                    'channel_gift_transaction'=> '💸 برداشت هدیه',
                    'channel_errors'          => '❌ خطاها',
                ];
                $cl = $labels[$ck] ?? $ck;
                $cv = get_option($ck, 0);
                $cv_str = ($cv && $cv != '0') ? "\nمقدار فعلی: <code>{$cv}</code>\n" : '';
                if ($ck === 'channel_main') {
                    $t = "✏️ <b>ویرایش {$cl}</b>{$cv_str}\n@یوزرنیم کانال را ارسال کنید یا یک پست از کانال فوروارد کنید.";
                } else {
                    $t = "✏️ <b>ویرایش {$cl}</b>{$cv_str}\nیک پست از کانال مورد نظر فوروارد کنید.\n(ربات باید ادمین کانال باشد)\n\nیا آیدی عددی کانال را مستقیم ارسال کنید.";
                }
                break;

            case 'ch_lock_add_prompt':
                $t = "➕ <b>افزودن کانال جوین اجباری</b>\n\nیک پست از کانال مورد نظر فوروارد کنید.\n(ربات باید ادمین کانال باشد)";
                break;

            case 'ch_saved':
                $t = "✅ ذخیره شد.";
                break;

            case 'ch_deleted':
                $t = "🗑 حذف شد.";
                break;
            case 'products_1':
                $t = "🛍 انتخاب کنید:";
                break;
            case 'payments_1':
                $t = "💳 انتخاب کنید:";
                break;
            case 'referral_1':
                $t = "👥 انتخاب کنید:";
                break;
            case 'settings_1':
                $t = "⚙️ انتخاب کنید:";
                break;
            case 'text_1':
                $t = "📝 انتخاب کنید:";
                break;
            case 'apis_1':
                $t = "🔗 انتخاب کنید:";
                break;
            case 'sms_panel_1':
                $t = "📱 انتخاب کنید:

این ربات با سایت farazsms همخوانی دارد.
در صورتی که نیاز به اتصال به پنل دیگری دارید، با پشتیبانی ارتباط برقرار کنید.";
                break;
            case 'list_admin':
                $result = $data['0'];
                $is_admin = $data['1'];
                $t = "🏷 لیست ادمین‌ها 👇
";
                foreach ($result as $id => $name) {
                    if ($is_admin) {
                        $tt = ($id == admins[0]) ?  null : " | /ac_$id";
                        $t .= "<code>$id</code> | <a href ='tg://user?id=$id'>$name</a>$tt\n";
                    } else {
                        $t .= "<a href ='tg://user?id=$id'>$name</a>\n";
                    }
                }
                $t .= "\n------------------\n";
                break;
            case 'chtiket_1':
                $t = "🔢 تعداد فعلی: {$data}
برای تغییر، عدد مورد نظر خود را ارسال کنید.
هر تعداد عددی که بزنید، هر کاربر مجاز به ارسال همان تعداد پشت هم پیام است و در صورت رسیدن به حد، دیگر امکان ارسال پیام نمی‌باشد.";
                break;
            case 'chnumber_1':
                $t = "🔢 تعداد فعلی: {$data}
برای تغییر، عدد مورد نظر خود را ارسال کنید:";
                break;
            case 'ok_settings_edit':
                $t = "✅ $data ثبت شد.";
                break;
            case 'send_int':
                $t = "✉️ متن خود را ارسال کنید:";
                break;
            case 'chspam_1':
                $t = "🚫 انتخاب کنید:";
                break;
            case 'admin_1':
                $t = "🔍 آیدی عددی شخص مورد نظر را بفرستید:";
                break;
            case 'ptid_ref':
                $t = "🔄 پترن فعلی:
<code>{$data}</code>
برای تغییر، کد پترن جدید را ارسال کنید.

در متن پترن حتماً کلمه %code% استفاده نمایید.";
                break;
            case 'ptid_payment':
                $t = "🔄 پترن فعلی:
<code>{$data}</code>
برای تغییر، کد پترن جدید را ارسال کنید.

در متن پترن حتماً کلمه %code% استفاده نمایید.";
                break;
            case 'user_sms':
                $t = "📱 نام کاربری فعلی:
<code>{$data}</code>
برای تغییر، نام کاربری جدید را ارسال کنید:";
                break;
            case 'pas_sms':
                $t = "🔑 رمز فعلی:
<code>{$data}</code>
برای تغییر، رمز جدید را ارسال کنید:";
                break;
            case 'from_number':
                $t = "📞 شماره فعلی:
<code>{$data}</code>
برای تغییر، شماره را ارسال کنید:";
                break;
            case 's_spam':
                $t = "⏰ عدد فعلی: {$data} ثانیه
برای تغییر، عدد جدید را ارسال کنید:";
                break;
            case 'add_api_1':
                $t = "🔗 لطفاً نام مورد نظر خود را برای وب سرویس به انگلیسی وارد کنید.
برای مثال: Name Company";
                break;
            case 'add_api_2':
                $t = "❓ آیا URL مورد نظر از وب سرویس v1/v2 پشتیبانی می‌کند؟";
                break;
            case 'add_api_3':
                $t = "🌐 آدرس وب سرویس مورد نظر را ارسال کنید. برای مثال:
https://site.com/api/v1";
                break;
            case 'add_api_4':
                $t = "🔑 رمز وب سرویس را وارد کنید:";
                break;
            case 'ok_add_api':
                $t = "✅ وب سرویس با موفقیت اضافه شد.";
                break;
            case 'add_api_error_1':
                $t = "❌ لطفاً نام کوتاه‌تری وارد کنید.";
                break;
            case 'add_api_error_2':
                $t = "❌ لطفاً نام دیگری وارد کنید.";
                break;
            case 'add_api_error_3':
                $t = "❌ یک وب سرویس به این اسم وجود دارد.";
                break;
            case 'add_api_error_4':
                $t = "❌ لطفاً متن انگلیسی وارد کنید.";
                break;
            case 'edit_api_error_1':
                $t = "❌ آدرس ارسالی اشتباه است.";
                break;
            case 'edit_api_error_2':
                $t = "❌ آدرس ارسالی با آدرس قبلی یکسان است.";
                break;
            case 'edit_api_error_3':
                $t = "❌ رمز ارسالی اشتباه می‌باشد.";
                break;
            case 'edit_api_error_4':
                $t = "❌ رمز ارسالی با رمز قبلی یکسان است.";
                break;
            case 'error_add_api':
                $t = "❌ وب سرویس نامعتبر است.";
                break;
            case 'balance_api':
                $date = jdate('Y/m/d - H:i:s');
                $t = "💰 موجودی وب سرویس:
📅 $date
جداکننده ',' برای خواندن راحت‌تر عدد و '.' جدا کننده اعشار می‌باشد.";
                break;
            case 'status_api':
                $t = "🔄 انتخاب کنید:
با انتخاب دکمه‌های ردیف، وضعیت وب سرویس خاموش و یا روشن می‌شود.
با انتخاب دکمه‌های ردیف چندگانه، امکان بررسی سفارشات چندگانه را می‌دهید (فقط وب سرویس‌های اسمارت پشتیبانی می‌کند).
روشن: ✅ | خاموش: ❌";
                break;
            case 'not_found_api':
                $t = "❌ هیچ وب سرویسی ثبت نشده است.";
                break;
            case 'not_found_order':
                $t = "❌ سفارش با شناسه {$data} یافت نشد.";
                break;
            case 'error_text_admin_home':
                $t = "❌ دستور شما ناشناخته است، لطفا از منوی ربات استفاده کنید.";
                break;
            case 'edit_api':
                $t = "🔄 وب سرویس مورد نظر خود را برای ویرایش انتخاب کنید:";
                break;
            case 'edit_api_info':
                $info = $data['0'];
                $date = ($data['1']) ? jdate('Y/m/d H:i:s') : null;
                $smart = ($info['smart_panel']) ? 'اسمارت پنل' : 'متفرقه';
                $info['api_key'] = type_text($info['api_key'], 's');
                $t = "ℹ️ اطلاعات فعلی:
اسم: {$info['name']}
آدرس: {$info['api_url']}
رمز دسترسی: {$info['api_key']}
نوع وب سرویس: {$smart}
بخش مورد نظر را انتخاب کنید
$date";
                break;
            case 'edit_api_1':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'new_name_api_name':
                $t = "🔤 نام جدید خود را وارد کنید:";
                break;
            case 'new_url_api_name':
                $t = "🌐 آدرس جدید خود را وارد کنید:";
                break;
            case 'new_key_api_name':
                $t = "🔑 رمز جدید خود را وارد کنید:";
                break;
            case 'delete_api_1':
                $t = "❌ برای حذف، روی دکمه تایید کلیک کنید.
توجه کنید که این عملیات غیرقابل بازگشت است.";
                break;
            case 'edit_api_ok':
                $t = "✅ با موفقیت تغییر کرد.";
                break;
            case 'delete_api_2':
                $t = "✅ وب سرویس با موفقیت حذف شد.
وضعیت محصولات مرتبط با این وب سرویس را مشخص کنید.";
                break;
            case 'delete_api_ok':
                $t = "✅ با موفقیت حذف شد.";
                break;
            case 'delete_api_3':
                $t = "❌ هنوز سفارشاتی در صف بررسی وضعیت هستند.
لطفاً وضعیت این سفارشات را تعیین کنید.
پیشنهاد این است صبر کنید تا وضعیت این سفارشات تعیین شود.";
                break;
            case 'delete_api_4':
                $t = "✅ انجام شد.
وضعیت محصولات مرتبط با این وب سرویس را مشخص کنید.";
                break;
            case 'delete_api_ok_product':
                $t = "✅ انجام شد.";
                break;
            case 'edit_api_setting_1':
                $t = "🔄 انتخاب کنید:
مقادیر فعلی:
تعداد سفارش بررسی: {$data['0']}
تعداد سفارش بررسی چندگانه: {$data['1']}
مدت زمان نامشخص شدن سفارش: {$data['2']} روز";
                break;
            case 'edit_api_setting_2':
                $t = "🔢 مقدار جدید را برای $data وارد کنید:";
                break;
            case 'edit_api_setting_ok':
                $t = "✅ انجام شد.
مقادیر فعلی:
تعداد سفارش بررسی: {$data['0']}
تعداد سفارش بررسی چندگانه: {$data['1']}
مدت زمان نامشخص شدن سفارش: {$data['2']} روز";
                break;
            case 'payment_status':
                $t = "💳 درگاه مورد نظر خود را انتخاب کنید:";
                break;
            case 'add_payment_1':
                $t = "💳 لطفاً نام فارسی درگاه مورد نظر خود را ارسال کنید:";
                break;
            case 'add_payment_2':
                $t = "📂 لطفاً ابتدا فایل را در داخل پوشه آپلود کنید.
لطفاً فایل درگاه را از بین دکمه‌های زیر انتخاب کنید:";
                break;
            case 'add_payment_3':
                $t = "🔑 لطفاً کد درگاه (مرچنت) مورد نظر خود را ارسال کنید:";
                break;
            case 'add_payment_ok':
                $t = "✅ با موفقیت اضافه شد.";
                break;
            case 'error_add_payment_1':
                $t = "❌ لطفاً نام کوتاه‌تری وارد کنید.";
                break;
            case 'error_add_payment_2':
                $t = "❌ درگاهی با اسم $data ثبت شده است.";
                break;
            case 'error_add_payment_3':
                $t = "❌ درگاهی با فایل $data ثبت شده است.";
                break;
            case 'error_add_payment_4':
                $t = "❌ با عرض پوزش مشکلی پیش آمده است. مجدداً تلاش کنید.";
                break;
            case 'error_edit_payment_1':
                $t = "❌ هیچ درگاهی با اسم $data ثبت نشده است.";
                break;
            case 'error_edit_payment_2':
                $t = "❌ لطفاً نام کوتاه‌تری وارد کنید.";
                break;
            case 'error_edit_payment_3':
                $t = "❌ یک درگاه به این اسم وجود دارد.";
                break;
            case 'error_edit_payment_4':
                $t = "❌ لطفاً یک عدد معتبر (بزرگتر یا مساوی صفر) وارد کنید.";
                break;
            case 'not_user':
                $t = "❌ کاربر ارسالی در ربات عضو نمی‌باشد.";
                break;
            case 'access_admin':
                $t = "🔐 دسترسی‌های مدیر را تعیین کنید:
در صورتی که دسترسی برای مدیر فعال باشد، تمامی قابلیت‌های آن بخش برای مدیر فعال می‌شود.
دسترسی این بخش فقط برای مدیرانی که آیدی آنها داخل فایل config.php موجود می‌باشد.";
                break;
            case 'not_access':
                $t = "❌ دسترسی ندارید.";
                break;
            case 'add_admin':
                $t = "✅ $data به لیست مدیران اضافه شد.";
                break;
            case 'add_admin_ok':
                $t = "✅ اکانت شما مدیر ربات شد.";
                break;
            case 'has_admin':
                $t = "✅ این شناسه ادمین ربات می‌باشد.";
                break;
            case 'hasn_admin':
                $t = "❌ این شناسه ادمین ربات نیست.";
                break;
            case 'delete_admin':
                $t = "✅ $data از لیست مدیران حذف شد.";
                break;
            case 'delete_admin_ok':
                $t = "❌ شما از مدیریت ربات عزل شدید.";
                break;
            case 'not_payment':
                $t = "❌ هیچ درگاهی ثبت نشده است.";
                break;
            case 'online_status':
                $t = "✅ روشن شد.";
                break;
            case 'offline_status':
                $t = "❌ خاموش شد.";
                break;
            case 'update_status':
                $t = "🔄 آپدیت شد.";
                break;
            case 'edit_payment':
                $t = "💳 درگاه مورد نظر خود را برای ویرایش انتخاب کنید:";
                break;
            case 'edit_payment_1':
                $payment = $data;
                $name = $payment['name'];
                $code = type_text($payment['code'], 's');
                $file = $payment['file'];
                $status = off($payment['status']);
                $ip = off($payment['ip']);
                $type = $payment['type'];
                $payment_data = json_decode($payment['data'], true);
                $t = "ℹ️ اطلاعات فعلی:
وضعیت درگاه: {$status}
اسم: {$name}
کارمزد درگاه : {$payment_data['percent_fee']}%
حداکثر کارمزد: {$payment_data['max_fee']} تومان
کد درگاه:
{$code}
فایل درگاه: {$file}
محدودیت IP: {$ip}
نوع درگاه : {$type}

بخش مورد نظر را انتخاب کنید";
                break;
            case 'edit_payment_2':
                $t = "🔤 نام جدید خود را وارد کنید:";
                break;
            case 'edit_payment_3':
                $t = "🔑 کد جدید خود را وارد کنید:";
                break;
            case 'edit_payment_4':
                $t = "❌ برای حذف، روی دکمه تایید کلیک کنید.
توجه کنید که این عملیات غیرقابل بازگشت است.
فایل درگاه در این مرحله پاک نمی‌شود.";
                break;
            case 'ok_payment_edit':
                $t = "✅ با موفقیت تغییر کرد.";
                break;
            case 'ok_payment_delete':
                $t = "✅ با موفقیت حذف شد.";
                break;
            case 'payment_edit_setting':
                $t = "⚙️ انتخاب کنید:";
                break;
            case 'type_payment':
                $t = "💳 نوع درگاه پرداخت را انتخاب کنید:";
                break;
            case 'percent_fee_payment':
                $t = "مقدار درصد کارمزد را به صورت عددی وارد کنید.\nدرصورت وارد کردن صفر، کارمزدی دریافت نمی‌شود.\n\n💸 برای مثال:\nاگر می‌خواهید 5% کارمزد دریافت کنید، عدد 5 را وارد کنید.\nاگر می‌خواهید 0.5% کارمزد دریافت کنید، عدد 0.5 را وارد کنید.";
                break;
            case 'max_fee_payment':
                $t = "💸 مقدار حداکثر کارمزد را به تومان وارد کنید:\nاگر صفر وارد کنید، محدودیتی وجود ندارد.\n\nحداکثر کارمزد یعنی اگر مبلغ کارمزد بیشتر از این عدد شود، فقط همین مقدار به عنوان کارمزد حساب می‌شود و بیشتر از آن دریافت نمی‌شود.";
                break;
            case 'payment_discount':
                $t = "💸 انتخاب کنید:";
                break;
            case 'payment_edit_setting_2':
                $data = number_format($data);
                $t = "🔢 عدد فعلی: {$data}
مقدار جدید را به تومان وارد کنید:";
                break;
            case 'kyc_media':
                $t = "نوع مدرک مورد نیاز برای بخش احراز هویت رو انتخاب کنید";
                break;
            case 'ok_payment_edit_setting':
                $t = "✅ انجام شد.";
                break;
            case 'rate_panel_1':
                $usd_rate_raw  = (float)($data[0] ?? 0);
                $usd_auto      = (int)($data[1] ?? 0);
                $interval      = (int)($data[2] ?? 60);
                $last          = ((int)($data[3] ?? 0)) > 0 ? jdate('Y/m/d H:i', $data[3]) : 'هرگز';
                $auto_starz    = (int)($data[5] ?? 0);
                $starz_usd_raw = (float)($data[6] ?? 0);

                $usd_rate   = number_format($usd_rate_raw);
                $starz_rate = number_format((float)($data[4] ?? 0));
                $starz_usd  = number_format($starz_usd_raw, 4);

                $usd_mode_txt   = $usd_auto ? "🤖 خودکار (هر {$interval} دقیقه)" : "✍️ دستی";
                $starz_mode_txt = $auto_starz ? "🤖 خودکار (بر اساس نرخ دلار)" : "✍️ دستی";

                $starz_conv_line = "";
                if ($auto_starz) {
                    $starz_converted = number_format(round($starz_usd_raw * $usd_rate_raw));
                    $starz_conv_line = "\n💱 تبدیل‌شده: {$starz_usd} دلار × {$usd_rate} = {$starz_converted} تومان";
                }

                $t = "💱 تنظیمات نرخ دلار و استارز

💵 قیمت دلار: {$usd_rate} تومان
🔄 دریافت: {$usd_mode_txt}
📅 آخرین بروزرسانی: {$last}
━━━━━━━━━━━━━
⭐️ قیمت استارز: {$starz_rate} تومان
🔄 محاسبه: {$starz_mode_txt}
💱 هر استارز: {$starz_usd} دلار{$starz_conv_line}

👇 برای تغییر، روی دکمه‌ها بزنید:";
                break;
            case 'rate_ask_usd':
                $t = "💵 قیمت دلار را به تومان وارد کنید:\n\nمقدار فعلی: {$data} تومان";
                break;
            case 'rate_ask_starz':
                $t = "⭐️ قیمت هر عدد استارز را به تومان وارد کنید:\n\nمقدار فعلی: {$data} تومان";
                break;
            case 'rate_ask_starz_usd':
                $t = "💱 قیمت هر عدد استارز را به دلار وارد کنید:\n\nمقدار فعلی: {$data} دلار";
                break;
            case 'rate_ask_interval':
                $t = "⏱ بازه بروزرسانی خودکار نرخ دلار را به دقیقه وارد کنید:\n(بین ۵ تا ۱۴۴۰ دقیقه)\n\nمقدار فعلی: {$data} دقیقه";
                break;
            case 'ch_detail_text':
                $ch_key = $data[0];
                $val    = $data[1];
                $name = $data[2];
                $ch_info = [
                    'channel_main'             => ['label' => '📢 کانال اصلی',        'desc' => 'پیام‌های موفقیت سفارش و اطلاعات عمومی در این کانال ارسال می‌شود.'],
                    'channel_transaction'      => ['label' => '💰 تراکنشات',           'desc' => 'تراکنش‌های مالی کاربران (شارژ، خرید) در این کانال ثبت می‌شود.'],
                    'channel_payment_offline'  => ['label' => '💳 کارت به کارت',       'desc' => 'درخواست‌های پرداخت کارت‌به‌کارت در این کانال ارسال می‌شود.'],
                    'channel_ads'              => ['label' => '📢 تبلیغاتی',           'desc' => 'پیام موفقیت خرید با تبلیغاتی به این کانال ارسال می‌شود.'],
                    'channel_order_api'        => ['label' => '🛍 سفارشات API',        'desc' => 'سفارشاتی که از طریق وب‌سرویس ثبت می‌شوند در این کانال نمایش داده می‌شوند.'],
                    'channel_order_noapi'      => ['label' => '🛍 سفارشات دستی',      'desc' => 'سفارشاتی که به‌صورت دستی (بدون وب‌سرویس) ثبت می‌شوند اینجا نمایش داده می‌شوند.'],
                    'channel_support'          => ['label' => '💬 پشتیبانی',           'desc' => 'تیکت‌های پشتیبانی کاربران در این کانال ارسال می‌شود.'],
                    'channel_kyc'              => ['label' => '🔐 احراز هویت',         'desc' => 'درخواست‌های احراز هویت کاربران (تصویر کارت) اینجا ارسال می‌شود.'],
                    'channel_gift_transaction' => ['label' => '💸 برداشت هدیه',        'desc' => 'درخواست‌های برداشت از کیف پول هدیه در این کانال ثبت می‌شوند.'],
                    'channel_errors'           => ['label' => '‼️ خطاها',             'desc' => 'خطاهای سیستمی و خطاهای ثبت سفارش در این کانال لاگ می‌شوند.'],
                ];
                $info    = $ch_info[$ch_key] ?? ['label' => $ch_key, 'desc' => ''];
                $current = ($val && $val != '0') ? (is_numeric($val) ? "آیدی عددی: <code>{$val}</code>" : "@{$val}") : '❌ تنظیم نشده';
                $t = "📌 {$info['label']}

ℹ️ {$info['desc']}
🪧 {$name}

📍 وضعیت فعلی: {$current}";
                break;
            case 'error_channel_3':
                $t = "❌ ابتدا ربات را داخل چنل ادمین کرده و سپس یک پست فوروارد کنید.";
                break;
            case 'error_channel_4':
                $t = "❌ لطفاً یک پیام از یک چنل فوروارد کنید.";
                break;
            case 'edit_text_1':
                $t = "📝 متن فعلی:
{$data}
متن جدید را ارسال کنید:";
                break;
            case 'ok_edit_text':
                $tx = $data[0];
                $name = $data[1];
                $t = "✅ متن
{$tx}

برای {$name} ثبت شد.";
                break;
            case 'error_text_1':
                $t = "❌ دستور ناشناخته.";
                break;
            case 'edit_gift_1':
                $t = "🎁 متن فعلی:
{$data}
متن جدید را ارسال کنید:";
                break;
            case 'edit_gift_2':
                $t = "🎁 بنر فعلی برای تغییر، بنر مورد نظر خود را ارسال نمایید:";
                break;
            case 'edit_gift_3':
                $t = "🎁 مقدار فعلی:
{$data}
مقدار جدید را ارسال کنید:";
                break;
            case 'ok_edit_gift':
                $t = "✅ انجام شد.";
                break;
            case 'error_referral_1':
                $t = "❌ دستور ناشناخته.";
                break;
            case 'cr_code_1':
                $t = "🔤 لطفاً کد مورد نظر خود را وارد کنید.
برای مثال: <code>code_7</code> | <code>nowroz</code>
متن انگلیسی یا عدد وارد کنید.";
                break;
            case 'edit_code_1':
                $t = "🔄 کد مورد نظر خود را برای ویرایش انتخاب کنید:";
                break;
            case 'edit_code_error_1':
                $t = "❌ هیچ کدی ثبت نشده است.";
                break;
            case 'edit_code_error_2':
                $t = "❌ این کد از قبل وجود دارد.";
                break;
            case 'edit_code_error_3':
                $t = "❌ متن دیگری وارد کنید.";
                break;
            case 'edit_code_error_4':
                $t = "❌ فقط عدد ارسال کنید.";
                break;
            case 'edit_code_error_5':
                $t = "❌ فقط عدد ارسال کنید.";
                break;
            case 'edit_code_error_6':
                $t = "❌ موارد را در سه خط وارد کنید.";
                break;
            case 'edit_code_error_7':
                $t = "❌ فقط عدد ارسال کنید.";
                break;
            case 'edit_code_error_8':
                $t = "❌ فقط عدد ارسال کنید.";
                break;
            case 'edit_code_error_9':
                $t = "❌ موارد را در دو خط وارد کنید.";
                break;
            case 'edit_code_error_10':
                $t = "❌ وجود ندارد.";
                break;
            case 'cr_code_2':
                $t = "🔄 لطفاً نوع کد خود را انتخاب کنید:

نوع درصدی به میزان درصد وارد شده به مبلغ پرداختی شخص اضافه شده و به کیف پول شخص وارد می‌شود.
نوع کد شارژ به میزان مبلغ وارد شده به حساب شخص وارد می‌شود.";
                break;
            case 'cr_code_3':
                $t = "🔢 لطفاً اطلاعات زیر را وارد کنید:
درصد (0 الی 100)
سقف کد (تومان)
تعداد قابل استفاده (نفر)
موارد را در سه خط وارد کنید.
منظور از سقف کد مقدار شارژی که در حداکثر پرداختی به کاربر داده می‌شود.";
                break;
            case 'cr_code_4':
                $t = "🔢 لطفاً اطلاعات زیر را وارد کنید:
مقدار کد (تومان)
تعداد قابل استفاده (نفر)
موارد را در دو خط وارد کنید.";
                break;
            case 'ok_add_discount_code':
                $t = "✅ با موفقیت اضافه شد.";
                break;
            case 'edit_code_2':
                $info = $data;
                $code = $info['code'];
                $type = str_replace(['fix', 'percent'], ['کد شارژ', 'کد تخفیف'], $info['type']);
                $decode = json_decode($info['amount'], true);
                $max = $decode['max'];
                $amount = $decode['amount'];
                $count = $info['count'];
                $t = "🔄 کد: $code
نوع: $type
مقدار: $amount
سقف: $max
تعداد قابل استفاده: $count
انتخاب کنید:";
                break;
            case 'edit_code_3':
                $t = "❌ آیا عملیات حذف کد تخفیف را تایید می‌کنید؟";
                break;
            case 'edit_code_4':
                $t = "🔢 لطفاً مقدار جدید را ارسال کنید:";
                break;
            case 'ok_edit_code':
                $t = "✅ انجام شد.";
                break;
            case 'cant_edit_code':
                $t = "❌ این عملیات برای این نوع کد قابل انجام نمی‌باشد.";
                break;
            case 'add_product_1':
                $t = "🛍 بخش مورد نظر خود را انتخاب کنید:";
                break;
            case 'type_of_delete':
                $t = "❌ بخش مورد نظر خود را برای حذف انتخاب کنید:";
                break;
            case 'product_status':
                $t = "🛍 بخش مورد نظر خود را انتخاب کنید:
با کلیک بر روی هر قسمت به پنل محصولات دسترسی پیدا می‌کنید.
با کلیک بر روی وضعیت هر محصول و یا دسته، آن را خاموش و یا روشن کنید.";
                break;
            case 'product_status_error_1':
                $t = "❌ محصولی یافت نشد.";
                break;
            case 'category_add_1':
                $t = "📂 نام دسته‌بندی را ارسال کنید:";
                break;
            case 'category_add_error_1':
                $t = "❌ دسته‌بندی با این اسم وجود دارد.";
                break;
            case 'category_add_error_2':
                $t = "❌ هیچ دسته‌بندی یافت نشد.";
                break;
            case 'category_add_error_3':
                $t = "❌ داخل این دسته‌بندی محصول وجود دارد و امکان افزودن زیر دسته نمی‌باشد.";
                break;
            case 'category_add_error_4':
                $t = "❌ دسته‌بندی با این اسم وجود ندارد.";
                break;
            case 'category_add_error_5':
                $t = "❌ هیچ دسته‌بندی یافت نشد.";
                break;
            case 'product_add_bot_key':
                $t = "🔘 از دکمه‌های ربات استفاده کنید:";
                break;
            case 'category_add_2':
                $t = "✅ این بخش ایجاد شد.
در صورتی نیاز به اضافه کردن دسته‌بندی دیگر، نام دسته را وارد کنید:";
                break;
            case 'sub_category_add_1':
                $t = "📂 دسته‌بندی مورد نظر خود را انتخاب کنید:";
                break;
            case 'sub_category_add_2':
                $t = "📂 نام این زیر دسته را وارد کنید:";
                break;
            case 'sub_category_add_3':
                $t = "✅ این بخش ایجاد شد.
در صورت نیاز به اضافه کردن زیر دسته دیگر در همین دسته‌بندی، نام آن را وارد کنید:";
                break;
            case 'product_add_1':
                $t = "📂 دسته‌بندی مورد نظر خود را انتخاب کنید:";
                break;
            case 'product_add_2':
                $t = "📂 دسته‌بندی مورد نظر خود را انتخاب کنید:";
                break;
            case 'product_add_3':
                $t = "🔗 وب سرویس مورد نظر برای این محصول را انتخاب کنید:";
                break;
            case 'product_add_4':
                if ($data == 1) {
                    $t = "📦 اطلاعات محصول مورد نظر را به این صورت بفرستید:
ایدی سرویس
قیمت
نام محصول (اختیاری)
حداقل (اختیاری)
حداکثر (اختیاری)
قیمت دلاری (اختیاری)

در صورت وارد نکردن موارد اختیاری، از وب سرویس دریافت می‌شود.
قیمت دلاری فقط زمانی قابل ارسال است که حداقل و حداکثر هم وارد شده باشند؛ در این صورت خط ششم قیمت دلاری محصول است و حالت دلاری آن روشن می‌شود.

هر کدام از موارد را در یک خط و جمع خط‌ها 2 و حداکثر 6 شود.

مثال:

325
9000";
                } elseif ($data == 2) {
                    $t = "📦 اطلاعات محصول مورد نظر را به این صورت بفرستید:
نام محصول
ایدی سرویس (در صورت نبود صفر وارد کنید)
قیمت
حداقل
حداکثر
قیمت دلاری (اختیاری)

در صورت ارسال خط ششم، آن قیمت دلاری محصول در نظر گرفته می‌شود و حالت دلاری آن روشن می‌شود.

هر کدام از موارد را در یک خط و جمع خط‌ها 5 و در صورت داشتن قیمت دلاری 6 شود.

مثال:

ممبر
0
10000
10
10000";
                } else {
                    $t = "📦 اطلاعات محصول مورد نظر را به این صورت بفرستید:

نام محصول
قیمت
حداقل
حداکثر
قیمت دلاری (اختیاری)

هر کدام از موارد را در یک خط
و جمع خط‌ها 4 و در صورت داشتن قیمت دلاری 5 شود.

اگر قیمت دلاری وارد شود، حالت دلاری محصول روشن می‌شود و قیمت آن بر اساس نرخ دلار روز محاسبه می‌شود.

مثال:

ممبر
9000
100
10000";
                }
                break;
            case 'product_add_preview':
                $p = $data[0];
                $cat = $data[1];
                $name_show = json_decode($p['name']);
                $cat_name = getCategoryHierarchy($cat['id']);
                $t = "📋 پیش‌نمایش محصول — قبل از ثبت بررسی کنید:

نام: <code>{$name_show}</code>
دسته‌بندی:
{$cat_name}
قیمت: <code>" . number_format((float) $p['price']) . "</code> تومان";
                if (!empty($p['is_usd'])) {
                    $t .= "
قیمت دلاری: <code>" . number_format((float) $p['price_usd'], 4) . "</code> دلار (حالت دلاری روشن ✅)";
                }
                $t .= "
حداقل: <code>{$p['min']}</code>
حداکثر: <code>{$p['max']}</code>";
                if (($p['api'] ?? 'noapi') !== 'noapi') {
                    $t .= "
وب سرویس: " . json_decode($p['api']) . "
ایدی سرویس: <code>{$p['service']}</code>";
                }
                $t .= "

برای ثبت نهایی تایید کنید، یا اطلاعات را دوباره ارسال کنید:";
                break;
            case 'product_add_5':
                $category = getCategoryHierarchy($data[0]);
                $name_product = $data[1];
                $price = $data[2];
                $min = $data[3];
                $max = $data[4];
                $t = "✅ محصول با مشخصات زیر وارد شد:
دسته:
<code>{$category}</code>
نام محصول: <code>$name_product</code>
قیمت: <code>$price</code>

حداقل: <code>$min</code>
حداکثر: <code>$max</code>
🔹🔸🔹🔸🔹🔸🔹🔸🔹🔸🔹🔸
توضیحات محصول را وارد کنید:";
                break;
            case 'product_add_error_1':
                $t = "❌ با عرض پوزش مشکلی پیش آمده است.";
                break;
            case 'product_add_error_2':
                $t = "❌ محصولی با این اسم از قبل وجود دارد.";
                break;
            case 'product_add_error_3':
                $t = "❌ مقادیر عددی اشتباه وارد شده‌اند. فقط عدد بفرستید.";
                break;
            case 'product_add_error_4':
                $t = "❌ حداکثر تعداد کاراکتر مجاز برای اسم 130 عدد است. لطفاً نام کمتری وارد کنید.";
                break;
            case 'product_add_error_5':
                $t = "❌ موارد خواسته شده را در 4 خط (یا 5 خط در صورت داشتن قیمت دلاری) ارسال کنید.";
                break;
            case 'product_add_error_6':
                $t = "❌ محصول در وب سرویس مورد نظر یافت نشد.";
                break;
            case 'product_add_error_7':
                $t = "❌ خطا در دریافت اطلاعات از وب سرویس.";
                break;
            case 'product_add_error_8':
                $t = "❌ موارد را در تعداد خط درست ارسال کنید (حداکثر 6 خط در صورت داشتن قیمت دلاری).";
                break;
            case 'product_add_confirmed_alert':
                $t = "✅ محصول ثبت شد.";
                break;
            case 'product_preview_expired':
                $t = "⌛️ این پیش‌نمایش دیگر معتبر نیست.";
                break;
            case 'product_add_retry_wait':
                $t = "🔄 در انتظار ارسال مجدد اطلاعات محصول...";
                break;
            case 'update_api_product_ok':
                $t = "✅ محصول با موفقیت اضافه شد.";
                break;
            case 'update_api_product_nok':
                $t = "❌ محصول اضافه نشد.";
                break;
            case 'update_api_product_error_1':
                $t = "❌ خطا در پردازش محصول.";
                break;
            case 'product_add_error_9':
                $t = "❌ وب سرویس انتخابی یافت نشد.";
                break;
            case 'product_add_error_10':
                $t = "❌ وب سرویس باید از نوع اسمارت پنل باشد.";
                break;
            case 'product_add_type':
                $t = "🎯 نوع سرویس محصول را انتخاب کنید:

🔵 معمولی: سفارش عادی
💬 کامنت دلخواه: ابتدا کامنت گرفته می‌شود
📦 پکیجی: قیمت دونه‌ای بدون تقسیم";
                break;
            case 'product_add_error_type':
                $t = "❌ لطفا از گزینه‌های موجود انتخاب کنید.";
                break;
            case 'product_add_repeat':
                $t = "🔄 در صورت نیاز به اضافه کردن محصول دیگری در این دسته، وب سرویس آن را مشخص کنید:";
                break;
            case 'product_add_repeat_2':
                $t = "✅ توضیحات:
$data
ثبت شد.
در صورت نیاز به اضافه کردن محصول دیگری در این دسته، وب سرویس آن را مشخص کنید:";
                break;
            case 'edit_shop_1':
                $t = "🛍 انتخاب کنید:";
                break;
            case 'edit_shop_error_0':
                $t = "❌ محصولی وجود ندارد.";
                break;
            case 'edit_shop_error_1':
                $t = "❌ دسته‌بندی وجود ندارد.";
                break;
            case 'edit_shop_error_2':
                $t = "❌ هیچ دسته‌بندی یافت نشد.";
                break;
            case 'edit_shop_error_3':
                $t = "❌ محصولی وجود ندارد.";
                break;
            case 'edit_category':
                $t = "🔄 برای ویرایش این دسته، بر روی دکمه زیر کلیک کنید:
دسته‌بندی: $data";
                break;
            case 'edit_shop_2':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'edit_shop_3':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'edit_product':
                $t = "🔄 برای ویرایش این محصول، بر روی دکمه زیر کلیک کنید:
دسته‌بندی: $data";
                break;
            case 'edit_category_info':
                $category = $data['0'];
                $name = json_decode($category['name'], true);
                $ordering = $category['ordering'];
                $under = ($data['1']) ? 'دارد' : 'ندارد';
                $date = ($data['2']) ? "\n" . jdate('Y/m/d H:i:s') : null;
                $t = "ℹ️ اطلاعات فعلی:
نام دسته: {$name}
زیر دسته: {$under}
اولویت نمایش: {$ordering}{$date}";
                break;
            case 'edit_products_panel':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'edit_under_info':
                $category = $data['0'];
                $category_by = getCategoryHierarchy($category['id']);
                $name = json_decode($category['name'], true);
                $ordering = $category['ordering'];
                $date = ($data['1']) ? "\n" . jdate('Y/m/d H:i:s') : null;
                $t = "ℹ️ اطلاعات فعلی: {$name}
دسته بالایی:
{$category_by}
اولویت نمایش: {$ordering}{$date}";
                break;
            case 'edit_product_info':
                $product = $data['0'];

                if ($product['api'] !== 'noapi') {
                    $service = json_decode($product['api']) . "\nایدی سرویس :" . $product['service'];
                    $confirm = ($product['confirm'] > 0) ? "حداقل سفارش نیاز به تایید : " . $product['confirm'] : 'بدون نیاز به تایید';
                } else {
                    $service = "محصول دستی است";
                    $confirm = "محصول دستی می‌باشد";
                }
                
                $product_type_display = '';
                switch ($product['type']) {
                    case 'default':
                        $product_type_display = 'معمولی 🔵';
                        break;
                    case 'custom_comments':
                        $product_type_display = 'کامنت دلخواه 💬';
                        break;
                    case 'package':
                        $product_type_display = 'پکیجی 📦';
                        break;
                    default:
                        $product_type_display = 'معمولی 🔵';
                        break;
                }
                
                $product['name'] = json_decode($product['name']);
                $product['price'] = $product['price'];
                $product['final_price'] = $product['price'] + (($product['price'] / 100) * $product['discount']);
                $category_by = getCategoryHierarchy($product['category_id']);

                $usd_line = '';
                if (!empty($product['is_usd'])) {
                    $usd_rate_now = (float) get_option('usd_rate', 0);
                    $usd_final = round(((float) $product['price_usd']) * $usd_rate_now);
                    $usd_line = "\n\n💵 حالت دلاری: ✅ روشن
قیمت دلاری: " . number_format((float) $product['price_usd'], 4) . " دلار
قیمت نهایی برای کاربر: " . number_format($usd_final) . " تومان (بر اساس نرخ دلار فعلی: " . number_format($usd_rate_now) . ")";
                } elseif (!empty($product['price_usd'])) {
                    $usd_line = "\n\n💵 حالت دلاری: ❌ خاموش (قیمت دلاری ثبت‌شده: " . number_format((float) $product['price_usd'], 4) . " دلار، فعلاً استفاده نمی‌شود)";
                }

                $date = ($data['1']) ? "\n" . jdate('Y/m/d H:i:s') : null;
                $t = "ℹ️ اطلاعات فعلی محصول:
اسم: {$product['name']}
دسته‌بندی:
{$category_by}

قیمت: {$product['price']}
تخفیف: {$product['discount']}
قیمت نهایی: {$product['final_price']}{$usd_line}

حداقل: {$product['min']}
حداکثر: {$product['max']}
نوع سرویس: {$product_type_display}
وب سرویس: {$service}
تایید سفارش: {$confirm}
الگو-پترن: {$product['pattern']}
توضیحات:
<code>{$product['info']}</code>{$date}";
                break;
            case 'edit_info':
                switch ($data['0']) {
                    case 'name':
                        $t = "🔤 اسم مورد نظر خود را ارسال کنید:";
                        break;
                    case 'ordering':
                        $t = "🔢 اولویت نمایش را وارد کنید:
محدوده: {$data['1']}-{$data['2']}
یک عدد مابین دو عدد ارسال کنید:";
                        break;
                    case 'delete':
                        $t = "❌ شما درخواست حذف را کرده‌اید. تایید کنید؟";
                        break;
                    case 'price':
                        $t = "💰 قیمت مورد نظر خود را ارسال کنید:";
                        break;
                    case 'price_usd':
                        $r_prod = $data['1'];
                        $cur_usd = number_format((float) ($r_prod['price_usd'] ?? 0), 4);
                        $usd_status = !empty($r_prod['is_usd']) ? '✅ روشن' : '❌ خاموش';
                        $t = "💵 قیمت دلاری محصول را ارسال کنید:

مقدار فعلی: {$cur_usd} دلار
وضعیت دلاری این محصول: {$usd_status}

⚠️ این عدد فقط زمانی برای کاربر محاسبه می‌شود که وضعیت «دلاری» محصول روشن باشد (از دکمه کنار بروزرسانی در صفحه اطلاعات محصول). در آن حالت، قیمت نهایی = این عدد × نرخ دلار فعلی ربات.";
                        break;
                    case 'min':
                        $t = "🔢 محدوده مورد نظر خود را ارسال کنید:";
                        break;
                    case 'info':
                        $t = "📝 توضیحات مورد نظر خود را ارسال کنید:";
                        break;
                    case 'api':
                        $t = "🔗 وب سرویس مورد نظر برای این محصول را انتخاب کنید:";
                        break;
                    case 'discount':
                        $t = "💸 تخفیف مورد نظر خود را ارسال کنید:\n-100 الی 100
100 به معنای افزایش قیمت به میزان 100% میباشد
-100 به معنای کاهش قیمت به میزان 100% میباشد";
                        break;
                    case 'confirm':
                        $t = "❓ برای اینکه قبل از ارسال به وب سرویس سفارش را تایید کنید، حداقل مقدار سفارشی که نیاز به تایید دارد را با عدد وارد کنید.
وارد کردن عدد صفر به معنی ارسال بدون بررسی و تایید ادمین می‌باشد.";
                        break;
                    case 'service':
                        $t = "🔑 ایدی سرویس مورد نظر خود را ارسال کنید:";
                        break;
                    case 'pattern':
                        $t = "🔤 پترن مورد نظر برای این محصول را انتخاب کنید:";
                        break;
                    case 'type':
                        $t = "🔄 نوع سرویس مورد نظر خود را انتخاب کنید:";
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'ok_delete_products':
                $t = "✅ انجام شد.";
                break;
            case 'error_edit_product_1':
                $t = "❌ اسم محصول وارد شده تکراری می‌باشد.";
                break;
            case 'error_edit_product_2':
                $t = "❌ لطفاً یک عدد ارسال کنید.";
                break;
            case 'error_edit_product_3':
                $t = "❌ لطفاً یک عدد ارسال کنید.";
                break;
            case 'error_edit_product_4':
                $t = "❌ لطفاً دو عدد ارسال کنید. در هر خط یک عدد.";
                break;
            case 'error_edit_product_5':
                $t = "❌ از دکمه‌های ربات استفاده کنید.";
                break;
            case 'error_edit_product_6':
                $t = "❌ یک عدد بین -100 الی 100 وارد کنید.";
                break;
            case 'error_edit_product_7':
                $t = "❌ لطفاً یک عدد ارسال کنید.";
                break;
            case 'error_edit_product_8':
                $t = "❌ اسم محصول وارد شده بیش از حد مجاز می‌باشد.";
                break;
            case 'error_edit_product_type':
                $t = "❌ لطفا از گزینه‌های موجود نوع سرویس انتخاب کنید.";
                break;
            case 'update_api_1':
                $t = "🔄 وب سرویس مورد نظر خود را انتخاب کنید:";
                break;
            case 'update_api_info_1':
                $t = "📂 افزودن تمامی دسته بندی ها به صورت خودکار\n\nربات تمام دسته‌بندی‌های موجود در وب‌سرویس را بررسی می‌کند و دسته‌هایی که در ربات وجود ندارند را اضافه می‌کند.\n\n⚠️ محصول اضافه نمی‌شود، فقط دسته‌بندی‌ها.";
                break;
            case 'update_api_info_2':
                $t = "🛍 افزودن تمامی محصولات به صورت خودکار\n\nربات محصولات وب‌سرویس را بررسی می‌کند و فقط محصولاتی که دسته‌بندی‌شان در ربات وجود دارد را اضافه می‌کند.\n\n⚠️ محصولات تکراری مجدداً اضافه نمی‌شوند.";
                break;
            case 'update_api_info_3':
                $t = "📂 انتخاب و افزودن دسته بندی ها به صورت انتخابی\n\nلیست دسته‌بندی‌های وب‌سرویس نشان داده می‌شود. می‌توانید دسته‌های مورد نظر را یکی یکی انتخاب و اضافه کنید.\n\n⚠️ محصول اضافه نمی‌شود، فقط دسته‌بندی‌ها.";
                break;
            case 'update_api_info_4':
                $t = "🛍 انتخاب و افزودن محصولات به صورت انتخابی\n\nلیست محصولات وب‌سرویس نشان داده می‌شود. می‌توانید محصولات مورد نظر را یکی یکی انتخاب کنید و به دسته‌بندی دلخواه اضافه کنید.\n\n⚠️ دسته‌بندی از قبل باید در ربات وجود داشته باشد.";
                break;
            case 'update_api_info_5':
                $t = "📦 افزودن تمامی محصولات و دسته بندی ها\n\nتمام دسته‌بندی‌ها و محصولات وب‌سرویس که در ربات وجود ندارند به صورت کامل و خودکار اضافه می‌شوند.\n\n⚠️ این گزینه ممکن است تعداد زیادی محصول اضافه کند.";
                break;
            case 'update_api_info_6':
                $t = "🔄 بروزرسانی محصولات فعلی\n\nمحصولات موجود در ربات با اطلاعات جدید وب‌سرویس بروز می‌شوند (قیمت، اسم، توضیحات و محدوده سفارش بر اساس تنظیمات انتخاب‌شده).\n\n💡 اگر گزینه «محدوده سفارش» روشن باشد، محصولاتی که در وب‌سرویس حذف شده‌اند خاموش می‌شوند و آن‌هایی که هنوز هستند روشن می‌مانند.";
                break;
            case 'update_api_info_7':
                $t = "📦 افزودن یک دسته انتخابی و محصولاتش بصورت خودکار\n\nیک دسته‌بندی از لیست وب‌سرویس انتخاب می‌کنید. اگر دسته در ربات وجود نداشت ایجاد می‌شود، سپس تمام محصولات آن دسته که در ربات نیستند اضافه می‌شوند.";
                break;
            case 'update_api_2':
                $t = "🔄 تنظیمات عملیات را انتخاب کنید، سپس تایید کنید:";
                break;
            case 'update_api_2_type6':
                $t = "🔄 تنظیمات بروزرسانی محصولات فعلی را انتخاب کنید، سپس تایید کنید:

💡 روش قیمت:
📈 فقط افزایش — اگر قیمت جدید وب‌سرویس از قیمت فعلی محصول کمتر بود، قیمت تغییر نمی‌کند (قیمت هرگز کاهش پیدا نمی‌کند).
🔄 همیشه بروز — قیمت همیشه با نرخ جدید وب‌سرویس جایگزین می‌شود، چه بیشتر چه کمتر.

💡 تبدیل دلار به تومان: اگر روشن باشد، قیمت دریافتی از وب‌سرویس بر اساس نرخ دلار فعلی ربات به تومان تبدیل می‌شود.";
                break;
            case 'update_api_wait':
                $t = "⏳ در حال انجام عملیات، لطفاً صبر کنید...";
                break;
            case 'update_api_ok':
                switch ($data['0']) {
                    case '1':
                        $t = "✅ تعداد {$data['1']} دسته‌بندی به ربات اضافه شد.";
                        break;
                    case '2':
                        $t = "✅ تعداد {$data['1']} محصول به ربات اضافه شد.";
                        break;
                    case '6':
                        $t = "✅ تعداد {$data['1']} محصول به‌روز شد و تعداد {$data['2']} محصول به دلیل غیرفعال شدن در وب سرویس خاموش شد.";
                        break;
                    case '7':
                        if (isset($data['2']) && $data['2'] == 1) {
                            if ($data['1'] > 0) {
                                $t = "⚠️ این دسته‌بندی قبلاً وجود داشت و {$data['1']} محصول جدید به آن اضافه شد.";
                            } else {
                                $t = "⚠️ این دسته‌بندی قبلاً وجود داشت و محصول جدیدی اضافه نشد.";
                            }
                        } else {
                            if ($data['1'] > 0) {
                                $t = "✅ تعداد {$data['1']} محصول برای دسته‌بندی انتخاب‌شده اضافه شد.";
                            } else {
                                $t = "✅ دسته‌بندی اضافه شد، اما محصول جدیدی برای افزودن یافت نشد.";
                            }
                        }
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'update_api_add_category_1':
                $t = "🔄 نوع این دسته‌بندی را مشخص کنید:";
                break;
            case 'update_report_link':
                $report_url = $data[0];
                $variant = $data[1] ?? 'update';
                if ($variant === 'category') {
                    $title = "📊 گزارش دسته «{$data[2]}»:";
                } elseif ($variant === 'final') {
                    $title = "📊 گزارش نهایی:";
                } else {
                    $title = "📊 گزارش بروزرسانی:";
                }
                $t = "{$title}\n{$report_url}";
                break;
            case 'delete_all_1':
                $t = "❌ عملیات فوق را تایید می‌کنید؟

$data";
                break;
            case 'delete_all_2':
                $t = "❌ عملیات فوق را تایید می‌کنید؟

$data";
                break;
            case 'delete_all_3':
                $t = "✅ انجام شد.";
                break;
            case 'display_product':
                $s = date('s');
                $t = "🔍 انتخاب کنید $s";
                break;
            case 'DIFF_TIME_1':
                $t = "⏰ مقدار فعلی: {$data}
اختلاف ساعت را به عدد و ساعت وارد کنید:";
                break;
            case 'usd_rate_interval_error':
                $t = "❌ مقدار نامعتبر است. عدد بین {$data[0]} تا {$data[1]} وارد کنید.";
                break;
            case 'usd_rate_updated':
                $t = "✅ نرخ دلار با موفقیت بروزرسانی شد: " . number_format($data) . " تومان";
                break;
            case 'usd_rate_auto_notify':
                $new_rate = $data[0];
                $old_rate = $data[1];
                $auto_starz = $data[2];
                $starz_rate = $data[3];
                $starz_line = $auto_starz ? "\n⭐️ قیمت استارز: " . number_format($starz_rate) . " تومان" : '';
                $t = "💵 نرخ دلار بروزرسانی شد\n\n"
                    . "نرخ جدید: " . number_format($new_rate) . " تومان\n"
                    . "نرخ قبلی: " . number_format($old_rate) . " تومان"
                    . $starz_line
                    . "\n\n🕐 " . jdate('Y/m/d H:i');
                break;
            case 'usd_rate_update_failed':
                $t = "❌ خطا در دریافت نرخ دلار از منبع خارجی. لطفاً دوباره تلاش کنید.";
                break;
            case 'send_int_min_one':
                $t = "❌ لطفاً عدد صحیح بیشتر از ۱ وارد کنید.";
                break;
            case 'min_more_than_max':
                $t = "❌ حداقل نمی‌تواند بیشتر از حداکثر ({$data}) باشد!";
                break;
            case 'max_less_than_min':
                $t = "❌ حداکثر نمی‌تواند کمتر از حداقل ({$data}) باشد!";
                break;
            case 'last_cron':
                $cron = $data['0'];
                $cron_done = $data['1'];
                $cron_auto = $data['2'];
                $t = "⏰ آخرین کرون همگانی: $cron
⏰ آخرین کرون سفارشات: $cron_done
⏰ آخرین کرون خودکار: $cron_auto
برای دریافت لینک کرون‌ها: /cron_link";
                break;
            case 'cron_link':
                $se  = type_text($data['0'], 'c');
                $se2 = type_text($data['1'], 'c');
                $se3 = type_text($data['2'], 'c');
                $t = "📤 کرون همگانی:\n$se\n================\n📦 کرون سفارشات:\n$se2\n================\n⚙️ کرون خودکار (نرخ دلار و...):\n$se3";
                break;
            case 'access_tx':
                $t = "🔐 دسترسی‌های شما از پنل مدیریت:";
                break;
            case 'status_order':
                $order = $data;
                if ($order['api'] != 'noapi') {
                    $api = '🔗 وب سرویس: ' . json_decode($order['api']);
                    if ($order['code_api'] != 0) {
                        $code = "کد: {$order['code_api']}\n";
                    } else {
                        $code = 0;
                    }
                    $result = $api . "\n" . $code;
                } else {
                    $result = "🛒 سفارش دستی می‌باشد.";
                }
                $t = "📋 وضعیت سفارش: {$order['status']}\n$result\n";
                break;
            case 'cancel_order':
                $t = "❌ سفارش لغو شد.";
                break;
            case 'cant_cancel_order':
                $t = "❌ این سفارش به وب سرویس ارسال شده است. امکان لغو نمی‌باشد.";
                break;
            case 'confirm_order':
                $t = "✅ سفارش تایید شد.";
                break;
            case 'send_answer':
                if ($data) {
                    $pm = type_text('لینک پیام', 'a', $data);
                } else {
                    $pm = null;
                }
                $t = "✉️ لطفاً پاسخ خود را ارسال کنید:
$pm";
                break;
            case 'send_answer_ok':
                $t = "✅ پیام با موفقیت ارسال شد.";
                break;
            case 'set_user_card':
                if ($data) {
                    $pm = type_text('لینک پیام', 'a', $data);
                } else {
                    $pm = null;
                }
                $t = "💳 شماره کارت کاربر را ارسال کنید. توجه کنید از این پس تمامی تراکنشات کاربر می‌بایست با این کارت پرداخت شود:
$pm";
                break;
            case 'set_user_card_ok':
                $t = "✅ شماره کارت ثبت شد.";
                break;
            case 'set_user_card_int':
                $t = "❌ لطفاً فقط شماره کارت ارسال کنید.";
                break;
            case 'sended':
                $t = "✅ ارسال شد.";
                break;
            case 'error_add_products_auto_1':
                $t = "❌ هیچ دسته‌بندی یافت نشد. لطفاً ابتدا حداقل یک دسته‌بندی به ربات اضافه کنید.";
                break;
            case 'error_add_products_auto_2':
                $t = "🔢 درصد افزایش قیمت‌ها را وارد کنید:
در صورت وارد کردن 0، هیچ درصدی افزایش و یا کاهش اعمال نمی‌شود.";
                break;
            case 'error_add_products_auto_3':
                $t = "🔢 عدد مورد نظر برای رند شدن را وارد کنید:
برای مثال در صورت وارد کردن عدد 100، قیمت به نزدیک‌ترین صدگان رو به بالا رند می‌شود و در صورت وارد کردن -100 به نزدیک‌ترین صدگان رو به پایین رند می‌شود.
برای دست نخوردن و رند نشدن عدد 0 را ارسال کنید.";
                break;
            case 'send_pm_result':
                $t = "✅ ارسال شد:
$data";
                break;
            case 'update_api_error_1':
                $t = "❌ هیچ وب سرویسی یافت نشد.";
                break;
            case 'status_off':
                $t = "❌ خاموش شد.";
                break;
            case 'status_on':
                $t = "✅ روشن شد.";
                break;
            case 'sort_by':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'order_by':
                $t = "🔄 انتخاب کنید:";
                break;
            case 'row_settings':
                $t = "🔢 نوع چینش خود را به صورت زیر ارسال کنید:

1-2-2-3

در مثال بالا، اولین ردیف تکی و ردیف دوم و سوم به صورت دوتایی و ردیف چهارم به صورت سه‌تایی نمایش داده می‌شود و از ردیف پنجم مجدد از تکی شروع می‌شود.
محدودیت در وارد کردن تعداد ردیف نمی‌باشد.
بهترین تنظیم تا 5 ردیف می‌باشد.";
                break;
            case 'page_settings':
                $t = "🔢 عدد مورد نظر خود را وارد کنید:
بهترین مقدار برای محصولات در هر صفحه 20 است.";
                break;
            case 'error_display_format':
                $t = "❌ لطفا عدد ارسال کنید.";
                break;
            case 'patterns_1':
                $t = "🔤 انتخاب کنید:";
                break;
            case 'add_pattern_1':
                $t = "🔤 لطفاً نام پترن مورد نظر خود را وارد کنید.
برای مثال: instagram | telegram";
                break;
            case 'add_pattern_2':
                $t = "📝 متن دریافت لینک را وارد کنید:\nاین متن برای راهنمایی کاربر موقع ارسال لینک نمایش داده میشود";
                break;
            case 'add_pattern_3':
                $t = "🔤 Regex دریافت لینک را وارد کنید:";
                break;
            case 'add_pattern_ok':
                $t = "✅ پترن با موفقیت اضافه شد.";
                break;
            case 'add_pattern_error_1':
                $t = "❌ لطفاً نام کوتاه‌تری و حتما به زبان انگلیسی باشد";
                break;
            case 'add_pattern_error_2':
                $t = "❌ پترن با نام $data وجود دارد.";
                break;
            case 'add_pattern_error_4':
            case 'edit_pattern_error_4':
                $t = "❌ Regex معتبر نیست.";
                break;
            case 'edit_pattern_1':
                $t = "🔤 پترن مورد نظر خود را برای ویرایش انتخاب کنید:";
                break;
            case 'edit_pattern_info':
                $pattern = $data;
                $regex = htmlspecialchars($pattern['pattern'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

$t = "ℹ️ اطلاعات فعلی پترن:
نام: {$pattern['type']}

متن دریافت لینک: {$pattern['text']}

Regex دریافت لینک:
<pre>{$regex}</pre>

انتخاب کنید:";
                break;
            case 'edit_pattern_3':
                $t = "📝 متن جدید دریافت لینک را وارد کنید:";
                break;
            case 'edit_pattern_4':
                $t = "🔤 Regex جدید دریافت لینک را وارد کنید:";
                break;
            case 'edit_pattern_updated':
                $pattern = $data;
                $t = "✅ پترن بروزرسانی شد.
نام: {$pattern['type']}
متن دریافت لینک: {$pattern['text']}
Regex دریافت لینک: <code>{$pattern['pattern']}</code>
انتخاب کنید:";
                break;
            case 'delete_pattern_confirm':
                $t = "❌ آیا از حذف پترن $data اطمینان دارید؟
این عملیات غیرقابل بازگشت است.";
                break;
            case 'delete_pattern_ok':
                $t = "✅ پترن با موفقیت حذف شد.";
                break;
            case 'delete_pattern_error_2':
                $t = "❌ پترن default قابل حذف نیست.";
                break;
            case 'send_reason_receipt':
                $t = "دلیل رد کردن پرداخت رو ارسال کن:";
                break;

            case 'send_up_receipt':
                $t = "مقدار موجودی مورد نظر رو وارد کن (به تومان):";
                break;

            case 'send_up_receipt_ok':
                $t = "✅ انجام شد.";
                break;
            case 'send_up_receipt_check':
                $usd = $data;
                $t = "مبلغ وارد شده : $usd تومان\nایا این مبلغ را تایید میکنید؟";
                break;
            case 'receipt_ok':
                $t = "👆🏻 پرداخت با کد پیگیری {$data} با موفقیت تأیید شد.";
                break;

            case 'receipt_nok':
                $t = "👆🏻 پرداخت با کد پیگیری {$data} رد شد.";
                break;
            case 'checked_transaction':
                $t = "❌ تراکنش قبلا بررسی شده است.";
                break;
            case 'dot_text':
                $t = ".";
                break;
            default:
                $t = 'انتخاب کنید ❌';
                break;
        }
        return $t;
    }
}
