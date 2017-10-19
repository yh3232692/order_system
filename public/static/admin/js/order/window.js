$(function () {
    (function () {
        $.jgrid.defaults.styleUI = 'Bootstrap';
        var h = document.documentElement.clientHeight || document.body.clientHeight;
        var jq_data = new Object();
        // 初始化jqGrid实时数据表格
        $('#window').jqGrid({
            data: [],
            datatype: "local",
            shrinkToFit: true,
            //列名称
            colNames:['窗口号','ip','窗口名','窗口地址','','','','','',''],
            // 常用到的属性
            colModel:[
                {name:'code'},
                {name:'ip'},
                {name:'name_info'}, 
                {name:'dinner_address'},
                {name:'description',hidden:true},
                {name:'dinner_id',hidden:true},
                {name:'dinner_name',hidden:true},
                {name:'floor_id',hidden:true},
                {name:'floor_name',hidden:true},
                {name:'id',hidden:true}              
            ],
            multiselect: true,              //添加多选框
            rownumbers : false,             //是否显示行号  
            loadonce:true,
            rowNum : 10,                    // 默认的每页显示记录条数  
            rowList : [10, 20, 30],         // 可供用户选择的每页显示记录条数。  
            pager : '#windowPager',         // 导航条对应的Div标签的ID,注意一定是DIV，不是Table  
            sortname : 'SYS_RES_ID',        // 默认的查询排序字段  
            viewrecords : true,             // 定义是否在导航条上显示总的记录数  
            autowidth : true,               //定义表格是否自适应宽度
            caption: "窗口详情展示",      //定义表格的标题
            height:h-200,
            hidegrid: true,                 //定义是否可以显示隐藏表格
            onSelectRow:function (rowid,status) {
                
            },
            loadComplete:function(xhr){
                // console.log(xhr); 
            },
        });
        //加载工具栏，并且隐藏之前页面定义的按钮
        $("#window").jqGrid('navGrid', '#windowPager', {
            edit: false,
            add: false,
            del: false,
            search: true
        }, {
            height: 200,
            reloadAfterSubmit: true
        });
        // 修改按钮
        $("#window").navButtonAdd('#windowPager',
        {
            caption: "修改窗口",
            buttonicon: "glyphicon glyphicon-edit",
            onClickButton: function(){
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#window").jqGrid("getGridParam","selarrrow");
                // 调用修改的方法
                openEditModal(ids);
            },
            position: "first"
        });
        //删除按钮
        $("#window").navButtonAdd('#windowPager',
        {
            caption: "删除",
            buttonicon: "ui-icon glyphicon glyphicon-trash",
            // 删除按钮绑定事件
            onClickButton: function () {
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#window").jqGrid("getGridParam","selarrrow");
                // 回调删除的方法
                del(ids);
            },
            position: "first"
        });
        // ajax请求初始化数据数据对jqgrid进行数据渲染
        function get_window_jqgrid () {
            async('../Window/jqgrid_all','post','',function (data) {
                if (data.code == 0) {
                    jq_data = data.message;
                    $("#window").jqGrid('clearGridData');  //清空表格
                    $("#window").jqGrid('setGridParam',{  // 重新加载数据
                        datatype:'local',
                        data :jq_data, 
                    }).trigger("reloadGrid");
                }            
            });
        }
        get_window_jqgrid();
        // 初始化餐厅下拉框内容
        async('../Window/get_dinner','post','',function (data) {
            var choose_dinner = document.getElementById('choose_dinner');        
            if (data.code == 0) {
                var data = data.message;
                create_options(choose_dinner,data);
            }
        })
        // 初始化楼层下拉框内容
        async('../Window/get_floor','post','',function (data) {
            var choose_floor = document.getElementById('choose_floor');        
            if (data.code == 0) {
                var data = data.message;
                create_options(choose_floor,data);
            }
        })

        var choose_sub = document.getElementById('choose_sub');
        choose_sub.addEventListener('click',function () {
            var choose_data = $('#choose_form').serialize();
            async('../Window/choose_data','post',choose_data,function (data) {
                var message = data.message;
                if (data.code == 0) {
                    $("#window").jqGrid('clearGridData');  //清空表格
                    $("#window").jqGrid('setGridParam',{  // 重新加载数据
                        datatype:'local',
                        data :message, 
                    }).trigger("reloadGrid");
                }
                if (data.code == -1) {
                    swal({
                        title: "",
                        text: message
                    });
                }
            })
        },false);
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
                var getIdRow = $("#window").jqGrid("getRowData",ids);
                console.log(getIdRow);
                $('#editModal').modal('show');
                async('../Window/get_dinner','post','',function (data) {
                    var dinnerObj = document.getElementById('editForm').getElementsByTagName('select')[0];  
                    dinnerObj.innerHTML = '';              
                    if (data.code == 0) {
                        var data = data.message;
                        create_options(dinnerObj,data);
                        for (var i = 0; i < dinnerObj.length; i++) {
                            if (dinnerObj[i].value == getIdRow.floor_id) {
                                dinnerObj[i].setAttribute('selected',true);
                            }          
                        }
                    }
                })
                async('../Window/get_floor','post','',function (data) {
                    var floorObj = document.getElementById('editForm').getElementsByTagName('select')[1];
                    floorObj.innerHTML = '';
                    if (data.code == 0) {
                        var data = data.message;
                        create_options(floorObj,data);
                        for (var i = 0; i < floorObj.length; i++) {
                            if (floorObj[i].value == getIdRow.floor_id) {
                                floorObj[i].setAttribute('selected',true);
                            }          
                        }
                    }
                })
                $("#editForm input[name='code']").val(getIdRow.code);
                $("#editForm input[name='id']").val(getIdRow.id);
                $("#editForm input[name='ip']").val(getIdRow.ip);
                $("#editForm textarea[name='description']").val(getIdRow.description);
                $("#editButton").click(function () {
                    $("#editForm").submit();
                });
                $('#editForm').validate({
                    onsubmit:true,// 是否在提交是验证  
                    onfocusout:false,// 是否在获取焦点时验证  
                    onkeyup :false,// 是否在敲击键盘时验证 
                    rules: {
                        code: "required",
                        dinner_id: "required",
                        floor_id: "required",
                    },
                    messages:{
                        code:"窗口号不能为空",
                        dinner_id:"餐厅不能为空",
                        floor_id:'楼层不能为空'
                    },
                    submitHandler:function () {
                        // 表单提交之前进行序列化
                        var formData = $('#editForm').serialize();
                        console.log(formData);
                        // 初始化楼层下拉框内容
                        async('../Window/update','post',formData,function (data) {
                            var message = data.message;
                            if (data.code == 0) {
                                swal({
                                    title: "",
                                    text: message
                                },function () {
                                    $('#editModal').modal('hide');
                                    get_window_jqgrid();
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
        $('#editModal').on('hide.bs.modal',function(){
            $('#editButton').unbind('click');
        });

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
                    async('../Window/delete','post',{ids:idStr},function (data) {
                        var message = data.message;
                        if (data.code == 0) {
                            swal("删除成功！", "您已经永久删除了这条信息。", "success");
                            get_window_jqgrid();
                        } else {
                            swal("删除失败！", message, "success");
                        }
                    })
                });
            }
        }
    })();
});