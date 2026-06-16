<?php
	/******************************
	 * Gestio del Modul Catalogue *
	 ******************************/
	global $_MANAGER;
	require_once($_MANAGER['modules'] . 'catalogue' . $_MANAGER['separator'] . 'catalogue.php');
	
/*******************
 * Table Catalogue *	 
 *******************/
	//function getList_catalogue(& $sgbd, $fields, & $sxe_row){
	/**
	 * Llista la taula catalogue
	 * PAGINAT !!!
	 *
	 * @param unknown_type $params
	 */
	function getList_catalogue($params){
		$sql = 'SELECT ' . $params[1] . ' FROM catalogue';
		
		$rs = $params[0]->query($sql);
		
		if ($rs === false) {
			// error al fer la query !!!
			print $query;
			print 'error SGBD query -> list2tableModule2';
		}
		else{
			while ($rows = $rs->fetchRow()){
				//print_r($rows);
				$sxe_row = $params[2]->addChild('row');
				foreach ($rows as $key => $value) {
					$sxe_children =	$sxe_row->addChild('field',$value);
					$sxe_children->addAttribute('name',$key);
					
					if ($key === 'id') $value_id = $value;
						
				}
				$sxe_row->addAttribute('id',$value_id);
			}			
		}		
	}
	
/****************************
 * Table Catalogue_category *
 ****************************/
	//function getList_catalogue_category(& $sgbd, $is_by_childs = true, $child = -1){$params
	/**
	 * Enter description here...
	 *
	 * @param array $params
	 * @param[0] -> Objecte SGBD
	 * @param[1] -> fields
	 * @param[2] -> Objecte SimpleXML
	 */
	function getList_catalogue_category($params){
		//print_r($params);exit();
		$xtra_params = explode(',', $params[3]);	
		
		$sql = 'SELECT ' . $params[1] . ',catalogue_category FROM catalogue_category WHERE catalogue_category=' . $xtra_params[0] . ($xtra_params[1] ? ' AND catalogue=' . $_SESSION['catalogue_id'] : '');
		
		//echo $sql;
		
		$rs = $params[0]->query($sql);
		
		if ($rs === false) {
			// error al fer la query !!!
			print $sql;
			print 'error SGBD query -> getList_catalogue_category';
		}
		else{
			while ($rows = $rs->fetchRow()){
				//print_r($rows);
				$sxe_row = $params[2]->addChild('row');
				foreach ($rows as $key => $value) {
					if ($key === 'catalogue_category') continue;
					
					$sxe_children =	$sxe_row->addChild('field',$value);
					$sxe_children->addAttribute('name',$key);
					
					if ($key === 'id') $value_id = $value;
						
				}
				$sxe_row->addAttribute('id',$value_id);
				getList_catalogue_category(array($params[0],$params[1],$params[2],$rows['id']));
			}

			//return $sxe_row;
		}
	}
	
	function createform_catalogue_category($xml_source, $xslt){	
		
		$xml_form = getElementsByTag($xml_source, 'table[@name="catalogue_category"]', true);
		
		$sxe_form = simplexml_load_string($xml_form);
		
		//catalogue_getDinamicFields($sxe_form);
		
		$xml_form = $sxe_form->asXML();
		
		return getTransformXML($xml_form, $xslt, true, false);
	}
	
/******************************
 * Table catalogue_item_model *	 
 ******************************/
	function getList_catalogue_item_model($params){
		$xtra_params = explode(',', $params[3]);		
		
		//$sql = 'SELECT ' .  . ',catalogue_category FROM catalogue_item_model WHERE catalogue=' . $_SESSION['catalogue_id'] : '');
		
		$sql = 'SELECT ' . $params[1] . ' FROM catalogue_item_model WHERE catalogue_item_model_category IN (SELECT id FROM catalogue_item_model_category WHERE catalogue=' . $_SESSION['catalogue_id'] . ')';
		
		//echo $sql;
		
		$rs = $params[0]->query($sql);
		
		if ($rs === false) {
			// error al fer la query !!!
			print $sql;
			print 'error SGBD query -> getList_catalogue_item_model';
		}
		else{
			while ($rows = $rs->fetchRow()){
				//print_r($rows);
				$sxe_row = $params[2]->addChild('row');
				foreach ($rows as $key => $value) {					
					
					$sxe_children =	$sxe_row->addChild('field',$value);
					$sxe_children->addAttribute('name',$key);
					
					if ($key === 'id') $value_id = $value;
						
				}
				$sxe_row->addAttribute('id',$value_id);				
			}

			//return $sxe_row;
		}
	}
	
	function createform_catalogue_item_model($xml_source, $xslt){	
		
		$xml_form = getElementsByTag($xml_source, 'table[@name="catalogue_item_model"]', true);
		
		$sxe_form = simplexml_load_string($xml_form);		
		
		$xml_form = $sxe_form->asXML();
		
		return getTransformXML($xml_form, $xslt, true, false);
	}	

/***************************************
 * Table catalogue_item_model_category *
 ***************************************/
	/**
	 * Llistat de la taula CATALOGUE_ITEM_MODEL_CATEGORY
	 *
	 * @param unknown_type $params
	 * 
	 * @param[0] -> SGBD
	 * @param[1] -> fields
	 * @param[2] -> simple XML element ( sxe )
	 * @param[3] -> xtra params
	 * 			 -> xtra params [0] -> catalogue_item_model_category, per la recursivitat
	 * 			 -> xtra params [1] -> is_by_catalogue (flag 1/0) per filtra per el cataleg que administrem $_SESSION['catalogue_id']
	 * 
	 */
	function getList_catalogue_item_model_category($params){
		//print_r($params);exit();
		$xtra_params = explode(',', $params[3]);	
		
		$sql = 'SELECT ' . $params[1] . ',catalogue_item_model_category FROM catalogue_item_model_category WHERE catalogue_item_model_category=' . $xtra_params[0] . ($xtra_params[1] ? ' AND catalogue=' . $_SESSION['catalogue_id'] : '');
		
		//echo $sql;
		
		$rs = $params[0]->query($sql);
		
		if ($rs === false) {
			// error al fer la query !!!
			print $sql;
			print 'error SGBD query -> getList_catalogue_item_model_category';
		}
		else{
			while ($rows = $rs->fetchRow()){
				//print_r($rows);
				$sxe_row = $params[2]->addChild('row');
				foreach ($rows as $key => $value) {
					if ($key === 'catalogue_item_model_category') continue;
					
					$sxe_children =	$sxe_row->addChild('field',$value);
					$sxe_children->addAttribute('name',$key);
					
					if ($key === 'id') $value_id = $value;
						
				}
				$sxe_row->addAttribute('id',$value_id);
				getList_catalogue_item_model_category(array($params[0],$params[1],$params[2],$rows['id']));
			}

			//return $sxe_row;
		}
	}
	
	function createform_catalogue_item_model_category($xml_source, $xslt){	
		
		$xml_form = getElementsByTag($xml_source, 'table[@name="catalogue_item_model_category"]', true);		
		
		$sxe_form = simplexml_load_string($xml_form);		
		
		$xml_form = $sxe_form->asXML();
		
		return getTransformXML($xml_form, $xslt, true, false);
	}
		
/******************************************
 * FI Table catalogue_item_model_category *
 ******************************************/

/************************
 * Table catalogue_item *
 ************************/
	/**
	 * Funcion para listar los items de catalogue_item
	 *
	 * @param array $params
	 * @param[0] -> MYSGBD
	 * @param[1] -> lista de campos
	 * @param[2] -> SimpleXML object
	 */
	function getList_catalogue_item($params){
		//print_r($params);exit();
		$xtra_params = explode(',', $params[3]);	
		
		$sql = 'SELECT ' . $params[1] . ' FROM catalogue_item 
					WHERE catalogue_item_model 
						IN (SELECT a.id FROM catalogue_item_model AS a 
								INNER JOIN catalogue_item_model_category AS b 
									ON a.catalogue_item_model_category=b.id AND b.catalogue=' . $_SESSION['catalogue_id'] . ')';
		
		//$sql = 'SELECT ' . $params[1] . ' FROM catalogue_item';
		
		//echo $sql;
		
		$rs = $params[0]->query($sql);
		
		if ($rs === false) {
			// error al fer la query !!!
			print $sql;
			print 'error SGBD query -> getList_catalogue_item';
		}
		else{
			while ($rows = $rs->fetchRow()){
				//print_r($rows);
				$sxe_row = $params[2]->addChild('row');
				foreach ($rows as $key => $value) {					
					$sxe_children =	$sxe_row->addChild('field',$value);
					$sxe_children->addAttribute('name',$key);
					
					if ($key === 'id') $value_id = $value;
						
				}
				$sxe_row->addAttribute('id',$value_id);
				//getList_catalogue_item_model_category(array($params[0],$params[1],$params[2],$rows['id']));
			}

			//return $sxe_row;
		}
	}
	
	/**
	 * Funcion crea el formulario de administracion para la table catalogue_item
	 *
	 * @param unknown_type $xml_source
	 * @param unknown_type $xslt
	 * @param unknown_type $params
	 * @return unknown
	 */
	function createForm_catalogue_item($xml_source, $xslt, $params){
		global $_MANAGER;
			
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];

		$sgbd = new mySGBD($dsn);	
		
		if ($sgbd->connect()) {
		//echo '!!!!!!!!!1';				
			$params = explode(',',$params);
			// params[0] -> mode (  0 -> ADD / 1 -> UPDATE )
			// params[1] -> link action del formulario
						
			// Objeto SimpleXML con el formulario sin los campos dinamicos
			$xml_form = getElementsByTag($xml_source, 'table[@name="catalogue_item"]', true);
			$sxe_form = simplexml_load_string($xml_form);
			
			// catalogue
			$arr['type_0'] = $_SESSION['catalogue_id'];
			
			// MODE -> UPDATE , editando un registro
			if ($params[0] == 1) {
				$sql = 'SELECT item.*,item_model.catalogue_item_model_category FROM catalogue_item AS item,catalogue_item_model AS item_model WHERE item.id=' .  $_REQUEST['id'] . ' and item.catalogue_item_model=item_model.id';
			
				$rs = $sgbd->query($sql);
				
				if ($rs === false) {
					// ERROR 
					echo 'ERROR --> QUERY - UPDATE - STATICS createForm_catalogue_item';
					echo $sql;
				}
				
				// registro del item - campos estaticos
				$reg_static = $rs->fetchRow();
				
				//print_r($reg_item);exit();
				
				// catalogue_item_model
				$arr['type_2'] = $reg_static['catalogue_item_model'];
				//$_REQUEST['catalogue_item_model'] = $arr['type_2'];
				// catalogue_item_model_category * recursivo hacia arriba
				$arr['type_3'][] = $reg_static['catalogue_item_model_category'];
				
				// catalogue_category * recursivo hacia arriba
				$arr['type_1'] = array(getCatalogue_categorybyCatalogue_item_model_category($arr['type_3'][0]));
				
				// catalogue_item
				$arr['type_4'] = $_REQUEST['id'];
				
				// conseguimos los campos dinamicos
				catalogue_getDinamicFields($sxe_form,$arr);

				foreach ($reg_static as $key => $value) {
					$x = $sxe_form->xpath('field[@name="' . $key . '"]');
					$x[0][0] = $value;
				}
				
				// registro del item - campos dinamicos
				$sql = 'SELECT `field`,`value`,`lang` FROM `catalogue_field_value` WHERE `object`=' . $_REQUEST['id'];
				
				$rs = $sgbd->query($sql);
				
				if ($rs === false) {
					// ERROR 
					echo 'ERROR --> QUERY - UPDATE - DYN createForm_catalogue_item';
					echo $sql;
				}				
				
				// registro del item - campos dinamicos
				//$reg_dyn = $rs->fetchRow();
				//echo $sxe_form->asXML();exit();
				
				while($reg_dyn = $rs->fetchRow()){
					$x = $sxe_form->xpath('field[@id_field="' . $reg_dyn['field'] . '"]');
					$x[0][0] = $reg_dyn['value'];					
				}
			}
			// MODE ADD. agregar un registro
			elseif ($params[0] == 0) {
				// catalogue_item_model y catalogue_item_model_category * recursivo hacia arriba
				list($arr['type_2'],$arr['type_3'][]) = explode(':',$_REQUEST['catalogue_item_model']);
				
				// catalogue_category * recursivo hacia arriba
				$arr['type_1'] = array(getCatalogue_categorybyCatalogue_item_model_category($arr['type_3'][0]));
				
				// catalogue_item
				$arr['type_4'] = null;
				
				// conseguimos los campos dinamicos
				catalogue_getDinamicFields($sxe_form,$arr);
			}			

			// Agregamos el link del formulario
			$sxe_form->addChild('action_link',htmlentities($params[1]));

			// formulario ya con los campos dinamicos
			$xml_form = $sxe_form->asXML();
			//echo $xml_form;exit();
			// comprobamos el modo en el que creamos el fomulario ( ADD o UODATE )

			$sgbd->disconnect();
			unset($sgbd);
			
			// En el modo en que mostramos el formulario, a la hora de transformar con el XSLT lo consulta
			$_REQUEST['mode'] = $params[0];
			
			// devolvemos el formulario
			return getTransformXML($xml_form, $xslt, true, false);
		}
		else{
			// ERROR
		}
	}
			
	/**
	 * Funcion para procesar un formulario de administracion de la tabla catalogue_item
	 * ADD / UPDATE
	 *
	 * @param unknown_type $params
	 */
	function processForm_catalogue_item($params){
		global $_MANAGER;
		
		$params = explode(',',$params);
		//print_r($_REQUEST);exit();
		
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];

		$sgbd = new mySGBD($dsn);
		
		if ($sgbd->connect()) {
			// XML para los DinamicFields
			$sxe_fields = simplexml_load_string('<fields></fields>');
			
			// catalogue
			$arr['type_0'] = $_SESSION['catalogue_id'];
			// catalogue_item
			$arr['type_4'] = $_REQUEST['id'];
			
			// MODE -> UPDATE , editando un registro
			if ($_REQUEST['mode'] == 1) {
				//echo 'ioooooooooo';exit();
				// catalogue_item_model
				$arr['type_2'] = $_REQUEST['catalogue_item_model'];
				
				// catalogue_item_model_category * recursivo hacia arriba
				//$arr['type_3'][] = $reg_static['catalogue_item_model_category'];
				$arr['type_3'][] = getId_catalogue_item_model_category_Of_catalogue_item_model($arr['type_2'],$sgbd);
				
				// catalogue_category * recursivo hacia arriba
				$arr['type_1'] = array(getCatalogue_categorybyCatalogue_item_model_category($arr['type_3'][0]));
				
			//	echo 'ioooooooooo';exit();
				// conseguimos los campos dinamicos
				catalogue_getDinamicFields($sxe_fields,$arr);
				
				$sql_static_fields = 'UPDATE `catalogue_item` SET `name`=\'' . htmlspecialchars($_REQUEST['name'],ENT_QUOTES,'UTF-8') . '\' WHERE `id`=' . $_REQUEST['id'];
				
				// sentencia SQL
				$sql_dinamic_fields = 'UPDATE `catalogue_field_value` SET `value`= CASE ';

				// recorremos los campos dinamicos y creamos la sentencia sql para realiazar el INSERT en la tabla catalogue_fields_value
				foreach ($sxe_fields as $field) {
					//$sql_dinamic_fields .= 'UPDATE `catalogue_field_value` SET `value`=\'' . $_REQUEST[(string)(string)$field['name']] . '\' WHERE `field`=' . (string)$field['id_field'] . ' AND `object`=' . $_REQUEST['id'] . ',';
					$sql_dinamic_fields .= 'WHEN (`field`='. (string)$field['id_field'] . ' AND `object`=' . $_REQUEST['id'] . ') THEN \'' . htmlspecialchars($_REQUEST[(string)(string)$field['name']],ENT_QUOTES,'UTF-8') . '\' ';
				}
				
				$sql_dinamic_fields .= 'ELSE `value` END';
				/*
				UPDATE `catalogue_field_value` 
					SET `value`= case when (`field`=14 AND `object`=0) then '150 xxxx' 
										when (`field`=4 AND `object`=0) then '150 xxxx' 
										else `value` end
				*/
				
				//$sql_dinamic_fields[strlen($sql_dinamic_fields)-1] = ';';
				
				//echo $sql_dinamic_fields;exit();
			}
			// MODE -> ADD, agregando un registro
			elseif ($_REQUEST['mode'] == 0) {// catalogue_item_model y catalogue_item_model_category * recursivo hacia arriba
				list($arr['type_2'],$arr['type_3'][]) = explode(':',$_REQUEST['catalogue_item_model']);

				// catalogue_category * recursivo hacia arriba
				$arr['type_1'] = array(getCatalogue_categorybyCatalogue_item_model_category($arr['type_3'][0], $sgbd));

				$sql_static_fields = 'INSERT INTO `catalogue_item` VALUES(' . $_REQUEST['id'] . ',' . $arr['type_2'] . ',\'' . $_REQUEST['name'] . '\')';
			
				// recuperamos los campos dinamicos
				catalogue_getDinamicFields($sxe_fields,$arr);
				
				// id para catalogue_field_value
				$id = getNewId4Table('catalogue_field_value');

				// sentencia SQL
				$sql_dinamic_fields .= 'INSERT INTO `catalogue_field_value` VALUES';

				// recorremos los campos dinamicos y creamos la sentencia sql para realiazar el INSERT en la tabla catalogue_fields_value
				foreach ($sxe_fields as $field) {
					$sql_dinamic_fields .= '(' . $id++ . ',' . (string)$field['id_field'] . ',' . $_REQUEST['id'] . ',"' . $_REQUEST[(string)(string)$field['name']] . '","' . $_SESSION['lang'] . '"),';
				}

				$sql_dinamic_fields[strlen($sql_dinamic_fields)-1] = ';';
			}

			// ADD/UPDATE campos estaticos
			$rs = $sgbd->query($sql_static_fields);

			if ($rs === false) {
				// ERROR				
				echo 'ERROR al INSERT/UPDATE -> STATIC processForm_catalogue_item ';
				echo $sql_static_fields;
			}			
			
			
			// ADD/UPDATE campos dinamicos
			$rs = $sgbd->query($sql_dinamic_fields);
			
			// ERROR ...
			if ($rs === false) {
				// ERROR
				echo ' ERROR processForm_catalogue_item INSERT/UPDATE -> DYN catalogue_field_value ';
				echo $sql_dinamic_fields;
			}						
			
			$sgbd->disconnect();
			unset($sgbd,$sxe_fields);
			
			header('Location: ?431&id=' . $_REQUEST['id']);

		}
		else{
			//ERROR
			echo 'ERROR connect --> processForm_catalogue_item';
		}		
	}
	

/***************************
 * FI Table catalogue_item *
 ***************************/
?>