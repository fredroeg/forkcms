<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterGraph extends FrontendBaseBlock
{
    public function execute()
    {
        $this->header->addJS('highcharts.js', 'core', false);

        parent::execute();

        $this->loadTemplate();

        $this->display();
    }

    private function display()
    {
        $this->parse();
    }

    private function parse()
    {
        $this->frm->parse($this->tpl);
    }



}
