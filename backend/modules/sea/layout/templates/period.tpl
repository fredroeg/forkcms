<div class="heading">
	<h3>{$lblStatistics|ucfirst} {$lblFrom} {$startTimestamp|formatdate} {$lblTill} {$endTimestamp|formatdate}</h3>
</div>

<div class="footer oneLiner">
	{form:periodPickerForm}
		<p>
			<label for="startDate">{$lblStartDate|ucfirst}</label>
			{$txtStartDate}
		</p>
		<p>
			<label for="endDate">{$lblEndDate|ucfirst}</label>
			{$txtEndDate}
		</p>
		<p>
			<input id="update" type="submit" name="update" value="{$lblChangePeriod|ucfirst}" />
		</p>
		{$txtStartDateError}
		{$txtEndDateError}
	{/form:periodPickerForm}
</div>