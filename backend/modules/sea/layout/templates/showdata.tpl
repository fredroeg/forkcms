{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSea|ucfirst}</h2>
</div>

<div id="returnedData">
<table>
    <tr>
    {iteration:columnheaders}
	<th>{$columnheaders.name}</th>
    {/iteration:columnheaders}
    </tr>
    {iteration:columncontent}
    <tr>
	<td>{$columncontent.medium}</td>
	<td>{$columncontent.source}</td>
	<td>{$columncontent.keyword}</td>
	<td>{$columncontent.visits}</td>
	<td>{$columncontent.bounces}</td>
    </tr>
    {/iteration:columncontent}
</table>
</div>

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}