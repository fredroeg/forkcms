<section id="currencyConvert" class="mod">
	<div class="inner">
		<div class="bd">
				{form:index}
					<p{option:txtAmountError} class="errorArea"{/option:txtAmountError}>
						<label for="amount">{$lblAmount|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$txtAmount} {$txtAmountError}
					</p>
                                        <p {option:txtCurrencyError} class="errorArea"{/option:txtCurrencyError}>
                                                <label for="currency">{$lblCurrency|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
						{$ddmCurrency} {$ddmCurrencyError}
                                        </p>
					<p>
						<input id="convertBtn" class="convertSubmit" type="submit" name="convertBtn" value="{$lblConvert|ucfirst}" />
					</p>
				{/form:index}
		</div>
	</div>
</section>