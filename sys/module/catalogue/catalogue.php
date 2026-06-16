<?php
	/********************
	 * Modul de Cataleg *
	 ********************/
	
/**
	 * Funcio retorna el codi xhtml per mostrar.
	 * 
	 * $params es un string separat per ',' amb la llista de parametres que fan falta.
	 * news_view, news_category, limit, pagination
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function getListCatalogue($params){
		global $_MANAGER;		
		
		//parametres ordenats a una array
		$arr_params = explode(',',$params);
		
		$sgbd = new mySGBD($_MANAGER['dsn']);
		
		
		// recuperem el id del block que volem llistar i el xsl per transformar el llistat
		$query = 'SELECT `catalogue_template`.`source` as xsl, `catalogue_view`.`catalogue` as catalogue 
					FROM `catalogue_template`, `catalogue_view` 
						WHERE `catalogue_template`.`id` = `catalogue_view`.`catalogue_template` and `catalogue_view`.`id`=' .$arr_params[0];

		
		$rs = $sgbd->query($query);
			
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> getListCatalogue';
		}
			
		$row = $rs->fetchRow();
		
		$catalogue = $row['block'];
		$xsl = $row['xsl'];
		
		unset($rs);
		
		// Recuperem el llistat de noticies
		$query = 'SELECT `id`, `holder` FROM `news` WHERE `news_block`=' . $news_block . ' AND (`lang`="' . $_SESSION['lang'] . '" OR `lang`="ALL")';
						
		$rs = $sgbd->query($query);
			
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> getListCatalogue';
		}
		
		$xml = <<<XML
		<news>\n
XML;
		
		while ($row = $rs->fetchRow()) {
			$xml .= '<holder id="' . $row['id'] . '"><![CDATA[' . $row['holder'] . ']]></holder>';
		}
		
		$xml .= '</news>';
				
		$sgbd->close();		
		
		unset($sgbd,$row, $rs);
				
		//return $params;
		return getTransformXML($xml,$xsl);
	}
	
	/**
	 * Aquesta funcio retorna els camps dinamics per aquest modul
	 *
	 */
	//function catalogue_getDinamicFields($id_catalogue, $id_category = null, $id_item = null){
	function catalogue_getDinamicFields(& $sxe_form, $arr){
		global $_MANAGER;
		//print 'catalogue_getDinamicFields';
		
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];

		$sgbd = new mySGBD($dsn);
				
		//print_r($arr);exit();
		
		if ($sgbd->connect()) {
			
			// los padres de la catalogue_category del item a buscar campos relacionados
			getIdFathersbyCategory($sgbd,'catalogue_category', $arr['type_1'][0],$arr['type_1']);
			// los padres de la catalogue_item_model_category del item a buscar campos relacionados
			getIdFathersbyCategory($sgbd, 'catalogue_item_model_category', $arr['type_3'][0],$arr['type_3']);
			
			// construir la QUERY para conseguir todos los campos dinamicos relacionados con el item
			$sql = 'SELECT field.id,field.name,field.type 
						FROM catalogue_field as field 
							INNER JOIN (SELECT * 
											FROM catalogue_field_rel 
												WHERE (object=' . $arr['type_0'] . ' AND type=0) OR ';
			// TYPE 1 ( CATALOGUE_CATEGORY )
			$count = count($arr['type_1']);
			for ($i=0;$i<$count;$i++)
				$sql .= '(object=' . $arr['type_1'][$i] . ' AND type=1) OR ';// . ($i+1<$count ? ' OR ' : ' ');

			// TYPE 2 ( CATALOGUE_ITEM_MODEL )
			$sql .= '(object=' . $arr['type_2'] . ' AND type=2) OR ';
			
			// TYPE 3 ( CATALOGUE_ITEM_MODEL_CATEGORY )
			$count = count($arr['type_3']);
			for ($i=0;$i<$count;$i++)
				$sql .= '(object=' . $arr['type_3'][$i] . ' AND type=3)' . ($i+1<$count ? ' OR ' : '');
				
			// TYPE 4 ( CATALOGUE_ITEM )
			if (!is_null($arr['type_4'])) $sql .= ' OR (object=' . $arr['type_4'] . ' AND type=4)';
			
			$sql .= ') AS rel ON field.id=rel.field';
				
			//echo $sql;exit();
			$rs = $sgbd->query($sql);
			
			if ($rs === false) {
				// ERROR al hacer la consulta
				echo $sql;
				echo 'ERROR al Query ---> catalogue_getDinamicFields';				
			}
			
			while ($rows = $rs->fetchRow()) {
				$child = $sxe_form->addChild('field');
				$child->addAttribute('is_dyn','true');
				$child->addAttribute('id_field',$rows['id']);
				$child->addAttribute('name',$rows['name']);
				$child->addAttribute('type',$rows['type']);
			}
			
			//$sgbd->disconnect();
			//unset($sgbd);
			//print_r($arr);exit();
		}
		else{
			// ERROR !!!
			echo 'ERROR catalogue_getDinamicFields -> connect';
		}
		// recuperem els pares de les categories arr[type_1] - arr[type_3]
		
		//var_dump($arr);exit();
		//print_r($arr);exit();
		//$sxe_form->addChild('prueba', 'jaaaaaaaaaa');
		
		//print_r($dsn);
	}
	
	
	/**
	 * Obtenim el nom del cataleg a partir del id
	 *
	 * @param unknown_type $id
	 */
	function getNameCatalogue($id){
		global $_MANAGER;
		
		//print_r($_REQUEST);
		
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];
									
		$sgbd = new mySGBD($dsn);
		
		if ($sgbd->connect()) {
			$sql = 'SELECT `name` FROM `catalogue` WHERE `id`=' . $id;
			
			$rs = $sgbd->query($sql);
			
			if ($rs === false) {
				// error al fer la query !!!
				print 'error SGBD query -> getNameCatalogue';				
			}
			
			$row = $rs->fetchRow();
			
			$sgbd->close();
			
		}
		else{
			// Controlar errors !!!
			echo 'error CONNECT -> getNameCatalogue';
		}			
		
		unset($sgbd,$rs);
		
		return $row['name'];
	}
	
	/**
	 * Funcion devuelve un objeto XML
	 *
	 * @param unknown_type $sgbd
	 * @param unknown_type $ctrl
	 * @param unknown_type $catalogue_category
	 * @param unknown_type $catalogue
	 * @param unknown_type $is_child
	 */
	function getCtrlSelect_catalogue_category(& $sgbd, & $ctrl, $catalogue_category = -1, $catalogue = null, $is_child = false){
		$sql = 'SELECT id,name 
					FROM catalogue_category 
						WHERE catalogue_category=' . $catalogue_category . (isset($catalogue) ? ' AND catalogue=' . $catalogue : '');
		
		$rs=$sgbd->query($sql);
		
		// ERROR !!!
		if ($rs === false) {
			echo $sql;
			echo 'ERROR QUERY getCtrlSelect_catalogue_category';
		}
		else{
			//if ($is_child) $child = $ctrl->addChild('child');			 
			
			while ($rows = $rs->fetchRow()) {
				if ($is_child) $childs = $ctrl->addChild('childs');
				
				$c = isset($childs) ? $childs->addChild('item') : $ctrl->addChild('item');
									
				//$c = $ctrl->addChild('item');
				
				//if ($is_child) $c->addAttribute('is_child',true);
				
				$c->addChild('value',$rows['id']);
				$c->addChild('name',$rows['name']);
				
				getCtrlSelect_catalogue_category($sgbd, $c, $rows['id'], $catalogue, true);
			}
			
			//$ctrl = $ctrl->asXML();
			
			//return $ctrl;	
		}		
	}

	/**
	 * Responde a la peticion AJAX de la lista de items(modelos) a partir de una 
	 * categoria
	 *
	 * @return unknown
	 */
	function ajax_getModelsbyCategory(){
		global $_MANAGER;
		//print_r($_REQUEST);
		if ($_REQUEST['catalogue_category'] == -1) return;
		
		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];
									
		$sgbd = new mySGBD($dsn);
		
		if ($sgbd->connect()) {

			$ids = array();
			$ids[] = $_REQUEST['catalogue_category'];
			getIdChildsbyCategory($sgbd,$_REQUEST['catalogue_category'],$ids);

			$sql = 'SELECT id,catalogue_item_model_category,name 
						FROM catalogue_item_model 
							WHERE catalogue_item_model_category 
								IN (SELECT id 
									FROM catalogue_item_model_category 
										WHERE ';
			$count = count($ids);
			for($i=0;$i<$count;$i++)
				$sql .= 'catalogue_category=' . $ids[$i] . ($i+1<$count ? ' OR ' : ')');
				
			//echo $sql;
			$rs=$sgbd->query($sql);
			
			if ($rs === false) {
				echo $sql;
				echo 'ERROR QUERY getCtrlSelect_catalogue_category';
			}
			else{
				while ($rows = $rs->fetchRow()) {
					$ajax_response .= '<item><name>' . $rows['name'] . '</name><value>' . $rows['id'] . ':' . $rows['catalogue_item_model_category'] . '</value></item>';
				}

				$sgbd->disconnect();
				
				unset($sgbd);

				return $ajax_response;
			}
		}
		else{
			// ERROR
			echo 'error connect to ajax_getModelsbyCategory';
		}		
		/*
		return '<item><name>Escape</name><value>Ford</value></item>
			<item><name>Expedition</name><value>Ford</value></item>
			<item><name>Explorer</name><value>Ford</value></item>
			<item><name>Focus</name><value>Ford</value></item>
			<item><name>Mustang</name><value>Ford</value></item>
			<item><name>Thunderbird</name><value>Ford</value></item>';
		*/
	}
	
	/**
	 * Funcion devuelve los hijos de una categoria a partir del padre
	 * recursivamente
	 *
	 * @param MYSGBD $sgbd
	 * @param unknown_type $id
	 * @param array $arr
	 */
	function getIdChildsbyCategory(& $sgbd, $id, & $arr){
		$sql = 'SELECT id FROM catalogue_category WHERE catalogue_category=' . $id;
		
		$rs=$sgbd->query($sql);

		// ERROR !!!
		if ($rs === false) {
			echo $sql;
			echo 'ERROR QUERY getCtrlSelect_catalogue_category';
		}
		else{
			while ($rows = $rs->fetchRow()) {
				$arr[] = $rows['id'];
				getIdChildsbyCategory($sgbd,$rows['id'],$arr);
			}
		}
	}
	
	/**
	 * Funcion devuelve los padres de una categoria a partir del hijo
	 * recursivamente
	 *
	 * @param MYSGBD $sgbd
	 * @param unknown_type $id
	 * @param array $arr
	 */
	function getIdFathersbyCategory(& $sgbd, $table, $id, & $arr){
		$sql = 'SELECT ' . $table . ' FROM ' . $table . ' WHERE id=' . $id;
		
		//echo $sql;//exit();
		$rs=$sgbd->query($sql);

		// ERROR !!!
		if ($rs === false) {
			echo $sql;
			echo 'ERROR QUERY getIdFathersbyCategory';
		}
		else{
			while ($rows = $rs->fetchRow()) {
				if ($rows[$table]>-1) $arr[] = $rows[$table];
				getIdFathersbyCategory($sgbd,$table,$rows[$table],$arr);
			}
		}
	}
	
	/**
	 * conseguimos el ID de la categoria del catalogo ( catalogue_category ) de una categoria de modelo ( catalogue_item_model_category )
	 *
	 * @param unknown_type $sgbd
	 * @param unknown_type $id_catalogue_item_model_category
	 * @return unknown
	 */
	function getCatalogue_categorybyCatalogue_item_model_category($id_catalogue_item_model_category, & $sgbd = null, $close_sgbd = false){
		if (is_null($sgbd)) {
			global $_MANAGER;
			$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
										: $_MANAGER['dsn'];

			$sgbd = new mySGBD($dsn);
		}
		
		if ($sgbd->connect()) {
			$sql = 'SELECT catalogue_category FROM catalogue_item_model_category WHERE id=' . $id_catalogue_item_model_category;

			$rs = $sgbd->query($sql);

			if ($rs === false){
				echo $sql;
				echo 'ERROR query --> getCatalogue_categorybyCatalogue_item_model_category';
			}

			$row = $rs->fetchRow();
			
			if ($close_sgbd) {
				$sgbd->disconnect();
				unset($sgbd);
			}

			return $row['catalogue_category'];
		}
		else{
			// ERROR
			echo 'ERROR Connect --> getCatalogue_categorybyCatalogue_item_model_category';
		}		
	}
	
	/**
	 * Conseguimos el nombre de un item a partir del modelo y las categorias a las que pertenece
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function getName_catalogue_item($params){
		global $_MANAGER;
		$params = explode(':',$params);

		$id_item_model = getParams2Xsl(1,$params[0]);
		list($id_item_model) = explode(':',$id_item_model);

		$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
									: $_MANAGER['dsn'];

		$sgbd = new mySGBD($dsn);

		if ($sgbd->connect()) {
			$name_item = getName_catalogue_item_model($id_item_model,true,$sgbd);

			$sgbd->disconnect();
			unset($sgbd);

			return $name_item;
		}
		else{
			// ERROR
			echo 'error connect to getName_catalogue_item';
		}
	}
	
	/**
	 * Conseguimos el nombre de un modelo del catalogo, con la posibilidad de recuperar
	 * la categoria del modelo (catalogue_item_model_category) 
	 *
	 * @param unknown_type $id_item_model
	 * @param unknown_type $with_item_model_category
	 * @param unknown_type $sgbd
	 * @param unknown_type $close_sgbd
	 */
	function getName_catalogue_item_model($id_item_model, $with_item_model_category = true, & $sgbd = null, $close_sgbd = false){
		//echo 'id_item_model --> ' . $id_item_model;exit();
		if (is_null($sgbd)) {
			global $_MANAGER;
			$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
										: $_MANAGER['dsn'];

			$sgbd = new mySGBD($dsn);
		}

		if ($sgbd->connect()) {
			$sql = 'SELECT `catalogue_item_model_category`,`name` FROM `catalogue_item_model` WHERE `id`=' . $id_item_model;

			$rs=$sgbd->query($sql);

			if ($rs === false) {
				echo $sql;
				echo 'ERROR QUERY getName_catalogue_item_model';
			}
			else{
				$row = $rs->fetchRow();
				
				if (!is_null($row)) {
					$name_item_model = $row['name'];

					// si queremos tambien el nombre concatenado de la categoria a la que pertenece el modelo ( Peugeot 307 HDI )
					if ($with_item_model_category) 
						$name_item_model = getName_catalogue_item_model_category($row['catalogue_item_model_category']) . ' ' . $name_item_model;
				}
			}
			
			// cerramos conexion BBDD ?
			if ($close_sgbd) {
				$sgbd->disconnect();
				unset($sgbd);	
			}
			
			// devolvemos el nombre 
			return $name_item_model;
			
		}
		else{
			// ERROR
			echo 'error connect to getName_catalogue_item_model';
		}		
	}
	
	/**
	 * Conseguimos el nombre de la categoria, con la posibilidad de concatenar el nombre
	 * de la categoria de la que hereda. Recursivamente.
	 *
	 * @param unknown_type $params
	 * @param unknown_type $is_recurcive
	 * @param unknown_type $sgbd
	 * @param unknown_type $close_sgbd
	 */
	function getName_catalogue_item_model_category($id_catalogue_item_model_category, & $sgbd = null, $close_sgbd = false, $is_recursive = true){
		//echo $id_catalogue_item_model_category;exit();
		if (is_null($sgbd)) {
			global $_MANAGER;
			$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
										: $_MANAGER['dsn'];

			$sgbd = new mySGBD($dsn);
		}

		if ($sgbd->connect()) {
			$sql = 'SELECT `catalogue_item_model_category`,`name` FROM `catalogue_item_model_category` WHERE `id`=' . $id_catalogue_item_model_category;

			$rs=$sgbd->query($sql);

			if ($rs === false) {
				//print_r($rs);exit();
				echo $sql;
				echo 'ERROR QUERY getName_catalogue_item_model_category';
			}
			else{
				$row = $rs->fetchRow();
				
				if (!is_null($row)){
					$name_item_model_category = $row['name'];
					if ($is_recursive)
						$name_item_model_category = getName_catalogue_item_model_category($row['catalogue_item_model_category'], $sgbd) . ' ' . $name_item_model_category;
				}
			}
			
			// cerramos conexion BBDD
			if ($close_sgbd) {
				$sgbd->disconnect();
				unset($sgbd);	
			}

			// devolvemos el nombre 
			return trim($name_item_model_category);			
		}
		else{
			// ERROR
			echo 'error connect to getName_catalogue_item_model_category';
		}
	}
	
	/**
	 * Obtenemos el ID de catalogue_item_model_category a la que pertenece un catalogue_item_model
	 *
	 * @param unknown_type $id_cat_item_model
	 */
	function getId_catalogue_item_model_category_Of_catalogue_item_model($id_cat_item_model, & $sgbd = null, $close_sgbd = false){
		if (is_null($sgbd)) {
			global $_MANAGER;
			$dsn = $_MANAGER['admin']   ? $_SESSION['site_admin_dsn']
										: $_MANAGER['dsn'];

			$sgbd = new mySGBD($dsn);
		}

		if ($sgbd->connect()) {
			$sql = 'SELECT `catalogue_item_model_category` FROM `catalogue_item_model` WHERE `id`=' . $id_cat_item_model;

			$rs=$sgbd->query($sql);

			if ($rs === false) {
				//print_r($rs);exit();
				echo $sql;
				echo 'ERROR QUERY getId_catalogue_item_model_category_Of_catalogue_item_model';
			}
			else{
				$row = $rs->fetchRow();
				
				if (!is_null($row)){
					$id_cat_item_model_category = $row['catalogue_item_model_category'];
				}
			}
			
			// cerramos conexion BBDD
			if ($close_sgbd) {
				$sgbd->disconnect();
				unset($sgbd);	
			}

			// devolvemos el nombre 
			return $id_cat_item_model_category;			
		}
		else{
			// ERROR
			echo 'error connect to getId_catalogue_item_model_category_Of_catalogue_item_model';
		}
	}
	
?>