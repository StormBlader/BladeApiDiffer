<!DOCTYPE html>
<html lang="zh-ch">
<head>
    <meta charset="utf-8">
    <title>ApiDiffer</title>
    <link rel="stylesheet" href="/static/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="/static/bootstrap/css/jquery.json-viewer.css">
    
    <script src="/static/js/jquery.min.js"></script>
    <script src="/static/bootstrap/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="/static/js/jquery.json-viewer.js"></script>
    <style>
        .wrap {
            margin-top: 10px;
            position: relative;
        }

        .params_value {
            width: 75%;
        }

        .params_name, .params_value {
            line-height: 1.5;
            padding: 5px 10px;
        }

        .rm-param {
            margin-left: 10px;
        }

        .params_name:focus, .params_value:focus {
            outline: none;
        }

        input[type='text'] {
            border: 1px solid #ddd;
            border-radius: 2px;
        }

        .link-list {
            position: absolute;
            display: block;
            width: 90%;
            background: #fff;
            list-style-type: none;
            margin-left: 90px;
            cursor: default;
            border: 2px solid #dddddd;
        }

        .link-list:hover {

        }

        .link-list li {
            padding: 10px;
        }

        .link-list li:hover {
            background: #eee;
        }
        body > div > form > ul:focus{
            background: #dddddd;
        }

    </style>
</head>
<body>
	<div class="container wrap">
        <div class="panel-group" id="accordion">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a id="showConfig" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
							点击配置master环境和test环境
						</a>
					</h4>
				</div>

				<div id="collapseTwo" class="panel-collapse collapse">
					<div class="panel-body">

                        <div class="input-group">
                            <span class="input-group-addon" style="color:red">必填</span>
                            <span class="input-group-addon" style="width:200px;">
                            master环境ip端口
                            </span>
                            <span class="input-group-addon" style="width:100px;">
                                <select id="master_protocol" class="selectpicker show-tick form-control" style="display:inline;width:100%;height:40px;">
                                    <option value="HTTP" <?php if($config['master_protocol'] == 'HTTP') { ?> selected="selected" <?php } ?> >HTTP</option>
                                    <option value="HTTPS" <?php if($config['master_protocol'] == 'HTTPS') { ?> selected="selected" <?php } ?>>HTTPS</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" placeholder="master环境ip，不填端口默认80，例如：172.20.1.177:8080"
                                   aria-describedby="basic-addon1" id="master_ip"
                                   style="height:60px;" value="<?=$config['master_ip'] . ":" . $config['master_port']?>"
                            />
                        </div>
                        <br>
						<div class="input-group">
                            <span class="input-group-addon">选填</span>
				            <span class="input-group-addon" style="width:200px;">
                            master环境host
                            </span>
				            <input type="text" class="form-control" placeholder="输入master环境的host地址，如napi.upesn.com"
				                   aria-describedby="basic-addon1" id="master_host"
				                   style="height:60px;" value="<?=$config['master_host']?>"
				            />
				        </div>
                        <br>
                        
                        <div class="input-group">
                            <span class="input-group-addon" style="color:red">必填</span>
                            <span class="input-group-addon" style="width:200px;">
                            test环境ip端口
                            </span>
                            <span class="input-group-addon" style="width:100px;">
                                <select id="test_protocol" class="selectpicker show-tick form-control" style="display:inline;width:100%;height:40px;">
                                    <option value="HTTP" <?php if($config['test_protocol'] == 'HTTP') { ?> selected="selected" <?php } ?> >HTTP</option>
                                    <option value="HTTPS" <?php if($config['test_protocol'] == 'HTTPS') { ?> selected="selected" <?php } ?> >HTTPS</option>
                                </select>
                            </span>
                            <input type="text" class="form-control" placeholder="test环境ip，不填端口默认80，例如：172.20.1.177:8080"
                                   aria-describedby="basic-addon1" id="test_ip"
                                   style="height:60px;" value="<?=$config['test_ip'] . ":" . $config['test_port']?>"
                            />
                        </div>
                        <br>
				        <div class="input-group">
                            <span class="input-group-addon">选填</span>
				            <span class="input-group-addon" style="width:200px;">
                            test环境host
                            </span>
				            <input type="text" class="form-control" placeholder="输入test环境的host地址，如napi.upesn.com"
				                   aria-describedby="basic-addon1" id="test_host"
				                   style="height:60px;" value="<?=$config['test_host']?>"
				            />
				        </div>
				        <br>
				        
						<button class="btn btn-default" id="saveConfig">点击保存</button>
					</div>
				</div>
			</div>
		</div>

        <br>

		<div class="input-group">
            <span class="input-group-addon" id="basic-addon1">
            <select id="requestMethod" class="selectpicker show-tick form-control" style="display:inline;width:100px;height:40px;">
                    <option value="GET">GET</option>
                    <option value="POST">POST</option>
                    <option value="PUT">PUT</option>
                    <option value="DELETE">DELETE</option>
            </select>
            </span>
            <input type="text" class="form-control" placeholder="输入你要测试的url地址，不需要输入host，如/rest/user/getDailyMemberList "
                   aria-describedby="basic-addon1" id="requestUri"
                   style="height:60px;"
            />
            <span class="input-group-addon" id="basic-addon1">
                <button class="btn btn-default" id="requestBtn">请求</button>
            </span>
        </div>

        <br>
        <table id="params_table" class="table table-bordered">
            <thead>
            <tr>
                <th width="35%">参数名</th>
                <th>参数值</th>
            </tr>
            </thead>
            <tbody id="param-body">
            <tr class="params_p" cnt="1">
                
            </tr>
            <tr id="params_end">
                <td colspan="2">
                    <button type="button" class="btn btn-default btn-sm" id="addParam">添加参数</button>
                </td>
            </tr>
            </tbody>
        </table>
        <br>
        <pre id="master-json" class="col-xs-6"></pre>
        <pre id="test-json" class="col-xs-6"></pre>
        
	</div>
    
</body>

<script> 
var uri          = "<?=isset($_SESSION['uri']) ? $_SESSION['uri'] : ''?>";
var method       = "<?=isset($_SESSION['method']) ? $_SESSION['method'] : 'GET'?>";
var params       = new Array();
<?php if(isset($_SESSION['params'])) { ?>
    params       = <?= isset($_SESSION['params'])? $_SESSION['params'] : ''?>;
<?php } ?>


$("#requestUri").val(uri);
$('#requestMethod').val(method);

var params_keys = Object.keys(params);
var params_count = params_keys.length;

$.each(params, function(index, item){
    $("tr.params_p").last().after('' +
                '<tr class="params_p" cnt="' + params_count + '">' +
                '<td><input class="params_name input-text " type="text" name="request_param_key[]" title="参数名称" alt="参数名称" value="' + index + '"' +
                ' ></td>' +
                ' <td><input class="params_value input-text" type="text" name="request_param_value[]" title="参数数值" alt="参数数值" value="' + item +'"' + ' maxlength="2000000000" />' +
                '<button type="button" class="btn btn-default btn-sm rm-param">删除参数</button>' +
                '</td>' +
                ' </tr>' +
                '');
});


$('#saveConfig').click(function(){
	var master_protocol = $('#master_protocol').val();
	var master_host     = $('#master_host').val();
	var master_ip       = $('#master_ip').val();
    
	var test_protocol   = $('#test_protocol').val();
	var test_host       = $('#test_host').val();
	var test_ip         = $('#test_ip').val();

    if($.trim(master_ip) == '' || $.trim(test_ip) == '') {
        alert('请填写完整信息');
        return false;
    }

    $.post(
        '/index/saveConfig', 
        {
            master_protocol : master_protocol,
            master_host : master_host,
            master_ip : master_ip,
            
            test_protocol : test_protocol,
            test_host : test_host,
            test_ip : test_ip
           
        }, 
        function(data){
            if(data.errno != 0) {
                alert('保存失败');
            }else {
                alert('保存成功');
                $("#showConfig").click();
            }
        });
});

$("#requestBtn").click(function(){
    var requestMethod = $("#requestMethod").val();
    var requestUri = $("#requestUri").val();
    var paramKeys = new Array();
    var paramValues = new Array();

    $("input[name='request_param_key[]']").each(function(index,item){
        paramKeys.push($(this).val());
    });

    $("input[name='request_param_value[]']").each(function(index,item){
        paramValues.push($(this).val());
    });

    $("#master-json").empty();
    $("#test-json").empty();
    $.post(
        '/index/requestUri',
        {
            requestMethod : requestMethod,
            requestUri : requestUri,
            paramKeys : paramKeys,
            paramValues : paramValues,
        },
        function(data){
            if(data.errno != 0) {
                alert('请求错误，请检查配置');
            }else {
                
                var master_ret = data.data.master_ret;
                var test_ret = data.data.test_ret;
                $("#master-json").jsonViewer(master_ret);
                $("#test-json").jsonViewer(test_ret);
            }
        });
});


$(function () {
    //添加参数
    $("#addParam").click(function () {
        var length = $("tr.params_p").length;
        var max = $("tr.params_p").eq(0).attr('cnt');
        for (var i = 0; i < length; i++) {
            var current_cnt = parseInt($("tr.params_p").eq(i).attr('cnt'));
            if (current_cnt > max) {
                max = current_cnt;
            }
        }
        max = max + 1;

        $("tr.params_p").last().after('' +
                '<tr class="params_p" cnt="' + max + '">' +
                '<td><input class="params_name input-text " type="text" name="request_param_key[]" title="参数名称" alt="参数名称" value=""' +
                ' ></td>' +
                ' <td><input class="params_value input-text" type="text" name="request_param_value[]" title="参数数值" alt="参数数值" value=""' + ' maxlength="2000000000" />' +
                '<button type="button" class="btn btn-default btn-sm rm-param">删除参数</button>' +
                '</td>' +
                ' </tr>' +
                '');
        //删除参数
        $(".rm-param").on('click', function () {
            $(this).parent().parent().remove();
        });
    });
    //删除参数
    $(".rm-param").on('click', function () {

        $(this).parent().parent().remove();
    });
})

</script>
</html>
