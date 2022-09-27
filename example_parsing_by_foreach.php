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
	$ides = imap_search($connection, 'UNSEEN');
	print_r($ides);
	if ($ides!=''){
		foreach ($ides as $key => $i) {
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
		$withouttags='ЭТО ТЕКСТ ПИСЬМА, ЧТОБЫ ЕГО ПРОЧИТАТЬ НАЖМИТЕ НА КНОПКУ "РАЗВЕРНУТЬ"';
		$withouttags.= strip_tags($mails_data[$i]["body"]);
		$withouttags=str_replace("





			", " ", $withouttags);
		$withouttags=str_replace("Document", "", $withouttags);
		$arrayfornewrow=array('       ','                                ','      ');
		$withouttags=str_replace($arrayfornewrow, "
			", $withouttags);
		$check=strpos($mails_data[$i]["body"],"Выставленный");
		$check2=strpos($mails_data[$i]["body"],"Слетать.Ру");
		if($check2!=''){
		if($check==''){
			$textstart=strpos($mails_data[$i]["body"],"<nobr>");
			$text = substr($mails_data[$i]["body"],$textstart);
			$textstart=strpos($text,"<nobr>");
			$textstart=$textstart+6;
			$text = substr($text,$textstart);
			$textstart=strpos($text,"<nobr>");
			$textstart=$textstart+6;
			$text = substr($text,$textstart);
			$textstart=strpos($text,"<nobr>");
			$textstart=$textstart+6;
			$text = substr($text,$textstart);
			$textstart=strpos($text,"<nobr>");
			$textstart=$textstart+6;
			$text = substr($text,$textstart);
			$textstart=strpos($text,"<nobr>");
			$textstart=$textstart+6;
			$text = substr($text,$textstart);
			$textstart=strpos($text,"<nobr>");
			$text = substr($text,$textstart);
			$countryend =strpos($text,"</nobr>");
			$country = substr($text,0,$countryend);
			$nobrs =array("<nobr>","</nobr>","&#160;","(",")");
			$country = str_replace($nobrs,"",$country);
			$toururlstart = strpos($text,"Ссылка");
			if($toururlstart!=''){
				$toururl = substr($text,$toururlstart);
				$toururlstart = strpos($toururl,'<nobr>');
				$toururl = substr($toururl,$toururlstart);
				$toururlend = strpos($toururl,'</nobr>');
				$toururl = substr($toururl,0,$toururlend);
				$toururl = str_replace($nobrs,"",$toururl);
				echo $toururl;
			}
			$customerstart = strpos($text,"Заказчик:");
			$customer = substr($text,$customerstart);
			$customerstart = strpos($customer,'<nobr>');
			$customer = substr($customer,$customerstart);
			$customerend = strpos($customer,"</nobr>");
			$customer = substr($customer,0,$customerend);
			$nobrsonly =array("<nobr>","</nobr>");
			$customer = str_replace($nobrsonly,"",$customer);
			$name ='';
			$name_pos='';
			$LAST_NAME='';
			$LAST_NAME_pos='';
			$SECOND_NAME='';
			$SECOND_NAME_pos='';
			$LAST_NAME_pos = strpos($customer,"&#160");
			$LAST_NAME = substr($customer,0,$LAST_NAME_pos);
			$LAST_NAME_pos +=6;
			$name = substr($customer,$LAST_NAME_pos);
			if($name!=''){
				$name_pos = strpos($name,"&#160");
				if($name_pos ==''){
					$name = substr($customer,$LAST_NAME_pos);
				}else{
					$name = substr($customer,$LAST_NAME_pos,$name_pos);
					$name_pos=$name_pos+6+$LAST_NAME_pos;
					$SECOND_NAME = substr($customer,$name_pos);
					$SECOND_NAME_pos = strpos($SECOND_NAME,"&#160");
					if($SECOND_NAME_pos ==''){
						$SECOND_NAME = substr($customer,$name_pos);
					}else{
						$SECOND_NAME = substr($customer,$name_pos,$SECOND_NAME);
					}
					$SECOND_NAME = str_replace($nobrs,"",$SECOND_NAME);
				}
			}
			$name = str_replace($nobrs,"",$name);
			$contactstart = strpos($text,"Контакты:");
			$contactstart +=17;
			$contact = substr($text,$contactstart);
			$contactstart = strpos($contact,"<nobr>");
			$contact = substr($contact,$contactstart);
			$contactend = strpos($contact,"</nobr>");
			$phone = substr($contact,0,$contactend);
			$phone = str_replace($nobrs,"",$phone);
			$emailstart = strpos($contact,"href=");
			$email = substr($contact,$emailstart);
			$emailstart = strpos($email,'"');
			$emailstart+=1;
			$email = substr($email,$emailstart);
			$emailend = strpos($email,'"');
			$email = substr($email,0,$emailend);
			echo $email;
			echo '<br>';
			echo $phone;
			echo '<br>';
			echo $LAST_NAME;
			echo '<br>';
			echo $name;
			echo '<br>';
			echo $SECOND_NAME;
			echo '<br>';
			echo $country;
			echo '<br>';
			echo $customer;
 	$b24Url = "URL";	// укажите URL своего Битрикс24
	$b24UserID = "id";						// ID пользователя, от имени которого будем добавлять лид
	$b24WebHook = "codehook";		// код вебхука, который мы только что получили
	// формируем URL, на который будем отправлять запрос
	$queryURL = "$b24Url/rest/$b24UserID/$b24WebHook/crm.lead.add.json";	
	
	// формируем параметры для создания лида	
	$queryData = array(
		"fields" => array(
			"TITLE" => "".$mails_data[$i]['title']."",	// название лида
			"NAME" => "".$name."",				// имя ;)
			"LAST_NAME" => "".$LAST_NAME."",
			"SECOND_NAME" => "".$SECOND_NAME."",
			"PHONE" => array(	// телефон в Битрикс24 = массив, поэтому даже если передаем 1 номер, то передаем его в таком формате
				"n0" => array(
					"VALUE" =>  "".$phone."",
					"VALUE_TYPE" => "MOBILE",			
				),
			),
			"EMAIL" => array(
				"n0" => array(
					"VALUE" =>  "".$email."",	
					"VALUE_TYPE" => "EMAIL",			
				),
			),
			"UF_CRM_1659585541" => "".$country."",
			"UF_CRM_1660310109" => "".$toururl."",
			"COMMENTS" => "".$comment."<br>".$info."",
		),
		'params' => array("REGISTER_SONET_EVENT" => "Y")	// Y = произвести регистрацию события добавления лида в живой ленте. Дополнительно будет отправлено уведомление ответственному за лид.	
	);
	$queryData =http_build_query($queryData);
	echo $queryData;
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
	$b24Url = "URL";	// укажите URL своего Битрикс24
	$b24UserID = "id";						// ID пользователя, от имени которого будем добавлять лид
	$b24WebHook = "code";		// код вебхука, который мы только что получили
	
	// формируем URL, на который будем отправлять запрос
	$queryURL = "$b24Url/rest/$b24UserID/$b24WebHook/crm.timeline.comment.add.json";
	
	// формируем параметры для создания лида	
	$queryData = array(
		"fields" => array(
			'COMMENT' => "".$withouttags."",
			"ENTITY_ID" => "".$result['result']."",	// название лида
			'ENTITY_TYPE' => "lead",				// имя ;)
		),
	);
	$queryData =http_build_query($queryData);
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
}
else{
	$withouttags='ЭТО ТЕКСТ ПИСЬМА, ЧТОБЫ ЕГО ПРОЧИТАТЬ НАЖМИТЕ НА КНОПКУ "РАЗВЕРНУТЬ"';
	$withouttags.= strip_tags($mails_data[$i]["body"]);
	$withouttags=str_replace("





			", " ", $withouttags);
		$withouttags=str_replace("Document", "", $withouttags);
 	$b24Url = "URL";	// укажите URL своего Битрикс24
	$b24UserID = "id";						// ID пользователя, от имени которого будем добавлять лид
	$b24WebHook = "code";		// код вебхука, который мы только что получили
	
	// формируем URL, на который будем отправлять запрос
	$queryURL = "$b24Url/rest/$b24UserID/$b24WebHook/crm.lead.add.json";	
	
	// формируем параметры для создания лида	
	$queryData = array(
		"fields" => array(
			"TITLE" => "".$mails_data[$i]['title']."",	// название лида
		),
		'params' => array("REGISTER_SONET_EVENT" => "Y")	// Y = произвести регистрацию события добавления лида в живой ленте. Дополнительно будет отправлено уведомление ответственному за лид.	
	);
	$queryData =http_build_query($queryData);
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
	$b24Url = "URL";	// укажите URL своего Битрикс24
	$b24UserID = "id";						// ID пользователя, от имени которого будем добавлять лид
	$b24WebHook = "code";		// код вебхука, который мы только что получили
	
	// формируем URL, на который будем отправлять запрос
	$queryURL = "$b24Url/rest/$b24UserID/$b24WebHook/crm.timeline.comment.add.json";
	
	// формируем параметры для создания лида	
	$queryData = array(
		"fields" => array(
			'COMMENT' => "".$withouttags."",
			"ENTITY_ID" => "".$result['result']."",	// название лида
			'ENTITY_TYPE' => "lead",				// имя ;)
		),
	);
	$queryData =http_build_query($queryData);
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
}
}
// 	echo $name;
// 	$phonestart=strpos($mails_data[$i]["body"],"Контактный телефон:");
// 	$phonestart = $phonestart+37;
// 	$phoneend = strpos($mails_data[$i]["body"],"Электронная почта:");
// 	$phoneend = $phoneend-$phonestart-2;
// 	$phone = substr($mails_data[$i]["body"],$phonestart,$phoneend);
// 	echo $phone;
// 	$mailstart = strpos($mails_data[$i]["body"],"Электронная почта:");
// 	$mailstart = $mailstart+35;
// 	$mailend = strpos($mails_data[$i]["body"],'Комментарий к заказу:');
// 	$mailend = $mailend-$mailstart-2;
// 	$mail = substr($mails_data[$i]["body"],$mailstart,$mailend);
// 	echo $mail;
// 	$commentstart = strpos($mails_data[$i]["body"],"Комментарий к заказу:");
// 	$commentstart = $commentstart+40;
// 	$commentend = strpos($mails_data[$i]["body"],'**');
// 	$commentend = $commentend-$commentstart-4;
// 	$comment = substr($mails_data[$i]["body"],$commentstart,$commentend);
// 	echo $comment;
// 	$infostart = strpos($mails_data[$i]["body"],"**");
// 	$infostart = $infostart+6;
// 	$infoend = strpos($mails_data[$i]["body"],'---');
// 	$infoend = $infoend-$infostart-4;
// 	$info = substr($mails_data[$i]["body"],$infostart,$infoend);
// 	echo $info;
// 	$info=str_replace("
// ","<br>",$info);
}
imap_close($connection);
}
}
?>