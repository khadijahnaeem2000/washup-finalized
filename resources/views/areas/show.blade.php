@extends('layouts.master')
@section('title','Areas')
@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=drawing"></script>
    <style type="text/css">
        #map_canvas {
            width: 100%;
            height: 500px;
        }
        #current {
            padding-top: 25px;
        }
        #map {
            height: 500px;
        }
    </style>
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Show @yield('title')</h3>
                    </div>
                    <div class="card-toolbar">
                        <a  href="{{ route('areas.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td width="20%">@yield('title') Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Poly Points </td>
                                        <td>
                                            <?php 
                                                $poly_points = ((json_decode($data->poly_points, true)));
                                                foreach($poly_points as $key =>$value)
                                                    echo "Lat: ".$value['lat']." lng: ".$value['lng']." <br>";
                                            ?>
                                        </td>
                                    </tr>

                                    {!! Form::hidden('lat',$poly_points[0]['lat'], array('id'=>'lat')) !!}
                                    {!! Form::hidden('lng',$poly_points[0]['lng'], array('id'=>'lng')) !!}
                                    
                                    {!! Form::hidden('poly_points',$data->poly_points, array('class' => 'form-control','id'=>'poly_points','readonly'=>'true')) !!}
                                   
                                </table><br><br>
                                @foreach($all_points as $key =>$value)
                                    <!-- <label class="badge badge-success">{{ $value->name }}</label><br> -->
                                    {!! Form::hidden('all_points[]',$value->poly_points, array('class' => 'form-control','id'=>'all_points['.$key.']','readonly'=>'true')) !!}
                                @endforeach
                    
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3">
                    <div class="card-title">
                        <h3 class="card-label">Location</h3>
                    </div>
                    <div class="card-toolbar">
                       
                    </div>
                </div>
              

                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                        
                                <div id="map"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function(){
            var p_point         = 0;
            var all_points      = $("input[name='all_points\\[\\]']")
                                    .map(function(){
                                        return $(this).val();
                                    }).get();
                                    // console.log(all_points[0]);

            var tmp_lat, tmp_lng;
            var poly_points     = document.getElementById('poly_points').value;  
            var pPoints         = JSON.parse(poly_points);
            tmp_lat             = pPoints[0]['lat'];
            tmp_lng             = pPoints[0]['lng'];
            initEditMap() ; 
            function initEditMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: tmp_lat, lng: tmp_lng },
                    zoom: 14,
                });
                for (x in all_points) {
                    p_points = document.getElementById('all_points['+x+']').value;
                    // color = "#"+((1<<24)*Math.random()|0).toString(16);
                    color = "#000CAD";
                    // console.log(p_points);
                    // Construct the polygon.
                    new google.maps.Polygon({
                        map,
                        paths: $.parseJSON(p_points),
                        strokeColor: color,
                        strokeOpacity: 0.8,
                        strokeWeight: 2,
                        editable: false,
                        fillColor: color,
                        fillOpacity: 0.35,
                        
                    });

                }
            
                
                // Construct the polygon.
                drawingManager = new google.maps.Polygon({
                    paths: $.parseJSON(poly_points),
                    strokeColor: "#FF0000",
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    editable: false,
                    fillColor: "#FF0000",
                    fillOpacity: 0.35,
                });

                // Set polylines
                drawingManager.setMap(map);
                
                // Poly complete event
                google.maps.event.addListener(drawingManager, 'polygoncomplete', function(polygon) {
                    const coords = polygon.getPath().getArray().map(coord => {
                        return {
                            lat: coord.lat(),
                            lng: coord.lng()
                        }
                    });
                    var poly_points = JSON.stringify(coords, null, 1);
                    $('#poly_points').val(poly_points);
                });
            }
        });
    </script>
@endsection
