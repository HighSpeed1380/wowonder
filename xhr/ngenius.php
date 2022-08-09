<?php
if ($f == 'ngenius') {
	if ($s == 'pay') {
		$data['status'] = 400;
		if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && $_POST['amount'] > 0) {
			$token = GetNgeniusToken();
			if (!empty($token->message)) {
				$data['status'] = 400;
		        $data['message'] = $token->message;
			}
			elseif (!empty($token->errors) && !empty($token->errors[0]) && !empty($token->errors[0]->message)) {
				$data['status'] = 400;
		        $data['message'] = $token->errors[0]->message;
			}
			else{
				$amount = (int) Wo_Secure($_POST['amount']);
				$postData = new StdClass();
			    $postData->action = "SALE";
			    $postData->amount = new StdClass();
			    $postData->amount->currencyCode = "AED";
			    $postData->amount->value = $amount;
			    $postData->merchantAttributes = new \stdClass();
		        $postData->merchantAttributes->redirectUrl = $wo['config']['site_url'] . "/requests.php?f=ngenius&s=success_ngenius";
		        // $postData->merchantAttributes->redirectUrl = "http://192.168.1.108/wowonder/requests.php?f=ngenius&s=success_ngenius";
			    $order = CreateNgeniusOrder($token->access_token,$postData);
			    if (!empty($order->message)) {
	    			$data['status'] = 400;
			        $data['message'] = $order->message;
	    		}
	    		elseif (!empty($order->errors) && !empty($order->errors[0]) && !empty($order->errors[0]->message)) {
	    			$data['status'] = 400;
			        $data['message'] = $order->errors[0]->message;
	    		}
	    		else{
	    			$db->where('user_id',$wo['user']['user_id'])->update(T_USERS,array('ngenius_ref' => $order->reference));
	    			$data['status'] = 200;
			        $data['url'] = $order->_links->payment->href;
	    		}
			}
		}
		else{
			$data = array(
	            'status' => 400,
	            'message' => $wo['lang']['empty_amount']
	        );
		}
		header("Content-type: application/json");
	    echo json_encode($data);
	    exit();
	}
	if ($s == 'success_ngenius') {
		if (!empty($_GET['ref'])) {
			$user = $db->objectBuilder()->where('ngenius_ref',Wo_Secure($_GET['ref']))->getOne(T_USERS);
			if (!empty($user)) {
				$token = GetNgeniusToken();
	    		if (!empty($token->message)) {
	    			header('Location: ' . Wo_SeoLink('index.php?link1=wallet'));
		        	exit();
	    		}
	    		elseif (!empty($token->errors) && !empty($token->errors[0]) && !empty($token->errors[0]->message)) {
	    			header('Location: ' . Wo_SeoLink('index.php?link1=wallet'));
		        	exit();
	    		}
	    		else{
	    			$order = NgeniusCheckOrder($token->access_token,$user->ngenius_ref);
	    			if (!empty($order->message)) {
		    			header('Location: ' . Wo_SeoLink('index.php?link1=wallet'));
			        	exit();
		    		}
		    		elseif (!empty($order->errors) && !empty($order->errors[0]) && !empty($order->errors[0]->message)) {
		    			header('Location: ' . Wo_SeoLink('index.php?link1=wallet'));
			        	exit();
		    		}
		    		else{
		    			if ($order->_embedded->payment[0]->state == "CAPTURED") {
							$amount = Wo_Secure($order->amount->value);
							$db->where('user_id', $wo['user']['user_id'])->update(T_USERS, array(
			                    'wallet' => $db->inc($amount),
			                    'aamarpay_tran_id' => ''
			                ));

			                $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $wo['user']['user_id'] . "', 'WALLET', '" . $amount . "', 'ngenius')");
			                $_SESSION['replenished_amount'] = $amount;
		    			}
		    		}
	    		}
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