@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor style --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/flag-icon/css/flag-icon.min.css')}}">
@endsection

{{-- page content --}}
@section('content')
<div class="section">
  <div class="card">
    
  </div>

  <!-- HTML VALIDATION  -->


  <div class="row">
    <div class="col s12">
    @include('panels.flashMessages')

      <div id="validations" >
        <div class="">
          <div id="view-validations" style="padding-top:40px">
         
          <form class="formValidate" method="post" action="{{ isset($mappingResult) ? route($formUrl,$mappingResult->id) : route($formUrl) }}">

            @csrf

              @if(isset($mappingResult))
                  @method('PUT') Use PUT for updating
              @endif

            

              <div class="input-field col s12">
                <label for="disease">{{__('locale.Select disease')}}</label>
                <br>
                <div class="input-field">
                 <select name="decease_id" id="disease" required>
                  <option value="Select" disabled selected>{{__('locale.Select disease')}} *</option>
                  @if(isset($deceaseResult) && !empty($deceaseResult))
                  @foreach($deceaseResult as $decease_val)
                  
                  <option value="{{ $decease_val->id }}">{{ $decease_val->name}}</option>
                    @endforeach
                  @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
             </div> 
             </div>          
              <?php //echo '<pre>';print_r($productIds); exit(); ?>

              <div class="input-field col s12">
                <label for="inventory">{{__('locale.Select inventory')}}</label>
                <br>
                <div class="input-field">
                 <select name="inventory_id" id="inventory" required>
                  <option value="Select" disabled selected>{{__('locale.Select inventory')}} *</option>
                  @if(isset($inventoryResult) && !empty($inventoryResult))
                  @foreach($inventoryResult as $inventory_val)
                  
                  <option value="{{ $inventory_val->id }}">{{ $inventory_val->name }}</option>
                    @endforeach
                  @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
             </div>  
             </div>
                          
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light right submit" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>

             


            </form>
          </div>
          
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
@section('page-script')
<script>
window.onload=function(){
    var disease_value="{{(isset($mappingResult->decease_id) && $mappingResult->decease_id!='NULL') ? $mappingResult->decease_id : old('decease_id')}}";
    console.log('disease_value',disease_value);
    $('#disease').val(disease_value);
    $('#disease').formSelect();

    var inventory_value="{{(isset($mappingResult->inventory_id) && $mappingResult->inventory_id!='NULL') ? $mappingResult->inventory_id : old('inventory_id')}}";
    console.log('inventory_value',inventory_value);
    $('#inventory').val(inventory_value);
    $('#inventory').formSelect();
  }
  </script>
@endsection

