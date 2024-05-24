@extends('layouts.master')
@section('title','Order Tags')
@section('content')
    @include( '../sweet_script')
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
    @if(isset($permission))
    <script>
        var text = "<?php echo $permission;?>";
        toastr.error(text)
    </script>
    @else
    <script>
        var txt = "<?php echo $success;?>";

        const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                confirmButton: 'btn btn-success',
                // cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: (txt)+ " Have you put the order in corret pile?",
                icon: 'success',
                // showCancelButton: true,
                confirmButtonText: 'Yes!',
                // cancelButtonText: 'No, cancel!',
                reverseButtons: true
            }) 
      
    </script>
    @endif
<style>

    @page{
            width:115px;
            height: 480px;
    }
    @media print
    {    
        .no-print, .no-print *
        {
            display: none !important;
        }
       
        th,td{
            padding-left:3px;
            font-weight: bold;
            color: black;
        }
        th{
            font-size: 18px;
        }
    }

    th,td{
        padding-left:3px;
        font-weight: bold;
        color: black;
    }
    th{ font-size: 18px;}
    /*table { page-break-after:always }*/
    
    
   
    
</style>

  <div class="row">
        <div class="col-lg-12">
            <!--begin::Card-->
                <div class="card card-custom gutter-b example example-compact">
                
                 <div class="card-header py-3 d-print-none">
                    <div class="card-title">
                        <h3 class="card-label">@yield('title')</h3>
                    </div>
                    <div class="card-toolbar " >
                        <div class="btn-group btn-group">
                            <a  href="{{ route('order_verifies.index') }}" class="btn btn-primary btn-sm ">
                            <i class="fas fa-arrow-left"></i></a>
                            <button    class="btn btn-info btn-sm "  onclick="printDiv('main')">
                            <i class="fa fa-print"></i></button>
                        </div>
                    </div>
                </div>
              

                <div class="card-body" id="main"  style = "padding: 0px;">
                   <div class="row"  >
                       <div class="col-12 col-md-12" >
                           <!--<div class="table-responsive">-->
                                @if($tags)
                                    @foreach($tags as $key =>$value)
                                            <table class="" style="border: 1px solid black;margin-top: 5px; margin-left:3px; width:110px; height: 400px;  page-break-after:always; color:black" >
                                                <tbody>
                                                    <tr>
                                                        <td style="width:20px !important;">Order#</td>
                                                        <th style="width:90px !important;"><span style= " font-size:22px ">{{$data->id}}</span></th>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td style="width:20px !important;">Client</td>
                                                        <?php $name = explode(" ",$data->name);?>
                                                        <th style="width:90px !important; font-size: 13px;">{{ substr($name[0], 0, 8) }}  </th>
                                                    </tr>
                                                    <tr style="line-height: 11px; height: 0px;">
                                                        <td style="width:20px !important;">Item</td>
                                                        <th style="width:90px !important; font-size: 18px;">{{$value->item_name}} </th>
                                                    </tr>
                                                    <tr>
                                                        <td colspan=2 style="text-align: center;">
                                                            @if($value->service_image)
                                                                <img src="{{ asset('uploads/services/'.$value->service_image) }}" alt="users view avatar" style="height: 80px; width: auto; margin-bottom:20px; margin-top: 4px">
                                                            @else
                                                                <img src="{{ asset('uploads/no_image.png') }}" alt="users view avatar" style="height: 80px; width: 100px;" >
                                                            @endif

                                                        </td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td colspan=2  style="text-align: center; "> 
                                                        <?php $code = "".$value->tag_code;?>
                                                            <img src="data:image/png;base64,{{DNS1D::getBarcodePNG($code, 'EAN13')}}" alt="barcode" class="barcode_image" style="transform: rotate(90deg); height:80px; width:200px; margin: 45px -130px 54px;color:black"/>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2" style="text-align: center;">
                                                            <span style="text-align:center; font-size:12px; font-weight:bold">
                                                            <?php echo ++$key." of ". $tot_tags;?>
                                                                www.washup.com.pk
                                                            </span>
                                                        </td>
                                                    </tr>
                                                <tbody>
                                            </table>
                                    @endforeach
                                @endif
                              
                           <!--</div>-->
                       </div>
                   </div>
                </div>
            </div>
       </div>
   </div>

    <script>
		function printDiv(divName){
            // var div = document.getElementById('btns');
            //     div.remove();
            // $(".btns").remove();
			var printContents = document.getElementById(divName).innerHTML;
			var originalContents = document.body.innerHTML;
          
            // console.log( printContents.children(".btns")) ;
            // console.log(printContents)
			document.body.innerHTML = printContents;
            
			window.print();
            // $(".btns").append();
			document.body.innerHTML = originalContents;

		}
    </script>
@endsection
