angularApp.filter('myFullDateFormatAlt', function myDateFormatAlt($filter){
  return function(text){
    var  tempdate = new Date(text);
    return $filter('date')(tempdate, "dd-MM-yyyy HH:mm:ss");
  }
});