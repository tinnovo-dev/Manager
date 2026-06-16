<?php
	/*********************
	 * Modul de Noticies *
	 *********************/
	//require_once($_MANAGER['lib'] . 'XML' . $_MANAGER['separator'] . 'commons.php');	
		
	/**
	 * Funcio retorna el codi xhtml per mostrar.
	 * 
	 * $params es un string separat x ',' amb la llista de parametres que fan falta.
	 * news_view, news_category, limit, pagination
	 *
	 * @param unknown_type $params
	 * @return unknown
	 */
	function getListnews($params){
		global $_MANAGER;		
		
		//parametres ordenats a una array
		$arr_params = explode(',',$params);
		
		$sgbd = new mySGBD($_MANAGER['dsn']);
		
		
		// recuperem el id del block que volem llistar i el xsl per transformar el llistat
		$query = 'SELECT `news_template`.`source` as xsl, `news_view`.`news` as block 
					FROM `news_template`, `news_view` 
						WHERE `news_template`.`id` = `news_view`.`news_template` and `news_view`.`id`=' .$arr_params[0];

		
		$rs = $sgbd->query($query);
			
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> obtain_Id_view2Name_view';
		}
			
		$row = $rs->fetchRow();
		
		$news_block = $row['block'];
		$xsl = $row['xsl'];
		
		unset($rs);
		
		// Recuperem el llistat de noticies
		$query = 'SELECT `id`, `holder` FROM `news` WHERE `news_block`=' . $news_block . ' AND (`lang`="' . $_SESSION['lang'] . '" OR `lang`="ALL")';
						
		$rs = $sgbd->query($query);
			
		if ($rs === false) {
			// error al fer la query !!!
			print 'error SGBD query -> obtain_Id_view2Name_view';
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
?>