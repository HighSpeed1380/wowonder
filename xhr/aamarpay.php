<?php
if ($f == 'aamarpay') {
	if ($s == 'pay') {
		if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0 && !empty($_POST['name']) && !empty($_POST['email']) && !empty($_POST['phone'])) {
			$amount   = (int)Wo_Secure($_POST[ 'amount' ]);
			$name   = Wo_Secure($_POST[ 'name' ]);
			$email   = Wo_Secure($_POST[ 'email' ]);
			$phone   = Wo_Secure($_POST[ 'phone' ]);
	        if ($wo['config']['aamarpay_mode'] == 'sandbox') {
	            $url = 'https://sandbox.aamarpay.com/request.php'; // live url https://secure.aamarpay.com/request.php
	        }
	        else {
	            $url = 'https://secure.aamarpay.com/request.php';
	        }
	        $tran_id = rand(1111111,9999999);
	        $fields = array(
	            'store_id' => $wo['config']['aamarpay_store_id'], //store id will be aamarpay,  contact integration@aamarpay.com for test/live id
	            'amount' => $amount, //transaction amount
	            'payment_type' => 'VISA', //no need to change
	            'currency' => 'BDT',  //currenct will be USD/BDT
	            'tran_id' => $tran_id, //transaction id must be unique from your end
	            'cus_name' => $name,  //customer name
	            'cus_email' => $email, //customer email address
	            'cus_add1' => '',  //customer address
	            'cus_add2' => '', //customer address
	            'cus_city' => '',  //customer city
	            'cus_state' => '',  //state
	            'cus_postcode' => '', //postcode or zipcode
	            'cus_country' => 'Bangladesh',  //country
	            'cus_phone' => $phone, //customer phone number
	            'cus_fax' => 'Not¬Applicable',  //fax
	            'ship_name' => '', //ship name
	            'ship_add1' => '',  //ship address
	            'ship_add2' => '',
	            'ship_city' => '',
	            'ship_state' => '',
	            'ship_postcode' => '',
	            'ship_country' => 'Bangladesh',
	            'desc' => 'top up wallet',
	            'success_url' => $wo['config']['site_url'] . "/requests.php?f=aamarpay&s=success_aamarpay", //your success route
	            'fail_url' => $wo['config']['site_url'] . "/requests.php?f=aamarpay&s=cancel_aamarpay", //your fail route
	            'cancel_url' => $wo['config']['site_url'] . "/requests.php?f=aamarpay&s=cancel_aamarpay", //your cancel url
	            'opt_a' => '',  //optional paramter
	            'opt_b' => '',
	            'opt_c' => '',
	            'opt_d' => '',
	            'signature_key' => $wo['config']['aamarpay_signature_key'] //signature key will provided aamarpay, contact integration@aamarpay.com for test/live signature key
	        );
	        $fields_string = http_build_query($fields);

	        $ch = curl_init();
	        curl_setopt($ch, CURLOPT_VERBOSE, true);
	        curl_setopt($ch, CURLOPT_URL, $url);

	        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
	        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	        $result = curl_exec($ch);
	        $url_forward = str_replace('"', '', stripslashes($result));
	        curl_close($ch);
	        $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('aamarpay_tran_id' => $tran_id));
	        if ($wo['config']['aamarpay_mode'] == 'sandbox') {
	            $base_url = 'https://sandbox.aamarpay.com/'.$url_forward;
	        }
	        else {
	            $base_url = 'https://secure.aamarpay.com/'.$url_forward;
	        }
	        $data['status'] = 200;
			$data['url'] = $base_url;
		}
		else{
			$data['status'] = 400;
			$data['message'] = $wo['lang']['please_check_details'];
		}
		header("Content-type: application/json");
	    echo json_encode($data);
	    exit();
	}
	if ($s == 'success_aamarpay') {
		if (!empty($_POST['amount']) && !empty($_POST['mer_txnid']) && !empty($_POST['pay_status']) && $_POST['pay_status'] == 'Successful') {
			$user = $db->objectBuilder()->where('aamarpay_tran_id',Wo_Secure($_POST['mer_txnid']))->getOne(T_USERS);
			if (!empty($user)) {
				$amount   = (int)Wo_Secure($_POST['amount']);
				$db->where('user_id', $user->user_id)->update(T_USERS, array(
                    'wallet' => $db->inc($amount),
                    'aamarpay_tran_id' => ''
                ));

                $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $user->user_id . "', 'WALLET', '" . $amount . "', 'aamarpay')");
                $_SESSION['replenished_amount'] = $amount;
			}
		}
		if (!empty($_COOKIE['redirect_page'])) {
        	$redirect_page = preg_replace('/on[^<>=]+=[^<>]*/m', '', $_COOKIE['redirect_page']);
		    $redirect_page = preg_replace('/\((.*?)\)/m', '', $redirect_page);
        	header("Location: " . $redirect_page);
        }
        else{
        	header("Location: " . Wo_SeoLink('index.php?link1=wallet'));
        }
        exit();
	}
	if ($s == 'cancel_aamarpay') {
		$db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('aamarpay_tran_id' => ''));
		header("Location: " . Wo_SeoLink('index.php?link1=wallet'));
	    exit();
	}
}