<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.doc_number')}}</th>
      <th>{{__('locale.supplier_code')}}</th>
      <th>{{__('locale.inventory_name')}}</th>
      <th>{{__('locale.quantity')}}</th>
      <th>{{__('locale.rate')}}</th>
      <th>{{__('locale.stock_in_by')}}</th>
      <th>{{__('locale.supplier_doc_no')}}</th>
      <th>{{__('locale.date')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($stockResult))
    @foreach($stockResult as $stock_key => $stock_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$stock_value->doc_no}}</td>
    <td>{{$stock_value->supplier_code}}</td>
    @if(isset($stock_value->inventoryname->name) && $stock_value->inventoryname->name!='')
    <td>{{$stock_value->inventoryname->name}}</td>
    @endif
    <td>{{$stock_value->quantity}}</td>
    <td>{{$stock_value->rate}}</td>
    @if(isset($stock_value->stock_in_by) && $stock_value->stock_in_by!='')
    <td>{{$stock_value->stock_in_by}}</td>
    @endif
    <td>{{$stock_value->supplier_doc_no}}</td>
    <td>{{$stock_value->date}}</td>
    
    
    <td>
      @if($editUrl=='stockin-edit')
      <a href="{{route($editUrl,$stock_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-stockin-edit')
        <a href="{{route($editUrl,$stock_value->id)}}"><i class="material-icons">edit</i></a>
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
@if(isset($stockResult) && !empty($stockResult))
{!! $stockResult->links('panels.paginationCustom') !!}
@endif

