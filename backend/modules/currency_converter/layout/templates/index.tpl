{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>{$lblCurrencyConverter|ucfirst}</h2>
</div>
{form:source}
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
    <div class="box">
            <div class="heading">
                    <h3>{$lblGraphSettings|ucfirst}</h3>
            </div>
            <div class="options">
                    <p>
                        Exchange Rate Source*<br />
                        {iteration:ersource}
                            <label for="{$ersource.id}">{$ersource.rbtErsource} {$ersource.label}</label>
                        {/iteration:ersource}
                        {$rbtErsourceError}
                    </p>
            </div>
            <div class="fullwidthOptions">
                    <div class="buttonHolderRight">
                        {$btnChange}
                    </div>
            </div>
    </div>
{/form:source}

<h3>{$lblExchangeRates|ucfirst}</h3>

{option:dataGrid}
	<div class="dataGridHolder">
		{$dataGrid}
	</div>
{/option:dataGrid}
{option:!dataGrid}<p>{$msgNoItems}</p>{/option:!dataGrid}

{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}