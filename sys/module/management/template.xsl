<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" encoding="UTF-8" />

	<xsl:template match="@*|node()">
        <xsl:copy>
            <xsl:apply-templates select="@*|node()" />
        </xsl:copy>        
    </xsl:template>
	
	<xsl:template match="part">
	        <xsl:apply-templates select="node()"/>
	</xsl:template>
	
	<xsl:template match="actions">
		<h5><xsl:value-of
		             disable-output-escaping="yes" select="php:function('getMessage',string(@name))" /></h5>
	</xsl:template>	
<!--
	<xsl:template match="table">
		<xsl:value-of
		             disable-output-escaping="yes" select="php:function('processingInstruction',string(@module),string(@action),string(@params))" />
	</xsl:template>	
-->
</xsl:stylesheet>