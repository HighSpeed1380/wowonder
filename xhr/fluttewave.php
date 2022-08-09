<?php
if ($f == 'fluttewave') {
	if ($s == 'pay') {
		$data['status'] = 400;
		if (!empty($_POST['amount']) && is_numeric($_POST['amount']) && !empty($_POST['email'])) {
			$email = $_POST['email'];
		    $amount = $_POST['amount'];

		    //* Prepare our rave request
		    $request = [
		        'tx_ref' => time(),
		        'amount' => $amount,
		        'currency' => 'NGN',
		        'payment_options' => 'card',
		        'redirect_url' => $wo['config']['site_url'] . "/requests.php?f=fluttewave&s=success",
		        'customer' => [
		            'email' => $email,
		            'name' => 'user_'.uniqid()
		        ],
		        'meta' => [
		            'price' => $amount
		        ],
		        'customizations' => [
		            'title' => 'Top Up Wallet',
		            'description' => 'Top Up Wallet'
		        ]
		    ];

		    //* Ca;; f;iterwave emdpoint
		    $curl = curl_init();

		    curl_setopt_array($curl, array(
		    CURLOPT_URL => 'https://api.flutterwave.com/v3/payments',
		    CURLOPT_RETURNTRANSFER => true,
		    CURLOPT_ENCODING => '',
		    CURLOPT_MAXREDIRS => 10,
		    CURLOPT_TIMEOUT => 0,
		    CURLOPT_FOLLOWLOCATION => true,
		    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		    CURLOPT_CUSTOMREQUEST => 'POST',
		    CURLOPT_POSTFIELDS => json_encode($request),
		    CURLOPT_HTTPHEADER => array(
		        'Authorization: Bearer '.$wo['config']['fluttewave_secret_key'],
		        'Content-Type: application/json'
		    ),
		    ));

		    $response = curl_exec($curl);

		    curl_close($curl);
		    
		    $res = json_decode($response);
		    if($res->status == 'success')
		    {
		    	$data['status'] = 200;
		        $data['url'] = $res->data->link;
		    }
		    else
		    {
		        $data['message'] = $wo['lang']['something_wrong'];
		    }
		}
		else{
			$data['message'] = $wo['lang']['please_check_details'];
		}
		header("Content-type: application/json");
	    echo json_encode($data);
	    exit();
	}
	if ($s == 'success') {
		if (!empty($_GET['status']) && $_GET['status'] == 'successful' && !empty($_GET['transaction_id'])) {
			$txid = $_GET['transaction_id'];

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.flutterwave.com/v3/transactions/{$txid}/verify",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => array(
                  "Content-Type: application/json",
                  "Authorization: Bearer ".$wo['config']['fluttewave_secret_key']
                ),
            ));
              
            $response = curl_exec($curl);
              
            curl_close($curl);
              
            $res = json_decode($response);
            if($res->status){
                $amount = $res->data->charged_amount;
                $db->where('user_id', $wo['user']['user_id'])->update(T_USERS, array(
                    'wallet' => $db->inc($amount)
                ));

                $create_payment_log = mysqli_query($sqlConnect, "INSERT INTO " . T_PAYMENT_TRANSACTIONS . " (`userid`, `kind`, `amount`, `notes`) VALUES ('" . $wo['user']['user_id'] . "', 'WALLET', '" . $amount . "', 'fluttewave')");
                $_SESSION['replenished_amount'] = $amount;
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
		header("Location: " . Wo_SeoLink('index.php?link1=wallet'));
        exit();
	}
}