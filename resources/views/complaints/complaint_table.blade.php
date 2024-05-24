<?php if(isset($rec)) { ?>
   <?php foreach($rec as $key =>$value){  ?><tr><td>
            @if(isset($value->srno))
                    {{$value->srno}}
                @endif
            </td>
            <td>
                @if(isset($value->order_id))
                    {{$value->order_id}}
                @endif
            </td>
            <td style="font-weight:bold">
                @if(isset($value->name))
                    {{$value->name}}
                @endif
            </td>
            <td>
                @if(isset($value->contact_no))
                    {{$value->contact_no}}
                @endif
            </td>
            <td>
                @if(isset($value->complaint_date))
                    {{$value->complaint_date}}
                @endif
            </td>
           
            <td>
                @if(isset($value->complaint_status))
                    {{$value->complaint_status}}
                    
                @endif
            </td>
            <td>
                @if(isset($value->cmp_ntr_name))
                    {{$value->cmp_ntr_name}}
                    
                @endif
            </td>
           
            <td>
                @if(isset($value->id))
                    <div class="btn-group btn-group">
                        <a  href="/complaints/trail_complaint/{{$value->id}}" class="btn btn-primary btn-sm">
                            <i class="far fa-clipboard"></i>
                        </a>
                        <a  href="/complaints/resolve_complaint/{{$value->id}}" class="btn btn-success btn-sm">
                            <i class="fas fa-check"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="complaints/{{$value->id}}">
                            <i class="fa fa-eye"></i>
                        </a>
                        <a class="btn btn-secondary btn-sm" href="complaints/{{$value->id}}/edit" id="{{$value->id}}">
                            <i class="fas fa-pencil-alt"></i>
                        </a>
                        <button
                            class="btn btn-danger btn-sm delete_all"
                            data-url="'. url('complaint_delete') .'" data-id="{{$value->id}}">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </div>
                @endif
            </td></tr><?php }}?>



