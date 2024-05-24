@if(!empty($riders))
  @foreach($riders as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif