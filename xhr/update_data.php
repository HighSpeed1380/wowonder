<?php
if ($f == 'update_data') {
    if (Wo_CheckMainSession($hash_id) === true) {
        $sql_query             = mysqli_query($sqlConnect, "UPDATE " . T_APP_SESSIONS . " SET `time` = " . time() . " WHERE `session_id` = '{$session_id}'");
        $data['pop']           = 0;
        $data['status']        = 200;
        $data['notifications'] = Wo_CountNotifications(array(
            'unread' => true
        ));
        $data['html']          = '';
        $notifications         = Wo_GetNotifications(array(
            'type_2' => 'popunder',
            'unread' => true,
            'limit' => 1
        ));
        foreach ($notifications as $wo['notification']) {
            $data['html']              = Wo_LoadPage('header/notifecation');
            $data['icon']              = $wo['notification']['notifier']['avatar'];
            $data['title']             = $wo['notification']['notifier']['name'];
            $data['notification_text'] = $wo['notification']['type_text'];
            $data['url']               = $wo['notification']['url'];
            $data['pop']               = 200;
            if ($wo['notification']['seen'] == 0) {
                $query     = "UPDATE " . T_NOTIFICATION . " SET `seen_pop` = " . time() . " WHERE `id` = " . $wo['notification']['id'];
                $sql_query = mysqli_query($sqlConnect, $query);
            }
        }
        $data['messages'] = Wo_CountMessages(array(
            'new' => true
        ), 'interval');
        $chat_groups      = Wo_CheckLastGroupUnread();
        $data['messages'] = $data['messages'] + count($chat_groups);
        $data['calls']    = 0;
        $data['is_call']  = 0;
        $check_calles     = Wo_CheckFroInCalls();
        if ($check_calles !== false && is_array($check_calles)) {
            $wo['incall']                 = $check_calles;
            $wo['incall']['in_call_user'] = Wo_UserData($check_calles['from_id']);
            $data['calls']                = 200;
            $data['is_call']              = 1;
            $data['calls_html']           = Wo_LoadPage('modals/in_call');
        }
        $data['audio_calls']   = 0;
        $data['is_audio_call'] = 0;
        $check_calles          = Wo_CheckFroInCalls('audio');
        if ($check_calles !== false && is_array($check_calles)) {
            $wo['incall']                 = $check_calles;
            $wo['incall']['in_call_user'] = Wo_UserData($check_calles['from_id']);
            $data['audio_calls']          = 200;
            $data['is_audio_call']        = 1;
            $data['audio_calls_html']     = Wo_LoadPage('modals/in_audio_call');
        }
        $data['followRequests']      = Wo_CountFollowRequests();
        $data['followRequests']      = $data['followRequests'] + Wo_CountGroupChatRequests();
        $data['notifications_sound'] = $wo['user']['notifications_sound'];
    }
    $data['count_num'] = 0;
    if ($_GET['check_posts'] == 'true') {
        if (!empty($_GET['before_post_id']) && isset($_GET['user_id'])) {
            $html      = '';
            $postsData = array(
                'before_post_id' => $_GET['before_post_id'],
                'publisher_id' => $_GET['user_id'],
                'limit' => 20,
                'ad-id' => 0,
                'placement' => 'multi_image_post'
            );
            $posts     = Wo_GetPosts($postsData);
            $count     = count($posts);
            if ($count == 1) {
                $data['count'] = str_replace('{count}', $count, $wo['lang']['view_more_post']);
            } else {
                $data['count'] = str_replace('{count}', $count, $wo['lang']['view_more_posts']);
            }
            $data['count_num'] = $count;
        }
    } else if ($_GET['hash_posts'] == 'true') {
        if (!empty($_GET['before_post_id']) && isset($_GET['user_id'])) {
            $html  = '';
            $posts = Wo_GetHashtagPosts($_GET['hashtagName'], 0, 20, $_GET['before_post_id']);
            $count = count($posts);
            if ($count == 1) {
                $data['count'] = str_replace('{count}', $count, $wo['lang']['view_more_post']);
            } else {
                $data['count'] = str_replace('{count}', $count, $wo['lang']['view_more_posts']);
            }
            $data['count_num'] = $count;
        }
    }
    $send_messages_to_phones = Wo_MessagesPushNotifier();


    if (!empty($wo['user']['coinpayments_txn_id'])) {
        $result = coinpayments_api_call(array('key' => $wo['config']['coinpayments_public_key'],
                                              'version' => '1',
                                              'format' => 'json',
                                              'cmd' => 'get_tx_info',
                                              'full' => '1',
                                              'txid' => $wo['user']['coinpayments_txn_id']));
        if (!empty($result) && $result['status'] == 200) {
            if ($result['data']['status'] == -1) {
                $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('coinpayments_txn_id' => ''));
                $notification_data_array = array(
                    'recipient_id' => $wo['user']['user_id'],
                    'type' => 'admin_notification',
                    'type2' => 'coinpayments_canceled',
                    'url' => 'index.php?link1=wallet',
                    'time' => time()
                );
                $db->insert(T_NOTIFICATION, $notification_data_array);
            }
            elseif ($result['data']['status'] == 100) {
                $amount   = $result['data']['checkout']['amountf'];
                $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('wallet' => $db->inc($amount),
                                                                                   'coinpayments_txn_id' => ''));

                $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $wo['user']['user_id'] . "', 'WALLET', '" . $amount . "', 'coinpayments')");
                $_SESSION['replenished_amount'] = $amount;

                $notification_data_array = array(
                    'recipient_id' => $wo['user']['user_id'],
                    'type' => 'admin_notification',
                    'type2' => 'coinpayments_approved',
                    'url' => 'index.php?link1=wallet',
                    'time' => time()
                );
                $db->insert(T_NOTIFICATION, $notification_data_array);
            }
        }
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
