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
      <div id="validations" class="card card-tabs">
        <div class="card-content">
          <div class="card-title">
            <div class="row">
              <div class="col s12 m6 l10">
                
              </div>
            </div>
          </div>
          <div id="view-validations">
            @include('panels.flashMessages')
            @if(isset($company_result))
            <form class="formValidate" action="{{route('company.update',$company_result->id)}}" id="formValidateCompany" method="post">
            {!! method_field('patch') !!}
            @else
            <form class="formValidate" action="{{route('company.store')}}" id="formValidateCompany" method="post">
            @endif
            @csrf()
              <div class="row">
              <div class="input-field col m12 s12">
                  <label for="name">{{__('locale.code')}}*</label>
                  <input id="name" class="" name="company_code" type="text" maxlength="8" oninput="this.value=this.value.replace(/[^0-9.,]/g,'');" data-error=".errorTxt1" value="{{(isset($company_result->company_code)) ? $company_result->company_code : $companyCode}}" 
                  >
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m12 s12">
                  <label for="name">{{__('locale.name')}}*</label>
                  <input id="name" class="validate" name="company_name" type="text" data-error=".errorTxt1" value="{{(isset($company_result->company_name)) ? $company_result->company_name : old('company_name')}}">
                  <small class="errorTxt1"></small>
                </div>
                <div class="input-field col m4 s12">
                  <label for="address1">{{__('locale.address1')}}*</label>
                  <input id="address1" type="text" name="address1" data-error=".errorTxt3" value="{{(isset($company_result->address1)) ? $company_result->address1 : old('address1')}}">
                  <small class="errorTxt3"></small>
                </div>
                <div class="input-field col m4 s12">
                  <label for="address2">{{__('locale.address2')}}*</label>
                  <input id="address2" type="text" class="error" name="address2" data-error=".errorTxt3" value="{{(isset($company_result->address2) && $company_result->address2!='NULL') ? $company_result->address2 : old('address2')}}">
                  <small class="errorTxt3"></small>
                </div>
                <div class="input-field col m4 s12">
                  <label for="address3">{{__('locale.address3')}}*</label>
                  <input id="address3" type="text" name="address3" data-error=".errorTxt3" value="{{(isset($company_result->address3) && $company_result->address3!='NULL') ? $company_result->address3 : old('address3')}}">
                  <small class="errorTxt3"></small>
                </div>
                <div class="col m12 s12">
                  <label for="country">{{__('locale.country')}} *</label>
                  <div class="input-field">
                    <select class="error" id="country" name="country" data-error=".errorTxt3">
                      <option value="">Choose {{__('locale.country')}}</option>
                      @if(isset($countries) && !empty($countries))
                        @foreach ($countries as $country_value)
                          <option value="{{$country_value->id}}">{{$country_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt3"></small>
                  </div>
                </div>
                <!-- <div class="input-field col m6 s12">
                  <label for="state">{{__('locale.state')}}*</label>
                  <input id="" type="text" name="state" data-error=".errorTxt4" value="{{(isset($company_result->state) && $company_result->state!='NULL') ? $company_result->state : old('state')}}">
                  <small class="errorTxt3"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="city">{{__('locale.city')}}*</label>
                  <input id="" type="text" name="city" data-error=".errorTxt4" value="{{(isset($company_result->city) && $company_result->city!='NULL') ? $company_result->city : old('city')}}">
                  <small class="errorTxt3"></small>
                </div> -->
                <div class="col m6 s12">
                  <label for="state">{{__('locale.state')}} *</label>
                  <div class="input-field">
                    <select class="error" id="state" name="state" data-error=".errorTxt7">
                      <option value="">Choose {{__('locale.state')}}</option>
                      @if(isset($company_result->state) && isset($states) && !empty($states))
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
                      @if(isset($company_result->city) && isset($cities) && !empty($cities))
                        @foreach ($cities as $city_value)
                          <option value="{{$city_value->id}}">{{$city_value->name}}</option>
                        @endforeach
                      @endif
                    </select>
                    <small class="errorTxt8"></small>
                  </div>
                </div>
                
                <div class="input-field col m6 s12">
                  <label for="Zip Code">{{__('locale.ZipCode')}}*</label>
                  <input id="zipcode" type="text" class="zip-valid" name="pincode" data-error=".errorTxt2" minlength="0" maxlength="6" value="{{(isset($company_result->pincode)) ? $company_result->pincode : old('pincode')}}">
                  <small class="errorTxt2"></small>
                </div>
                <div class="input-field col m6 s12">
                  <label for="phone">{{__('locale.phone')}}*</label>
                  <input id="phone" type="text" class="mobile-valid" name="phone_no" minlength="0" maxlength="13" data-error=".errorTxt3" value="{{(isset($company_result->phone_no)) ? $company_result->phone_no : old('phone_no')}}">
                  <small class="errorTxt3"></small>
                </div>
               

                <div class="input-field col m6 s12">
                  <label for="email">{{__('locale.email')}}*</label>
                  <input id="email" type="email" name="email" data-error=".errorTxt3" value="{{(isset($company_result->email)) ? $company_result->email : old('email')}}">
                  <small class="errorTxt3"></small>
                </div>

                <div class="input-field col m6 s12">
                  <label for="userLicense">{{__('locale.userLicense')}}*</label>
                  <input id="userLicense" type="text" name="no_of_user_license" data-error=".errorTxt3" value="{{(isset($company_result->no_of_user_license)) ? $company_result->no_of_user_license : old('no_of_user_license')}}">
                  <small class="errorTxt3"></small>
                </div>
                
                <div class="input-field col m6 s12">
                  <input id="termination_datepicker_from" type="text" class="validate datepicker" name="license_from" data-error=".errorTxt3" value="{{(isset($company_result->license_from)) ? $company_result->license_from : old('license_from')}}">
                  <label for="licensefrom">{{__('locale.licensefrom')}}*</label>
                  <small class="errorTxt3"></small>
                </div>

                <div class="input-field col m6 s12">
                  <input id="termination_datepicker_to" type="text" class="validate datepicker" name="license_to" data-error=".errorTxt3" value="{{(isset($company_result->license_to)) ? $company_result->license_to : old('license_to')}}">
                  <label for="licenseto">{{__('locale.licenseto')}}*</label>
                  <small class="errorTxt3"></small>
                </div>




                <div class="input-field col m12 s12">
                
                  <select name="option_for_block" id="option_for_block">
                    <option value="">Choose {{__('locale.option')}}</option>
                    <option value="1">{{__('locale.yes')}}</option>
                    <option value="0">{{__('locale.no')}}</option>
                    </select>
                    <label>{{__('locale.block')}}</label>
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

{{-- vendor script --}}
@section('vendor-script')
<script src="{{asset('vendors/jquery-validation/jquery.validate.min.js')}}"></script>
@endsection

{{-- page script --}}
@section('page-script')
<script src="{{asset('js/scripts/form-validation.js')}}"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script> -->
<script>
  window.onload=function(){
    var country_value = "{{(isset($company_result->country) && $company_result->country!='NULL') ? $company_result->country : old('country')}}";
    var state_value = "{{(isset($company_result->state) && $company_result->state!='NULL') ? $company_result->state : old('state')}}";
    var city_value = "{{(isset($company_result->city) && $company_result->city!='NULL') ? $company_result->city : old('state')}}";
    var block_value= "{{(isset($company_result->option_for_block) && $company_result->option_for_block!='NULL') ? $company_result->option_for_block : old('option_for_block')}}";
    console.log(block_value,state_value);
    $('#country').val(country_value);
    $('#country').formSelect();
    $('#state').val(state_value);
    $('#state').formSelect();
    $('#city').val(city_value);
    $('#city').formSelect();
    $('#option_for_block').val(block_value);
    $('#option_for_block').formSelect();
  }

  
  
  $(document).ready(function () {
   $( "#termination_datepicker_from" ).datepicker({ 
          maxDate: new Date('2024-10-12'),
          onSelect: function(selectdate){
           console.log(selectdate)
             $("#termination_datepicker_to").datepicker({
                minDate: selectdate
             });
             console.log(selectdate)
          }
    });
      

        $('#country').on('change', function () {
            var idCountry = this.value;
            console.log(idCountry);
            $("#state").html('');
            $.ajax({
                url: "{{url('api/user-fetch-states')}}",
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
                url: "{{url('api/user-fetch-cities')}}",
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


$('.mobile-valid').on('keypress', function(e) {

var $this = $(this);
var regex = new RegExp("^[0-9\b]+$");
var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
// for 10 digit number only
if ($this.val().length > 13) {
    e.preventDefault();
    return false;
}
if (e.charCode < 54 && e.charCode > 47) {
    if ($this.val().length == 0) {
        e.preventDefault();
        return false;
    } else {
        return true;
    }

}
if (regex.test(str)) {
    return true;
}
e.preventDefault();
return false;
});

$('.zip-valid').on('keypress', function(e) {

var $this = $(this);
var regex = new RegExp("^[0-9\b]+$");
var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
// for 10 digit number only
if ($this.val().length > 9) {
    e.preventDefault();
    return false;
}
if (e.charCode < 52 && e.charCode > 47) {
    if ($this.val().length == 0) {
        e.preventDefault();
        return false;
    } else {
        return true;
    }

}
if (regex.test(str)) {
    return true;
}
e.preventDefault();
return false;
});




});
</script>


@endsection

