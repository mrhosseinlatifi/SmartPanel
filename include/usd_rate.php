<?php

/**
 * Fetches current USD/Toman rate from Iranian free market (TGJU).
 * Returns rate in Tomans (Rials / 10), or 0 on failure.
 */
function fetch_usd_rate()
{
    $rate = _usd_from();
    if ($rate > 0) {
        return $rate;
    }
    error_log('usd_rate: failed to fetch from source');
    return 0;
}

function _usd_from()
{
    $ch = curl_init('https://apiv2.nobitex.ir/market/stats?srcCurrency=usdt');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'Mozilla/5.0 (compatible; SmartPanelBot)',
        CURLOPT_HTTPHEADER     => ['Accept: application/json'],
    ]);
    $resp = curl_exec($ch);
    $err  = curl_errno($ch);
    curl_close($ch);

    if ($err || !$resp) {
        return 0;
    }

    $data = json_decode($resp, true);
    if (isset($data['status']) && $data['status'] == 'ok') {
        $rial = (int) str_replace(',', '', $data['stats']['usdt-rls']['latest']);
        return $rial > 0 ? (int) ($rial / 10) : 0;
    }
    return 0;
}

/**
 * Saves new USD rate and cascades the auto-update to the starz rate.
 * Dollar-priced products are handled dynamically at purchase time via the
 * per-product `is_usd` flag, so no product price rewrite is needed here.
 */
function apply_usd_rate_update($new_rate)
{
    update_option('usd_rate', $new_rate);
    update_option('usd_rate_last_update', time());

    if ((int) get_option('auto_starz_rate', 0)) {
        $starz_usd = (float) get_option('starz_rate_usd', 0);
        if ($starz_usd > 0) {
            update_option('starz_rate', round($starz_usd * $new_rate));
        }
    }
}
