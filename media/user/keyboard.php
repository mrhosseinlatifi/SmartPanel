<?php
//---------------کیبورد------------------//
$key['start'] = [
    '/start'
];

$key['price'] = [
    "📊 قیمت خدمات",
    "/price"
];

$key['order'] = [
    "🛍 ثبت سفارش",
    "/buy"
];

$key['info'] = [
    "👤 اطلاعات حساب",
    "/info"
];

$key['payment'] = [
    "💳 شارژ حساب",
    "/charge"
];

$key['support'] = [
    "📬 پشتیبانی",
    "/support"
];

$key['status'] = [
    "💾 پیگیری",
    "/status"
];

$key['free'] = [
    "💎 شارژ رایگان",
    "/free"
];

$key['share_contact'] = "✅ تایید شماره";
$key['back'] = "🔙 منوی اصلی";
$key['back_to_before'] = "🔙 منوی قبلی";
$key['join_channel'] = "☑️ عضویت در کانال";
$key['ozv'] = "✅ عضو شدم";
$key['payment_offline'] = "💳 کارت به کارت";
$key['payment_online'] = "💸 پرداخت آنلاین";
$key['crypto_payment'] = "💰 پرداخت با ارز دیجیتال";
$key['starz_payment'] = "⭐️ پرداخت با استارز";
$key['send_receipt'] = "📤 ارسال فیش پرداخت";
$key['change_gift_balance'] = "🔄 تبدیل درآمد به موجودی";
$key['withdraw_balance'] = "💰 برداشت موجودی";
$key['balance'] = "💵 موجودی 💵";
$key['status_order'] = ["🔍 پیگیری سفارش", "/orderstatus"];
$key['last_order'] = ["🛒 سفارشات اخیر", "/lastorder"];
$key['last_payment'] = ["💸 تراکنش های اخیر", "/lastcharge"];
$key['close_key'] = "❌ بستن لیست";
$key['product'] = "✏️ نام محصول";
$key['list_price'] = "💸 قیمت محصول";
$key['back_price'] = "🔙 بازگشت";
$key['cancel_order'] = "❌ لغو سفارش";
$key['ok_order'] = "✅ تایید سفارش";
$key['ok'] = "✅ تایید میکنم";
$key['start_bot'] = "🤖 ورود به ربات";
$key['not_product'] = "❌ محصولی برای نمایش وجود ندارد";
$key['status_order_inline'] = "🔍 پیگیری وضعیت سفارش";
$key['fast_support'] = '⚡️ پاسخ سریع';
$key['next_page'] = '➡️ صفحه بعد';
$key['prev_page'] = '⬅️ صفحه قبل';
$key['move_balance'] = '💰 انتقال موجودی';
$key['charge_code'] = '💎 ثبت کد شارژ';
$key['link_payment'] = '👇🏻لینک پرداخت👇🏻';
$key['fast_link'] = '🔗 لینک سریع محصول';
$key['reorder'] = '🔄 سفارش مجدد';
$key['support_panel'] = ['🔍 | پیگیری سفارشات', '💸 | پیگیری تراکنش ها', '👨‍💻 | ارتباط با مدیریت', '✳️ | سایر موارد', '⭕️ | پیشنهاد،انتقاد و شکایت'];
$key['gift_code'] = '🎁 اعمال کد تخفیف';
$key['back_to_payment'] = '💵 برگشت به پرداخت';
$key['fast_order'] = '💎 سفارش محصول 💎';
$key['panel_admin'] = '‍• پنل مدیریت •';
$key['ok_move_balance'] = '✅ تایید انتقال';
$key['identity_verification'] = '🔐 احراز هویت';
//---------------کیبورد------------------//
trait user_keyboard
{
    function keys($keys, $data = null)
    {
        global $key, $key_admin, $section_status, $admin, $settings, $idbot;
        switch ($keys) {
            case 'home':
                if ($section_status['main']['free']) {
                    $t = ['keyboard' => [
                        [['text' => $key['price'][0]], ['text' => $key['order'][0]]],
                        [['text' => $key['info'][0]], ['text' => $key['payment'][0]]],
                        [['text' => $key['support'][0]], ['text' => $key['status'][0]], ['text' => $key['free'][0]]],
                    ]];
                } else {
                    $t = ['keyboard' => [
                        [['text' => $key['price'][0]], ['text' => $key['order'][0]]],
                        [['text' => $key['info'][0]], ['text' => $key['payment'][0]]],
                        [['text' => $key['support'][0]], ['text' => $key['status'][0]]],
                    ]];
                }
                if ($admin) {
                    $t['keyboard'][] = [['text' => $key['panel_admin']]];
                }
                break;
            case 'request_contact':
                $t = ['keyboard' => [
                    [['text' => $key['share_contact'], 'request_contact' => true]],
                    [['text' => $key['back']]],
                ]];
                break;
            case 'back':
                $t = [
                    'keyboard' => [
                        [['text' => $key['back']]],
                    ]
                ];
                break;
            case 'back_to_before':
                $t = [
                    'keyboard' => [
                        [['text' => $key['back']], ['text' => $key['back_to_before']]],
                    ]
                ];
                break;
            case 'ok_order':
                $t = [
                    'keyboard' => [
                        [['text' => $key['ok_order']]],
                        [['text' => $key['cancel_order']], ['text' => $key['back_to_before']]],
                    ]
                ];
                break;
            case 'ok_panel':
                $t = [
                    'keyboard' => [
                        [['text' => $key['ok']]],
                        [['text' => $key['back']]],
                    ]
                ];
                break;
            case 'shop_keyboard':
                $result = $data[0];
                $c = $data[1];
                $depth = $data[2];
                $currentIndex = $data[3];
                if ($depth == 'product') {
                    $displaySettings = json_decode($settings['display_products'], true);
                } else {
                    $displaySettings = ($depth === null)
                        ? json_decode($settings['display_category'], true)
                        : json_decode($settings['display_sub_category'], true);
                }
                $previousIndex = $currentIndex - $displaySettings['page'];
                $nextIndex = $currentIndex + $displaySettings['page'];

                foreach ($result as $button) {
                    $button['name'] = json_decode($button['name']);
                    $t[] = [['text' => $button['name']]];
                }

                $t = row_chunk($t, $displaySettings['row']);

                if ($c > $displaySettings['page']) {
                    if ($previousIndex < 0) {
                        $t[] = [['text' => $key['next_page']]];
                    } elseif ($c > $nextIndex) {
                        $t[] = [['text' => $key['prev_page']], ['text' => $key['next_page']]];
                    } elseif ($c <= $nextIndex) {
                        $t[] = [['text' => $key['prev_page']]];
                    }
                }

                $t[] = [['text' => $key['back']], ['text' => $key['back_to_before']]];

                $t = ['keyboard' => $t];
                break;
            case 'price_info':

                $result = $data['0'];
                $c = $data['1'];
                $depth = $data['2'];
                $currentIndex = $data['3'];

                $depth = ($depth == '') ? 0 : $depth;

                $displaySettings = ($depth === 0)
                    ? json_decode($settings['display_category'], true)
                    : json_decode($settings['display_sub_category'], true);

                $previousIndex = $currentIndex - $displaySettings['page'];
                $nextIndex = $currentIndex + $displaySettings['page'];

                foreach ($result as $button) {
                    $button['name'] = json_decode($button['name']);
                    $t[] = [['text' => $button['name'], 'callback_data' => 'price_info_category_show_' . $button['id']]];
                }

                $t = row_chunk($t, $displaySettings['row']);

                if ($c > $displaySettings['page']) {

                    if ($previousIndex < 0) {
                        $t[] = [['text' => $key['next_page'], 'callback_data' => 'price_info_category_page_' . $depth . '_' . $nextIndex]];
                    } elseif ($c > $nextIndex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'price_info_category_page_' . $depth . '_' . $previousIndex], ['text' => $key['next_page'], 'callback_data' => 'price_info_category_page_' . $depth . '_' . $nextIndex]];
                    } elseif ($c < $nextIndex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'price_info_category_page_' . $depth . '_' . $previousIndex]];
                    }

                    if ($depth) {
                        $t[] = [['text' => $key['back_price'], 'callback_data' => 'price_info_category_back_' . $depth], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    } else {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                } else {
                    if ($depth) {
                        $t[] = [['text' => $key['back_price'], 'callback_data' => 'price_info_category_back_' . $depth], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    } else {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                }

                $t = ['inline_keyboard' => $t];
                break;
            case 'price_info_products':
                $result = $data['0'];
                $c = $data['1'];
                $depth = $data['2'];
                $currentIndex = $data['3'];

                $displaySettings = json_decode($settings['display_products'], true);

                $previousIndex = $currentIndex - $displaySettings['page'];
                $nextIndex = $currentIndex + $displaySettings['page'];

                foreach ($result as $button) {
                    $button['name'] = json_decode($button['name']);
                    $t[] = [['text' => $button['name'], 'callback_data' => 'price_info_product_show_' . $button['id']]];
                }
                $t = row_chunk($t, $displaySettings['row']);

                if ($c > $displaySettings['page']) {

                    if ($previousIndex < 0) {
                        $t[] = [['text' => $key['next_page'], 'callback_data' => 'price_info_product_page_' . $depth . '_' . $nextIndex]];
                    } elseif ($c > $nextIndex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'price_info_product_page_' . $depth . '_' . $previousIndex], ['text' => $key['next_page'], 'callback_data' => 'price_info_product_page_' . $depth . '_' . $nextIndex]];
                    } elseif ($c < $nextIndex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'price_info_product_page_' . $depth . '_' . $previousIndex]];
                    }

                    if ($depth) {
                        $t[] = [['text' => $key['back_price'], 'callback_data' => 'price_info_category_back_' . $depth], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    } else {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                } else {
                    if ($depth) {
                        $t[] = [['text' => $key['back_price'], 'callback_data' => 'price_info_category_back_' . $depth], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    } else {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                }


                $t = ['inline_keyboard' => $t];
                break;
            case 'product_info':
                $result = $data;
                $t[] = [['text' => $key['fast_order'], 'callback_data' => 'price_info_product_order_' . $result['id']]];
                $t[] = [['text' => $key['fast_link'], 'url' => 'https://t.me/' . $idbot . '?start=product_' . $result['id']]];

                $t[] = [['text' => $key['back_price'], 'callback_data' => 'price_info_product_back_' . $result['category_id']], ['text' => $key['close_key'], 'callback_data' => 'close']];
                $t = ['inline_keyboard' => $t];
                break;
            case 'last_order':
                $c   = $data['0'];
                $page = $data['1'];
                $bef = $data['2'];
                $nex = $data['3'];
                if ($c > $page) {
                    if ($bef < 0) {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close'], ['text' => $key['next_page'], 'callback_data' => 'userorder_' . $nex]];
                    } elseif ($c > $nex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'userorder_' . $bef], ['text' => $key['close_key'], 'callback_data' => 'close'], ['text' => $key['next_page'], 'callback_data' => 'userorder_' . $nex]];
                    } elseif ($c < $nex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'userorder_' . $bef], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                } else {
                    $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                }
                $t = ['inline_keyboard' => $t];
                break;
            case 'last_payment':
                $c   = $data['0'];
                $page = $data['1'];
                $bef = $data['2'];
                $nex = $data['3'];
                if ($c > $page) {
                    if ($bef < 0) {
                        $t[] = [['text' => $key['close_key'], 'callback_data' => 'close'], ['text' => $key['next_page'], 'callback_data' => 'userpayment_' . $nex]];
                    } elseif ($c > $nex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'userpayment_' . $bef], ['text' => $key['close_key'], 'callback_data' => 'close'], ['text' => $key['next_page'], 'callback_data' => 'userpayment_' . $nex]];
                    } elseif ($c < $nex) {
                        $t[] = [['text' => $key['prev_page'], 'callback_data' => 'userpayment_' . $bef], ['text' => $key['close_key'], 'callback_data' => 'close']];
                    }
                } else {
                    $t[] = [['text' => $key['close_key'], 'callback_data' => 'close']];
                }
                $t = ['inline_keyboard' => $t];
                break;
            case 'lock_channel':
                $channels = $data['0'];
                $keyboard = [];

                foreach ($channels as $ch) {
                    $keyboard[] = [['text' => $key['join_channel'] . " $ch", 'url' => "https://t.me/" . $ch]];
                }

                if (isset($data[1]) && is_numeric($data[1])) {
                    $referral_id = $data[1];
                    $keyboard[] = [['text' => $key['ozv'], 'callback_data' => 'ref_join' . $referral_id]];
                } else {
                    $keyboard[] = [['text' => $key['ozv'], 'callback_data' => 'ozv']];
                }

                $t = ['inline_keyboard' => $keyboard];

                break;

            case 'status_order_inline':
                $code = $data;
                $t = ['inline_keyboard' => [
                    [['text' => $key['status_order_inline'], 'callback_data' => "orderstatus_" . $code]],
                ]];
                break;
            case 'back_to_payment':
                $t = ['inline_keyboard' => [
                    [['text' => $key['back_to_payment'], 'callback_data' => 'backcoin_' . $data]]
                ]];
                break;
            case 'payment_gateways':
                $result = $data['0'];
                $amount = nformat((float) $data['1']);
                $domin = $data['2'];
                $code = $data['3'];
                $t[] = [['text' => $this->text('link_payment'), 'callback_data' => 'fyk']];
                foreach ($result as $pa) {
                    $t[] = [['text' => $this->text('payment_key', [$amount, $pa['name']]), 'url' => "https://" . $domin . "/payment/index.php?file=" . $pa['file'] . "&code=" . $code . "&action=get"]];
                }
                if ($section_status['payment']['gift_code']) {
                    $t[] = [['text' => $key['gift_code'], 'callback_data' => "gift_code_" . $code]];
                }
                $t = ['inline_keyboard' => $t];
                break;
            case 'payment':
                $status = $data;
                if ($status['payment']['offline_payment']) {
                    $t[] = [['text' => $key['payment_offline']]];
                }
                if ($status['payment']['online_payment']) {
                    $t[] = [['text' => $key['payment_online']]];
                }
                if ($status['payment']['move_balance']) {
                    $t[] = [['text' => $key['move_balance']]];
                }
                if ($section_status['payment']['gift_charge']) {
                    $t[] = [['text' => $key['charge_code']]];
                }
                if ($status['payment']['crypto_payment']) {
                    $t[] = [['text' => $key['crypto_payment']]];
                }
                if ($status['payment']['starz_payment']) {
                    $t[] = [['text' => $key['starz_payment']]];
                }
                $t = row_chunk($t, [2]);
                $t[] = [['text' => $key['back']]];
                $t = ['keyboard' => $t];
                break;
            case 'send_receipt':
                $t = [
                    'inline_keyboard' => [
                        [['text' => nformat((float) $data) . ' تومان', 'callback_data' => 'fyk']],
                        [['text' => $key['send_receipt'], 'callback_data' => 'send_receipt_' . $data]],
                    ]
                ];
                break;
            case 'gift':
                $t = [
                    'keyboard' => [
                        [['text' => $key['balance']]],
                        [['text' => $key['withdraw_balance']], ['text' => $key['change_gift_balance']]],
                        [['text' => $key['back']]],
                    ]
                ];
                break;
            case 'status':
                $t = [
                    'keyboard' => [
                        [['text' => $key['status_order'][0]]],
                        [['text' => $key['last_order'][0]], ['text' => $key['last_payment'][0]]],
                        [['text' => $key['back']]],
                    ]
                ];
                break;
            case 'support_panel':
                $t = [
                    'keyboard' => [
                        [['text' => $key['support_panel'][0]], ['text' => $key['support_panel'][1]]],
                        [['text' => $key['support_panel'][2]], ['text' => $key['support_panel'][3]]],
                        [['text' => $key['support_panel'][4]]],
                        [['text' => $key['back']]],
                    ]
                ];
                break;
            case 'ok_move_balance':
                $t = [
                    'keyboard' => [
                        [['text' => $key['ok_move_balance']]],
                        [['text' => $key['back']]],
                    ],
                ];
                break;
            case 'daily_limit_with_verify':
                $t = [
                    'keyboard' => [
                        [['text' => $key['identity_verification']]],
                        [['text' => $key['back']]],
                    ],
                ];
                break;
            case 'reorder_link':
                $t = [
                    'inline_keyboard' => [
                        [['text' => $key['reorder'], 'url' => 'https://t.me/' . $idbot . '?start=product_' . $data]],
                    ],
                ];
                break;
            case 'remove':
                $t = ['remove_keyboard' => true];
                break;
            default:
                $t = null;
                break;
        }
        if (isset($t['keyboard'])) {
            $t = array_merge(array_slice($t, 0), ['resize_keyboard' => true], array_slice($t, 0));
            return $t;
        } else {
            return $t;
        }
    }
}
