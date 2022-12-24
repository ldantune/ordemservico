<!-- Sidebar Navidation Menus-->
<span class="heading">Main</span>
<ul class="list-unstyled">

  <li class="<?php echo (url_is('/') ? 'active' : '') ?>"><a href="<?php echo site_url('/') ?>"> <i class="icon-home"></i>Home </a></li>

  <li class="<?php echo (url_is('usuarios*') ? 'active' : '') ?>"><a href="<?php echo site_url('usuarios') ?>"> <i class="icon-user"></i>Usuários </a></li>
  <li class="<?php echo (url_is('fornecedores*') ? 'active' : '') ?>"><a href="<?php echo site_url('fornecedores') ?>"> <i class="icon-user"></i>Fornecedores </a></li>
  <li class="<?php echo (url_is('itens*') ? 'active' : '') ?>"><a href="<?php echo site_url('itens') ?>"> <i class="icon-list"></i>Itens </a></li>

  <li class="<?php echo (url_is('clientes*') ? 'active' : '') ?>"><a href="<?php echo site_url('clientes') ?>"> <i class="fa fa-users"></i>Clientes </a></li>

  <li class="<?php echo (url_is('grupos*') ? 'active' : '') ?>"><a href="<?php echo site_url('grupos') ?>"> <i class="icon-settings"></i>Grupos & Permissões </a></li>
  <li><a href="charts.html"> <i class="fa fa-bar-chart"></i>Charts </a></li>
  <li><a href="forms.html"> <i class="icon-padnote"></i>Forms </a></li>
  <li><a href="#exampledropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-windows"></i>Example dropdown </a>
    <ul id="exampledropdownDropdown" class="collapse list-unstyled ">
      <li><a href="#">Page</a></li>
      <li><a href="#">Page</a></li>
      <li><a href="#">Page</a></li>
    </ul>
  </li>
  <li><a href="<?php echo site_url("usuarios/editarsenha") ?>"> <i class="fa fa-key"></i>Alterar Senha</a></li>
</ul><span class="heading">Extras</span>
<ul class="list-unstyled">
  <li> <a href="#"> <i class="icon-settings"></i>Demo </a></li>
  <li> <a href="#"> <i class="icon-writing-whiteboard"></i>Demo </a></li>
  <li> <a href="#"> <i class="icon-chart"></i>Demo </a></li>
</ul>