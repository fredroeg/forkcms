{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
	<h2>{$lblSea|ucfirst}</h2>
</div>
{form:connectform}
<div id="changeSettingsForm>
        <div class="box horizontal">
		<div class="heading">
			<h3>{$lblGoogleConsoleSettings|ucfirst}</h3
			<p>{$msgConsoleInfo}</p>
		</div>
		<div class="options">
                        <p>
                            <label for="clientid">{$lblClientId|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtClientId} {$txtClientIdError}
                        </p>
                        <p>
                            <label for="clientidsecret">{$lblClientIdSecret|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtClientIdSecret} {$txtClientIdSecretError}
                        </p>
			<p>
			{option:profileId}
			<label>{$lblProfile|ucfirst}</label>
			{iteration:profileId}
			    <label for="{$profileId.id}">{$profileId.rbtProfileId} {$profileId.label}</label>
			{/iteration:profileId}
			{/option:profileId}
			{option:profileError}
			<div class='errorMessage'>
			    {$profileError}
			</div>
			{/option:profileError}
                </div>
                <div class="fullwidthOptions">
                        <div class="buttonHolderRight">
                            {$btnChange}
                        </div>
                </div>
	</div>
</div>
{/form:connectform}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}