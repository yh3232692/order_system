$(function () {
    (function () {
        $.jgrid.defaults.styleUI = 'Bootstrap';
        var h = document.documentElement.clientHeight || document.body.clientHeight;
        var jq_data = new Object();
        // 初始化jqGrid实时数据表格
        $('#garnish').jqGrid({
            data: [],
            datatype: "local",
            shrinkToFit: true,
            //列名称
            colNames:['窗口','菜名','价格','是否出售半份','半价','排序','所属时间','计划数量','最大数量','有效期','','','','','','','','','',''],
            // 常用到的属性
            colModel:[
                {name:'name_info',},
                {name:'cook_menu_name'},
                {name:'price'},
                {name:'is_half_info'}, 
                {name:'half_price'},
                {name:'sort',},
                {name:'meals_time_info'},
                {name:'plan_number',},
                {name:'max_number',},
                {name:'start_end',},
                {name:'id',hidden:true},
                {name:'advice_price',hidden:true},
                {name:'cook_menu_id',hidden:true},                
                {name:'start_time',hidden:true},
                {name:'end_time',hidden:true},
                {name:'ip',hidden:true},
                {name:'is_half',hidden:true},
                {name:'meals_time_id',hidden:true},
                {name:'window_code',hidden:true},  
                {name:'window_id',hidden:true}                                
            ],
            multiselect: true,              //添加多选框
            rownumbers : false,             //是否显示行号  
            loadonce:true,
            rowNum : 10,                    // 默认的每页显示记录条数  
            rowList : [10, 20, 30],         // 可供用户选择的每页显示记录条数。  
            pager : '#garnish_pager',         // 导航条对应的Div标签的ID,注意一定是DIV，不是Table  
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
        $("#garnish").jqGrid('navGrid', '#garnish_pager', {
            edit: false,
            add: false,
            del: false,
            search: true
        }, {
            height: 200,
            reloadAfterSubmit: true
        });
        // 修改按钮
        $("#garnish").navButtonAdd('#garnish_pager',
        {
            caption: "修改",
            buttonicon: "glyphicon glyphicon-edit",
            onClickButton: function(){
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#garnish").jqGrid("getGridParam","selarrrow");
                // 调用修改的方法
                openEditModal(ids);
            },
            position: "first"
        });
        //删除按钮
        $("#garnish").navButtonAdd('#garnish_pager',
        {
            caption: "删除",
            buttonicon: "ui-icon glyphicon glyphicon-trash",
            // 删除按钮绑定事件
            onClickButton: function () {
                // 获取页面上多选框选中之后返回的id数组
                var ids = $("#garnish").jqGrid("getGridParam","selarrrow");
                // 回调删除的方法
                del(ids);
            },
            position: "first"
        });
        // 添加按钮
        $("#garnish").navButtonAdd('#garnish_pager',
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
            async('../Garnish/jqgrid_all','post','',function (data) {
                if (data.code == 0) {
                    jq_data = data.message;
                    $("#garnish").jqGrid('clearGridData');  //清空表格
                    $("#garnish").jqGrid('setGridParam',{  // 重新加载数据
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
                var getIdRow = $("#garnish").jqGrid("getRowData",ids);
                $('#editModal').modal('show');
                async('../Window/jqgrid_all','post','',function (data) {
                    var windowObj = document.getElementById('editForm').getElementsByTagName('select')[0];  
                    windowObj.innerHTML = '';              
                    if (data.code == 0) {
                        var data = data.message;
                        create_window_options(windowObj,data);
                        for (var i = 0; i < windowObj.length; i++) {
                            if (windowObj[i].value == getIdRow.window_id) {
                                windowObj[i].setAttribute('selected',true);
                            }          
                        }
                    }
                })
                async('../cook_menu/jqgrid_all','post','',function (data) {
                    var cook_menu_obj = document.getElementById('editForm').getElementsByTagName('select')[1];
                    cook_menu_obj.innerHTML = '';
                    if (data.code == 0) {
                        var data = data.message;
                        create_options(cook_menu_obj,data);
                        for (var i = 0; i < cook_menu_obj.length; i++) {
                            if (cook_menu_obj[i].value == getIdRow.cook_menu_id) {
                                cook_menu_obj[i].setAttribute('selected',true);
                            }          
                        }
                    }
                })
                var is_half_obj = document.getElementById('editForm').getElementsByTagName('select')[2];
                for (var i = 0; i < is_half_obj.length; i++) {
                    if (is_half_obj[i].value == getIdRow.is_half) {
                        is_half_obj[i].setAttribute('selected',true);
                    }          
                }

                var meals_time_obj = document.getElementById('editForm').getElementsByTagName('select')[3];
                for (var i = 0; i < meals_time_obj.length; i++) {
                    if (meals_time_obj[i].value == getIdRow.meals_time_id) {
                        meals_time_obj[i].setAttribute('selected',true);
                    }          
                }
                $("#editForm input[name='price']").val(getIdRow.price);
                $("#editForm input[name='plan_number']").val(getIdRow.plan_number);
                $("#editForm input[name='max_number']").val(getIdRow.max_number);
                $("#editForm input[name='start_time']").val(getIdRow.start_time);
                $("#editForm input[name='end_time']").val(getIdRow.end_time);
                $("#editForm input[name='id']").val(getIdRow.id);
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
                        async('../Garnish/update','post',formData,function (data) {
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
                    async('../Garnish/delete','post',{ids:idStr},function (data) {
                        var message = data.message;
                        if (data.code == 0) {
                            swal("删除成功！", "您已经永久删除了这条信息。", "success");
                            get_jqgrid();
                        } else {
                            swal("删除失败！", message, "success");
                        }
                    })
                });
            }
        }
        // 执行添加操作
        function openAddModal () {
            $('#addModal').modal('show');
            
            async('../Window/jqgrid_all','post','',function (data) {
                var windowObj = document.getElementById('addForm').getElementsByTagName('select')[0];  
                windowObj.innerHTML = '';              
                if (data.code == 0) {
                    var data = data.message;
                    create_window_options(windowObj,data);
                }
            })
            async('../cook_menu/jqgrid_all','post','',function (data) {
                var cook_menu_obj = document.getElementById('addForm').getElementsByTagName('select')[1];
                cook_menu_obj.innerHTML = '';
                if (data.code == 0) {
                    var data = data.message;
                    create_options(cook_menu_obj,data);
                }
            })
            $("#addButton").click(function () {
                $("#addForm").submit();
            });
            $('#addForm').validate({
                onsubmit:true,// 是否在提交是验证  
                onfocusout:false,// 是否在获取焦点时验证  
                onkeyup :false,// 是否在敲击键盘时验证 
                rules: {
                    window_id:"required",
                    cook_menu_id:"required",
                    price:'required',
                    plan_number:'required',
                    max_number:'required',
                    is_half:'required',
                    meals_time_id:'required',
                    start_time:'required',
                    end_time:'required',
                },
                messages:{
                    window_id:"窗口不能为空",
                    cook_menu_id:"菜单不能为空",
                    price:'价格不能为空',
                    plan_number:'计划出售数量不能为空',
                    max_number:'最大销售数量不能为空',
                    is_half:'请选择是否可以出售半份',
                    meals_time_id:'请选择时间区间',
                    start_time:'请选择上架时间',
                    end_time:'请选择下架时间',
                },
                submitHandler:function () {
                    // 表单提交之前进行序列化
                    var formData = $('#addForm').serialize();
                    console.log(formData);
                    async('../Garnish/add','post',formData,function (data) {
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
        function create_window_options (element,data) {
            for (var i = 0; i < data.length; i++) {
                var option  =  document.createElement("option");   
                var text  =  document.createTextNode(data[i].name_info);   
                option.appendChild(text);   
                option.value = data[i].id;
                element.appendChild(option);  
            }
            return element;
        }
        
        var start_time = {
            elem: '#addstart',
            event: 'focus',
            format: 'YYYY-MM-DD',
            min: laydate.now(),
            istime: false,
            istoday: true,
            choose: function (datas) {
                end_time.min = datas; //开始日选好后，重置结束日的最小日期
                end_time.start = datas; //将结束日的初始值设定为开始日
            }   
        };
        var end_time = {
            elem: '#addend',
            event: 'focus',
            format: 'YYYY-MM-DD',
            min: laydate.now(),
            istime: false,
            istoday: true,
            choose: function (datas) {
                start_time.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(end_time);
        laydate(start_time);
    

        var start_time = {
            elem: '#editstart',
            event: 'focus',
            format: 'YYYY-MM-DD',
            min: laydate.now(),
            istime: false,
            istoday: true,
            choose: function (datas) {
                end_time.min = datas; //开始日选好后，重置结束日的最小日期
                end_time.start = datas; //将结束日的初始值设定为开始日
            }   
        };
        var end_time = {
            elem: '#editend',
            event: 'focus',
            format: 'YYYY-MM-DD',
            min: laydate.now(),
            istime: false,
            istoday: true,
            choose: function (datas) {
                start_time.max = datas; //结束日选好后，重置开始日的最大日期
            }
        };
        laydate(end_time);
        laydate(start_time);
        
        
        $('#editModal').on('hide.bs.modal',function(){
            $('#editButton').unbind('click');
        });
        $('#addModal').on('hide.bs.modal',function(){
            $('#addButton').unbind('click');
        });
    })();
});