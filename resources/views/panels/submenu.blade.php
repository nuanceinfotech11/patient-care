<div class="collapsible-body">
  <ul class="collapsible collapsible-sub" data-collapsible="accordion">
    @foreach ($menu as $submenu)
      @php
        $custom_classes="";
        if(isset($submenu->class))
        {
        $custom_classes = $submenu->class;
        }
        
      @endphp
        @if(!empty($permission_arr) && isset($submenu->permission_guard_name) && in_array($submenu->permission_guard_name,$permission_arr))

          <li class="{{(request()->is($submenu->url)) ? 'active' : '' }}">
            <a href="@if(($submenu->url) === 'javascript:void(0)'){{$submenu->url}} @else{{url($submenu->url)}} @endif"
              class="{{$custom_classes}} {{(request()->is($submenu->url)) ? 'active '.$configData['activeMenuColor'] : '' }}"
              @if(!empty($configData['activeMenuColor'])) {{'style=background:none;box-shadow:none;'}} @endif
              {{isset($submenu->newTab) ? 'target="_blank"':''}}>
              <i class="material-icons">radio_button_unchecked</i>
              <span>{{ __('locale.'.$submenu->name)}}</span>
            </a>
            @if (isset($submenu->submenu))
              @include('panels.submenu', ['menu' => $submenu->submenu])
            @endif
          </li>
        @elseif(!empty($permission_arr) && isset($submenu->permission_guard_name) && $submenu->permission_guard_name=='read' && (in_array('update',$permission_arr) || in_array('delete',$permission_arr)))
          <li class="{{(request()->is($submenu->url)) ? 'active' : '' }}">
           
            <a href="@if(($submenu->url) === 'javascript:void(0)'){{$submenu->url}} @else{{url($submenu->url)}} @endif"
              class="{{$custom_classes}} {{(request()->is($submenu->url)) ? 'active '.$configData['activeMenuColor'] : '' }}"
              @if(!empty($configData['activeMenuColor'])) {{'style=background:none;box-shadow:none;'}} @endif
              {{isset($submenu->newTab) ? 'target="_blank"':''}}>
              <i class="material-icons">radio_button_unchecked</i>
              <span>{{ __('locale.'.$submenu->name)}}</span>
            </a>
            @if (isset($submenu->submenu))
              @include('panels.submenu', ['menu' => $submenu->submenu])
            @endif
            
          </li>
        @elseif(!in_array(Auth()->user()->role()->first()->name,['company-admin','company-user']))
          <li class="{{(request()->is($submenu->url)) ? 'active' : '' }}">
           
          <a href="@if(($submenu->url) === 'javascript:void(0)'){{$submenu->url}} @else{{url($submenu->url)}} @endif"
              class="{{$custom_classes}} {{(request()->is($submenu->url)) ? 'active '.$configData['activeMenuColor'] : '' }}"
              @if(!empty($configData['activeMenuColor'])) {{'style=background:none;box-shadow:none;'}} @endif
              {{isset($submenu->newTab) ? 'target="_blank"':''}}>
              <i class="material-icons">radio_button_unchecked</i>
              <span>{{ __('locale.'.$submenu->name)}}</span>
            </a>
            @if (isset($submenu->submenu))
              @include('panels.submenu', ['menu' => $submenu->submenu])
            @endif
            
          </li>
        @endif
          
        
    @endforeach
  </ul>
</div>