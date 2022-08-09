<?php
if ($f == 'fortumo') {
	if ($s == 'pay') {
		$hash = rand(11111,55555).rand(11111,55555);
	    $db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('fortumo_hash' => $hash));
	    $data['status'] = 200;
		$data['url'] = 'https://pay.fortumo.com/mobile_payments/'.$wo['config']['fortumo_service_id'].'?cuid='.$hash;
		header("Content-type: application/json");
        echo json_encode($data);
        exit();
	}
	if ($s == 'success_fortumo') {
		if (!empty($_GET) && !empty($_GET['amount']) && !empty($_GET['status']) && $_GET['status'] == 'completed' && !empty($_GET['cuid']) && !empty($_GET['price'])) {
	        $fortumo_hash = Wo_Secure($_GET['cuid']);
	        $amount = (int) Wo_Secure($_GET['price']);
	        $user = $db->objectBuilder()->where('fortumo_hash',$fortumo_hash)->getOne(T_USERS);
	        if (!empty($user)) {
	        	$db->where('user_id', $user->user_id)->update(T_USERS, array(
                    'wallet' => $db->inc($amount),
                    'fortumo_hash' => ''
                ));

                $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $user->user_id . "', 'WALLET', '" . $amount . "', 'fortumo')");
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
}