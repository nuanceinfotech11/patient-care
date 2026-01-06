{{-- layout --}}
@extends('layouts.contentLayoutMaster')

{{-- page title --}}
@include('panels.page-title')

{{-- vendor styles --}}
@section('vendor-style')
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2.min.css')}}">
<link rel="stylesheet" type="text/css" href="{{asset('vendors/select2/select2-materialize.css')}}">
<!-- Add these in the head section of your HTML file -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>


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
          @if(isset($patient_schedule_result->id))
          <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-update'; ?>
            <form class="formValidate" action="{{route($formUrl,$patient_schedule_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('post') !!}
            @else
            <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-create'; ?>
          <form id="accountForm"  method="post">
            @endif
            @csrf()
            <div class="row">
            <div class="input-field col s12">
            <label for="serach">{{__('locale.Select patient')}}</label><br>
            <div class="input-field">
                <select name="patient_id" id="patient" required>
                <option value="Select" disabled selected>{{__('locale.Select patient')}} *</option>
                @if(isset($patient) && !empty($patient))
                @foreach($patient as $patient_val)
                
                <option value="{{$patient_val->id}}">{{ $patient_val->name }}</option>
                @endforeach
                @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
            </div> 
            </div>
                <div class="input-field col m6 s12">
                  <input id="datepicker" class="validate datepicker" name="date" type="text"  data-error=".errorTxt1" value="{{(isset($patient_schedule_result->date)) ? $patient_schedule_result->date : old('date')}}">
                  <label for="name">{{__('locale.date')}}</label>
                  <small class="errorTxt1"></small>
                </div>

                <div class="input-field col m6 s12">
                  <input id="name" class="validate" name="time" type="time" data-error=".errorTxt1" value="{{(isset($patient_schedule_result->time)) ? $patient_schedule_result->time : old('time')}}">
                  <label for="name">{{__('locale.time')}}</label>
                  <small class="errorTxt1"></small>
                </div>

                <div class="input-field col m6 s12">
                <label for="serach">{{__('locale.Select carer name')}}</label><br>
                <div class="input-field">
                <select name="carer_code" id="carer" required>
                <option value="Select" disabled selected>{{__('locale.Select carer name')}} *</option>
                @if(isset($carer) && !empty($carer))
                @foreach($carer as $carer_val)
                
                <option value="{{ $carer_val->id }}">{{ $carer_val->name }}</option>
                @endforeach
                @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
            </div> 
            </div>

            
                  <input id="name" class="validate" name="carer_assigned_by" type="hidden" data-error=".errorTxt1" value="{{auth()->user()->id}}" readonly>
                  
                 

                <!-- <div class="input-field col m6 s12">
                <label for="serach">{{__('locale.assign_by')}}</label><br>
                <div class="input-field">
                  <select name="carer_assigned_by" id="carer_assign">
                  <option value="Select" disabled selected>{{__('locale.assign_by')}}*</option> -->
                  <!-- <input id="name" class="validate" name="carer_assigned_by" type="text" data-error=".errorTxt1" value="{{auth()->user()->id}}"> -->
                  
                      <!-- </select>
                  <small class="errorTxt1"></small>
                </div>
                </div> -->
                <div class="input-field col m6 s12">
                <label for="serach">{{__('locale.Select alternate carer name')}}</label><br>
                <div class="input-field">
                <select name="alternate_carer_code" id="alternate_carer" required>
                <option value="Select" disabled selected>{{__('locale.Select alternate carer name')}} *</option>
                @if(isset($carer) && !empty($carer))
                @foreach($carer as $carer_val)
                
                <option value="{{ $carer_val->id }}">{{ $carer_val->name }}</option>
                @endforeach
                @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
            </div> 
            </div>   




                <!-- <div class="input-field col m12 s12">
                    <select name="Option">
                    <option value="1" disabled selected>{{__('locale.yes')}}</option>
                    <option value="0">{{__('locale.no')}}</option>
                    </select>
                    <label>{{__('locale.Option')}}</label>
                </div> -->
                
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
<!-- Add these in the head section of your HTML file -->


<!-- <script src="{{asset('js/scripts/form-elements.js')}}"></script> -->
<script>
  window.onload=function(){
    var patient_value = "{{(isset($patient_schedule_result->patient_id) && $patient_schedule_result->patient_id!='NULL') ? $patient_schedule_result->patient_id : old('patient_id')}}";
    
    var carer_value = "{{(isset($patient_schedule_result->carer_code) && $patient_schedule_result->carer_code!='NULL') ? $patient_schedule_result->carer_code : old('carer_code')}}";

    var carer_assign_value="{{(isset($patient_schedule_result->carer_assigned_by) && $patient_schedule_result->carer_assigned_by!='NULL') ? $patient_schedule_result->carer_assigned_by : old('carer_assigned_by')}}";

    var alternate_carer_value = "{{(isset($patient_schedule_result->alternate_carer_code) && $patient_schedule_result->alternate_carer_code!='NULL') ? $patient_schedule_result->alternate_carer_code : old('alternate_carer_code')}}";
    
    console.log(patient_value,carer_value,alternate_carer_value,carer_assign_value);
    $('#patient').val(patient_value);
    $('#patient').formSelect();

    $('#carer').val(carer_value);
    $('#carer').formSelect();

    $('#carer_assign').val(carer_assign_value);
    $('#carer_assign').formSelect();

    $('#alternate_carer').val(alternate_carer_value);
    $('#alternate_carer').formSelect();
    
  }
    $(document).ready(function () {


      $("#datepicker").datepicker({
      dateFormat: "YYYY-MM-DD", // Format the date as needed (e.g., YYYY-MM-DD).
      changeYear: true, // Enable year dropdown selection.
      changeMonth: true, // Enable month dropdown selection.
      // You can add more options as needed.
    });
      
      

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