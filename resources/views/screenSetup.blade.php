@extends('layouts.app')

@section('content')
<div class="subnavbar">
<!-- /For Space-->
</div>
<!-- /subnavbar -->
<div class="main">
<div class="main-inner">
<div class="container">
  @if(session('message'))
  		   <div class='alert alert-success fade in'><a href='#' class='close' data-dismiss='alert'>&times;</a>{{ session('message') }}</div>
  		   @endif
<!-- /span6 -->
 <div class="span12">
  <div class="widget widget-nopad">
    <div class="widget-header"> <i class="icon-list-alt"></i>
      <h3> Screen Setup</h3>
    </div>
    <!-- /widget-header -->
    <div class="widget-content">


      <table class="table table-striped table-bordered">
<thead>

          <th>
            Module Name
          </th>
          <th>
            Existing Column Name
          </th>
          <th>
              New Column Name
          </th>


      </thead>


           <tr>
               @foreach ($tableColumns as $tableColumns)
             {!! Form::open(array('url' => 'screenSetUp/'.$tableColumns->ID, 'method' => 'post')) !!}

             <td>
               {{$tableColumns -> MODULE}}
             </td>
             <td>
      {!! Form::label('Existing Column Name', $tableColumns->COLUMN_DISPLAY , array('class' => ' control-label')); !!}
    </td>
    <td>
      {!! Form::text('column_display', '',array('class'=> 'form-control','placeholder' =>'Enter New Name')); !!}
    </td>

    <td>
    <button type="submit" class=""button btn btn-success btn-small">
              	<i class="shortcut-icon icon-edit"> </i>
            </button>
          </td>
        </tr>
            {!!Form::close() !!}
      @endforeach




</table>
    <!-- /widget-content -->
  </div>
  <!-- /widget -->
</div>
<!-- /span6 -->
</div>
<!-- /row -->
</div>
<!-- /container -->
</div>
<!-- /main-inner -->
</div>
<!-- /main -->
    @endsection
