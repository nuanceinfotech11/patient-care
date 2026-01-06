<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.disease')}}</th>
      <th>{{__('locale.inventory')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($deceaseinventoryMappingResult) && !empty($deceaseinventoryMappingResult))
    @foreach($deceaseinventoryMappingResult as $inventory_key => $inventory_map_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{ isset($inventory_map_value->decease->name) ? $inventory_map_value->decease->name : '' }}</td>
    <td>{{ isset($inventory_map_value->inventory->name) ? $inventory_map_value->inventory->name : '' }}</td>
    <td>
    <a href="{{route($editUrl,$inventory_map_value->id)}}"><i class="material-icons">edit</i></a>
      <a href="{{route($deleteUrl,$inventory_map_value->id)}}" onclick="return confirm('Are you sure?')">
      <i class="material-icons">delete</i></a>
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
@if(isset($deceaseinventoryMappingResult) && !empty($deceaseinventoryMappingResult))
{!! $deceaseinventoryMappingResult->links('panels.paginationCustom') !!}
@endif