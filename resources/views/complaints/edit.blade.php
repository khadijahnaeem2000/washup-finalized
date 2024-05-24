@extends('layouts.master')
@section('title','Complaints')
@section('content')

@include( '../sweet_script')
  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
            <div class="card card-custom gutter-b example example-compact">
                <div class="card-header">
                    <h3 class="card-title">Edit Complaint</h3>
                    <div class="card-toolbar">
                        <a  href="{{ route('complaints.index') }}" class="btn btn-primary btn-sm ">
                        <i class="fas fa-arrow-left"></i></a>
                    </div>
                </div>
                <!--begin::Form-->
                {!! Form::model($data, ['method' => 'PATCH','id'=>'form','enctype'=>'multipart/form-data','route' => ['complaints.update', $data->id]]) !!}
                    {{  Form::hidden('updated_by', Auth::user()->id ) }}

                    <div class="card-body">

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('iron_rating','Iron rating: ')) !!}
                                    {{ Form::text('iron_rating', null, array('class' => 'form-control','readonly' => 'true' )) }}
                                   
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('softner_rating','Softner rating: ')) !!}
                                    {{ Form::text('softner_rating', null, array('class' => 'form-control','readonly' => 'true' )) }}
                                   
                                </div>
                            </div>
                           
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('contact_no','Account/Contact No: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('contact_no', null, array('placeholder' => 'Enter account or contact no','class' => 'form-control' ,'readonly' => 'true' )) }}
                                    @if ($errors->has('contact_no'))  
                                        {!! "<span class='span_danger'>". $errors->first('contact_no')."</span>"!!} 
                                    @endif
                                  
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                {!! Html::decode(Form::label('name','Customer Name <span class="text-danger">*</span>')) !!}
                                {{ Form::text('name', null, array('placeholder' => 'Enter customer name','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('name'))  
                                        {!! "<span class='span_danger'>". $errors->first('name')."</span>"!!} 
                                    @endif
                                {{ Form::hidden('customer_id', null, array('id'=>'customer_id','placeholder' => 'Enter customer id','class' => 'form-control','readonly' => 'true' )) }}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('order_id','Order No: <span class="text-danger">*</span>')) !!}
                                    {{ Form::number('order_id', null, array('placeholder' => 'Enter Order No','class' => 'form-control','readonly' => 'true' )) }}
                                    @if ($errors->has('order_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('order_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('delivery_date','Delivery Date: ')) !!}
                                    {{ Form::date('delivery_date', null, array('placeholder' => 'Enter delivery date','class' => 'form-control','readonly' => 'true')) }}
                                    @if ($errors->has('delivery_date'))  
                                        {!! "<span class='span_danger'>". $errors->first('delivery_date')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                      

                      
                        <div class="row">
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('complaint_nature_id','Complaint Nature: ')) !!}
                                    {!! Form::select('complaint_nature_id', ['' =>'--- Select Nature ---']+$complaint_natures,null, array('class'=> 'form-control','autofocus' => '' ,'onchange'=>'show_tags(this.value)')) !!}
                                    @if ($errors->has('complaint_nature_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('complaint_nature_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                <div class="form-group">
                                    {!! Html::decode(Form::label('complaint_tag_id','Complaint Tag: ')) !!}
                                    {!! Form::select('complaint_tag_id',[''=>'--- Select Tag ---'],$data->complaint_tag_id, array('class'=> 'form-control')) !!}
                                    @if ($errors->has('complaint_tag_id'))  
                                        {!! "<span class='span_danger'>". $errors->first('complaint_tag_id')."</span>"!!} 
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-lg-10">
                                {!! Html::decode(Form::label('complaint_detail','Complaint Detail ')) !!}
                                {!! Form::textarea('complaint_detail', null, array('placeholder' => 'Complaint Detail','rows'=>5, 'class' => 'form-control')) !!}
                                @if ($errors->has('complaint_detail'))  
                                    {!! "<span class='span_danger'>". $errors->first('complaint_detail')."</span>"!!} 
                                @endif
                            </div>
                        
                            <div class="col-lg-2" style="margin-top: 20px">
                                <div class="image-input image-input-outline" id="kt_profile_avatar">
                                
                                    @if($data->image)
                                        <?php $images =  explode('|',$data->image);?>
                                        <img id="blah" src="{{ asset('uploads/complaints/'.$images[0]) }}" class="image-input-wrapper" alt="your image" /></center>
                                    @else
                                        <img id="blah" src="{{ asset('uploads/no_image.png') }}" class="image-input-wrapper" alt="your image"/></center>
                                    @endif
                                    @if ($errors->has('image'))  
                                        {!! "<span class='span_danger'>". $errors->first('image')."</span>"!!} 
                                    @endif

                                    <label class="btn btn-xs btn-icon btn-circle btn-white btn-hover-text-primary btn-shadow" data-action="change" data-toggle="tooltip" title="" data-original-title="Change Image">
                                        <i class="fa fa-pen icon-sm text-muted"></i>
                                        {!! Form::file('image[]', array('id'=>'exampleInputFile','accept'=>'.png, .jpg, .jpeg','multiple')) !!}
                                    </label>
                                </div>

                                <!-- <label class="custom-file-label" for="exampleInputFile">Choose file</label> -->
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="row">
                            <div class="col-lg-12 text-right">
                                <button type="submit" class="btn btn-primary mr-2">Save</button>
                                <!-- <button type="reset" class="btn btn-secondary">Cancel</button> -->
                            </div>
                        </div>
                    </div>
                {!! Form::close() !!}
                <!--end::Form-->
            </div>
        </div>
    </div>

    
    <script>
        function readURL(input) {
          if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function(e) {
              $('#blah').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]); // convert to base64 string
          }
        }

        $("#exampleInputFile").change(function() {
          readURL(this);
        });
    </script>

    <script type="text/javascript">
       $(document).ready(function(){
           
        var id = document.getElementById('complaint_nature_id').value;  
            show_tags(id);
       });
       function show_tags($id){
            // alert($id);
            var token = $("input[name='_token']").val();
            $.ajax({
                url: "{{ url('fetch_complaint_tags') }}",
                method: 'POST',
                data: {id:$id, _token:token},
                success: function(data) {
                    // console.log(data);
                    if(data.data){
                        $("#complaint_tag_id").html(data.data);
                    }else{
                        $("#complaint_tag_id").html('<option value=0>--- Select Tag ---</option>');
                    }
                }
            });
        }
    </script>
@endsection
