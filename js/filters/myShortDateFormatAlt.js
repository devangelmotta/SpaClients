angularApp.filter('myShortDateFormatAlt', function myDateFormatShort($filter){
  return function(text){
  	var  tempdate = new Date(text.replace(/-/g,"/"));
    var  tempdate = new Date(text);
    return $filter('date')(tempdate, "dd-MM-yyyy");
  }
});