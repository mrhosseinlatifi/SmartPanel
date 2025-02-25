<?php
$key['admin_answer'] = 'پاسخ';
$key['admin_rad'] = 'رد';
$key['admin_info'] = 'مشخصات کاربر';
$key['admin_ban'] = 'مسدود کردن';
$key['admin_confirm_order'] = 'تایید سفارش';
$key['admin_cancel_order'] = 'لغو سفارش';
$key['admin_pending_order'] = 'در انتظار';
$key['admin_inprogress_order'] = 'در حال انجام';
$key['admin_complete_order'] = 'تکمیل سفارش';
$key['admin_completed_order'] = 'تکمیل شد';
$key['admin_cancel_order'] = 'لغو سفارش';
$key['admin_canceled_order'] = 'لغو شد';
$key['admin_status_order'] = ' وضعیت سفارش';
$key['admin_api_order'] = 'این سفارش به سایت ارسال شد';
$key['admin_ok_card'] = 'تایید کارت';
$key['admin_cancel_card'] = 'رد کارت';
trait admin_user_text
{
    public function akeys($k, $data = null)
    {
        global $key, $idbot;
        switch ($k) {
            case 'admin_support':
                $fid = $data;
                $t = ['inline_keyboard' => [
                    [['text' => $key['admin_answer'], 'callback_data' => "admintiket_answer_" . $fid], ['text' => $key['admin_rad'], 'callback_data' => "admintiket_rad_" . $fid]],
                    [['text' => $key['admin_info'], 'callback_data' => "admintiket_info_" . $fid]],
                ]];
                break;
            case 'fast_support':
                $fid = $data;
                $t = ['inline_keyboard' => [
                    [['text' => $key['fast_support'], 'callback_data' => "support"]]
                ]];
                break;
            case 'gifts_payouts':
                $t = ['inline_keyboard' => [
                    [['text' => 'پرداخت شد', 'callback_data' => 'payout_ok_' . $data], ['text' => 'کنسل کردن', 'callback_data' => 'payout_nok_' . $data]]
                ]];
                break;
            case 'gift_payouts':
                if ($data == 1) {
                    $t = ['inline_keyboard' => [
                        [['text' => 'واریزشد', 'callback_data' => 'fyk']],
                    ]];
                } elseif ($data == 2) {
                    $t = ['inline_keyboard' => [
                        [['text' => 'لغو شد', 'callback_data' => 'fyk']],
                    ]];
                }
                break;
            case 'order_noapi':
                $code = $data['0'];
                switch ($data['1']) {
                    case 'pending':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_pending_order'], 'callback_data' => "adminorderch_go-" . $code], ['text' => $code, 'callback_data' => "fyk"]],
                            [['text' => $code, 'callback_data' => "fyk"], ['text' => $key['admin_cancel_order'], 'callback_data' => "adminorderch_cancel-" . $code]],
                        ]];
                        break;
                    case 'inprogress':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_complete_order'], 'callback_data' => "adminorderch_completed-" . $code], ['text' => $code, 'callback_data' => "fyk"]],
                            [['text' => $code, 'callback_data' => "fyk"], ['text' => $key['admin_cancel_order'], 'callback_data' => "adminorderch_cancel-" . $code]],
                        ]];
                        break;
                    case 'cancel':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_canceled_order'], 'callback_data' => "fyk"], ['text' => $code, 'callback_data' => "fyk"]],
                        ]];
                        break;
                    case 'completed':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_completed_order'], 'callback_data' => "fyk"], ['text' => $code, 'callback_data' => "fyk"]],
                        ]];
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'order_api':
                $code = $data['0'];
                switch ($data['1']) {
                    case 'pending':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_status_order'], 'callback_data' => "adminorderch_check-" . $code], ['text' => $code, 'callback_data' => "fyk"]],
                            [['text' => $code, 'callback_data' => "fyk"], ['text' => $key['admin_cancel_order'], 'callback_data' => "adminorderch_cancel-" . $code]],
                        ]];
                        break;
                    case 'confirm':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_status_order'], 'callback_data' => "adminorderch_check-" . $code], ['text' => $code, 'callback_data' => "fyk"]],
                        ]];
                        break;
                    default:
                        # code...
                        break;
                }
                break;
            case 'order_api_confirm':
                $code = $data['0'];
                switch ($data['1']) {
                    case 'pending':
                        $t = ['inline_keyboard' => [
                            [['text' => $key['admin_confirm_order'], 'callback_data' => "adminorderch_confirm-" . $code], ['text' => $code, 'callback_data' => "fyk"]],
                            [['text' => $code, 'callback_data' => "fyk"], ['text' => $key['admin_cancel_order'], 'callback_data' => "adminorderch_cancel-" . $code]],
                        ]];
                        break;

                    default:
                        # code...
                        break;
                }
                break;
            case 'successful_order':
                $t = ['inline_keyboard' => [
                    [['text' => $key['start_bot'], 'url' => "https://t.me/" . $idbot . "?start"]],
                ]];
                break;
            case 'verify_keys':
                $t = ['inline_keyboard' => [
                    [
                        ['text' =>  $key['admin_ok_card'], 'callback_data' => "verifycard-ok-" . $data],
                        ['text' => $key['admin_cancel_card'], 'callback_data' => "verifycard-nok-" . $data]
                    ],
                    [
                        ['text' => $key['admin_info'], 'callback_data' => "verifycard-info-" . $data]
                    ],
                ]];
                break;
            default:
                $t = ['remove_keyboard' => true];
                break;
        }
        if (isset($t['keyboard'])) {
            $t = array_merge(array_slice($t, 0), ['resize_keyboard' => true], array_slice($t, 0));
            return $t;
        } else {
            return $t;
        }
    }
    public function atext($tx, $data = null)
    {
        global $idbot;
        switch ($tx) {
            case 'admin_support':
                $fid = $data[0];
                $first_name = $data[1];
                $d = str_replace(' ', '_', $data[2]);
                $text = $data[3];
                $id = mention_id($fid, $first_name);
                $t = "$id | #user_$fid\nبخش : #$d\nمتن پیام : \n$text";
                break;
            case 'support_pm2':
                $text = $data;
                $t = "🗣 شما یک پیام از پشتیبانی دارید\n$text";
                break;
            case 'up_coin':
                $amount = $data[0];
                $balance = $data[1];
                $t = "❤️ کاربر عزیز
    💸 {$amount} تومان از طرف مدیریت به حساب شما واریز شد.
    💎 موجودی جدید شما : {$balance}";
                break;
            case 'down_coin':
                $amount = $data[0];
                $balance = $data[1];
                $t = "❤️ کاربر عزیز
  💸 {$amount} تومان از طرف مدیریت از حساب شما کسر شد.
  💎 موجودی جدید شما : {$balance}";
                break;
            case 'block':
                $t = '❌ اکانت شما مسدود میباشد.';
                break;
            case 'unblock':
                $t = '🟢 کاربر گرامی، حساب شما آزاد شد.';
                break;
            case 'db_error':
                $product = $data[0];
                $fid = $data[1];
                $error = $data[2];
                $t = "مشکل در ثبت سفارش در دیتابیس: \nمحصول : {$product}\n$error";
                break;
            case 'api_order_error':
                $fid = $data[0];
                $code = $data[1];
                $code_api = $data[2];
                $t = "یک سفارش با مشکل مواجه شد و در کد پیگیری وب سرویس ان در دیتابیس ربات ثبت نشد\nکاربر : $fid کد دیتابیس : $code کد وب سرویس : {$code_api} وضعیت این محصول به دستی نغییر یافت";
                break;
            case 'api_error':
                $product = $data[0];
                $api = $data[1];
                $fid = $data[2];
                $error = $data[3];
                $t = "مشکل در ثبت سفارشی در وب سرویس : \nوب سرویس : {$api}\nمحصول : {$product}\nشرح مشکل : $error";
                break;
            case 'apinotfound_error':
                $product = $data[0];
                $api = $data[1];
                $fid = $data[2];
                $error = 'وب سرویس یافت نشد';
                $t = "مشکل در ثبت سفارشی در وب سرویس : \nوب سرویس : {$api}\nمحصول : {$product}\nشرح مشکل : $error";
                break;
            case 'product_error':
                $product = $data[0];
                $fid = $data[1];
                $error = 'شرح مشکل : محصول مورد نظر در دیتابیس یافت نشد یا خاموش است';
                $t = "مشکل  در ثبت سفارش : \nمحصول  : {$product}\n$error";
                break;
            case 'cat_error':
                $category = $data[0];
                $fid = $data[1];
                $error = 'شرح مشکل : دسته بندی مورد نظر در ربات یافت نشد یا خاموش است';
                $t = "مشکل  در ثبت سفارش : \nدسته بندی : {$category}\n$error";
                break;
            case 'sms_error':
                $error = $data;
                $t = "مشکل در پنل پیامک \n$error";
                break;
            case 'curl_payment_error':
                $t = 'cURL Error ' . $data[0] . ' # : ' . json_encode($data[1], 448);
                break;
            case 'ok_gift_out':
                $code = $data[0];
                $fid = $data[1];
                $first_name = $data[2];
                $req = $data[3];
                $info = $data[4];
                $time = jdate('Y/m/d - H:i');
                $t = "در خواست برداشت درآمد <code>{$code}</code>
نام : <a href ='tg://user?id=$fid'>$first_name</a>
ایدی عددی : <code>$fid</code>
میزان درخواست : {$req}
اطلاعات فرستاده شده : {$info}
زمان ثبت : $time";
                break;
            case 'error_getway_get':
                $t = "مشکل در درگاه " . $data['0'] . " : \n\n" . $data['1'];
                break;
            case 'order_receipt':
                $category = $data[0];
                $product = json_decode($data[1]);
                $sefaresh = "\n" . $category . "\n - " . $product;
                $first_name = $data[2];
                $fid = $data[3];
                $price = $data['4'];
                $count = $data['5'];
                $link = $data[6];
                $code = $data[7];
                $old_balance = $data['8'];
                $new_balance = $data['9'];

                $date = jdate('Y/m/d - H:i:s');
                if ($data['10'] != 'noapi') {
                    $api = $data['10'];
                    $tt = "\nوب سرویس : $api\n";
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
            case 'order_receipt_channel':
                $category = $data[0];
                $product = json_decode($data[1]);
                $count = $data[2];
                $price = $data[3];
                $date = jdate('Y/m/d - H:i:s');
                $t = "🔥✅ گزارش سفارش موفق
  ‏
  💎 مشخصات سفارش :

  📝نام محصول : {$category}

  📌دسته انتخابی : {$product}

  ❔تعداد : {$count}
  💸قیمت : {$price}
  🗓تاریخ سفارش : {$date}
  ‏
  ⁉️ مراحل ثبت سفارش :

  ➊ وارد ربات @{$idbot} شوید.
  ➋ موجودی خود را افزایش دهید.
  ➌ سرویس مورد نظر خود را سفارش دهید.
  ";
                break;
            case 'ok_number':
                $contactuser = $data[0];
                $id = $data[1];
                $first_name = type_text($data[2], 'm', $id);
                $time = jdate('Y/m/d - H:i');
                $t = "شماره : <code>$contactuser</code>
ایدی : <code>$id</code>
اسم : $first_name
تاریخ ثبت نام : $time";
                break;
            case 'ok_number_2':
                $contactuser = $data[0];
                $id = $data[1];
                $first_name = type_text($data[2], 'm', $id);
                $referral_id = type_text($data[3], 'm', $data[3]);
                $time = jdate('Y/m/d - H:i');
                $t = "شماره : <code>$contactuser</code>
ایدی : <code>$id</code>
اسم : $first_name
معرف : $referral_id
تاریخ ثبت نام : $time";
                break;
            case 'payment_verify_caption':
                $fid = $data['0'];
                $first_name = $data['0'];
                $caption = $data['0'];
                $t = "<a href = 'tg://user?id=$fid'>$first_name</a> | #user_$fid\nمتن پیام : \n" . $caption;
                break;
            case 'sended_pm':
                $t = "پیام برای همه کابران ارسال شد";
                break;
            case 'forwarded_pm':
                $t = "پیام برای همه کاربران فوروارد شد";
                break;
            case 'off_all_payments':
                $t = "❌ درحال حاضر پرداخت آنلاین غیرفعال میباشد.";
                break;
            case 'reject_card':
                $t = "❌ مشخصات ارسالی توسط شما تأیید نشد !
👈 لطفاً به پشتیبانی پیام دهید.";
                break;
            case 'ok_card':
                $t = "✅ شماره کارت شما ثبت و تایید شد !";
                break;
            case 'admin_ok_payment':
                $getway = $data['0'];
                $name = $data['1'];
                $user = $data['2'];
                $fid = $user['user_id'];
                $name = type_text($name, 'm', $fid);
                $new = $user['balance'];
                $number = $user['number'];
                $payment = $data['3'];
                $tracking_code = $payment['tracking_code'];
                $amount = $payment['amount'];
                $ip = json_decode($payment['data'], true)['ip'];
                $card = $data['4'];
                $t = "#تراکنش جدید  $getway
    کاربر : $name
    شناسه عددی : <code>$fid</code>
    شماره پیگیری : <code>$tracking_code</code>
    مبلغ پرداختی : $amount
    موجودی قبلی : {$user['balance']}
    موجودی کلی : {$new}
    شماره کاربر : <code>$number</code>
    IP : $ip
    card : $card";
                break;
            case 'cancel_payout':
                $t = "❌ درخواست برداشت شما رد شد و موجودی به حساب شما بازگشت.";
                break;
            case 'ok_payout':
                $t = "✅ درخواست برداشت شما تأیید شد.";
                break;
            case 'ok_verify':
                $card = $data;
                $t = "کارت شما به شماره : $card ثبت شد.\nلطفا فقط از این کارت برای پرداخت استفاده نمایید";
                break;
            default:
                $t = '❓انتخاب کنید';
                break;
        }
        return $t;
    }
}
