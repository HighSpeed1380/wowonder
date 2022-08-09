<?php
// +------------------------------------------------------------------------+
// | @author Deen Doughouz (DoughouzForest)
// | @author_url 1: http://www.wowonder.com
// | @author_url 2: http://codecanyon.net/user/doughouzforest
// | @author_email: wowondersocial@gmail.com   
// +------------------------------------------------------------------------+
// | WoWonder - The Ultimate Social Networking Platform
// | Copyright (c) 2016 WoWonder. All rights reserved.
// +------------------------------------------------------------------------+
$json_error_data   = array();
$json_success_data = array();
if (empty($_GET['type']) || !isset($_GET['type'])) {
    $json_error_data = array(
        'api_status' => '400',
        'api_text' => 'failed',
        'api_version' => $api_version,
        'errors' => array(
            'error_id' => '1',
            'error_text' => 'Bad request, no type specified.'
        )
    );
    header("Content-type: application/json");
    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
    exit();
}
$json_error_data = array(
    'api_status' => '400',
    'api_text' => 'failed',
    'api_version' => $api_version,
    'errors' => array()
);
$type = Wo_Secure($_GET['type'], 0);
if ($type == 'user_registration') {
    if (empty($_POST['username'])) {
        $json_error_data['errors'] = array(
            'error_id' => '2',
            'error_text' => 'Please write your username.'
        );
    } else if (in_array(true, Wo_IsNameExist($_POST['username'], 0))) {
    	$json_error_data['errors'] = array(
            'error_id' => '3',
            'error_text' => 'Username is already exists.'
        );
    } else if (in_array($_POST['username'], $wo['site_pages'])) {
    	$json_error_data['errors'] = array(
            'error_id' => '4',
            'error_text' => 'Invalid username characters.'
        );
    } else if (strlen($_POST['username']) < 5 OR strlen($_POST['username']) > 32) {
    	$json_error_data['errors'] = array(
            'error_id' => '6',
            'error_text' => 'Username must be between 5 / 32'
        );
    } else if (!preg_match('/^[\w]+$/', $_POST['username'])) {
    	$json_error_data['errors'] = array(
            'error_id' => '7',
            'error_text' => 'Invalid username characters'
        );
    } else if (empty($_POST['email'])) {
        $json_error_data['errors'] = array(
            'error_id' => '8',
            'error_text' => 'Please write your email.'
        );
    } else if (Wo_EmailExists($_POST['email']) === true) {
    	$json_error_data['errors'] = array(
            'error_id' => '9',
            'error_text' => 'This e-mail is already in use.'
        );
    } else if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    	$json_error_data['errors'] = array(
            'error_id' => '10',
            'error_text' => 'This e-mail is invalid.'
        );
    } else if (empty($_POST['password'])) {
    	$json_error_data['errors'] = array(
            'error_id' => '11',
            'error_text' => 'Please write your password.'
        );
    } else if (strlen($_POST['password']) < 6) {
    	$json_error_data['errors'] = array(
            'error_id' => '12',
            'error_text' => 'Password is too short.'
        );
    } else if (empty($_POST['confirm_password'])) {
    	$json_error_data['errors'] = array(
            'error_id' => '13',
            'error_text' => 'Please confirm your password.'
        );
    } else if ($_POST['password'] != $_POST['confirm_password']) {
    	$json_error_data['errors'] = array(
            'error_id' => '14',
            'error_text' => 'Password not match.'
        );
    }
    if (empty($json_error_data['errors'])) {
        $username        = Wo_Secure($_POST['username'], 0);
        $password        = $_POST['password'];
        $email           = Wo_Secure($_POST['email'], 0);
        $gender          = 'male';
        if (!empty($_POST['gender'])) {
        	if ($_POST['gender'] == 'female') {
        		$gender  = 'female';
        	}
        }
        $activate = ($wo['config']['emailValidation'] == '1') ? '0' : '1';
        $device_id = '';
        if (!empty($_POST['device_id'])) {
            $device_id = Wo_Secure($_POST['device_id']);
        }
        $re_data  = array(
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'email_code' => $username,
            'src' => 'Phone',
            'timezone' => 'UTC',
            'gender' => Wo_Secure($gender),
            'lastseen' => time(),
            'active' => Wo_Secure($activate)
        );
        $register = Wo_RegisterUser($re_data);
        if ($register === true) {
            if ($activate == 1) {
                $json_success_data  = array(
                	'api_status' => '200',
                    'api_text' => 'success',
                    'api_version' => $api_version,
                    'message' => 'Successfully joined, Please wait..',
                    'success_type' => 'registered',
                    'access_token' => 0,
                    'cookie' => Wo_CreateLoginSession(Wo_UserIdForLogin($username)),
                    'user_id' => 0
                );
                $user_id = Wo_UserIdFromUsername($username);
                $time           = time();
                $cookie         = '';
                $access_token   = sha1(rand(111111111, 999999999)) . md5(microtime()) . rand(11111111, 99999999) . md5(rand(5555, 9999));
                $add_session = mysqli_query($sqlConnect, "INSERT INTO " . T_APP_SESSIONS . " (`user_id`, `session_id`, `platform`, `time`) VALUES ('{$user_id}', '{$access_token}', 'windows', '{$time}')");
                if ($add_session) {
            	    $json_success_data['access_token'] = $access_token;
            	    $json_success_data['user_id'] = $user_id;
                }
            }

            elseif ($wo['config']['sms_or_email'] == 'mail') {
                $wo['user']        = $_POST;
                $body              = Wo_LoadPage('emails/activate');
                $send_message_data = array(
                    'from_email' => $wo['config']['siteEmail'],
                    'from_name' => $wo['config']['siteName'],
                    'to_email' => $email,
                    'to_name' => $password,
                    'subject' => $wo['lang']['account_activation'],
                    'charSet' => 'utf-8',
                    'message_body' => $body,
                    'is_html' => true
                );
                $send              = Wo_SendMessage($send_message_data);
                
                if ($send) {
                    $json_success_data  = array(
                        'api_status' => '200',
                        'api_text' => 'success',
                        'api_version' => $api_version,
                        'message' => 'Registration successful! We have sent you an email, Please check your inbox/spam to verify your email.',
                        'success_type' => 'verification',
                        'access_token' => 0,
                        'user_id' => 0
                    );
                } else {
                    $json_error_data['errors'] = array(
                        'error_id' => '15',
                        'error_text' => 'Error found while sending the verification email, please try again later.'
                    );
                    header("Content-type: application/json");
                    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                    exit();
                }
            }
            elseif ($wo['config']['sms_or_email'] == 'sms' && !empty($_POST['phone_num'])) {
                $random_activation = Wo_Secure(rand(11111, 99999));
                $message           = "Your confirmation code is: {$random_activation}";

                if (Wo_SendSMSMessage($_POST['phone_num'], $message) === true) {
                    $user_id             = Wo_UserIdFromUsername($username);
                    $query             = mysqli_query($sqlConnect, "UPDATE " . T_USERS . " SET `sms_code` = '{$random_activation}' WHERE `user_id` = {$user_id}");
                    $json_success_data  = array(
                        'api_status' => '200',
                        'api_text' => 'success',
                        'api_version' => $api_version,
                        'message' => 'Registration successful! We have sent you an sms, Please check your phone to verify your account.',
                        'success_type' => 'verification',
                        'access_token' => 0,
                        'user_id' => $user_id
                    );
                } else {
                    $json_error_data['errors'] = array(
                        'error_id' => '16',
                        'error_text' => 'Error found while sending the verification sms, please try again later.'
                    );
                    header("Content-type: application/json");
                    echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                    exit();
                }
            }
            elseif ($wo['config']['sms_or_email'] == 'sms' && empty($_POST['phone_num'])) {
                $json_error_data['errors'] = array(
                    'error_id' => '17',
                    'error_text' => 'phone_num can not be empty.'
                );
                header("Content-type: application/json");
                echo json_encode($json_error_data, JSON_PRETTY_PRINT);
                exit();
            }

        }
    } else {
        header("Content-type: application/json");
        echo json_encode($json_error_data, JSON_PRETTY_PRINT);
        exit();
    }
}
header("Content-type: application/json");
echo json_encode($json_success_data);
exit();
?>