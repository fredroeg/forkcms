<section id="currencyConvert" class="mod">
	<div class="inner">
		<div class="bd">
			{option:convertHasFormError}<div class="message error"><p>{$errFormError}</p></div>{/option:convertHasFormError}
			{option:convertIsSuccess}<div class="message success"><p>{$msgConvertSuccess}</p></div>{/option:convertIsSuccess}

				{form:convert}
					<p{option:txtAmountError} class="errorArea"{/option:txtAmountError}>
						<label for="amount">{$lblAmount|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtAmount} {$txtAmountError}
					</p>
                                        <p {option:txtCurrencyError} class="errorArea"{/option:txtCurrencyError}>
                                                <label for="currency">{$lblCurrency|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$ddlCurrency} {$ddlCurrencyError}
                                        </p>
					<p>
						<input id="convertBtn" class="convertSubmit" type="submit" name="convertBtn" value="{$lblConvert|ucfirst}" />
					</p>
				{/form:convert}
		</div>
                <div class="test">Frederick Roegiers</div>
	</div>
</section>