<?php

abstract class Chinese {
	public	function big52utf8($str){
		return $str = mb_convert_encoding($str,"UTF-8","BIG5");
	}
}

class sfs3Data extends Chinese{

	public function big52utf8($str){
		$str = mb_convert_encoding($str,"UTF-8","BIG5");
		$i=1;
		while ($i != 0){
		//print $string;
			$pattern = '/&#\d+\;/';
			preg_match($pattern, $str, $matches);
			$i = sizeof($matches);
			if ($i !=0){
				$unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
				$str = preg_replace("/$matches[0]/",$unicode_char,$string);
			} //end if
		}
		return $str;

	}
}

class jsonBoard extends sfs3Data{

	public function json($str){

		$str= $this->big52utf8($str);
		$str = $this->stringRevise($str);
		return $str;	
		
	}

	public function stringRevise($str){
		$pattern = "/\s+/";
		//$replacement = '　';
		$replacement = '\u0020';
		return preg_replace($pattern, $replacement, $str);
	}

}


$data = new jsonBoard ;

print $data->json("abc       defg");
