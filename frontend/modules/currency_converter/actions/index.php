<?php
/**
 * This is the overview-action
 *
 * @author Frederick Roegiers <frederick.roegiers@wijs.be>
 */
class FrontendCurrencyConverterIndex extends FrontendBaseBlock
{
    /**
     * Name of the cachefile
     *
     * @var	string
     */
    private $cacheFile;
    
    /**
     * Name of the sourcefile
     *
     * @var	string
     */
    private $sourceFile;
    
    /**
     *
     * @var	FrontendForm
     */
    private $frm;
    
    /**
     *
     * @var string
     */
    private $term = 'currencyCache';
    
    /**
     *
     * @var array
     */    
    protected $currencies = array();
    
    
    
    public function execute()
    {
            parent::execute();

            $this->loadTemplate();
            $this->createForm();
            $this->validateForm();
            $this->display();
    }
    
    private function display()
    {
        $this->cacheFile = FRONTEND_CACHE_PATH . '/' . $this->getModule() . '/' . md5($this->term) . '.php';
        $this->sourceFile = FRONTEND_MODULES_PATH . '/' . $this->getModule() . '/sourcefile/source.php';

        // load the cached data
        $this->runCachedData();

        // parse
        $this->parse();
    }
    
    
    /**
     * Check if cached data exists, and run the cached data
     */
    private function runCachedData()
    {
        // Open the file to get existing content
        $sourceFile = file_get_contents($this->sourceFile);
            
        // check if cachefile exists
        if(!SpoonFile::exists($this->cacheFile))
        {
            // set cache content
            SpoonFile::setContent($this->cacheFile, $sourceFile);
        }
        
        // get cachefile modification time
        $cacheInfo = @filemtime($this->cacheFile);

        // check if cache file is recent enough (1 day)
        if($cacheInfo < strtotime('-1 day'))
        {
            // change modification date in current date
            touch($this->cacheFile);
            
            // include cache file
            require_once $this->cacheFile;
            
            // error handling
            if(isset ($xmlError))
            {
                $this->handleXmlLoadError($xmlError);
            }
        }
    }
    
    /*
     *  Create the form and append the input box and dropdownlist
     */
    private function createForm()
    {
        $this->frm = new FrontendForm('index', null, null, 'indexForm');
        $this->frm->addText('amount');
        $this->frm->addDropdown('currencyTarget', $this->getData());
    }
    
    
    /**
     * Get the data from the model, and put it in an array
     * 
     * @return Array 
     */
    private function getData()
    {
        $this->currencies = FrontendCurrencyConverterModel::getCurrencies();
        $currencyArray = array();
        foreach ($this->currencies as $currency)
        {
            $currencyArray[$currency['rate']] = $currency['currency'];
        }
        return $currencyArray;
    }
    
    
    /**
     * Validate the form.
     */
    private function validateForm()
    {
        // submitted
        if($this->frm->isSubmitted())
        {
            // amount is required
            if($this->frm->getField('amount')->isFilled('Please fill in the amount'))
            {
                $this->frm->getField('amount')->isFloat('Only decimal values please!');
            }
            
            
            if($this->frm->isCorrect())
            {
                // all the information that was submitted
                $data = $this->frm->getValues();

                $amount = $data['amount'];
                $rate = $data['currencyTarget'];
                
                $converted = $amount * $rate;
                
                $this->tpl->assign('convertIsSuccess', true);
                $this->tpl->assign('convertSucces', $converted);
            }
        }
    }
    
    /**
     * Parse the data into the template
     */
    private function parse()
    {
        // parse form
        $this->frm->parse($this->tpl);
    }
    
    
    /**
     * function to handle an error message in case of xml-read error
     * 
     * @param type $errorMessage 
     */
    private function handleXmlLoadError($errorMessage)
    {
        $this->tpl->assign('xmlErrorOption', true);
        $this->tpl->assign('xmlError', $errorMessage);
    }


	
}
