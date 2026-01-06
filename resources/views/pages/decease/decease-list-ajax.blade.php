<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.name')}}</th>
      <th>{{__('locale.symptoms')}}</th>
      <th>{{__('locale.note')}}</th>
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
    <td>{{$user_value->name}}</td>
    <td>{{$user_value->symptoms}}</td>
    <td>{{$user_value->note}}</td>
    
    
    
    <td>
      @if($editUrl=='decease-edit')
        
      <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-decease-edit')
        
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($deleteUrl=='decease-delete')
      <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='admin-decease-delete')
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