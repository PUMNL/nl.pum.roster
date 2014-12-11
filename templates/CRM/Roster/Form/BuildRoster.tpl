{* HEADER *}

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="top"}
</div>

<div class="crm-block crm-form-block">
{if $warning neq ''}
	{$warning}
{else}
	<table class="form-layout-compressed">
	{if $mode eq 'edit'}
		{foreach from=$elementNames item=elementName}
		<tr>
			<td class="label">{$form.$elementName.label}</td>
			<td class="content">{$form.$elementName.html}</td>
		</tr>
		{/foreach}    
	{else}
		<tr>
			<td class="label"><label for="roster_type_txt">{$roster_type_txt.label}</label></td>
			<td class="content">{$roster_type_txt.value}</td>
		</tr>
		{if $roster_type_txt.code eq'w'}
		<tr>
			<td class="label"><label for="roster_week_txt">{$roster_week_txt.label}</label></td>
			<td class="content">{$roster_week_txt.value}</td>
		</tr>
		{else}
		<tr>
			<td class="label"><label for="roster_month_txt">{$roster_month_txt.label}</label></td>
			<td class="content">{$roster_month_txt.value}</td>
		</tr>
		{/if}
		<tr>
			<td class="label"><label for="roster_interval_txt">{$roster_interval_txt.label}</label></td>
			<td class="content">{$roster_interval_txt.value}</td>
		</tr>
		<tr>
			<td class="label"><label for="roster_nextrun_txt">{$roster_nextrun_txt.label}</label></td>
			<td class="content">{$roster_nextrun_txt.value}</td>
		</tr>
	{/if}
		<tr>
			<td class="label"><label for="last_run">{$roster_lastrun_txt.label}</label></td>
			<td class="content">{$roster_lastrun_txt.value}</td>
		</tr>
		<tr>
			<td class="label"><label for="privilege">{$roster_privilege_txt.label}</label></td>
			<td class="content">{$roster_privilege_txt.value}</td>
		</tr>
	</table>
{/if}









	
</div>

<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>

{literal}
<script type="text/javascript">
	// add onChange event to roster_type field to display the appropriate days selection field
	cj('#roster_type').change(function() { processRosterTypeChange(this) });
	// trigger onChange event on roster_type field
	cj('#roster_type').trigger('change', ['{$form.dsa_location.value[0]']);
	
	function processRosterTypeChange(elm) {
		var row_week = cj('#roster_week').parents('tr');
		var row_month = cj('#roster_month').parents('tr');
		switch (elm.value) {
			case 'w':
				row_week.show();
				row_month.hide();
				break;
			case 'm':
				row_week.hide();
				row_month.show();
				break;
			default:
				row_week.hide();
				row_month.hide();
		}
		return;
	}
</script>
{/literal}
