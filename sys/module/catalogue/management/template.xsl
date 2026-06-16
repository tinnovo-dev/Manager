<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:php="http://php.net/xsl" exclude-result-prefixes="php">
	<xsl:output method="xml" indent="yes" omit-xml-declaration="yes" encoding="UTF-8" />

<!--<xsl:template match="@*|node()"> <xsl:copy> </xsl:copy>        -->
	<xsl:template match="admin">        
            <xsl:apply-templates select="@*|node()" />        
    </xsl:template>
    	

	<xsl:template match="table">
		<!-- accion - $action = 1 -> Nuevo Registro / $action = 2 -> Editando Registro -->
<!--		<xsl:variable name="action" select="php:function('getParams2Xsl',1,string('action'))" /> 
				<xsl:variable name="mode" select="0" />-->
		<xsl:variable name="mode" select="php:function('getParams2Xsl',1,string('mode'))" /> 
		<!-- Pestaña para los idiomas -->
		<div class="ddoverlap">
			
		</div>
		<div class="form">
			<form method="post" name="{@name}" action="{./action_link}">
				<input value="{php:function('getMessage','send')}" type="submit" name="submit" id="submit" class="button" tabindex="6" />
				<hr />
				<xsl:for-each select="./field">
					<!-- Comprobamos si se trata de un registro nuevo ( mode 0 ), o si estamos editando un registro ( mode 1) -->
					<xsl:choose>
						<!-- ADD -->
						<xsl:when test="$mode=0">
							<!-- recorremos los fields del formulario -->
							<xsl:choose>
								<!-- separtor Line -->
								<xsl:when test="@type=-1">
									<hr />
								</xsl:when>
								<!-- text field ID -->
								<xsl:when test="@type=0">
									<xsl:variable name="value_id">
										<xsl:value-of select="php:function('getNewId4Table',string(../@name))" />
									</xsl:variable>
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: <xsl:value-of select="$value_id" /></label>
									</p>
									<input value="{$value_id}" type="hidden" name="{@name}" id="{@name}" />
								</xsl:when>
								<!-- text field -->		
								<xsl:when test="@type=1">
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
										<input value="" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" />
									</p>
								</xsl:when>
								<!-- File input -->
								<xsl:when test="@type=13">
									<p>
										<label class="left" for="{@name}"><xsl:value-of select="php:function('getMessage',string(@name),true())" /></label>
										<input type="file" name="{@name}" id="{@name}" class="field" tabindex="1" />
									</p>
								</xsl:when>						
								<!-- text field con el value heredado -->
								<xsl:when test="@type=14">
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
		<!--								<input value="{php:function('getName_catalogue_item',concat(string(@source),':1'))}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" /> -->
										<input value="{php:function('getName_catalogue_item',string(@source))}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" />
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
										<xsl:value-of disable-output-escaping="yes" select="php:function('getControl2ForeignData',string(@foreign),string(@foreign_fields),boolean(@is_category),string(@where))" />
									</p>							
								</xsl:when>						
							</xsl:choose>
						</xsl:when>
						<!-- EDIT -->
						<xsl:when test="$mode=1">
							<!-- recorremos los fields del formulario -->
							<xsl:choose>
								<!-- separtor Line -->
								<xsl:when test="@type=-1">
									<hr />
								</xsl:when>
								<!-- text field ID -->
								<xsl:when test="@type=0">									
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: <xsl:value-of select="." /></label>
									</p>
									<input value="{.}" type="hidden" name="{@name}" id="{@name}" />
								</xsl:when>
								<!-- text field -->		
								<xsl:when test="@type=1">
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
<!--										<input value="{.}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" /> -->
										<input value="{php:function('html_entity_decode',string(.))}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" />
										
									</p>
								</xsl:when>
								<!-- File input -->
								<xsl:when test="@type=13">
									<p>
										<label class="left" for="{@name}"><xsl:value-of select="php:function('getMessage',string(@name),true())" /></label>
										<input type="file" name="{@name}" id="{@name}" class="field" tabindex="1" />
									</p>
								</xsl:when>						
								<!-- text field con el value heredado -->
								<xsl:when test="@type=14">
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
		<!--								<input value="{php:function('getName_catalogue_item',concat(string(@source),':1'))}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" /> -->
										<input value="{php:function('html_entity_decode',string(.))}" type="text" name="{@name}" id="{@name}" class="field" tabindex="1" />
									</p>
								</xsl:when>		
								<!-- textarea field -->
								<xsl:when test="@type=2">
									<p>
										<label for="{@name}" class="left"><xsl:value-of select="php:function('getMessage',string(@name),true())" />: </label>
										<textarea name="{@name}" id="{@name}" cols="80" rows="6"><xsl:value-of select="php:function('html_entity_decode',string(.))" /></textarea>
									</p>						
								</xsl:when>
								<!-- string (no field) -->
								<xsl:when test="@type=4">
									<h6><xsl:value-of select="php:function('getMessage',string(@name))" />: </h6>
								</xsl:when>
								<!-- hidden field -->
								<xsl:when test="@type=5">
									<input type="hidden" name="{@name}" value="{.}" />
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
										<xsl:value-of disable-output-escaping="yes" select="php:function('getControl2ForeignData',string(@foreign),string(@foreign_fields),boolean(@is_category),string(@where))" />
									</p>							
								</xsl:when>						
							</xsl:choose>
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