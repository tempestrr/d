<?php  
include 'function.php';

function referral($reff) {
	$fake_name = get('https://fakenametool.net/generator/random/id_ID/indonesia');
	preg_match_all('/<td>(.*?)<\/td>/s', $fake_name, $result);

	$name = $result[1][0];
	$user = explode(' ', $name);
	$domain = ['givmail.com','dropjar.com'];
	$rand = array_rand($domain);
	$email = str_replace(' ', '', strtolower($name)).number(2).'@'.$domain[$rand];
	$username = explode('@', $email);
	$password = random(8);


	$headers = [
		'locale: in',
		'Content-Type: application/x-www-form-urlencoded',
		'Host: api.adappter.kr',
		'User-Agent: okhttp/4.8.1',
		'Connection: Keep-Alive'
	];

	$send_email = post('api.adappter.kr/api/vA/v1/sign/auth/mail_send', 'email='.$email.'&lang=en', $headers);

	if (stripos($send_email, '"result":0')) {
		echo "\nSuccess send email verification | $email\n";
                ulang:
		$getmsg = get('https://getnada.com/api/v1/inboxes/'.$email);
		$id = fetch_value($getmsg, '"uid":"','"');
                if($id == ""){
                sleep(3);
                goto ulang;
                }
		if ($getmsg != "") {
			echo "Email verification found\n";
			$readmsg = get('https://getnada.com/api/v1/messages/html/'.$id);
			echo "Try to get otp code\n";

			if (stripos($readmsg, 'Please verify your email address')) {
				$code[1][1] = fetch_value($readmsg,'DIGIT CODE: [',']');
				echo "Success get otp code ".$code[1][1]."\n";

				$headers = [
					'locale: in',
					'Content-Type: application/x-www-form-urlencoded',
					'Host: api.adappter.kr',
					'User-Agent: okhttp/4.8.1',
					'Connection: Keep-Alive'
				];

				$check = post('api.adappter.kr/api/vA/v1/sign/auth/mail_check', 'email='.$email.'&a_num='.$code[1][1], $headers);

				if (stripos($check, '"result":0')) {
					echo "Success verif email\n";
					echo "Try register account\n";

					$headersx = [
						'locale: in',
						'Content-Type: application/json; charset=UTF-8',
						'Host: api.adappter.kr',
						'User-Agent: okhttp/4.8.1',
						'Connection: Keep-Alive'
					];

					$register = post('api.adappter.kr/api/vA/v1/sign/up', '{"email":"'.$email.'","pw":"'.$password.'","nick":"'.$name.'","name":"'.$name.'","code":"'.$reff.'"}', $headersx);

					if (stripos($register, '"result":0,')) {
						echo "Success to register | ".$email."\n";
					} else {
						echo "Failed to register\n";
					}
				} else {
					echo "Failed verif email\n";
				}

			} else {
				echo "Cannot get otp code\n";
			}
		} else {
			echo "Email verification not found\n";
		} 
	} else {
		echo "Failed to send email\n";
	}
}


echo "Referral : ";
$reff = trim(fgets(STDIN));

while (true) {
    referral($reff);
}












?>
