<?php
/**
 * Multidimensional array to XML with Parameters.
 * Made for easy reading the output text with TAB handler
 * Vertion: 0.2
 * Autor: Honor� Vasconcelos
 * Clean XML To Array: http://www.phpclasses.org/browse/package/3598.html
 */

/*
array for input for the class example:

Array
(
    [config] => Array
        (
            [filepath] => /tmp
            [interval] => 5
            [admins] => Array
                (
                    [n0] => admin1
                    [n1] => admin2
                    [n2] => Array
                        (
                            [n0] => fsdfsd
                            [n1] => dsfsdfs
                            [n2] => sdfsd
                        )

                )

            [admins-ATTR] => Array
                (
                    [test] => test3
                )

        )

)
*/

class multidi_array2xml {
	/**
	 * Parse multidimentional array to XML.
	 *
	 * @param array $array
	 * @return string	XML
	 */
	var $XMLtext;
	
	public function array2xml($array, $output=true) {
		//star and end the XML document
		$this->XMLtext="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<array>\n";
		
		if(count($array) > 0)
			$this->array_transform($array);
		
		$this->XMLtext .="</array>";
		if($output) return $this->XMLtext;
	}
	public function SaveXml($src){
		$myFile = "testFile.txt";
		$fh = @fopen($src, 'w');
		if($fh){
			fwrite($fh, $this->XMLtext);
			fclose($fh);
			return true;
		}else {
			return false;
		}
		
	}
	private function array_transform($array){
		static $Depth;

		foreach($array as $key => $value){
			if(!is_array($value)){
				unset($Tabs);
				for($i=1;$i<=$Depth+1;$i++) $Tabs .= "\t";
				//if(preg_match("/^[0-9]\$/",$key)) $key = "n$key";
				if(preg_match("/^[0-9]*\$/",$key)) $key = "n$key";
				$this->XMLtext .= "$Tabs<$key>$value</$key>\n";
			} else {
				$Depth += 1;
				unset($Tabs);
				for($i=1;$i<=$Depth;$i++) $Tabs .= "\t";
				//search for atribut like [name]-ATTR to put atributs to some object
				if(!preg_match("/(-ATTR)\$/", $key)) {
					//if(preg_match("/^[0-9]\$/",$key)) $keyval = "n$key"; else $keyval = $key;
					if(preg_match("/^[0-9]*\$/",$key)) $keyval = "n$key"; else $keyval = $key;
					$closekey = $keyval;
					if(is_array($array[$key."-ATTR"])){
						foreach ($array[$key."-ATTR"] as $atrkey => $atrval ) $keyval .= " ".$atrkey."=\"$atrval\"";
					} 
					$this->XMLtext.="$Tabs<$keyval>\n";
					$this->array_transform($value);
					$this->XMLtext.="$Tabs</$closekey>\n";
					$Depth -= 1;
					
				}
			}
		}
		return true;
	}
}

?>