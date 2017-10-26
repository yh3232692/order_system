$(function () {
    (function () {
        $.jgrid.defaults.styleUI = 'Bootstrap';
        var h = document.documentElement.clientHeight || document.body.clientHeight;
        var jq_data = new Object();
        // 初始化jqGrid实时数据表格
        $('#order').jqGrid({
            data: [],
            datatype: "local",
            shrinkToFit: true,
            //列名称
            colNames:['窗口名','饭点','学号','订单总价','点餐时间','','','','',''],
            // 常用到的属性
            colModel:[
                {name:'window_name'},
                {name:'meals_time_info'},
                {name:'student_id'}, 
                {name:'order_price'},
                {name:'order_time'},
                {name:'id',hidden:true},
                {name:'window_id',hidden:true},
                {name:'date',hidden:true},
                {name:'meals_time_id',hidden:true},
                {name:'order_info',hidden:true}              
            ],
            // multiselect: true,              //添加多选框
            rownumbers : false,             //是否显示行号  
            loadonce:true,
            rowNum : 20,                    // 默认的每页显示记录条数  
            rowList : [20,40,60],         // 可供用户选择的每页显示记录条数。  
            pager : '#order_pager',         // 导航条对应的Div标签的ID,注意一定是DIV，不是Table  
            sortname : 'SYS_RES_ID',        // 默认的查询排序字段  
            viewrecords : true,             // 定义是否在导航条上显示总的记录数  
            autowidth : true,               //定义表格是否自适应宽度
            caption: "订单详情展示",      //定义表格的标题
            height:h-200,
            hidegrid: true,                 //定义是否可以显示隐藏表格
            onSelectRow:function (rowid,status) {
                show_order_info(rowid);
            },
            loadComplete:function(xhr){
                // console.log(xhr); 
            },
        });
        //加载工具栏，并且隐藏之前页面定义的按钮
        $("#order").jqGrid('navGrid', '#order_pager', {
            edit: false,
            add: false,
            del: false,
            search: true
        }, {
            height: 200,
            reloadAfterSubmit: true
        });
        // ajax请求初始化数据数据对jqgrid进行数据渲染
        function get_window_jqgrid () {
            async('../Allorder/show_orders','post','',function (data) {
                if (data.code == 0) {
                    jq_data = data.message;
                    for (var i = 0; i < jq_data.length; i++) {
                        jq_data[i].id = jq_data[i]._id.$id;                        
                    }
                    $("#order").jqGrid('clearGridData');  //清空表格
                    $("#order").jqGrid('setGridParam',{  // 重新加载数据
                        datatype:'local',
                        data :jq_data, 
                    }).trigger("reloadGrid");
                }            
            });
        }
        get_window_jqgrid();
        // 初始化所有窗口下拉框内容
        async('../Window/jqgrid_all','post','',function (data) {
            var window_select = document.getElementById('window_select');        
            if (data.code == 0) {
                var data = data.message;
                create_window_options(window_select,data);
            }
        })
        // 加载时间插件
        var start_time = {
            elem: '#start',
            event: 'focus',
            format: 'YYYY-MM-DD',
            istime: false,
            istoday: true, 
        };
        laydate(start_time);

        var choose_sub = document.getElementById('choose_sub');
        choose_sub.addEventListener('click',function () {
            var choose_data = $('#choose_form').serialize();
            async('../Allorder/choose_order','post',choose_data,function (data) {
                var message = data.message;
                if (data.code == 0) {
                    $("#order").jqGrid('clearGridData');  //清空表格
                    $("#order").jqGrid('setGridParam',{  // 重新加载数据
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

        // 构造tr表格
        var order_info = document.getElementById('order_info');
        function show_order_info (rowid) 
        {
            var getIdRow = $("#order").jqGrid("getRowData",rowid);
            $('#showModal').modal('show');  
            async('../Allorder/get_order_info','post',{order_id:rowid,window_id:getIdRow.window_id},function (data) {
                var message = data.message;
                var html = '';
                if (data.code == 0) {
                    for (var i = 0; i < message.length; i++) {
                        html += "<tr><td>"+message[i].cook_menu_name+"</td><td>"+message[i].num+"份</td><td>"+message[i].price+"</td><td>"+message[i].total_price+"</td></tr>";
                    }
                    order_info.innerHTML = html;       
                }
                if (data.code == -1) {
                    swal({
                        title: "",
                        text: message
                    });
                }
            })
        }
        // 构造窗口的下拉菜单的方法
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
    })();
});