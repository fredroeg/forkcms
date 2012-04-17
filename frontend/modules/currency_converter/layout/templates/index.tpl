<section id="currencyConvert" class="mod">
	<div class="inner">
		<div class="bd">
			{option:convertHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:convertHasFormError}
			{option:convertIsSuccess}<div class="message success"><p>{$msgConvertSuccess}</p></div>{/option:convertIsSuccess}

				{form:convert}
					<p{option:txtEmailError} class="errorArea"{/option:txtEmailError}>
						<label for="email">{$lblEmail|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtEmail} {$txtEmailError}
					</p>
					<p>
						<input id="send" class="inputSubmit" type="submit" name="send" value="{$lblSend|ucfirst}" />
					</p>
				{/form:convert}
		</div>
	</div>
</section>