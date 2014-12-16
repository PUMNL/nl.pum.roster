<div id="roster_view" class="dataTables_wrapper">
	<table>
		<tr>
			<th>{$labels.name}</th>
			<th>{$labels.type}</th>
			<th>{$labels.value}</th>
			<th>{$labels.next_run}</th>
			<th>{$labels.last_run}</th>
			<th>{$labels.privilege}</th>
			<th>{$labels.links}</th>
		</tr>
{assign var="rowClass" value="odd-row"}
{if ! isset($var)}

{else}
{foreach from=$rows key=index item=record}
		<tr id="row_{$index}" class={$rowClass}>
{if $record.id eq ''}
			<td colspan="7">{$record.name}</td>
{else}
			<td>{$record.name}</td>
			<td>{$record.type_translated}</td>
			<td>{$record.value_translated}</td>
			<td>{$record.next_run}</td>
			<td>{$record.last_run}</td>
			<td>{$record.privilege}</td>
			<td>{$record.links}</td>
{/if}
		</tr>
	{if $rowClass eq "odd-row"}
		{assign var="rowClass" value="even-row"}
	{else}
		{assign var="rowClass" value="odd-row"}                        
	{/if}
{/foreach}


	</table>
</div>
