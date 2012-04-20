<section id="graph" class="mod">
    <div class="inner">
        {form:graph}
            <p {option:txtCurrency} class="errorArea"{/option:txtCurrencyError}>
                <label for="currency">{$lblCurrency|ucfirst}<abbr title="{$lblRequiredField}">*</abbr></label>
                {$ddmCurrency} {$ddmCurrencyError}
            </p>
            <p>
                <input id="viewGraphBtn" class="viewGraphSubmit" type="submit" name="viewGraphBtn" value="{$lblViewGraph}" />
            </p>
        {/form:graph}
    </div>
</section>

<script>
    graphDataObj = new Object();
    graphDataObj.graphValues = {$val};
    graphDataObj.graphCurrency = "{$cur}";
</script>

<!-- HighRoller: linechart div container -->
<div id="linechart" style="height: 400px; width: 600px;"></div>