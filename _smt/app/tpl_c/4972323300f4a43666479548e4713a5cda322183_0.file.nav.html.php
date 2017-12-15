<?php
/* Smarty version 3.1.30, created on 2017-12-15 13:47:30
  from "/data/vnnox-deploy/tpl/index/nav.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a3361f22533e8_56067730',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4972323300f4a43666479548e4713a5cda322183' => 
    array (
      0 => '/data/vnnox-deploy/tpl/index/nav.html',
      1 => 1513234109,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5a3361f22533e8_56067730 (Smarty_Internal_Template $_smarty_tpl) {
?>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse"
                    data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#">vnnox发布系统</a>
        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
            <ul class="nav navbar-nav">
                <li <?php if ($_smarty_tpl->tpl_vars['_controller']->value == 'index') {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['myf_path']->value;?>
/">历史版本</a></li>
                <li <?php if ($_smarty_tpl->tpl_vars['_controller']->value == 'data') {?>class="active"<?php }?>><a href="<?php echo $_smarty_tpl->tpl_vars['myf_path']->value;?>
/data/">数据备份/还原</a></li>
            </ul>
            <ul class="nav navbar-nav navbar-right">
                <li><a href="#">Link</a></li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true"
                       aria-expanded="false">Dropdown <span class="caret"></span></a>
                    <ul class="dropdown-menu">
                        <li><a href="#">Action</a></li>
                        <li><a href="#">Another action</a></li>
                        <li><a href="#">Something else here</a></li>
                        <li role="separator" class="divider"></li>
                        <li><a href="#">Separated link</a></li>
                    </ul>
                </li>
            </ul>
        </div><!-- /.navbar-collapse -->
    </div><!-- /.container-fluid -->
</nav><?php }
}
