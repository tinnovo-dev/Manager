<?php
	/************************************************************
	 * Cor del programa, per on s'inicia la crida de la peticio *
	 ************************************************************/
	
	// incloem el fitxer amb funcions de suport per el core
	require_once('support' . $_MANAGER['separator'] . 'support_core.php');
	// incloem el fitxer commons per el XML
	//require_once($_MANAGER['lib'] . 'XML' . $_MANAGER['separator'] . 'commons.php');	
	
	/**
	 * Funcio principal, per aqui entren les comandes de documents
	 *
	 */
	function to_request_doc(){
		global $_MANAGER;		
		
		// inicien sessions !!!
		start_session();		
		
		//session_destroy();
		//print 'lang ----------> ' . array_shift(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE']));
		
		if(!isset($_SESSION['lang'])){
			//print 'eiiiii';
			
			$_MANAGER['lang'] = getLangOptim($_MANAGER['langs_site'], array_shift(explode(',',$_SERVER['HTTP_ACCEPT_LANGUAGE'])));
			$_SESSION['lang'] = $_MANAGER['lang'];
		}
								
		// peticio de canvi d'idioma
		if(isset($_REQUEST['lang'])){
			// comproven si canvian d'idioma o es el mateix
			if(strcmp($_SESSION['lang'],trim($_REQUEST['lang'])) !== 0){
				//$_SESSION['lang'] = trim($_REQUEST['lang']);
				$_SESSION['lang']  = getLangOptim($_MANAGER['langs_site'], trim($_REQUEST['lang']));
			}			
		}
		// fi sessions
	
		// objecte x conectarnos a la bbdd i fer les querys i demes ...
		$sgbd = new mySGBD($_MANAGER['dsn']);
		
		// comprovem si arriven parametres a la peticio
		//if(count($_REQUEST) > 1){	
		//echo '--->' . count($_GET);
		
		if(count($_GET) > 1 || (count($_GET) == 1 && !key_exists('lang',$_GET))){	
						
			// aconseguim el id i el nom de la vista de la peticio					
			$_MANAGER['view_name'] = array_key_first($_GET);
			$_MANAGER['view_id']   = $_GET[$_MANAGER['view_name']];
			
			// demanam una vista a partir del id de la vista i no per el nom --> ?410&param=xx ...
			if (is_numeric($_MANAGER['view_name'])) {
				//echo 'is numeric !!!';
				$_MANAGER['view_id'] = $_MANAGER['view_name'];
			}			
			else{	// demanan una vista per el nom
				$_MANAGER['view_id'] = strlen($_MANAGER['view_id']) == 0 ? obtain_Id_view2Name_view($_MANAGER['view_name'],$_SESSION['lang'],$sgbd) 
																		 : $_MANAGER['view_id'];	
			}			
																	 
			//print $_MANAGER['view_name'] . ' -- ' . $_MANAGER['view_id'];
			//print is_numeric($_MANAGER['view_name']) ? 'numerico' : 'string';																	 
			
		}
		// sense parametres viste per defecte (index)
		else{			
			
			$_MANAGER['view_id'] 	= $_MANAGER['default_view_id'];
			$_MANAGER['view_name'] 	= $_MANAGER['default_view_name'];
		}		
		
		//echo '------>' . $_MANAGER['view_name'];
		//echo '------>' . $_MANAGER['view_id'];
		// comprovar que $_MANAGER['view_id'] no sigui null (PAGE_NOT_FOUND)
		if (is_null($_MANAGER['view_id'])) {
			$_MANAGER['view_id'] = -404;
		}
		
//		print $_MANAGER['view_id'];exit();
								
		//recuperarem el doc i la template inicial de la vista
		list($xml, $xsl) = getXmlandXsl2View($_MANAGER['view_id'], $sgbd, 'doc', false, $_SESSION['lang']);
				
		// Desconectem i eliminem el objecte $sgbd
		$sgbd->disconnect();
		$sgbd = null;

		// output del doc demanat
		//echo $xml;exit();
		$xhtml = getTransformXML($xml, $xsl);
		//header('Content-Type: text/xml; charset=UTF-8');
		//echo $xhtml;//exit();		
		
		// pregunten si es una peticio AJAX
		if ($_MANAGER['aux']['is_AJAX_request']) {
		
			$_MANAGER['aux']['is_AJAX_request'] = false;
		
			// capçalera XML per les peticions AJAX !!
			header('Content-Type: text/xml; charset=UTF-8');
			echo $xhtml;//exit();
		
			
		}
		else{
			

		//echo $xhtml = str_replace("&","&amp;",$xhtml) ;
		//echo $xhtml;
		//$xhtml = str_replace("&","&amp;",$xhtml) ;
		
		//echo $xhtml;
		//exit(0);
	

/*	
$xsl4 = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
	<xsl:output method="xml" indent="yes" disable-output-escaping="no" omit-xml-declaration="yes" encoding="UTF-8" doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" />

	<xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>        
    </xsl:template>
	
	<xsl:template match="doc">
		<html>
        	<xsl:apply-templates select="node()"/>
    	</html>
    </xsl:template>
    
    <xsl:template match="part">
       	<xsl:apply-templates select="node()"/>
    </xsl:template>

	<xsl:template match="head">
		<head>
			<xsl:apply-templates select="title"/>
			<xsl:apply-templates select="source_head"/>
		</head>		
	</xsl:template>
	
	<xsl:template match="title">
		<title>
			<xsl:for-each select="//doc_title">
				<xsl:if test="position()=count(//doc_title)">
					<xsl:value-of select="." />				
				</xsl:if>
			</xsl:for-each>
		</title>		
	</xsl:template>

	<xsl:template match="source_head">
		<xsl:for-each select="//css">
			<link rel="{@rel}" type="{@type}" media="{@media}" href="{@href}" />
		</xsl:for-each>

		<xsl:for-each select="//js[@head]">
			<script type="{@type}" src="{@src}"><xsl:if test=".!=''">//<xsl:comment><xsl:value-of select="comment()" /></xsl:comment></xsl:if></script>
		</xsl:for-each>

	</xsl:template>	

	<xsl:template match="doc_title">
		
    </xsl:template>	
    
    <xsl:template match="css">
		
    </xsl:template>

    <xsl:template match="js">
		
    </xsl:template>

    <xsl:template match="source">
		<xsl:apply-templates select="node()"/>
    </xsl:template>

</xsl:stylesheet>
EOB;
		*/
		$xsl_end = $_MANAGER['kernel'] . $_MANAGER['separator'] . 'support' . $_MANAGER['separator'] . 'core.xsl';
		
		//echo(getTransformXML($xhtml,$xsl4));
		echo(getTransformXML($xhtml,$xsl_end,true,false));
		
		//echo(getTransformXML($xml, $xsl));
		}
	}

?>