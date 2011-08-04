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
		<tr>
			<td><?php echo Str::decode($page['title']); ?></td>
			<td><a href="<?php echo SITE_URL; ?>admin/page/edit/<?php echo $page['id']; ?>">Bewerk</a></td>
			<td><a href="<?php echo SITE_URL; ?>admin/page/delete/<?php echo $page['id']; ?>">Verwijder</a></td>
			<td>
				
				<form id="pos_<?php echo $page['pos']; ?>" method="post" action="">
					<input type="hidden" name="old_pos" value="<?php echo $page['pos']; ?>" />
					
					<?php if ($page['pos'] != 0) { ?>
						<button name="pos_up">Omhoog</button>
					<?php } ?>
					
					<?php if ($page['pos'] != $highest_pos) { ?>
						<button name="pos_down">Omlaag</button>
					<?php } ?>
					
				</form>
				
			</td>
		</tr>
	<?php } ?>
</table>




	<?php $_ = OutlineRuntime::finish(__FILE__); ?>