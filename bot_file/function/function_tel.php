<?php

function sendlog($text, $keyboard = null, $force = false)
{
    global $bot, $fid;

    $bot->sm($fid, $text, $keyboard);

    return true;
}


function sm_user($text, $keyboard = null, $id = null)
{
    global $bot, $fid, $media;

    $recipient_id = isset($id) ? $id : $fid;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media->text($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->keys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }
    return $bot->sm($recipient_id, $textContent, $keyboardContent);
}

function sm_channel($channel, $text, $keyboard = null)
{
    global $bot, $media, $settings;

    $recipient_id = (isset($settings[$channel]) && $settings[$channel] != 0) ? $settings[$channel] : admins['0'];
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        $ar_t = isset($text[1]) ? $text[1] : null;
    }

    $textContent = $media->atext($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            $ar_k = isset($keyboard[1]) ? $keyboard[1] : null;
        }
        $keyboardContent = $media->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }

    $response = $bot->sm($recipient_id, $textContent, $keyboardContent);

    if (!isset($response['ok']) || !$response['ok']) {
        $admin_id = admins['0'];
        $errorMessage = "Error Access To Channel\n\n📢 Channel: {$channel}";
        $bot->sm($admin_id, $errorMessage);
        $response = $bot->sm($admin_id, $textContent, $keyboardContent);
    }

    return $response;
}

function edt_channel($channel, $text, $keyboard = null)
{
    global $bot, $cid, $media, $settings, $message_id;

    $recipient_id = $cid;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }

    $textContent = $media->atext($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }
    return $bot->edit_text($recipient_id, $message_id, $textContent, $keyboardContent);
}

function edk_channel($channel, $keyboard)
{
    global $bot, $cid, $media, $settings, $message_id;

    $recipient_id = $cid;

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }
    return $bot->edit_keyboard($recipient_id, $message_id, $keyboardContent);
}

function edt_user($text, $keyboard = null, $id = null, $msg_id = null)
{
    global $bot, $fid, $media, $message_id;

    $recipient_id = isset($id) ? $id : $fid;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media->text($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->keys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }

    $msg_id = isset($msg_id) ? $msg_id : $message_id;

    return $bot->edit_text($recipient_id, $msg_id, $textContent, $keyboardContent);
}

function edk_user($keyboard, $id = null)
{
    global $bot, $fid, $media, $message_id;

    $recipient_id = isset($id) ? $id : $fid;

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->keys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }
    return $bot->edit_keyboard($recipient_id, $message_id, $keyboardContent);
}

function alert_user($text, $showalert = false)
{
    global $bot, $call_back_id, $media;

    $type_text = $text[0];
    if ($type_text == 'none') {
        return $bot->alert($call_back_id);
    }
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media->text($type_text, $ar_t);

    return $bot->alert($call_back_id, $textContent, $showalert);
}


function sm_to_user($text, $keyboard = null, $id = null)
{
    global $bot, $fid, $media;

    $recipient_id = isset($id) ? $id : $fid;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media->atext($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }
    return $bot->sm($recipient_id, $textContent, $keyboardContent);
}

function sm_admin($text, $keyboard = null, $id = null)
{
    global $bot, $fid, $media_admin;

    $recipient_id = isset($id) ? $id : $fid;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }

    $textContent = $media_admin->atext($type_text, $ar_t);

    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media_admin->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }

    return $bot->sm($recipient_id, $textContent, $keyboardContent);
}

function edt_admin($text, $keyboard = null, $megid = null, $id = null)
{
    global $bot, $fid, $media_admin, $message_id;

    $recipient_id = isset($id) ? $id : $fid;
    $message_id = isset($megid) ? $megid : $message_id;
    $type_text = $text[0];
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media_admin->atext($type_text, $ar_t);
    if (isset($keyboard)) {
        $type_key = $keyboard[0];
        unset($keyboard[0]);
        if (isset($keyboard[2])) {
            $ar_k[] = $keyboard;
            $ar_k = array_merge(...$ar_k);
        } else {
            if (isset($keyboard[1])) {
                $ar_k = $keyboard[1];
            } else {
                $ar_k = null;
            }
        }

        $keyboardContent = $media_admin->akeys($type_key, $ar_k);
    } else {
        $keyboardContent = null;
    }

    return $bot->edit_text($recipient_id, $message_id, $textContent, $keyboardContent);
}

function edk_admin($keyboard, $megid = null, $id = null)
{
    global $bot, $fid, $media_admin, $message_id;

    $recipient_id = isset($id) ? $id : $fid;
    $message_id = isset($megid) ? $megid : $message_id;

    $type_key = $keyboard[0];
    unset($keyboard[0]);
    if (isset($keyboard[2])) {
        $ar_k[] = $keyboard;
        $ar_k = array_merge(...$ar_k);
    } else {
        if (isset($keyboard[1])) {
            $ar_k = $keyboard[1];
        } else {
            $ar_k = null;
        }
    }

    $keyboardContent = $media_admin->akeys($type_key, $ar_k);

    return $bot->edit_keyboard($recipient_id, $message_id, $keyboardContent);
}

function alert_admin($text, $force = false)
{
    global $call_back_id, $bot, $media_admin;

    $type_text = $text[0];
    if ($type_text == 'none') {
        return $bot->alert($call_back_id);
    }
    unset($text[0]);

    if (isset($text[2])) {
        $ar_t[] = $text;
        $ar_t = array_merge(...$ar_t);
    } else {
        if (isset($text[1])) {
            $ar_t = $text[1];
        } else {
            $ar_t = null;
        }
    }
    $textContent = $media_admin->atext($type_text, $ar_t);

    return $bot->alert($call_back_id, $textContent, $force);
}

function sendallmsg($id, $data)
{
    global $bot;
    if ($data["step"] == "sendall") {
        $info = json_decode($data['info'], 1);
        if ($info['send'] == 'sm') {
            $bot->sm($id, $info['text']);
        } else {
            $bot->bot($info['send'], ['chat_id' => $id, $info['type_file'] => $info['file_id'], 'caption' => $info['caption']]);
        }
    } elseif ($data["step"] == "fwd") {
        $info = json_decode($data['info'], 1);
        $bot->bot('forwardmessage', [
            'chat_id' => $id,
            'from_chat_id' => $info['from_chat'],
            'message_id' => $info['msgid']
        ]);
    } else {
        $bot->sm($id, "Error: Unknown step");
    }
}

function handlePoshtibani2($update, $media, $channel, $bot, $fid, $first_name, $user)
{
    $types = ['video', 'photo', 'audio', 'voice', 'document'];
    $file_id = null;
    $caption = '';
    $recipient_id = (isset($settings[$channel]) && $settings[$channel] != 0) ? $settings[$channel] : admins['0'];
    $true = false;

    foreach ($types as $i) {
        if (isset($update['message'][$i])) {
            $type = $update['message'][$i];
            $caption = $update['message']['caption'] ?? '';
            $file_id = ($i == 'photo') ? end($type)['file_id'] : $type['file_id'];
            $true = true;
            break;
        }
    }
    if ($true) {

        $send = str_replace($types, ['sendvideo', 'sendphoto', 'sendaudio', 'sendvoice', 'senddocument'], $i);

        $re = $bot->bot($send, [
            'chat_id' => $recipient_id,
            $i => $file_id,
            'caption' => $media->atext('admin_support', [$fid, $first_name, $user['data'], $caption]),
            'parse_mode' => 'Html',
            'reply_markup' => json_encode($media->akeys('admin_support', $fid)),
        ])['ok'];
        if ($re) {
            return true;
        } else {
            return false;
        }
    } else {
        return false;
    }
}

function handleStart($type)
{
    global $db, $admin, $section_status, $settings, $fid;

    if (!$db->has('users_information', ['user_id' => $fid])) {
        $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'join_date' => time()]);
        sm_user([$type, $settings['text_start']], ['home'], $fid);
        StartGift($fid);
    } else {
        user_set_data(['step' => 'none', 'data[JSON]' => []]);
        sm_user([$type, $settings['text_start']], ['home']);
        if ($admin) {
            $db->update('admins', ['step' => 'user'], ['user_id' => $fid]);
        }
    }
}

function StartGift($fid)
{
    global $section_status, $settings;
    if ($section_status['free']['gift_start'] and $settings['gift_start'] > 0) {

        $old_balance = 0;
        $new_balance = $settings['gift_start'];
        insertTransaction('gift', $fid, $old_balance, $new_balance, $settings['gift_start'], 'StartGift');

        user_set_data(['balance[+]' => $settings['gift_start']], $fid);
        sm_user(['gift_start', $settings['gift_start']], null, $fid);
    }
}

function js($text)
{
    return json_encode($text);
}

function removeWhiteSpace($text)
{
    if (empty($text)) {
        return $text;
    }

    $text = json_encode($text);
    $text = str_replace('\u00a0', '', $text);
    $text = json_decode($text, true);
    return trim(strip_tags($text));
}

function getip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ip = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
    elseif (!empty($_SERVER['HTTP_X_REAL_IP']))
        $ip = $_SERVER['HTTP_X_REAL_IP'];
    elseif (!empty($_SERVER['HTTP_AR_REAL_IP']))
        $ip = $_SERVER['HTTP_AR_REAL_IP'];
    else
        $ip = $_SERVER['REMOTE_ADDR'];
    return $ip;
}

function ip_info($ip, $type = 2)
{
    if ($type == 1) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "http://ip-api.com/csv/" . $ip . "");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $exec = curl_exec($c);
        curl_close($c);
        $exp    = explode(",", $exec);
        $pais   = $exp[1];
        return $pais;
    } elseif ($type == 2) {
        $c = curl_init();
        curl_setopt($c, CURLOPT_URL, "https://api.country.is/" . $ip . "");
        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($c, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($c, CURLOPT_SSL_VERIFYPEER, 0);
        $exec = curl_exec($c);
        curl_close($c);
        $decode = json_decode($exec, 1);
        if ($decode['country'] == 'IR') {
            $decode['country'] = 'iran';
        }
        return $decode['country'];
    }
}

function curl_get($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $resp = curl_exec($curl);
    $e = curl_error($curl);
    curl_close($curl);
    if ($e) {
        return $e;
    } else {
        return $resp;
    }
}
function curl_post($url, $data = [], $headers = array("Content-Type: application/json"))
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_TIMEOUT, 30);

    $headers = $headers;

    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    $data = json_encode($data);

    curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

    $resp = curl_exec($curl);
    $e = curl_error($curl);
    curl_close($curl);
    if ($e) {
        return $e;
    } else {
        return $resp;
    }
}

function redirect($url)
{
    if (!headers_sent()) {
        header("Location: $url");
    } else {
        echo "<script type='text/javascript'>window.location.href='$url'</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$url'/></noscript>";
    }
    exit;
}
function round_up($float, $pow = '500', $dec = -1)
{
    if ($dec == 0) {
        if ($float < 0) {
            return floor($float);
        } else {
            return ceil($float);
        }
    } else {
        $d = pow($pow, $dec);
        if ($float < 0) {
            return floor($float * $d) / $d;
        } else {
            return ceil($float * $d) / $d;
        }
    }
}
function price_once($min, $max, $price = 0)
{
    if ($price == 0) {
        return $price;
    } else {
        if ($min == 1 and $max == 1) {
            return $price;
        } elseif ($max < 1000) {
            $price = $price / $max;
            return $price;
        } else {
            $price = $price / 1000;
            return $price;
        }
    }
}

function random_code($r = 8)
{
    $alf = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890";
    $key = "";
    for ($i = 0; $i < $r; $i++) {
        $key .= $alf[rand(0, strlen($alf) - 1)];
    }
    return $key;
}

function text_starts_with($text, string $needle): bool
{
    if (empty($needle) || empty($text)) {
        return false;
    }
    return str_starts_with($text, $needle);
}

function text_contains($text, string $needle): bool
{
    if (empty($needle) || empty($text)) {
        return false;
    }
    return strpos($text, $needle) !== false;
}


function check_allow($type, $main = 'main')
{
    global $access;
    if (isset($access[$main][$type]) && $access[$main][$type]) {
    } else {
        return;
    }
}


function admin_step($step = 'none')
{
    global $fid, $db;
    $db->update('admins', ['step' => $step], ['user_id' => $fid]);
}

function admin_data($data)
{
    global $fid, $db;
    $db->update('admins', $data, ['user_id' => $fid]);
}

function get_admin($id, $type = 'user_id')
{
    global $db;
    return $db->get('admins', '*', [$type => $id]);
}

function get_option($option, $defult = 0)
{
    global $db;
    $get =  $db->get('setting_options', '*', ['option_key' => $option]);
    if ($get) {
        return $get['option_value'];
    } else {
        return $defult;
    }
}

function update_option($option, $value)
{
    global $db;
    $get =  $db->get('setting_options', '*', ['option_key' => $option]);
    if ($get) {
        $db->update('setting_options', ['option_value' => $value], ['id' => $get['id']]);
    } else {
        $db->insert('setting_options', ['option_value' => $value, 'option_key' => $option]);
    }
}


function flattenArray($array)
{
    $result = [];
    foreach ($array as $k => $item) {
        if (is_array($item)) {
            $result = array_merge($result, flattenArray($item));
        } else {
            $result[] = $item;
        }
    }
    return $result;
}

function mention_id($id, $name = null)
{
    $name = (isset($name)) ? $name : $id;
    return "<a href = 'tg://user?id=$id'>$name</a>";
}

function get_category($data = null, $level = null)
{
    global $db, $settings;

    $level = ($level == 0) ? null : $level;

    $status = (!isset($data['status'])) ? 1 : [0, 1];

    $displaySettings = ($level == null) ? json_decode($settings['display_category'], true) : json_decode($settings['display_sub_category'], true);

    $page = $displaySettings['page'];
    $order = $displaySettings['sort_by'];
    $sort = $displaySettings['sort'];

    $result = $db->select(
        'categories',
        ['name', 'id', 'status'],
        [
            'status' => $status,
            'LIMIT' => [$data['offset'], $page],
            'category_id' => $level,
            'ORDER' => [$order => $sort]
        ]
    );

    return $result;
}

function get_products($data = null, $level = null)
{
    global $db, $settings;

    $displaySettings = json_decode($settings['display_products'], true);

    $status = (!isset($data['status'])) ? 1 : [0, 1];

    $page = $displaySettings['page'];
    $order = $displaySettings['sort_by'];
    $sort = $displaySettings['sort'];

    $result = $db->select(
        'products',
        '*',
        [
            'status' => $status,
            'LIMIT' => [$data['offset'], $page],
            'category_id' => $level,
            'ORDER' => [$order => $sort]
        ]
    );
    return $result;
}



function escapeHtml($text)
{
    return str_replace(['>', '<'], ['&gt;', '&lt;'], $text);
}

function updateBlockBotStatus($oldStatus, $newStatus, $tc)
{
    if (($oldStatus == 'member' && $newStatus == 'kicked' || $oldStatus == 'kicked' && $newStatus == 'member') && $tc == 'private') {
        user_set_data(['step' => 'none', 'block_bot' => ($newStatus == 'member' ? 0 : 1)]);
    }
}

function updateLastMessage($lastMsg, $admin = false)
{
    global $settings;
    $currentTime = time();
    if ($admin != null) {
        $lastMsg['block'] = 0;
        $lastMsg['last_msg'] = $currentTime;
    } else {
        if ($lastMsg['block'] && $lastMsg['last_msg'] <= $currentTime) {
            $lastMsg['block'] = 0;
            $lastMsg['last_msg'] = $currentTime + $settings['s_spam'];
        } elseif ($lastMsg['last_msg'] <= $currentTime) {
            $lastMsg['last_msg'] = $currentTime + $settings['s_spam'];
        } elseif (!$lastMsg['block']) {
            $lastMsg['last_msg'] = $currentTime + $settings['s_block'];
            $lastMsg['block'] = 1;
            sm_user(['spam', $settings['s_block']]);
            user_set_data(['last_msg[JSON]' => $lastMsg]);
        } else {
            exit;
        }
    }
    user_set_data(['last_msg[JSON]' => $lastMsg]);
}

function getCategoryHierarchy($categoryId, $array = false)
{
    global $db;
    $categoryNames = [];

    $currentCategoryId = $categoryId;

    while (true) {
        $currentCategory = $db->get('categories', '*', ['id' => $currentCategoryId]);

        $categoryNames[] = '- ' . json_decode($currentCategory['name']);

        if ($currentCategory['category_id'] === null) {
            break;
        }

        $currentCategoryId = $currentCategory['category_id'];
    }

    $categoryNames = array_reverse($categoryNames);

    if (!$array) {
        return implode("\n", $categoryNames);
    } else {
        return $categoryNames;
    }
}


function processReferral($referral_id, $fid)
{
    global $section_status, $db, $settings;
    $giftReferral = $section_status['free']['gift_referral'];
    $gift_payment = $section_status['free']['gift_payment'];

    if ($giftReferral && $gift_payment) {

        $usResult = $db->get('users_information', '*', ['user_id' => $referral_id]);
        $old_balance = $usResult['balance'];
        $new_balance = $old_balance + $settings['gift_referral'];
        insertTransaction('gift', $referral_id, $old_balance, $new_balance, $settings['gift_referral'], 'GiftJoin');

        user_set_data(['gift[+]' => $settings['gift_referral'], 'referral[+]' => 1, 'gift_referral[+]' => $settings['gift_referral']], $referral_id);

        if (!$db->has('users_information', ['user_id' => $fid])) {
            $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'referral_id' => $referral_id, 'join_date' => time()]);
            StartGift($fid);
        } else {
            $db->update('users_information', ['referral_id' => $referral_id, 'join_date' => time()], ['user_id' => $fid]);
        }

        sm_user(['gift', 1, $settings['gift_payment'], $settings['gift_referral'], $referral_id, $fid], null, $referral_id);
    } elseif ($giftReferral) {

        $usResult = $db->get('users_information', '*', ['user_id' => $referral_id]);
        $old_balance = $usResult['balance'];
        $new_balance = $old_balance + $settings['gift_referral'];
        insertTransaction('gift', $referral_id, $old_balance, $new_balance, $settings['gift_referral'], 'GiftJoin');

        user_set_data(['gift[+]' => $settings['gift_referral'], 'referral[+]' => 1, 'gift_referral[+]' => $settings['gift_referral']], $referral_id);

        if (!$db->has('users_information', ['user_id' => $fid])) {
            $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'join_date' => time()]);
            StartGift($fid);
        }

        sm_user(['gift', 3, $settings['gift_referral'], $referral_id, $fid], null, $referral_id);
    } elseif ($gift_payment) {
        user_set_data(['referral[+]' => 1], $referral_id);

        if (!$db->has('users_information', ['user_id' => $fid])) {
            $db->insert('users_information', ['user_id' => $fid, 'step' => 'none', 'referral_id' => $referral_id, 'join_date' => time()]);
            StartGift($fid);
        } else {
            $db->update('users_information', ['referral_id' => $referral_id, 'join_date' => time()], ['user_id' => $fid]);
        }
        sm_user(['gift', 2, $settings['gift_payment'], $referral_id, $fid], null, $referral_id);
    }
}

function orderlist($result)
{
    global $media;
    $tx = null;
    foreach ($result as $row) {
        $tx .= $media->text('last_order_row', $row);
    }
    return $tx;
}

function transactionslist($result)
{
    global $media;
    $tx = null;
    foreach ($result as $row) {
        $tx .= $media->text('last_payment_row', $row);
    }
    return $tx;
}

function insertTransaction($type, $id, $old, $new, $amount, $type2, $admin = 0)
{
    global $db;

    if (!is_numeric($id) || !is_numeric($amount) || !is_numeric($old) || !is_numeric($new)) {
        throw new Exception("Invalid numeric input");
    }

    $data = [
        'user_id' => (int)$id,
        'status' => 1,
        'type' => $type,
        'amount' => (int)$amount,
        'data[JSON]' => [
            'old' => (int)$old,
            'new' => (int)$new,
            'amount' => (int)$amount,
            'type' => $type2,
            'admin' => (int)$admin
        ],
        'date' => time(),
        's_date' => time(),
    ];

    try {
        $db->insert('transactions', $data);
        if (!$db->id()) {
            throw new Exception("Insert failed");
        }
    } catch (Exception $e) {
        file_put_contents('error.log', "insertTransaction error: " . $e->getMessage() . "\n", FILE_APPEND);
        throw $e;
    }
}

function get_user($id, $type = 'user_id')
{
    global $db;
    return $db->get('users_information', '*', [$type => $id]);
}

function user_set_step($step = 'none', $id = null)
{
    global $db, $fid;
    $fid = ($id == null) ? $fid : $id;
    $db->update('users_information', ['step' => $step], ['user_id' => $fid]);
}

function user_set_data($data, $id = null)
{
    global $db, $fid;
    $userId = ($id === null) ? $fid : $id;

    if (!is_array($data) || empty($data) || !is_numeric($userId)) {
        throw new Exception("Invalid input data or user ID");
    }

    try {
        if (!$db->update('users_information', $data, ['user_id' => $userId])) {
            throw new Exception("No rows updated");
        }
    } catch (Exception $e) {
        file_put_contents('error.log', "user_set_data error: " . $e->getMessage() . "\n", FILE_APPEND);
        throw $e;
    }
}

function add_tranaction($type, $id, $amount, $data = [])
{
    global $db;
    $db->insert('transactions', [
        'user_id' => $id,
        'status' => 2,
        'type' => $type,
        'amount' => $amount,
        'data[JSON]' => $data,
        'date' => time(),
        's_date' => time(),
    ]);
    $code  = $db->id();
    return $code;
}

function type_text($text, $type = 'none', $href = null)
{
    switch ($type) {
        case 'b':
            $t = "<b>" . $text . "</b>";
            break;
        case 'i':
            $t = "<i>" . $text . "</i>";
            break;
        case 'u':
            $t = "<u>" . $text . "</u>";
            break;
        case 'a':
            $t = "<a href='" . $href . "'>" . $text . "</a>";
            break;
        case 'm':
            $t = "<a href='tg://user?id=" . $href . "'>" . $text . "</a>";
            break;
        case 'c':
            $t = "<code>" . $text . "</code>";
            break;
        case 's':
            $t = "<tg-spoiler>" . $text . "</tg-spoiler>";
            break;
        default:
            $t = $text;
            break;
    }
    return $t;
}



function up_price($price, $percent)
{
    return $price + ($price * $percent / 100);
}

function get_DIFF_TIME()
{
    return (get_option('DIFF_TIME', 0) * 60 * 60);
}

function get_settings(&$settings)
{
    global $db;
    $result = $db->select('setting_options', '*');
    foreach ($result as $row) {
        $settings[$row['option_key']] = $row['option_value'];
    }
}


function convertnumber($string)
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    $num = range(0, 9);

    $string = str_replace($persian, $num, $string);
    $string = str_replace($arabic, $num, $string);

    return (int)$string;
}


function nformat(float $number, $number_float = null, $decimal = '.')
{
    $broken_number = explode($decimal, (string)$number);
    $number_float = (isset($number_float)) ? $number_float : get_option('number_float', 0);
    if (!empty($broken_number[1])) {
        $dec = substr($broken_number[1], 0, $number_float);
        $result = rtrim(number_format((float)($broken_number[0] . $decimal . $dec), $number_float, $decimal, ''), '0');

        return rtrim($result, $decimal);
    } else {
        return $broken_number[0];
    }
}

function row_chunk($array = [], $sizes = [])
{
    $result = [];
    $index = 0;
    $size_count = count($sizes);

    $array = array_merge(...$array);

    while ($index < count($array)) {
        foreach ($sizes as $size) {
            $result[] = array_slice($array, $index, $size);
            $index += $size;
            if ($index >= count($array)) break;
        }
    }

    return $result;
}


function off($off)
{
    return str_replace([0, 1], ['❌', '✅'], $off ?? '');
}
