<?php
	/*****************
	 * Modul de Lang *
	 *****************/
	
	function getLangsSite(){
		global $_MANAGER;
		
		$xsl_list = <<<EOB
<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" indent="yes" disable-output-escaping="yes" omit-xml-declaration="yes" encoding="UTF-8" />
		
	<xsl:variable name="url">
		<xsl:value-of select="php:function('str_replace',concat('&amp;lang=',php:function('getParams2Xsl',1,'lang')),string(''),php:function('getParams2Xsl',2,'QUERY_STRING'))"/>
	</xsl:variable>
	
	<xsl:variable name="lang" select="php:function('getParams2Xsl',3,'lang')" />
	
	<xsl:template match="/">
        <ul>
            <xsl:apply-templates name="li" />
        </ul>        
    </xsl:template>
    
    <xsl:template match="li">
    	<xsl:element name="li">
    		<xsl:if test="\$lang=@name">    			
    				<xsl:attribute name="class">active</xsl:attribute>    			
    		</xsl:if>
    		<a href="?{\$url}&amp;lang={@name}"><xsl:value-of select="." /></a>
		</xsl:element>
    	
    </xsl:template>
</xsl:stylesheet>
EOB;
		
		//transformen el array d'idiomes del site a XML
		$xml = array2xml($_MANAGER['langs_site']);		

		return getTransformXML($xml,$xsl_list);
	}
	
?>