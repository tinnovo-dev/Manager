<?php
	/******************
	 * Modul de Login *
	 ******************/
	
	
	
	/**
	 * Login user del Site
	 *
	 */
	function loginUserSite(){
		global $_MANAGER;
		
		
	}
	
	/**
	 * Login Administrador d'un Site
	 *
	 */
	function loginUserAdminSite(){
		
		// comprovem que arribar el formulari ...
		if(count($_POST) > 0){
			global $_MANAGER;
			
			/*
			print_r($_POST);
			print '--->' . sha1('1q2w3e4r');
			*/
			//separem el nom d'ususari del site user@domain.com
			list($user,$site) = explode('@',$_POST['login']);
			
			$user = trim($user);
			$site = trim($site);
			
			$pwd = $_POST['password'];
			
			
			$sgbd = new mySGBD($_MANAGER['dsn']);
			
			if ($sgbd->connect()) {
				$query = "SELECT `site`.id as site, `site`.domain, `user`.`id` as user ,`user`.`pwd`, `user`.`name` as user_name
							FROM `user`,`site` 
								WHERE `user`.`name`='" . $user . "' AND `site`.`domain`='" . $site . "'";
			
				$rs = $sgbd->query($query);
				
				if ($rs === false) {
					// error al fer la query !!!
					print 'error SGBD query -> loginUserAdminSite';
					return false;
				}
				
				$row = $rs->fetchRow();			
				
				if (($user === $row['user_name']) && ($pwd === $row['pwd']) && ($site === $row['domain']) && isUser4Site($sgbd, $row['user'], $row['site'])){
					$_SESSION['user_admin'] = $user;
					$_SESSION['id_site_admin'] = $row['site'];
					$_SESSION['domain_admin'] = $row['domain'];
					
					$_SESSION['site_admin_dsn'] = array(
							'phptype' 	=>	'mysql',
							'hostspec'	=>	'127.0.0.1:3306',
							'database'	=>	'manager',
							'username'	=>	'root',
							'password'	=>	''
							);
					
					header('Location: ?' . $_MANAGER['default_view_name']);
					exit(0);
				}
				else{					
					$_MANAGER['error'] = 1;
				}
					
			}
			else{
				print 'error SGBD Connect -> loginUserAdminSite';
				return false;
			}
			
			$sgbd->close();
			unset($sgbd,$rs);
		}
	}
	
	
	/**
	 * Aquesta funcio es dira si el usuari es un User del Site
	 *
	 * @param unknown_type $sgbd
	 * @param unknown_type $user
	 * @param unknown_type $site
	 */
	function isUser4Site(& $sgbd,$user,$site){
		
		$query='SELECT * 
					FROM `user_rel_site`
						WHERE `user`=' . $user . ' AND `site`=' . $site;				
		
		$rs = $sgbd->query($query);
		
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> isUser4Site';
			return false;
		}
				
		return !is_null($rs->fetchRow());
	}
	
	function controlErrorLogin($n_error){		
		include('errors.php');
		
		unset($_MANAGER['error']);
		
		return getMessage($ERROR[1]);
	}
?>