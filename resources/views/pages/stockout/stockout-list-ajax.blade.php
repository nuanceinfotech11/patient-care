<table class="table">
  <thead>
    <tr>
      <th>{{__('locale.s.no')}}</th>
      <th>{{__('locale.doc_number')}}</th>
      <th>{{__('locale.patient_name')}}</th>
      <th>{{__('locale.carer_name')}}</th>
      <th>{{__('locale.inventory_name')}}</th>
      <th>{{__('locale.quantity')}}</th>
      <th>{{__('locale.stock_out_by')}}</th>
      <th>{{__('locale.date')}}</th>
      <th>{{__('locale.action')}}</th>
    </tr>
  </thead>
  <tbody>
    @php
    $serialNumber = ($page - 1) * $perPage + 1; // Calculate the correct serial number
    @endphp
    @if(isset($stockoutResult))
    @foreach($stockoutResult as $stock_key => $stock_value)
    <tr>
    <td>{{$serialNumber}}</td>
    <td>{{$stock_value->doc_no}}</td>
    <td>{{$stock_value->patientname->name}}</td>
    @if(isset($stock_value->carername->name) && $stock_value->carername->name!='')
    <td>{{$stock_value->carername->name}}</td>
    @endif
    @if(isset($stock_value->inventorynameout->name) && $stock_value->inventorynameout->name!='')
    <td>{{$stock_value->inventorynameout->name}}</td>
    @endif
    <td>{{$stock_value->quantity}}</td>
    @if(isset($stock_value->stock_out_by) && $stock_value->stock_out_by!='')
    <td>{{$stock_value->stock_out_by}}</td>
    @endif
    <td>{{$stock_value->date}}</td>
    <td>
      @if($editUrl=='stockout-edit')
      <a href="{{route($editUrl,$stock_value->id)}}"><i class="material-icons">edit</i></a>
      @endif
      @if($editUrl=='admin-stockout-edit')
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
@if(isset($stockoutResult) && !empty($stockoutResult))
{!! $stockoutResult->links('panels.paginationCustom') !!}
@endif

