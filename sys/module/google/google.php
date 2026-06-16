<?php
	/******************************
	 * Modul de Serveis de Google *
	 ******************************/
	
	/**
	 * Aquesta funcio retorna un Google Map
	 *
	 */
	function getGoogleMap(){
		include('support/GoogleMap.class.php');
		
		$map = & new EasyGoogleMap('ABQIAAAAoM-kEW8yHxWwveOZAouVXhTkQdzC1XuexHlQDsWmu58XcfHJ8xQB-xtA9nt_7NDWTsfJfHHxosdNZg');
		
		$map->SetMarkerIconStyle('STAR');
		$map->SetMapZoom(10);
		$map->SetAddress("10 market st, san francisco");
		$map->SetInfoWindowText("This is the address # 1.");
		
		$xhtml = '';
		$xhtml .= $map->GmapsKey();
		
		$xhtml = str_replace('&','&#x26;',$xhtml);
				
		$xhtml .= $map->MapHolder();
		$xhtml .= $map->InitJs();		
		$xhtml .= $map->UnloadMap();
				
		$map = null;
		unset($map);
				
		return $xhtml;
	}

?>