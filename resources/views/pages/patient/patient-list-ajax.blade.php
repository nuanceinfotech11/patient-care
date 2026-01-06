<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.patient_name')}}</th>
      <th>{{__('locale.date')}}</th>
      <th>{{__('locale.time')}}</th>
      <th>{{__('locale.carer_name')}}</th>
      <th>{{__('locale.carer_assigned_by')}}</th>
      <th>{{__('locale.alternate_carer_name')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($patientscheduleResult))
    @foreach($patientscheduleResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$user_value->patientname->name}}</td>
    <td>{{$user_value->date}}</td>
    <td>{{$user_value->time}}</td>
    @if(isset($user_value->carername->name)&& $user_value->carername->name!='')
    <td>{{$user_value->carername->name}}</td>
    @endif
    @if(isset($user_value->role->name)&& $user_value->role->name!='')
    <td>{{$user_value->role->name}}</td>
    @endif
    @if(isset($user_value->alternatecarername->name)&& $user_value->alternatecarername->name!='')
    <td>{{$user_value->alternatecarername->name}}</td>
    @endif
    <!-- <td>
      
      {{ isset($user_value->company[0]->company_name) ? $user_value->company[0]->company_name : '' }}
      
    </td>
    <td>{{($user_value->blocked==1) ? 'Blocked' : 'Un-blocked'}}</td> -->
    
    <td>
      @if($editUrl=='patient-schedule-edit')
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
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
@if(isset($patientscheduleResult) && !empty($patientscheduleResult))
{!! $patientscheduleResult->links('panels.paginationCustom') !!}
@endif
