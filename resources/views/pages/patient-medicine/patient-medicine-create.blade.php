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
          @if(isset($medicineResponse->id))
          <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-update'; ?>
            <form class="formValidate" action="{{route($formUrl,$medicineResponse->id)}}" id="formValidateCompany" method="post">
            {!! method_field('post') !!}
            @else
            <?php //$formUrl = (isset($formUrl) && $formUrl!='') ? $formUrl : 'company-admin-create'; ?>
          <form id="accountForm" action="{{route($formUrl)}}" method="post">
            @endif
            @csrf()
            <div class="row">

            

            <!-- @if(isset($userType) && $userType!=config('custom.superadminrole'))
                  <input type="hidden" name="company" value="{{Helper::loginUserCompanyId()}}"/>
                  @else
                  <div class="col s12 input-field">
                    <select class="error" id="company" name="company" data-error=".errorTxt7" required>
                      <option value="">Choose {{__('locale.code')}}</option>
                      @if(isset($companies) && !empty($companies))
                        @foreach ($companies as $company_value)
                          <option value="{{$company_value->id}}">{{$company_value->company_name}} ({{$company_value->company_code}})</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="company">{{__('locale.Care home code')}}</label>
                    <small class="errorTxt7"></small>
                  </div>
            @endif -->
            <div class="input-field col  m6 s12">
                <label for="disease">{{__('locale.Select medicine')}}</label>
                <br>
                <div class="input-field">
                 <select name="medicine_code" id="medicine" required>
                  <option value="Select" disabled selected>{{__('locale.Select medicine')}} *</option>
                  @if(isset($medicineResult) && !empty($medicineResult))
                  @foreach($medicineResult as $decease_val)
                  
                  <option value="{{ $decease_val->id }}">{{ $decease_val->medicine_name}}</option>
                    @endforeach
                  @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
             </div> 
             </div>

             <div class="input-field col m6 s12">
                <label for="disease">{{__('locale.Select patient')}}</label>
                <br>
                <div class="input-field">
                 <select name="patient_code" id="patient" required>
                  <option value="Select" disabled selected>{{__('locale.Select patient')}} *</option>
                  @if(isset($patientResult) && !empty($patientResult))
                  @foreach($patientResult as $decease_val)
                  
                  <option value="{{ $decease_val->id }}">{{ $decease_val->name}}</option>
                    @endforeach
                  @endif
                </select>
                @error('company_id')
                <div style="color:red">{{$message}}</div>
                @enderror
             </div> 
             </div>
             
             <input type="hidden" name="c_home_code" value="{{Helper::loginUserCompanyId()}}"/>
                  <div class="col s12 input-field">
                    <select class="error" id="company" name="c_home_code" data-error=".errorTxt7" required>
                      <option value="">Choose {{__('locale.care_home_code')}}</option>
                      @if(isset($companies) && !empty($companies))
                        @foreach ($companies as $company_value)
                          <option value="{{$company_value->id}}">{{$company_value->company_name}} ({{$company_value->company_code}})</option>
                        @endforeach
                      @endif
                    </select>
                    <label for="company">{{__('locale.Care home code')}}*</label>
                    <small class="errorTxt7"></small>
                  </div>
                  </div>
           
                <div class="input-field col m6 s12">
                  <label for="name">{{__('locale.remark')}}*</label>
                  <input id="name" class="validate" name="remark" type="text" data-error=".errorTxt1" value="{{(isset($medicineResponse->remark)) ? $medicineResponse->remark : old('remark')}}">
                  <small class="errorTxt1"></small>
                </div>

                <div class="input-field col m6 s12">
                  <label for="name">{{__('locale.doses')}}*</label>
                  <input id="name" class="validate" name="doses" type="text" data-error=".errorTxt1" value="{{(isset($medicineResponse->doses)) ? $medicineResponse->doses : old('doses')}}">
                  <small class="errorTxt1"></small>
                </div>

                <input id="name" class="validate" name="updated_by_user" type="hidden" data-error=".errorTxt1" value="{{auth()->user()->id}}" readonly>
                
                <!-- <div class="input-field col m4 s12">
                  <label for="address2">{{__('locale.address3')}}</label>
                  <input id="address2" type="text" name="address3" data-error=".errorTxt4" value="{{(isset($user_result->address3) && $user_result->address3!='NULL') ? $user_result->address3 : old('address3')}}">
                  <small class="errorTxt4"></small>
                </div>
                <div class="col m12 s12">
                  <label for="country">{{__('locale.country')}} *</label>
                  <div class="input-field">
                    <select class="error" id="country" name="country" data-error=".errorTxt6">
                      <option value="">Choose {{__('locale.country')}}</option>
                      @if(isset($countries) && !empty($countries))
                        @foreach ($countries as $country_value)
                          <option value="{{$country_value->id}}">{{$country_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt6"></small>
                  </div>
                </div>
                <div class="col m6 s12">
                  <label for="state">{{__('locale.state')}} *</label>
                  <div class="input-field">
                    <select class="error" id="state" name="state" data-error=".errorTxt7">
                      <option value="">Choose {{__('locale.state')}}</option>
                      @if(isset($user_result->state) && isset($states) && !empty($states))
                        @foreach ($states as $state_value)
                          <option value="{{$state_value->id}}">{{$state_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt7"></small>
                  </div>
                </div>
                <div class="col m6 s12">
                  <label for="city">{{__('locale.city')}} *</label>
                  <div class="input-field">
                    <select class="error" id="city" name="city" data-error=".errorTxt8">
                      <option value="">Choose {{__('locale.city')}}</option>
                      @if(isset($user_result->city) && isset($cities) && !empty($cities))
                        @foreach ($cities as $city_value)
                          <option value="{{$city_value->id}}">{{$city_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt8"></small>
                  </div>
                </div>
                
                <div class="input-field col m6 s12">
                  <label for="Zip Code">{{__('locale.ZipCode')}}</label>
                  <input id="zipcode" type="text" name="zipcode" data-error=".errorTxt2" value="{{(isset($user_result->zipcode)) ? $user_result->zipcode : old('company_code')}}">
                  <small class="errorTxt2"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="phone">{{__('locale.phone')}}*</label>
                  <input id="phone" type="number" name="phone" data-error=".errorTxt3" value="{{(isset($user_result->phone)) ? $user_result->phone : old('phone')}}">
                  <small class="errorTxt3"></small>
                </div>
                

                <div class="input-field col m6 s12">
                  <label for="email">{{__('locale.email')}}*</label>
                  <input id="email" type="email" name="email" data-error=".errorTxt3" value="{{(isset($user_result->email)) ? $user_result->email : old('email')}}">
                  <small class="errorTxt3"></small>
                </div>

                <div class="col m6 s12">
                  <label for="type">{{__('locale.select type')}} *</label>
                  <div class="input-field">
                  <select name="typeselect" id="myselect">
                      <?php 
                     // foreach ($roles as $role) {
                       // $isSelected = (isset($user_result->typeselect) && $user_result->typeselect == $role['name']) ? 'selected' : '';
                        ?>
                        <option value="<?php //echo $role['name']; ?>" <?php// echo $isSelected; ?>>
                          <?php// echo $role['name']; ?>
                        </option>
                        <?php 
                     // }
                      ?>
                    </select>
                    <small class="errorTxt6"></small>
                  </div>
                </div>

                 <div class="input-field col m6 s12">
                  <label for="userLicense">{{__('locale.userLicense')}}*</label>
                  <input id="userLicense" type="text" name="address1" data-error=".errorTxt3" value="{{(isset($company_result->userLicense)) ? $company_result->userLicense : old('userLicense')}}">
                  <small class="errorTxt3"></small>
                </div>
                
                <div class="input-field col m6 s12">
                  <label for="licensefrom">{{__('locale.licensefrom')}}*</label>
                  <input id="licensefrom" type="text" name="address1" data-error=".errorTxt3" value="{{(isset($company_result->userLicense)) ? $company_result->userLicense : old('userLicense')}}">
                  <small class="errorTxt3"></small>
                </div>

                <div class="input-field col m6 s12">
                  <label for="licenseto">{{__('locale.licenseto')}}*</label>
                  <input id="licenseto" type="text" name="address1" data-error=".errorTxt3" value="{{(isset($company_result->userLicense)) ? $company_result->userLicense : old('userLicense')}}">
                  <small class="errorTxt3"></small>
                </div> -->

 


                <!-- <div class="input-field col m12 s12">
                    <select name="Option">
                    <option value="1" disabled selected>{{__('locale.yes')}}</option>
                    <option value="0">{{__('locale.no')}}</option>
                    </select>
                    <label>{{__('locale.block')}}</label>
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
<?php //print_r($deceaseResult[0]->code) ?>
<script>
  window.onload=function(){
    var disease = "{{(isset($medicineResponse->medicine_code) && $medicineResponse->medicine_code!='') ? $medicineResponse->medicine_code : old('medicine_code')}}";
    console.log('disease',disease);

    var patient = "{{(isset($medicineResponse->patient_code) && $medicineResponse->patient_code!='NULL') ? $medicineResponse->patient_code : old('patient_code')}}";

    var carehome = "{{(isset($medicineResponse->c_home_code) && $medicineResponse->c_home_code!='NULL') ? $medicineResponse->c_home_code : old('c_home_code')}}";
    
    
    $('#medicine').val(disease);
    $('#medicine').formSelect();
    $('#patient').val(patient);
    $('#patient').formSelect();
    $('#company').val(carehome);
    $('#company').formSelect();
    
    // if(country_value_edit && country_value_edit!=''){
    //   $('#company').attr('disabled',true);
    // }
    // $('#company').formSelect();
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