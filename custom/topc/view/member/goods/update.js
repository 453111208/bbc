﻿/**
 * Created with JetBrains WebStorm.
 * User: zihan
 * Date: 13-1-11
 * Time: 下午8:47
 */
function challs_flash_update(){ //Flash 初始化函数
    var a={};
    //定义变量为Object 类型

    a.title = "选择图片"; //设置组件头部名称

    a.FormName = "Filedata";
    //设置Form表单的文本域的Name属性

    a.url = "update.php";
    //设置服务器接收代码文件

    a.parameter = "";
    //设置提交参数，以GET形式提交,例："key=value&key=value&..."

    a.typefile = ["Images (*.gif,*.png,*.jpg,*jpeg)","*.gif;*.png;*.jpg;*.jpeg;",
        "GIF (*.gif)","*.gif;",
        "PNG (*.png)","*.png;",
        "JPEG (*.jpg,*.jpeg)","*.jpg;*.jpeg;"];
    //设置可以上传文件 数组类型
    //"Images (*.gif,*.png,*.jpg)"为用户选择要上载的文件时可以看到的描述字符串,
    //"*.gif;*.png;*.jpg"为文件扩展名列表，其中列出用户选择要上载的文件时可以看到的 Windows 文件格式，以分号相隔
    //2个为一组，可以设置多组文件类型

    a.newTypeFile = ["Images (*.gif,*.png,*.jpg,*jpeg)","*.gif;*.png;*.jpg;*.jpeg;","JPE;JPEG;JPG;GIF;PNG","GIF (*.gif)","*.gif;","GIF","PNG (*.png)","*.png;","PNG","JPEG (*.jpg,*.jpeg)","*.jpg;*.jpeg;","JPE;JPEG;JPG"];
    //设置可以上传文件，多了一个苹果电脑文件类型过滤 数组类型, 设置了此项，typefile将无效
    //"Images (*.gif,*.png,*.jpg)"为用户选择要上载的文件时可以看到的描述字符串,
    //"*.gif;*.png;*.jpg"为文件扩展名列表，其中列出用户选择要上载的文件时可以看到的 Windows 文件格式，以分号相隔
    //"JPE;JPEG;JPG;GIF;PNG" 分号分隔的 Macintosh 文件类型列表，如下面的字符串所示："JPEG;jp2_;GI

    a.UpSize = 100;
    //可限制传输文件总容量，0或负数为不限制，单位MB

    a.fileNum = 50;
    //可限制待传文件的数量，0或负数为不限制

    a.size = 5;
    //上传单个文件限制大小，单位MB，可以填写小数类型

    a.FormID = ['select','select2'];
    //设置每次上传时将注册了ID的表单数据以POST形式发送到服务器
    //需要设置的FORM表单中checkbox,text,textarea,radio,select项目的ID值,radio组只需要一个设置ID即可
    //参数为数组类型，注意使用此参数必须有 challs_flash_FormData() 函数支持

    a.autoClose = 1;
    //上传完成条目，将自动删除已完成的条目，值为延迟时间，以秒为单位，当值为 -1 时不会自动关闭，注意：当参数CompleteClose为false时无效

    a.CompleteClose = true;
    //设置为true时，上传完成的条目，将也可以取消删除条目，这样参数 UpSize 将失效, 默认为false

    a.repeatFile = true;
    //设置为true时，可以过滤用户已经选择的重复文件，否则可以让用户多次选择上传同一个文件，默认为false

    a.returnServer = true;
    //设置为true时，组件必须等到服务器有反馈值了才会进行下一个步骤，否则不会等待服务器返回值，直接进行下一步骤，默认为false

    a.MD5File = 1;
    //设置MD5文件签名模式，参数如下 ,注意：FLASH无法计算超过100M的文件,在无特殊需要时，请设置为0
    //0为关闭MD5计算签名
    //1为直接计算MD5签名后上传
    //2为计算签名，将签名提交服务器验证，在根据服务器反馈来执行上传或不上传
    //3为先提交文件基本信息，根据服务器反馈，执行MD5签名计算或直接上传，如果是要进行MD5计算，计算后，提交计算结果，在根据服务器反馈，来执行是否上传或不上传

    a.loadFileOrder=true;
    //选择的文件加载文件列表顺序，TRUE = 正序加载，FALSE = 倒序加载

    a.mixFileNum=0;
    //至少选择的文件数量，设置这个将限制文件列表最少正常数量（包括等待上传和已经上传）为设置的数量，才能点击上传，0为不限制

    a.ListShowType = 2;
    //文件列表显示类型：1 = 传统列表显示，2 = 缩略图列表显示（适用于图片专用上传）

    a.InfoDownRight = "已上传：%2% 张 / 等待上传：%1% 张";
    //右下角统计信息的文本设置,文本中的 %1% = 等待上传数量的替换符号，%2% = 已经上传数量的替换符号，例子“等待上传：%1%个  已上传：%2%个”

    a.TitleSwitch = false;
    //是否显示组件头部

    a.ForceFileNum = 0;
    //强制条目数量，已上传和待上传条目相加等于为设置的值（不包括上传失败的条目），否则不让上传, 0为不限制，设置限制后mixFileNum,autoClose和fileNum属性将无效！

    a.autoUpload = false;
    //设置为true时，用户选择文件后，直接开始上传，无需点击上传，默认为false;

    a.adjustOrder = true;
    //设置为true时，用户可以拖动列表，重新排列位置

    a.deleteAllShow = true
    //设置是否显示，全部清除按钮

    a.language = 1;
    //语言包控制，0 自动检测 1 简体中文，2 繁体中文 3 英文

    a.countData = true;
    //是否向服务器端提交组件文件列表统计信息，POST方式提交数据
    //access2008_box_info_max 列表总数量
    //access2008_box_info_upload 剩余数量 （包括当前上传条目）
    //access2008_box_info_over 已经上传完成数量 （不包括当前上传条目)

    a.isShowUploadButton = true;
    //是否显示上传按钮，默认为true

    return a ;
    //返回Object
}

function challs_flash_onComplete(a){ //每次上传完成调用的函数，并传入一个Object类型变量，包括刚上传文件的大小，名称，上传所用时间,文件类型
    var name=a.fileName; //获取上传文件名
    var size=a.fileSize; //获取上传文件大小，单位字节
    var time=a.updateTime; //获取上传所用时间 单位毫秒
    var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
    document.getElementById('show').innerHTML+=name+' --- '+size+'字节 ----文件类型：'+type+'--- 用时 '+(time/1000)+'秒<br><br>'
}

function challs_flash_onCompleteData(a){ //获取服务器反馈信息事件
    document.getElementById('show').innerHTML+='<font color="#ff0000">服务器端反馈信息：</font><br />'+a+'<br />';
}

function challs_flash_onStart(a){ //开始一个新的文件上传时事件,并传入一个Object类型变量，包括刚上传文件的大小，名称，类型
    var name=a.fileName; //获取上传文件名
    var size=a.fileSize; //获取上传文件大小，单位字节
    var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
    document.getElementById('show').innerHTML+=name+'开始上传！<br />';

    return true; //返回 false 时，组件将会停止上传
}

function challs_flash_onStatistics(a){ //当组件文件数量或状态改变时得到数量统计，参数 a 对象类型
    var uploadFile = a.uploadFile; //等待上传数量
    var overFile = a.overFile; //已经上传数量
    var errFile = a.errFile; //上传错误数量
}

function challs_flash_alert(a){ //当提示时，会将提示信息传入函数，参数 a 字符串类型
    document.getElementById('show').innerHTML+='<font color="#ff0000">组件提示：</font>'+a+'<br />';
}

function challs_flash_onCompleteAll(a){ //上传文件列表全部上传完毕事件,参数 a 数值类型，返回上传失败的数量
    document.getElementById('show').innerHTML+='<font color="#ff0000">所有文件上传完毕，</font>上传失败'+a+'个！<br />';
    //window.location.href='http://www.access2008.cn/update'; //传输完成后，跳转页面
}

function challs_flash_onSelectFile(a){ //用户选择文件完毕触发事件，参数 a 数值类型，返回等待上传文件数量
    document.getElementById('show').innerHTML+='<font color="#ff0000">文件选择完成：</font>等待上传文件'+a+'个！<br />';
}

function challs_flash_deleteAllFiles(){ //清空按钮点击时，出发事件
    //返回 true 清空，false 不清空
    return confirm("你确定要清空已选择的图片列表吗?");
}

function challs_flash_onError(a){ //上传文件发生错误事件，并传入一个Object类型变量，包括错误文件的大小，名称，类型
    var err=a.textErr; //错误信息
    var name=a.fileName; //获取上传文件名
    var size=a.fileSize; //获取上传文件大小，单位字节
    var type=a.fileType; //获取文件类型，在 Windows 上，此属性是文件扩展名。 在 Macintosh 上，此属性是由四个字符组成的文件类型
    document.getElementById('show').innerHTML+='<font color="#ff0000">'+name+' - '+err+'</font><br />';
}

function challs_flash_FormData(a){ // 使用FormID参数时必要函数
    try{
        var value = '';
        var id=document.getElementById(a);
        if(id.type == 'radio'){
            var name = document.getElementsByName(id.name);
            for(var i = 0;i<name.length;i++){
                if(name[i].checked){
                    value = name[i].value;
                }
            }
        }else if(id.type == 'checkbox'){
            var name = document.getElementsByName(id.name);
            for(var i = 0;i<name.length;i++){
                if(name[i].checked){
                    if(i>0) value+=",";
                    value += name[i].value;
                }
            }
        }else if(id.type == 'select-multiple'){
            for(var i=0;i<id.length;i++){
                if(id.options[i].selected){
                    if(i>0) value+=",";
                    values += id.options[i].value;
                }
            }
        }else{
            value = id.value;
        }
        return value;
    }catch(e){
        return '';
    }
}

function challs_flash_style(){ //组件颜色样式设置函数
    var a = {};
    /*  整体背景颜色样式 */
    a.backgroundColor=['#fbfbfb','#fbfbfb','#fbfbfb'];	//颜色设置，3个颜色之间过度
    a.backgroundLineColor='#fbfbfb';					   //组件外边框线颜色
    a.backgroundFontColor='#666666';					   //组件最下面的文字颜色
    a.backgroundInsideColor='#FFFFFF';					   //组件内框背景颜色
    a.backgroundInsideLineColor=['#fbfbfb','#fbfbfb'];  //组件内框线颜色，2个颜色之间过度
    a.upBackgroundColor='#ffffff';						  //上翻按钮背景颜色设置
    a.upOutColor='#000000';							  //上翻按钮箭头鼠标离开时颜色设置
    a.upOverColor='#FF0000';							  //上翻按钮箭头鼠标移动上去颜色设置
    a.downBackgroundColor='#ffffff';					  //下翻按钮背景颜色设置
    a.downOutColor='#000000';							  //下翻按钮箭头鼠标离开时颜色设置
    a.downOverColor='#FF0000';							  //下翻按钮箭头鼠标移动上去时颜色设置
    /*  头部颜色样式 */
    a.Top_backgroundColor=['#fbfbfb','#fbfbfb']; 		//颜色设置，数组类型，2个颜色之间过度
    a.Top_fontColor='#333333';							//头部文字颜色
    /*  按钮颜色样式 */
    a.button_overColor=['#0c79ba','#0c79ba'];			//鼠标移上去时的背景颜色，2个颜色之间过度
    a.button_overLineColor='#0c79ba';					//鼠标移上去时的边框颜色
    a.button_overFontColor='#ffffff';					//鼠标移上去时的文字颜色
    a.button_outColor=['#2e9de0','#2e9de0']; 			//鼠标离开时的背景颜色，2个颜色之间过度
    a.button_outLineColor='#2e9de0';					//鼠标离开时的边框颜色
    a.button_outFontColor='#ffffff';					//鼠标离开时的文字颜色
    /* 文件列表样式 */
    a.List_scrollBarColor="#999999"				    //列表滚动条颜色
    a.List_backgroundColor='#ffffff';					//列表背景色
    a.List_fontColor='#999999';						//列表文字颜色
    a.List_LineColor='#eeeeee';						//列表分割线颜色
    a.List_cancelOverFontColor='#cc0000';				//列表取消文字移上去时颜色
    a.List_cancelOutFontColor='#D76500';				//列表取消文字离开时颜色
    a.List_progressBarLineColor='#ffffff';				//进度条边框线颜色
    a.List_progressBarBackgroundColor='#eeeeee';		//进度条背景颜色
    a.List_progressBarColor=['#ff9900','#ff9900'];	//进度条进度颜色，2个颜色之间过度
    /* 错误提示框样式 */
    a.Err_backgroundColor='#eeeeee';					//提示框背景色
    a.Err_fontColor='#ffffff';							//提示框文字颜色
    a.Err_shadowColor='#ffffff';						//提示框阴影颜色
    return a;
}

var isMSIE = (navigator.appName == "Microsoft Internet Explorer");
function thisMovie(movieName){
    if(isMSIE) return window[movieName];
    else return document[movieName];
}