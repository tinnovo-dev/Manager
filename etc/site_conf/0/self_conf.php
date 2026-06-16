<?php

	// incloem el path al core del Manager	
	require_once($_MANAGER['path_core']);
	require_once('langs_site.php');
	
	$_MANAGER['site_id'] = 0;
	$_MANAGER['site_name'] = 'manager';
	
	//viste per defecte, quant no ems demanem cap document mostrarem aquesta vista
	$_MANAGER['default_view_id'] = 0;
	$_MANAGER['default_view_name'] = 'index';
	
	$_MANAGER['langs_message'] = $_MANAGER['site_conf'] . $_MANAGER['site_id'] . $_MANAGER['separator'] . 'lang' . $_MANAGER['separator'];

	$_MANAGER['dsn'] = array(
							'phptype' 	=>	'mysql',
							'hostspec'	=>	'127.0.0.1:3306',
							'database'	=>	'manager',
							'username'	=>	'root',
							'password'	=>	''
							);

?>