
{if $success}
	<div class="success">
		{$success}
	</div>
{/if}

{ignore}
<script type="text/javascript" charset="utf-8">
	function postPosition(id) {
		var theForm = document.getElementById('pos_'+id);
		if (theForm) {
			theForm.submit();
		}
	}
</script>
{/ignore}

<h1>Overzicht</h1>
<p><a href="{#SITE_URL}admin/page/add">Pagina's toevoegen</a></p>
<table>
	{foreach $pages as $key => $page}
		{if $page['menu_level'] == 0}
			{#$page['page']}
		{/if}
	{/foreach}
</table>




