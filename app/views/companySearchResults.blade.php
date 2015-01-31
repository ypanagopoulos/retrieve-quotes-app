<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Company</title>
    <!-- CSS code-->
    {{HTML::style('bootstrap/css/bootstrap.min.css')}}
    {{HTML::style('bootstrap/css/bootstrap-theme.css')}}
    {{HTML::style('jquery-ui/jquery-ui.min.css')}}      
    {{HTML::style('datatables/css/jquery.dataTables.min.css')}}      
</head>
<body>
    
    <div style="margin: 20px;">       
        <div class="page-header">
            <h1>- <strong>{{{$companyName}}}</strong> - historical quotes <small>from {{{$startDate}}} to {{{$endDate}}}</small></h1>
            <a href="">New search</a>
        </div>
        
     <!-- display chart -->     
     <div style="margin: 5%;">       
        <canvas id="myChart" style="width: 1200px; height: 400px;" width="1200" height="400"></canvas>
     </div>
      <!-- display table data -->
      <div class="table-responsive">          
            <table class="table" id="quotesTable">                   
              @foreach ($quote as $index =>$row)
                  @if ($index == 0)
                    <thead>
                          <tr>
                            <th> {{{ $row[0] }}}</th>
                            <th> {{{ $row[1] }}}</th>
                            <th> {{{ $row[2] }}}</th>
                            <th> {{{ $row[3] }}}</th>
                            <th> {{{ $row[4] }}}</th>
                            <th> {{{ $row[5] }}}</th>
                            <th> {{{ $row[6] }}}</th>
                          </tr>
                    </thead>
                    <tbody>            
                  @else
                    <tr>
                        <td>{{{ $row[0] }}}</td>
                        <td>{{{ $row[1] }}}</td>
                        <td>{{{ $row[2] }}}</td>
                        <td>{{{ $row[3] }}}</td>
                        <td>{{{ $row[4] }}}</td>
                        <td>{{{ $row[5] }}}</td>
                        <td>{{{ $row[6] }}}</td>
                  </tr> 
                  @endif
              @endforeach
                    </tbody>
            </table>
      </div>
      

  
        
    </div>

    <!-- JS code-->
    {{ HTML::script('jquery/jquery.min.js') }}
    {{ HTML::script('bootstrap/js/bootstrap.min.js') }}
    {{ HTML::script('jquery-ui/jquery-ui.min.js') }}
    {{ HTML::script('chartjs/Chart.min.js') }}
    {{ HTML::script('datatables/js/jquery.dataTables.min.js') }}
    
    <!-- draw chart-->
    <script>   
        $('#quotesTable').DataTable();
        var ctx = $("#myChart").get(0).getContext("2d");
        Chart.defaults.global.responsive = true;
        var data = {
            labels: [{{$labels}}],
            datasets: [
                {
                    label: "Open price",
                    fillColor: "rgba(220,220,220,0.2)",
                    strokeColor: "rgba(220,220,220,1)",
                    pointColor: "rgba(220,220,220,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(220,220,220,1)",
                    data: [{{$open}}]
                },
                {
                    label: "Close price",
                    fillColor: "rgba(151,187,205,0.2)",
                    strokeColor: "rgba(151,187,205,1)",
                    pointColor: "rgba(151,187,205,1)",
                    pointStrokeColor: "#fff",
                    pointHighlightFill: "#fff",
                    pointHighlightStroke: "rgba(151,187,205,1)",
                    data: [{{$close}}]
                }
            ]
        };
        var myLineChart = new Chart(ctx).Line(data, {
            responsive: true,
        });        
    </script>

</body>
</html>