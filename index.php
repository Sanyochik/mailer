<?php
header("Content-Type: text/html; charset=utf-8");


require_once("functions.php");

$mail_login    = "DreamHollo@yandex.ru";
$mail_password = "authpass";
$mail_imap 	   = "{imap.yandex.ru:993/imap/ssl}";

	// Список учитываемых типов файлов
$mail_filetypes = array(
	"MSWORD"
);

$connection = imap_open($mail_imap, $mail_login, $mail_password);

if(!$connection){

	echo("Ошибка соединения с почтой - ".$mail_login);
	exit;
}else{
	$i = imap_search($connection, 'UNSEEN');
	$i = $i[0];
	if ($i!=''){
		$msg_num = imap_num_msg($connection);

		$mails_data = array();

			// Шапка письма
		$msg_header = imap_header($connection, $i);

		$mails_data[$i]["time"] = time($msg_header->MailDate);
		$mails_data[$i]["date"] = $msg_header->MailDate;

		foreach($msg_header->to as $data){

			$mails_data[$i]["to"] = $data->mailbox."@".$data->host;
		}

		foreach($msg_header->from as $data){

			$mails_data[$i]["from"] = $data->mailbox."@".$data->host;
		}

		$mails_data[$i]["title"] = get_imap_title($msg_header->subject);

			// Тело письма
		$msg_structure = imap_fetchstructure($connection, $i);
		$msg_body 	   = imap_fetchbody($connection, $i, 1);
		$body 		   = "";

		$recursive_data = recursive_search($msg_structure);

		if($recursive_data["encoding"] == 0 ||
			$recursive_data["encoding"] == 1){

			$body = $msg_body;
	}

	if($recursive_data["encoding"] == 4){

		$body = structure_encoding($recursive_data["encoding"], $msg_body);
	}

	if($recursive_data["encoding"] == 3){

		$body = structure_encoding($recursive_data["encoding"], $msg_body);
	}

	if($recursive_data["encoding"] == 2){

		$body = structure_encoding($recursive_data["encoding"], $msg_body);
	}

	if(!check_utf8($recursive_data["charset"])){

		$body = convert_to_utf8($recursive_data["charset"], $msg_body);
	}

	$mails_data[$i]["body"] = $body;
	print_r($mails_data[$i]["body"]);
	$check2=strpos($mails_data[$i]["body"],"Слетать.ру");
	echo '<br>';
	echo $check2;
	echo '<pre>';
	imap_close($connection);
	$nameend=strpos($mails_data[$i]["body"],"Контактный");
	$end=$nameend-$name-25;
	$name = substr($mails_data[$i]["body"],23,$end);
	echo $name;
	$phonestart=strpos($mails_data[$i]["body"],"Контактный телефон:");
	$phonestart = $phonestart+37;
	$phoneend = strpos($mails_data[$i]["body"],"Электронная почта:");
	$phoneend = $phoneend-$phonestart-2;
	$phone = substr($mails_data[$i]["body"],$phonestart,$phoneend);
	echo $phone;
	$mailstart = strpos($mails_data[$i]["body"],"Электронная почта:");
	$mailstart = $mailstart+35;
	$mailend = strpos($mails_data[$i]["body"],'Комментарий к заказу:');
	$mailend = $mailend-$mailstart-2;
	$mail = substr($mails_data[$i]["body"],$mailstart,$mailend);
	echo $mail;
	$commentstart = strpos($mails_data[$i]["body"],"Комментарий к заказу:");
	$commentstart = $commentstart+40;
	$commentend = strpos($mails_data[$i]["body"],'**');
	$commentend = $commentend-$commentstart-4;
	$comment = substr($mails_data[$i]["body"],$commentstart,$commentend);
	echo $comment;
	$infostart = strpos($mails_data[$i]["body"],"**");
	$infostart = $infostart+6;
	$infoend = strpos($mails_data[$i]["body"],'---');
	$infoend = $infoend-$infostart-4;
	$info = substr($mails_data[$i]["body"],$infostart,$infoend);
	echo $info;
	$info=str_replace("
","<br>",$info);
	echo $newinfo;
	$b24Url = "URL";	// укажите URL своего Битрикс24
	$b24UserID = "1";						// ID пользователя, от имени которого будем добавлять лид
	$b24WebHook = "code";		// код вебхука, который мы только что получили
	
	// формируем URL, на который будем отправлять запрос
	$queryURL = "$b24Url/rest/$b24UserID/$b24WebHook/crm.lead.add.json";	
	
	// формируем параметры для создания лида	
	$queryData = array(
		"fields" => array(
			"TITLE" => "".$mails_data[$i]['title']."",	// название лида
			"NAME" => "".$name."",				// имя ;)
			"PHONE" => array(	// телефон в Битрикс24 = массив, поэтому даже если передаем 1 номер, то передаем его в таком формате
				"n0" => array(
					"VALUE" =>  "".$phone."",
					"VALUE_TYPE" => "MOBILE",			
				),
			),
			"EMAIL" => array(
				"n0" => array(
					"VALUE" =>  "".$mail."",	
					"VALUE_TYPE" => "EMAIL",			
				),
			),
			"COMMENTS" => "".$comment."<br>".$info."",
		),
		'params' => array("REGISTER_SONET_EVENT" => "Y")	// Y = произвести регистрацию события добавления лида в живой ленте. Дополнительно будет отправлено уведомление ответственному за лид.	
	);
	print_r($queryData);
	$queryData =http_build_query($queryData);
	echo '<br>';
	print_r($queryData);
	// отправляем запрос в Б24 и обрабатываем ответ
	$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_SSL_VERIFYPEER => 0,
		CURLOPT_POST => 1,
		CURLOPT_HEADER => 0,
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => $queryURL,
		CURLOPT_POSTFIELDS => $queryData,
	));
	$result = curl_exec($curl);
	curl_close($curl);
	$result = json_decode($result,1); 

	// если произошла какая-то ошибка - выведем её
	if(array_key_exists('error', $result))
	{      
		die("Ошибка при сохранении лида: ".$result['error_description']);
	}
	
	echo "Лид добавлен, отличная работа :)";
	}
}
?>
