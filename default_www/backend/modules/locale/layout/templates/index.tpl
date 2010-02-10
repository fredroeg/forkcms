{include:file='{$BACKEND_CORE_PATH}/layout/templates/header.tpl'}
{include:file='{$BACKEND_CORE_PATH}/layout/templates/sidebar.tpl'}
		<td id="contentHolder">
			<div id="statusBar">
				<p class="breadcrumb">{$lblLocale|ucfirst} &gt; {$lblOverview|ucfirst}</p>
			</div>

			<div class="inner">
				{form:filter}
					<div class="box">
						<div class="heading">
							<h3>{$lblFilter|ucfirst}</h3>
						</div>
						<div class="options">
							<div class="horizontal">
								<p>
									<label for="name">{$lblName|ucfirst}</label>
									{$txtName} {$txtNameError}
									<span class="helpTxt">{$msgNameHelpTxt}</span>
								</p>
								<p>
									<label for="value">{$lblValue|ucfirst}</label>
									{$txtValue} {$txtValueError}
									<span class="helpTxt">{$msgValueHelpTxt}</span>
								</p>

								<p>
									<label for="language">{$lblLanguage|ucfirst}</label>
									{$ddmLanguage} {$ddmLanguageError}
								</p>
								<p>
									<label for="application">{$lblApplication|ucfirst}</label>
									{$ddmApplication} {$ddmApplicationError}
								</p>
								<p>
									<label for="module">{$lblModule|ucfirst}</label>
									{$ddmModule} {$ddmModuleError}
								</p>
								<p>
									<label for="type">{$lblType|ucfirst}</label>
									{$ddmType} {$ddmTypeError}
								</p>
								<p class="spacing">
									<input id="search" class="inputButton button mainButton" type="submit" name="search" value="{$lblSearch|ucfirst}" />
								</p>
							</div>
						</div>
					</div>
				{/form:filter}

				{option:datagrid}
					<div class="datagridHolder">
						<div class="tableHeading">
							<h3>{$lblTranslations|ucfirst}</h3>
							<div class="buttonHolderRight">
								<a href="{$var|geturl:'add'}&language={$language}&application={$application}&module={$module}&type={$type}&name={$name}&value={$value}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
							</div>
						</div>
						<form action="{$var|geturl:'mass_action'}" method="get" class="forkForms submitWithLink" id="massLocaleAction">
							<input type="hidden" name="offset" value="{$offset}" />
							<input type="hidden" name="order" value="{$order}" />
							<input type="hidden" name="sort" value="{$sort}" />
							<input type="hidden" name="language" value="{$language}" />
							<input type="hidden" name="application" value="{$application}" />
							<input type="hidden" name="module" value="{$module}" />
							<input type="hidden" name="type" value="{$type}" />
							<input type="hidden" name="name" value="{$name}" />
							<input type="hidden" name="value" value="{$value}" />
							<div class="datagridHolder">
								{$datagrid}
							</div>
						</form>
					</div>
				{/option:datagrid}
				{option:!datagrid}
				<div class="datagridHolder">
					<div class="tableHeading">
						<h3>{$lblTranslations|ucfirst}</h3>
						<div class="buttonHolderRight">
							<a href="{$var|geturl:'add'}&language={$language}&application={$application}&module={$module}&type={$type}&name={$name}&value={$value}" class="button icon iconAdd"><span><span><span>{$lblAdd|ucfirst}</span></span></span></a>
						</div>
					</div>
					<table border="0" cellspacing="0" cellpadding="0" class="datagrid">
						<tr>
							<td>{$msgNoItems}</td>
						</tr>
					</table>
				</div>
				{/option:!datagrid}
			</div>
		</td>
	</tr>
</table>
{include:file='{$BACKEND_CORE_PATH}/layout/templates/footer.tpl'}