<?php
return function (array $state)
{
    $pre_checkout_status = $state['pre_checkout_status'];
    $payment_status = $state['payment_status'];
    $invoice_payload = $state['invoice_payload'];
    $fid = $state['fid'];
    $id = $state['id'];
    $bot = $state['bot'];
    $update = $state['update'];
    $db = $state['db'];
    $total_amount = $state['total_amount'];
    $settings = $state['settings'];
    $user = $state['user'];
    $section_status = $state['section_status'];
    $media = $state['media'];
    $first_name = $state['first_name'];

    if ($pre_checkout_status) {
    if ($invoice_payload == $fid) {
        $bot->bot('answerPreCheckoutQuery', [
            'pre_checkout_query_id' => $id,
            'ok' => true,
        ]);
    } else {
        $bot->bot('answerPreCheckoutQuery', [
            'pre_checkout_query_id' => $id,
            'ok' => false,
            'error_message' => 'Invalid payload',
        ]);
    }
        return true;
    }

if ($payment_status) {
    if (isset($update['message']['successful_payment'])) {
        $telegram_charge_id = $update['message']['successful_payment']['telegram_payment_charge_id'];

        $exists = $db->get('transactions', 'id', [
            'tracking_code' => $telegram_charge_id,
            'getway' => 'starz'
        ]);

        if (!$exists && $invoice_payload == $fid) {
            $irt_amount = $total_amount * $settings['starz_rate'];

            $db->insert('transactions', [
                'status' => 1,
                'user_id' => $fid,
                'type' => 'payment',
                'amount' => $irt_amount,
                'data[JSON]' => [
                    'starz_rate' => $settings['starz_rate'],
                    'starz_amount' => $total_amount,
                    'amount' => $irt_amount,
                    'old' => $user['balance'],
                    'new' => $user['balance'] + $irt_amount
                ],
                'date' => time(),
                's_date' => time(),
                'tracking_code' => $telegram_charge_id,
                'getway' => 'starz'
            ]);
            $code = $db->id();

            if (
                $user["referral_id"] > 0 &&
                !text_contains($user["referral_id"], 'off') &&
                $section_status['main']['free'] &&
                $section_status['free']['gift_payment'] &&
                $fid != $user["referral_id"]
            ) {
                $gifi = (($irt_amount * $settings['gift_payment']) / 100);
                $usResult = $db->get('users_information', '*', ['user_id' => $user["referral_id"]]);
                $old_balance = $usResult['balance'];
                $new_balance = $old_balance + $gifi;
                insertTransaction('gift', $user["referral_id"], $old_balance, $new_balance, $gifi, 'GiftPayment');
                $db->update('users_information', ['gift[+]' => $gifi, 'gift_payment[+]' => $gifi], ['user_id' => $user["referral_id"]]);
                $bot->sm($user["referral_id"], $media->text('refral_gift_payment', [$fid, $first_name, $irt_amount, $gifi]));
            }

            $db->update('users_information', [
                'balance[+]' => $irt_amount,
                'amount_paid[+]' => $irt_amount
            ], ['user_id' => $fid]);

            $payment = [
                'tracking_code' => $telegram_charge_id,
                'amount' => $irt_amount,
                'data' => json_encode(['ip' => 0]),
            ];
            sm_channel('channel_transaction', ['admin_ok_payment', 'starz', $first_name, $user, $payment, 0]);
            sm_user(['starz_payment_ok', $total_amount, $irt_amount, $user]);
        }
            return true;
    }

        return true;
    }

    return false;
};