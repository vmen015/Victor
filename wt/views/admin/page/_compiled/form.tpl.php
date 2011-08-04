<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?><form method="post" id="mmForm">
	<a href="<?php echo SITE_URL; ?>admin/page">Ga terug naar pagina overzicht</a>
	
	<?php if (isset($page_data) && !empty($page_data)) { ?>
		<?php $data = true; ?>
	<?php } ?>
	
	<?php if ($data) { ?> 	<input type="text" name="id" value="<?php echo $page_data['id']; ?>" style="display:none;" />  <?php } ?>
	
	<div class="row  ptb-5">
		<input type="text" name="page" value="<?php if ($data) { echo Str::decode($page_data['page']); } ?>" id="page" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Pagina naam" data-tip="Naam van pagina	" data-tip-position="right" data-tip-edge="left" />
	</div>
	
	<div class="row  ptb-5">
		<p>Plaatsing in het menu</p>
		<select name="privacy" id="privacy">
			<option value="0">Hoofdmenu</option>
		</select>
	</div>
	
	<div class="row  ptb-5">
		<p>Zichbaar in het menu</p>
		<select name="privacy" id="privacy">
			<option value="0">Onzichtbaar</option>
			<option value="1">Zichtbaar</option>	
		</select>
	</div>
	
	<div class="row  ptb-5">
		<p>Status van pagina</p>
		<select name="privacy" id="privacy">
			<option value="0">Prive</option>
			<option value="1">Openbaar</option>	
		</select>
	</div>
	
	<div class="row  ptb-5">
		<input type="text" name="titles" value="<?php if ($data) { echo Str::decode($page_data['title']); } ?>" id="titles" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Titel" data-tip="Titel van pagina" data-tip-position="right" data-tip-edge="left" />
	</div>
	
	<div class="row  ptb-5">
		<input type="text" name="meta_tags" value="<?php if ($data) { echo $page_data['meta_tags']; } ?>" id="tags" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Tags" data-tip="Scheiden door komma ( , )" data-tip-position="right" data-tip-edge="left" />
	</div>
	
	<div class="row  ptb-5">
		<textarea name="meta_description" class="mmPlaceholder wp-100 bsB mmTip" placeholder="Google omschrijving" data-tip="Google omschrijving (max. 255 chars)" data-tip-position="right" data-tip-edge="left"><?php if ($data) { echo $page_data['meta_description']; } ?></textarea>
	</div>
	
	
	<div class="row  ptb-5">
		<textarea id="mmTextEditor" name="text" class="mmPlaceholder wp-100 bsB"><?php if ($data) { echo $page_data['text']; } ?></textarea>
	</div>
	                
	<input type="submit" name="submit" value="<?php if ($data) { ?>Wijzigingen opslaan<?php } else { ?>Maak pagina aan<?php } ?>">
	
</form><?php $_ = OutlineRuntime::finish(__FILE__); ?>