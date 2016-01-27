
var myCar = new Object();
myCar.make = "Ford";
myCar.model = "Mustang";
myCar.year = 1969;


//for-in 迴圈 列舉物件的屬性
for(var i in myCar){
	if(myCar.hasOwnProperty(i)){
		console.log(i,":",myCar[i]);
	}

}
