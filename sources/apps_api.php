<?php
$limit  = 20;
if (isset($_GET['limit']) && !empty($_GET['limit']) && is_numeric($_GET['limit']) && $_GET['limit'] > 0) {
    $limit = Wo_Secure($_GET['limit']);
}
if (!is_numeric($limit)) {
    $limit = 20;
}
if ($limit > 100) {
    $limit = 100;
}
$api_version = '1.4.4';
if (empty($_GET['access_token'])) {
    $errors = array(
        'status' => 400,
        'errors' => array(
            'error_code' => 1,
            'message' => 'Unauthorized'
        )
    );
    header("Content-type: application/json");
    echo json_encode($errors, JSON_PRETTY_PRINT);
    exit();
}
if (empty($_GET['type'])) {
    $errors = array(
        'status' => 400,
        'errors' => array(
            'error_code' => 5,
            'message' => 'API type is not specified'
        )
    );
    header("Content-type: application/json");
    echo json_encode($errors, JSON_PRETTY_PRINT);
    exit();
}
$user_id = Wo_UserIdFromToken($_GET['access_token']);
if ($user_id === false) {
    $errors = array(
        'status' => 400,
        'errors' => array(
            'error_code' => 2,
            'message' => 'Invalid or Unauthorized token'
        )
    );
    header("Content-type: application/json");
    echo json_encode($errors, JSON_PRETTY_PRINT);
    exit();
}
$user = $wo['user'] = Wo_UserData($user_id);
if (empty($user) || !is_array($user)) {
    $errors = array(
        'status' => 400,
        'errors' => array(
            'error_code' => 3,
            'message' => 'Error found while fetching the data, please try again later.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($errors, JSON_PRETTY_PRINT);
    exit();
}
$wo["loggedin"]           = true;
$non_allowed = array(
    'password',
    'background_image_status',
    'email_code',
    'type',
    'start_up',
    'start_up_info',
    'startup_follow',
    'startup_image',
    'id',
    'cover_full',
    'cover_org',
    'avatar_org',
    'app_session',
    'last_email_sent',
    'sms_code',
    'pro_time',
    'css_file',
    'src',
    'followers_data',
    'following_data',
    'likes_data',
    'album_data',
    'groups_data',
    'sidebar_data',
    'showlastseen',
    'joined',
    'social_login',
);
if ($_GET['type'] == 'get_user_data') {
    $user_data = array(
        'status' => 200,
        'valid_until' => 3600,
        'api_version' => '1.4',
        'user_data' => array(
            'username' => $user['username'],
            'email' => $user['email'],
            'name' => $user['name'],
            'first_name' => $user['first_name'],
            'last_name' => $user['last_name'],
            'gender' => $user['gender'],
            'avatar' => $user['avatar'],
            'cover' => $user['cover'],
            'is_pro' => $user['is_pro'],
            'language' => $user['language'],
            'verified' => $user['verified'],
            'lastseen' => $user['lastseen'],
            'address' => $user['address'],
            'about' => $user['about']
        )
    );
    header("Content-type: application/json");
    echo json_encode($user_data, JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'posts_data') {
    $publisher_id = $user['user_id'];
    if (empty($publisher_id)) {
        $json_error_data = array(
            'api_status' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '1',
                'error_text' => 'Username is not exists.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
    $api_data = Wo_GetPosts(array(
        'limit' => $limit,
        'publisher_id' => $publisher_id
    ));
    if (empty($api_data)) {
        $json_error_data = array(
            'api_status' => 'failed',
            'api_version' => $api_version,
            'errors' => array(
                'error_id' => '2',
                'error_text' => 'User does not have any posts.'
            )
        );
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
    header("Content-type: application/json");
    foreach ($api_data as $post_data) {
        $result = array();
        $json_data = array(
            'post_id' => $post_data['post_id'],
            'post_data' => array(
                'post_id' => $post_data['post_id'],
                'post_text' => $post_data['postText'],
                'post_file' => Wo_GetMedia($post_data['postFile']),
                'post_soundcloud' => $post_data['postSoundCloud'],
                'post_youtube' => $post_data['postYoutube'],
                'post_vine' => $post_data['postVine'],
                'post_map' => $post_data['postMap'],
                'post_time' => $post_data['time'],
                'post_likes' => Wo_CountLikes($post_data['post_id']),
                'post_wonders' => Wo_CountWonders($post_data['post_id'])
            ),
            'publisher_data' => array(
                'id' => $post_data['publisher']['user_id'],
                'username' => $post_data['publisher']['username'],
                'first_name' => $post_data['publisher']['first_name'],
                'last_name' => $post_data['publisher']['last_name'],
                'gender' => $post_data['publisher']['gender'],
                'birthday' => $post_data['publisher']['birthday'],
                'about' => $post_data['publisher']['about'],
                'website' => $post_data['publisher']['website'],
                'facebook' => $post_data['publisher']['facebook'],
                'twitter' => $post_data['publisher']['twitter'],
                'vk' => $post_data['publisher']['vk'],
                'google+' => $post_data['publisher']['google'],
                'profile_picture' => $post_data['publisher']['avatar'],
                'cover_picture' => $post_data['publisher']['cover'],
                'verified' => $post_data['publisher']['verified'],
                'url' => $post_data['url']
            )
        );
        array_push($result, $json_data);
    }
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $result
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_pages') {
    $pages = Wo_GetMyPages(false,$limit);
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $pages
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_groups') {
    $groups = Wo_GetMyGroupsAPI($limit,0,'DESC');
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $groups
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_products') {
    $products = array();
    $get_products = Wo_GetProducts(array('user_id' => $wo['user']['user_id'],
                                         'limit' => $limit));
    foreach ($get_products as $key => $product) {
        foreach ($non_allowed as $key => $value) {
           unset($product['seller'][$value]);
           unset($product['user_data'][$value]);
        }
        if (!empty($product['post_id']) && !empty($product['images'])) {
            $products[] = $product;
        }
    }
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $products
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_followers') {
    $following = Wo_GetFollowers($wo['user']['user_id'], 'profile', $limit,false);
    foreach ($following as $key2 => $user_list) {

        $lastseen = ($user_list['lastseen'] > (time() - 60)) ? 'on' : 'off';
        $following[$key2] = $user_list;
        $following[$key2]['lastseen_unix_time'] = $user_list['lastseen'];
        $following[$key2]['lastseen_time_text'] = Wo_Time_Elapsed_String($user_list['lastseen']);
        $following[$key2]['lastseen'] = $lastseen;
        $following[$key2]['user_platform'] = Wo_GetPlatformFromUser_ID($user_list['user_id']);
        $following[$key2]['is_following'] = (Wo_IsFollowing($user_list['user_id'],$wo['user']['user_id'])) ? 1 : 0;

        foreach ($non_allowed as $key => $value) {
            unset($following[$key2][$value]);
        }
    }
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $following
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_following') {
    $following = Wo_GetFollowing($wo['user']['user_id'], 'profile', $limit,false);
    foreach ($following as $key2 => $user_list) {

        $lastseen = ($user_list['lastseen'] > (time() - 60)) ? 'on' : 'off';
        $following[$key2] = $user_list;
        $following[$key2]['lastseen_unix_time'] = $user_list['lastseen'];
        $following[$key2]['lastseen_time_text'] = Wo_Time_Elapsed_String($user_list['lastseen']);
        $following[$key2]['lastseen'] = $lastseen;
        $following[$key2]['user_platform'] = Wo_GetPlatformFromUser_ID($user_list['user_id']);
        $following[$key2]['is_following'] = (Wo_IsFollowing($user_list['user_id'],$wo['user']['user_id'])) ? 1 : 0;

        foreach ($non_allowed as $key => $value) {
            unset($following[$key2][$value]);
        }
    }
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $following
    ), JSON_PRETTY_PRINT);
    exit();
} else if ($_GET['type'] == 'get_friends') {
    $following = Wo_GetFollowing($wo['user']['user_id'], 'profile', $limit,false);
    foreach ($following as $key2 => $user_list) {

        $lastseen = ($user_list['lastseen'] > (time() - 60)) ? 'on' : 'off';
        $following[$key2] = $user_list;
        $following[$key2]['lastseen_unix_time'] = $user_list['lastseen'];
        $following[$key2]['lastseen_time_text'] = Wo_Time_Elapsed_String($user_list['lastseen']);
        $following[$key2]['lastseen'] = $lastseen;
        $following[$key2]['user_platform'] = Wo_GetPlatformFromUser_ID($user_list['user_id']);
        $following[$key2]['is_following'] = (Wo_IsFollowing($user_list['user_id'],$wo['user']['user_id'])) ? 1 : 0;

        foreach ($non_allowed as $key => $value) {
            unset($following[$key2][$value]);
        }
    }
    echo json_encode(array(
        'api_status' => 'success',
        'api_version' => '1.4.4',
        'status' => 200,
        'valid_until' => 3600,
        'items' => $following
    ), JSON_PRETTY_PRINT);
    exit();
}
exit('Type not found');
?>