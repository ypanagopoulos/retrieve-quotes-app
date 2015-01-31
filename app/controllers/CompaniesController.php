<?php

class CompaniesController extends BaseController 
{
    var $symbol;
    var $startDate;
    var $endDate;
    var $email;
    var $error;
    var $data;
    
    public function __construct() {
        $this->error='';
        $this->data=array();  
    }
    
    /**
     * Display the form
     * @return type
     */
    public function showForm(){
        return View::make('companySearch');
    }
    
    /**
     * Process form 
     * @return type
     */
    public function processForm(){
        $this->symbol=Input::get('symbol');
        $this->startDate=Input::get('startDate');
        $this->endDate=Input::get('endDate');
        $this->email=Input::get('email');        
        
        /**
         * validate input
         */
        
        //validate symbol
        if(!empty($this->symbol)){
            $len=strlen($this->symbol);
            if ($len<1 || $len>6){
                $this->error='Invalid Symbol';
            }
        }
        else{
            $this->error='Symbol is empty';
        }
        
        //validate startDate
        if(!empty($this->startDate)){
            $date_parts  = explode('/', $this->startDate);
            if (count($date_parts) == 3) {
                if (!checkdate($date_parts[1], $date_parts[2], $date_parts[0])){
                    $this->error='Invalid Start Date';
                }
                else{
                    $this->startMonth=$date_parts[1];
                    $this->startDay=$date_parts[2];
                    $this->startYear=$date_parts[0];                    
                }
            } else {
                $this->error='Invalid Start Date';
            }
        }
        else{
            $this->error='Start date is empty';
        }
        
        //validate endDate
        if(!empty($this->endDate)){
            $date_parts  = explode('/', $this->endDate);
            if (count($date_parts) == 3) {
                if (!checkdate($date_parts[1], $date_parts[2], $date_parts[0])){
                    $this->error='Invalid End Date';
                }
                else{
                    $this->endMonth=$date_parts[1];
                    $this->endDay=$date_parts[2];
                    $this->endYear=$date_parts[0];                     
                }
            } else {
                $this->error='Invalid End Date';
            }
        }
        else{
            $this->error='End date is empty';
        }
        
        //check if start date is earlier than end date
        if (strtotime($this->endDate)<=strtotime($this->startDate)){
            $this->error='End date should be greater than start date';
        }
        //check if start date is in the future
        if (strtotime($this->startDate)>=time()){
            $this->error='Start date should not be in the future';
        }
        //check if end date is in the future
        if (strtotime($this->endDate)>=time()){
            $this->error='End date should not be in the future';
        }
        
        //validate email
        if(!empty($this->email)){
            if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
                $this->error='Invalid email';
            } 
        }
        else{
            $this->error='Email is empty';
        }   
        
        //error found - return it
        if (!empty($this->error)){
            $this->data['error']=$this->error;
            $this->data['symbol']=$this->symbol;
            $this->data['startDate']=$this->startDate;
            $this->data['endDate']=$this->endDate;
            $this->data['email']=$this->email;
            return View::make('companySearch',$this->data);            
        }
        //no validation errors found, proceed with retrieving quotes
        else{                        
            $yf_object=new yahoofinance();
            $result=$yf_object->getHistoricalQuote(
                    $this->symbol, 
                    $this->startMonth, 
                    $this->startDay, 
                    $this->startYear, 
                    $this->endMonth, 
                    $this->endDay, 
                    $this->endYear);
                                                    
            if (!empty($result[0][0])){
            //results found  
                
                $this->data['quote']=$result;  
                $this->data['symbol']=$this->symbol;
                $this->data['startDate']=$this->startDate;
                $this->data['endDate']=$this->endDate;  
                $this->data['email']=$this->email;
                
                //gather items needed for the chart
                $this->data['labels']='';
                $this->data['open']='';
                $this->data['close']='';
                
                //limit how many points will be drawn
                $maximumpoints=10;
                $interval=1;
                if (sizeof($result)>$maximumpoints){
                    $interval=sizeof($result)/$maximumpoints;
                }     
                //reverse loop so that we start from early dates
                //$i>1 because first line has headers
                for($i=sizeof($result)-1;$i>1;$i=$i-$interval){                      
                    $row=$result[$i];
                    $this->data['labels'].="'".$row[0]."',";
                    $this->data['open'].=$row[1].',';
                    $this->data['close'].=$row[4].',';                                          
                }
                  
                $this->data['labels']=rtrim($this->data['labels'], ',');
                $this->data['open']=rtrim($this->data['open'], ',');
                $this->data['close']=rtrim($this->data['close'], ',');
                
                
                /**
                 * Get company name from REDIS
                 */
                $redis = Redis::connection();     
                
                //symbol is stored in uppercase
                $this->symbol=strtoupper($this->symbol);
                
                $this->companyName = $redis->get($this->symbol);
                
                if (empty($this->companyName)){
                    //could not find company name in redis, 
                    //sync companies
                    companies::syncCompanyNames();
                    
                    //try again
                    $this->companyName = $redis->get($this->symbol);
                    
                    //still cannot find company name. Revert to company symbol
                    if (empty($this->companyName)){
                        $this->companyName = $this->symbol;
                        Log::warning('could not find company name for '.$this->symbol);
                    }
                }
                $this->data['companyName']=$this->companyName;

                //send email
                Mail::send('emails.companySearchResults', array(
                    'startDate' => $this->startDate,
                    'endDate' => $this->endDate), function($message)
                {        
                    $filename=storage_path().'/cache/quotes/'.$this->symbol.$this->startMonth.$this->startDay.$this->startYear.$this->endMonth.$this->endDay.$this->endYear.'.csv';                                
                    $mime='application/csv';
                    $message->to($this->email)->subject($this->companyName);
                    $message->attach($filename, array('mime' => $mime));                    
                });      
                
                //display data on results page
                return View::make('companySearchResults',$this->data);                  
            }            
            //no results found - return error
            else{
                $this->data['error']='No results found for your query. '
                        . 'Please try again with new filters';
                $this->data['symbol']=$this->symbol;
                $this->data['startDate']=$this->startDate;
                $this->data['endDate']=$this->endDate;
                $this->data['email']=$this->email;
                return View::make('companySearch',$this->data);                 
            }                      
        }        
    }

}