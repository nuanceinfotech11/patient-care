<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.patient_name')}}</th>
      <!-- <th>{{__('locale.date')}}</th>
      <th>{{__('locale.time')}}</th> -->
      <th>{{__('locale.carer_name')}}</th>
      <th>{{__('locale.care_home_code')}}</th>
      <!-- <th>{{__('locale.attended')}}</th>
      <th>{{__('locale.remarks')}}</th>
      <th>{{__('locale.attended_remarks')}}</th> -->
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($patientcarerResult))
    @foreach($patientcarerResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    @if(isset($user_value->patientname->name)&& $user_value->patientname->name!='')
    <td>{{$user_value->patientname->name}}</td>
    @endif
    <!-- <td>{{$user_value->date}}</td>
    <td>{{$user_value->attended_on_time}}</td> -->
    @if(isset($user_value->carername->name)&& $user_value->carername->name!='')
    <td>{{$user_value->carername->name}}</td>
    @endif
    
    @if(isset($user_value->comp->company_name)&& $user_value->comp->company_name!='')
    <td>{{$user_value->comp->company_name}}</td>
    @endif
    <!-- <td>{{$user_value->attended}}</td>
    <td>{{$user_value->remarks}}</td>
    <td>{{$user_value->attended_remarks}}</td> -->
    
    
    <td>
      @if($editUrl=='patient-carer-map-edit')
        
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-patient-carer-map-edit')
        
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
     @if($editUrl=='manager-patient-carer-map-edit')
        
     <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
     @endif
      @if($deleteUrl=='patient-carer-map-delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='admin-patient-carer-map-delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='manager-patient-carer-map-delete')
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
@if(isset($patientcarerResult) && !empty($patientcarerResult))
{!! $patientcarerResult->links('panels.paginationCustom') !!}
@endif
