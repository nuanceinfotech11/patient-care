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

          <?php //echo '<pre>'; print_r($user_result); die; ?>

          @if(isset($user_result->id))

            <form class="formValidate" id="formValidateCompany" method="post" action="{{ isset($user_result) ? route($formUrl, $user_result->id) : route($formUrl) }}">
            <!-- {!! method_field('patch') !!} -->
            @method('PATCH') 
             <!-- // patch method use for updated -->

            @endif

           @csrf()

            <div class="row">
              <div class="col s12 m12">
                <div class="row">
                 
                  <div class="col s12 m6 input-field">
                    <input id="name" name="name" type="text" class="validate"  value="{{(isset($user_result->name)) ? $user_result->name : old('name')}}"
                      data-error=".errorTxt1">
                    <label for="name">{{__('locale.name')}}</label>
                    <small class="errorTxt1"></small>
                  </div>
                 
                  <div class="col s12 m6 input-field">
                    <input id="phonenumber" type="text" name="phone" class="mobile-valid" oninput="this.value=this.value.replace(/[^0-9.,]/g,'');" class="validate"  value="{{(isset($user_result->phone)) ? $user_result->phone : old('phone')}}" data-error=".errorTxt2">
                    <label for="phonenumber">{{__('locale.phone')}}</label>
                    <small class="errorTxt2"></small>
                  </div>
                  <div class="col s12 m6 input-field">
                    <input id="address" name="email" type="text" class="validate" value="{{(isset($user_result->email)) ? $user_result->email: old('email')}}" data-error=".errorTxt3" autocomplete="off"
                    <?php echo isset($user_result->email) ? "readonly" : "" ?>>
                    <label for="email">{{__('locale.email')}}</label>
                    <small class="errorTxt3"></small>
                  </div>

                  <div class="col s12 m6 input-field">
                    <input id="password" name="password" type="password" class="validate" data-error=".errorTxt4" autocomplete="off" >
                    <label for="password">{{__('locale.Password')}}</label>
                    <small class="errorTxt4"></small>
                  </div>   
                
                </div>
              </div>
              
              <div class="col s12 display-flex justify-content-end mt-3">
                <button type="submit" class="btn indigo">
                  Save changes</button>
                <!-- <button type="reset" class="btn btn-light">Cancel</button> -->
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

<script>

  $('.mobile-valid').on('keypress', function(e) {

    var $this = $(this);
    var regex = new RegExp("^[0-9\b]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    // for 10 digit number only
    if ($this.val().length > 9) {
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


</script>
@endsection
