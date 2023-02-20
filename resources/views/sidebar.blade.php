<?php
	$path = Request::segment(1);
	$sesion_menu = session('menu7890_2386');
	$menu_session  = json_decode(($sesion_menu));
?>


 @foreach($menu_session as $mnu_induk)
  <?php
  $active = false;
  $expand = false;
  foreach($mnu_induk->child as $mc){
      if($path==$mc->url){
          $active = true;
          $expand = true; //<i class="align-middle" data-feather="package"></i>
      }
  } 
  ?>
  <li class="sidebar-item  @if($expand) active @endif ">
	<a data-bs-target="#mnu_{!! $mnu_induk->id_menu !!}" data-bs-toggle="collapse" class="sidebar-link collapsed">
      {!! $mnu_induk->icon !!} <span class="align-middle">{!! $mnu_induk->nama_menu !!}</span>
    </a>
    	<ul id="mnu_{!! $mnu_induk->id_menu !!}" class="sidebar-dropdown list-unstyled collapse @if($expand)  show @endif " data-bs-parent="#sidebar">
    	  @foreach($mnu_induk->child as $mnu_child)
	      <li class="sidebar-item @if($path==$mnu_child->url) active @endif"><a href="{!! url($mnu_child->url) !!}" 
	      	class="sidebar-link">{!! $mnu_child->nama_menu !!}</a></li>
	      @endforeach
	   	</ul>
  </li>
 @endforeach

 