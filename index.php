<!DOCTYPE html>
<html>
<head>
	<title>Lab 8</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
</head>
<body>
	<header>
		<span>Lab 7<br>Sending mail</span>		
	</header>
	<main>
		<div class="sending">
			<?php
				if ($_SERVER['REQUEST_METHOD'] == 'POST'){
					
					$users = htmlentities(trim($_POST['users'], " ;"));
					$subject = htmlentities(trim($_POST['subject']));
					$subject = '=?utf-8?B?'.base64_encode($subject).'?=';
					$message = htmlentities(trim($_POST['message']));
					$headers = 'From: Захар Шишкин<zahar.shishkin.2001@mail.ru>' . "\r\n" .
    					'Reply-To: zahar.shishkin.2001@mail.ru' . "\r\n" .
    					'Content-type: text/plain; charset=utf-8';

					if ((empty($users) && empty($subject)) || (empty($users))){
						echo "Enter information!!";
					}
					elseif (empty($subject)){
						if (!mail_check($users)){
							echo "Enter subject and valid e-mails!";
							$users = "";
						}
						else
							echo "Enter information!";
					}
					elseif (!mail_check($users)){
						echo "Enter valid e-mails!";
						$users = "";
					}
					else{
						if (mail($users, $subject, $message, $headers)){
							echo '<span style="color: green">Success!</span>';
							user_write($users);
							$users = "";
							$subject = "";
							$message = "";
						}
						else
							echo '<span style="color: red">Error!</span>';
					}
				}
				stat_generator();


				function user_write($users){
					$f = fopen("users.txt", 'a'); 
					$a = explode(';', $users);
					$n = "\n";
					$str = date("d F Y h:i:s A\n"); 
					fwrite($f, $str);
					foreach ($a as $v)
						fwrite($f, $v . $n);
					fwrite($f, "----------------\n");
					fclose($f);
				}

				function mail_check($users){
					$users = explode(';', $users);
					foreach ($users as $v) {
						if (!preg_match("/[0-9a-z]+@[a-z]/", $v))
							return false;
					}
					return true;
				}

				function stat_generator(){
					$host = 'localhost';
					$database = 'statistics';
					$user = 'root';
					$password = 'root';
					$link = mysqli_connect($host, $user, $password, $database) 
    					or die("Ошибка " . mysqli_error($link));

					$os = php_uname('v');
					$pattern = '/\(.+\)/';
					preg_match($pattern, $os, $match);
					$os = trim($match[0], '(');
					$os = trim($os, ')');
					echo $os;

					$query ="SELECT * FROM os WHERE name = '$os'";
					$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
					if($result)
					{
						$rows = mysqli_num_rows($result);
						//echo "Successful request.";					    
					}

					if ($rows == 0){
						$query = "INSERT INTO `os`(`key`, `name`, `count`) VALUES (NULL, '$os', 1)";
						$result = mysqli_query($link, $query) or die ("Error!");
						//echo "Creation.". '<br>';
					}
					else{
						$row = mysqli_fetch_row($result);
						$count = $row[2] + 1;
						$query = "UPDATE `os` SET `count`= '$count'";
						$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 
						if($result){
							//echo "Ok";
						}
			?>
			<div class="info">
			<?php
						$query ="SELECT * FROM os";
						$result = mysqli_query($link, $query) or die("Ошибка " . mysqli_error($link)); 

						echo "<table><tr><th>Id</th><th>OS</th><th>Количество</th></tr>";

						for ($i = 0 ; $i < $rows ; ++$i)
					    {
					        $row = mysqli_fetch_row($result);
					        echo "<tr>";
					            for ($j = 0 ; $j < 3 ; ++$j) echo "<td>$row[$j]</td>";
					        echo "</tr>";
					    }
					    echo "</table>";
					     
					    // очищаем результат
					    mysqli_free_result($result);
					}
					mysqli_close($link);
				}

				function getRealIpAddr() {
				  if (!empty($_SERVER['HTTP_CLIENT_IP'])){
				  	$ip=$_SERVER['HTTP_CLIENT_IP']; 
				  }				  	
				  elseif 
				  	(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
				  { 
				  	$ip=$_SERVER['HTTP_X_FORWARDED_FOR']; 
				  }
				  else { 
				  	$ip=$_SERVER['REMOTE_ADDR']; 
				  }
				  return $ip;
				}	
			?>	
			</div>
			<form action="" method="POST">
				<span>To</span><br>
				<input class = "fields" required type="text" name="users" value = "<?php echo $users; ?>"><br>
				<span>Subject</span><br>
				<input class = "fields" required type="text" name="subject" value = "<?php echo $subject; ?>"><br>
				<span>Message</span><br>
				<textarea name="message" cols="60" rows="10"><?php echo $message; ?></textarea><br>
				<input type="submit" name="send"><br>
		
				
			</form>
		</div>
	</main>
</body>
</html>