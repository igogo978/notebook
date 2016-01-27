<script>

//如果忘記利用var 宣告變數, 則此變數即會變成全域變數, 非常可怕
	function add(x,y){
		var sum1 ;
		sum1 = x+y;	
		sum2 = x+y ;
	}
	add(1,2);
	console.log(sum2);  //結果等於3, 自動成為一個全域變數
	console.log(sum1); //警告訊息, not defined
</script>

