<?php
/**************
 * Modul AJAX *
 **************/

	function respondRequest($params){
		global $_MANAGER;
		
		$params = explode(':',$params);
		
		require_once($_MANAGER['modules'] . $params[0] . $_MANAGER['separator'] . $params[0] . '.php');
		
		$function = $params[1];
		
		return $function($params[2]);
			
	}
?>