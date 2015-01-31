<?php

/**
 * model that retrieves data from yahoo API
 */
class yahoofinance extends Eloquent {

    public function __construct()
    {
        
    }
    
    /**
     * Get historical quote data as array
     * 
     * @param type $symbol
     * @param type $startMonth
     * @param type $startDay
     * @param type $startYear
     * @param type $endMonth
     * @param type $endDay
     * @param type $endYear
     * @return array
     */
    public function getHistoricalQuote( 
            $symbol,
            $startMonth,
            $startDay,
            $startYear,
            $endMonth,
            $endDay,
            $endYear){
        
        $output='';
        $output_array=array();
        if (!empty($symbol) && !empty($startMonth) && !empty($startDay) 
                && !empty($startYear) && !empty($endMonth) 
                && !empty($endDay) && !empty($endYear)) {
           
            $filename=storage_path().'/cache/quotes/'.$symbol.$startMonth.$startDay.$startYear.$endMonth.$endDay.$endYear.'.csv';            
            //check if a cached version already exists
            if (!file_exists ( $filename )){
                 
                $url='http://ichart.yahoo.com/table.csv'
                        .'?s='.$symbol
                        .'&a='.($startMonth-1)
                        .'&b='.$startDay
                        .'&c='.$startYear                
                        .'&d='.($endMonth-1)
                        .'&e='.$endDay
                        .'&f='.$endYear;
                try{                    
                    //use curl
                    $ch = curl_init();                
                    $timeout = 5;
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
                    $output = curl_exec($ch);
                    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                    curl_close($ch);                          

                    if($httpCode != 404) {
                        $fp = fopen($filename,'x');
                        fwrite($fp, $output);
                        fclose($fp); 
                    }         
                    unset($output);                    
                }   
                catch (Exception $e) {
                    Log::warning('error in retrieving quotes using url:'.$url.' '.$e);
                }   
            }
            try{
                //attempt to read data from file
                if (($handle = fopen($filename, "r")) !== FALSE) {
                    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                        $output_array[]=$data;
                    }
                    fclose($handle);
                }
            }
            catch (Exception $e) {
                Log::warning('error in reading file: '.$filename.' '.$e);
            }              
        }
        
        return $output_array;
    }	


}
