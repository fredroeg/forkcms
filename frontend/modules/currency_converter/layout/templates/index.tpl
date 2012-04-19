<section id="currencyConvert" class="mod">
	<div class="inner">
		<div class="bd">
                    {form:index}
                            <p{option:txtAmountError} class="errorArea"{/option:txtAmountError}>
                                    <label for="amount">{$lblAmount|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$txtAmount} {$txtAmountError}
                            </p>
                            <p {option:txtCurrencySourceError} class="errorArea"{/option:txtCurrencySourceError}>
                                    <label for="currencySource">{$lblCurrencySource|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                                    {$ddmCurrencySource} {$ddmCurrencySourceError}
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
		</div>
	</div>
</section>

<script>
    data = {
        value: {$val}
    }
</script>

<!-- HighRoller: linechart div container -->
<div id="linechart" style="height: 400px; width: 600px;"></div>