<?php
// нужно для прекращения работы скрипта при проверке на работоспособность модуля mod_rewrite
if(isset($_GET['test'])) {
    exit;
}
// удаляем файл если он имееться
if(file_exists('.htaccess')) {
    unlink('.htaccess');
}
header('Content-Type: text/html; charset=utf-8');
/*
*	Скрипт для автоматической распаковки файлов системы на хостинге.
*	Проверяет наличие обязательных расширений php, выдает пользователю предупреждение или ошибку в зависимости от критичности отсутствующего расширения.
*	После распаковки архива удаляет его, и переходит на вторй шаг инсталятора - "условия использования".
*/ 
if(!empty($_REQUEST['step'])){
  if($_REQUEST['step']=='upload'){
    uploadFile();
  }
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8"/>
<meta name="keywords" content="#"/>
<meta name="description" content="#"/>
<link href="#" rel="shortcut icon" type="image/x-icon" />
<title>Moguta.CMS | Установка</title>
<style type="text/css">
html{padding:0px;margin:0px;height:100%;}
a{	color: #1198D2;	}
a:hover{	text-decoration: none;	}
body {
    background-image: url('data:image/jpeg;base64, iVBORw0KGgoAAAANSUhEUgAAAAoAAAAICAYAAADA+m62AAAAI0lEQVR42mNgwAS8DEQAXmIU8xJjMi8xmnmJdA4DKW6nIgAAaJ8Ag/Qo5GUAAAAASUVORK5CYII=');
    padding: 0px;
    margin: 0px;
    font-family: Tahoma,  Verdana,  sans-serif;
    font-size: 14px;
}

.clearfix::before,
.clearfix::after {
    content: ' ';
    display: table;
}

.clearfix::after {
    clear: both;
}

.alert{	vertical-align: -3px;}
.feature-list .notify, .feature-list .error{
	display: inline-block;
	padding: 5px 10px;
	font-size: 12px;
	margin: 0 0 0 5px;
}

.agree-blok {
    padding: 15px 20px;
    border-top: 1px solid #ddd;
    text-align: right;
    margin: 0 -20px -20px -20px;
}

.agree-blok label {
    display: inline-block;
}

.feature-list {
    padding: 0;
    list-style: none;
}

.feature-list li {
    margin: 0 0 10px 0;
}

.clear {
    clear: both;
}

.start-install{
    display: inline-block;
    cursor: pointer;
    outline: none;
    background: #fdfdfd;
    background: -moz-linear-gradient(top,  #fdfdfd 0%, #efefef 100%);
    background: -webkit-linear-gradient(top,  #fdfdfd 0%,#efefef 100%);
    background: linear-gradient(to bottom,  #fdfdfd 0%,#efefef 100%);
    filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#fdfdfd', endColorstr='#efefef',GradientType=0 );
    font-size: 14px;
    height: 34px;
    line-height: 32px;
    padding: 0 15px;
    text-align: center;
    box-shadow: 0 1px 1px rgba(0, 0, 0, 0.20);
    border: 1px solid #CCC;
    font-weight: bold;
    color: #666;
    text-decoration: none;
}

.start-install:hover{
		background: #eeeeee; /* Old browsers */
        background: -moz-linear-gradient(top, #eeeeee 0%, #eeeeee 100%); /* FF3.6+ */
        background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#eeeeee), color-stop(100%,#eeeeee)); /* Chrome,Safari4+ */
        background: -webkit-linear-gradient(top, #eeeeee 0%,#eeeeee 100%); /* Chrome10+,Safari5.1+ */
        background: -o-linear-gradient(top, #eeeeee 0%,#eeeeee 100%); /* Opera11.10+ */
        background: -ms-linear-gradient(top, #eeeeee 0%,#eeeeee 100%); /* IE10+ */
		filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#eeeeee', endColorstr='#eeeeee',GradientType=0 ); /* IE6-9 */
        background: linear-gradient(top, #eeeeee 0%,#eeeeee 100%); 
		outline:none;/* W3C */
}

.start-install:active{
    box-shadow: inset 0 0 13px 2px rgba(0,0,0,0.05);
    position: relative;
    top: 1px;
    outline: none;
}

.error{
	display: block;
    margin: 10px 0;
    padding:10px;
	color:#c2646d;
	background: #fdd6da;
	border:1px solid #eca8a8;
	text-align: center;
}

.install-text .error p {
    padding: 0;
    margin: 0;
}

.install-body {
    width: 980px;
    margin: 0 auto;
    height: 100%;
}
.install-body .logo {    margin: 15px 0;}

.center-wrapper {
    background: #fff;
    box-shadow: 0 0 8px rgba(0,0,0,.15);
    border: 1px solid #ddd;
    font-size: 14px;
}

.install-text {
    padding: 20px;
    color: #3E3E3E;
}

.install-text p {
    line-height: 22px;
    padding: 0;
    margin: 0 0 10px 0;
}

span.notify{
	display: inline-block; 
	color:#92862e;
	border:1px solid #e1d260;
	background:#fff6ae;
	padding:10px;
	margin:0 0 10px 0;
}
span.error{
	display: inline-block; 
	padding:10px;
	margin:0 0 10px 0;
}

.start-install:active {
    -moz-box-shadow: 0 0 4px 2px rgba(0, 0, 0, .3) inset;
    -webkit-box-shadow: 0 0 4px 2px rgba(0, 0, 0, .3) inset;
    box-shadow: 0 0 4px 2px #CFCFCF inset;
    position: relative;
    top: 1px;
    outline: none;
}

.widget-table-title {
    background: #f5f5f5;
    border-bottom: 1px solid #ddd;
    padding: 15px 20px;
    font-size: 14px;
}

.widget-table-title .step-list{
    float: right;
    margin: -15px -20px -15px 0;
    padding: 0;
    list-style: none;
}

.widget-table-title .step-list li.passed{
    background: #6F6E6E;
    opacity: 0.3;
}

.widget-table-title .step-list li.active{
    color: #fff;
    background: #57AF57;
}

.widget-table-title .step-list li{
    float: left;
    width: 50px;
    height: 50px;
    font-size: 14px;
    font-weight: bold;
    line-height: 50px;
    text-align: center;
    background: #fff;
    border-left: 1px solid #ddd;
}

.widget-table-title h4 {
    float: left;
    margin: 0;
    font-size: 16px;
}

.install-text h2 {
    font-size: 14px;
}

div.note {
    background: beige;
    padding: 0 10px;
    border-width: 2px;
    border-style: solid;
}

div.note.error {
    border-color: #ff0033;
    color: #ff0033;
}

div.note.warning {
    border-color: #226a66;
    color: #226a66;
    margin-bottom: 10px;
}

div.note.ok {
    border-color: #1ec547;
    color: #1ec547;
}

img.er {
    vertical-align: -2px;
}
div.image-block{text-align:center;}
.opacity{opacity:0.5;filter: alpha(opacity=50);}
</style>
<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
<script type="text/javascript">
function upload(){
  document.getElementById('accept_license').style.display = 'none';
	document.getElementById('button_start_upload').style.display = 'none';
	document.getElementById('ajaxloader').style.display = 'block';
	return false;
}  
	$(document).ready(function(){
	  $('.start-install').prop('disabled', 'disabled');
	  $('.start-install').addClass('opacity');	  
	  $('#agree').change(function() {
      var checkBox = $(this).prop('checked');
      if(checkBox){
        $('.start-install').removeClass('opacity');
      }
      else{
        $('.start-install').addClass('opacity');
      }
	  });
	  $('#agree').change(function() {
		  $('.start-install').prop('disabled', function(i, val) {
        return !val;
		  })
	  });
    $('#button_start_upload').on('click', function() {
     if (!$(this).hasClass('opacity')) {
        upload();
      }
	  });
	});
</script>
</head>
<body>
<?php $_SESSION = array();?>
<div class="install-body">
<div class="image-block"> 
<img class="logo" src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAATsAAAAwCAYAAABubN2wAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJ
bWFnZVJlYWR5ccllPAAAA2ZpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdp
bj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6
eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYwIDYxLjEz
NDc3NywgMjAxMC8wMi8xMi0xNzozMjowMCAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJo
dHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlw
dGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wTU09Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEu
MC9tbS8iIHhtbG5zOnN0UmVmPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvc1R5cGUvUmVz
b3VyY2VSZWYjIiB4bWxuczp4bXA9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8iIHhtcE1N
Ok9yaWdpbmFsRG9jdW1lbnRJRD0ieG1wLmRpZDo5M0VCOEU0NzhCQTZFNjExQjQ1Q0Y0NzUzODkw
QUMxRiIgeG1wTU06RG9jdW1lbnRJRD0ieG1wLmRpZDo3ODk2NDkxMTY2MkMxMUU3QjlCNjkxNzJD
Q0YyQkM0MyIgeG1wTU06SW5zdGFuY2VJRD0ieG1wLmlpZDo3ODk2NDkxMDY2MkMxMUU3QjlCNjkx
NzJDQ0YyQkM0MyIgeG1wOkNyZWF0b3JUb29sPSJBZG9iZSBQaG90b3Nob3AgQ1M2IChXaW5kb3dz
KSI+IDx4bXBNTTpEZXJpdmVkRnJvbSBzdFJlZjppbnN0YW5jZUlEPSJ4bXAuaWlkOjk0RUI4RTQ3
OEJBNkU2MTFCNDVDRjQ3NTM4OTBBQzFGIiBzdFJlZjpkb2N1bWVudElEPSJ4bXAuZGlkOjkzRUI4
RTQ3OEJBNkU2MTFCNDVDRjQ3NTM4OTBBQzFGIi8+IDwvcmRmOkRlc2NyaXB0aW9uPiA8L3JkZjpS
REY+IDwveDp4bXBtZXRhPiA8P3hwYWNrZXQgZW5kPSJyIj8+8C3TvQAAGX1JREFUeNrsXQ90ltV5
fyQ1EhuNRlJjUxFoXBwuNi4ujBYbzAbFA4Vh6dLGYmNlUFgcFg4UBgPxYOHIgepgpbBSMhg06VAH
wshkYDhw9GhlplAZrKlU1nQpwbRZY1PTRncfv9/1u7m5933v+35/EsL7nPOcL/m+971/n+d3n+e5
/6547733KBGqKC/nj2GCfyu4U/v5DsGLBZfi8zkaZHTk6FGKKKKIBj4NSUIalYJ/KPi44MnK93cK
Piz4fsG3Ca4XPDpq8ogiiqg/6EMJWHRDxcc/Cf5LfPVRwfsFf1FwneBRgttg9TFlCf6c4NNRs0cU
UUSXkmX3KQXoVFon+BbBTwv+I8GfFrxD8K8FL8ffEwB+EUUUUUQDHuwqLN9/TPDLgh8XPFLwMcFf
FlwkeJXgPxf8vOCfCP6m4D9NxMKMKKKIInKhK8JMUAgXlkHyB4L/GF/x5ES74J8J/r3gPPBFAFsd
QI/pKsFTBC8SPAbfnRG8SfA/JMEtv1bwDXCrGXivo9jEyPlUNGA0QRFRRJcGhbWoOA7XIHib4B8D
SC4I/qViMbKbmg3QY0TNENwj+B24uM/AFV4geBrAjp/5e498MwFmnOZNALSRALXhggsE56J8Vynv
PSp4M8D0F1G3RxRRZNmFsfKSUY57BB8EQFXBahwFEBsBEOPPGwFkwwCMQYknTL4F0GuLLLuIIorA
Ll1gd6Xg3+FvthK/AuuPQoKZK12AJbkZrnYEdhFdlpQkY6XfKIj+9dfEwDjBcwQXCp5IsZladl/v
19zPVNFHBD8meJ7gjYK/TbGYY1rojhcejLQsopTSyXu2R40wQMCOY3mtgm8G8LELywuTvy94ZhrL
kU+xWeO/BthupXjcMdW0hmLxRRMdELwvQFqrKRbHNBEPfbsd0+H45yQwhw6ykO55WNxHwJxmdxJk
oFzhDOR/DgMPfzYJ3uMQcuDyTte+a8GA5kqLMPja2s6rv4KSV59wW1RZfmNv5NQgxCGWNbkhgeWq
1ud57qeHuN+FZVqi/XYWMrpPWH0NA8mNHQJX9h38z7OovLXs4xRbqsKVGoXPK9PQ6KwgTwr+R8Ed
qXJjYdm9SbFJFRNxh93mmBx39msev2+GBetFYwWvAGi4EIPPWgwOnQGrz4DBk1JzHcFDCv9KDJAm
WgIwUolB4Y4A5TqGgdfWdl79FZS8+oR3HdmWdXGoZ1ailp3QWR5UZgBYecAfjXZuRhvzYFvv4+3U
QVdZNxcK3pBAe6yHTBD07jrLc5no5/kUD3O1Qm8Jg7O6S+uE4IeFjr4kwaY/6V0F6JjeothSFRaG
Ryi2RIULvyONI8w6KAo3fk4/tQsD/QTHZ+cmkA8LzxbBLwYAOoK1xwL6OoDSldj64tn7ZQGsJC7j
bMGvGsBosFGhB9AxVSYikwLkSgWzfr0h+AlYUwxYXWjnUnzHk3gXEOKx5TdasYTn4/0wxABVrfxv
y4/BbS/0sht6eoMAspsE3wW+nWKrNOYAuLk+x0SdawYC2LnQVwV/Ic153gxlPgnQze6Hetc4PJOT
gNufA2tmdgJlZEunES6Fi6v9TAKuYAHyqhrEYOfXF9lh6i+UPUPwegwYExAiYBef969fAY/qavw9
EcZGF2TwjGVA69LkoDJknadDJvxCFfMxILO1eRfFDhZpN3harYLZ47gd3keGtMgvhZ0LL/QjKHMj
8S6Pv4CQ/C6NeU9G/l6LoR+icNvuMgE8ZZbfOxH3UN3GCYip2axDdiUaPNzMZR7laUZ+qmVbbhnd
v4PnX0mzLJzyUMgiw4DY4uF2n7e0Y7UjIG4OAHSc7kFYjAxQS/l9AQjdljDUIfBaWHZT4VpP0fpI
xmz34RmOee5MAOBrkYZsi26t36WbyyDnu78e9Vsq6tcg/j7qC3biwc+Lj+fEw7/tR7D7DPX/Ptpy
xEo2pzHPDLioS1Pgwq7ycJfYPVhO5gmIEijAOENZ6zCatmi/jTPE01TFYuFtsgw0qw2Wazfck3SD
3RSP30zxvk0ADFeaQX0nmdoAollaH5QFqP929DX3y31Cl13fY0CehoGsyNCvkuph+RXD8moIGK4p
BwhvUsAuS5O/fFj2ElydSQKdixvLpuNn+xFkuIJfGyAWJlsnw9Kcp5flNon6zh660EhllFSJrZDx
AB/bTGsTntlqcYvXGEBwoyWt5XCZmjyU7QGKB+Q7AR63pHnQSWdf68TWzoGwg5wwVubC7WUwmRYA
6FSagz4/q32vTuA9hc9FIa26evKeEMw2WJSBaYhHQ2UhdrUSpnC6if143lY2YoAII1saC1Ochx6D
yMOI72rVucwgL6C+wWReVsJxUZepZX52nubSSKqi3jOWlbBEdGKwfNyxTbbBwrgFVm4HDT4qslja
28A6+U5UCJ3NgWX8/sAigO5ECsu/GYNRhaW/TZSlWO1bqXcMUA8JnKP4ZoOpSQc7QdejQW8HuqeK
rsfIka+41Rws5TV3YwaYUPJ6vD9MYfomwa6xWGeTDTEiFxfCJCzPOgKdCniPWFzvKg38TIC+OGC7
7KM0LvruBzJNTLwCa+qQwYXMIv+JqWoYDOfhIiabuhWLvp3ia+OWOL4/HYM5x0Jfor4xOj2vPfj7
WxRyUs4L7G5SRg8O9BUETDsLsYXh5B1z40W8fOwTL0l4DZXigOqfDUChvIa8A+2JEgOOHnwtQ4xK
V44Mg7XU45N+KZnXim0NUVYW0uOG78cpAlthyauDIlL1pNpj4OM+3ekIkCpJj2C3nIxIMnVp/69D
WWeQeSLL5rZv1QZRE9gRvKoWWH28FI0nTWbCgk0Y7G7RgO9vQyA/70p4AwrMQdw6mNbcuZ+ClfEN
wX8Ca44P++TTjD89gIWzirzXQiXDJfCy7rIM8Z1uR8Aabemn4wmAsy0P2yB3nCLSQSnXACT1yv+1
hvd4QsC4xnHYo29kK78dSANYEyzIeuo9c2ojuZ6wSwPyTosbKz2XMYr3UgHQ+5UAvEbBqwVP4GU2
YcDuVn0kEQkFcSsZpXej8iMw4lfCMtoOod+LOMxEujTW/DHxWqQV+EwF1VLfXQmVFJ+pM83asRva
6pB2gcWt7ApZVtMSinx82ravNVu+Z/fnvQA8fJCA3WxLWEG1fs/C1XO17goV6yhVsTopo5madSdd
6DyPd2W82W9iwgR49wL0tikyXw5c4QXTbwmcekbwpCBgN1T7n+NpjwVsEI61/N8gHI25ce9PoRDV
erg6pomJTUkQ2mS9m2lQAi/353Km0WTeFVLr4dbqg6DJjZOWYpdwYVPV3iaXk2fWD8EyswExy8VM
ixfTqVmLNuJ45ix4nLwlcLkyGHB7cDzwoAC8lwWXu4Ado+5vtO8mipeDKPlPyTxrNxhoGYVfNR7G
lWWQKzO4Lk0BXMNOD0ssDNksRfXTpogRmQHhvEVn9hgGClu8LyMJA5kfdVtcTmndzbeAljox8YoF
QJ1XfwgwPyX4ccGfBPjNg6vbA305LDBrpifYiZd5m4jpntcVQYKCWuxhMBDHy3ibzQOUuoWtpw0C
z0HfXY7ASAFcyGzEf8JQmUce58k8YTI2Be3V46HwyVDoVJANqGot9emg+IykLo+mZ9+3crxiWAmS
zWI8hAE4z1K/2R5ym1B7Y6sY7w5hV/dW6CfXfwsffuAXJzOZzn9AwdabceV/PojA7hQ6k09TPpfC
fDZZYjG6UAfZonPUIqQzQlp1EywuhrQqTNt6qi3psZLfZeDxDmUxWZF5AetjsnDbUti/NhdUHnxg
YlPfFRtc4WYlrZEp1geT9SZ3jizQBh11YmK3B4AmfACHADzWTZ4LaEEZ5/uB3WEotU7zBVK6XnjN
J5kcoMFDV6cpn31k36KjAkSQmEyXxUWqCQEOT1iEcp/mepmsQdP6Ow42nzBwk0NZ2ixg52qxFpB5
N0pLCvvXFtPiiZotFp7t4W18QBcfHdVO8XPvJqSo/J0eLuceWPaF1PucQVn+3ZSG5UcC8DoUC7Js
iM/D71qsO770ZsUl7MrKGb0wNDRNZexxcFHDTEyYlqhwHG0Hue9BnmQBrNPUezmK7bw7XhhaksS2
Om5x/VwXuNqeS1WYojjJ7nwl9Y2F1nu4uckMHWRYflunWHcSFKs9ZFB1v5O5Y0taubkuyz3+RfD/
mBoYBwW40DEaOCesMshx7CvsLPHQNJZ1m0cco4Hsyzj8LMbjFgB7zQGE2Arca/ltsQY6bK2ZtoXJ
46X8JniGAxhd3FhTnRiQ/RaBzybzLpVmcjhdIyTNTXJ6ph0VuyE7fIbd9BSCXbaH19EGUOcZ0amw
tk84DCLJPPhDeiztvmAnrDsWpO9ZfmbQcNmoz43+9AABu8dRn7BxgavTWNZWiytIlNhm+DkWN4L3
aL4Ml4mFsxRcBtBgMNxoGXk3W8IVGyzCzUrCi8xfhWVVpuTHyslHOZ0h9/PbbHttVyMPeR5aKVy7
GgDuloDpJQOYUnEm32xNb89R/PTg9QLw8hIss82N9XJztyrW3VxlAPcKs9hoO5mP/fIjaU0ecV3I
u9NiYVyJBt1J/gdcPk2pnd1yIR5t/i5ko0m6Ks1lNrmq5yixOChbLPdZ+kMGyPcCJF4FAK72sPrY
ynzYY6C718OyZ/BZgzxkfs+Q94kvi6nvgmaeCHvWIw8+bv8g0n+ezEdVfYAVFO5sNheqsgy0XKcr
HLnW8L5pzd46WKg8SbFXAF6YQ2h50Ptfip1okmEZtGz0FACM06gAAO70MYpMaVaDD3qUow/hwNJS
5LvJCezEKPEj8fFvHo98CW6E1yb5H1H/bhViBZLHBd2ZQDpD01xuXizZZLCiehJMlxWa9x8nGoTn
skzzKU878kp0oooV50ElHqQTrwFN9G5LBuXPJ6F9nSwwxSUMAq61jtZdOwYa6U7yItuigGWtAzhn
aW0irTAv4GnTylrvYxHa4oA7YSVyGZ6Ah2GcVMOpzHwRTyMsSk7zfl6WEmSLlt/ey08AzLyWMXw/
xcDwG4z4DA77KXZF4nIoQTUqfg0ldnJJui073brrIv/bl1zpOIB/awir+yysw3mO77LgT4ELHQZg
OQZ1m0/duW0mwtvoDtnOYyh1J6zIkIBO+8htu5+ko2SO1/bZZyuUnJ/7JHSCrb/XBRDwujOvmWo+
UecwXPws6NBjFivMbzJhgwJifqGXTgvY9UBu7oPcFcMyvyDq8TPBr0qm2LWsB+G9MRZMFG3w/gqB
IMeys+nPdzJ43diUC0Djzf0rDaPjfghSmFX0PYgz8Skpv6DYpEkLKsR//xzft6PC71rS4enwm5Ic
vwhrQehLJjo8FL0aeR8h+/ovdm/1vZDnHUCIBWktXMcJZD+uvQ357/GIJboMmrVw56ZDKHM83O09
sAhcJwtYCReiPhwnqiTzAQhqPxyAIp4PWaezBrkwgVc5mfeqhom/PkXmNYvlujvPgCeAgAGP467z
YbHxXvcWlEeWtQicr8jTgz7Wsp8+NKM8I8l9n67NNX4WA8MkAPJYhFYKtHY/IGVU1P0DDHK+SlE0
zAjEO25wLPB+KM8Fg+J+0TENDiDztYYXAWwXAXaJ7PfjzvtuAu9/g5QZvgSuUhzIlA2hJ7gKbQDi
5hTlV4LRvAiA0wVw60pBffIV5W6mQXrclO0qRaHHuQC7CrDJDT2AAWmfh4XM1tVMeAd+R6XnARTP
J7ueQfQviGX35QBAR3BZmjCCN4YEu7MUPlDMnfhhWA3s3r6F7xmwf0X2uynTZdkNZOqk1J2WYSIZ
kzwxSOozYAlxvLVgBr9CxbJuDRBiYIvY9RDWtoFQdyewwyzOl0Kkf8EwMvBtYT+h2EXYfnSz5fsr
Abwcf7sRf38Uz38Ebiozn4L8MViZ05VOWknxc/ODUsIxO68LjCOKKM3UfLlU1NWyY6AIcrkLuwZ8
gxUHfPXrB9+m+GW3fsTmL0+i6PE3/u6fyf00Y74KcbxiYfKhovdSsIuhJQ2N9COiQWTpXTZ1dZ2N
nRUgTbaieNbpm2S/Z9U1uD3MAi7vUCxuFiSmo9989DXFtY3ALqKILnewEy4sL1R0OSa9Ba7uZ+Gm
ehEvIHWJoVxP9jghp/H1AHVly049fZlX5y8N0WbZkdhEFNHgdGO/4vM7T+duA3BcdMz3XVh3pT7P
seXmdUE3r7XhKfUvOOTJVpy+HIBneoOeOvzrsI19CczCRhTRoCSOkw/xsep4EuFzPmDE8bC/CgB0
kv7VB8iYeOmJ30zO35DbGqz/sgAVu7P/HaDcu5LQ9vJSkhcpthqcN7uraw/z8B3/1qi44NUUm00u
UaxM/n+18i7HVw/jXT7EQV0x30jxLVnMO6j31rlyhCFOwnK2nZgxSUuH67GC4ivaM1Hml5HWXqUc
hXinRktLxk9r8L+MEedgUHsN+agb3p/UysE8Tkn3ebzH+dsW0eptOk5rU7m3Nlv7fYbyrsp1St/I
1f7cDuruhtVKGsfwjBrDrkM/SJqh1Y1XBKxR0p6P70sM5WE2ndX3HYPsyPrsV+S0RpFTXqwrT2vJ
N+Qj3ytFm5+k+L5kVT73K/2yhOIrHBpRLkk7FFmQcrNEq0OjUta5aM+T6PuxQdxYFqxrPX7nzdL7
Qir8GTSeF+13SIfBcBb5x+9Oe7zP1uvbDnmx630oCWDHyrse4NCBTnoRIJGJDpuLv3MBJMUQsFJF
8TLwvzygkRWKt8WNRr0mQ6gKFWUoQD24PaZCKOR2oF3IkzfuD4cwme6ZzUW+HUiL234V6kR4h2Oq
vHSoGeV4HuXNwrsFWloS7Avwfxaeb4QS8vKUbijAXAU4Td5BJWSrBO1QoQGaSmqbcp7btTYdif8z
FPAtpd5blQq1ZzJQXwaKU2inLYqiyjS3oE7FaDsJiKM1cM7D8zmKXixAH7YD9Nd4PG+iIjwzX5Gn
JfiuWBn8GLDPoR/L0a5ZkBPb1Zx1KMNL6NcngSVyEGd5aUC+ayh+XFyJMig+hHekLGQZ8itS+rQM
+bSgXcahrLm+bqyw6jhe9oBHY/0nxU9VCEO3kvkOA0l8BNMLjmlxoy4k7+OAfugDhGzd+e2ZrRX8
+wSBbjiEmst8N8V2hqxCh1crncgDyXJFmSRoyN+7tPhhBkZpFv7b8Tkd4MeKMQ/PsSu/VVG6EsVK
Hw8FkZf+HIMQ2Qa0IxDafACKXGa0h+KnOMtFwlUU/L6LSpRvjlLmY6iPuuvgLu29H6MOd0L4K2Dt
chvf55HfGrLfWlaCNAs1eagF+ExG+3XAqhyL0M5aDaDWKu+PQb+OhjVSrdQzUwFytUxjUR9VPnYB
tJajLeZCF/gi8wOQk1Jt0Cb0lzyF5TTkqlPr37vxdwv6dBHAsFXp63laW41HX3cDI85AjnYqep+N
9CoM3lsBQLab+m5Hy1Pqkq1hwJ2oTyvadRnq9JIV7ATQTURjeh3pvJxvLqooD3yACJ/a8FV0upfV
yJbOmwHS3QyX27Yc5XXL9zMwIvhdAs5b0ZKxt1dec6ceOHkAiqi2t7ppvtngguiUA0E4RPG9nQ0K
OJLm6khaDAUtgDUiy/CKAqI2Wq24QW2KIs+GsmUA6M45pGUiuc1LntQrqYe892QWovwtitJ2k/fy
qXGwxFZqrp0aAnClYgU81xisYtWrkINtq1a+fK2f9LSXUd+z+kaSed1cCQYJVQflAHcE/XUKlnib
Aq5TEQrJRPudVoDYRrmQI9l3p7S+74T8jlMGDP1kH2n91VPfs/9mUO/99x1KWesofmCBnm9vN1YA
14cF8wv/bhgtVfqeALqDIZV9Aypzrc9zzwVIcwTF9s3dY/mdFzf/1PD9UnRmgUMez1LfrW9hqFUT
WlJGqjZFAdSRWJ8BnkV972fogCCNVoSxWBmVJTVR/H6HA1DGQorf5P4wlGGdQ12kJTERZdyI7xcA
cHlh93UUfpvQeWUgU++lGEPep5K0oE7ZCthn+pRjFSwA2z7V8cj7EYdyy8FmN/W9U0O1nLIUYMvT
LJxW5Z21Wt2YthnStu1+aIZ1LFmlrZC1mYa6L0E7j6LYYnqXA3gnQwZXQo5M99VI+V0Ja3aj8lsZ
gGsWmbfy7VHqq54GVIM+vg1GXJ/j/D9kcBv8To+9qJjPQYmF32UnxtsBY2PbyftiljcNpvKNmNxw
IZ49/i4lh04DZCYjQNsGN6IVo1wGRuz1GJFzMAreraRxFq5IjmbtrIPSHsPv0/H9Bs1imK1ZmdmK
Esr4k8sdIxWwAmQsLk8ZvWXsrZ3M91tMghVQqMRoyqn3IQT1sHhnIs1m/P2wD9itg7V+DAA2Wfne
Rpzegx7pNkH5XFzxegDFdPTvaejVY9R7R1EjLKtyKKq6NbJbcTfVNjkE0KnCM00YqNZ5xK3V0IVO
h2B55yP/GdpgUwbOdTQKWjQ5KtZk70UAVrMiF6p8ZEIPGsh87mSb0i6d2gCTocRfC/zA7jMUWwjc
aXE/Gd2/Lqy6N0Iq+t0Y6X/p8QyfBPwfiLu4EAPCJyCIppNOrtFMeElsjQzzKYssDzf8Dyh59ACs
20kArCNwJ9sVK2I9AKAN4OiyZ3EtOroKAnoKwNBkAbtmhCua8OwExH5mktu9IXIzeQ/ccrlXcg7F
ZxRPkPnIpBJtwkCmRZq1ei/iN4uQTwP5nzW3CcpZDTfoLPmf3rEc7ZGThP5l/ZmCctdACY8Y9KoW
9coGGLnEwBngpiHthwAORyn8oQk9yLvI0E/LAVZ1AOyzDukdQfvXwEKr19plj+JFSI9pngaWC0PU
YynqsAvl7DMh+f8CDAAuD6QgLTkNzAAAAABJRU5ErkJggg==">
</div>  
<div class="center-wrapper step2">
		<div class="widget-table-title clearfix">
			<h4 class="product-table-icon">Добро пожаловать в мастер установки Moguta.CMS</h4>
            <ul class="step-list">
                <li class="step-number active">шаг 1</li>
                <li class="step-number">2</li>
                <li class="step-number ">3</li>
                <li class="step-number last">4</li>
            </ul>
		</div>
		<div class="install-text">
      <?php install();?>
			<div class="clear"></div>
		</div>
	</div>
</div>
</body>
</html>
<?php
//  Установка
function install(){
	testserver();
  checkLaunchingInstall("launching");   
}
//  Проверка сервера
function testserver(){
	  //phpinfo();
  $err = false;
  $requireList = array();
  $desireList = array();
  $memoryLimitWarning = '';
  $sourceOk = '<img src="data:image/jpeg;base64,iVBORw0KGgoAAAANSUhEUgAAAAsAAAAICAYAAAAvOAWIAAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMy1jMDExIDY2LjE0NTY2MSwgMjAxMi8wMi8wNi0xNDo1NjoyNyAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNiAoV2luZG93cykiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MzRENEU4OTkzNEE0MTFFMjkyMDg4QkYwNDQ1ODEzNzUiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MzRENEU4OUEzNEE0MTFFMjkyMDg4QkYwNDQ1ODEzNzUiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDozNEQ0RTg5NzM0QTQxMUUyOTIwODhCRjA0NDU4MTM3NSIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDozNEQ0RTg5ODM0QTQxMUUyOTIwODhCRjA0NDU4MTM3NSIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PljBVMgAAACaSURBVHjaYvz//z8DLtC4kZEdSB0D4u9A7MxCQOFuIDaCCpUwQSWUgVgdSSEXkDoIxLZQIZCmiYwNGxgMgYwTQMwKxJL1/v9fAhVfALL1oQrXAcWCQQyQMwKBmA0qsR+o8BeSwiVAhbEwGxlBHgQqmAJkZ6M5ezZQYRqyANjNQMEcINWFJD4ZXSHcZCSP9YOcBFSYjS2EAAIMAEVuM9o+OC6/AAAAAElFTkSuQmCC"/>';
  $sourceAl = '<img src="data:image/png;base64,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" class="er"/>';
  $sourceEr = '<img src="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAJPSURBVDjLpZPLS5RhFMYfv9QJlelTQZwRb2OKlKuINuHGLlBEBEOLxAu46oL0F0QQFdWizUCrWnjBaDHgThCMoiKkhUONTqmjmDp2GZ0UnWbmfc/ztrC+GbM2dXbv4ZzfeQ7vefKMMfifyP89IbevNNCYdkN2kawkCZKfSPZTOGTf6Y/m1uflKlC3LvsNTWArr9BT2LAf+W73dn5jHclIBFZyfYWU3or7T4K7AJmbl/yG7EtX1BQXNTVCYgtgbAEAYHlqYHlrsTEVQWr63RZFuqsfDAcdQPrGRR/JF5nKGm9xUxMyr0YBAEXXHgIANq/3ADQobD2J9fAkNiMTMSFb9z8ambMAQER3JC1XttkYGGZXoyZEGyTHRuBuPgBTUu7VSnUAgAUAWutOV2MjZGkehgYUA6O5A0AlkAyRnotiX3MLlFKduYCqAtuGXpyH0XQmOj+TIURt51OzURTYZdBKV2UBSsOIcRp/TVTT4ewK6idECAihtUKOArWcjq/B8tQ6UkUR31+OYXP4sTOdisivrkMyHodWejlXwcC38Fvs8dY5xaIId89VlJy7ACpCNCFCuOp8+BJ6A631gANQSg1mVmOxxGQYRW2nHMha4B5WA3chsv22T5/B13AIicWZmNZ6cMchTXUe81Okzz54pLi0uQWp+TmkZqMwxsBV74Or3od4OISPr0e3SHa3PX0f3HXKofNH/UIG9pZ5PeUth+CyS2EMkEqs4fPEOBJLsyske48/+xD8oxcAYPzs4QaS7RR2kbLTTOTQieczfzfTv8QPldGvTGoF6/8AAAAASUVORK5CYII=" class="alert"/>';
  $requireExtentions = array('zip', 'mysqli', 'gd', 'json', 'session', 'curl','ionCube Loader');
  $desireExtentions = array('xmlwriter', 'xmlreader');
  $systemMemoryLimit = 128;
  $modeRewrite = '';

  $current_dir = dirname(__FILE__);
  if(!@mkdir($current_dir."/test", 0777)){
    $err = true;
  }elseif(!@chmod($current_dir."/test", 0777)){
    $err = true;
  }elseif(!$tf=@fopen($current_dir."/test/test.txt", 'w')){
    $err = true;
  }else{
    @fclose($tf);
  }
  if(!@chmod($current_dir."/test/test.txt", 0777)){
    $err = true;
  }elseif(!@unlink($current_dir."/test/test.txt")){
    $err = true;
  }elseif(!@rmdir($current_dir."/test")){
    $err = true;
  }
  if($err){
    $access = $sourceEr.' <span class="error">необходимо установить CHMOD = 755.</span>';
  }else{
    $access = $sourceOk;
  }
  if((!empty($_SERVER['SERVER_SOFTWARE']))&&(!$err)) {
    // пытаемся найти apache
    if(substr_count(mb_strtolower($_SERVER['SERVER_SOFTWARE']), 'apache')) {
      // проверка модреврайта
      if(isModRewrite() == 'true') {
        $modeRewrite = "<li>Поддержка работы модуля mod_rewrite ".$sourceOk."</li>";
      } else {
        $modeRewrite = "<li>Поддержка работы модуля mod_rewrite <span class='notify'>необходимо включить mod_rewrite</span></li>";
      }
    }
    // пытаемся найти apache
    if(substr_count(mb_strtolower($_SERVER['SERVER_SOFTWARE']), 'nginx')) {
      $nginxWarning = '<span class="notify">Для корректной работы системы, необходимо дополнить конфигурационный файл nginx<br>
        Для работы панели управления в блоке <b>location/ {</b> должна быть следующая строка<br>
          <b>if (!-f $request_filename) {rewrite ^(.*)$ /index.php;}</b><br>
      Для защиты конфигурационного файла нужно добавить следующий блок<br>
      <b>location ~* /.*\.(ini)$ { return 502; }</b><br>
      <b>Если вы не понимаете о чем идет речь, скопируйте это сообщение и покажите его администртору вашего сервера</b></span>';
    } else {
      $nginxWarning = '';
    }
  }
  foreach($requireExtentions as $ext){
    if(!extension_loaded($ext)){
      $err = true;
      $requireList[$ext] = $sourceEr.' <span class="error">необходимо подключить php модуль '.$ext.'</span>';
    }else{
      $requireList[$ext] = $sourceOk;
    }
  }
  $desireList['xml'] = $sourceOk;
  foreach($desireExtentions as $ext){
    if(!extension_loaded($ext)){
      if(in_array($ext, array('xmlwriter','xmlreader'))){
        $desireList['xml'] = $sourceAl.' <span class="notify">необходимо подключить php модуль xmlwriter и xmlreader</span>';
      }
      $desireList[$ext] = $sourceAl.' <span class="notify">необходимо подключить php модуль '.$ext.'</span>';
    }else{
      $desireList[$ext] = $sourceOk;
    }
  }  
  if(version_compare(PHP_VERSION, '5.4.0', '<')){
    $phpVersion = $sourceAl.'<span class="notify">Рекомендуем установить PHP не ниже версии 5.4. Текущая версия '.PHP_VERSION.'</span>';
    $err = true;
  }else if(version_compare(PHP_VERSION, '7.3.99', '>')){
    $phpVersion = $sourceAl.'<span class="notify">Рекомендуем установить PHP не выше версии 7.3. Текущая версия '.PHP_VERSION.'</span>';
    $err = true;
  } else {
    $phpVersion = $sourceOk;
  }
 $memoryLimit = str_replace(array('M','m'),'',ini_get('memory_limit'));
  if($memoryLimit < $systemMemoryLimit){
    $memoryLimitWarning = '<span class="notify">Рекомендованный объем, выделяемой для системы памяти, <strong>'.$systemMemoryLimit.'М</strong><br />
      Текущее значение "memory_limit": <strong>'.$memoryLimit.'</strong></span>';
  }
  
  echo '<form action="" method="post">';
  $selectEdition = '<select name="edition">
  <option value="MogutaSaasForPHP">Гипермаркет</option>
  <option value="MogutaGiperForPHP">Гипермаркет</option>
  <option value="MogutaMarketForPHP">Маркет</option>
  <option value="MogutaMiniMarketForPHP">Минимаркет</option>
  <option value="MogutaVitrinaForPHP">Витрина</option>  
  <option value="MogutaRentForPHP">Магазин в аренду</option>  
  </select>';      
  
  echo '<p>Cейчас будет произведена установка Вашего интернет-магазина.</p>
        <h3>Выберите желаемую редакцию Moguta.CMS:</h3>
		<p>'.$selectEdition.' (<a href="http://moguta.ru/downloads" target="_blank">сравнение редакций</a>)</p>
		<p>Вам необходимо иметь базу данных на Вашем хостинге и знать параметры для подключения к ней. 
        Если в процессе установки у Вас возникнут вопросы, Вы можете найти ответы в <a href="http://wiki.moguta.ru/ustanovka-sistemy" target="_blank">документации</a> 
        или на <a href="http://forum.moguta.ru" target="_blank">форуме Moguta.CMS</a></p>
      ';

  echo "<h3>Минимальные системные требования:</h3>";
  echo "<ul class='feature-list'>";
  echo "<li>Версия PHP не ниже 5.4 ".$phpVersion."</li>";
  echo "<li>MySQL с поддержкой MySQLi ".$requireList['mysqli']."</li>";
  echo "<li>Поддержка работы с ZIP архивами ".$requireList['zip']."</li>";
  echo $modeRewrite;
  echo "<li>Поддержка работы с графическими изображениями ".$requireList['gd']."</li>";
  echo "<li>Поддержка работы с XML файлами ".$desireList['xml']."</li>";
	echo "<li>Поддержка работы с данными в формате JSON ".$requireList['json']."</li>";
 
  echo "<li>Открытые права на запись файлов ".$access."</li>";
  echo "<li>Поддержка получения обновлений ".$requireList['curl']."</li>";
  echo "<li>PHP модуль ionCube Loader ".$requireList['ionCube Loader']."</li>";
  echo "</ul>";
  echo '<p><strong>Перед началом установки необходимо ознакомиться с 
  <a href="http://moguta.ru/license" target="_blank">"Лицензионным соглашением и условиями использования"</a>.</strong></p>';
		
  if(!$err){
    echo $nginxWarning;
    echo $memoryLimitWarning;
    echo '<form action="" method="post">
          <div class="agree-blok"> 
      <label id="accept_license">Я прочитал <a href="http://moguta.ru/license" target="_blank">"Условия использования"</a> и согласен с ними <input type="checkbox" name="agree" value="ok" id="agree" ></label>          
		  <button class="start-install opacity" id="button_start_upload" type="submit" name="step" value="upload" disabled><span>Начать установку</span></button>
          <div class="upload-process" id="ajaxloader" style="display:none;float:right;">Идет загрузка файлов системы<br /><img src="http://moguta.ru/downloads/ajax-loader.gif" style="margin-top:5px;float:right;" /></div>		
      </div>';
  }else{
    echo '<div class="error"><p>Дальнейшая установка невозможна!</p></div>';
	  }
  }
  echo '</form>';
function uploadFile(){
  $phpVersion = PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION;
  $edition=$_REQUEST['edition'];

  $nameArhive = $edition.$phpVersion.'.zip';
  $urlArhive = 'http://updata.moguta.ru/downloads/'.$nameArhive ;

  $ch = curl_init($urlArhive);
  $fp = fopen($nameArhive , 'wb');
  curl_setopt($ch, CURLOPT_FILE, $fp);
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_exec($ch);
  curl_close($ch);
  fclose($fp);
  extractZip($nameArhive);

}
  /**
 * Распаковывает архив с обновлением, если он есть в корне сайта.
 * После распаковки удаляет заданый архив. *
 * @param $file - название архива, который нужно распаковать
 * @return bool
 */
function extractZip($file) {
  if (file_exists($file)) {
    $zip = new ZipArchive;
    $res = $zip->open($file, ZIPARCHIVE::CREATE);
    if($res === TRUE){
      $realDocumentRoot = str_replace(DIRECTORY_SEPARATOR.'mg-core'.DIRECTORY_SEPARATOR.'lib', '', dirname(__FILE__));
      $zip->extractTo($realDocumentRoot);
      $zip->close();
      unlink($file);	
      $arrayEdition=array(
      "MogutaSaasForPHP"=>"saas",
	    "MogutaGiperForPHP"=>"giper",
	    "MogutaMarketForPHP"=>"market",
	    "MogutaMiniMarketForPHP"=>"lite",
        "MogutaVitrinaForPHP"=>"vitrina",
	    "MogutaRentForPHP"=>"rent" 
	  );
	  $edition = $arrayEdition[$_REQUEST['edition']];	  
      checkLaunchingInstall("upload",$edition);
      if(extension_loaded('Zend OPcache')){@opcache_reset();}
      header("Location: index.php?step1=go&agree=ok&id=29805");
      exit();
    }else{
      echo '<div class="error"><p>В процессе распаковки произошла непредвиденная ошибка.<br />
		Очистите корневую директорию сайта и попробуйте снова.</p></div>';
    }
  }
}
// отправляет на сервер флаг запуска установщика 
function checkLaunchingInstall($flag = null,$edition="") {
$id = 29805;
if ($id&&$flag) {
	$post = "&installer=".$id."&flag=".$flag;
    $url = "https://moguta.ru/checkinstaller";
     // Иницализация библиотеки curl.
    $ch = curl_init();
    // Устанавливает URL запроса.
    curl_setopt($ch, CURLOPT_URL, $url);
    // При значении true CURL включает в вывод заголовки.
    curl_setopt($ch, CURLOPT_HEADER, false);
    // Куда помещать результат выполнения запроса:
    //  false – в стандартный поток вывода,
    //  true – в виде возвращаемого значения функции curl_exec.
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Нужно явно указать, что будет POST запрос.
    curl_setopt($ch, CURLOPT_POST, true);
    // Здесь передаются значения переменных.
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    // Максимальное время ожидания в секундах.
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15);
    // Выполнение запроса.
    $res = curl_exec($ch);
    curl_close($ch);    
    return $res;
    }
    return false;
  } 

  // функция для проверки мод реврайта
  function isModRewrite() { 
    $result = false;
    if (isset($_SERVER['HTTPS']) &&
      ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
      isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
      $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
        $protocol = 'https://';
    }
    else {
      $protocol = 'http://';
    }
    if(!$result) {
      // создаем стандартный файл htaccess
      createHtAccessForTest(1);
      // отправляем тестовый запрос для проверки перенаправления
      $ch = curl_init($protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'test?test=test');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $res = curl_exec($ch);
      $info = curl_getinfo($ch);
      $result = $info['http_code'];
      curl_close($ch);
    }

    if($result != 200) {
      // создаем измененный файл htaccess
      createHtAccessForTest(2);
      // отправляем тестовый запрос для проверки перенаправления
      $ch = curl_init($protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'test?test=test');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $res = curl_exec($ch);
      $info = curl_getinfo($ch);
      $result = $info['http_code'];
      curl_close($ch);
    }

    if($result != 200) {
      // создаем измененный файл htaccess
      createHtAccessForTest(3);
      // отправляем тестовый запрос для проверки перенаправления
      $ch = curl_init($protocol.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'].'test?test=test');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      $res = curl_exec($ch);
      $info = curl_getinfo($ch);
      $result = $info['http_code'];
      curl_close($ch);
    }

    if(file_exists('.htaccess')) {
      unlink('.htaccess');
    }

    // если ответ сервера 200, то все работает отлично
    if($result == 200) {
      return true;
    } else {
      return false;
    }
  } 

  // функция для создания тестового файла htaccess
  function createHtAccessForTest($var = 1) {
      if($var == 1) {
          $rewriteBase = '#RewriteBase /';
      } else {
          $rewriteBase = 'RewriteBase /';
      }
      if(file_exists('.htaccess')) {
          unlink('.htaccess');
      }
      $htaccess = 'AddType image/x-icon .ico
      AddDefaultCharset UTF-8

      <IfModule mod_rewrite.c>
      ';
      if($var != 3) {
          $htaccess .= 'Options +FollowSymlinks
          Options -Indexes
          ';
      }
      $htaccess .= 'RewriteEngine on
      #запрос к изображению напрямую без запуска движка 
      RewriteCond %{REQUEST_URI} \.(png|gif|ico|swf|jpe?g|js|css|ttf|svg|eot|woff|yml|xml|zip|txt|doc)$
      RewriteRule ^(.*) $1 [QSA,L]

      '.$rewriteBase.'
      #Перенаправление на www.site~
      #RewriteCond %{HTTP_HOST} !^www.
      #RewriteRule (.*) http://www.%{HTTP_HOST}/$1 [R=301,L]
      RewriteCond %{REQUEST_FILENAME} !-f [OR]
      RewriteCond %{REQUEST_URI} \.(ini|php.*)$ 
      RewriteRule ^(.*) index.php [E=HTTP_AUTHORIZATION:%{HTTP:Authorization},L,QSA]
      </IfModule>
      ';
      if($var != 3) {
          $htaccess .= '<IfModule mod_php5.c> 
          php_flag magic_quotes_gpc Off
          </IfModule>';
      }

      @file_put_contents('.htaccess', $htaccess);
      @chmod('.htaccess', 0755);
  }  