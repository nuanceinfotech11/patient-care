<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.name')}}</th>
      <th>{{__('locale.type')}}</th>
      <th>{{__('locale.option')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($inventoryResult) && !empty($inventoryResult->items()))
    @foreach($inventoryResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$user_value->name}}</td>
    <td>{{$user_value->type}}</td>
    <td>{{(isset($user_value->option)) ? $user_value->option : ''}}</td>
    
    
    
    <td>
      @if($editUrl=='inventory-edit')
        
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-inventory-edit')
        
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($deleteUrl=='inventory-delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='admin-inventory-delete')
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
@if(isset($inventoryResult) && !empty($inventoryResult))
{!! $inventoryResult->links('panels.paginationCustom') !!}
@endif