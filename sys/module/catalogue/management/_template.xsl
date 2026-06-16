<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" encoding="UTF-8" />

<!--<xsl:template match="@*|node()"> <xsl:copy> </xsl:copy>        -->
	<xsl:template match="admin">        
            <xsl:apply-templates select="@*|node()" />        
    </xsl:template>
    	
<!--
	<xsl:template match="actions">
		<xsl:variable name="module" select="@module" />
		<xsl:for-each select="./action">
			<h1 class="block"><xsl:value-of
		    	         disable-output-escaping="yes" select="php:function('getMessage',string(@name))" /></h1>
			<ul>
				<li><a href="?management&#x26;module={$module}&#x26;table={@table}&#x26;action=1">
						<xsl:value-of
		    	        	 	disable-output-escaping="yes" select="php:function('getMessage',concat('add_',@table))" />
		    	    </a></li>
				<li><a href="?management&#x26;module={$module}&#x26;table={@table}&#x26;action=3">
						<xsl:value-of
		    	        		 disable-output-escaping="yes" select="php:function('getMessage',concat('list_',@table))" />
		    	    </a></li>
			</ul>
		</xsl:for-each>
	</xsl:template>	
-->
<!--
<table name="news_block">
		<field name="id" is_pk="true" type="0" order="1" />
		<field name="name" is_pk="false" type="1" order="2" />
		<field name="description" is_pk="false" type="text" order="3" />
		<field name="date_create" is_pk="false" type="date" order="4" />
		<field name="date_modificated" is_pk="false" type="date" order="5" />		
		<field name="user_create" is_pk="false" type="varchar" order="6" />
		<field name="user_modificated" is_pk="false" type="varchar" order="7" />
</table>
-->
	<xsl:template match="table">
		<xsl:variable name="action" select="php:function('getParams2Xsl',1,string('action'))" />
		<!--<xsl:variagle name="subaction" select="{$action}" />	
		<xsl:variable name="module" select="php:function('getParams2Xsl',1,string('module'))" />		
		<xsl:variable name="table" select="php:function('getParams2Xsl',1,string('table'))" />-->
		
		<xsl:variable name="id">
			<xsl:choose>
				<xsl:when test="$action=1">
					<xsl:value-of select="php:function('getNewId4Table',string(@name))" />
				</xsl:when>
				<xsl:when test="$action=2">
					<xsl:value-of select="php:function('getParams2Xsl',1,string('id'))" />
				</xsl:when>
			</xsl:choose>
		</xsl:variable>
		
<!--		<h1 class="pagetitle">
			<xsl:choose>
				<xsl:when test="$action=1">
 					<xsl:value-of select="php:function('getMessage',concat('add_',$table))" />
				</xsl:when>
			</xsl:choose>
		</h1>
-->	
		<div class="ddoverlap">
			
		</div>
		<div class="form">
			<form method="post" action="?management&#x26;action=5&#x26;subaction={$action}">
				<input value="{php:function('getMessage','send')}" type="submit" name="submit" id="submit" class="button" tabindex="6" />
				<input type="hidden" name="support" value="catalogue:{@name}" />
				<xsl:for-each select="./field">
					<xsl:choose>
						<xsl:when test="@type=-1">
							<hr />
						</xsl:when>
						<!-- text field -->
						<xsl:when test="@type=0 or @type=1">
							<xsl:variable name="value">
								<xsl:choose>
									<xsl:when test="@is_pk and string(@name)='id'">
										<xsl:value-of select="$id" />
									</xsl:when>
									<xsl:otherwise>
									
									</xsl:otherwise>
								</xsl:choose>
							</xsl:variable>
							<p>
								<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
								<input value="{$value}" type="text" name="{@name}" id="{@name}" class="small_field" tabindex="1" />
							</p>
						</xsl:when>
						<!-- textarea field -->
						<xsl:when test="@type=2">
							<p>
								<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
								<textarea name="{@name}" id="{@name}" cols="80" rows="6"></textarea>
							</p>						
						</xsl:when>
						<!-- string (no field) -->
						<xsl:when test="@type=4">
							<h6><xsl:value-of select="php:function('getMessage',string(@name))" />: </h6>
						</xsl:when>
						<!-- hidden field -->
						<xsl:when test="@type=5">
							<input type="hidden" name="{@name}" value="{php:function('getParams2Xsl',1,string(@name))}" />
						</xsl:when>
						<!-- bool (check/option) -->
						<xsl:when test="@type=6">
							<h6><xsl:value-of select="php:function('getMessage',string(@name))" />: </h6>							
							<label for="{@name}_yes">Si</label>
							<input type="radio" class="radio" name="{@name}" id="{@name}_yes" value="1" checked="checked" />
							<label for="{@name}_no">No</label>
							<input type="radio" class="radio" name="{@name}" id="{@name}_no" value="0" />
						</xsl:when>
						<!-- foreign data -->
						<xsl:when test="@type=7">
							<p>
								<label class="left" for="{@name}"><xsl:value-of select="php:function('getMessage',string(@name))" /></label>
<!--								<select name="{@name}">								-->
									<xsl:value-of disable-output-escaping="yes" select="php:function('getControl2ForeignData',string(@foreign),string(@foreign_fields),boolean(@is_category),string(@where))" />
<!--								</select> -->
							</p>							
						</xsl:when>
						<xsl:when test="@type=13">
							<p>
								<label class="left" for="{@name}"><xsl:value-of select="php:function('getMessage',string(@name),true())" /></label>
								<input type="file" name="{@name}" id="{@name}" class="small_field" tabindex="1" />
							</p>
						</xsl:when>						
					</xsl:choose>
				</xsl:for-each>
				<input value="{php:function('getMessage','send')}" type="submit" name="submit" id="submit" class="button" tabindex="6" />
			</form>
		</div>
	</xsl:template>
	
	<xsl:template match="@module">
	
	</xsl:template>
	
	

</xsl:stylesheet>