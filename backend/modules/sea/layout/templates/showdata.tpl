{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSea|ucfirst}</h2>
</div>

<div class="box">
		{include:{$BACKEND_MODULE_PATH}/layout/templates/period.tpl}
		<div class="options content">
			<div class="analyticsColWrapper clearfix">
				<div class="analyticsCol">
					<p><strong>{$visits}</strong> {$lblVisits|ucfirst}</p>
				</div>
				<div class="analyticsCol">
					<p><strong>{$conversions}</strong> {$lblConversions|ucfirst}</p>
				</div>
				<div class="analyticsCol">
					<p><strong>{$conversionPercentage}</strong> {$lblConversionPercentage|ucfirst}</p>
				</div>
				<div class="analyticsCol">
					<p><strong>{$costPerConversion}</strong> {$lblCostPerConversion|ucfirst}</p>
				</div>
			</div>
		</div>
		<div class="options content">
			<div id="linechart"></div>
		</div>
</div>


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}