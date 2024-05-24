@if(!empty($addons))
  @foreach($addons as $key => $value)
    <div class="checkbox-inline">
      <label class="checkbox checkbox-success"> 
      {!! Form::checkbox("item_addons[$service_id][$item_id][$key]",$key,false, array("class" => "adn_check")) !!}
      <span></span>{{ $value }}</label>
    </div> 
  @endforeach
@endif


