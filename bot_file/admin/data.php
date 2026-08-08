<?php
function admin_data_step()
{
    extract($GLOBALS);
    switch ($data) {
        case text_starts_with($data, 'adminaccess_'):
            if (in_array($fid, admins)) {
                $str = str_replace('adminaccess_', '', $data);
                $ex = explode('-', $str);
                $main = $ex[0];
                $type = $ex[1];
                $id = $ex[2];
                $user_status = json_decode($db->get('admins', 'access', ['user_id' => $id]), 1);
                if ($user_status[$main][$type]) {
                    $user_status[$main][$type] = 0;
                    $db->update('admins', ['access[JSON]' => $user_status], ['user_id' => $id]);
                    alert_admin(['offline_status']);
                } else {
                    $user_status[$main][$type] = 1;
                    $db->update('admins', ['access[JSON]' => $user_status], ['user_id' => $id]);
                    alert_admin(['online_status']);
                }

                edk_admin(['show_access_admin', $user_status, $id]);
            } else {
                alert_admin(['not_access']);
            }
            break;
        case text_starts_with($data, 'adminupacces_'):
            $str = str_replace('adminupacces_', '', $data);
            $user_status = json_decode($db->get('admins', 'access', ['user_id' => $str]), 1);

            edk_admin(['show_access_admin', $user_status, $str]);
            alert_admin(['update_status']);
            break;
        case text_starts_with($data, 'adminapi_up_'):
            $str = str_replace('adminapi_up_', '', $data);
            $info_api = $db->get('apis', '*', ['id' => $str]);
            if ($info_api) {
                edt_admin(['edit_api_info', $info_api, true], ['edit_api_update', $info_api['id']]);
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminoff_api_'):
            $str = str_replace('adminoff_api_', '', $data);
            $ex = explode('_', $str);
            $type = $ex[0];
            $id = $ex[1];
            $infobut = $db->get('apis', $type, ['id' => $id]);
            if ($infobut == 1) {
                $of = 0;
                alert_admin(['offline_status'], false);
            } else {
                $of = 1;
                alert_admin(['online_status'], false);
            }
            $db->update('apis', [$type => $of], ['id' => $id]);

            $result = $db->select('apis', '*', ['LIMIT' => 95]);
            edk_admin(['status_api', $result]);

            break;
        case text_starts_with($data, 'adminoff_payment_'):
            $str = str_replace('adminoff_payment_', '', $data);
            $ex = explode('_', $str);
            $type = $ex[0];
            $id = $ex[1];
            $infobut = $db->get('payment_gateways', $type, ['id' => $id]);
            if ($infobut == 1) {
                $of = 0;
                alert_admin(['offline_status'], false);
            } else {
                $of = 1;
                alert_admin(['online_status'], false);
            }

            $db->update('payment_gateways', [$type => $of], ['id' => $id]);

            $result = $db->select('payment_gateways', '*');

            edk_admin(['payments_status', $result]);
            break;
        case text_starts_with($data, '/edit_'):
            $str = str_replace('/edit_', '', $data);
            $ex = explode('_', $str);
            $bot->delete_msg($fid, $message_id);
            if (in_array($admin['step'], ['edit_3', 'edit_2', 'edit_1'])) {
                $type = $ex['0'];
                $id = $ex['1'];
                switch ($type) {
                    case 'category':
                        $result = $db->get('categories', '*', ['id' => $id]);
                        if ($result['category_id'] == null) {
                            admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'category', 'id' => $id]]);

                            $un = $db->has('categories', ['category_id' => $result['id']]);

                            sm_admin(['edit_category_info', $result, $un, false], ['update_info', 'category', $result['id']]);

                            sm_admin(['edit_products_panel'], ['edit_products_panel', 'category']);
                        } else {
                            admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'under', 'id' => $id]]);

                            sm_admin(['edit_under_info', $result, false], ['update_info', 'under', $result['id']]);

                            sm_admin(['edit_products_panel'], ['edit_products_panel', 'under']);
                        }
                        break;
                    case 'product':
                        $result = $db->get('products', '*', ['id' => $id]);

                        admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'product', 'id' => $id]]);

                        sm_admin(['edit_product_info', $result, false], ['update_info', 'product', $result['id'], $result['is_usd'] ?? 0]);

                        sm_admin(['edit_products_panel'], ['edit_products_panel', 'product']);

                        break;
                }
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminpro_up_'):
            $str = str_replace('adminpro_up_', '', $data);
            $ex = explode('_', $str);
            $type = $ex['0'];
            $id = $ex['1'];

            switch ($type) {
                case 'category':
                    $result = $db->get('categories', '*', ['id' => $id]);
                    if ($result['category_id'] == null) {
                        $un = $db->has('categories', ['category_id' => $result['id']]);
                        edt_admin(['edit_category_info', $result, $un, true], ['update_info', 'category', $result['id']]);
                    } else {
                        edt_admin(['edit_under_info', $result, true], ['update_info', 'under', $result['id']]);
                    }
                    break;
                case 'product':
                    $result = $db->get('products', '*', ['id' => $id]);
                    edt_admin(['edit_product_info', $result, true], ['update_info', 'product', $result['id'], $result['is_usd'] ?? 0]);
                    break;
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminpro_usd_toggle_'):
            $prod_id = str_replace('adminpro_usd_toggle_', '', $data);
            $result  = $db->get('products', '*', ['id' => $prod_id]);
            if ($result) {
                $new_usd = $result['is_usd'] ? 0 : 1;
                $db->update('products', ['is_usd' => $new_usd], ['id' => $prod_id]);
                edt_admin(['edit_product_info', array_merge($result, ['is_usd' => $new_usd]), true], ['update_info', 'product', $prod_id, $new_usd]);
            }
            alert_admin(['none']);
            break;

        case text_starts_with($data, 'update_api_info_'):
            $str = str_replace('update_api_info_', '', $data);
            if ($admin['step'] == 'update_api_2') {
                edt_admin(['update_api_info_' . $str], ['update_api_info_confirm', $str]);
            }
            alert_admin(['none']);
            break;

        case 'update_api_back_types':
            if ($admin['step'] === 'update_api_2') {
                edt_admin(['update_api_2'], ['update_api_type_0']);
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'update_api_type_'):
            $str = str_replace('update_api_type_', '', $data);
            if ($admin['step'] == 'update_api_2') {
                edt_admin(['update_api_wait']);
                $admin_data = json_decode($admin['data'], true);
                $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);
                switch ($str) {
                    case '1':
                        $services = $api->services($result_api);
                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {

                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }
                        $admin_data['update_type'] = 1;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_1', $count]);
                        break;
                    case '2':
                        $services = $api->services($result_api);
                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {
                            if (!in_array($row['category'], $list)) {
                                $name_category = mb_substr(removeWhiteSpace($row['category']), 0, 130);
                                $text_en = js($name_category);
                                if ($db->has('categories', ['name' => $text_en])) {
                                    $count += 1;
                                    $list[] = $row['category'];
                                }
                            }
                        }
                        $count2 = 0;
                        foreach ($services['data'] as $row) {
                            if (in_array($row['category'], $list)) {
                                $count2 += 1;
                            }
                        }

                        $admin_data['update_type'] = 2;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_2', $count, $count2]);
                        break;
                    case '3':
                        $services = $api->services($result_api);
                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {

                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }

                        $admin_data['update_type'] = 3;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_3', $count]);
                        break;
                    case '4':
                        $services = $api->services($result_api);
                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {

                            if (!in_array($row['name'], $list)) {
                                $count += 1;
                                $list[] = $row['name'];
                            }
                        }

                        $admin_data['update_type'] = 4;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_4', $count]);
                        break;
                    case '5':
                        $services = $api->services($result_api);
                        $list = [];
                        $list2 = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {

                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }

                        $count2 = 0;
                        foreach ($services['data'] as $row) {
                            if (!in_array($row['name'], $list2)) {
                                $count2 += 1;
                                $list2[] = $row['name'];
                            }
                        }

                        $admin_data['update_type'] = 5;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_5', $count, $count2]);
                        break;
                    case '7':
                        $services = $api->services($result_api);
                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {
                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }
                        $admin_data['update_type'] = 7;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_7', $count]);
                        break;
                    case '6':
                        $count = $db->count('products', ['api' => $result_api['name']]);
                        $admin_data['update_type'] = 6;
                        admin_data(['step' => 'update_api_3', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_type_6', $count]);

                        break;
                    default:
                        # code...
                        break;
                }
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'update_api_ok_'):
            $str = str_replace('update_api_ok_', '', $data);
            if ($admin['step'] == 'update_api_3') {
                edt_admin(['update_api_wait']);
                $admin_data = json_decode($admin['data'], true);
                $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);
                switch ($str) {
                    case '1':
                        $services = $api->services($result_api);

                        $list = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {
                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }
                        $add_count = 0;

                        foreach ($list as $service) {

                            $name_category = mb_substr(removeWhiteSpace($service), 0, 130);
                            $text_en = js($name_category);
                            if (!$db->has('categories', ['name' => $text_en, 'category_id' => null])) {

                                $ordering = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                                $ordering += 1;
                                $in_data = [
                                    'name' => $text_en,
                                    'category_id' => null,
                                    'status' => 1,
                                    'ordering' => $ordering,
                                ];
                                $db->insert('categories', $in_data);
                                if ($db->id()) {
                                    $add_count += 1;
                                }
                            }
                        }
                        admin_step('products');
                        sm_admin(['update_api_ok', 1, $add_count], ['products_panel']);
                        break;
                    case '2':
                        $p_s = ['up' => 0, 'round' => 0, 'convert' => 0, 'next' => 0];
                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_product_settings', 1, $p_s]);
                        break;
                    case '3':
                        $services = $api->services($result_api);

                        $list = [];
                        $categories = [];
                        $count = 0;
                        foreach ($services['data'] as $row) {
                            if (!in_array($row['category'], $list)) {
                                $count += 1;
                                $list[] = $row['category'];
                            }
                        }
                        $add_count = 0;
                        $i = 0;
                        foreach ($list as $service) {

                            $name_category = mb_substr(removeWhiteSpace($service), 0, 130);
                            $text_en = js($name_category);
                            if (!$db->has('categories', ['name' => $text_en])) {
                                $categories[$i] = $name_category;
                                $i += 1;
                            }
                        }

                        file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($categories));
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'category', 1]);
                        break;
                    case '4':
                        if ($db->has('categories', ['id[>]' => 0])) {

                            $p_s = ['up' => 0, 'round' => 0, 'convert' => 0, 'next' => 0];
                            $admin_data['p_s'] = $p_s;
                            admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                            edt_admin(['update_api_2'], ['update_api_product_settings', 4, $p_s]);
                        } else {
                            admin_step('products');
                            sm_admin(['error_add_products_auto_1']);
                        }
                        break;
                    case '5':
                        $p_s = ['up' => 0, 'round' => 0, 'convert' => 0, 'next' => 0];
                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_product_settings', 5, $p_s]);
                        break;
                    case '7':
                        $p_s = ['up' => 0, 'round' => 0, 'convert' => 0, 'next' => 0];
                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_product_settings', 7, $p_s]);
                        break;
                    case '6':
                        $p_s = ['name' => 0, 'price' => 1, 'price_type' => 1, 'min' => 1, 'info' => 0, 'up' => 0, 'round' => 0, 'next' => 0, 'convert' => 0];
                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2_type6'], ['update_api_product_settings', 6, $p_s]);
                        break;
                    default:
                        # code...
                        break;
                }
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'update_api_add_'):
            $str = str_replace('update_api_add_', '', $data);
            if ($admin['step'] == 'update_api_4') {
                $admin_data = json_decode($admin['data'], true);
                $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);

                $explode = explode('_', $str);
                $type = $explode[0];
                $id = $explode[1];
                switch ($type) {
                    case 'category':
                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);
                        $name_category = mb_substr(removeWhiteSpace($data_file[$id]), 0, 130);
                        $text_en = js($name_category);

                        if (isset($admin_data['update_type']) && $admin_data['update_type'] == 7) {
                            $page_number = isset($admin_data['page_number']) ? max(1, (int) $admin_data['page_number']) : 1;
                            $category_exists = $db->has('categories', ['name' => $text_en, 'category_id' => null]);
                            $category_id = $db->get('categories', 'id', ['name' => $text_en, 'category_id' => null]);
                            if (!$category_exists) {
                                $ordering = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                                $ordering += 1;
                                $db->insert('categories', [
                                    'name' => $text_en,
                                    'category_id' => null,
                                    'status' => 1,
                                    'ordering' => $ordering,
                                ]);
                                $category_id = $db->id();
                            }

                            if ($category_id) {
                                $services = $api->services($result_api);
                                $usd_rate = $settings['usd_rate'];
                                $add_count = 0;
                                $added_products = [];

                                foreach ($services['data'] as $row) {
                                    $category_value = mb_substr(removeWhiteSpace($row['category']), 0, 130);
                                    if ($category_value !== $name_category) {
                                        continue;
                                    }

                                    $name_product = mb_substr(removeWhiteSpace($row['name']), 0, 130);
                                    $text_product = js($name_product);

                                    if (!$db->has('products', ['service' => $row['service'], 'api' => $result_api['name'], 'name' => $text_product, 'category_id' => $category_id])) {
                                        $orig_rate = $row['rate'];
                                        $rate = $row['rate'];
                                        if ($admin_data['p_s']['convert'] && $usd_rate > 0) {
                                            $rate *= $usd_rate;
                                        }
                                        if ($admin_data['p_s']['up'] != 0) {
                                            $rate = up_price($rate, $admin_data['p_s']['up']);
                                        }
                                        if ($admin_data['p_s']['round'] != 0) {
                                            $rate = round_up($rate, $admin_data['p_s']['round']);
                                        }

                                        $price = $rate;
                                        $min = $row['min'];
                                        $max = $row['max'];
                                        $info = removeWhiteSpace($row['desc'] ?? '');
                                        $ser_id = $row['service'];
                                        $ordering = (int) $db->max('products', 'ordering', ['category_id' => $category_id]);
                                        $ordering += 1;

                                        $product_type = 'default';
                                        if (isset($row['type']) && !empty($row['type'])) {
                                            $api_type = strtolower(trim($row['type']));
                                            if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                                $product_type = $api_type;
                                            }
                                        }

                                        $db->insert('products', [
                                            'name' => $text_product,
                                            'price' => $price,
                                            'price_usd' => $admin_data['p_s']['convert'] ? (float) $orig_rate : 0,
                                            'cost_price' => $admin_data['p_s']['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate,
                                            'min' => $min,
                                            'max' => $max,
                                            'info' => $info,
                                            'api' => $result_api['name'],
                                            'service' => $ser_id,
                                            'category_id' => $category_id,
                                            'ordering' => $ordering,
                                            'type' => $product_type,
                                        ]);
                                        if ($db->id()) {
                                            $add_count += 1;
                                            $added_products[] = [
                                                'name'       => json_decode($text_product),
                                                'service'    => $ser_id,
                                                'cost_price' => $admin_data['p_s']['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate,
                                                'price'      => (float) $price,
                                                'unit_price' => (float) price_once($min, $max, $price, $product_type),
                                                'price_usd'  => $admin_data['p_s']['convert'] ? (float) $orig_rate : 0,
                                                'min'        => (int) $min,
                                                'max'        => (int) $max,
                                            ];
                                        }
                                    }
                                }

                                unset($data_file[$id]);
                                $data_file = array_values($data_file);
                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($data_file));

                                $report_key = save_update_log([
                                    'type' => 7,
                                    'type_label' => $key_admin['update_api_type']['type_7'],
                                    'api' => json_decode($result_api['name']),
                                    'category' => $name_category,
                                    'category_new' => !$category_exists,
                                    'add_count' => $add_count,
                                    'products' => $added_products,
                                ]);
                                $report_url = 'https://' . $domin . '/update_report.php?k=' . $report_key;

                                $total_categories = count($data_file);
                                if ($total_categories > 0) {
                                    $page_number = min($page_number, max(1, ceil($total_categories / 20)));
                                    $admin_data['page_number'] = $page_number;
                                    admin_data(['data[JSON]' => $admin_data]);
                                    alert_admin(['update_api_ok', 7, $add_count, $category_exists ? 1 : 0]);
                                    sm_admin(['update_report_link', $report_url, 'category', $name_category]);
                                    edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'category', $page_number]);
                                } else {
                                    admin_step('products');
                                    sm_admin(['update_api_ok', 7, $add_count, $category_exists ? 1 : 0], ['products_panel']);
                                    sm_admin(['update_report_link', $report_url, 'final']);
                                }
                            } else {
                                alert_admin(['update_api_product_error_1']);
                            }
                            break;
                        }

                        if (!$db->has('categories', ['name' => $text_en, 'category_id' => null])) {
                            $admin_data['category_id'] = $id;
                            admin_data(['step' => 'update_api_5', 'data[JSON]' => $admin_data]);
                            edt_admin(['update_api_add_category_1'], ['type_add_category']);
                            alert_admin(['none']);
                        } else {
                            alert_admin(['update_api_product_error_1']);
                        }
                        break;
                    case 'sub-category':

                        break;
                    case 'page':
                        $page_number = $id;

                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);
                        $admin_data['page_number'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'category', $page_number]);
                        break;
                    case 'page2':
                        $page_number = $id;

                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);
                        $admin_data['page_number'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'sub_category', $page_number]);
                        break;
                    case 'page3':
                        $page_number = $id;

                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);
                        $admin_data['page_number'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'product', $page_number]);
                        break;
                    case 'product':
                        /** List Category id == null */
                        $result = $db->select('categories', ['name', 'id'], ['category_id' => null]);

                        foreach ($result as $row) {
                            $categories[$row['id']] = json_decode($row['name']);
                        }
                        $admin_data['page_number_2'] = 1;
                        $admin_data['product_id'] = $id;
                        admin_data(['step' => 'update_api_6', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'product', 1]);
                        break;
                    default:
                        # code...
                        break;
                }
            } elseif ($admin['step'] == 'update_api_6') {
                $admin_data = json_decode($admin['data'], true);
                $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);

                $explode = explode('_', $str);
                $type = $explode[0];
                $id = $explode[1];
                switch ($type) {
                    case 'category':
                        break;
                    case 'sub-category':
                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);

                        $category_id = $id;

                        $id = $admin_data['category_id'];


                        $name_category = mb_substr(removeWhiteSpace($data_file[$id]), 0, 130);
                        $text_en = js($name_category);
                        if (!$db->has('categories', ['name' => $text_en, 'category_id' => $category_id])) {

                            $ordering = (int) $db->max('categories', 'ordering', ['category_id' => $category_id]);
                            $ordering += 1;
                            $in_data = [
                                'name' => $text_en,
                                'category_id' => $category_id,
                                'status' => 1,
                                'ordering' => $ordering,
                            ];
                            $db->insert('categories', $in_data);
                            if ($db->id()) {
                                unset($data_file[$id]);
                                edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'category', $admin_data['page_number_2']]);

                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($data_file));
                                admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                alert_admin(['update_api_product_ok'], true);
                            }
                        } else {
                            alert_admin(['update_api_product_error_1'], true);
                        }
                        break;
                    case 'page':
                        break;
                    case 'page2':
                        /** List Category id == null */
                        $result = $db->select('categories', ['name', 'id'], ['category_id' => null]);

                        foreach ($result as $row) {
                            $categories[$row['id']] = json_decode($row['name']);
                        }
                        $admin_data['page_number_2'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'sub_category', $id]);
                        break;
                    case 'page3':
                        /** List Category id == null */
                        $result = $db->select('categories', ['name', 'id'], ['category_id' => null]);

                        foreach ($result as $row) {
                            $categories[$row['id']] = json_decode($row['name']);
                        }
                        $admin_data['page_number_2'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'product', $id]);
                        break;
                    case 'product':
                        if ($db->has('categories', ['category_id' => $id])) {
                            /** List Category id == null */
                            $result = $db->select('categories', ['name', 'id'], ['category_id' => $id]);

                            foreach ($result as $row) {
                                $categories[$row['id']] = json_decode($row['name']);
                            }
                            $admin_data['page_number_3'] = 1;
                            $admin_data['category_id'] = $id;
                            admin_data(['step' => 'update_api_7', 'data[JSON]' => $admin_data]);
                            edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'product', 1]);
                        } else {
                            $p_s = $admin_data['p_s'];
                            $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);

                            $services = $api->services($result_api);

                            $true = true;
                            $usd_rate = $settings['usd_rate'];
                            foreach ($services['data'] as $row) {
                                if ($row['service'] == $admin_data['product_id']) {
                                    $name_product = mb_substr(removeWhiteSpace($row['name']), 0, 130);
                                    $text_en = js($name_product);

                                    if (!$db->has('products', ['service' => $row['service'], 'api' => $result_api['name'], 'name' => $text_en, 'category_id' => $id])) {
                                        $orig_rate = $row['rate'];
                                        $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                        $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                        $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                        $row['_price_usd'] = $p_s['convert'] ? (float) $orig_rate : 0;
                                        $row['_cost_price'] = $p_s['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate;
                                        $product = $row;
                                        $true = true;
                                    } else {
                                        $true = false;
                                    }
                                    break;
                                } else {
                                    $true = false;
                                }
                            }

                            if ($true) {
                                $name_product = mb_substr(removeWhiteSpace($product['name']), 0, 130);
                                $text_en = js($name_product);

                                $ordering = (int) $db->max('products', 'ordering', ['category_id' => $id]);
                                $ordering += 1;

                                $price = $product['rate'];
                                $min = $product['min'];
                                $max = $product['max'];
                                $desc = removeWhiteSpace($product['desc'] ?? '');
                                $ser_id = $product['service'];

                                $product_type = 'default';
                                if (isset($product['type']) && !empty($product['type'])) {
                                    $api_type = strtolower(trim($product['type']));
                                    if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                        $product_type = $api_type;
                                    }
                                }

                                $db->insert('products', [
                                    'name' => $text_en,
                                    'price' => $price,
                                    'price_usd' => $product['_price_usd'] ?? 0,
                                    'cost_price' => $product['_cost_price'] ?? 0,
                                    'min' => $min,
                                    'max' => $max,
                                    'info' => $desc,
                                    'api' => $result_api['name'],
                                    'service' => $ser_id,
                                    'category_id' => $id,
                                    'ordering' => $ordering,
                                    'type' => $product_type,
                                ]);
                                if ($db->id()) {
                                    unset($data_file[$ser_id]);
                                    admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                    edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'product', $admin_data['page_number']]);
                                    file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($data_file));

                                    alert_admin(['update_api_product_ok'], true);
                                } else {

                                    alert_admin(['update_api_product_nok'], true);
                                }
                            } else {
                                alert_admin(['update_api_product_error_1']);
                            }
                        }
                        break;
                    default:
                        # code...
                        break;
                }
            } elseif ($admin['step'] == 'update_api_7') {
                $admin_data = json_decode($admin['data'], true);
                $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);

                $explode = explode('_', $str);
                $type = $explode[0];
                $id = $explode[1];

                switch ($type) {
                    case 'page3':
                        /** List Category id == null */
                        $result = $db->select('categories', ['name', 'id'], ['category_id' => $admin_data['category_id']]);

                        foreach ($result as $row) {
                            $categories[$row['id']] = json_decode($row['name']);
                        }
                        $admin_data['page_number_3'] = $id;
                        admin_data(['data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'product', $id]);
                        break;
                    case 'product':
                        $p_s = $admin_data['p_s'];
                        $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);

                        $services = $api->services($result_api);

                        $true = true;
                        $usd_rate = $settings['usd_rate'];
                        foreach ($services['data'] as $row) {
                            if ($row['service'] == $admin_data['product_id']) {
                                $name_product = mb_substr(removeWhiteSpace($row['name']), 0, 130);
                                $text_en = js($name_product);

                                if (!$db->has('products', ['service' => $row['service'], 'api' => $result_api['name'], 'name' => $text_en, 'category_id' => $id])) {
                                    $orig_rate = $row['rate'];
                                    $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                    $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                    $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                    $row['_price_usd'] = $p_s['convert'] ? (float) $orig_rate : 0;
                                    $row['_cost_price'] = $p_s['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate;
                                    $product = $row;
                                    $true = true;
                                } else {
                                    $true = false;
                                }
                                break;
                            } else {
                                $true = false;
                            }
                        }

                        if ($true) {
                            $name_product = mb_substr(removeWhiteSpace($product['name']), 0, 130);
                            $text_en = js($name_product);

                            $ordering = (int) $db->max('products', 'ordering', ['category_id' => $id]);
                            $ordering += 1;

                            $price = $product['rate'];
                            $min = $product['min'];
                            $max = $product['max'];
                            $desc = removeWhiteSpace($product['desc'] ?? '');
                            $ser_id = $product['service'];

                            $product_type = 'default';
                            if (isset($product['type']) && !empty($product['type'])) {
                                $api_type = strtolower(trim($product['type']));
                                if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                    $product_type = $api_type;
                                }
                            }

                            $db->insert('products', [
                                'name' => $text_en,
                                'price' => $price,
                                'price_usd' => $product['_price_usd'] ?? 0,
                                'cost_price' => $product['_cost_price'] ?? 0,
                                'min' => $min,
                                'max' => $max,
                                'info' => $desc,
                                'api' => $result_api['name'],
                                'service' => $ser_id,
                                'category_id' => $id,
                                'ordering' => $ordering,
                                'type' => $product_type,
                            ]);
                            if ($db->id()) {
                                unset($data_file[$ser_id]);
                                admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'product', $admin_data['page_number']]);
                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($data_file));

                                alert_admin(['update_api_product_ok'], true);
                            } else {
                                alert_admin(['update_api_product_nok'], true);
                            }
                        } else {
                            alert_admin(['update_api_product_error_1'], true);
                        }
                        break;
                }
            }
            break;
        case text_starts_with($data, 'add_api_'):
            $str = str_replace('add_api_', '', $data);
            if ($admin['step'] == 'update_api_5') {
                $admin_data = json_decode($admin['data'], true);
                $data_file = json_decode(file_get_contents(ROOTPATH . '/temp/' . $fid . '.json'), true);

                switch ($str) {
                    case 'category':
                        $id = $admin_data['category_id'];
                        $name_category = mb_substr(removeWhiteSpace($data_file[$id]), 0, 130);
                        $text_en = js($name_category);
                        if (!$db->has('categories', ['name' => $text_en, 'category_id' => null])) {

                            $ordering = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                            $ordering += 1;
                            $in_data = [
                                'name' => $text_en,
                                'category_id' => null,
                                'status' => 1,
                                'ordering' => $ordering,
                            ];
                            $db->insert('categories', $in_data);
                            if ($db->id()) {
                                unset($data_file[$id]);
                                edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'category', $admin_data['page_number']]);

                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($data_file));
                                admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                alert_admin(['update_api_product_ok'], true);
                            }
                        } else {
                            alert_admin(['update_api_product_error_1'], true);
                        }
                        break;
                    case 'sub_category':
                        /** List Category id == null */
                        $result = $db->select('categories', ['name', 'id'], ['category_id' => null]);

                        foreach ($result as $row) {
                            $categories[$row['id']] = json_decode($row['name']);
                        }
                        $admin_data['page_number_2'] = 1;
                        admin_data(['step' => 'update_api_6', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'sub_category', 1]);
                        break;
                    case 'cancel':
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_select_product', $data_file, 'category', $admin_data['page_number']]);
                        break;
                }
            }
            alert_admin(['none']);
            break;
        case 'rate_toggle_usdauto':
            $cur = get_option('usd_rate_mode', 'manual');
            update_option('usd_rate_mode', $cur === 'auto' ? 'manual' : 'auto');
            render_rate_panel($message_id);
            alert_admin(['none']);
            break;
        case 'rate_toggle_starzauto':
            $new_starzauto = get_option('auto_starz_rate', 0) ? 0 : 1;
            update_option('auto_starz_rate', $new_starzauto);
            if ($new_starzauto) {
                $starz_usd = (float) get_option('starz_rate_usd', 0);
                $usd_rate  = (float) get_option('usd_rate', 0);
                if ($starz_usd > 0 && $usd_rate > 0) {
                    update_option('starz_rate', round($starz_usd * $usd_rate));
                }
            }
            render_rate_panel($message_id);
            alert_admin(['none']);
            break;

        case 'rate_fetch_now':
            $new_rate = fetch_usd_rate();
            if ($new_rate > 0) {
                apply_usd_rate_update($new_rate);
                render_rate_panel($message_id);
                alert_admin(['usd_rate_updated', $new_rate], true);
            } else {
                alert_admin(['usd_rate_update_failed'], true);
            }
            break;

        case 'rate_set_usd':
            admin_data(['step' => 'rate_input', 'data[JSON]' => ['field' => 'usd', 'msgid' => $message_id]]);
            edt_admin(['rate_ask_usd', number_format((float) get_option('usd_rate', 0))], ['rate_prompt_back'], $message_id);
            alert_admin(['none']);
            break;

        case 'rate_set_starz':
            admin_data(['step' => 'rate_input', 'data[JSON]' => ['field' => 'starz', 'msgid' => $message_id]]);
            edt_admin(['rate_ask_starz', number_format((float) get_option('starz_rate', 0))], ['rate_prompt_back'], $message_id);
            alert_admin(['none']);
            break;

        case 'rate_set_starz_usd':
            admin_data(['step' => 'rate_input', 'data[JSON]' => ['field' => 'starz_usd', 'msgid' => $message_id]]);
            edt_admin(['rate_ask_starz_usd', number_format((float) get_option('starz_rate_usd', 0), 4)], ['rate_prompt_back'], $message_id);
            alert_admin(['none']);
            break;

        case 'rate_set_interval':
            admin_data(['step' => 'rate_input', 'data[JSON]' => ['field' => 'interval', 'msgid' => $message_id]]);
            edt_admin(['rate_ask_interval', (int) get_option('usd_rate_interval', 60)], ['rate_prompt_back'], $message_id);
            alert_admin(['none']);
            break;

        case 'rate_back':
            admin_step('payments');
            render_rate_panel($message_id);
            alert_admin(['none']);
            break;

        case 'admin_add_product_confirm':
            if ($admin['step'] === 'add_product_confirm') {
                $admin_data = json_decode($admin['data'], true);
                $pending = $admin_data['pending'] ?? null;
                if ($pending) {
                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $pending['category_id']]);
                    $ordering += 1;
                    $insert_data = $pending;
                    $next = $insert_data['next'];
                    $next_variant = $insert_data['next_variant'] ?? 0;
                    unset($insert_data['next'], $insert_data['next_variant']);
                    $insert_data['ordering'] = $ordering;

                    $db->insert('products', $insert_data);
                    $insert_id = $db->id();

                    if ($insert_id) {
                        $admin_data['id'] = $insert_id;
                        unset($admin_data['pending']);

                        $bot->edit_text($fid, $message_id, $media_admin->atext('product_add_confirmed_alert'), ['inline_keyboard' => []]);

                        if ($next === 'type') {
                            admin_data(['step' => 'add_product_type', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_type'], ['product_service_type_panel']);
                        } else {
                            $btn = $db->get('categories', '*', ['id' => $pending['category_id']]);
                            $name_show = json_decode($insert_data['name']);
                            admin_data(['step' => 'add_product_6', 'data[JSON]' => $admin_data]);
                            sm_admin(['product_add_5', $btn['id'], $name_show, $insert_data['price'], $insert_data['min'], $insert_data['max']], ['skip_back_panel', $next_variant]);
                        }
                        alert_admin(['product_add_confirmed_alert'], true);
                    } else {
                        $bot->edit_text($fid, $message_id, $media_admin->atext('product_add_error_1'), ['inline_keyboard' => []]);
                        alert_admin(['product_add_error_1'], true);
                    }
                } else {
                    $bot->edit_text($fid, $message_id, $media_admin->atext('product_preview_expired'), ['inline_keyboard' => []]);
                    alert_admin(['none']);
                }
            } else {
                $bot->edit_text($fid, $message_id, $media_admin->atext('product_preview_expired'), ['inline_keyboard' => []]);
                alert_admin(['none']);
            }
            break;

        case 'admin_add_product_retry':
            if ($admin['step'] === 'add_product_confirm') {
                $admin_data = json_decode($admin['data'], true);
                unset($admin_data['pending']);

                if (empty($admin_data['api'])) {
                    $variant = 0;
                } else {
                    $result_api = $db->get('apis', '*', ['id' => $admin_data['api']]);
                    $variant = ($result_api && $result_api['smart_panel']) ? 1 : 2;
                }

                $bot->edit_text($fid, $message_id, $media_admin->atext('product_add_retry_wait'), ['inline_keyboard' => []]);

                admin_data(['step' => 'add_product_5', 'data[JSON]' => $admin_data]);
                sm_admin(['product_add_4', $variant], ['back_panel']);
            } else {
                $bot->edit_text($fid, $message_id, $media_admin->atext('product_preview_expired'), ['inline_keyboard' => []]);
            }
            alert_admin(['none']);
            break;

        case text_starts_with($data, 'adminupdate_'):
            $str = str_replace('adminupdate_', '', $data);
            $admin_data = json_decode($admin['data'], true);
            if ($admin['step'] == 'update_api_4') {
                switch ($str) {
                    case 'next':
                        edt_admin(['update_api_wait']);
                        $result_api = $db->get('apis', '*', ['id' => $admin_data['api_id']]);
                        $p_s = $admin_data['p_s'];

                        switch ($admin_data['update_type']) {
                            case '2':

                                $services = $api->services($result_api);
                                $list = [];
                                $products = [];
                                $ids = [];

                                foreach ($services['data'] as $row) {
                                    if (!in_array($row['category'], $list)) {
                                        $name_category = mb_substr(removeWhiteSpace($row['category']), 0, 130);
                                        $text_en = js($name_category);
                                        $id = $db->get('categories', 'id', ['name' => $text_en]);
                                        if ($id) {
                                            $list[] = $row['category'];
                                            $ids[$row['category']] = $id;
                                        }
                                    }
                                }
                                $usd_rate = $settings['usd_rate'];
                                foreach ($services['data'] as $row) {
                                    if (in_array($row['category'], $list)) {
                                        $name_product = mb_substr(removeWhiteSpace($row['name']), 0, 130);
                                        $text_en = js($name_product);
                                        $cate = $ids[$row['category']];

                                        if (!$db->has('products', ['service' => $row['service'], 'api' => $result_api['name'], 'name' => $text_en, 'category_id' => $cate])) {
                                            $orig_rate = $row['rate'];
                                            $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                            $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                            $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                            $row['_price_usd'] = $p_s['convert'] ? (float) $orig_rate : 0;
                                            $row['_cost_price'] = $p_s['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate;
                                            $products[] = $row;
                                        }
                                    }
                                }

                                $add_count = 0;
                                $not = 0;
                                $added_products = [];
                                foreach ($products as $value) {
                                    $name_product = mb_substr(removeWhiteSpace($value['name']), 0, 130);
                                    $text_en = js($name_product);
                                    $cate = $ids[$value['category']];

                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $cate]);
                                    $ordering += 1;

                                    $price = $value['rate'];
                                    $min = $value['min'];
                                    $max = $value['max'];
                                    $info = removeWhiteSpace($value['desc'] ?? '');
                                    $ser_id = $value['service'];

                                    $product_type = 'default';
                                    if (isset($value['type']) && !empty($value['type'])) {
                                        $api_type = strtolower(trim($value['type']));
                                        if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                            $product_type = $api_type;
                                        }
                                    }

                                    $db->insert('products', [
                                        'name' => $text_en,
                                        'price' => $price,
                                        'price_usd' => $value['_price_usd'] ?? 0,
                                        'cost_price' => $value['_cost_price'] ?? 0,
                                        'min' => $min,
                                        'max' => $max,
                                        'info' => $info,
                                        'api' => $result_api['name'],
                                        'service' => $ser_id,
                                        'category_id' => $cate,
                                        'ordering' => $ordering,
                                        'type' => $product_type,
                                    ]);
                                    $insert = $db->id();
                                    if ($insert) {
                                        $add_count += 1;
                                        $added_products[] = [
                                            'name'       => json_decode($text_en),
                                            'service'    => $ser_id,
                                            'cost_price' => (float) ($value['_cost_price'] ?? 0),
                                            'price'      => (float) $price,
                                            'unit_price' => (float) price_once($min, $max, $price, $product_type),
                                            'price_usd'  => (float) ($value['_price_usd'] ?? 0),
                                            'min'        => (int) $min,
                                            'max'        => (int) $max,
                                        ];
                                    }
                                }
                                $report_key = save_update_log([
                                    'type' => 2,
                                    'type_label' => $key_admin['update_api_type']['type_2'],
                                    'api' => json_decode($result_api['name']),
                                    'add_count' => $add_count,
                                    'products' => $added_products,
                                ]);
                                admin_step('products');
                                sm_admin(['update_api_ok', 2, $add_count], ['products_panel']);
                                sm_admin(['update_report_link', 'https://' . $domin . '/update_report.php?k=' . $report_key, 'update']);
                                break;
                            case '4':

                                $services = $api->services($result_api);

                                $list = [];
                                foreach ($services['data'] as $service) {

                                    $name_category = mb_substr(removeWhiteSpace($service['name']), 0, 130);
                                    $text_en = js($name_category);
                                    if (!$db->has('products', ['name' => $text_en, 'api' => $result_api['name']])) {
                                        $list[$service['service']] = $name_category;
                                    }
                                }
                                $admin_data['page_number'] = 1;
                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($list));
                                admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                edt_admin(['update_api_2'], ['update_api_select_product', $list, 'product', 1]);

                                break;
                            case '5':

                                $services = $api->services($result_api);
                                $list = [];
                                $products = [];
                                $ids = [];

                                foreach ($services['data'] as $row) {
                                    if (!in_array($row['category'], $list)) {
                                        $name_category = mb_substr(removeWhiteSpace($row['category']), 0, 130);
                                        $text_en = js($name_category);
                                        $id = $db->get('categories', 'id', ['name' => $text_en]);
                                        if ($id) {
                                            $list[] = $row['category'];
                                            $ids[$row['category']] = $id;
                                        } else {
                                            $ordering = (int) $db->max('categories', 'ordering', ['category_id' => null]);
                                            $ordering += 1;

                                            $in_data = [
                                                'name' => $text_en,
                                                'category_id' => null,
                                                'status' => 1,
                                                'ordering' => $ordering,
                                            ];
                                            $db->insert('categories', $in_data);

                                            $id = $db->id();
                                            if ($id) {
                                                $list[] = $row['category'];
                                                $ids[$row['category']] = $id;
                                            }
                                        }
                                    }
                                }
                                $usd_rate = $settings['usd_rate'];
                                foreach ($services['data'] as $row) {
                                    if (in_array($row['category'], $list)) {
                                        $name_product = mb_substr(removeWhiteSpace($row['name']), 0, 130);
                                        $text_en = js($name_product);
                                        $cate = $ids[$row['category']];

                                        $id = $db->has('products', ['service' => $row['service'], 'api' => $result_api['name'], 'name' => $text_en, 'category_id' => $cate]);
                                        if (!$id) {
                                            $orig_rate = $row['rate'];
                                            $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                            $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                            $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                            $row['_price_usd'] = $p_s['convert'] ? (float) $orig_rate : 0;
                                            $row['_cost_price'] = $p_s['convert'] ? (float) ($orig_rate * $usd_rate) : (float) $orig_rate;
                                            $products[] = $row;
                                        }
                                    }
                                }

                                $add_count = 0;
                                $not = 0;
                                $added_products = [];
                                foreach ($products as $value) {
                                    $name_product = mb_substr(removeWhiteSpace($value['name']), 0, 130);
                                    $text_en = js($name_product);
                                    $cate = $ids[$value['category']];

                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $cate]);
                                    $ordering += 1;

                                    $price = $value['rate'];
                                    $min = $value['min'];
                                    $max = $value['max'];
                                    $info = removeWhiteSpace($value['desc'] ?? '');
                                    $ser_id = $value['service'];

                                    $product_type = 'default';
                                    if (isset($value['type']) && !empty($value['type'])) {
                                        $api_type = strtolower(trim($value['type']));
                                        if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                            $product_type = $api_type;
                                        }
                                    }

                                    $db->insert('products', [
                                        'name' => $text_en,
                                        'price' => $price,
                                        'price_usd' => $value['_price_usd'] ?? 0,
                                        'cost_price' => $value['_cost_price'] ?? 0,
                                        'min' => $min,
                                        'max' => $max,
                                        'info' => $info,
                                        'api' => $result_api['name'],
                                        'service' => $ser_id,
                                        'category_id' => $cate,
                                        'ordering' => $ordering,
                                        'type' => $product_type,
                                    ]);
                                    $insert = $db->id();
                                    if ($insert) {
                                        $add_count += 1;
                                        $added_products[] = [
                                            'name'       => json_decode($text_en),
                                            'service'    => $ser_id,
                                            'cost_price' => (float) ($value['_cost_price'] ?? 0),
                                            'price'      => (float) $price,
                                            'unit_price' => (float) price_once($min, $max, $price, $product_type),
                                            'price_usd'  => (float) ($value['_price_usd'] ?? 0),
                                            'min'        => (int) $min,
                                            'max'        => (int) $max,
                                        ];
                                    }
                                }
                                $report_key = save_update_log([
                                    'type' => 5,
                                    'type_label' => $key_admin['update_api_type']['type_5'],
                                    'api' => json_decode($result_api['name']),
                                    'add_count' => $add_count,
                                    'products' => $added_products,
                                ]);
                                admin_step('products');
                                sm_admin(['update_api_ok', 2, $add_count], ['products_panel']);
                                sm_admin(['update_report_link', 'https://' . $domin . '/update_report.php?k=' . $report_key, 'update']);

                                break;
                            case '6':
                                $add_count = 0;
                                $off_count = 0;
                                $disabled_products = [];

                                $services = $api->services($result_api);
                                $products = [];
                                $usd_rate = $settings['usd_rate'];
                                foreach ($services['data'] as $row) {
                                    $products[$row['service']] = $row;
                                }
                                $result_products = $db->select('products', '*', ['api' => $result_api['name']]);
                                $updated_products = [];
                                foreach ($result_products as $key => $value) {
                                    if (!isset($products[$value['service']])) {
                                        if ($p_s['min']) {
                                            $db->update('products', ['status' => 0], ['id' => $value['id']]);
                                            $off_count += 1;
                                            $disabled_products[] = [
                                                'id'      => $value['id'],
                                                'service' => $value['service'],
                                                'name'    => json_decode($value['name']),
                                            ];
                                        }
                                        continue;
                                    }

                                    $data_p = $products[$value['service']];
                                    $s = [];

                                    if ($p_s['name']) {
                                        $name_product = mb_substr(removeWhiteSpace($data_p['name']), 0, 130);
                                        $text_en = js($name_product);
                                        $s['name'] = $text_en;
                                    }

                                    if ($p_s['price']) {
                                        $price = $data_p['rate'];
                                        $price = ($p_s['convert'] && $usd_rate > 0) ? $price * $usd_rate : $price;

                                        if ($p_s['up'] != 0) {
                                            $price = up_price($price, $p_s['up']);
                                        }

                                        if ($p_s['round'] != 0) {
                                            $price = round_up($price, $p_s['round']);
                                        }

                                        if ($p_s['price_type'] == 1 && $price < $value['price']) {
                                            $price = $value['price'];
                                        }

                                        $s['price'] = $price;
                                        $s['price_usd'] = $p_s['convert'] ? (float) $data_p['rate'] : 0;
                                        $s['cost_price'] = $p_s['convert'] ? (float) ($data_p['rate'] * $usd_rate) : (float) $data_p['rate'];
                                    }

                                    if ($p_s['min']) {
                                        $s['min'] = $data_p['min'];
                                        $s['max'] = $data_p['max'];
                                        $s['status'] = 1;
                                    }

                                    if ($p_s['info']) {
                                        $s['info'] = removeWhiteSpace($data_p['desc'] ?? '');
                                    }

                                    if (isset($data_p['type']) && !empty($data_p['type'])) {
                                        $api_type = strtolower(trim($data_p['type']));
                                        if (in_array($api_type, ['default', 'custom_comments', 'package'])) {
                                            $s['type'] = $api_type;
                                        }
                                    }

                                    if (!empty($s)) {
                                        $db->update('products', $s, ['id' => $value['id']]);
                                        $add_count += 1;

                                        $u_new_price = isset($s['price']) ? (float) $s['price'] : (float) $value['price'];
                                        $u_min       = (int) ($s['min'] ?? $value['min']);
                                        $u_max       = (int) ($s['max'] ?? $value['max']);
                                        $u_type      = $s['type'] ?? $value['type'] ?? 'default';
                                        $u_cost      = (float) ($s['cost_price'] ?? $value['cost_price'] ?? 0);

                                        $updated_products[] = [
                                            'name'       => json_decode($s['name'] ?? $value['name']),
                                            'service'    => $value['service'],
                                            'cost_price' => $u_cost,
                                            'old_price'  => (float) $value['price'],
                                            'new_price'  => $u_new_price,
                                            'unit_price' => (float) price_once($u_min, $u_max, $u_new_price, $u_type),
                                            'price_usd'  => (float) ($s['price_usd'] ?? $value['price_usd'] ?? 0),
                                            'min'        => $u_min,
                                            'max'        => $u_max,
                                        ];
                                    }
                                }

                                $report_key = save_update_log([
                                    'type' => 6,
                                    'type_label' => $key_admin['update_api_type']['type_6'],
                                    'api' => json_decode($result_api['name']),
                                    'add_count' => $add_count,
                                    'off_count' => $off_count,
                                    'settings' => $p_s,
                                    'disabled_products' => $disabled_products,
                                    'products' => $updated_products,
                                ]);
                                admin_step('products');
                                sm_admin(['update_api_ok', 6, $add_count, $off_count], ['products_panel']);
                                sm_admin(['update_report_link', 'https://' . $domin . '/update_report.php?k=' . $report_key, 'update']);
                                break;
                            case '7':
                                $services = $api->services($result_api);
                                $list = [];
                                foreach ($services['data'] as $row) {
                                    if (!in_array($row['category'], $list)) {
                                        $list[] = $row['category'];
                                    }
                                }
                                if (empty($list)) {
                                    admin_step('products');
                                    sm_admin(['update_api_error_1']);
                                    break;
                                }
                                file_put_contents(ROOTPATH . '/temp/' . $fid . '.json', json_encode($list));
                                admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                                edt_admin(['update_api_2'], ['update_api_select_product', $list, 'category', 1]);
                                break;
                        }
                        break;
                    case 'name':
                    case 'price':
                    case 'min':
                    case 'info':
                    case 'convert':
                        $p_s = $admin_data['p_s'];
                        $p_s[$str] = (!empty($p_s[$str])) ? 0 : 1;

                        $admin_data['p_s'] = $p_s;
                        admin_data(['data[JSON]' => $admin_data]);
                        edk_admin(['update_api_product_settings', $admin_data['update_type'], $p_s]);
                        alert_admin(['none']);
                        break;
                    case 'price_type':
                        $p_s = $admin_data['p_s'];
                        $p_s[$str] = (!isset($p_s[$str]) || $p_s[$str] == 1) ? 2 : 1;

                        $admin_data['p_s'] = $p_s;
                        admin_data(['data[JSON]' => $admin_data]);
                        edk_admin(['update_api_product_settings', $admin_data['update_type'], $p_s]);
                        alert_admin(['none']);
                        break;
                    case 'up':
                        $r = sm_admin(['error_add_products_auto_2'])['result']['message_id'];
                        $admin_data['msgid1'] = $message_id;
                        $admin_data['msgid2'] = $r;
                        $admin_data['type'] = 'up';
                        admin_data(['step' => 'update_api_41', 'data[JSON]' => $admin_data]);
                        alert_admin(['none']);
                        break;
                    case 'round':
                        $r = sm_admin(['error_add_products_auto_3'])['result']['message_id'];
                        $admin_data['msgid1'] = $message_id;
                        $admin_data['msgid2'] = $r;
                        $admin_data['type'] = 'round';
                        admin_data(['step' => 'update_api_41', 'data[JSON]' => $admin_data]);
                        alert_admin(['none']);
                        break;
                    case 'list':
                        $p_s = $admin_data['p_s'];
                        edk_admin(['update_api_product_settings', 6, $p_s]);
                        alert_admin(['none']);
                        break;
                }
            }
            break;
        case text_starts_with($data, 'adminchkey_'):
            $str = str_replace('adminchkey_', '', $data);
            if ($admin['step'] == 'products') {
                switch ($str) {
                    case 'update_row':
                        edt_admin(['display_product'], ['display_products']);
                        break;
                    case 'sort_product_by':
                        admin_data(['data[JSON]' => ['type' => 'product']]);
                        edt_admin(['sort_by'], ['sort_by', 'product']);
                        break;
                    case 'sort_category_by':
                        admin_data(['data[JSON]' => ['type' => 'category']]);
                        edt_admin(['sort_by'], ['sort_by', 'category']);
                        break;
                    case 'sort_under_by':
                        admin_data(['data[JSON]' => ['type' => 'sub']]);
                        edt_admin(['sort_by'], ['sort_by', 'sub']);
                        break;
                    case 'sort_category':
                        admin_data(['data[JSON]' => ['type' => 'category']]);
                        edt_admin(['order_by'], ['order_by']);
                        break;
                    case 'sort_under':
                        admin_data(['data[JSON]' => ['type' => 'sub']]);
                        edt_admin(['order_by'], ['order_by']);
                        break;
                    case 'sort_product':
                        admin_data(['data[JSON]' => ['type' => 'product']]);
                        edt_admin(['order_by'], ['order_by']);
                        break;
                    case 'row_product':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['row_settings']);
                        break;
                    case 'row_under':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['row_settings']);
                        break;
                    case 'row_category':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['row_settings']);
                        break;
                    case 'page_product':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['page_settings']);
                        break;
                    case 'page_under':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['page_settings']);
                        break;
                    case 'page_category':
                        admin_data(['step' => 'display_product_1', 'data[JSON]' => ['type' => $str, 'msgid' => $message_id]]);
                        edt_admin(['page_settings']);
                        break;
                }
            }
            alert_admin(['none']);
            break;
        case text_starts_with($data, 'adminch2_key_'):
            $str = str_replace('adminch2_key_', '', $data);
            $admin_data = json_decode($admin['data'], true);
            $type = $admin_data['type'];
            if ($str == 'back') {
                edt_admin(['display_product'], ['display_products', true]);
            } else {
                switch ($type) {
                    case 'category':
                        $category = json_decode($settings['display_category'], true);
                        switch ($str) {
                            case 'id':
                            case 'name':
                            case 'ordering':
                                $category['sort_by'] = $str;
                                break;
                            case 'ASC':
                            case 'DESC':
                                $category['sort'] = $str;
                                break;
                        }
                        update_option('display_category', js($category));
                        edt_admin(['display_product'], ['display_products', true]);
                        break;
                    case 'sub':
                        $category = json_decode($settings['display_sub_category'], true);
                        switch ($str) {
                            case 'id':
                            case 'name':
                            case 'ordering':
                                $category['sort_by'] = $str;
                                break;
                            case 'ASC':
                            case 'DESC':
                                $category['sort'] = $str;
                                break;
                        }
                        update_option('display_sub_category', js($category));
                        edt_admin(['display_product'], ['display_products', true]);
                        break;
                    case 'product':
                        $category = json_decode($settings['display_products'], true);
                        switch ($str) {
                            case 'id':
                            case 'name':
                            case 'ordering':
                            case 'price':
                                $category['sort_by'] = $str;
                                break;
                            case 'ASC':
                            case 'DESC':
                                $category['sort'] = $str;
                                break;
                        }
                        update_option('display_products', js($category));
                        edt_admin(['display_product'], ['display_products', true]);
                        break;
                }
            }
            break;
        case text_starts_with($data, 'adminoffpanel_'):
            $str = str_replace('adminoffpanel_', '', $data);
            $ex = explode('_', $str);
            $type = $ex['0'];
            switch ($type) {
                case 'category':
                    $type_2 = $ex['1'];
                    switch ($type_2) {
                        case 'show':
                            $id = $ex['2'];
                            $categoryResult = $db->get('categories', '*', ['id' => $id]);
                            if ($categoryResult) {
                                if ($db->has('categories', ['category_id' => $categoryResult['id']])) {
                                    # has under
                                    $result = get_category(['inline', 'offset' => 0, 'status' => 1], $categoryResult['id']);
                                    if ($result) {
                                        $c = $db->count('categories', ['category_id' => $categoryResult['id']]);
                                        edk_admin(['category_status', $result, $c, $categoryResult['id'], 0]);
                                    } else {
                                        alert_user(['not_found']);
                                    }
                                } else {
                                    # go show product
                                    if ($db->has('products', ['category_id' => $categoryResult['id']])) {
                                        # get products
                                        $result = get_products(['inline', 'offset' => 0, 'status' => 1], $categoryResult['id']);
                                        if ($result) {

                                            $c = $db->count('products', ['category_id' => $categoryResult['id']]);
                                            edk_admin(['product_status', $result, $c, $categoryResult['id'], 0]);
                                        } else {
                                            alert_user(['not_found']);
                                        }
                                    } else {
                                        alert_user(['not_found']);
                                    }
                                }
                            } else {
                                alert_user(['not_found']);
                            }
                            alert_admin(['none']);
                            break;
                        case 'page':
                            $depth = $ex['2'];
                            $depth = ($depth == '0') ? null : $depth;
                            $displaySettings = ($depth === null)
                                ? json_decode($settings['display_category'], true)
                                : json_decode($settings['display_sub_category'], true);

                            $page = $displaySettings['page'];
                            $now = $ex['3'];

                            $result = get_category(['inline', 'offset' => $now, 'status' => 1], $depth);
                            $c = $db->count('categories', ['category_id' => $depth]);
                            edk_admin(['category_status', $result, $c, $depth, $now]);
                            alert_admin(['none']);
                            break;
                        case 'back':
                            $id = $ex['2'];
                            $categoryResult = $db->get('categories', '*', ['id' => $id]);
                            if ($categoryResult) {
                                if ($categoryResult['category_id'] == null) {

                                    $displaySettings = json_decode($settings['display_category'], true);
                                    $depth = null;
                                    $now = 0;

                                    $result = get_category(['inline', 'offset' => $now, 'status' => 1], $depth);
                                    $c = $db->count('categories', ['category_id' => $depth]);
                                    edk_admin(['category_status', $result, $c, $depth, $now]);
                                } else {

                                    # has under
                                    $result = get_category(['inline', 'offset' => 0, 'status' => 1], $categoryResult['category_id']);
                                    if ($result) {
                                        $c = $db->count('categories', ['category_id' => $categoryResult['category_id']]);
                                        edk_admin(['category_status', $result, $c, $categoryResult['category_id'], 0]);
                                    } else {
                                        alert_user(['not_found']);
                                    }
                                }
                            } else {
                                alert_user(['not_found']);
                            }
                            alert_admin(['none']);
                            break;
                        case 'status':
                            $id = $ex['2'];

                            $get = $db->get('categories', 'status', ['id' => $id]);
                            if ($get == 1) {
                                $of = 0;
                                alert_admin(['status_off']);
                            } else {
                                $of = 1;
                                alert_admin(['status_on']);
                            }
                            $db->update('categories', ['status' => $of], ['id' => $id]);

                            $inline_keyboard = $callback_query['message']['reply_markup']['inline_keyboard'];
                            $i = 0;
                            $e = count($inline_keyboard);
                            $start = 0;
                            for ($i = $start; $i < $e; $i++) {
                                $k = $inline_keyboard[$i];

                                $tx1 = $tx2 = $cx1 = $cx2 = null;

                                foreach ($k as $callback) {
                                    $tx = $callback['text'];
                                    $callback_tx = $callback['callback_data'];

                                    if (text_starts_with($callback_tx, 'adminoffpanel_category_show_')) {
                                        $p = str_replace('adminoffpanel_category_show_', '', $callback_tx);
                                        $cx1 = 'adminoffpanel_category_show_' . $p;
                                        $tx1 = $callback['text'];
                                    } elseif (text_starts_with($callback_tx, 'adminoffpanel_category_status_')) {
                                        $status_id = str_replace('adminoffpanel_category_status_', '', $callback_tx);
                                        $infobut = ($status_id == $id) ? off($of) : $tx;
                                        $cx2 = 'adminoffpanel_category_status_' . $status_id;
                                        $tx2 = $infobut;
                                    }
                                }

                                if ($cx1 !== null || $cx2 !== null) {
                                    $buttons = [];
                                    if ($cx1 !== null) $buttons[] = ['text' => $tx1, 'callback_data' => $cx1];
                                    if ($cx2 !== null) $buttons[] = ['text' => $tx2, 'callback_data' => $cx2];
                                    $x[] = $buttons;
                                } else {
                                    $x[] = $inline_keyboard[$i];
                                }
                            }

                            $bot->edit_keyboard($fid, $message_id, ['inline_keyboard' => $x]);
                            break;
                        default:
                            alert_admin(['none']);
                            break;
                    }
                    break;
                case 'product':
                    $type_2 = $ex['1'];
                    switch ($type_2) {
                        case 'page':
                            $depth = $ex['2'];
                            $now = $ex['3'];

                            $result = get_products(['inline', 'offset' => $now, 'status' => 1], $depth);
                            $c = $db->count('products', ['category_id' => $depth]);
                            edk_admin(['product_status', $result, $c, $depth, $now]);
                            alert_admin(['none']);
                            break;
                        case 'status':
                            $id = $ex['2'];

                            $get = $db->get('products', 'status', ['id' => $id]);
                            if ($get == 1) {
                                $of = 0;
                                alert_admin(['status_off']);
                            } else {
                                $of = 1;
                                alert_admin(['status_on']);
                            }
                            $db->update('products', ['status' => $of], ['id' => $id]);

                            $inline_keyboard = $callback_query['message']['reply_markup']['inline_keyboard'];
                            $i = 0;
                            $e = count($inline_keyboard);
                            $start = 0;
                            for ($i = $start; $i < $e; $i++) {
                                $k = $inline_keyboard[$i];

                                $tx1 = $tx2 = $cx1 = $cx2 = null;

                                foreach ($k as $callback) {
                                    $tx = $callback['text'];
                                    $callback_tx = $callback['callback_data'];

                                    if ($callback_tx == 'fyk') {
                                        $cx1 = 'fyk';
                                        $tx1 = $callback['text'];
                                    } elseif (text_starts_with($callback_tx, 'adminoffpanel_product_status_')) {
                                        $status_id = str_replace('adminoffpanel_product_status_', '', $callback_tx);
                                        $infobut = ($status_id == $id) ? off($of) : $tx;
                                        $cx2 = 'adminoffpanel_product_status_' . $status_id;
                                        $tx2 = $infobut;
                                    }
                                }

                                if ($cx1 !== null || $cx2 !== null) {
                                    $buttons = [];
                                    if ($cx1 !== null) $buttons[] = ['text' => $tx1, 'callback_data' => $cx1];
                                    if ($cx2 !== null) $buttons[] = ['text' => $tx2, 'callback_data' => $cx2];
                                    $x[] = $buttons;
                                } else {
                                    $x[] = $inline_keyboard[$i];
                                }
                            }

                            $bot->edit_keyboard($fid, $message_id, ['inline_keyboard' => $x]);
                            break;
                    }
            }
            break;
        case text_starts_with($data, 'ch_detail_'):
            $ch_key = str_replace('ch_detail_', '', $data);
            $ch_val = get_option($ch_key, 0);
            
            $name = null;
            
            if($ch_val != 0){
                if ($ch_key == 'channel_main') {
                    $tx = '@' . $ch_val;
                    $g = $bot->bot('GetChat', ['chat_id' => $tx]);
                    if ($g['result']['type'] == 'channel') {
                        $name = $g['result']['title'];
                    } else {
                        $name = null;
                    }
                }else{
                    $g = $bot->bot('GetChat', ['chat_id' => $ch_val]);
                    if ($g['result']['type'] == 'channel') {
                        $name = $g['result']['title'];
                    } else {
                        $name = $g['result']['first_name'];
                    }
                }
            }
            edt_admin(['ch_detail_text', $ch_key, $ch_val,$name], ['ch_detail_panel', $ch_key, !empty($ch_val) && $ch_val != '0']);
            alert_admin(['none']);
            break;

        case 'ch_panel':
            admin_step('ch_idle');
            edt_admin(['channels_panel'], ['channels_panel']);
            alert_admin(['none']);
            break;

        case text_starts_with($data, 'ch_set_'):
            $ch_key = str_replace('ch_set_', '', $data);
            admin_data(['step' => 'ch_set', 'data[JSON]' => ['key' => $ch_key, 'msgid' => $message_id]]);
            edt_admin(['ch_set_prompt', $ch_key], ['ch_cancel']);
            alert_admin(['none']);
            break;

        case text_starts_with($data, 'ch_del_'):
            $ch_key = str_replace('ch_del_', '', $data);
            update_option($ch_key, 0);
            edt_admin(['channels_panel'], ['channels_panel']);
            alert_admin(['ch_deleted']);
            break;

        case 'ch_lock':
            $lock_val = get_option('channel_lock', 0);
            $lock_arr = ($lock_val && $lock_val != '0') ? (json_decode($lock_val, true) ?: []) : [];
            edt_admin(['ch_lock_panel', $lock_arr], ['channel_lock_panel', $lock_arr]);
            alert_admin(['none']);
            break;

        case text_starts_with($data, 'ch_lock_del_'):
            $username = str_replace('ch_lock_del_', '', $data);
            $lock_val = get_option('channel_lock', 0);
            $lock_arr = ($lock_val && $lock_val != '0') ? (json_decode($lock_val, true) ?: []) : [];
            $lock_arr = array_values(array_filter($lock_arr, fn($ch) => $ch !== $username));
            update_option('channel_lock', json_encode($lock_arr));
            edt_admin(['ch_lock_panel', $lock_arr], ['channel_lock_panel', $lock_arr]);
            alert_admin(['ch_deleted']);
            break;

        case 'ch_lock_add':
            admin_data(['step' => 'ch_lock_set', 'data[JSON]' => ['msgid' => $message_id]]);
            edt_admin(['ch_lock_add_prompt'], ['ch_cancel_lock']);
            alert_admin(['none']);
            break;

        case 'sendall_album_send':
            $album_data = json_decode($admin['data'], true);
            $files   = $album_data['files'] ?? [];
            $caption = $album_data['caption'] ?? '';
            $sendstep = $db->has('jobs', ['step[!]' => "none"]);
            if ($sendstep) {
                admin_step('sendall');
                $bot->edit_text($fid, $message_id, $media_admin->atext('sendall_6'), ['inline_keyboard' => []]);
            } elseif (count($files) >= 2) {
                $data_send = ['step' => 'sendall', 'info[JSON]' => ['send' => 'sendmediagroup', 'files' => $files, 'caption' => $caption], 'user' => '0', 'admin' => $fid];
                $db->insert('jobs', $data_send);
                admin_step('sendall');
                admin_data(['data' => 'none']);
                $bot->edit_text($fid, $message_id, $media_admin->atext('sendall_album_added', count($files)), ['inline_keyboard' => []]);
                sm_admin(['sendall_5'], ['send_panel']);
            } elseif (!empty($files)) {
                $f = $files[0];
                $data_send = ['step' => 'sendall', 'info[JSON]' => ['send' => 'sendphoto', 'file_id' => $f['file_id'], 'type_file' => 'photo', 'caption' => $caption], 'user' => '0', 'admin' => $fid];
                $db->insert('jobs', $data_send);
                admin_step('sendall');
                admin_data(['data' => 'none']);
                $bot->edit_text($fid, $message_id, $media_admin->atext('sendall_photo_added'), ['inline_keyboard' => []]);
                sm_admin(['sendall_5'], ['send_panel']);
            }
            alert_admin(['none']);
            break;

        case 'sendall_album_cancel':
            admin_data(['step' => 'sendall_2', 'data' => 'none']);
            $bot->edit_text($fid, $message_id, $media_admin->atext('sendall_album_cancelled'), ['inline_keyboard' => []]);
            sm_admin(['sendall_2'], ['back_panel_all']);
            alert_admin(['none']);
            break;

        // ── Sendall status/control ─────────────────────────────────────────────────
        case 'sendall_preview':
            $job = $db->get('jobs', '*', ['step[!]' => 'none']);
            if ($job) {
                sendallmsg($fid, $job);
                alert_admin(['sendall_preview_sent']);
            } else {
                alert_admin(['sendall_status_empty']);
            }
            break;

        case 'sendall_pause':
            $db->update('jobs', ['paused' => 1], ['step[!]' => 'none']);
            render_sendall_status($message_id);
            alert_admin(['sendall_paused']);
            break;

        case 'sendall_resume':
            $db->update('jobs', ['paused' => 0], ['step[!]' => 'none']);
            render_sendall_status($message_id);
            alert_admin(['sendall_resumed']);
            break;

        case 'sendall_status_refresh':
            render_sendall_status($message_id);
            alert_admin(['none']);
            break;

        case 'sendall_full_cancel':
            $db->update('jobs', ['step' => 'none', 'paused' => 0], ['step[!]' => 'none']);
            render_sendall_status($message_id);
            alert_admin(['sendall_cancelled']);
            break;

        default:

            break;
    }
}
