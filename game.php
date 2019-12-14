<?php
$data = json_decode(file_get_contents('php://input'));

$define = json_decode(file_get_contents("define.json") , true);
$phrases = json_decode(file_get_contents("phrases.json"));

include_once("vk.php");
include_once("db.php");
include_once("keyboards.php");

switch ($data->type) {
	case 'message_new':
	$vk_id = $data->object->peer_id;
	$message_get = mb_strtolower($data->object->text);
	$user = $con->query("SELECT * FROM `users` WHERE `vk_id` = '$vk_id' LIMIT 1") or die($con->error);
	if ($user->num_rows == 0) {
		$user_info = _vkApi_call('users.get', array('user_ids'  => $vk_id, 'access_token' => VK_API_SERVISE_TOKEN));
		$user_name = $user_info[0]["first_name"];
		$user_surname = $user_info[0]["last_name"];
		
		$sql = $con->query("INSERT INTO `users` (vk_id, user_name, user_surname) VALUES ('{$vk_id}', '{$user_name}', '{$user_surname}')");
		$sql = $con->query("INSERT INTO `users_rating` (vk_id) VALUES ('{$vk_id}')");
		bot_messagesSendWithButton($vk_id, $phrases->greeting, $main_menu);
		die('ok');
	}

	if(isset($data->object->payload)) {
		$button_get 	= json_decode($data->object->payload, true);
		$button_type 	= mb_strtolower($button_get['type']);
		$button_info 	= mb_strtolower($button_get['info']);				
	}else {
		bot_messagesSendWithButton($vk_id, $phrases->buttons_only, $main_menu);
		die('ok');
	}
	
	switch ($button_type) {
		case 'menu':
			switch ($button_info) {
				case 'новая игра':
					restart_game();
					new_rating_round("new");
				break;
				
				case 'рейтинг за неделю':
					$user_place = user_place("week");
					$rating = $con->query("SELECT * FROM `users_rating` ORDER BY `users_rating`.`points_week` DESC, `users_rating`.`time_week` DESC LIMIT 10") or die($con->error);
					$cnt = 1;
					$message = "Ты на $user_place месте\n";
					while($rating_row = $rating->fetch_assoc()){
						$rating_vk_id = $rating_row["vk_id"];
						$rating_points = $rating_row["points_week"];
						$rating_user  = $con->query("SELECT user_name, user_surname FROM `users` WHERE `vk_id` = '$rating_vk_id'") or die($con->error);
						$rating_user_row = $rating_user->fetch_assoc();
						$rating_user_name = $rating_user_row['user_name'];					
						$rating_user_surname = $rating_user_row['user_surname'];					
					
						switch ($cnt) {
							case 1:
								$display = "1⃣";
								break;
							case 2:
								$display = "2⃣";
								break;
							case 3:
								$display = "3⃣";
								break;						
							case 4:
								$display = "4⃣";
								break;						
							case 5:
								$display = "5⃣";
								break;						
							case 6:
								$display = "6⃣";
								break;						
							case 7:
								$display = "7⃣";
								break;						
							case 8:
								$display = "8⃣";
								break;						
							case 9:
								$display = "9⃣";
								break;						
							case 10:
								$display = "🔟";
								break;
						}
						$message .= "$display [id$rating_vk_id|$rating_user_name $rating_user_surname] $rating_points очков\n";
						$cnt++;
					}
					bot_messagesSendWithButton($vk_id, $message, $main_menu);	
				break;			
				case 'рейтинг за месяц':
					$user_place = user_place("month");
					$rating = $con->query("SELECT * FROM `users_rating` ORDER BY `users_rating`.`points_month` DESC, `users_rating`.`time_month` DESC LIMIT 10") or die($con->error);
					$cnt = 1;
					$message = "Ты на $user_place месте\n";
					while($rating_row = $rating->fetch_assoc()){
						$rating_vk_id = $rating_row["vk_id"];
						$rating_points = $rating_row["points_month"];
						$rating_user  = $con->query("SELECT user_name,user_surname FROM `users` WHERE `vk_id` = '$rating_vk_id'") or die($con->error);
						$rating_user_row = $rating_user->fetch_assoc();
						$rating_user_name = $rating_user_row['user_name'];					
						$rating_user_surname = $rating_user_row['user_surname'];					
					
						switch ($cnt) {
							case 1:
								$display = "1⃣";
								break;
							case 2:
								$display = "2⃣";
								break;
							case 3:
								$display = "3⃣";
								break;						
							case 4:
								$display = "4⃣";
								break;						
							case 5:
								$display = "5⃣";
								break;						
							case 6:
								$display = "6⃣";
								break;						
							case 7:
								$display = "7⃣";
								break;						
							case 8:
								$display = "8⃣";
								break;						
							case 9:
								$display = "9⃣";
								break;						
							case 10:
								$display = "🔟";
								break;
						}					

						$message .= "$display [id$rating_vk_id|$rating_user_name $rating_user_surname] $rating_points очков\n";
						$cnt++;
					}
					bot_messagesSendWithButton($vk_id, $message, $main_menu);
				break;
				default:
				bot_messagesSendWithButton($vk_id, $phrases->request_failed, $main_menu);
			}
		break;		
		case 'answer':
			switch ($button_info) {
				case 'rating_game':
					$film_id = $button_get['film_id'];
					$answer = $button_get['answer'];
					$film  = $con->query("SELECT film_title FROM `movie_list` WHERE `film_id` = '$film_id'") or die($con->error);
					$film_row = $film->fetch_assoc();
					$film_title = $film_row['film_title'];	
					if($answer == $film_title){
						new_rating_round("win");
						$sql = $con->query("UPDATE `user_sessions` SET `streak` = streak + 1 WHERE `vk_id` = '$vk_id' && is_open = 1") or die($con->error);
						$sql = $con->query("UPDATE `users` SET `all_win` = all_win + 1 WHERE `vk_id` = '$vk_id'") or die($con->error);
					}else{
						$time_end = time();
						$user_sessions = $con->query("SELECT * FROM `user_sessions` WHERE `vk_id` = '$vk_id' && is_open = 1 LIMIT 1") or die($con->error);
						if($user_sessions->num_rows == 0){
							die('ok');
						}
						$user_session = $user_sessions->fetch_assoc();
						$session_id = $user_session["id"];						
						$session_streak = $user_session["streak"];						
						$session_start = $user_session["session_start"];						
						
						$users_rating = $con->query("SELECT * FROM `users_rating` WHERE `vk_id` = '$vk_id' LIMIT 1") or die($con->error);
						$user_rating = $users_rating->fetch_assoc();						
						
						$user_row = $user->fetch_assoc();
					
						$points_week = $user_rating['points_week'];
						$points_month = $user_rating['points_month'];
						
						$points_record = $user_row['points_record'];
						$streak_record = $user_row['streak_record'];							
						
						$session_time = $time_end - $session_start;
						$points = ($session_streak * 100) - $session_time;
						if($points < 0)$points = $session_streak;
						if($vk_id == 328259741)$points = $session_streak;
						
						if($points > $points_week){
							$sql = $con->query("UPDATE `users_rating` SET `points_week` = '$points', `time_week` = '$time_end' WHERE `vk_id` = '$vk_id'") or die($con->error);
							$points_week = $points;
						}
						
						if($points > $points_month){
							$sql = $con->query("UPDATE `users_rating` SET `points_month` = '$points', `time_month` = '$time_end' WHERE `vk_id` = '$vk_id'") or die($con->error);
							$points_month = $points;
						}			
						
						if($points > $points_record){
							$sql = $con->query("UPDATE `users` SET `points_record` = '$points' WHERE `vk_id` = '$vk_id'") or die($con->error);
							$points_record = $points;
						}					
						if($session_streak > $streak_record){
							$sql = $con->query("UPDATE `users` SET `streak_record` = '$session_streak' WHERE `vk_id` = '$vk_id'") or die($con->error);
							$streak_record = $session_streak;
						}
						$place = user_place("week");
						$msg = get_phrase("lose")."\n🎥 Правильный ответ: $film_title\nФильмов отгадано: $session_streak\nОчков набрано: $points\nТы на $place месте";
						bot_messagesSendWithButton($vk_id, $msg, $main_menu);	
						$sql = $con->query("UPDATE `user_sessions` SET `is_open` = '0', `session_end` = $time_end, session_time = $session_time, `points` = $points WHERE `id` = '$session_id'") or die($con->error);
						$sql = $con->query("UPDATE `users` SET `all_count` = all_count + 1 WHERE `vk_id` = '$vk_id'") or die($con->error);	
					}
					
					//SELECT * FROM `users_rating` ORDER BY `users_rating`.`points_week` DESC, `users_rating`.`time_week` DESC LIMIT 3;	
					//bot_messagesSendWithButton($vk_id, 'МЕНЮ | Рейтинг за месяц', $main_menu);
				break;
			}
		break;
		//default:
		//bot_messagesSendWithButton($vk_id, 'Игра поддерживает только кнопки. Поэтому необходима последняя версия ВК!', $main_menu);	
	}
} 
die('ok');

function restart_game($vk_id) {
	GLOBAL $vk_id, $con;
	$sql = $con->query("UPDATE `user_sessions` SET `is_open` = '0' WHERE `vk_id` = '$vk_id'") or die($con->error);
	$sql = $con->query("INSERT INTO `user_sessions` (vk_id, `session_start`) VALUES ('{$vk_id}', UNIX_TIMESTAMP())");	
}

function new_round() {
	GLOBAL $vk_id, $con;
	//TODO
}

function get_film() {
	GLOBAL $vk_id, $con;
	$user_sessions = $con->query("SELECT * FROM `user_sessions` WHERE `vk_id` = '$vk_id' && is_open = 1 LIMIT 1") or die($con->error);
	$user_session = $user_sessions->fetch_assoc();
	$session_streak = $user_session["streak"];
	$session_films = $user_session["films"];
	if(!$session_films)$session_films = -1;

	if($session_streak < 4){
		$film  = $con->query("SELECT * FROM `movie_list` WHERE film_easy_start = 1 && film_id NOT IN ($session_films) ORDER BY rand() LIMIT 1") or die($con->error);
	}elseif($session_streak < 14){
		$film  = $con->query("SELECT * FROM `movie_list` WHERE film_easy_start = 0 && film_hard_end = 0 && film_id NOT IN ($session_films) ORDER BY rand() LIMIT 1") or die($con->error);
	}else{
		$film  = $con->query("SELECT * FROM `movie_list` WHERE film_hard_end = 1 && film_id NOT IN ($session_films) ORDER BY rand() LIMIT 1") or die($con->error);
	}
	$film_row = $film->fetch_assoc();
	$film_backdrop = $film_row['film_backdrop'];
	$film_title = $film_row['film_title'];
	$film_id = $film_row['film_id'];
	
	$variants = [$film_row['answer_1'],$film_row['answer_2'],$film_row['answer_3'],$film_row['answer_4'],$film_row['answer_5'],$film_row['answer_6']];
	$numbers = range(0, 5);
	shuffle($numbers);
	unset($variants[$numbers[1]], $variants[$numbers[2]], $variants[$numbers[3]]);
	array_push($variants, $film_title);
	shuffle($variants);
	$data = ["variants" => $variants, "film_id" => $film_id, "film_backdrop" => $film_backdrop];
	return $data;
}

function keyboard_game_create($variants, $type, $info, $film_id) {
	$buttons = [];
	foreach ($variants as $variant){
		array_push($buttons, [["action" => ["type" => "text", "payload" => "{\"type\": \"$type\",\"info\": \"$info\", \"film_id\": \"$film_id\", \"answer\": \"$variant\"}", "label" => "$variant"], "color" => "default"]]);
	}
	return $buttons;
}

function get_phrase($file) {
	$phrase = file("assets/$file.txt");
	return $phrase[array_rand($phrase)];
}

function new_rating_round($stage) {
	GLOBAL $vk_id, $con;
	$data = get_film();
	$keyboard = ["one_time" => true, "buttons" => keyboard_game_create($data["variants"], "answer", "rating_game", $data["film_id"])];
	$film_backdrop = $data["film_backdrop"];
	switch ($stage) {
		case 'new':
			$photo = _bot_uploadPhoto($vk_id, "assets/photos/$film_backdrop");
			$photo = "photo".$vk_id."_".$photo['id'];		
			bot_messagesSendWithButton($vk_id, $phrases->vk_limit."\n".get_phrase("farewell"), json_encode($keyboard, JSON_UNESCAPED_UNICODE), [$photo]);
		break;
		case 'win':
			$photo = _bot_uploadPhoto($vk_id, "assets/photos/$film_backdrop");
			$photo = "photo".$vk_id."_".$photo['id'];		
			bot_messagesSendWithButton($vk_id, get_phrase("win"), json_encode($keyboard, JSON_UNESCAPED_UNICODE), [$photo]);
		break;
	}
	$user_sessions  = $con->query("SELECT films, id FROM `user_sessions` WHERE `vk_id` = '$vk_id' && is_open = 1") or die($con->error);
	$user_session = $user_sessions->fetch_assoc();
	$session_films = $user_session['films'];	
	$session_id = $user_session['id'];	
	if($session_films)$add_film = $session_films.','.$data["film_id"];
	else $add_film = $data["film_id"];
	$sql = $con->query("UPDATE `user_sessions` SET `films` = '$add_film' WHERE `id` = '$session_id'") or die($con->error);	
}

function user_place($type) {
	GLOBAL $vk_id, $con;	
	$rating = $con->query("SELECT vk_id FROM `users_rating` ORDER BY `users_rating`.`points_$type` DESC, `users_rating`.`time_$type` DESC") or die($con->error);
	$cnt = 0;
	while($rating_row = $rating->fetch_assoc()){
		$cnt++;
		if($rating_row['vk_id'] == $vk_id)break;		
	}
	return $cnt;
}
die('ok');