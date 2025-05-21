@extends('template.main')

@section('title', trans('boards.import_pinyes')) 
@section('css_before')
  <link rel="stylesheet" href="{!! asset('js/plugins/select2/css/select2.min.css') !!}">
@endsection
@section('css_after')
  <style>
    #dibuix div {
      border: 1px solid black;
    }
  </style>
@endsection

@section('content')
  <div class="d-flex justify-content-between">
    {!! Form::open(array('id' => 'FormTranslateImportBoard', 'url' => route('boards.import-translate'), 'method' => 'POST', 'class' => '', 'enctype' => 'multipart/form-data')) !!}
    <input type="hidden" name="boardId" value="{!! $new_board->getId() !!}">
    <div>
      <div class="w-75">
        <label class="" for="newName" >Nom:</label> 
        <input type="text" class="w-auto d-inline form-control" name="newName" value="{!! $new_board->name !!}" required>
        <hr>
        <div>{!! trans('boards.select_translations') !!}</div>
        @php($checked_positions = [])
        @foreach ($board_positions as $pos)        
          @if (!in_array($pos->position, $checked_positions) and $pos->position != 'baix')
            @php($checked_positions[] = $pos->position)
            <label class="" for="traductor_{!! $pos->position !!}" >{!! $pos->position !!} = </label> 
            <select name="traductor_{!! count($checked_positions) !!}" id="traductor_{!! count($checked_positions) !!}" class="w-auto d-inline form-control" required>
              @foreach ($colla_tags as $tag)        
                <option class="" value="{!! $pos->position !!};{!! $tag->name !!}">{!! $tag->name !!}</option>
              @endforeach    
            </select> <br>
          @endif
        @endforeach    
      </div>
      <div id="dibuix">
      </div>
    </div>
    <button type="submit" form="FormTranslateImportBoard" class="btn btn-alt-primary m-2"> {!! trans('boards.import_pinya') !!}</button>
    {!! Form::close() !!}
  </div>
@endsection

@section('js')
  <script>
    
  </script>
@endsection