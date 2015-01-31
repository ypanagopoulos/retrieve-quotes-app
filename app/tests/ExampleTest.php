<?php

class ExampleTest extends TestCase {

    /**
     * Test that page runs ok
     *
     * @return void
     */
    public function testHomepageUp()
    {
        $crawler = $this->client->request('GET', '/');
        $this->assertTrue($this->client->getResponse()->isOk());
    }

    /**
     * Check the response after submitting the form with correct data
     */
    public function testSubmitCorrectResults()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/12/20',
                    'endDate' => '2015/01/01',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check that symbol is returned correctly
        $this->assertEquals('goog', $view['symbol']);
        
        //check that the correct company name is retrieved from redis
        $this->assertEquals('Google Inc.', $view['companyName']);       
        
        //check that the date labels are retrieved correctly from the yahoo API
        $this->assertEquals("'2014-12-22','2014-12-23','2014-12-24','2014-12-26','2014-12-29','2014-12-30'",$view['labels']);
        
        //check that open values are retrieved correctly from yahoo API
        $this->assertEquals('516.08,527.00,530.51,528.77,532.19,528.09', $view['open']);        

    }
    
    /**
     * Test the response after submitting the form with invalid symbol
     */
    public function testSubmitInvalidSymbol()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'gfdsg',
                    'startDate' => '2014/12/20',
                    'endDate' => '2015/01/01',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('No results found for your query. Please try again with new filters', $view['error']);               

    }
    
    /**
     * Test the response after submitting the form with invalid start date
     */    
    public function testSubmitInvalidStartDate()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/02/31',
                    'endDate' => '2015/01/01',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('Invalid Start Date', $view['error']);               

    }
    
    /**
     * Test the response after submitting the form with invalid end date format
     */    
    public function testSubmitInvalidEndDateFormat()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/12/01',
                    'endDate' => '01/01/2015',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('Invalid End Date', $view['error']);               

    }
    
    /**
     * Test the response after submitting the form with 
     * end date greater than start date
     */    
    public function testSubmitStartDateAfterEndDate()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/12/01',
                    'endDate' => '2014/11/01',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('End date should be greater than start date', $view['error']);               

    }
    
    /**
     * Test the response after submitting the form future end date
     */    
    public function testSubmitFutureDate()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/12/01',
                    'endDate' => '2018/01/01',
                    'email' => 'y.panagopoulos@gmail.com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('End date should not be in the future', $view['error']);               

    }
    
    /**
     * Test the response after submitting the form with invalid email
     */    
    public function testSubmitInvalidEmail()
    {        
        $response = $this->action('POST', 'CompaniesController@processForm',
                array(  
                    'symbol' => 'goog',
                    'startDate' => '2014/12/01',
                    'endDate' => '2015/01/01',
                    'email' => 'ypanagopoulos@gmail-com',
                ));        
                
        $view = $response->original;
        
        // check the error message
        $this->assertEquals('Invalid email', $view['error']);               

    }   
        
}
