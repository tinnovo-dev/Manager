<?php
	/*********************************************
	 * fitxer amb funcions de suport per el core *
	 *********************************************/
	// incloem les llibreries imprescindible per el funcionament de la app
	require_once($_MANAGER['lib'] . 'SGBD' . $_MANAGER['separator'] . 'mySGBD.class.php');
	//require($_MANAGER['lib'] . 'SGBD' . $_MANAGER['separator'] . 'mySGBD.class.php');

	require_once($_MANAGER['lib'] . 'XML' . $_MANAGER['separator'] . 'commons.php');	
	//require($_MANAGER['lib'] . 'XML' . $_MANAGER['separator'] . 'commons.php');
	
	require_once($_MANAGER['lib'] . 'SUPPORT' . $_MANAGER['separator'] . 'support.php');
	
	/**
	 * Funcio per inicialitzar la sessio d'Administracio
	 *
	 */
	function start_session(){
		global $_MANAGER;
		
		session_name('sid_' . $_MANAGER['site_name']);
		session_start();
	}
	
	/**
	 * Amb aquesta funcio li pasarem valors de variables $_GET/$_POST/$_SERVER/$_SESSION a la template de Xslt
	 *
	 * @param unknown_type $method
	 * @param unknown_type $name
	 * @return unknown
	 */
	function getParams2Xsl($method, $name){
		
		switch ((integer)$method){
			case 1:
				return $_REQUEST[$name];	// $_GET/$_POST
			break;
			case 2:
				return $_SERVER[$name];		// $_SERVER['']
			break;
			case 3:
				return $_SESSION[$name];	// $_SESSION['']
			break;
			case 4:
				global $_MANAGER;
				return $_MANAGER[$name];	// $_MANAGER['']
			break;
		}		
	}
	
	function getValueVar($params, $separator = ':'){
		$params = explode($separator, $params);
		
		switch ((integer)$params[0]){
			case 0:				
				global $_MANAGER;
				return $_MANAGER[$params[1]];	// $_MANAGER['']
			break;
			case 1:
				return $_SESSION[$params[1]];	// $_SESSION['']				
			break;
		}
	}
	
	/**
	 * Aquesta funcio ens retorna el xml d'un doc o doc_part i la seva plantilla XSL que te relacionada
	 *
	 * @param unknown_type $view_id
	 * @param unknown_type $sgbd
	 * @param unknown_type $tbl
	 * @return unknown
	 */
	function getXmlandXsl2View($view_id, $sgbd, $tbl, $is_doc_view = false, $lang = null){
		global $_MANAGER;
				
		if($tbl == 'doc'){
			
				$query_xml = 'SELECT `doc`.source as xml 
								FROM `doc` 
									WHERE `doc`.`id`=(SELECT `doc` 
														FROM `doc_view` 
															WHERE `id`=' . $view_id . ' AND 
																(`lang`="' . $lang . '" OR 
																	`lang`="ALL"))';
				$query_xsl = 'SELECT `doc_template`.source as xsl 
								FROM `doc_template` 
									WHERE `doc_template`.`id`=(SELECT `doc_template` 
														FROM `doc_view` 
															WHERE `doc_view`.`id`=' . $view_id . ' AND 
																(`doc_view`.`lang`="' . $lang . '" OR 
																	`doc_view`.`lang`="ALL"))';
		}
		elseif ($tbl === 'doc_part'){
			$query_xml = 'SELECT `doc_part`.source as xml 
								FROM `doc_part` 
									WHERE `doc_part`.`id`=(SELECT `doc_part` 
															FROM `doc_part_view` 
																WHERE `doc_part_view`.`id`=' . $view_id . ') AND 
																	(`doc_part`.`lang`="' . $lang . '" OR 
																		`doc_part`.`lang`="ALL")' . ($is_doc_view ? ' AND `doc_part`.`doc_part`=' . $_MANAGER['view_id'] : '');
				
				$query_xsl = 'SELECT `doc_part_template`.source as xsl 
								FROM `doc_part_template` 
									WHERE `doc_part_template`.`id`=(SELECT `doc_part_template` 
															FROM `doc_part_view` 
																WHERE `doc_part_view`.`id`=' . $view_id . ')';			
		}
		
		//print $query_xsl;

		// millor fer una query per cada taula ...
		if ($sgbd->connect()) {
			// XML

			$rs = $sgbd->query($query_xml);
			//print $query_xml . '<br />';
			
			if ($rs === false) {
				print $query_xml . '<br />';
				// error al fer la query !!!
				print 'errorrrrr ...';
			}
									
			$row_xml = $rs->fetchRow();
			
			// XSL		

			$rs = $sgbd->query($query_xsl);
			//print $query_xsl . '<br />';
			if ($rs === false) {
				print $query_xsl . '<br />';
				// error al fer la query !!!
				print 'errorrrrr ...';
			}
									
			$row_xsl = $rs->fetchRow();
		}
		else{
			// error al conectar a Gestor de BBDD!!!
			print 'errorrrrr ...';
		}

		//return array(html_entity_decode($row_xml['xml'],null,"utf-8"),$row_xsl['xsl']);
		return array($row_xml['xml'],$row_xsl['xsl']);
	}	
	
	
	/**
	 * Aquesta funcio ens retorna un document o parts de document que aniren ensamblan en la construccion del document final
	 *
	 * @param unknown_type $xml
	 * @param unknown_type $sgbd
	 * @return unknown
	 */
	
	function constructGetDoc(& $source, & $id, & $params, $is_doc_view = false){
		global $_MANAGER;
				
		$sgbd = new mySGBD($_MANAGER['dsn']);
			
		list($xml, $xsl) = getXmlandXsl2View($id, $sgbd, $source, $is_doc_view, $_SESSION['lang']);
						
		$sgbd->close();
		$sgbd = null;
		
		// preguntem si hi ha xsl per transformar el xml, si no hi ha retornem el string directament que troben ...
		return $xsl === null ? $xml : getTransformXML($xml, $xsl);
				
	}
	
	/**
	 * Aquesta funcio cridara al process necesari del modul especificat per retorna un xml parsejat
	 *
	 * @param unknown_type $module
	 * @param unknown_type $function
	 * @param unknown_type $params
	 * @return unknown
	 */
	function processingInstruction($module, $function, $params){
		global $_MANAGER;		
				
		require_once($_MANAGER['modules'] . $module . $_MANAGER['separator'] . $module . '.php');
		
		$xhtml = $function($params);
		//echo 'wwwwwwwwww';
		return $xhtml;
	}
	
	/**
	 * Funcio redirecciona cap al login si es un site que te validacio d'usuaris, ja sigui x q es un
	 * Site d'Administracio o te control d'usuaris registrats al Site
	 *
	 */
	function login(){	
		global $_MANAGER;
		//si es un site d'Administracio
		if(isset($_MANAGER['admin'])){			
			header("Cache-Control: no-cache, must-revalidate");
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Content-Type: text/html; charset=UTF-8",true);
			header("Cache-control: private");

			//print $_MANAGER['view_name'];
			
			// comprovem si esta validat ja ...
			if(!isset($_SESSION['user_admin'])){
				header('Location: ?login');
				exit(0);
			}
			else if ((strcmp($_MANAGER['view_name'],'logout') == 0)) {				
			
				session_destroy();	
//				print 'uooooooo'			;
				header('Location: ?' . $_MANAGER['default_view_name']);
				exit(0);
			}
			
		}
	}

	/**
	 * Aquesta funcion s'encarrega de fer la transformacio XSLT del xml
	 *
	 * @param unknown_type $xml
	 * @param unknown_type $xsl
	 * @return unknown
	 */
	/*
	function getTransformXML(& $xml, & $xsl, $parse_html_entities = false){
		//$xmldoc = DOMDocument::loadXML(html_entity_decode($xml,null,"utf-8"));
		$xmldoc = DOMDocument::loadXML($xml);
		
		$xsldoc = DOMDocument::loadXML($xsl);

		$proc = new XSLTProcessor();
		
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsldoc);
		
		//$xmldoc = htmlentities($xmldoc);
		
		$xml_trasform = $proc->transformToXML($xmldoc);
		$proc = null;		
		
		return $parse_html_entities ? html_entity_decode($xml_trasform,null,"utf-8") : $xml_trasform;
		//return $xml_trasform;
	}
	*/
	
	/**
	 * Funcio retorna el id de la vista a partir del nom d'una vista
	 *
	 * @param unknown_type $name
	 * @param unknown_type $lang
	 * @param unknown_type $sgbd
	 * @return unknown
	 */
	function obtain_Id_view2Name_view($name, $lang, & $sgbd){
		if ($sgbd->connect()) {
			/*
			$query = 'SELECT `id` as id 
						FROM `doc_view` 
							WHERE `name`="' . $name . '" AND (`lang`="' . $lang . '" OR `lang`="ALL")';
			*/
			$query = 'SELECT `id` as id 
						FROM `doc_view` 
							WHERE `name`="' . $name . '"';
		
			$rs = $sgbd->query($query);
			
			if ($rs === false) {
				// error al fer la query !!!
				print 'error SGBD query -> obtain_Id_view2Name_view';
			}
			
			$row = $rs->fetchRow();
	
			return $row['id'];
		}
		else{
			print 'error SGBD connect -> obtain_Id_view2Name_view';
		}
		
		// Error no ha trobat el ID de la vista !!!!!
		return -2;
	}
	
	/**
	 * Aquesta funcio retorna el lang mes adient per la peticio o sessio
	 *
	 */
	function getLangOptim(& $langs_site, $langs_client){
		
		
		//print $_SESSION['lang'];
		$pred = isset($_SESSION['lang']) ? $_SESSION['lang'] : key($langs_site);
		//print $pred;
		//print $langs_site;
		//print $pred;	
		
		return key_exists($langs_client,$langs_site) == 1 ? $langs_client : $pred ;
		
	}
	
	/**
	 * Funcio retorna el string del fitxer d'idiomes
	 *
	 * @param unknown_type $message
	 */
	function getMessage($message, $is_admin=false){
		global $_MANAGER;		
		//print_r($_SESSION);
		if ($is_admin)
			require $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'lang' . $_MANAGER['separator'] . $_SESSION['lang'] . '.php';
		else 
			require $_MANAGER['langs_message'] . $_SESSION['lang'] . '.php';

		return 	$lang[$message];
	}
	
	/**
	 * Funcio per recuperar templates XSLT de la BBDD
	 *
	 */
	function getTemplate($table, $id){
		global $_MANAGER;
		
		//print_r($_MANAGER['dsn']);
/*		
		print 'table -->' . $table;
		print 'id -->' . $id;
*/
		$sgbd = new mySGBD($_MANAGER['dsn']);
		//$sgbd = new mySGBD($_SESSION['site_admin_dsn']);
		$query = 'SELECT `source` FROM ' . $table . ' WHERE `id`=' . $id;
		
		$rs = $sgbd->query($query);
		
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> getTemplate';
		}
		
		$row = $rs->fetchRow();
						
		$sgbd->close();
		
		unset($sgbd,$rs);
		
		return $row['source'];
	}
	
	
	
	
	function callFunction($params, $require = false, $module = null){
		/*		
		
		print_r($_SESSION);
		print_r($_MANAGER);
//		*/
		if ($require) {
			global $_MANAGER;
			require($_MANAGER['modules'] . $module . $_MANAGER['separator'] . $module . '.php');
		}
				
		$params = explode(',',$params);
		
		//print_r($params);
		
		//$params_function = explode(':',$params[1]);		
				
		return $params[0]($params[1]);
	}
?>