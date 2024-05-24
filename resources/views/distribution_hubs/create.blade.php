@extends('layouts.master')
@section('title','Distribution Hubs')
@section('content')
    @include( '../sweet_script')
    @include( '../map_script')
    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add @yield('title')</h3>

                    <div class="card-toolbar">
                        <a href="{{ route('distribution_hubs.index') }}" class="btn btn-primary btn-sm ">
                            <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' =>
                'distribution_hubs.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                {{ Form::hidden('created_by', Auth::user()->id ) }}
                <div class="card-body">
                    <div class="form-group row">
                        <div class="col-lg-10 col-md-10 col-sm-12 col-xs-12">
                            {!! Html::decode(Form::label('name','Distribution Hub Name <span class="text-danger">*</span>'))!!}
                            {{ Form::text('name', null, array('placeholder' => 'Enter distribution hub name','class' => 'form-control','autofocus' => '','required'=>'true'  )) }}
                            @if($errors->has('name'))
                                {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!}
                            @endif
                        </div>
                        <div class="col-lg-2 col-md-2 col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Html::decode(Form::label('cus_address','Customer Address (App) <span class="text-danger">*</span>')) !!}
                                <span class="switch switch-outline switch-icon switch-primary">
                                    <label>
                                        {!! Form::checkbox('cus_address',1,true,  array('class' => 'form-control')) !!}
                                        <span></span>
                                    </label>
                                </span>
                            
                                @if ($errors->has('cus_address'))  
                                    {!! "<span class='span_danger'>". $errors->first('cus_address')."</span>"!!} 
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="row">
                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Html::decode(Form::label('zone[]','Zone <span class="text-danger">*</span>')) !!}
                                {!! Form::select('zone[]', $zones,null, array('class' => 'form-control','multiple' ,'required'=>'true' )) !!}
                                @if($errors->has('zone'))
                                    {!! "<span class='span_danger'>". $errors->first('zone')."</span>"!!}
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Html::decode(Form::label('day[]','Working Days <span class="text-danger">*</span>')) !!}
                                {!! Form::select('day[]', $days,null, array('class' => 'form-control','multiple','required'=>'true' )) !!}
                                @if($errors->has('day'))
                                    {!! "<span class='span_danger'>". $errors->first('day')."</span>"!!}
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Html::decode(Form::label('rider[]','Riders <span class="text-danger">*</span>')) !!}
                                {!! Form::select('rider[]', $riders,null, array('class' => 'form-control','multiple','required'=>'true' )) !!}
                                @if($errors->has('rider'))
                                    {!! "<span class='span_danger'>". $errors->first('rider')."</span>"!!}
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Html::decode(Form::label('user[]','Users <span class="text-danger">*</span>')) !!}
                                {!! Form::select('user[]', $users,null, array('class' => 'form-control','multiple','required'=>'true' )) !!}
                                @if($errors->has('user'))
                                    {!! "<span class='span_danger'>". $errors->first('user')."</span>"!!}
                                @endif
                            </div>
                        </div>
                    </div>

                    {!! Html::decode(Form::label('address','Enter Address <span class="text-danger">*</span>')) !!}
                    <div class="form-group row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            {{ Form::text('address', null, array('placeholder' => 'Enter area name','class' => 'form-control','id'=>'address' ,'required'=>'true' )) }}
                            @if($errors->has('address'))
                                {!! "<span class='span_danger'>". $errors->first('address')."</span>"!!}
                            @endif

                            {{ Form::hidden('radius', 0, array('class' => 'form-control','id'=>'radius','onchange'=>'radius_change()')) }}
                            {!! Form::hidden('lat',null, array('class' =>'form-control','id'=>'lat','readonly'=>'true')) !!}
                            {!! Form::hidden('lng',null, array('class' =>'form-control','id'=>'lng','readonly'=>'true')) !!}
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            <div id='map_canvas'></div>
                            <div id="current"></div>
                        </div>
                    </div>
                
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                            @if($errors->has('time_slot'))
                                {!! "<span class='span_danger'>". $errors->first('time_slot')."</span>"!!}
                            @endif
                            <div class="table-responsive">
                                
                                <table id="myTable" class="table">
                                    <thead>
                                        <tr>
                                            <th>Slot</th>
                                            <th>Nos: of Locations</th>
                                            <th width="5%"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <tbody>
                                </table>
                            </div>
                            <table id="" class="table">
                                <tbody>
                                    <tr>
                                        <td colspan="2" style="text-align:right"> Add New Row</td>
                                        <td width="5%">
                                            <input class="btn btn-success btn-sm" type="button" onclick="add_time_slot_row();" value="+">
                                        </td>
                                    </tr>
                                <tbody>
                            </table>
                        </div>
                    </div>
                </div>


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

    <!-- time_slot table -->
    <script type="text/javascript">
        function add_time_slot_row() {
            $rowno = $("#myTable tr").length;
            $rowno = $rowno + 1;
            var ids = $("select[name='time_slot\\[\\]']")
                .map(function () {
                    return $(this).val();
                }).get();

            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_timeslot') }}",
                method: 'POST',
                data: {
                    ids: ids,
                    _token: token
                },
                success: function (data) {
                    if (data.data) {
                        var timeslot_row = data.data;
                        $("#myTable tr:last").after("<tr id='row" + $rowno + "'>" +
                            timeslot_row +
                            "<td> " +
                            '{!! Form::number("location[]", 1, array("placeholder" => "No: of Locations","class" => "form-control","min"=>"1")) !!}' +
                            "</td>" +
                            "<td  width='40px'>" +
                            "<input class='btn btn-danger btn-sm' type='button' value='-' onclick=delete_time_slot_row('row" +
                            $rowno + "')>" +
                            "</td>" +
                            "</tr>");

                    } else {
                        alert("No more timeslot found available!");
                    }
                }
            });
        }

        function delete_time_slot_row(rowno) {
            $('#' + rowno).remove();
        }

    </script>

    <!-- Google Map Location -->
    <script type="text/javascript">
        $( '#address' ).on( 'keypress', function( e ) {
            if( e.keyCode === 13 ) {
                e.preventDefault();
            }
        });
        // LOCATION IN LATITUDE AND LONGITUDE.
        var lat = 24.8429252;
        var lng = 67.06582089999999;

        var radius = 0;
        document.getElementById('lat').value = lat;
        document.getElementById('lng').value = lng;
        document.getElementById('radius').value = radius;

        document.getElementById('map_canvas')
        var center = new google.maps.LatLng(lat, lng);





        const geocoder = new google.maps.Geocoder();
        var map = new google.maps.Map(document.getElementById('map_canvas'), {
            zoom: 14,
            center: center,
            mapTypeId: google.maps.MapTypeId.ROADMAP
        });

        var marker = new google.maps.Marker({
            position: center,
            draggable: true
        });

        var circle = new google.maps.Circle({
            center: center,
            map: map,
            radius: radius, // IN METERS.
            strokeOpacity: 0.8,
            fillColor: '#FF6600',
            fillOpacity: 0.3,
            strokeColor: "#FFF",
            strokeWeight: 0 // DON'T SHOW CIRCLE BORDER.
        });

        function initialize(lat, lng, radius) {

            circle = new google.maps.Circle({
                center: new google.maps.LatLng(lat, lng),
                map: map,
                radius: radius, // IN METERS.
                fillColor: '#FF6600',
                fillOpacity: 0.3,
                strokeColor: "#FFF",
                strokeWeight: 0 // DON'T SHOW CIRCLE BORDER.
            });
        }

        var input = document.getElementById('address');
        // map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);  
        var searchBox = new google.maps.places.SearchBox(input);

        google.maps.event.addListener(searchBox, 'places_changed', function () {
            var places = searchBox.getPlaces();
            place = places[0];
            var posit = place.geometry.location;
            var lat = posit.lat();
            var lng = posit.lng();

            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            marker.setMap(null);

            marker.position = new google.maps.LatLng(lat, lng);
            map.setZoom(16);
            map.setCenter(marker.position);
            marker.setMap(map);


            /// Start Extract Address from the LAT & LNG ///
            const latlng = {
                lat: parseFloat(lat),
                lng: parseFloat(lng),
            };

            geocoder.geocode({
                location: latlng
            }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        // console.log(results[0].formatted_address);
                        document.getElementById('address').value = results[0].formatted_address;
                    } else {
                        window.alert("No results found");
                    }
                } else {
                    window.alert("Geocoder failed due to: " + status);
                }
            });
            /// End Extract Address from the LAT & LNG ///

        });

        google.maps.event.addListener(marker, 'dragend', function (evt) {
            document.getElementById('current').innerHTML = '<p>Marker dropped: Current Lat: ' + evt.latLng.lat() + ' Current Lng: ' + evt.latLng.lng() + '</p>';
            var lat = evt.latLng.lat();
            var lng = evt.latLng.lng();
            document.getElementById('lat').value = lat;
            document.getElementById('lng').value = lng;
            radius = parseInt(document.getElementById('radius').value);
            circle.setMap(null);
            initialize(lat, lng, radius);

            /// Start Extract Address from the LAT & LNG ///
            const latlng = {
                lat: parseFloat(lat),
                lng: parseFloat(lng),
            };

            geocoder.geocode({
                location: latlng
            }, (results, status) => {
                if (status === "OK") {
                    if (results[0]) {
                        // console.log(results[0].formatted_address);
                        document.getElementById('address').value = results[0].formatted_address;
                    } else {
                        window.alert("No results found");
                    }
                } else {
                    window.alert("Geocoder failed due to: " + status);
                }
            });
            /// End Extract Address from the LAT & LNG ///

        });

        google.maps.event.addListener(marker, 'dragstart', function (evt) {
            document.getElementById('current').innerHTML = '<p>Currently dragging marker...</p>';
        });

        map.setCenter(marker.position);
        marker.setMap(map);

    </script>
@endsection
