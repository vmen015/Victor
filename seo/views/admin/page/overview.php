
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
		<tr>
			<td>{#Str::decode($page['title'])}</td>
			<td><a href="{#SITE_URL}admin/page/edit/{$page['id']}">Bewerk</a></td>
			<td><a href="{#SITE_URL}admin/page/delete/{$page['id']}">Verwijder</a></td>
			<td>
				
				<form id="pos_{$page['pos']}" method="post" action="">
					<input type="hidden" name="old_pos" value="{$page['pos']}" />
					
					{if $page['pos'] != 0}
						<button name="pos_up">Omhoog</button>
					{/if}
					
					{if $page['pos'] != $highest_pos}
						<button name="pos_down">Omlaag</button>
					{/if}
					
				</form>
				
			</td>
		</tr>
	{/foreach}
</table>




	