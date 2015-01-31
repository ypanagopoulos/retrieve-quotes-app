<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Company</title>
    <!-- CSS code-->
    {{HTML::style('bootstrap/css/bootstrap.min.css')}}
    {{HTML::style('bootstrap/css/bootstrap-theme.css')}}
    {{HTML::style('jquery-ui/jquery-ui.min.css')}}      
</head>
<body>
    <div style="margin: 20px;">
        <div class="page-header">
            <h1>Find Company Historical Quotes</h1>
        </div>
        <form class="form-horizontal" id="searchform" method="post">
            <div class="form-group">
                <label for="symbol" 
                       class="col-sm-2 control-label">
                    Company Symbol
                </label>
                <div class="col-sm-10 controls">
                    <input class="form-control " 
                           type="text" 
                           id="symbol" 
                           name="symbol" 
                           placeholder="Enter Symbol"
                           maxlength="6"
                           minlength="1"
                           style="width:150px;"
                           required="true"
                           value="{{ $symbol or ''}}"
                           >
                </div>
            </div>  
            <div class="form-group controls">                
                <label for="startDate" class="col-sm-2 control-label">
                    Start Date
                </label>
                <div class="col-sm-10 controls">
                    <input class="datepicker form-control startDate" 
                           id="startDate" 
                           name="startDate" 
                           placeholder="Enter Start Date"
                           style="width:150px;"
                           required="true"
                           date="true"
                           noFutureDate="true"
                           value="{{$startDate or ''}}">
                </div>
            </div>
            <div class="form-group">                
                <label for="endDate" class="col-sm-2 control-label">
                    End Date
                </label>
                <div class="col-sm-10 controls">        
                    <input class="datepicker form-control endDate" 
                            id="endDate" 
                            name="endDate" 
                            placeholder="Enter End Date"
                            style="width:150px;" 
                            required="true"
                            date="true"  
                            noFutureDate="true"
                            value="{{$endDate or ''}}">
                </div>
            </div>    
            <div class="form-group">
                <label for="email" 
                       class="col-sm-2 control-label">
                    Email
                </label>
                <div class="col-sm-10 controls">
                    <input type="email" 
                           class="form-control" 
                           id="email" 
                           name="email" 
                           placeholder="Enter your email"
                           maxlength="200"
                           style="width:250px;" 
                           required="true"
                           minlength="5"
                           value="{{$email or ''}}">
                    <p class="help-block"></p>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" class="btn btn-primary">
                      Search
                    </button>
                </div>
            </div>
            @if(isset($error))
                <div class="alert alert-danger alert-dismissible col-sm-5" role="alert">
                  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <strong>{{{$error}}}</strong>  
                </div>            
            @endif            
        </form>           
    </div>
    
    <!-- JS code-->
    {{ HTML::script('jquery/jquery.min.js') }}
    {{ HTML::script('jquery/jquery.validate.min.js') }}
    {{ HTML::script('jquery-ui/jquery-ui.min.js') }}
    {{ HTML::script('bootstrap/js/bootstrap.min.js') }}    
    {{ HTML::script('js/companySearch.js') }}

</body>
</html>