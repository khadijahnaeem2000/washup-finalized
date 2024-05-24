@if(!empty($addons))
  @foreach($addons as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif