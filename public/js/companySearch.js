/**
 * CompanySearch js
 */
$(document).ready(function () {  
    
    //setup date picker
    $('.datepicker').datepicker({
        dateFormat: "yy/mm/dd",
        changeMonth: true,
        changeYear: true
    }); 
    
    $.validator.addMethod("noFutureDate",
        function (value, element) { 
            return Date.parse(value.replace("-","/")) < new Date().getTime(); 
        },"Date must not be in the future."
    );    
    
    $.validator.addMethod("endDate", 
        function(value, element) {
            var startDate = $('.startDate').val();
            return Date.parse(startDate) < Date.parse(value) || value == "";
    }, "End date must be after start date");
        
    //validate the form
    $('#searchform').validate({
        
    });
   
});
