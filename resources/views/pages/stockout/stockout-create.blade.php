{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
@endsection

{{-- page style --}}
@section('page-style')
<link rel="stylesheet" type="text/css" href="{{asset('css/pages/page-users.css')}}">
@endsection

{{-- page content --}}
@section('content')
<!-- users edit start -->
<div class="section users-edit">
  <div class="card">
    <div class="card-content">
      <!-- <div class="card-body"> -->
      
      <div class="row">
        <div class="col s12" id="account">
          
          <!-- users edit media object ends -->
          <!-- users edit account form start -->
          @include('panels.flashMessages')
          @if(isset($stockout_result->id))
          <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-update'; ?>
            <form class="formValidate" action="{{route($formUrl,$stockout_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('post') !!}
            @else
            <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-create'; ?>
          <form id="accountForm"  method="post">
            @endif
            @csrf()
            <div class="row">
                <div class="input-field col m6 s12">
                  <label for="name">{{__('locale.doc')}}</label>
                  <input id="name" class="validate" name="doc_no" type="text" data-error=".errorTxt1" value="{{(isset($stockout_result->doc_no)) ? $stockout_result->doc_no : old('doc_no')}}">
                  <small class="errorTxt1"></small>
                </div>

                <div class="input-field col m6 s12">
                  <input id="name" class="validate datepicker" name="date" type="text" data-error=".errorTxt1" value="{{(isset($stockout_result->date)) ? $stockout_result->date : old('date')}}">
                  <label for="name">{{__('locale.date')}}</label>
                  <small class="errorTxt1"></small>
                </div>

                <div class="input-field col m6 s12">
                <label for="disease">{{__('locale.Select patient name')}}*</label><br>
                <div class="input-field">
                  <!-- <input id="name" class="validate" name="inventory_code" type="text" data-error=".errorTxt1" value="{{(isset($result->name)) ? $result->name : old('name')}}"> -->
                  <select name="patient_code" id="patient" required>
                  <option value="Select" disabled selected>{{__('locale.Select patient name')}}</option>
                  @if(isset($patient) && !empty($patient))
                  @foreach($patient as $patient_val)

                  <option value="{{$patient_val->id}}">{{$patient_val->name}}</option>
                  @endforeach
                  @endif
                  </select>
                  @error('company_id')
                  <div style="color:red">{{$message}}</div>
                  @enderror
                  
                </div>
                </div>

                <div class="input-field col m6 s12">
                <label for="disease">{{__('locale.Select carer name')}}*</label><br>
                <div class="input-field">
                  <!-- <input id="name" class="validate" name="inventory_code" type="text" data-error=".errorTxt1" value="{{(isset($result->name)) ? $result->name : old('name')}}"> -->
                  <select name="carer_code" id="carer" required>
                  <option value="Select" disabled selected>{{__('locale.Select carer name')}}</option>
                  @if(isset($carer) && !empty($carer))
                  @foreach($carer as $carer_val)

                  <option value="{{$carer_val->id}}">{{$carer_val->name}}</option>
                  @endforeach
                  @endif
                  </select>
                  @error('company_id')
                  <div style="color:red">{{$message}}</div>
                  @enderror
                  
                </div>
                </div>



                

                <div class="input-field col m6 s12">
                <label for="disease">{{__('locale.Select inventory name')}}*</label><br>
                <div class="input-field">
                  <!-- <input id="name" class="validate" name="inventory_code" type="text" data-error=".errorTxt1" value="{{(isset($result->name)) ? $result->name : old('name')}}"> -->
                  <select name="inventory_code" id="inventory" required>
                  <option value="Select" disabled selected>{{__('locale.Select inventory name')}}</option>
                  @if(isset($inventory) && !empty($inventory))
                  @foreach($inventory as $inventory_val)

                  <option value="{{$inventory_val->id}}">{{$inventory_val->name}}</option>
                  @endforeach
                  @endif
                  </select>
                  @error('company_id')
                  <div style="color:red">{{$message}}</div>
                  @enderror
                  
                </div>
                </div>

                <div class="input-field col m6 s12">
                  <label for="name">{{__('locale.quantity')}}</label><br>
                  <input id="name" class="validate" name="quantity" min="0" type="number" data-error=".errorTxt1" value="{{(isset($stockout_result->quantity)) ? $stockout_result->quantity : old('quantity')}}" style="margin-top:15px;">
                  <small class="errorTxt1"></small>
                </div>

                

                <div class="input-field col m12 s12">
                  <label for="name">{{__('locale.stby')}}</label>
                  <input id="name" class="validate" name="stock_out_by" type="text" data-error=".errorTxt1" value="{{(isset($stockout_result->stock_out_by)) ? $stockout_result->stock_out_by : old('stock_out_by')}}">
                  <small class="errorTxt1"></small>
                </div>

                
                
                
                <div class="input-field col s12">
                  <button class="btn waves-effect waves-light right submit" type="submit" name="action">Submit
                    <i class="material-icons right">send</i>
                  </button>
                </div>
              </div>
          </form>
          <!-- users edit account form ends -->
        </div>
      </div>
      <!-- </div> -->
    </div>
  </div>
</div>
<!-- users edit ends -->
@endsection

{{-- vendor scripts --}}
@section('vendor-script')
<script src="{{asset('vendors/select2/select2.full.min.js')}}"></script>
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page scripts --}}
@section('page-script')
<script src="{{asset('js/scripts/page-users.js')}}"></script>
<script src="{{asset('js/scripts/form-validation.js')}}"></script>
<script>
  window.onload=function(){
    var inventory_value = "{{(isset($stockout_result->inventory_code) && $stockout_result->inventory_code!='NULL') ? $stockout_result->inventory_code : old('inventory_code')}}";
    var patient_value = "{{(isset($stockout_result->patient_code) && $stockout_result->patient_code!='NULL') ? $stockout_result->patient_code : old('patient_code')}}";
    var carer_value = "{{(isset($stockout_result->carer_code) && $stockout_result->carer_code!='NULL') ? $stockout_result->carer_code : old('carer_code')}}";

    console.log(inventory_value);
    $('#inventory').val(inventory_value);
    $('#inventory').formSelect();

    $('#patient').val(patient_value);
    $('#patient').formSelect();

    $('#carer').val(carer_value);
    $('#carer').formSelect();
    
  }
    $(document).ready(function () {
      

        $('#country').on('change', function () {
            var idCountry = this.value;
            console.log(idCountry);
            $("#state").html('');
            $.ajax({
                url: "{{url('api/fetch-states')}}",
                type: "POST",
                data: {
                    country_id: idCountry,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (result) {
                    $('#state').html('<option value="">Select State</option>');
                    $.each(result.states, function (key, value) {
                        $("#state").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#state').formSelect();
                    $('#city').html('<option value="">Select City</option>');
                }
            });
        });
        $('#state').on('change', function () {
            var idState = this.value;
            $("#city").html('');
            $.ajax({
                url: "{{url('api/fetch-cities')}}",
                type: "POST",
                data: {
                    state_id: idState,
                    _token: '{{csrf_token()}}'
                },
                dataType: 'json',
                success: function (res) {
                    $('#city').html('<option value="">Select City</option>');
                    $.each(res.cities, function (key, value) {
                        $("#city").append('<option value="' + value
                            .id + '">' + value.name + '</option>');
                    });
                    $('#city').formSelect();
                }
            });
        });
    });
</script>
@endsection