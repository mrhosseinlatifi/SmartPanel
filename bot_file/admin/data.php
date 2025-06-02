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
                    case 'produc':
                        $result = $db->get('products', '*', ['id' => $id]);

                        admin_data(['step' => 'edit_info', 'data[JSON]' => ['type' => 'product', 'id' => $id]]);

                        sm_admin(['edit_product_info', $result, false], ['update_info', 'product', $result['id']]);

                        sm_admin(['edit_products_panel'], ['edit_products_panel', 'product']);

                        break;
                }
            } else {
                alert_admin(['none']);
            }
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
                    edt_admin(['edit_product_info', $result, true], ['update_info', 'product', $result['id']]);
                    break;
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
                    case '6':
                        $p_s = ['name' => 0, 'price' => 1, 'price_type' => 1, 'min' => 1, 'info' => 0, 'up' => 0, 'round' => 0, 'next' => 0, 'convert' => 0];
                        $admin_data['p_s'] = $p_s;
                        admin_data(['step' => 'update_api_4', 'data[JSON]' => $admin_data]);
                        edt_admin(['update_api_2'], ['update_api_product_settings', 6, $p_s]);
                        break;
                    default:
                        # code...
                        break;
                }
            }
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
                        edt_admin(['update_api_2'], ['update_api_select_product', $categories, 'sub_category', $id]);
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
                                        $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                        $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                        $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
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
                                $desc = removeWhiteSpace($product['desc']);
                                $ser_id = $product['service'];

                                $db->insert('products', [
                                    'name' => $text_en,
                                    'price' => $price,
                                    'min' => $min,
                                    'max' => $max,
                                    'info' => $desc,
                                    'api' => $result_api['name'],
                                    'service' => $ser_id,
                                    'category_id' => $id,
                                    'ordering' => $ordering,
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
                                    $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                    $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                    $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
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
                            $desc = removeWhiteSpace($product['desc']);
                            $ser_id = $product['service'];

                            $db->insert('products', [
                                'name' => $text_en,
                                'price' => $price,
                                'min' => $min,
                                'max' => $max,
                                'info' => $desc,
                                'api' => $result_api['name'],
                                'service' => $ser_id,
                                'category_id' => $id,
                                'ordering' => $ordering,
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

                                            $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                            $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                            $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                            $products[] = $row;
                                        }
                                    }
                                }

                                $add_count = 0;
                                $not = 0;
                                foreach ($products as $value) {
                                    $name_product = mb_substr(removeWhiteSpace($value['name']), 0, 130);
                                    $text_en = js($name_product);
                                    $cate = $ids[$value['category']];

                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $cate]);
                                    $ordering += 1;

                                    $price = $value['rate'];
                                    $min = $value['min'];
                                    $max = $value['max'];
                                    $info = removeWhiteSpace($value['desc']);
                                    $ser_id = $value['service'];

                                    $db->insert('products', [
                                        'name' => $text_en,
                                        'price' => $price,
                                        'min' => $min,
                                        'max' => $max,
                                        'info' => $info,
                                        'api' => $result_api['name'],
                                        'service' => $ser_id,
                                        'category_id' => $cate,
                                        'ordering' => $ordering,
                                    ]);
                                    $insert = $db->id();
                                    if ($insert) {
                                        $add_count += 1;
                                    }
                                }
                                admin_step('products');
                                sm_admin(['update_api_ok', 2, $add_count], ['products_panel']);
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
                                            $row['rate'] = ($p_s['convert'] && $usd_rate > 0) ? $row['rate'] * $usd_rate : $row['rate'];
                                            $row['rate'] = ($p_s['up'] > 0) ? up_price($row['rate'], $p_s['up']) : $row['rate'];
                                            $row['rate'] = ($p_s['round'] > 0) ? round_up($row['rate'], $p_s['round']) : $row['rate'];
                                            $products[] = $row;
                                        }
                                    }
                                }

                                $add_count = 0;
                                $not = 0;
                                foreach ($products as $value) {
                                    $name_product = mb_substr(removeWhiteSpace($value['name']), 0, 130);
                                    $text_en = js($name_product);
                                    $cate = $ids[$value['category']];

                                    $ordering = (int) $db->max('products', 'ordering', ['category_id' => $cate]);
                                    $ordering += 1;

                                    $price = $value['rate'];
                                    $min = $value['min'];
                                    $max = $value['max'];
                                    $info = removeWhiteSpace($value['desc']);
                                    $ser_id = $value['service'];

                                    $db->insert('products', [
                                        'name' => $text_en,
                                        'price' => $price,
                                        'min' => $min,
                                        'max' => $max,
                                        'info' => $info,
                                        'api' => $result_api['name'],
                                        'service' => $ser_id,
                                        'category_id' => $cate,
                                        'ordering' => $ordering,
                                    ]);
                                    $insert = $db->id();
                                    if ($insert) {
                                        $add_count += 1;
                                    }
                                }
                                admin_step('products');
                                sm_admin(['update_api_ok', 2, $add_count], ['products_panel']);

                                break;
                            case '6':
                                $add_count = 0;
                                $off_count = 0;

                                $services = $api->services($result_api);
                                $list = [];
                                $products = [];
                                $ids = [];
                                $usd_rate = $settings['usd_rate'];
                                foreach ($services['data'] as $row) {
                                    $products[$row['service']] = $row;
                                }
                                $result_products = $db->select('products', '*', ['api' => $result_api['name']]);
                                foreach ($result_products as $key => $value) {
                                    if (!isset($products[$value['service']])) {
                                        $db->update('products', ['status' => 0], ['id' => $value['id']]);
                                        $off_count;
                                        continue;
                                    }

                                    
                                    $data_p = $products[$value['service']];
                                    $s = [];

                                    $s['status'] = 1;
                                    
                                    if ($p_s['name']) {
                                        $name_product = mb_substr(removeWhiteSpace($data_p['name']), 0, 130);
                                        $text_en = js($name_product);
                                        $s['name'] = $text_en;
                                    }

                                    if ($p_s['price']) {
                                        $price = $data_p['rate'];
                                        $price = ($p_s['convert'] && $usd_rate > 0) ? $price * $usd_rate : $price;
                                        if ($p_s['price_type'] == 2 && $value['price'] < $data_p['rate']) {
                                            $price = $value['price'];
                                        }

                                        if ($p_s['up'] > 0) {
                                            $price = up_price($price, $p_s['up']);
                                        }

                                        if ($p_s['round'] > 0) {
                                            $price = round_up($price, $p_s['round']);
                                        }
                                        $price = $price;
                                        $s['price'] = $price;
                                    }

                                    if ($p_s['min']) {
                                        $s['min'] = $data_p['min'];
                                        $s['max'] = $data_p['max'];
                                    }

                                    if ($p_s['info']) {
                                        $s['info'] = removeWhiteSpace($data_p['desc']);
                                    }

                                    if (!empty($s)) {
                                        $db->update('products', $s, ['id' => $value['id']]);
                                        $add_count += 1;
                                    }
                                }


                                admin_step('products');
                                sm_admin(['update_api_ok', 6, $add_count, $off_count], ['products_panel']);
                                break;
                        }
                        break;
                    case 'name':
                    case 'price':
                    case 'min':
                    case 'info':
                    case 'convert':
                        $p_s = $admin_data['p_s'];
                        $p_s[$str] = ($p_s[$str]) ? 0 : 1;

                        $admin_data['p_s'] = $p_s;
                        admin_data(['data[JSON]' => $admin_data]);
                        edk_admin(['update_api_product_settings', $admin_data['update_type'], $p_s]);
                        alert_admin(['none']);
                        break;
                    case 'price_type':
                        $p_s = $admin_data['p_s'];
                        $p_s[$str] = ($p_s[$str] == 1) ? 2 : 1;

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
            if ($admin['step'] == 'display_product') {
                switch ($str) {
                    case 'update_row':
                        edt_admin(['display_product'], ['display_prodcuts']);
                        alert_admin(['none']);
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
            break;
        case text_starts_with($data, 'adminch2_key_'):
            $str = str_replace('adminch2_key_', '', $data);
            $admin_data = json_decode($admin['data'], true);
            $type = $admin_data['type'];
            if ($str == 'back') {
                edt_admin(['display_product'], ['display_prodcuts', true]);
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
                        edt_admin(['display_product'], ['display_prodcuts', true]);
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
                        edt_admin(['display_product'], ['display_prodcuts', true]);
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
                        edt_admin(['display_product'], ['display_prodcuts', true]);
                        break;
                }
            }
            break;
        case text_starts_with($data, 'adminoffpanel_'):
            $str = str_replace('adminoffpanel_', '', $data);
            $ex = explode('_', $str);
            $type = $ex['0'];
            // adminoffpanel_category_status_1
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
                            # code...
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
                            edk_user(['product_status', $result, $c, $depth, $now]);
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
        default:

            break;
    }
}
