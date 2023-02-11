<!-- Sidebar Navidation Menus-->
<span class="heading">Main</span>
<ul class="list-unstyled">

  <li class="<?php echo (url_is('/') ? 'active' : '') ?>"><a href="<?php echo site_url('/') ?>"> <i class="icon-home"></i>Home </a></li>

  <?php if (usuario_logado()->is_cliente) : ?>
    <li class="<?php echo (url_is('ordens*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('ordens/minhas') ?>"> <i class="fa fa-list"></i>Minhas Ordens </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-ordens')) : ?>
    <li class="<?php echo (url_is('ordens*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('ordens') ?>"> <i class="fa fa-list"></i>Ordem de Serviço </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-itens')) : ?>
    <li class="<?php echo (url_is('itens*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('itens') ?>"> <i class="icon-list"></i>Itens </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-clientes')) : ?>
    <li class="<?php echo (url_is('clientes*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('clientes') ?>"> <i class="fa fa-users"></i>Clientes </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-fornecedores')) : ?>
    <li class="<?php echo (url_is('fornecedores*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('fornecedores') ?>"> <i class="icon-user"></i>Fornecedores </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-contas')) : ?>
    <li class="<?php echo (url_is('contas*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('contas') ?>"> <i class="fa fa-usd"></i>Contas Pagar </a>
    </li>
  <?php endif; ?>


  <?php if (usuario_logado()->temPermissaoPara('listar-formas')) : ?>
    <li class="<?php echo (url_is('formas*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('formas') ?>"> <i class="fa fa-money"></i>Formas de Pagamentos </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-formas')) : ?>
    <li class="<?php echo (url_is('eventos*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('eventos') ?>"> <i class="fa fa-calendar-o"></i>Eventos</a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('listar-usuarios')) : ?>
    <li class="<?php echo (url_is('usuarios*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('usuarios') ?>"> <i class="icon-user"></i>Usuários </a>
    </li>
  <?php endif; ?>


  <?php if (usuario_logado()->temPermissaoPara('listar-grupos')) : ?>
    <li class="<?php echo (url_is('grupos*') ? 'active' : '') ?>">
      <a href="<?php echo site_url('grupos') ?>"> <i class="icon-settings"></i>Grupos & Permissões </a>
    </li>
  <?php endif; ?>

  <?php if (usuario_logado()->temPermissaoPara('visualizar-relatorios')) : ?>
    <li class="<?php echo (url_is('relatorios*') ? 'active' : '') ?>"><a href="#exampledropdownDropdown" aria-expanded="false" data-toggle="collapse"> <i class="icon-windows"></i>Relatórios </a>
      <ul id="exampledropdownDropdown" class="collapse list-unstyled ">
        <li><a href="<?php echo site_url('relatorios/itens') ?>">Itens</a></li>
        <li><a href="<?php echo site_url('relatorios/ordens') ?>">Ordens de serviço</a></li>
        <li><a href="<?php echo site_url('relatorios/contas') ?>">Contas de fornecedores</a></li>
        <li><a href="<?php echo site_url('relatorios/equipe') ?>">Desempenho da equipe</a></li>
      </ul>
    </li>
  <?php endif; ?>

  <li>
    <a href="<?php echo site_url("usuarios/editarsenha") ?>"> <i class="fa fa-key"></i>Alterar Senha</a>
  </li>
</ul>