@extends('layouts.master')
@section('title','Zones')
@section('content')

    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=drawing"></script>
    <style type="text/css">
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
                        <a  href="{{ route('zones.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12 col-md-12">
                            <div class="table-responsive">
                                <table class="table dt-responsive">
                                    <tr>
                                        <td>@yield('title') Name</td>
                                        <td>{{$data->name}}</td>
                                    </tr>
                                    <tr>
                                        <td>Areas</td>
                                        <td>
                                            @foreach($selected_areas as $key =>$value)
                                                <label class="badge badge-success">{{ $value->name }}</label><br>
                                                {!! Form::hidden('poly_points[]',$value->poly_points, array('class' => 'form-control','id'=>'poly_points['.$key.']','readonly'=>'true')) !!}
                                            @endforeach
                                        </td>
                                    </tr>
                                </table>
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
            var tmp_lat, tmp_lng;
            var p_point         = 0;
            var poly_points     = $("input[name='poly_points\\[\\]']")
                                    .map(function(){
                                        return $(this).val();
                                    }).get();
            // console.log(poly_points[0]);
            var first_point     = document.getElementById('poly_points[0]').value;
            var fPoint          = JSON.parse(first_point);
            tmp_lat             = fPoint[0]['lat'];
            tmp_lng             = fPoint[0]['lng'];


            if (poly_points[0]!=0){
                initEditMap() ; 
                function initEditMap() {
                    const map = new google.maps.Map(document.getElementById("map"), {
                        center: { lat: tmp_lat, lng: tmp_lng },
                        zoom: 14,
                    });

                    for (x in poly_points) {
                        p_points = document.getElementById('poly_points['+x+']').value;
                        color = "#"+((1<<24)*Math.random()|0).toString(16);
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
                }
            }else{
                alert("areas have no polypoints");
            }
        });
    </script>
@endsection
