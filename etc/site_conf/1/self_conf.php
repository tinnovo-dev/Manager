<?php

	// incloem el path al core del Manager	
	require_once($_MANAGER['path_core']);
		
	$_MANAGER['site_id'] = 1;
	$_MANAGER['site_name'] = 'manager_admin';
	
	//viste per defecte, quant no ems demanem cap document mostrarem aquesta vista
	$_MANAGER['default_view_id'] = 0;
	$_MANAGER['default_view_name'] = 'index';

	$_MANAGER['dsn'] = array(
				'phptype' 	=>	'mysql',
				'hostspec'	=>	'127.0.0.1:3306',
				'database'	=>	'manager_admin',
				'username'	=>	'root',
				'password'	=>	''
				);

?>