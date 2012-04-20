{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>{$lblCurrencyConverter|ucfirst}</h2>
</div>
{form:edit}

<div id="changeSettingsForm>

        <script type="text/javascript">
		<![CDATA[
			var defaultErrorMessages = {};

			{option:errors}
				{iteration:errors}
					defaultErrorMessages.{$errors.type} = '{$errors.message}';
				{/iteration:errors}
			{/option:errors}
		//]]>
	</script>

        <div class="box horizontal">
		<div class="heading">
			<h3>{$lblGraphSettings|ucfirst}</h3>
		</div>
		<div class="options">
                        <input type="hidden" name="id" id="formId" value="{$id}" />

                        <p>
                            <label for="block">{$lblBlock|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtBlock} {$txtBlockError}
                        </p>
                        <p>
                            <label for="type">{$lblType|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$ddmType} {$ddmTypeError}
                        </p>
                        <p>
                            <label for="theme">{$lblTheme|ucfirst}</label>
                            {$ddmTheme} {$ddmThemeError}
                        </p>
                        <p>
                            <label for="title">{$lblTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtTitle} {$txtTitleError}
                        </p>
                        <p>
                            <label for="subtitle">{$lblSubtitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtSubtitle} {$txtSubtitleError}
                        </p>
                        <p>
                            <label for="xaxistitle">{$lblXAxisTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtXaxistitle} {$txtXaxistitleError}
                        </p>
                        <p>
                            <label for="yaxistitle">{$lblYAxisTitle|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                            {$txtYaxistitle} {$txtYaxistitleError}
                        </p>
                        <p>

                        </p>
                </div>
                <div class="fullwidthOptions">
                        <div class="buttonHolderRight">
                            {$btnChange}
                        </div>
                </div>
	</div>
</div>
{/form:edit}



{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}