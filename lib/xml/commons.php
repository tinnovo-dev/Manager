<?php
	/***********************************************
	 * Funcions de suport per tractar xml i el xsl *
	 ***********************************************/
	require_once('array2xml.class.php');
	
	/**
	 * Aquesta funcio transformen XML amb una template XSLT
	 *
	 * @param unknown_type $xml
	 * @param unknown_type $xsl
	 * @param unknown_type $parse_html_entities
	 * @return unknown
	 */
	function getTransformXML(& $xml, & $xsl, $is_string_xml = true, $is_string_xsl = true, $parse_html_entities = false){
		//$xmldoc = DOMDocument::loadXML(html_entity_decode($xml,null,"utf-8"));
		$xmldoc = $is_string_xml ? DOMDocument::loadXML($xml) : DOMDocument::load($xml);
		
		$xsldoc = $is_string_xsl ? DOMDocument::loadXML($xsl) : DOMDocument::load($xsl);
		
//		print_r($xml);exit();

		$proc = new XSLTProcessor();		
		
		$proc->registerPHPFunctions();
		$proc->importStyleSheet($xsldoc);
		
		//$xmldoc = htmlentities($xmldoc);
		
		$xml_trasform = $proc->transformToXML($xmldoc);
		$proc = null;		
		
		return $parse_html_entities ? html_entity_decode($xml_trasform,null,"utf-8") : $xml_trasform;
		//return $xml_trasform;
	}
	
	/**
	 * Aquesta funcio retorna un XML a partir d'un Array
	 *
	 * @param unknown_type $array
	 */
	function array2xml(& $array){
		$xml = '<xml>';
		
		foreach ($array as $key => $value) {
			$xml .= '<li name="' . $key . '">' . $value . '</li>';
		}
		
		$xml .= '</xml>';
		
		//print $xml;
			
		return $xml;
	}
	
	
	function getElementsByTag($xml, $tagname, $is_for_xpath = false, $is_file = true, $is_XML = true){
		
		//print_r($tagname);
			
		$obj_xml = $is_file ? simplexml_load_file($xml) 
							: simplexml_load_string($xml);
				
		if($is_for_xpath){
			$result = $obj_xml->xpath($tagname);
			
			if (count($result) === 0) {
				return false;
			}
			
			return $is_XML  ? $result[0]->asXML()
							: $result[0];
		}
		
		//echo '2222222222';
				
		return $is_XML  ? $obj_xml->$tagname->asXML()
						: $obj_xml->$tagname;		
	}
	
	
	function & getValueAttr($attrs, $name){
		return 	$attrs[$name];
	}
?>