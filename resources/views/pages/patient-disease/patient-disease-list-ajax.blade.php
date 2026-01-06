<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.disease')}}</th>
      <th>{{__('locale.patient')}}</th>
      <th>{{__('locale.care_home_code')}}</th>
      <th>{{__('locale.remark')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($deceaseResult) && !empty($deceaseResult->items()))
    @foreach($deceaseResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    @if(isset($user_value->disease->name) && $user_value->disease->name!='')
    <td>{{$user_value->disease->name}}</td>
    @endif
    @if(isset($user_value->patientname->name) && $user_value->patientname->name!='')
    <td>{{$user_value->patientname->name}}</td>
    @endif
    @if(isset($user_value->carehome->company_name) && $user_value->carehome->company_name!='')
    <td>{{$user_value->carehome->company_name}}</td>
    @endif
    <td>{{$user_value->remark}}</td>
    
    
    
    <td>
      @if($editUrl=='admin-patient-disease-edit')
        
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($deleteUrl=='admin-patient-disease-delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
    </td>      
    </tr>
    @php
    $serialNumber++;
    @endphp
    @endforeach
    @else
    
    <tr>
    <td colspan="10"><p class="center">{{__('locale.no_record_found')}}</p></td>
    </tr>
    @endif
  </tbody>
</table>
@if(isset($deceaseResult) && !empty($deceaseResult))
{!! $deceaseResult->links('panels.paginationCustom') !!}
@endif