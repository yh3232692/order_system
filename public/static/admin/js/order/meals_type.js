$(function () {
    (function () {
        $.jgrid.defaults.styleUI = 'Bootstrap';
        var h = document.documentElement.clientHeight || document.body.clientHeight;
        var jq_data = new Object();
        // 初始化jqGrid实时数据表格
        $('#meals_type').jqGrid({
            data: [],
            datatype: "local",
            shrinkToFit: true,
            //列名称
            colNames:['名称','编号','图片',''],
            // 常用到的属性
            colModel:[
                {name:'name'},
                {name:'code'}, 
                {name:'img_url'},
                {name:'id',hidden:true},
            ],
            multiselect: true,              //添加多选框
            rownumbers : false,             //是否显示行号  
            loadonce:true,
            rowNum : 10,                    // 默认的每页显示记录条数  
            rowList : [10, 20, 30],         // 可供用户选择的每页显示记录条数。  
            pager : '#meals_type_pager',         // 导航条对应的Div标签的ID,注意一定是DIV，不是Table  
            sortname : 'SYS_RES_ID',        // 默认的查询排序字段  
            viewrecords : true,             // 定义是否在导航条上显示总的记录数  
            autowidth : true,               //定义表格是否自适应宽度
            caption: "楼层详情展示",      //定义表格的标题
            height:h-150,
            hidegrid: true,                 //定义是否可以显示隐藏表格
            onSelectRow:function (rowid,status) {
                
            },
            loadComplete:function(xhr){
                // console.log(xhr); 
            },
        });
        //加载工具栏，并且隐藏之前页面定义的按钮
        $("#meals_type").jqGrid('navGrid', '#meals_type_pager', {
            edit: false,
            add: false,
            del: false,
            search: true
        }, {
            height: 200,
            reloadAfterSubmit: true
        });
        // 修改按钮
        $("#meals_type").navButtonAdd('#meals_type_pager',
        {
            caption: "修改",
            buttonicon: "glyphicon glyphicon-edit",
            onClickButton: function(){
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#meals_type").jqGrid("getGridParam","selarrrow");
                // 调用修改的方法
                openEditModal(ids);
            },
            position: "first"
        });
        //删除按钮
        $("#meals_type").navButtonAdd('#meals_type_pager',
        {
            caption: "删除",
            buttonicon: "ui-icon glyphicon glyphicon-trash",
            // 删除按钮绑定事件
            onClickButton: function () {
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#meals_type").jqGrid("getGridParam","selarrrow");
                // 回调删除的方法
                del(ids);
            },
            position: "first"
        });
        // 添加按钮
        $("#meals_type").navButtonAdd('#meals_type_pager',
        {
            caption: "添加",
            buttonicon: "ui-icon glyphicon glyphicon-plus",
            onClickButton: function(){
                // 调用的方法
                openAddModal();
            },
            position: "first"
        });
        // ajax请求初始化数据数据对jqgrid进行数据渲染
        function get_jqgrid () {
            async('../meals_type/jqgrid_all','post','',function (data) {
                if (data.code == 0) {
                    jq_data = data.message;
                    $("#meals_type").jqGrid('clearGridData');  //清空表格
                    $("#meals_type").jqGrid('setGridParam',{  // 重新加载数据
                        datatype:'local',
                        data :jq_data, 
                    }).trigger("reloadGrid");
                }            
            });
        }
        get_jqgrid();
        // 执行修改操作
        function openEditModal (ids) {
            var idsLength = ids.length;
            if (idsLength == 0) {
                swal({
                    title: "",
                    text: "请选择要执行操作的数据"
                });
            } else if (idsLength > 1) {
                swal({
                    title: "",
                    text: "只能选择修改一条数据"
                });
            } else {
                //获取某一行的数据，并且进行赋值
                var getIdRow = $("#meals_type").jqGrid("getRowData",ids);
                console.log(getIdRow);
                $('#editModal').modal('show');
                $("#editForm input[name='name']").val(getIdRow.name);
                $("#editForm input[name='id']").val(getIdRow.id);
                $("#editForm input[name='code']").val(getIdRow.code);
                $("#editButton").click(function () {
                    $("#editForm").submit();
                });
                $('#editForm').validate({
                    onsubmit:true,// 是否在提交是验证  
                    onfocusout:false,// 是否在获取焦点时验证  
                    onkeyup :false,// 是否在敲击键盘时验证 
                    rules: {
                        code: "required",
                        meals_type_id: "required",
                        meals_type_id: "required",
                    },
                    messages:{
                        code:"窗口号不能为空",
                        meals_type_id:"餐厅不能为空",
                        meals_type_id:'楼层不能为空'
                    },
                    submitHandler:function () {
                        // 表单提交之前进行序列化
                        var formData = $('#editForm').serialize();
                        console.log(formData);
                        // 初始化楼层下拉框内容
                        async('../meals_type/update','post',formData,function (data) {
                            var message = data.message;
                            if (data.code == 0) {
                                swal({
                                    title: "",
                                    text: message
                                },function () {
                                    $('#editModal').modal('hide');
                                    get_jqgrid();
                                });
                            }
                            if (data.code == -1) {
                                swal({
                                    title: "",
                                    text: message
                                });
                            }
                        })
                    }
                });
            } 
        }
        
        //删除的时候执行的操作
        function del(ids){
            // 返回id的数组个数
            var idsLength=ids.length;
            /*
            判断当多选框个数小于1，不执行任何操作
            判断当多选框个数大于1，返回要执行的操作，进行删除
            */
            if(idsLength<1){
                    swal({
                    title: "",
                    text: "请选择要执行操作的数据"
                });
            }else{
                swal({
                    title: "您确定要删除这条信息吗",
                    text: "删除后将无法恢复，请谨慎操作！",
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    confirmButtonText: "删除",
                    cancelButtonText:"取消",
                    closeOnConfirm: false
                }, function () {
                    //定义空字符串用来接收返回多选框返回的字符串，给后台数据库进行传参
                    var idStr = "";
                    ids.forEach(function(obj){
                        idStr += obj+',';
                    })
                    console.log(idStr);
                    async('../meals_type/delete','post',{ids:idStr},function (data) {
                        var message = data.message;
                        if (data.code == 0) {
                            swal("删除成功！", "您已经永久删除了这条信息。", "success");
                            get_jqgrid();
                        } else {
                            swal("删除失败！", message, "error");
                        }
                    })
                });
            }
        }

        // 执行添加操作
        function openAddModal () {
            $('#addModal').modal('show');
            $("#addButton").click(function () {
                $("#addForm").submit();
            });
            $('#addForm').validate({
                onsubmit:true,// 是否在提交是验证  
                onfocusout:false,// 是否在获取焦点时验证  
                onkeyup :false,// 是否在敲击键盘时验证 
                rules: {
                    name: "required",
                    code: "required"
                },
                messages:{
                    name:"名称不能为空",
                    code:"编号不能为空"
                },
                submitHandler:function () {
                    // 表单提交之前进行序列化
                    var formData = $('#addForm').serialize();
                    console.log(formData);
                    async('../meals_type/add','post',formData,function (data) {
                        var message = data.message;
                        if (data.code == 0) {
                            swal({
                                title: "",
                                text: message
                            },function () {
                                $('#addModal').modal('hide');
                                get_jqgrid();
                                $('.reset').click();
                            });
                        }
                        if (data.code == -1) {
                            swal({
                                title: "",
                                text: message
                            });
                        }
                    })
                }
            });
        }
        
        $('#editModal').on('hide.bs.modal',function(){
            $('#editButton').unbind('click');
        });
        $('#addModal').on('hide.bs.modal',function(){
            $('#addButton').unbind('click');
        });
    })();
});