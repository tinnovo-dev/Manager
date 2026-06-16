<?php
	
	/**************************************
	 * Fitxer de configuracio del Manager *
	 **************************************/
	
	$_MANAGER['separator'] = '/';	//UNIX
	//$_MANAGER['separator'] = '\\';	//MS Windows
	
	// path root
	$_MANAGER['path_root'] = '/home/tinnovo/Escritorio/_manager/' ;
	
	$_MANAGER['etc'] = $_MANAGER['path_root'] . 'etc' . $_MANAGER['separator'];			// dir. fitxers de configuracions(etc)
	$_MANAGER['lib'] = $_MANAGER['path_root'] . 'lib' . $_MANAGER['separator'];			// dir. fitxers de llibreries(lib)	
	$_MANAGER['tmp'] = $_MANAGER['path_root'] . 'tmp' . $_MANAGER['separator'];			// dir. fitxers temporals(tmp)
	$_MANAGER['sys'] = $_MANAGER['path_root'] . 'sys' . $_MANAGER['separator'];			// dir. fitxers de sistema(sys)
	
	// path al kernel
	$_MANAGER['kernel'] = $_MANAGER['sys'] . 'kernel' . $_MANAGER['separator'];
	
	// path als Moduls del Manager
	$_MANAGER['modules'] = $_MANAGER['sys'] . 'module' . $_MANAGER['separator'];
	
	$_MANAGER['site_conf'] = $_MANAGER['etc'] . 'site_conf' . $_MANAGER['separator'];	// dir. configuracions dels sites
	
	$_MANAGER['www'] = '/var/www/html/';												// dir. al htdocs del srv

	// indiquem si fem servir PEAR propi
	$_MANAGER['include_pear'] = false;
	// path al PEAR propi (ja no s'utilitza, substituït per PDO)
	$_MANAGER['dir_pear'] = $_MANAGER['lib'] . 'PEAR' . $_MANAGER['separator'];
	
	// path al fitxer del core	
	$_MANAGER['path_core'] = $_MANAGER['kernel'] . 'core.php';
	
?>