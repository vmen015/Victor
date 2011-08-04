<?php $_ = OutlineRuntime::start(__FILE__, isset($this) ? $this : null); ?>
<?php if ($success) { ?>
	<div class="success">
		<?php echo $success; ?>
	</div>
<?php } ?>


<script type="text/javascript" charset="utf-8">
	function postPosition(id) {
		var theForm = document.getElementById('pos_'+id);
		if (theForm) {
			theForm.submit();
		}
	}
</script>


<h1>Overzicht</h1>
<p><a href="<?php echo SITE_URL; ?>admin/page/add">Pagina's toevoegen</a></p>
<table>
	<?php foreach ($pages as $key => $page) { ?>
		<?php if ($page['menu_level'] == 0) { ?>
			<?php echo $page['page']; ?>
			<?php echo $this->hoi(); ?>
		<?php } ?>
	<?php } ?>
</table>




<?php $_ = OutlineRuntime::finish(__FILE__); ?>