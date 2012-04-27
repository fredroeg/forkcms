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
			{option:graphData}
				<div id="dataChartSingleMetricPerDay" class="hidden">
					<span id="maxYAxis">{$maxYAxis}</span>
					<span id="tickInterval">{$tickInterval}</span>
					<span id="yAxisTitle">{$lblVisits|ucfirst}</span>
					<ul class="series">
						{iteration:graphData}
							<span class="name">{$graphData.label}</span>
							<ul class="data">
								{iteration:graphData.data}
									<li>
										<span class="fulldate">{$graphData.data.date|date:'D d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
										<span class="date">{$graphData.data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
										<span class="value">{$graphData.data.value}</span>
									</li>
								{/iteration:graphData.data}
							</ul>
						{/iteration:graphData}
					</ul>
				</div>
				<div id="chartSingleMetricPerDay">&nbsp;</div>
			{/option:graphData}
			<!--<div id="linechart"></div>-->
		</div>
</div>


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}