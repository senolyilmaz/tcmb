<?php
    class TCMB {
        private $tcmb_url;
        private $errors;
        private $rates;
        
        public function __construct() {
            $this->tcmb_url = "http://www.tcmb.gov.tr/kurlar/today.xml";
            $this->errors   = [];
            $this->rates    = [];
        }

        /**
         * @param array $symbols Symbols for collecting rates from array
         * @return object Result object will return
         */        
        public function get_rates($symbols=['USD','EUR','GBP','RUB','HKD']){
            $symbol_array = is_array($symbols) ? $symbols : ( strlen($symbols)==0 ? [] : explode(",",$symbols) );
            if($symbol_array){
                $xml_source = file_get_contents($this->tcmb_url);
                if(strlen($xml_source)){
                    $array_values = json_decode(json_encode(simplexml_load_string($xml_source)),true);
                    if($array_values){
                        if($array_values['Currency']){
                            $currencies = $array_values['Currency'];
                            foreach ($symbol_array as $symbol) {
                                $rate = $this->get_rate_row($currencies,$symbol);
                                if($rate->buy>0){
                                    $this->rates[] = $rate;
                                }
                                else{
                                    $this->errors[] = $this->create_error(2,12,"Doviz kuru ( $symbol ) okunmamadi");
                                }
                            }
                        }
                        else{
                            $this->errors[] = $this->create_error(1,12,"Dizi icinde Currency anahtari bulunamadi");
                        }
                    }
                    else{
                        $this->errors[] = $this->create_error(1,12,"XML kaynagi diziye cevrilemedi");
                    }
                }
                else{
                    $this->errors[] = $this->create_error(1,11,"XML kaynagindan veri alinamadi.");
                }
            }
            else{
                $this->errors[] = $this->create_error(1,10,"Sembol bulunamadi");
            }
            return $this->prepare_result();
        } 

        /**
         * @param array $allcur All currency data array
         * @param string $symbol Symbol to be read rate
         * @return object Rate object will return
         */  
        private function get_rate_row($alcur,$symbol){
            $rate       = new stdClass();
            $rate->symbol = $symbol;
            $rate->buy  = 0 ;
            $rate->sell = 0;
            if($alcur){
                foreach ($alcur as $ac) {
                    if($ac['@attributes']['CurrencyCode'] == $symbol){
                        $rate->buy  = !is_array($ac['BanknoteBuying'])?floatval($ac['BanknoteBuying']):floatval($ac['ForexBuying']);
                        $rate->sell = !is_array($ac['BanknoteBuying'])?floatval($ac['BanknoteSelling']):floatval($ac['ForexSelling']);
                    }
                }
            }
            return $rate;
        }

        /**
         * @return object Result object will return
         */  
        private function prepare_result(){
            $ro           = new stdClass();
            $ro->status   = $this->has_critical_errors() ? 500 : ( $this->rates ? 200 : 201 );
            $ro->messages = $this->errors;
            $ro->data     = $this->rates;
            return $ro;
        }

        /**
         * @param integer $type Set error type 1 : Critical 2:Warning
         * @param integer $code Error code number
         * @param string  $desc Error description
         * @return object Error object will return
         */          
        private function create_error($type,$code,$desc){
            $err = new stdClass();
            $err->type = $type; 
            $err->code = $code;
            $err->desc = $desc;
            return $err;
        }

        /**
         * @return boolean True/False will return
         */  
        private function has_critical_errors(){
            if($this->errors){
                foreach ($this->errors as $error) {
                    if($error->type==1)
                        return true;
                }
            }
            return false;
        }
    }
?>
