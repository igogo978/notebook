<?php
  //20151127 39行 iconv 並不能正確轉換所有big5 字, 改用 mb_convert_encoding
  //http://sweslo17.blogspot.tw/2012/04/big5-erpms-sql-local-cache-phpiconv.html

//pre check everyting.
session_start();
$time_start = microtime(true);
if (empty($_SESSION['session_tea_sn'])){
	$stop_msg = "發生錯誤了. 請回<a href='javascript:history.go(-1)'>上一頁</a> ";
	print $stop_msg; 
}


$dirWritable = array("db","templates_c");

foreach($dirWritable as $dir){
	if (!is_writable($dir)){
		$stop_msg = sprintf("The directory : %s should be writable. <br> Please chmod -R 777 %s",$dir,$dir);
	  print $stop_msg;
	  exit;
	}
}

//program begins from here
require 'vendor/autoload.php';
require_once "../module-cfg.php";
require "Phonetic.class.php";

$update_stud_eng_names = isset($_POST['update_stud_eng_names'])?'yes':'null';
$stop_msg = 'null';
$route = 'mainView';
if(isset($_POST['view_selected'])) {
  $route = $_POST['view_selected'] ;
}

try {
  $phDB = new PDO('sqlite:./db/ph.sqlite');
  $phDB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

}catch (PDOException $e) {
    throw new pdoDbException($e);
}

if ( (empty($_POST['raw_data'])) && (empty($_POST['users_name_data'])) ) {
	print $stop_msg = "發生錯誤了.重新登入系統 ";
	exit ;
}else {

	if (isset($_POST['raw_data'])){
		foreach($_POST['raw_data'] as $id => $name){
			//中文字轉utf8
			$name = mb_convert_encoding(urldecode($name), "UTF-8", "BIG5");
			$name = Phonetic::decimal_notation_converting($name);
			$users_name_data[$id] = $name ;
		}
		}else {
			foreach($_POST['users_name_data'] as $id => $name){
			$users_name_data[$id] = $name ;
			}
		}
}

$action_options = array( 'printView' => '網頁列印',
                        'csv' => 'csv格式',
		        'updateDB' => '寫入資料庫');

$name_format_options = array( 'passport_format' => '護照格式',
			      'passport_format_no_hyphen' => '護照格式無 -',
			      'passport_format_western' => '護照格式western',
			    'common_format' => 'common format');

$pinyin_method_options = array( 'hy' => '漢語拼音',
                                'wg' => 'Wade-Giles拼音',
				'ty' => '通用拼音',
                                'g2' => '國音二式');


//start 初使資料
$stud_users = Array();
foreach($users_name_data as $id => $name){
  $stud_users[$id] =  Phonetic::mb_str_split($name);
}

//$studPh->_users = $stud_users;
$studPh = new UsersNamePhonetic($phDB,$stud_users);

$USERS = $studPh->getUsers() ;

//預設主畫面
if ($route== "mainView"){ 

  $default_pinyin = empty($_POST['set_all_pinyin_metod'])?'hy':$_POST['set_all_pinyin_metod']; //預設使用漢語拼音

  //拼音處理
  $pinyin_selected_value = Array();
  $pattern = "pinyin_select";
  $pinyin_selected_values = $studPh->check_selected($pattern,$_POST);
  $studPh->set_pinyin_method($pinyin_selected_values,$default_pinyin); //設定拼音方式

  $users_name_char_pinyin = ($studPh->set_char_to_pinyin($USERS)); //每個字的查詢結果
  $users_name_multiph = $studPh->set_multiph($USERS);  //只包含名字中 有多音字的部份

  //print_r($users_name_multiph); 
  //end 初使資料

  //多音字處理, 只取出有多音字部份  方便標示選擇哪個注音
  $hanzi_multi_ph = Array();
  $hanzi = Array();

  $tmp = $studPh->show_hanzi_has_multiph($users_name_multiph);
  //$tmp = show_hanzi_has_multiph($users_name_multiph);
  $hanzi = $tmp['hanzi'];
  $hanzi_multi_ph = $tmp['hanzi_multi_ph'] ;


  //print_r($studPh->_users_name_multiph);
  //決定每個字注音的sn值, 撈出_POST傳過來的值
  $pattern = "ph_select";
  $post_ph_selected_values = $studPh->check_selected($pattern,$_POST);  //使用者傳送選擇的多音字

  $user_name_eng = Array();
  $user_name_eng = $studPh->user_name_eng($USERS,$post_ph_selected_values,$users_name_char_pinyin);

  //print_r($user_name_eng); //9901] => Array ( [0] => huang [1] => jun [2] => kai )
  //傳值進來的方法將參考   interface INameFormat
  $_post_name_format=(empty($_POST['set_name_format']))?"passport_format":$_POST['set_name_format'] ;

  $eng_name_format = Array();
  $eng_name_format = $studPh->eng_name_format($user_name_eng,$_post_name_format);
  //english name 到這邊成功產生

}




if ($route== "printView"){ 
    $all_pinyin_method = ["hy","wg","ty","g2"];
    $eng_name_format = Array();
    $hanzi_multi_ph = Array();
    $hanzi = Array();
  foreach($all_pinyin_method as $key => $pinyin_method){


    $default_pinyin = $pinyin_method;

    //拼音處理
    $pinyin_selected_value = Array();
    /*
    $pattern = "pinyin_select";
    $pinyin_selected_values = $studPh->check_selected($pattern,$_POST);
    */
    $studPh->set_pinyin_method($pinyin_selected_values,$default_pinyin); //設定拼音方式

    $users_name_char_pinyin = ($studPh->set_char_to_pinyin($USERS)); //每個字的查詢結果
    $users_name_multiph = $studPh->set_multiph($USERS);  //只包含名字中 有多音字的部份

    //print_r($users_name_multiph); 
    //end 初使資料

    //多音字處理, 只取出有多音字部份  方便標示選擇哪個注音

    $tmp = $studPh->show_hanzi_has_multiph($users_name_multiph);
    //$tmp = show_hanzi_has_multiph($users_name_multiph);
    $hanzi = $tmp['hanzi'];
    $hanzi_multi_ph = $tmp['hanzi_multi_ph'] ;


    //print_r($studPh->_users_name_multiph);
    //決定每個字注音的sn值, 撈出_POST傳過來的值
    $pattern = "ph_select";
    $post_ph_selected_values = $studPh->check_selected($pattern,$_POST);  //使用者傳送選擇的多音字

    $user_name_eng = Array();
    $user_name_eng = $studPh->user_name_eng($USERS,$post_ph_selected_values,$users_name_char_pinyin);

    //print_r($user_name_eng); //9901] => Array ( [0] => huang [1] => jun [2] => kai )
    //傳值進來的方法將參考   interface INameFormat
    $_post_name_format=(empty($_POST['set_name_format']))?"passport_format":$_POST['set_name_format'] ;

    $eng_name_format[$default_pinyin] = $studPh->eng_name_format($user_name_eng,$_post_name_format);
    //english name 到這邊成功產生

  }//end foreach
//print_r($eng_name_format);

}

if ($route != "mainView"){
	$studPh->update($post_ph_selected_values);
}

/*
if ($route == "csv"){

//顯示拼音方式
	$users_pinyin_selected = Array();
	foreach($studPh->getPinyinSelectedValues() as $id => $value){
		$users_pinyin_selected[$id] = $pinyin_method_options[$value];
	}

	//顯示多音字選擇結果
	$hanzi_with_post_selected_ph = Array();
	$str = '';
	foreach($users_multi_ph as $id => $name){
		foreach($name as $pos => $value){
			$char = ($hanzi[$id][$pos]['chinese']);
			$sn = $post_ph_selected_values[$id][$pos];
			$ph = $users_multi_ph[$id][$pos][$sn];
			$str = sprintf("%s %s:%s",$str,$char,$ph);
		}
		$hanzi_with_post_selected_ph[$id] = $str;
		$str = '';
	}

	$csvObj = new ExportCSV;
	$header = array("編號","姓名","拼音方式","多音字","姓名英譯","確認簽名");
	$csv_data = $csvObj->getStuff($header,
					 $users_name_data,
					 $users_pinyin_selected,
					 $hanzi_with_post_selected_ph,
					 $eng_name_format
									);

		$fp = fopen('php://memory', 'w+');
		foreach ($csv_data as $row) {
			fputcsv($fp, $row);
		}

		rewind($fp);
		$csv_file = stream_get_contents($fp);
		fclose($fp);

		header('Content-Type: text/csv');
		header('Content-type: application/vnd.sun.xml.calc') ;
		header('Content-Length: '.strlen($csv_file));
		header('Content-Disposition: attachment; filename="pinyin.csv"');
		print "\xEF\xBB\xBF";  //UTF8 BOM
		exit($csv_file);
		exit;
} //if ($route=="csv"){
*/

$time_end = microtime(true);
$time_elapsed = sprintf("%01.2f",$time_end - $time_start);

//使用 smarty 引擎
$smarty=new Smarty;// instantiates an object $smarty of class Smarty
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

	//$smarty->debugging = true;
	$smarty->assign("my_title",$my_title);
	$smarty->assign("my_title_version",$my_title_version);
	$smarty->assign("users_name_data",$users_name_data);

	$smarty->assign('pinyin_method_options',$pinyin_method_options);
	$smarty->assign('name_format_options',$name_format_options);
	$smarty->assign('action_options',$action_options);

	$smarty->assign("default_pinyin",$default_pinyin);
	$smarty->assign("pinyin_selected_values",$studPh->getPinyinSelectedValues() );
	//print_r($studPh->_pinyin_selected_values);
	$smarty->assign('post_ph_selected_values',$post_ph_selected_values);
	$smarty->assign('hanzi_multi_ph',$hanzi_multi_ph);
	$smarty->assign('hanzi',$hanzi);
	$smarty->assign("eng_name_format",$eng_name_format);
	$smarty->assign("_post_name_format",$_post_name_format);
	$smarty->assign("route",$route);

	$smarty->assign("join_collection",$join_collection);
	$smarty->assign("update_stud_eng_names",$update_stud_eng_names);
	$smarty->assign("time_elapsed",$time_elapsed);
	//$smarty->assign("stop_msg",$stop_msg);
	$smarty->clearAllCache(600);

	$viewTemplate = $route.".tpl";
	$smarty->display("templates/$viewTemplate");

