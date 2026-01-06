<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.code')}}</th>
      <th>{{__('locale.name')}}</th>
      <th>{{__('locale.email')}}</th>
      <th>{{__('locale.phone')}}</th>
      <th>{{__('locale.address1')}}</th>
      <th>{{__('locale.country')}}</th>
      <th>{{__('locale.state')}}</th>
      <th>{{__('locale.city')}}</th>
      <th>{{__('locale.zipcode')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($supplierResult) && !empty($supplierResult->items()))
    @foreach($supplierResult as $user_key => $user_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$user_value->code}}</td>
    <td>{{$user_value->name}}</td>
    <td>{{$user_value->email}}</td>
    <td>{{$user_value->phone}}</td>
    <td>{{$user_value->address1}}</td>
    @if(isset($user_value->countryname->name) && $user_value->countryname->name!='')
    <td>{{$user_value->countryname->name}}</td>
    @endif
    @if(isset($user_value->statename->name) && $user_value->statename->name!='')
    <td>{{$user_value->statename->name}}</td>
    @endif
    @if(isset($user_value->cityname->name) && $user_value->cityname->name!='')
    <td>{{$user_value->cityname->name}}</td>
    @endif
    
    <td>{{$user_value->zipcode}}</td>
    
    <td>
      @if($editUrl=='supplier-edit')
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-supplier-edit')
        <a href="{{route($editUrl,$user_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($deleteUrl=='supplier-delete')
        <a href="{{route($deleteUrl,$user_value->id)}}" onclick="return confirm('Are you sure you want to delete this item')"><i class="material-icons">delete</i></a>
      @endif
      @if($deleteUrl=='admin-supplier-delete')
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
@if(isset($supplierResult) && !empty($supplierResult))
{!! $supplierResult->links('panels.paginationCustom') !!}
@endif