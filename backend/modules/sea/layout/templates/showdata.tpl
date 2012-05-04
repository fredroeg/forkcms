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
	</div>
	<div class="options content">
		<div class="heading">
			<h3>{$lblStatisticsThisMonth|ucfirst}</h3>
		</div>
		<div class="options">
			<div class="seaColWrapper clearfix">
				<div class="seaCol">
					<p><strong>{$impressions}</strong> {$lblImpressions|ucfirst}</p>
					<p><strong>{$clicks}</strong> {$lblClicks|ucfirst}</p>
				</div>
				<div class="seaCol">
					<p><strong>{$ctr}</strong> {$lblCTR|ucfirst}</p>
					<p><strong>{$costPerClick}</strong> {$lblCostPerClick|ucfirst}</p>
				</div>
				<div class="seaCol">
					<p><strong>{$position}</strong> {$lblPosition|ucfirst}</p>
					<p><strong>{$cost}</strong> {$lblCost|ucfirst}</p>
				</div>
			</div>
		</div>
	</div>
	<div class="options content">
		<div class="analyticsGraphWrapper">
			<div class="analyticsLeftCol">
				<div class="heading">
					<h3>Goals</h3>
				</div>
				<div class="options">
				{option:goals}
				    <ul>
					    {iteration:goals}
						    <li>{$goals.goal_name}</li>
					    {/iteration:goals}
				    </ul>
				{/option:goals}
				</div>
			</div>
			<div class="analyticsRightCol">
				<div class="heading">
					<h3>Cost/... Highchart</h3>
				</div>
				<div class="options">
					{option:graphDataMulti}
						<div id="dataChartDoubleMetricPerDay" class="hidden">
							    <span id="yAxisTitle">{$lblVisits|ucfirst}</span>
							    <ul class="series">
								    {iteration:graphDataMulti}
									    <li class="serie" id="metric{$graphDataMulti.i}serie">
										    <span class="name">{$graphDataMulti.label}</span>
										    <ul class="data">
											    {iteration:graphDataMulti.data}
												    <li>
													<span class="date">{$graphDataMulti.data.date|date:'d M':{$INTERFACE_LANGUAGE}|ucwords}</span>
													    <span class="value">{$graphDataMulti.data.value}</span>
												    </li>
											    {/iteration:graphDataMulti.data}
										    </ul>
									    </li>
								    {/iteration:graphDataMulti}
							    </ul>
						</div>
						<div id="chartDoubleMetricPerDay">
							&nbsp;
						</div>
					{/option:graphDataMulti}
				</div>
			</div
		</div>
	</div>
</div>


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}