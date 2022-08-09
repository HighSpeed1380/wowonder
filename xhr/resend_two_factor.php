<?php
if ($f == 'resend_two_factor') {
	$hash = '';
	if (!empty($_SESSION) && !empty($_SESSION['two_factor_hash'])) {
		$hash = filter_var($_SESSION['two_factor_hash'], FILTER_SANITIZE_STRING);
		$hash = Wo_Secure($hash);
	}
	if (!empty($_COOKIE) && !empty($_COOKIE['two_factor_hash'])) {
		$hash = filter_var($_COOKIE['two_factor_hash'], FILTER_SANITIZE_STRING);
		$hash = Wo_Secure($hash);
	}
	if (empty($hash)) {
		$data['status'] = 400;
		$data['message'] = $wo['lang']['code_two_expired'];
	}
	else{
		$user = $db->where('two_factor_hash',$hash)->where('email_code','','!=')->getOne(T_USERS);
		if (!empty($user)) {
			if ($user->time_code_sent == 0 || $user->time_code_sent < (time() - (60 * 1))) {
				if (Wo_TwoFactor($user->username) === false) {
					$db->where('user_id',$_SESSION['code_id'])->update(T_USERS,array('time_code_sent' => time()));
					$data = array(
                        'status' => 200,
                        'message' => $wo['lang']['code_successfully_sent']
                    );
				}
				else{
					$data['status'] = 400;
		   			$data['message'] = $wo['lang']['something_wrong'];
				}
			}
			else{
				$data['status'] = 400;
		        $data['message'] = $wo['lang']['you_cant_send_now'];
			}
		}
		else{
			$data['status'] = 400;
		    $data['message'] = $wo['lang']['something_wrong'];
		}
	}
	header("Content-type: application/json");
    echo json_encode($data);
    exit();
}