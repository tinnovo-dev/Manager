<?php
	/**********************
	 * Modul de Contactes *
	 **********************/
	
	/**
	 * Amb aquesta funcio li pasarem pareametres a la template de Xslt que reben al script de PHP
	 * mitjançant $_GET / $_POST
	 *
	 * @param unknown_type $method
	 * @param unknown_type $name
	 * @return unknown
	 */
	/*
	function getParams2Xsl($method, $name){
		return $_REQUEST[$name];
		/*
		//$_GET[$name]
		if(method=='1'){
			return $_GET[$name];
		}
		//$_POST[$name]
		elseif ($method=='2'){
			return $_POST[$name];
		}
		
	}
	*/
	
	function processContact($params){
		global  $_MANAGER;
		
		//parametres ordenats a una array
		$arr_params = explode(',',$params);
		
		$sgbd = new mySGBD($_MANAGER['dsn']);
		
		$action = isset($_GET['action']) ? $_GET['action'] : null;
		
		// ens arrivar el formulari ...
		if(sizeof($_POST) > 0){
			//echo 'siiiiii';
			processContactForm($sgbd);
		}
		
		
		//print_r($_POST);
		//print_r($_GET);
		
		//print_r($_MANAGER['dsn']);
		//exit(0);
		
		/*
		// recuperem el id del block que volem llistar i el xsl per transformar el llistat
		$query = 'SELECT `contact_template`.`source` as xsl, `contact_form`.`source` as form 
					FROM `contact_template`, `contact_form` 
						WHERE `contact_template`.`id` = (SELECT `contact_template` 
															FROM `contact_view` 
																WHERE `id`='. $arr_params[0] . ') `contact_template` and `news_view`.`id`=' .$arr_params[0];
		*/													
		
		// recuperem el formulari i la template
		$query = 'SELECT `contact_form`.source as form, `contact_template`.source as xsl 
					FROM `contact_form` 
						inner join (`contact_template` 
										inner join `contact_view` 
											on `contact_template`.id =`contact_view`.contact_template and `contact_view`.id=' . $arr_params[0] . ') 
								on `contact_form`.id= `contact_view`.contact_form AND (`lang`="' . $_SESSION['lang'] . '" OR `lang`="ALL")';
		
		$rs = $sgbd->query($query);
			
		if ($rs === false) {
			// error al fer la query !!!
			echo $query;
			echo $sgbd->getLastErrorMessage() . '\n';
			print 'error SGBD query -> contact';
		}
			
		$row = $rs->fetchRow();
		
		$form = $row['form'];
		$xsl = $row['xsl'];	
		
		$sgbd->close();
				
		unset($rs,$sgbd,$row);

		//$hola = 'aiiiiiii'	;
		
		
		$xhtml = $result . getTransformXML($form,$xsl);
		
		//return $form;		
		return $xhtml; 
		//return '<p>formulari ....</p>';
	}
	
	
	/**
	 * Aquesta funcio procesara el formulari de contactes, envian mail, guardan a la bbdd ...
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function processContactForm(& $sgbd){
		global  $_MANAGER;
		
		//if($action == '1'){
			require($_MANAGER['lib'] . 'MAIL' . $_MANAGER['separator'] . 'class.phpmailer.php');
			
			$mail = new PHPMailer();
			
			$mail->Mailer = "smtp";
			$mail->Host = "smtp.jppintors.com";
			
			$mail->Username = "quim@jppintors.com"; 
  			$mail->Password = "W8ou7zT53mX";
  			
  			$mail->SMTPAuth = true;
  			
  			$mail->From = "quim@jppintors.com";
  			
  			$mail->FromName = "joaquim";
  			
  			//$mail->Timeout=120;
  			
  			$mail->AddAddress("jppintors@jppintors.com");
  			
  			$mail->Subject = "Prueba de phpmailer";
  			
  			$mail->Body = $_POST['contact_body'];
  			
  			// comentat mentres fem proves ...
			/*
			$exito = $mail->Send();

			//Si el mensaje no ha podido ser enviado se realizaran 4 intentos mas como mucho 
			//para intentar enviar el mensaje, cada intento se hara 5 segundos despues 
			//del anterior, para ello se usa la funcion sleep	
			$intentos=1; 
			while ((!$exito) && ($intentos < 5)) {
				sleep(5);
			    echo $mail->ErrorInfo;
			    $exito = $mail->Send();
			    $intentos=$intentos+1;				
			}
								
			if(!$exito){
				echo "Problemas enviando correo electr�nico a ".$valor;
				echo "<br>".$mail->ErrorInfo;
				// si va malament ...
				return false;
			}
			else
			{
				echo "Mensaje enviado correctamente";
			}
			*/
			$query = 'INSERT INTO `contact_message` 
						VALUES(' . ($sgbd->valueMax('contact_message','id') + 1) . ',\'' . $_POST['contact_email'] . '\',\'' . $_POST['contact_body'] . '\',\'' . $_SERVER['REMOTE_ADDR'] .'\',\'' . $_POST['contact_depart'] . '\')';
			
			$rs = $sgbd->query($query);
			
			if ($rs === false) {
				// error al fer la query !!!
				//echo $query;
				echo $sgbd->getLastErrorMessage() . '\n';
				print 'error SGBD query -> processContactForm';
			}
			
			unset($rs);
		//}
		
		// retornem true si tot a anat be ...
		return true;
	}
	
	function getComboContact($params){
		global  $_MANAGER;
		
		//parametres ordenats a una array
		$arr_params = explode(',',$params);
		
		$sgbd = new mySGBD($_MANAGER['dsn']);		
	}
?>