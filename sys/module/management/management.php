<?php
	/**************************
	 * Modul d' Administracio *
	 **************************/
	
	function createForm(){
		global $_MANAGER;
		
		//$xml = $_MANAGER['modules'] . $_REQUEST['module'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'admin.xml';
		//$xsl = $_MANAGER['modules'] . $_REQUEST['module'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'template.xsl';
		
		// font del xml -> fitxer/string
		$source_xml = $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'admin.xml';
		// XSLT
		$xsl = $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'template.xsl';
		
		// Recuperem el formulari en xml
		$xml_form = getElementsByTag($source_xml, 'table[@name="' . $_REQUEST['table'] . '"]', true);
					
		// pregunten si te camps dinamics
		$dinamic_fields = getElementsByTag($xml_form, 'dinamic_fields', true, false);
						
		getFiedlsDinamic($dinamic_fields, $xml_form);	
		
		return getTransformXML($xml_form, $xsl, true, false);
	}
	
	/**
	 * Crea el formulario de administracion del Modulo y la table correspondiente
	 *
	 * @param string $params
	 * @return xtml del formulario
	 */
	function createForm2($params){
		global $_MANAGER;
		
		//print_r($params);exit();
				
		$params = explode(':',$params);
		// $params[0] -> module
		// $params[1] -> table
		// $params[2] -> list params (mode ( 0 -> ADD / 1 -> UPDATE ) - link action ) ... etc
		
		require_once($_MANAGER['modules'] . strtolower($params[0]) . $_MANAGER['separator'] . 'management' . $_MANAGER['separator'] . 'manage_' . strtolower($params[0]) . '.php');
		
		$function_createForm = 'createForm_' . $params[1];
		
		$xml_source = $_MANAGER['modules'] . strtolower($params[0]) . $_MANAGER['separator'] . 'management' . $_MANAGER['separator'] . 'admin.xml';
		$xslt = $_MANAGER['modules'] . strtolower($params[0]) . $_MANAGER['separator'] . 'management' . $_MANAGER['separator'] . 'template.xsl';
				
		return $function_createForm($xml_source, $xslt, $params[2]);
	}
	
	/**
	 * Funcion para procesar un formulario de administracion
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function processForm($params){
		global $_MANAGER;
		
		$params = explode(':',$params);
		// $params[0] -> module
		// $params[1] -> table
		// $params[2] -> list params (mode ( 0 -> ADD / 1 -> UPDATE ) - link action ) ... etc
		
		require_once($_MANAGER['modules'] . strtolower($params[0]) . $_MANAGER['separator'] . 'management' . $_MANAGER['separator'] . 'manage_' . strtolower($params[0]) . '.php');
		
		$function_processForm = 'processForm_' . $params[1];
		
		// lanzamos la funcion processForm del modulo y la tabla correspondiente
		$function_processForm($params[2]);
		
		//print_r($params);exit();
		
	}
	
	function add2tableModule(){
		global $_MANAGER;
		
		list($module,$table) = explode(':', $_POST['support']);
		
		//print $module . ' ' . $table;
		
		//print_r($_SESSION);
		
		//$xml = $_MANAGER['modules'] . $module . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'admin.xml';
		$xml = $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'admin.xml';
		
		$xml_form = getElementsByTag($xml,'table[@name="' . $table . '"]',true);
		
		
		
		$xml_tbl = simplexml_load_string($xml_form);
		
		//print_r($xml_form);
		//print_r($xml_tbl);
		$fields = '';
		$values = '';
		
		$date = date('d-m-Y G:i:s');
				
		foreach ($xml_tbl->field as $field) {
			// SEPARATOR
			if ((string)$field['type'] != '-1'){
				$fields.= '`' . (string)$field['name'] . '`,';
				$value = $_REQUEST[(string)$field['name']];

				// INTEGER
				if ((string)$field['type'] == '0' or (string)$field['type'] == '6' or (string)$field['type'] == '7') {
					$values .= $value . ',';
				}
				// DATE
				elseif ((string)$field['type'] == '3'){
					$values .= '\'' . $date . '\',';
				}
				// USER
				elseif ((string)$field['type'] == '8'){
					$values .= '\'' . $_SESSION['user_admin'] . '\',';
				}
				else
					$values .= '\'' . $value . '\',';
			}
			//echo "\n";
		}
		
		$fields = substr($fields,0,strlen($fields)-1);
		$values = substr($values,0,strlen($values)-1);
		
		$query = 'INSERT INTO `' . $table . '`(' . $fields . ') VALUES(' . $values . ')';
		
		//echo $query;exit();
		
		$sgbd = new mySGBD($_SESSION['site_admin_dsn']);
		
		if ($sgbd->connect()) {
			
			$rs = $sgbd->query($query);
			
			if ($rs === false) {
				echo $query;
				// error al fer la query !!!
				print 'error SGBD query -> add2tableModule';
			}		
			
			$sgbd->close();
			unset($sgbd);
			
		}
		else{
			print 'error SGBD connect -> add2tableModule';
		}
		
		//echo $query;
	}
	
	function update2tableModule(){
		print '<h1>update2tableModule</h1>';
	}
	
	function delete2tableModule(){
		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param llistat de parametres necesaris
     * $params[0] -> module
	 * $params[1] -> table
	 * $params[2] -> fields (id,name,description ...)
	 * $params[3] -> XSLT
	 * $params[4] -> aux params, parametres extres per depen quines funcions [OPCIONAL]
	 * @return unknown
	 */
	function list2tableModule2(& $params = null){
		//print_r($params); exit();
		$params = explode(':', $params);		
		
		// ens connectem a la base de dades del site que estem administran
		$sgbd = new mySGBD($_SESSION['site_admin_dsn']);
		
		if ($sgbd->connect()) {			
			global $_MANAGER;
			
			// hacemos el require del modulo que queremos listar
			require_once($_MANAGER['modules'] . strtolower($params[0]) . $_MANAGER['separator'] . 'management' . $_MANAGER['separator'] . 'manage_' . strtolower($params[0]) . '.php');
			
			// Objecte Simple XML per fer el XML del llistat
			$sxe_list = simplexml_load_string('<list table="' . $params[1] . '"></list>');
			
			// funcio per fer el llistat
			$function = 'getList_' . strtolower($params[1]);
				
			$function(array(& $sgbd, & $params[2], & $sxe_list, $params[4]));		
			
			// recuperem la template XSLT de la taula template sempre del manage_sites !!!
			$xsl = getTemplate('template',$params[3]);
			
			$arr_fields = explode(',',$params[2]);

			$sxe_child = $sxe_list->addChild('fields');
			
			foreach ($arr_fields as $field)
				$sxe_child->addChild('head',$field);
				
			$xml = $sxe_list->asXML();
			
			//echo $xml;exit();

			$sgbd->close();
			unset($sgbd);			
			
			return getTransformXML($xml, $xsl);
			
		}
		else{
			print 'error SGBD connect -> list2tableModule';
		}
	}
	
	/**
	 * Aquesta funcio llista una taula d'un modul del Site que esten administran.
	 * PAGINAT !!!
	 *
	 */
	function list2tableModule(& $params = null){
		global $_MANAGER;
		
		// OJO !!! al fer la cridar desde XML amb el caracter SEPARADOR es ":" !!!!
		$params = explode(':', $params);

		// recuperem la template XSLT de la taula template sempre del manage_sites !!!
		$xsl = getTemplate('template',$params[2]);
		
		$table = $_REQUEST['table'] ? $_REQUEST['table'] 
									: $params[0];
									
		$where = $params[3] ? ' WHERE `' . $params[4] . '`=' . $_SESSION[$params[5]]
							: '';
		
		//$query = 'SELECT ' . $fields . ' FROM`' . $table . '';
		$query = 'SELECT ' . $params[1] . ' FROM `' . $table . '`' . $where;
		
		// ens connectem a la base de dades del site que estem administran
		$sgbd = new mySGBD($_SESSION['site_admin_dsn']);
		
		if ($sgbd->connect()) {
			
			$rs = $sgbd->query($query);
			
			if ($rs === false) {
				// error al fer la query !!!
				print $query;
				print 'error SGBD query -> list2tableModule';
			}
			else{
				$listxml = '<list table="' . $table . '"></list>';
				
				$simplexml = simplexml_load_string($listxml);
					
				
							
//				$array2xml =  new multidi_array2xml();								
//				$arr = array();
				while($rows = $rs->fetchRow()){
					//print_r($rows);
					$row = $simplexml->addChild('row');
					foreach ($rows as $key => $value) {
						$children = $row->addChild('field', $value);
						$children->addAttribute('name', $key);

						if ($key === 'id') $value_id = $value;
					}
					$row->addAttribute('id',$value_id);
//					$arr[] = $rows;					
				}
				
//				$xml_arr = $array2xml->array2xml($arr);
//				echo $xml_arr;
				
				$fields = explode(',',$params[1]);

				$child = $simplexml->addChild('fields');
				foreach ($fields as $field)
					$child->addChild('head',$field);				
			
				// extraiem el nou XML
				//$xmls = $simple->asXML();
										
			}	

			$listxml = $simplexml->asXML();
				
			//echo $listxml;
			
			//echo $xmls;			
			
			$sgbd->close();
			unset($sgbd,$array2xml,$simple);
			
			//return getTransformXML($xmls,$xsl);
			return getTransformXML($listxml,$xsl);
			
		}
		else{
			print 'error SGBD connect -> list2tableModule';
		}		
//*/
	}
	
	/**
	 * BORRARRRRRRR !!!
	 *
	function listActions(& $params){
		global $_MANAGER;
		
		$params = explode(',',$params);
		
		//print_r($params);
		
		$xml = $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'admin.xml';
		$xsl = is_null($params[1]) ? $_MANAGER['site_conf'] . $_SESSION['id_site_admin'] . $_MANAGER['separator'] . 'manage' . $_MANAGER['separator'] . 'template.xsl'
									 : getTemplate('template',$params[2]);
		
		//print  $xml .' <-> ' . $xsl . ' <-> '. $params[1] . ' <-> '. $params[0];
		//print_r(getElementsByTag($xml,'table[@name="news"]'));
		
		//return getTransformXML($xml,$xsl,false, false);
		//echo getElementsByTag($xml,'actions[@module="' . $params[0] .'"]/action[@name="' . $params[1] . '"]',true);
		//echo '---------> ' . getTransformXML(getElementsByTag($xml,'actions[@module="' . $params[0] .'"]/action[@name="' . $params[1] . '"]',true),$xsl,true, true);
		
		return is_null($params[1])  ? getTransformXML(getElementsByTag($xml,'actions[@module="' . $params[0] .'"]',true),$xsl,true, false)
									: getTransformXML(getElementsByTag($xml,'actions[@module="' . $params[0] .'"]/action[@name="' . $params[1] . '"]',true),$xsl,true, true);
								
		//return $module;
	}
	*/
	
	/**
	 * Funcio processar l'accio d'administracio a realitzar ( ADD, UPDATE, LIST, DEL)
	 *
	 * @return unknown
	 */
	function processRequest(){
		
		//print_r($_REQUEST);
		
		// accio a realitzar
		switch ((int)$_REQUEST['action']){
			case 0:	// Admin
				return '<h1>eiiiiiii</h1>';
			break;
			case 1:	// CREATE FORM
				//return createForm();
				return createForm2();
			break;
			case 2:	// UPDATE
				return '<h1>eiiiiiii</h1>';
			break;
			case 3:	// LIST
				return list2tableModule();
			break;
			case 4:	// DEL
				return '<h1>eiiiiiii</h1>';
			break;
			case 5: // PROCESS FORM
				switch ((int)$_REQUEST['subaction']){
					case 1:	// ADD
						add2tableModule();	
					break;
					case 2:	// UPDATE
						update2tableModule();	
					break;
				}
				//return processForm();
			break;
		}		
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $table
	 * @param unknown_type $fields
	 * @return unknown
	 */
	function getControl2ForeignData(& $table, & $fields, $is_category, $where = null){
		
		// XSL
$xsl = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" encoding="UTF-8" />
	
	<xsl:template match="/">
        <xsl:copy>        	
            <xsl:apply-templates select="array" />
        </xsl:copy>        
    </xsl:template>
    
	<xsl:template match="array">
            <select name="{./table/@name}">
            	<xsl:element name="option">
            		<xsl:attribute name="value">-1</xsl:attribute>
            		<xsl:if test="not(boolean(./table/@id))">
            			<xsl:attribute name="selected">selected</xsl:attribute>            			
            		</xsl:if>
            		<xsl:value-of select="php:function('getMessage','selected')" />
            	</xsl:element>            	
            	<xsl:call-template name="options">
            		<xsl:with-param name="espace" select="string('&#x2002;')" />
            		<xsl:with-param name="id" select="./table/@id" />
        		</xsl:call-template>
            </select>
    </xsl:template>
    <xsl:template name="options">
    	<xsl:param name="espace"/>
    	<xsl:param name="id"/>
    	<xsl:for-each select="./*">    	
        	<xsl:if test="name(.)!='table'">
        	<xsl:element name="option">
        		<xsl:if test="\$id=./id">
        			<xsl:attribute name="selected">selected</xsl:attribute>
        		</xsl:if>
        		<xsl:attribute name="value"><xsl:value-of select="./id" /></xsl:attribute>        		
        		<xsl:value-of select="string(\$espace)" /><xsl:value-of select="./name" />
        	</xsl:element>        		
        		<xsl:apply-templates select="childs">
        			<xsl:with-param name="espace" select="concat(string(\$espace),string('&#x2002;'))" />
        			<xsl:with-param name="id" select="\$id" />
        		</xsl:apply-templates>
        	</xsl:if>        	
        </xsl:for-each>
    </xsl:template>
    <xsl:template match="childs">
    	<xsl:param name="espace"/>
    	<xsl:param name="id"/>
    	<xsl:for-each select="./*">
        	<option value="{./id}"><xsl:value-of select="string(\$espace)" /><xsl:value-of select="./name" /></option>
        	<xsl:apply-templates select="childs">
        		<xsl:with-param name="espace" select="concat(string(\$espace),string('&#x2002;'))" />
        	</xsl:apply-templates>
        </xsl:for-each>
    </xsl:template>
</xsl:stylesheet>
EOB;

		//echo '------>' . strlen($where) . ' ------->' . $where . '<--------';exit();

		$where = strlen($where) === 0   ? null
								 		: explode(':', $where);

		$sgbd = new mySGBD($_SESSION['site_admin_dsn']);
		
		//aux($sgbd,$table,$fields);
		//$fields = explode(':',$fields);
		
		//print_r($fields);
		
			
		
		if ($sgbd->connect()) {
			
			$arr = array();
			
			if ($is_category) {
				$arr = getChild4Category($sgbd, $table, $fields, -1, $where);
				
				//print_r($arr);
			}
			else
				$arr = getList4table($sgbd, $table, $fields);
			
				/*	
			$size = count($arr);
			
			for ($i=0;$i<$size;$i++){
				$arr[$i]['name'] = aux($sgbd, $arr[$i]['catalogue_item_model_category']) . ' ' . $arr[$i]['name'] ;
				//echo '....... ' . $arr[$i]['name'] . ' .......';
			}
//			*/
			// pasem el array a xml ...
			$array2xml =  new multidi_array2xml();
			
								
			$xml = $array2xml->array2xml($arr);
			
			//echo '<pre>' . $xml . '</pre>';exit();
			
			// afegin el node table ...
			$simple = simplexml_load_string($xml);
			
			$child = $simple->addChild('table');
			$child->addAttribute('name', $table);
			
			if (isset($_SESSION[$table . '_id'])) {
				$child->addAttribute('id', $_SESSION[$table . '_id']);	
			}
			
			//$simple->table = $table;
			
			// extraiem el nou XML
			$xml = $simple->asXML();	
			
			$sgbd->close();
			
			unset($sgbd,$array2xml,$simple);			
		}
		else{
			print 'error SGBD connect -> getControl2ForeignData';
		}
		
		/*
		echo $xml;
		echo $xsl;
		
//		exit(); */
		
		// Error no ha trobat el ID de la vista !!!!!
		//return -2;
		// transformen i retornem el Select control ...
		
		//echo $xml;
		
		return getTransformXML($xml,$xsl);
	}
	
	/**
	 * ??????????????????????????????????????????
	 *
	 * @param unknown_type $sgbd
	 */
	
	function aux(& $sgbd, $id){		
		
		$query = 'SELECT name,catalogue_item_model_category from catalogue_item_model_category where id=' . $id;
				
		$rs = $sgbd->query($query);
		
		if ($rs === false) {
			// error al fer la query !!!
			echo $query;
			print 'error SGBD query -> aux';
		}
		
		while($rows = $rs->fetchRow()){						
			$name = aux($sgbd,$rows['catalogue_item_model_category']) . ' ' . $rows['name'];
			
		}
		
		return $name;
	}
	
	/**
	 * Recuperar Categories en un Array ...
	 *
	 * @param unknown_type $sgbd
	 * @param unknown_type $table
	 * @param unknown_type $father
	 */
	function getChild4Category(& $sgbd, & $table, & $fields, $father=-1, $where = null){
		
		//print 'getChild4Category';print_r($where);
		$where = is_null($where) ? ''
								 : ' AND `' . $where[0] . '`=' . $_SESSION[$where[1]];
		
		//$query = 'SELECT `id`,`category`,`name` FROM `' . $table . '` WHERE `category`=' . $father;
		$query = 'SELECT ' . $fields . ' FROM `' . $table . '` WHERE `'. $table . '`=' . $father . $where;
		
		//echo $query; //exit();
		
		$rs = $sgbd->query($query);
		
		if ($rs === false) {
			// error al fer la query !!!
			echo ' -->' . $query . ' ';
			print 'error SGBD query -> getChild4Category';
		}
		
		$arr = array();
		
		while($rows = $rs->fetchRow()){
			
			//var_dump($rows);
			$arr[$rows['id']] = array('id'=>		$rows['id'],
									  'category'=>	$rows['category'],
									  'name'	=>	$rows['name'],
									  'childs'	=>	null);
			//$arr =  + ;
			$arr[$rows['id']]['childs'] = getChild4Category($sgbd, $table, $fields, (int)$rows['id']);
		}	
		
		
		//var_dump($rows);
		//return is_null($rows) ? null : $arr;
		return count($arr)> 0 ? $arr : null;
	}
		
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $table
	 * @param unknown_type $fields
	 */
	function getList4table(& $sgbd, & $table, & $fields){
		//print '<-- getList4table -->';
		
		$query = 'SELECT ' . $fields . ' FROM ' . $table;
		
		$rs = $sgbd->query($query);
		
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> getList4table';
			//echo $query;exit();
		}
		
		$arr = array();
		
		while($rows = $rs->fetchRow())			
			$arr[] = $rows;
		
		return $arr;
	}
	
	/**
	 * Enter description here...
	 *
	 * @param unknown_type $table
	 * @return unknown
	 */
	function getNewId4Table($table){
		
		global $_MANAGER;
		
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];
		
		$sgbd = new mySGBD($dsn);
		
		if ($sgbd->connect()) {
			
			$query = 'SELECT max(`id`) + 1 as id 
						FROM `' . $table . '`';
					
			$rs = $sgbd->query($query);
			
			if ($rs === false) {
				echo $query;
				// error al fer la query !!!
				print 'error SGBD query -> getNewId4Table';
			}
			
			$row = $rs->fetchRow();
			
			//$sgbd->close();
			
			//unset($sgbd);
	
			return is_null($row['id']) ? 0 : $row['id'];
		}
		else{
			print 'error SGBD connect -> getNewId4Table';
		}
		
		// Error no ha trobat el ID de la vista !!!!!
		return -2;
		
	}
	
	/**
	 * Enter description here...
	 *
	 */
	function getFiedlsDinamic($xml_dinamic_fields, & $xml_form){
		if (!$xml_dinamic_fields) {
			return;
		}
		
		global $_MANAGER;
						
		$simplexml_form = simplexml_load_string($xml_form);
		
		$simplexml_form->addChild('field');
				
		$simplexml_dinamicfields = simplexml_load_string($xml_dinamic_fields);
		
		$attrs = $simplexml_dinamicfields->attributes();
		
		$module = $attrs['module'];
		$table =  $attrs['table'];
		
		//$params =  $attrs['params'];
		
		//print '------>' .$module . '<------';
		
		require_once($_MANAGER['modules'] . $module . $_MANAGER['separator'] . $module . '.php');
		
		//catalogue_getDinamicFields();
		
		$function = strtoupper($module) . '_getDinamicFields';
		
		$function();
		
		//print_r($_SESSION);
		
		$xml_form =  $simplexml_form->asXML();
		
		unset($simplexml_dinamicfields, $simplexml_form);
	}	
?>