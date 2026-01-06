<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.care_home_code')}}</th>
      <th>{{__('locale.medicine_name')}}</th>
      <th>{{__('locale.quantity')}}</th>
      <th>{{__('locale.purcahse_issue_type')}}</th>
      <th>{{__('locale.date')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
  
    @php
    $serialNumber = ($page-1)*$perPage+1;
    @endphp
    
    @if(isset($medicinestockResult))
    @foreach($medicinestockResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$user_value->company->company_name}}</td>
    <td>{{$user_value->medicine->medicine_name}}</td>
    <td>{{$user_value->quantity}}</td>
    <td>{{$user_value->purchase_issue_type}}</td>
    <td>{{$user_value->dates}}</td>
    
    
    
    
    <td>
      @if($editUrl=='superadmin.medicine-stock-management.edit')
        
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin.medicine-stock-management.edit')
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='manager.medicine-stock-management.edit')
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($deleteUrl=='superadmin.medicine-stock-management.delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='admin.medicine-stock-management.delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='manager.medicine-stock-management.delete')
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
@if(isset($medicinestockResult) && !empty($medicinestockResult))
{!! $medicinestockResult->links('panels.paginationCustom') !!}
@endif
