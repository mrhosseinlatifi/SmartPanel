<?php
trait user_text
{
    public function text($tx, $data = null)
    {
        global $idbot;
        switch ($tx) {
            case 'start':
                $start_text = $data;
                $t = $start_text;
                break;
            case 'back':
                $start_text = $data;
                $t = $start_text;
                break;
            case 'text_order':
                $text_order = $data;
                $t = $text_order;
                break;
            case 'error_text_key':
                $t = "❌ دستور شما ناشناخته است، لطفا از منوی ربات استفاده کنید.";
                break;
            case 'spam':
                $time_block_spam = $data;
                $t = "🚫 به دلیل اسپم به مدت $time_block_spam ثانیه از ربات مسدود شدید.
📛 لطفاً از تکرار این رفتار خودداری کنید.";
                break;
            case 'off_all':
                $t = "❌ این بخش تا اطلاع ثانوی غیرفعال میباشد.";
                break;
            case 'lock_channel':
                $channels = $data;
                if (is_array($channels)) {
                    $list = '';
                    foreach ($channels as $ch) {
                        if ($ch) {
                            $list .= "\n@{$ch}";
                        }
                    }
                    $t = "👈 برای استفاده از ربات ابتدا باید عضو کانال های اطلاع رسانی ما شوید:" . $list . "\nپس از عضویت روی « ✅ عضو شدم » کلیک کنید.";
                } else {
                    $t = "👈 برای استفاده از ربات ابتدا باید عضو کانال اطلاع رسانی ما شوید، \n@{$channels}\n پس از عضویت در کانال روی دکمه « ✅ عضو شدم » کلیک کنید.";
                }
                break;
            case 'ok_support':
                $t = '✅ پیام شما با موفقیت برای پشتیبانی ارسال شد، لطفا تا پاسخ پیام خود از ارسال پیام مجدد خودداری کنید.

• پیام شما در اسرع وقت پاسخ داده میشود.';
                break;
            case 'send_msg':
                $t = '📌 درخواست خود را با رعایت قوانین و با احترام در قالب یک پیام ارسال کنید.';
                break;
            case 'reason_support':
                $t = "👇 لطفا موضوع تیکت خود را انتخاب کنید :";
                break;
            case 'pending_support':
                $t = "❌ خطا

👈 شما یک تیکت بدون پاسخ دارید لطفاً تا دریافت پاسخ تیکت قبلی صبور باشید، شما تا قبل پاسخ پیام قبلی خود قادر به ارسال پیام مجدد نیستید.

🌹 با سپاس از صبر و شکیبایی شما";
                break;
            case 'comment_request':
                $t = "💬 لطفا کامنت‌های دلخواه خود را وارد کنید:

📝 هر خط یک کامنت جداگانه محسوب می‌شود
🔢 تعداد کامنت‌ها = تعداد سفارش شما
🔸 مثال:
کامنت اول
کامنت دوم
کامنت سوم

✅ این کامنت‌ها همراه با سفارش شما ثبت خواهند شد.";
                break;
            case 'comment_min_error':
                $t = "❌ خطا: تعداد کامنت‌ها کمتر از حداقل مجاز است!

🔢 حداقل تعداد کامنت مورد نیاز: " . $data['1'] . "
💡 لطفاً حداقل " . $data['1'] . " کامنت وارد کنید.";
                break;
            case 'comment_max_error':
                $t = "❌ خطا: تعداد کامنت‌ها بیشتر از حداکثر مجاز است!

🔢 حداکثر تعداد کامنت مجاز: " . $data['1'] . "
💡 لطفاً حداکثر " . $data['1'] . " کامنت وارد کنید.";
                break;
            case 'use_bot_keyboards':
                $t = '❌ لطفا از دکمه های ربات استفاده کنید.';
                break;
            case 'support':
                $t = "📌 درخواست خود را با رعایت قوانین و با احترام در قالب یک پیام ارسال کنید.
• در صورتی که در سفارش خود مشکلی دارید حتما کد پیگیری سفارش را ارسال کنید.";
                break;
            case 'off_support':
                $t = "❌ پشتیبانی غیرفعال میباشد.";
                break;
            case 'info':
                $first_name = $data[1];
                $last_name = $data[2];
                $user_name = $data[3];
                $user = $data[4];
                $fid = $user['user_id'];
                $balance = $user['balance'];
                $number_order = $user['number_order'];
                $amount_paid = $user['amount_paid'];
                $amount_spent = $user['amount_spent'];
                $discount = $user['discount'];
                $discount_tx = null;
                if ($discount != 0) {
                    $discount_tx = "\n🎁 میزان تخفیف برای شما : $discount %\n";
                }
                $join_date = jdate('Y/m/d', $user['join_date']);

                if ($data[0] == 1) {
                    // defult no gift

                    $t = "📝 نام اکانت شما : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
🔢 آیدی عددی شما : <code>{$fid}</code>

🛒 تعداد سفارشات : <code>{$number_order}</code>
💸 مجموع پرداخت شما : <code>{$amount_paid}</code>
🛍 مجموع خرید شما : <code>{$amount_spent}</code>
$discount_tx
💰 موجودی شما : <code>{$balance}</code> تومان

⏰ تاریخ عضویت : <code>{$join_date}</code>

🤖 | @{$idbot}";
                } elseif ($data[0] == 2) {
                    // comission gift refrral
                    $referral_id = $user['referral_id'];
                    $referral = $user['referral'];
                    $gift = $user['gift'];
                    $gift_referral = $user['gift_referral'];
                    $gift_payment = $user['gift_payment'];
                    $t = "📝 نام اکانت شما : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
🔢 آیدی عددی شما : <code>$fid</code>

🛒 تعداد سفارشات : <code>{$number_order}</code>
💸 مجموع پرداخت شما : <code>{$amount_paid}</code>
🛍 مجموع خرید شما : <code>{$amount_spent}</code>

💰 درآمد شما : <code>{$gift}</code>
🫂 تعداد زیر مجموعه ها :  <code>{$referral}</code>
🎊 هدیه زیر مجموعه گیری : <code>{$gift_referral}</code>
🤑 پورسانت خرید زیرمجموعه ها : <code>{$gift_payment}</code>
$discount_tx
💰 موجودی شما : <code>{$balance}</code> تومان

⏰ تاریخ عضویت : <code>{$join_date}</code>

🤖 | @{$idbot}";
                } elseif ($data[0] == 3) {
                    // comission
                    $referral_id = $user['referral_id'];
                    $referral = $user['referral'];
                    $gift = $user['gift'];
                    $gift_payment = $user['gift_payment'];
                    $t = "📝 نام اکانت شما : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
🔢 آیدی عددی شما : <code>$fid</code>

🛒 تعداد سفارشات : <code>{$number_order}</code>
💸 مجموع پرداخت شما : <code>{$amount_paid}</code>
🛍 مجموع خرید شما : <code>{$amount_spent}</code>

💰 درآمد شما : <code>{$gift}</code>
🫂 تعداد زیر مجموعه ها :  <code>{$referral}</code>
🤑 پورسانت خرید زیرمجموعه ها : <code>{$gift_payment}</code>
$discount_tx

💰 موجودی شما : <code>{$balance}</code> تومان

⏰ تاریخ عضویت : <code>{$join_date}</code>

🤖 | @{$idbot}";
                } elseif ($data[0] == 4) {
                    // gift
                    $referral_id = $user['referral_id'];
                    $referral = $user['referral'];
                    $gift = $user['gift'];
                    $gift_referral = $user['gift_referral'];
                    $t = "📝 نام اکانت شما : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
🔢 آیدی عددی شما : <code>$fid</code>

🛒 تعداد سفارشات : <code>{$number_order}</code>
💸 مجموع پرداخت شما : <code>{$amount_paid}</code>
🛍 مجموع خرید شما : <code>{$amount_spent}</code>

💰 درآمد شما : <code>{$gift}</code>
🫂 تعداد زیر مجموعه ها :  <code>{$referral}</code>
🎊 هدیه زیر مجموعه گیری : <code>{$gift_referral}</code>
$discount_tx

💰 موجودی شما : <code>{$balance}</code> تومان

⏰ تاریخ عضویت : <code>{$join_date}</code>

🤖 | @{$idbot}";
                }
                break;
            case 'status':
                $t = "✅ لطفا انتخاب کنید :";
                break;
            case 'last_order':
                $t = "🔰 سفارشات شما :\n$data";
                break;
            case 'last_order_row':
                $en_status = [
                    'pending',
                    'processing',
                    'in progress',
                    'completed',
                    'canceled',
                    'partial',
                    'confirm',
                ];
                $fa_status = [
                    '〰️ در صف انتظار',
                    '🔘 درحال پردازش',
                    '♻️ درحال انجام',
                    '✅ تکمیل شده',
                    '❌ لغو شده',
                    '💢 ناتمام',
                    '💤 در انتظار تایید'
                ];

                $order = $data;
                $status = $order['status'];
                $status_fa = str_replace($en_status, $fa_status, $status);
                $code = $order['id'];
                $code_api = $order['code_api'];
                $link = $order['link'];
                $count = $order['count'];
                $price = $order['price'];
                $date = jdate('Y/m/d - H:i', $order['date']);
                $order_decode = json_decode($order['product'], true);
                $sefaresh = implode("\n", $order_decode['category']) . "\n" . $order_decode['product'];

                $t = "❔ نوع سفارش : \n{$sefaresh}\n🔰 وضعیت سفارش : {$status_fa} | 🔍 کد پیگیری : {$code}\n📍 اطلاعات سفارش : {$link} | 💠 تعداد : {$count} |💰 هزینه سفارش : $price تومان\n{$date}\n-------\n";

                break;
            case 'not_order':
                $t = "❌ شما هیچ سفارشی ثبت نکرده اید.";
                break;
            case 'last_payment':
                $t = "💸 پرداخت های شما :\n$data";
                break;
            case 'last_payment_row':
                $payment = $data;
                $amount = number_format($payment['amount']);
                $date = jdate('Y/m/d - H:i', $payment['date']);
                $code = $payment['id'];
                switch ($payment['status']) {
                    case '1':
                        // موفق
                        $stp = '✅ موفق';
                        $tracking_code = $payment['tracking_code'];
                        $t = "$stp | 💳 مبلغ : {$amount} تومان\nشماره 💎 پیگیری درگاه : {$tracking_code} | ⁉️ شماره 🔍 پیگیری ربات : {$code}\n{$date}\n-------\n";
                        break;
                    case '2':
                        // پرداخت نشده است
                        $stp = '⭕️ فاکتور پرداخت نشده';
                        $t = "$stp | 💳 مبلغ : {$amount} تومان\n🔍 شماره پیگیری ربات : {$code}\n{$date}\n-------\n";
                        break;
                    case '3':
                        // پرداخت نشده است
                        $stp = '⭕️ فاکتور پرداخت نشده';
                        $t = "$stp | 💳 مبلغ : {$amount} تومان\n🔍 شماره پیگیری ربات : {$code}\n{$date}\n-------\n";
                        break;
                    case '0':
                        // ناموفق
                        $stp = '❌ ناموفق';
                        $t = "$stp | 💳 مبلغ : {$amount} تومان\n🔍 شماره پیگیری ربات : {$code}\n{$date}\n-------\n";
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'not_payment':
                $t = "❌ شما پرداختی انجام نداده اید.";
                break;
            case 'order_code':
                $t = "⁉️🔍 کد پیگیری را ارسال کنید :";
                break;
            case 'info_order_status':
                $en_status = [
                    'pending',
                    'processing',
                    'in progress',
                    'completed',
                    'canceled',
                    'partial',
                    'confirm',
                ];
                $fa_status = [
                    '〰️ در صف انتظار',
                    '🔘 درحال پردازش',
                    '♻️ درحال انجام',
                    '✅ تکمیل شده',
                    '❌ لغو شده',
                    '💢 ناتمام',
                    '💤 در انتظار تایید'
                ];

                $order = $data;
                $status = $order['status'];
                $status_fa = str_replace($en_status, $fa_status, $status);
                $code = $order['id'];
                $code_api = $order['code_api'];
                $link = $order['link'];
                $count = $order['count'];
                $price = $order['price'];
                $date = jdate('Y/m/d - H:i', $order['date']);
                $order_decode = json_decode($order['product'], true);
                $sefaresh = implode("\n", $order_decode['category']) . "\n" . $order_decode['product'];

                $t = "❔ نوع سفارش : \n{$sefaresh}\n🔰 وضعیت سفارش : {$status_fa} | 🔍 کد پیگیری : {$code}\n📍 اطلاعات سفارش : {$link} | 💠 تعداد : {$count} |💰 هزینه سفارش : $price تومان\n{$date}\n-------\n";

                break;
            case 'wrong_code':
                $t = "❌ کد پیگیری اشتباه میباشد !";
                break;
            case 'ban':
                $t = "❌ اکانت شما مسدود میباشد.";
                break;
            case 'fyk':
                $t = "❗️ این دکمه نمایشی است ❗️";
                break;
            case 'close':
                $t = "❌ پنل مورد نظر بسته شد.";
                break;
            case 'not_join':
                $t = "❌ شما هنوز عضو کانال نشدید.";
                break;
            case 'joined':
                $t = "✅ عضویت شما با موفقیت تایید شد.";
                break;
            case 'payment_authentication':
                $t = "🔒 در راستای افزایش امنیت در پرداخت ، شماره  خود را با استفاده از دکمه زیر برای ما ارسال نمایید.

❗️ ☎️ شماره شما نزد ما محفوظ است و امکان دسترسی به آن برای کسی مقدور نیست.";
                break;
            case 'wait_verify_card':
                $t = "❌ شما یک درخواست برای تایید کارت ارسال کرده اید، لطفا تا تعیین وضعیت آن صبر کنید.";
                break;
            case 'payment_selection':
                $t = "⁉️ لطفا روش پرداخت خود را انتخاب کنید :";
                break;
            case 'off_payment':
                $t = "❌ بخش پرداخت موقتا غیرفعال میباشد.";
                break;
            case 'payment_amount':
                $min_deposit = number_format($data[0]);
                $max_deposit = number_format($data[1]);
                $t = "💸 مبلغ مورد نظر خود را بین $min_deposit تومان تا $max_deposit تومان وارد کنید.";
                break;
            case 'link_payment':
                $t = "✅ لینک های پرداخت";
                break;
            case 'payment_key':
                $amount = $data[0];
                $payment = $data[1];
                $t = "$amount تومان | $payment";
                break;
            case 'payment_text':
                $user = $data[0];
                $fid = $user['user_id'];
                $balance = $user['balance'];
                $code = $data[1];
                $amount = $data[2];
                if (isset($data[3])) {
                    $gift_code = $data[3];
                    $gift_code_amount = $data[4];
                    $gift_code_max = $data[5];
                    $t = "🔗 لینک پرداخت شما با موفقیت ایجاد شد !

♻️ پس از پرداخت وجه، حساب شما خودکار شارژ خواهد شد.

💾 شناسه : {$fid}
💰 موجودی : {$balance} تومان
🔎 شماره پیگیری : {$code}
🎁 کد تخفیف ثبت شده : $gift_code
🎁 درصد کد : $gift_code_amount %
🎁 حداکثر هدیه قابل دریافت : $gift_code_max

👇لطفا بر روی دکمه زیر کلیک کنید👇";
                } else {
                    $t = "🔗 لینک پرداخت شما با موفقیت ایجاد شد !

♻️ پس از پرداخت وجه، حساب شما خودکار شارژ خواهد شد.

💾 شناسه : {$fid}
💰 موجودی : {$balance} تومان
🔎 شماره پیگیری : {$code}

👇لطفا بر روی دکمه زیر کلیک کنید👇";
                }
                break;
            case 'payment_int':
                $t = "❌ مبلغ وارد شده صحیح نمیباشد، لطفا مبلغ درخواستی خود را با اعداد انگلیسی و بر حسب تومان ارسال کنید.";
                break;
            case 'gift_code':
                $t = "کد تخفیف خود را ارسال کنید";
                break;
            case 'wrong_gift_code':
                $t = "❌ کد تخفیف ارسالی نامعتبر میباشد.";
                break;
            case 'off_allpayment':
                $t =  "❌ پرداخت آنلاین غیرفعال میباشد.";
                break;
            case 'payment_mistake':
                $t = "❌ مشکلی پیش آمد، لطفا 5 دقیقه دیگر مجددا تلاش فرمایید.";
                break;
            case 'daily_limit_exceeded':
                $limit = $data['0'];
                $daily_total = $data['1'];
                $remaining = $data['2'];
                $verifyShow = $data['3'];
                if($verifyShow){
                    $verifyShow = "👇 دکمه احراز هویت را فشار دهید تا مراحل احراز هویت را آغاز کنید.";
                }else{
                    $verifyShow = "";
                }
                $t = "🚫 محدودیت تراکنش روزانه
                
❌ شما به حد مجاز تراکنش روزانه رسیده‌اید!

📊 اطلاعات تراکنش:
💰 حد مجاز روزانه: {$limit} تومان
💳 مجموع تراکنش‌های امروز: {$daily_total} تومان
🔢 باقی‌مانده: {$remaining} تومان

💡 برای افزایش محدودیت، حساب خود را احراز هویت کنید.
🔐 کاربران احراز هویت شده محدودیت ندارند.

{$verifyShow}

⏰ محدودیت فردا بازنشانی می‌شود.";
                break;
            case 'verify_disabled':
                $t = "❌ احراز هویت در حال حاضر غیرفعال است.
                
لطفاً با پشتیبانی تماس بگیرید.";
                break;
            case 'daily_limit_exceeded_help':
                $t = "❗️ لطفاً از دکمه‌های موجود استفاده کنید:";
                break;
            case 'payment_authentication_wrong_code':
                $t = "❌ کد ارسالی شما صحیح نمیباشد.";
                break;
            case 'payment_authentication_int':
                $t = "❌ لطفاً فقط عدد وارد کنید.";
                break;
            case 'payment_authentication_send_sms':
                $t = "📲 یک پیامک حاوی کد فعالسازی به شماره شما فرستاده شد، جهت فعالسازی لطفا کد را وارد کنید :";
                break;
            case 'payment_authentication_send_sms_again':
                $t = "❌ لطفا شماره خود را دوباره وارد کنید.";
                break;
            case 'payment_authentication_just_key':
                $t = "👇 لطفاً فقط با استفاده از دکمه زیر شماره خود را تایید کنید.";
                break;
            case 'payment_authentication_wrong_number':
                $t = "❌ شماره ارسالی با اکانت شما تطابق ندارد.";
                break;
            case 'payment_authentication_not_iran':
                $t = "👈🏻🇮🇷 این ربات تنها برای شماره های ایران فعال میباشد!

🔴 شماره شما مورد قبول نیست، لطفا فقط با شماره ایرانی(+98) وارد شوید.";
                break;
            case 'is_number_db':
                $number = $data;
                $t = "✔️ با شماره ارسالی شما کاربری از قبل ثبت نام کرده است.";
                break;
            case 'error':
                $t = "🚫 لینک پرداخت شما ساخته نشد !";
                break;
            case 'error_req':
                $t = "❌ درخواست شما نامعتبر است !";
                break;
            case 'iran_ip':
                $t =  "‼️ لطفا ابتدا فیلترشکن خود را خاموش و سپس اقدام به پرداخت کنید.";
                break;
            case 'time_payment_end':
                $t = "❌ زمان مجاز برای پرداخت به اتمام رسیده است.";
                break;
            case 'off_dargah':
                $t = "🛑 این درگاه غیرفعال میباشد.";
                break;
            case 'link_out':
                $t = "❌ لینک تراکنش فاقد اعتبار است، لطفاً مجدداً تلاش فرمایید.";
                break;
            case 'desc_payment':
                $fid = $data[0];
                $name = $data[1];
                $payment_file = $data[2];
                if ($payment_file == 'nowpayments') {
                    $t = "Charge Balance : $name | $fid";
                } else {
                    $t = "افزاش موجودی کاربر : $name | $fid";
                }
                break;
            case 'desc_starz':
                $amount = $data;
                $t = "💠 مقدار شارژ حساب : $amount";
                break;

            case 'ok_payment':
                $pay = $data[0];
                $Amount = $data[1];
                $balanceold = $data[2];
                $balancenew = $balanceold + $Amount;
                $payment = $data[3];
                $channel = $data[4];
                $t = "✅ پرداخت شما موفقیت آمیز بود !

🔍 شماره پیگیری درگاه : <code>$pay</code>
💸 مبلغ پرداختی : $Amount
💰 موجودی قبلی : $balanceold تومان
💰 موجودی جدید : $balancenew تومان

با تشکر از اعتماد شما 🌹
📣 | @$channel";
                break;
            case 'cancel_payment':
                $t = "❌ تراکنش شما لغو شد !

👈 دلایل احتمالی :

1⃣. شما از پرداخت منصرف شده اید.
2⃣.  تراکنش شما با کارت ثبت نشده انجام شده است.";
                break;
            case 'payment_pending':
                $t = "⏳ تراکنش شما در دست بررسی است.

پرداخت شما هنوز توسط درگاه تایید نهایی نشده، لطفاً چند دقیقه دیگر دوباره تلاش کنید. در صورت تایید، موجودی شما به‌صورت خودکار افزایش می‌یابد.";
                break;
            case 'not_pay_payment':
                $t = "❌ تراکنش انجام نشد !";
                break;
            case 'not_pay_payment_card':
                $t = "📛 تراکنش شما با کارت احراز شده انجام نشده است، جهت اطلاعات بیشتر به پشتیبانی پیام دهید.";
                break;
            case 'error_payment':
                $t = "❌ خطایی رخ داده است.";
                break;
            case 'verify_msg':
                $t = $data;
                break;
            case 'just_card':
                $card = $data;
                $t = "🔰 دوست عزیز برای پرداخت حتما از کارت ثبت شده واریز کنید در غیر اینصورت پرداخت شما تکمیل نخواهد شد\n💳 شماره کارت مجاز :$card";
                break;
            case 'send_verify_ok':
                $t = '✅ درخواست شما ارسال شد.

👈 نتیجه درخواست شما به زودی اعلام میشود.';
                break;
            case 'vetify_just_photo':
                $t = "❌ لطفاً فقط عکس ارسال کنید.";
                break;
            case 'verify_just_video':
                $t = "❌ لطفاً فقط ویدیو ارسال کنید.";
                break;
            case 'no_verify':
                $t = '❌ اطلاعات ارسال شده توسط شما مورد پذیرش نیست.';
                break;
            case 'ok_verify':
                $card = $data;
                $t = "✅ کارت شما به شماره $card ثبت شد !\n‼️ لطفا فقط از این کارت برای پرداخت استفاده نمایید.";
                break;
            case 'baner_tx':
                $baner_tx = $data[0];
                $fid = $data[1];
                $t = $baner_tx . "\nhttps://t.me/$idbot?start=$fid";
                break;
            case 'referral_text':
                if ($data[0] == 1) {
                    $gift_refrral = $data[1];
                    $gift_payment = $data[2];
                    $t = "👆🏻 بنر بالا حاوی لینک دعوت شما به ربات است !

🎁 با دعوت دوستان خود به ربات با لینک اختصاصی خود میتوانید به ازای هر نفر {$gift_refrral} تومان فورا دریافت کنید و {$gift_payment} درصد از مبلغ هر خرید زیرمجموعه خود را به طور دائم داشته باشید.

☑️ پس با زیرمجموعه گیری به راحتی میتوانید موجودی حساب خود را « کامــلا رایگان » افزایش دهید";
                } elseif ($data[0] == 2) {

                    $gift_refrral = $data[1];
                    $t = "🔺 بنر بالا حاوی لینک دعوت اختصاصی شما برای وروده به ربات است با اشتراک بنر برای گروه ها و دوستانتان موجودی جمع کنید.

◾️ با زیرمجموعه گیری به ازای هر دوست خود که دعوت میکنید {$gift_refrral} تومان هدیه میگیرد.";
                } else {

                    $gift_payment = $data[1];
                    $t = "🔺 بنر بالا حاوی لینک دعوت اختصاصی شما برای وروده به ربات است با اشتراک بنر برای گروه ها و دوستانتان موجودی جمع کنید.

◾️  {$gift_payment} درصد از هر شارژ حساب زیرمجموعه تان به شما تعلق میگیرد.";
                }
                break;

            case 'gift_start':
                $gift = $data;
                $t = "😍 کاربر عزیز، به عنوان هدیه اولین ورود شما به ربات مبلغ $gift تومان به عنوان هدیه دریافت کردید !";
                break;
            case 'gift':
                if ($data[0] == 1) {
                    $Porsant    = $data[1];
                    $freebalance = $data[2];
                    $t = "🥳 تبریک !

👈 یک کاربر با استفاده از لینک دعوت شما وارد ربات شد و $freebalance تومان به موجودی شما افزوده شد.\n◾️🤑 همچنین $Porsant درصد از هر خرید زیرمجموعه شما به حسابتان تعلق میگیرد.";
                } elseif ($data[0] == 2) {
                    $Porsant    = $data[1];
                    $t = "🌟 تبریک ! یک کاربر با استفاده از لینک دعوت شما وارد ربات شده است.\nبا هر شارژ حساب کاربر برای شما $Porsant درصد از مبلغ شارژ حساب به شما تعلق میگیرد.";
                } else {
                    $freebalance = $data[1];
                    $t = "🌟 تبریک ! یک کاربر با استفاده از لینک دعوت شما وارد ربات شده و $freebalance تومان به موجودی شما افزوده شد.";
                }
                break;
            case 'ok_commission':
                $gift_payment = $data[0];
                $t = "🤩 یک کاربر با کد دعوت شما وارد ربات شده است، درصورت تایید شماره به ازای هر مقدار شارژ $gift_payment درصد آن به شما تعق میگیرد.";
                break;
            case 'referral_authentication':
                $t = "🔐 برای افزایش امنیت ربات و جلوگیری از زیرمجموعه گیری فیک فقط شماره های ایرانی قادر به ثبت نام در ربات میباشند.";
                break;
            case 'is_member':
                $t = "📌 شما از قبل عضو ربات بودید و نمیتوانید با لینک دعوت کسی وارد ربات شوید.";
                break;
            case 'not_new_id':
                $referral_id = $data;
                $t = "❌ کاربری با ایدی $referral_id یافت نشد.";
                break;
            case 'self_referral':
                $t = "😐 شما نمیتوانید زیرمجموعه خودتان شوید:/";
                break;
            case 'wrong_new_id':
                $t = "❌ آیدی معرف اشتباه است !";
                break;
            case 'referral_authentication_sms':
                $t = "📲 یک پیامک حاوی کد فعالسازی به شماره شما فرستاده شد، جهت فعالسازی لطفا کد را وارد کنید :";
                break;
            case 'again_referral_authentication':
                $t = "❌ لطفا شماره خود را دوباره وارد کنید.";
                break;
            case 'delay_time_sms':
                $time = $data - time();
                $t = "✅ یک پیام حاوی کد فعالسازی برای شما پیامک شده است.
👈 لطفا کد را وارد کنید.

❌ درصورتی که پیام برای شما ارسال نشده است لطفا $time ثانیه بعد تلاش کنید.";
                break;
            case 'not_iran_contact':
                $t = "👈🏻🇮🇷 این ربات تنها برای شماره های ایران فعال میباشد!

🔴 شماره شما مورد قبول نیست، لطفا فقط با شماره ایرانی(+98) وارد شوید.";
                break;
            case 'number_is_db':
                $t = "✔️ با شماره ارسالی شما کاربری از قبل ثبت نام کرده است.";
                break;
            case 'only_key_use_contact':
                $t = "👇 لطفاً فقط با استفاده از دکمه زیر شماره خود را تایید کنید.";
                break;
            case 'ok_number':
                $contactuser = $data[0];
                $fid = $data[1];
                $first_name = $data[2];
                $last_name = $data[3];
                $user = $data[4];
                $referral_id = $data[5];
                $time = jdate('Y/m/d - H:i');
                $t = "شماره : <code>$contactuser</code>
ایدی : <code>$fid</code>
اسم : <a href ='tg://user?id=$fid'>$first_name</a>
معرف : <a href ='tg://user?id=$referral_id'>$referral_id</a>
تاریخ ثبت نام : $time";
                break;
            case 'wrong_code_referral_authentication':
                $t = "❌ کد وارد شده صحیح نیست !";
                break;
            case 'int_code_referral_authentication':
                $t = "❌ لطفا فقط اعداد انگلیسی ارسال کنید.";
                break;
            case 'off_referral':
                $t = "❌ این بخش غیرفعال میباشد.";
                break;
            case 'info_referral':
                if ($data[0] == 1) {
                    $first_name = $data[1];
                    $last_name = $data[2];
                    $user_name = $data[3];
                    $user = $data[4];
                    $fid = $user['user_id'];
                    $gift = $user['gift'];
                    $referral = $user['referral'];
                    $gift_referral = $user['gift_referral'];
                    $gift_payment = $user['gift_payment'];
                    $gift_spent = ($user['gift_payment'] + $user['gift_referral']) - $user['gift'];
                    $t = "👤 نام : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
👤 شناسه : <code>{$fid}</code>

💰 درآمد شما : <code>{$gift}</code>
🫂 تعداد زیر مجموعه ها :  <code>{$referral}</code>
🎊 هدیه زیر مجموعه گیری : <code>{$gift_referral}</code>
💢 هدیه مصرف شده : <code>$gift_spent</code>
🤑 پورسانت خرید زیرمجموعه ها : <code>{$gift_payment}</code>

🤖 | @{$idbot}";
                } elseif ($data[0] == 2) {
                    $first_name = $data[1];
                    $last_name = $data[2];
                    $user_name = $data[3];
                    $user = $data[4];
                    $fid = $user['user_id'];
                    $gift = $user['gift'];
                    $referral = $user['referral'];
                    $gift_referral = $user['gift_referral'];
                    $gift_spent = ($user['gift_payment'] + $user['gift_referral']) - $user['gift'];
                    $t = "👤 نام : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
👤 شناسه : <code>{$fid}</code>

💰 درآمد شما : <code>{$gift}</code>
💰 هدیه مصرف شده : <code>$gift_spent</code>
💎 تعداد زیر مجموعه :  <code>{$referral}</code>
🛍 هدیه زیر مجموعه گیری : <code>{$gift_referral}</code>

@$idbot";
                } else {
                    $first_name = $data[1];
                    $last_name = $data[2];
                    $user_name = $data[3];
                    $user = $data[4];
                    $fid = $user['user_id'];
                    $gift = $user['gift'];
                    $referral = $user['referral'];
                    $gift_referral = $user['gift_referral'];
                    $gift_payment = $user['gift_payment'];
                    $gift_spent = ($user['gift_payment'] + $user['gift_referral']) - $user['gift'];
                    $t = "👤 نام : <a href ='tg://user?id={$fid}'>$first_name $last_name</a>
👤 شناسه : <code>{$fid}</code>

💰 درآمد شما : <code>{$gift}</code>
💰 هدیه مصرف شده : <code>$gift_spent</code>
💎 تعداد زیر مجموعه :  <code>{$referral}</code>
🛍 پورسانت خرید زیرمجموعه : <code>{$gift_payment}</code>

@$idbot";
                }
                break;
            case 'amount_gift_balance':
                $min = $data[0];
                $gift_balance = $data[1];
                $t = "✅ مبلغ مورد نظر خود را از $min تومان الی {$gift_balance} تومان وارد کنید.";
                break;
            case 'min_gift_balance':
                $min = $data;
                $t = "❌ کمترین حد تبدیل $min تومان است.";
                break;
            case 'ok_gift_balance':
                $amount = $data;
                $t = "✅ مبلغ {$amount} تومان به کیف پول شما انتقال داده شد.";
                break;
            case 'amount_gift_balance_wrong':
                $min = number_format($data[0]);
                $gift_balance = number_format($data[1]);
                $t = "❌ لطفا عددی از بازه $min تومان الی {$gift_balance} تومان وارد کنید.";
                break;
            case 'gift_balance_int':
                $t = "❌ لطفا مقدار مورد نظر خود را فقط به عدد وارد کنید.";
                break;
            case 'amount_withdraw_balance':
                $min = number_format($data[0]);
                $gift_balance = number_format($data[1]);
                $t = "❌ لطفا عددی از بازه $min تومان الی {$gift_balance} تومان وارد کنید.";
                break;
            case 'min_withdraw_balance':
                $min = number_format($data);
                $t = "❌ کمترین حد برداشت $min تومان است !";
                break;
            case 'amount_withdraw_balance_wrong':
                $min = number_format($data[0]);
                $gift_balance = number_format($data[1]);
                $t = "❌ لطفا عددی از بازه $min تومان الی {$gift_balance} تومان وارد کنید.";
                break;
            case 'info_withdraw_balance':
                $t = "✅ لطفا اطلاعات کارت بانکی خود را با فرمت زیر بفرستید :
• شماره کارت - نام صاحب کارت - نام بانک";
                break;
            case 'ok_withdraw_balance':
                $code = $data;
                $t = "✅ درخواست شما ثبت شد !\n\n• کد پیگیری : $code";
                break;
            case 'error_withdraw_balance':
                $t = "با عرض پوزش مشکلی پیش آمده است مجددا تلاش کنید";
                break;
            case 'refral_gift_payment':
                $fid = $data[0];
                $name = $data[1];
                $Amount = $data[2];
                $gifi = $data[3];
                $t = "🥳 تبریک !
👥 زیر مجموعه شما <a href = 'tg://user?id=$fid'>$name</a> مقدار $Amount تومان حساب خود را شارژ کرد و مقدار $gifi پورسانت برای شما واریز شد";
                break;
            case 'price_info':
                $t = "👈 لطفا دسته بندی خود مورد نظر خود را انتخاب کنید.

💵 قیمت ها بر حسب 1000 عدد و به تومان میباشد.";
                break;
            case 'cancel_order':
                $t = "❌ سفارش لغو شد.";
                break;
            case 'off_buy':
                $t = "❌ بخش خرید موقتا غیرفعال میباشد.";
                break;
            case 'shop1':
                $t = "👈 دسته بندی مورد نظر خود را انتخاب کنید.";
                break;
            case 'shop2':
                $category = json_decode($data);
                $t = "👈 دسته انتخابی : $category\n👈 یک گزینه انتخاب کنید :";;
                break;
            case 'shop3':
                $category = json_decode($data);
                $t = "👈 دسته انتخابی : $category\n👈 یک گزینه انتخاب کنید :";;
                break;
            case 'shop_justkey':
                $t = "❌ لطفا از دکمه های ربات استفاده کنید.";
                break;

            case 'shop4':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $ca = getCategoryHierarchy($product['category_id']);
                $price = number_format($data[1]);
                $price_once = number_format($data[2]);
                $how_much = number_format($data[3]);
                $min = number_format($product['min']);
                $max = number_format($product['max']);
                $info = $product['info'];
                if ($info != null) {
                    $info = "\n📝 توضیحات : \n $info";
                } else {
                    $info = null;
                }
                
                $pone = ($product['type'] == 'package') ? '(به ازای 1 عدد)' : '(به ازای 1000 عدد)';

                $t = "✅ سرویس انتخابی شما : {$product_name}
📱 دسته بندی :
{$ca} {$info}
ــ ـ ــ  ــ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ
💸 قیمت $pone : {$price} تومان | قیمت هر عدد : $price_once تومان
⬇️ حداقل : {$min} | ⬆️ حداکثر : {$max}

✔️ شما با موجودی فعلی خود میتوانید $how_much عدد سفارش دهید.

👈 تعداد مورد نظر خود را از بازه {$min} تا {$max} ارسال کنید";

                break;
            case 'shop4_comments':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $ca = getCategoryHierarchy($product['category_id']);
                $price = number_format($data[1]);
                $price_once = number_format($data[2]);
                $min = $data[3];
                $max = $data[4];
                $info = $product['info'];
                if ($info != null) {
                    $info = "\n📝 توضیحات : \n $info";
                } else {
                    $info = null;
                }

                $t = "✅ سرویس انتخابی شما : {$product_name}
📱 دسته بندی :
{$ca} {$info}
ــ ـ ــ  ــ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ
💸 قیمت هر کامنت : {$price_once} تومان
⬇️ حداقل : {$min} کامنت | ⬆️ حداکثر : {$max} کامنت

💬 لطفاً کامنت‌های مورد نظر خود را ارسال کنید:
📝 هر خط یک کامنت جداگانه محسوب می‌شود
🔢 تعداد کامنت‌ها = تعداد سفارش شما";

                break;
            case 'shop4_direct_link':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $ca = getCategoryHierarchy($product['category_id']);
                $price = number_format($data[1]);
                $price_once = number_format($data[2]);
                $link_text = $data[3];
                $info = $product['info'];
                if ($info != null) {
                    $info = "\n📝 توضیحات : \n $info";
                } else {
                    $info = null;
                }

                $t = "✅ سرویس انتخابی شما : {$product_name}
📱 دسته بندی :
{$ca} {$info}
ــ ـ ــ  ــ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ
💸 قیمت : {$price_once} تومان (1 عدد)

👈 {$link_text}";

                break;
            case 'shop4_comments_simple':
                $comment_count = $data[0];
                $price_once = number_format($data[1]);
                $total_price = number_format($data[2]);
                $link_text = $data[3];

                $t = "💬 تعداد کامنت‌ها : {$comment_count} عدد
💸 قیمت هر کامنت : {$price_once} تومان
💰 قیمت کل : {$total_price} تومان

👈 {$link_text}";

                break;
            case 'shop4_comments_link':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $ca = getCategoryHierarchy($product['category_id']);
                $total_price = number_format($data[1]);
                $price_once = number_format($data[2]);
                $comment_count = $data[3];
                $link_text = $data[4];
                $info = $product['info'];
                if ($info != null) {
                    $info = "\n📝 توضیحات : \n $info";
                } else {
                    $info = null;
                }

                $t = "✅ سرویس انتخابی شما : {$product_name}
📱 دسته بندی :
{$ca} {$info}
ــ ـ ــ  ــ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ
💬 تعداد کامنت‌ها : {$comment_count} عدد
💸 قیمت هر کامنت : {$price_once} تومان
💰 قیمت کل : {$total_price} تومان

👈 {$link_text}";

                break;
            case 'price_info_products':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $category_names = getCategoryHierarchy($product['category_id']);
                $price = number_format($data[1]);
                $price_once = number_format($data[2]);
                $how_much = number_format($data[3]);
                $min = $product['min'];
                $max = number_format($product['max']);
                $pone = ($product['type'] == 'package') ? '(به ازای 1 عدد)' : '(به ازای 1000 عدد)';

                $t = "✅ سرویس انتخابی شما : {$product_name}
📱 دسته بندی : \n{$category_names}
‌‌‌‌ـ ـ ــ  ــ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـ ــ ـــ ـ ــ ـ ــ ـ
💸 قیمت $pone : {$price} تومان | قیمت هر عدد : $price_once تومان
⬇️ حداقل : {$min} | ⬆️ حداکثر : {$max}

✔️ شما با موجودی فعلی خود میتوانید $how_much عدد سفارش دهید.";

                break;
            case 'low_balance':
                $balance_required = $data[0];
                $balance = $data[1]['balance'];
                $t = "❌ موجودی حساب شما جهت ثبت سفارش کافی نمیباشد، لطفا ابتدا اقدام به افزایش موجودی کنید

• هزینه لازم برای ثبت این سفارش : $balance_required";
                break;
            case 'shop_range':
                $min = $data[0];
                $max = $data[1];
                $t = "❌ خطا، لطفا عدد ارسالی خود را بین بازه {$min} تا {$max} ارسال کنید.";
                break;
            case 'shop5':
                $product = $data[0];
                $product_name = json_decode($product['name']);
                $count = $data[1];
                $link = $data[2];
                $price = $data[3];
                $comments = isset($data[4]) ? $data[4] : null;
                
                $comments_text = "";
                if (!empty($comments) && is_array($comments)) {
                    $comments_text = "\n💬 کامنت‌های شما:\n";
                    foreach ($comments as $index => $comment) {
                        $comment_number = $index + 1;
                        $comments_text .= "   {$comment_number}. {$comment}\n";
                    }
                }
                
                $t = "✅ اطلاعات زیر را بررسی کرده و درصورت صحیح بودن تایید کنید.

🔰 نوع سفارش : {$product_name}
💠 تعداد : {$count}
🔗 اطلاعات سفارش : {$link}
💸 قیمت فاکتور : {$price} تومان{$comments_text}

👈 درصورت تایید کردن سفارش امکان لغو سفارش وجود ندارد.";
                break;
            case 'shop_just_en':
                $t = "❌ لطفاً فقط از زبان انگلیسی استفاده کنید.";
                break;
            case 'order_receipt':
                $category = $data[0]['name'];
                $under = $data[1]['name'];
                $product = $data[2];
                $sefaresh = $category . ' ' . $under . ' ' . $product;
                $first_name = $data[3];
                $last_name = $data[4];
                $user_name = $data[5];
                $fid = $data[6];
                $count = $data[7];
                $link = $data[8];
                $code = $data[9];
                $price = $data[10];
                $old_balance = $data[11];
                $new_balance = $data[12];
                $date = jdate('Y/m/d - H:i:s');
                if ($data[13] !== 'noapi') {
                    // متن سفارشات وب سرویس
                    $code_api = $data[13];
                    $api = $data[14];
                    $tt = "\nکد پیگیری وب سرویس : $code_api\nوب سرویس : $api\n";
                    $t = "
❇️ سفارش  {$sefaresh}

• تعداد سفارش  : {$count}
• لینک  : {$link}
‌‌‌‌
ـ 👤 : <a href='tg://user?id={$fid}'>{$first_name}</a>| : <code>{$fid}</code>
کد پیگیری ربات : {$code}
$tt
قیمت فاکتور : {$price}
تاریخ ثبت : {$date}";
                } else {
                    $t = "❇️ سفارش  {$sefaresh}

• تعداد سفارش  : {$count}
• لینک : {$link}
‌‌‌
ـ 👤 : <a href='tg://user?id={$fid}'>{$first_name}</a> |  <code>{$fid}</code>
کد پیگیری ربات : {$code}
قیمت فاکتور : {$price}
تاریخ ثبت : {$date}";
                }
                break;

            case 'order_receipt_ok':
                $link = $data[0];
                $count = $data[1];
                $price = $data[2];
                $code = $data[3];
                $product_name = $data[4];
                $date = jdate('Y/m/d - H:i:s');
                $t = "✅ سفارش شما با موفقیت ثبت شد !

🔰 نوع سفارش : {$product_name}
💠 تعداد : {$count}
🔗 اطلاعات سفارش : {$link}
💸 مبلغ سفارش : {$price} تومان
🔍 کد پیگیری شما : {$code}
⏳ زمان ثبت : {$date}

✳️ انجام سفارشات زمان بر میباشد، پس لطفاً تا انجام سفارش صبور باشید.";
                break;
            case 'error_order':
                $t = "❌ با عرض پوزش، مشکلی پیش آمد لطفا مجددا تلاش فرمایید.";
                break;
            case 'not_product':
                $t = "❌ محصول مورد نظر شما موقتا غیرفعال میباشد.";
                break;
            case 'order_go':
                $order_info = $data[0];
                $code = type_text($order_info['id'], 'c');
                $channel = $data[1];
                $time = jdate('H:i:s');
                $date = jdate('Y/m/d');

                $t = "♻️ سفارش شما با کد پیگیری {$code} در تاریخ {$date} و ساعت {$time} تایید شد و به زودی انجام میشود !

👈 لطفا تا زمان انجام سفارش خود صبور باشید.

📣 | @{$channel}
🤖 | @{$idbot}";

                break;
            case 'order_confirmation':
                $order_info = $data[0];
                $code = type_text($order_info['id'], 'c');
                $channel = $data[1];
                $time = jdate('H:i:s');
                $date = jdate('Y/m/d');

                $t = "✅  سفارش شما با کد پیگیری {$code} در تاریخ {$date} و ساعت {$time} با موفقیت تکمیل شد !

📣 | @{$channel}
🤖 | @{$idbot}";

                break;
            case 'order_cancel':
                $order_info = $data[0];
                $code = type_text($order_info['id'], 'c');
                $amount = $order_info['price'];
                $channel = $data[1];
                $time = jdate('H:i:s');
                $date = jdate('Y/m/d');

                $t = "❌  سفارش شما با کد پیگیری {$code} در تاریخ {$date} و ساعت {$time} بررسی شد و بنا به دلایلی لغو شد!

👈 مبلغ {$amount} تومان به حساب شما عودت داده شد.

📣 | @{$channel}
🤖 | @{$idbot}";

                break;
            case 'order_partial':
                $order_info = $data[0];
                $code = type_text($order_info['id'], 'c');
                $back = $data[1];
                $channel = $data[2];
                $time = jdate('H:i:s');
                $date = jdate('Y/m/d');

                $t = "✳️ سفارش شما با کد پیگیری {$code} در تاریخ {$date} و ساعت {$time} بررسی شد و بنا به دلایلی کامل انجام نشد!

👈 مبلغ {$back} تومان بابت باقی مانده سفارش به حساب شما عودت داده شد.

📣 | @{$channel}
🤖 | @{$idbot}.";

                break;
            case 'not_category':
                $t = '❌ محصولی برای نمایش وجود ندارد.';
                break;
            case 'fast_order_int':
                $t = "✅ لطفا برای ثبت سفارش سریع فقط عدد محصول را ارسال کنید.";
                break;
            case 'result_order_inline':
                $status = $data;
                $en_status = [
                    'pending',
                    'processing',
                    'in progress',
                    'completed',
                    'canceled',
                    'partial'
                ];
                $fa_status = [
                    '〰️ در صف انتظار',
                    '🔘 درحال پردازش',
                    '♻️ درحال انجام',
                    '✅ تکمیل شده',
                    '❌ لغو شده',
                    '💢 ناتمام'
                ];

                $status_fa = str_replace($en_status, $fa_status, $status);

                $t = 'وضعیت سفارش : ' . $status_fa;
                break;
            case 'fast_order_help':
                $t = "\n✅ با کلیک بر روی هر محصول میتوانید مشخصات آن محصول را مشاهده کنید.\n• برای سفارش سریع هر محصول میتوانید عدد آن را ارسال کنید.";
                break;
            case 'inline_order_info':
                $price = $data[0];
                $min = $data[1];
                $max = $data[2];
                $id = $data[3];

                $t = "💸 قیمت : $price تومان
⬇️ حداقل : $min | ⬆️ حداکثر : $max ⚡️ سفارش سریع : $id";
                break;
            case 'move_balance':
                $min = $data['0'];
                $balance = $data['1'];
                $t = "💰 در این بخش میتوانید موجودی داخل ربات را برای بقیه کاربران منتقل کنید.\n⚠️ توجه : حتما هردو کاربر باید عضو ربات باشند.\n لطفا ایدی عددی کاربر مقصد را وارد کنید :

💰 حداقل مبلغ برای انتقال : {$min} تومان
💰 موجودی فعلی شما : {$balance}";
                break;
            case 'min_move_balance':
                $min = $data;
                $t = "❌ حداقل موجودی برای انتقال {$min} تومان میباشد.";
                break;
            case 'off_move_balance':
                $t = "❌ این بخش غیرفعال میباشد.";
                break;
            case 'min_charge_code':
                $min = number_format($data);
                $t = "❌ حداقل موجودی برای استفاده از کد شارژ باید {$min} تومان باشد.";
                break;
            case 'off_charge_code':
                $t = "❌ این بخش غیرفعال میباشد.";
                break;
            case 'error_offline_payment':
                $t = "❌ خطایی در ثبت فیش واریزی رخ داد، لطفاً دوباره تلاش کنید.";
                break;
            case 'error_processing':
                $t = "❌ خطایی رخ داد، لطفاً دوباره تلاش کنید.";
                break;
            case 'link_text':
                $t = $data;
                break;
            case 'not_found':
                $t = "❌ درخواست مورد نظر شما یافت نشد !";
                break;
            case 'card_be_card':
                $t = $data;
                break;
            case 'back_pay':
                $t = $data;
                break;
            case 'error_getway':
                $t = "❗️ لطفاً مجدداً از ربات لینک پرداخت دریافت کنید.";
                break;
            case 'move_balance_1':
                $t = "💰 مقدار موجودی برای انتقال را وارد کنید :";
                break;
            case 'move_balance_int':
                $t = "❌ لطفا فقط عدد وارد کنید.";
                break;
            case 'move_balance_not_found':
                $t = "❌ کاربر مورد نظر شما یافت نشد و عضو ربات نمیباشد !";
                break;
            case 'move_balance_2':
                $name = type_text($data['0'], 'm', $data['1']);
                $amount = $data['2'];
                $t = "⁉️ آیا انتقال $amount تومان به کاربر $name را تایید میکنید؟";
                break;
            case 'move_balance_int_2':
                $t = "❌ لطفا فقط عدد ارسال کنید.";
                break;
            case 'move_balance_not_enough':
                $min = $data['0'];
                $max = $data['1'];
                $t = "⭕️ مبلغ انتقال میبایست بین $min تومان الی $max تومان باشد.";
                break;
            case 'ok_move_balance':
                $t = "✅ با موفقیت انجام شد.";
                break;
            case 'receive_balance':
                $k = $data['0'];
                $id = $data['1'];
                $t = "🔰 مقدار $k تومان از طرف کاربر $id برای شما واریز شد.";
                break;
            case 'charge_code_input':
                $t = "🎁 کد شارژ را وارد کنید :";
                break;
            case 'ok_charge_code':
                $amount = $data;
                $t = "💎 کد شارژ به مقدار $amount با موفقیت ثبت شد !";
                break;
            case 'wrong_charge_code':
                $t = "❌ کد ارسالی اشتباه است !";
                break;
            case 'already_charge_code':
                $t = "❌ شما قبلا از این کد استفاده کرده اید.";
                break;
            case 'payment_offline':
                $min = number_format($data[0]);
                $max =  number_format($data[1]);
                $t = "👈 لطفا مبلغ مورد نظر خود را از $min تومان الی $max تومان وارد کنید :";
                break;
            case 'payment_offline_int':
                $t = "❌ لطفا فقط عدد وارد کنید.";
                break;
            case 'amount_deposit_wrong':
                $min = number_format($data[0]);
                $max = number_format($data[1]);
                $t = "❌ لطفا عددی از بازه $min تومان الی {$max} تومان وارد کنید.";
                break;
            case 'send_receipt_photo':
                $amount = number_format($data);
                $t = "💰 مبلغ درخواستی شما $amount تومان میباشد.
📸 لطفا عکس فیش واریزی خود را ارسال کنید :";
                break;
            case 'ok_offline_payment':
                $t = "✅ فیش واریزی شما با موفقیت ارسال شد.
👈 نتیجه درخواست شما به زودی اعلام میشود.
کد پیگیری : $data";
                break;
            case 'crypto_payment_amount':
                $min = number_format($data[0]);
                $max = number_format($data[1]);
                $t = "👈 لطفا مبلغ مورد نظر خود را از $min تومان الی $max تومان وارد کنید :";
                break;
            case 'off_crypto_payment':
                $t = "❌ پرداخت ارز دیجیتال موقتا غیرفعال میباشد.";
                break;
            case 'starz_payment_amount':
                $min = $data[0];
                $max = $data[1];
                $price = number_format($data[2]);
                $t = "⭐️ لطفا تعداد استارز مورد نظر خود را وارد کنید:

📊 محدوده مجاز: $min تا $max استارز
💰 نرخ تبدیل: هر استارز = $price تومان

💡 مثال: اگر $min استارز وارد کنید، " . number_format($min * $data[2]) . " تومان به حساب شما اضافه می‌شود.";
                break;
            case 'starz_payment_help':
                $t = "پس از پرداخت ، حساب شما بصورت خودکار شارژ خواهد شد";
                break;
            case 'starz_amount_wrong':
                $min = $data[0];
                $max = $data[1];
                $t = "❌ تعداد استارز نامعتبر!

📊 لطفاً عددی بین $min تا $max استارز وارد کنید.";
                break;
            case 'starz_payment_ok':
                $starz_amount = $data[0];
                $irt_amount = $data[1];
                $user = $data[2];
                $old_balance = $user['balance'];
                $new_balance = $old_balance + $irt_amount;
                $new_balance = number_format($new_balance);
                $old_balance = number_format($old_balance);
                $date = jdate('Y/m/d - H:i:s');
                $t = "✅ پرداخت شما با موفقیت انجام شد !
💠 مقدار استارز پرداختی : $starz_amount
💰 مبلغ شارژ شده به تومان : $irt_amount
💰 موجودی قبلی شما : $old_balance تومان
💰 موجودی جدید شما : $new_balance تومان
⏳ زمان پرداخت : $date

با تشکر از اعتماد شما 🌹";
                break;
            case 'off_starz_payment':
                $t = "❌ پرداخت با استارز موقتا غیرفعال میباشد.";
                break;
            default:
                $t = '👈 لطفا انتخاب کنید :';
                break;
        }
        return $t;
    }
}
