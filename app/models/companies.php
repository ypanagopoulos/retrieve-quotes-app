<?php

/**
 * model that handles companies 
 */
class companies extends Eloquent {
    

    /**
     * Sync company names to local redis (from nasdaq api)
     * @return boolean
     */
    static function syncCompanyNames(){
                          
        $url='http://nasdaq.com/screening/companies-by-name.aspx?render=download';
        try{            
            //use curl
            $ch = curl_init();                
            $timeout = 10;
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);            
            $output = curl_exec($ch);
            
            curl_close($ch);  
        }   
        catch (Exception $e) {
            Log::error('error in retrieving data from nasdaq: '.$e);
        }                           
        
        $redis = Redis::connection();    
        //convert data to array
        $separator = "\r\n";
        $line = strtok($output, $separator);
        
        //skip first line with headers
        $line = strtok( $separator );
        
        //go through companies
        while ($line !== false) {
            $row=str_getcsv($line,',','"');     
            //check if it already exists
            if (!$redis->exists($row[0])){
                //if not, set it
                $redis->set($row[0], $row[1]);
            }
            $line = strtok( $separator );
        }  
        Log::info('Companies have been synced');
        unset($output);                
        
        return true;
    }	

}
