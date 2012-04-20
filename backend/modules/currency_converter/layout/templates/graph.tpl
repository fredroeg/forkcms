{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>{$lblCurrencyConverter|ucfirst}</h2>
</div>

<h3>{$lblGraphSettings|ucfirst}</h3>
{option:dgGraphSettings}
        <div class="datagridHolder">
            {$dgGraphSettings}
        </div>
{/option:dgGraphSettings}



{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}