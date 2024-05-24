

@if(!empty($addons))
  @foreach($addons as $key => $value)
    <div class="checkbox-list">
      <label class="checkbox"> 
      {!! Form::checkbox("item_addons[$service_id][$item_id][$key]", null,null, array("class" => "form-control")) !!}
      <span></span>{{ $value }}</label>
    </div> 
  @endforeach
@endif


