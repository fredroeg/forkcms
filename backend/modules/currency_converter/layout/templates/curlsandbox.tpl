{include:{$BACKEND_CORE_PATH}/layout/templates/head.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/structure_start_module.tpl}

<div class="pageTitle">
    <h2>Sandbox</h2>
</div>

{iteration:tweets}
    <ul>
        <li>{$tweets.created_at}</li>
        <li>{$tweets.text}</li>
        <li>--------------------</li>
    </ul>
{/iteration:tweets}


{include:{$BACKEND_CORE_PATH}/layout/templates/structure_end_module.tpl}
{include:{$BACKEND_CORE_PATH}/layout/templates/footer.tpl}