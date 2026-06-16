<?php
	/**
 	 * Aquesta funcio podren registrar variables de sessio i demes, per despres operar amb elles
	 *
	 * @param unknown_type $mode
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
//	function register_add_var($mode, $name, $value){
	function register_add_var($params){
		$params = explode(':', $params);
		
		/**
		 * $params [0] -> mode (0 - $_MANAGER / 1 - $_SESSION
		 * $params [1] -> name var
		 * $params [2] -> value var
		 */
		
		//print_r($params);
		//exit();
		if (isset($params[2]) and strlen($params[2]) === 0) {			
			//print'wwwwwwwww';
			switch ((int)$params[3]) { // value source mode
				case 0: // $_REQUEST[]
					$value = $_REQUEST[$params[1]];
					//echo 'eiiiiii';
				break;
			}
		}
		else
			$value = $params[2];
		
		//echo $value;exit();
		
		switch ((int)$params[0]){ // mode
			case 0: // registrem la variable en $_MANAGER['aux'][$name]
				global $_MANAGER;
			
				$_MANAGER['aux'][$params[1]] = $value;
			break;
			case 1: // registrem la variable a $_SESSION[name]
				$_SESSION[$params[1]] = $value; 
			break;
		}
		
		//print_r($_MANAGER);
		//print_r($_SESSION);
	}
	
	/**
	 * Aquesta funcio podren modificar variables de sessio i demes, per despres operar amb elles
	 *
	 * @param unknown_type $mode
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	function register_mod_var($mode, $name, $value){
		switch ((int)$mode){
			case 0: // modifiquen la variable en $_MANAGER['aux'][$name]
				global $_MANAGER;
			break;
			case 1: // modifiquen la variable a $_SESSION[name]
				
			break;			
		}
	}

	/**
	 * Aquesta funcio podren eliminar variables de sessio i demes
	 *
	 * @param unknown_type $mode
	 * @param unknown_type $name
	 * @param unknown_type $value
	 */
	function register_del_var($mode, $name, $value){
		switch ((int)$mode){
			case 0: // eliminem la variable en $_MANAGER['aux'][$name]
				global $_MANAGER;
				unset($_MANAGER['aux'][$name]);
			break;
			case 1: // eliminem la variable a $_SESSION[name]
				unset($_SESSION[$name]);
			break;			
		}
	}
	
	function getCtrlSelect($params){		
		global $_MANAGER;
		
		$params = explode(':',$params);		
		
		$sgbd = new mySGBD($_MANAGER['admin'] ? $_SESSION['site_admin_dsn'] : $_MANAGER['dsn']);
		
		if ($sgbd->connect()) {			
		
			//$sxe_select = simplexml_load_string('<select name="' . $params[0] . '"></select>');
			//$select = '<select name="' . $params[0] . '" id="' . $params[0] . '">';
			/*
			$rs = $sgbd->query('select ' . $params[1] . ' from ' . $params[0]);			
			
			if ($rs!==false) {
				$fields = explode(',',$params[1]);
				while ($row = $rs->fetchRow()) {
					$select .= '<option value="' . $row[$fields[0]] . '">' . $row[$fields[1]] . '</option>';
				}
			}
			else{
				echo 'select ' . $params[1] . ' from ' . $params[0];
				echo 'error $sgbd->query --------> getCtrlSelect';
			}
			
			$select .= '</select>';
			
			$sgbd->close();
			
			//$xml_select = $sxe_select->asXML();
		
			unset($sgbd,$sxe_select);
		
			//echo (string)$xml_select;
			
			//return (string)$xml_select;
			*/
			require_once($_MANAGER['modules'] . $params[0] . $_MANAGER['separator'] . $params[0] . '.php');
			
			$function = 'getCtrlSelect_' . $params[1];
			
			$ctrl = simplexml_load_string('<ctrl name="' . $params[1] . '" id="' . $params[1] . '"></ctrl>');
			
			//$function($sgbd,$ctrl,-1,1);
			//print_r($_SESSION);
			//list($mode,$name) = explode(',',$params[3]);
			
			//echo $mode . ' -- ' . $name;exit();
			
			$function($sgbd,$ctrl,-1,isset($params[3]) ? getValueVar($params[3],',') : null);
			
			//echo getTransformXML($ctrl->asXML(),getTemplate('template',$params[2]));//exit();
			return getTransformXML($ctrl->asXML(),getTemplate('template',$params[2]));//exit();
			
			//echo $ctrl->asXML();exit();
			
			//return $select;
		}

		// ERROR !!!
		return;	
	}
?>