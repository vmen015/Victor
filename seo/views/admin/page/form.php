<form method="post" id="mmForm">
	<a href="{#SITE_URL}admin/page">Ga terug naar pagina overzicht</a>
	
	{if isset($page_data) && !empty($page_data)}
		{set $data = true}
	{/if}
	
	{if $data} 	<input type="text" name="id" value="{#$page_data['id']}" style="display:none;" />  {/if}
	
	<div class="row  ptb-5">
		<input type="text" name="titles" value="{if $data}{# Str::decode($page_data['title'])}{/if}" id="titles" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Titel" data-tip="Titel van pagina" data-tip-position="right" data-tip-edge="left" />
	</div>
	
	<div class="row  ptb-5">
		<input type="text" name="meta_tags" value="{if $data}{#$page_data['meta_tags']}{/if}" id="tags" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Tags" data-tip="Scheiden door komma ( , )" data-tip-position="right" data-tip-edge="left" />
	</div>
	
	<div class="row  ptb-5">
		<textarea name="meta_description" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Google omschrijving" data-tip="Google omschrijving (max. 255 chars)" data-tip-position="right" data-tip-edge="left">{if $data}{#$page_data['meta_description']}{/if}</textarea>
	</div>
	
	
	<div class="row  ptb-5">
		<textarea id="mmTextEditor" name="text" class="mmPlaceholder wp-100 bsB">{if $data}{#$page_data['text']}{/if}</textarea>
	</div>
	                
	<input type="submit" name="submit" value="{if $data}Wijzigingen opslaan{else}Maak pagina aan{/if}">
	
</form>