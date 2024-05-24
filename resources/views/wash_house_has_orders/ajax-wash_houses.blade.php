<!-- <option value="0" selected disabled>--- Select Wash house ---</option> -->
@if(!empty($rec))
  @foreach($rec as $key => $value)
    <option value="{{ $key }}">{{ $value }}</option>
  @endforeach
@endif

