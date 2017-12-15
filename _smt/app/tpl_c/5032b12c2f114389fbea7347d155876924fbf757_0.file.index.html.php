<?php
/* Smarty version 3.1.30, created on 2017-12-15 13:47:30
  from "/data/vnnox-deploy/tpl/index/index.html" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_5a3361f223e1a2_51971702',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5032b12c2f114389fbea7347d155876924fbf757' => 
    array (
      0 => '/data/vnnox-deploy/tpl/index/index.html',
      1 => 1513234109,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:../index/head.html' => 1,
    'file:../index/nav.html' => 1,
    'file:../index/bottom.html' => 1,
  ),
),false)) {
function content_5a3361f223e1a2_51971702 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>历史版本</title>
    <?php $_smarty_tpl->_subTemplateRender("file:../index/head.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>
<body>
<div class="container-fluid" style="margin-left:300px;">
    <?php $_smarty_tpl->_subTemplateRender("file:../index/nav.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


    <div class="mainBody">
        <div class="clearfix">
            <div class="left search">
                <form action="<?php echo $_smarty_tpl->tpl_vars['myf_path']->value;?>
/" id="searchForm" method="get">
                    <div class="input-group">
                        <input type="hidden" name="playerUuid" value="<?php echo $_smarty_tpl->tpl_vars['playerUuid']->value;?>
" id="txtPlayerUuid">
                        <div class="row">
                            <div class="col-md-5" style="margin:0;padding:0 0 0 15px ">
                                <select class="form-control">
                                    <option value="">所有节点</option>
                                    <option value="beta">beta</option>
                                    <option value="cn">cn</option>
                                    <option value="us">us</option>
                                    <option value="jp">jp</option>
                                    <option value="au">au</option>
                                    <option value="eu">eu</option>
                                </select>
                            </div>
                            <div class="col-md-7" style="margin:0;padding:0 0 0 5px;">
                                <input type="text" class="form-control" id="txtName" name="name" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword']->value, ENT_QUOTES, 'UTF-8', true);?>
" placeholder="名称,支持模糊搜索">
                            </div>
                        </div>
                        <span class="input-group-btn">
                    <button class="btn btn-default" id="btnSearch" type="button">搜索</button>
                </span>
                    </div>
                </form>
            </div>
            <div class="left mr5">
                <button data-href="<?php echo $_smarty_tpl->tpl_vars['myf_path']->value;?>
/deploy/create" data-title="版本发布" id="btnCreate"
                        onclick="openModel('btnCreate')" class="btn btn-primary">版本发布
                </button>
                <button class="btn btn-primary trigger-custom1">pop</button>
            </div>

        </div>


        <div class="split-line"></div>

        <table id="tablePlayer" class="table table-striped table-hover">
            <thead>
            <tr>
                <th class="ckb"><input type="checkbox" id="chkAll"></th>
                <th class="spic">标题</th>
                <th>节点</th>
                <th>TAG</th>
                <th>状态</th>
                <th>创建时间</th>
                <th>操作</th>
            </tr>
            </thead>
            <tbody>
            <?php if (empty($_smarty_tpl->tpl_vars['rows']->value)) {?>
            <tr>
                <td colspan="7">
                    暂无数据
                </td>
            </tr>
            <?php } else { ?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['rows']->value, 'vo');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['vo']->value) {
?>
            <tr data-id="<?php echo $_smarty_tpl->tpl_vars['vo']->value['uuid'];?>
">
                <th><input type="checkbox" class="chk-item" id="chk_<?php echo $_smarty_tpl->tpl_vars['vo']->value['id'];?>
" value="<?php echo $_smarty_tpl->tpl_vars['vo']->value['id'];?>
"></th>
                <td>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['vo']->value['title'], ENT_QUOTES, 'UTF-8', true);?>

                </td>
                <td>
                    <?php echo $_smarty_tpl->tpl_vars['vo']->value['node'];?>

                </td>
                <td>
                    <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['vo']->value['tag'], ENT_QUOTES, 'UTF-8', true);?>

                </td>
                <td>
                    <?php if ($_smarty_tpl->tpl_vars['vo']->value['status'] == 0) {?>
                    <span class=" bg-info vStatus">加密中</span>
                    <?php } elseif ($_smarty_tpl->tpl_vars['vo']->value['status'] == 1) {?>
                    <span class=" bg-success vStatus">成功</span>
                    <?php } else { ?>
                    <span class=" bg-danger vStatus">失败</span>
                    <?php }?>
                </td>
                <td>
                    <?php echo $_smarty_tpl->tpl_vars['vo']->value['create_time'];?>

                </td>
                <td>
                    <a href="javascript:openModel('btnRollBack_<?php echo $_smarty_tpl->tpl_vars['vo']->value['id'];?>
');" id="btnRollBack_<?php echo $_smarty_tpl->tpl_vars['vo']->value['id'];?>
"  data-href="<?php echo $_smarty_tpl->tpl_vars['myf_path']->value;?>
/encode/?tag=<?php echo $_smarty_tpl->tpl_vars['vo']->value['tag'];?>
&type=release" data-title="版本发布">发布</a>
                </td>
            </tr>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl);
?>

            <?php }?>
            </tbody>
        </table>
    </div>

</div>

<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" id="modelDialog" role="document" style="width:800px;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"></h4>
            </div>
            <div class="modal-body">
                <iframe type="text/html" id="modalFrame" name="modalFrame" width="758" height="460" src="" frameborder="0" allowfullscreen=""></iframe>
            </div>
        </div>
    </div>
</div>

<?php $_smarty_tpl->_subTemplateRender("file:../index/bottom.html", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>


<?php echo '<script'; ?>
 type="text/javascript">
    function openModel(id) {
        var obj = $("#" + id);
        var href = obj.data('href');
        var title = obj.data("title");
        $("#myModalLabel").html(title);
        $('#myModal').modal({
            show:true,
            keyboard:false,
            backdrop:'static'
        });
        $('#myModal iframe').attr('src', href);
        $('#myModal').on('shown.bs.modal', function (e) {
            $("#myModal").css("padding-left",'300px')
        })
    }



    function clearMessage() {
        SelfBuild.html("");
    }

    $(".trigger-custom1").on('click', function (event) {
        event.preventDefault();

        iziToast.error({
            title: 'Error',
            message: 'Illegal operation',
            position: 'bottomCenter',
            transitionIn: 'fadeInDown'
        });

        iziToast.warning({
            title: 'Caution',
            message: 'You forgot important data',
            position: 'bottomCenter',
            transitionIn: 'fadeInDown'
        });

        iziToast.success({
            title: 'OK',
            message: 'Successfully inserted record!',
            position: 'bottomCenter',
            transitionIn: 'fadeInDown'
        });
    });
<?php echo '</script'; ?>
>


<?php echo '<script'; ?>
 type="text/javascript">
    initWs();

    function changeChildProgress(value) {
        console.log(typeof $("#modalFrame")[0].contentWindow.changeProgress);
        if(typeof $("#modalFrame")[0].contentWindow.changeProgress =='function'){
            $("#modalFrame")[0].contentWindow.changeProgress(value);
        }else{
            console.log("no frames progress:."+value);
        }
    }

    function setChildProgressError() {
        if(typeof $("#modalFrame")[0].contentWindow.addProgressClass =='function'){
            $("#modalFrame")[0].contentWindow.addProgressClass("progress-bar-danger");
            $("#modalFrame")[0].contentWindow.changeProgress('出错了！');
        }
    }

<?php echo '</script'; ?>
>
</body>
</html><?php }
}
