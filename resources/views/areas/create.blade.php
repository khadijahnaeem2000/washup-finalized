@extends('layouts.master')
@section('title','Area')
@section('content')
    @include( '../sweet_script')
    <script src="https://maps.googleapis.com/maps/api/js?key={{env('MAP_API_KEY')}}&libraries=places,drawing"></script>  
    
    <style type="text/css">
        #map {
            height: 500px;
            /* height: 100%; */
        }
    </style>


    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    @if ($errors->has('latitude'))  
                        {!! "<span class='span_danger'>". $errors->first('latitude')."</span>"!!} 
                    @endif
                  
                    <div class="card-toolbar">
                        <div class="btn-group btn-group">
                            <a  href="{{ route('areas.index') }}" class="btn btn-primary btn-sm ">
                                <i class="fas fa-arrow-left"></i>
                            </a>

                            <a  href="{{ route('areas.create') }}"  class="btn btn-danger btn-sm">
                                <i class="fas fa-eraser"></i>Clear polygon
                            </a>
                            <!-- <button  id="delete-button" class="btn btn-danger btn-sm">
                                <i class="fas fa-eraser"></i>Clear polygon
                            </button> -->
                        </div>
                    </div>
                </div>

                <!--begin::Form-->
                {!! Form::open(array('route' => 'areas.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}
                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                              {!! Html::decode(Form::label('name','Area Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'Enter area name','class' => 'form-control','autofocus' => '','id'=>'name','required'=>'true' )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif
                            </div>
                        </div>

                        
                        <div class="form-group row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                {!! Html::decode(Form::label('address_name','Search Address ')) !!}
                                {{ Form::text('address_name', null, array('placeholder' => 'Enter area name','class' => 'form-control','id'=>'address_name' )) }}
                                @if ($errors->has('address_name'))  
                                    {!! "<span class='span_danger'>". $errors->first('address_name')."</span>"!!} 
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div id="map"></div>
                            </div>
                        </div>
                        <br>
                        {!! Form::text('poly_points',null, array('class' => 'form-control','id'=>'poly_points','readonly'=>'true')) !!}
                        {!! Form::hidden('center_points',null, array('class' => 'form-control','id'=>'center_points','readonly'=>'true')) !!}
                    </div>

                    @foreach($all_points as $key =>$value)
                        <!-- <label class="badge badge-success">{{ $value->name }}</label><br> -->
                        {!! Form::hidden('all_points[]',$value->poly_points, array('class' => 'form-control','id'=>'all_points['.$key.']','readonly'=>'true')) !!}
                    @endforeach
                    

                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>
   
    <script type="text/javascript">
        $(document).ready(function(){
            var p_point = 0;
            var all_points     = $("input[name='all_points\\[\\]']")
                                    .map(function(){
                                        return $(this).val();
                                    }).get();
                                    // console.log(all_points[0]);



            $( '#address_name' ).on( 'keypress', function( e ) {
                if( e.keyCode === 13 ) {
                    e.preventDefault();
                }
            });
            var drawingManager  = "";
            function deleteSelectedShape() {
                if (drawingManager) {
                    drawingManager.setMap(null);
                    $('#poly_points').val('');
                    initMap() ; 
                }
            }

            initMap(); 
            function initMap() {
                const map = new google.maps.Map(document.getElementById("map"), {
                    center: { lat: 24.8429252, lng: 67.06582089999999 },
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
                    

                const input     = document.getElementById("address_name");
                const searchBox = new google.maps.places.SearchBox(input);
                // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
              
                google.maps.event.addListener(searchBox, 'places_changed', function() {  
                    var places  = searchBox.getPlaces();  
                    place       = places[0];
                    var posit   = place.geometry.location;
                
                    drawingManager.setMap(null);

                    drawingManager.position = new google.maps.LatLng(posit.lat(), posit.lng());
                    map.setCenter(drawingManager.position);
                    drawingManager.setMap(map);
                });
              
               
                
                //  Draw Polylines
                drawingManager = new google.maps.drawing.DrawingManager({
                    drawingMode: google.maps.drawing.OverlayType.POLYGON,
                    drawingControl: true,
                    drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: [ google.maps.drawing.OverlayType.POLYGON,],
                    },
                    
                    circleOptions: {
                        fillColor: "#ffff00",
                        fillOpacity: 1,
                        strokeWeight: 5,
                        clickable: false,
                        editable: true,
                        zIndex: 1,
                    },
                });

                // Set polylines
                drawingManager.setMap(map);
            }
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
                var pPoints = JSON.parse(poly_points);
                var bounds = new google.maps.LatLngBounds();
                var i;
                
                var arr = [];
                for(var i=0;i<pPoints.length;i++){
                    arr.push(new google.maps.LatLng(pPoints[i]['lat'], pPoints[i]['lng']));
                }

                // The Bermuda Triangle
                var polygonCoords =arr;

                for (i = 0; i < polygonCoords.length; i++) {
                    bounds.extend(polygonCoords[i]);
                }

                $('#center_points').val(JSON.stringify(bounds.getCenter()));
            });
            // google.maps.event.addDomListener(document.getElementById('delete-button'), 'click', deleteSelectedShape);
        });
    </script>
@endsection

