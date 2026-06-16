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