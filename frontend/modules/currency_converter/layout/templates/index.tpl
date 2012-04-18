<section id="currencyConvert" class="mod">
	<div class="inner">
		<div class="bd">
                    {form:index}
                            <p{option:txtAmountError} class="errorArea"{/option:txtAmountError}>
                                    <label for="amount">{$lblAmount|ucfirst} (&euro;)<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$txtAmount} {$txtAmountError}
                            </p>
                            <p {option:txtCurrencyError} class="errorArea"{/option:txtCurrencyError}>
                                    <label for="currencyTarget">{$lblCurrencyTarget|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$ddmCurrencyTarget} {$ddmCurrencyTargetError}
                            </p>
                            <p>
                                    <input id="convertBtn" class="convertSubmit" type="submit" name="convertBtn" value="{$lblConvert|ucfirst}" />
                            </p>
                    {/form:index}
                    {option:convertIsSuccess}<div class="message success"><p>{$convertSucces}</p></div>{/option:convertIsSuccess}
                    {option:xmlErrorOption}<div class="message error"><p>{$xmlError}</p></div>{/option:xmlErrorOption}
		</div>
	</div>
</section>