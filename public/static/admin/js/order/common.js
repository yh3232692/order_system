
// 封装ajax,返回json格式的数据
var async = function (url,type,data,onSuccess) {
    $.ajax({
        url:url,
        timeout:30000,
        type:type,
        dataType:'json',
        data:data,
        success : function (data) {
            if (onSuccess) {
                var data = JSON.parse(data);
                console.log(data);
                onSuccess(data);
            }
        },
        error : function (XMLHttpRequest, textStatus, errorThrown) {
            console.log(XMLHttpRequest.status);
            console.log(XMLHttpRequest.readyState);
            console.log(textStatus);
        },
        complete : function (XMLHttpRequest,status) {      //请求完成后最终执行参数
            if (status == 'timeout') {                      //超时,status还有success,error等值的情况
    　　　　　  	alert("超时");
            }
        }
    })
}
// 动态生成下拉框
var create_options = function (element,data) {
    for (var i = 0; i < data.length; i++) {
        var option  =  document.createElement("option");   
		var text  =  document.createTextNode(data[i].name);   
		option.appendChild(text);   
        option.value = data[i].id;
        element.appendChild(option);  
    }
    return element;
}
