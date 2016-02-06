<?php
//http://codepad.org/C8A6TWZa
//DATE:20110314
//$Id:$
include "config.php";

//認證
sfs_check();

mb_internal_encoding("Big5");
mb_regex_encoding("Big5");

function gsub($str){
  return $str = mb_ereg_replace("台","臺", $str);
}

function is_match($str){
  $pattern='^(.{3,5})?(臺北|臺中|臺南|高雄)縣';
  return mb_ereg_match($pattern,$str)==1 ? "1" : "0" ;
}

function mb_fix_addr($str){
  $pattern=array('^(.{3,5})?臺北縣','^(.{3,5})?(..)縣','^(.*市)?(.{2,5}?)[鎮|鄉|市]','^(.*)?村');
  $replacement=array('\\1新北市','\\1\\2市','\\1\\2區','\\1里');
  for ($i=0; $i<sizeof($pattern); $i++) {
    $str = mb_ereg_replace($pattern[$i], $replacement[$i], $str);
  }
  return $str;
}

$query="select * from stud_base where stud_study_cond='0'";
$res=$CONN->Execute($query);
while(!$res->EOF) {
  $sn=$res->fields['student_sn'];
    $addr[1]=$res->fields['stud_addr_1'];
    $addr[2]=$res->fields['stud_addr_2'];

  for ($i=1; $i<(sizeof($addr))+1; $i++){
    $str=gsub($addr[$i]);
    if (is_match($str)) {
      $ok=mb_fix_addr($str);
    }else{
      $ok=$str;
    }
    printf("%s<br><font color=blue>%s</font><br>",$addr[$i],$ok);
    $db_str="stud_addr_".$i;
    $$db_str=$ok;
    $query2="update stud_base set stud_addr_1='".addslashes($stud_addr_1)."', stud_addr_2='".addslashes($stud_addr_2)."' where student_sn='$sn'";
    $CONN->Execute($query2);
  }

  $res->MoveNext();
}
?>
