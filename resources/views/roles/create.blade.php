@extends('layouts.master')
@section('title','Roles')
@section('content')
    @include( '../sweet_script')

    <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Add new @yield('title')</h3>
                    <div class="card-toolbar">
                        <button name="checkAll" id="checkAll" class="btn btn-primary mr-2">
                            <i class="fas fa-check"></i>Check / Un-Check All
                        </button>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::open(array('route' => 'roles.store','method'=>'POST','id'=>'form','enctype'=>'multipart/form-data')) !!}
                    {{  Form::hidden('created_by', Auth::user()->id ) }}

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-lg-12">
                              {!! Html::decode(Form::label('name','Role Name <span class="text-danger">*</span>')) !!}
                               {{ Form::text('name', null, array('placeholder' => 'role name','class' => 'form-control','autofocus' => ''  )) }}
                                @if ($errors->has('name'))  
                                    {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                @endif

                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div style="width: 100%; padding-left: -10px; ">
                                    <div class="table-responsive">
                                        <table id="myTable" class="table table-separate table-head-custom dt-responsive " style="width: 100%;" cellspacing="0">
                                            <tr>
                                                <th> <label> Role Name</label></th>
                                                <th>List / Show</th>
                                                <th>Create</th>
                                                <th>Edit</th>
                                                <th>Delete</th>
                                            </tr>
                                            <?php   $i=0;
                                                $val = $permission[0]['name'];
                                                $explodedFirstValue = explode("-", $val);
                                                $firstVal = $explodedFirstValue[0];  // exploded permission name
                                                ?>
                                            <tr>
                                                <td> <label>{{ ucfirst($firstVal)}}</label></td>
                                                <?php
                                                    foreach($permission as $value){
                                                        $currentVal = $value->name;
                                                        $explodedLastValue = explode("-", $currentVal);
                                                        $LastVal = $explodedLastValue[0];
                                                        if( $firstVal == $LastVal){ ?>
                                                            
                                                                    
                                                <td>
                                                    <div class="checkbox-inline">
                                                        <label class="checkbox checkbox-success">
                                                            {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                            <span></span>
                                                        </label>
                                                    </div>
                                                </td>
                                                <?php }else{
                                                        $firstVal = $LastVal;
                                                ?>
                                            </tr>
                                            <tr>
                                                <td> <label>{{ ucfirst($firstVal)}}
                                                    <?php 
                                                        if($firstVal == "special_polybag"){
                                                            $txt = " (print) ";

                                                        }elseif($firstVal == "special_tag"){
                                                            $txt = " (print) ";

                                                        }elseif($firstVal == "special_wash_house"){
                                                            $txt = " (Assign) ";
                                                        }else{
                                                            $txt = "";

                                                        }
                                                    ?> {{$txt }}
                                                    </label></td>
                                                <td>
                                                    <div class="checkbox-inline">
                                                        <label class="checkbox checkbox-success">
                                                            {{ Form::checkbox('permission[]', $value->id, false, array('class' => 'name')) }}
                                                             <span></span>  
                                                        </label>
                                                    </div>
                                                </td>
                                                <?php if ($LastVal == 'profile'){ echo "<td> </td><td> </td><td></td>";}}?>
                                                <?php } ?>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
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

    <script>
        $("#checkAll").click(function(){
            if ($("input[type=checkbox]").prop("checked")) {
                console.log("un-checked");
                $('input:checkbox').prop('checked', false);
            } else { 
                console.log("checked"); 
                $('input:checkbox').prop('checked',true);
            } 
        });
    </script>
@endsection
