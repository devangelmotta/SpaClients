<?php require $_SERVER['DOCUMENT_ROOT'].'/php/enviroment.php'; ?>
<!DOCTYPE html>
<html ng-app="support" ng-strict-di ng-cloak>
<head>
  <title>supportApp</title>
  <!--META-->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <meta http-equiv="Content-type" content="text/html; charset=utf-8">
  <!--NOSCRIPT-->
  <noscript><meta http-equiv="Refresh" content="0; URL=./nojs.html"></noscript>
  <link rel="manifest" href="../manifest.json">
  <!--No descuidar el orden de los archivos CCS y JS-->
  <!--CSS DEPENDENCIES-->
  <?php
  if(constant("envDebug") == "development"){
    $globsJS = ["{/src/vendor/*.js}", "{/src/support/*.js}", "{/src/directives/**/*.js}", "{/src/filters/**/*.js}", "{/src/factories/**/*.js}", "{/src/support/tab/**/*.js}", "{/src/support/modal/**/*.js}"];

    $files = glob("{/css/*.css}",GLOB_BRACE);
    for($i = 0; $i < count($files); $i++){
      echo '<link rel="stylesheet" href="..'.$files[$i].'?v='.constant("envSHA").'">', PHP_EOL;
    }
    unset($i);

    foreach($globsJS as $glob){
      $files = glob($glob,GLOB_BRACE);
      for($i = 0; $i < count($files); $i++){
        echo '<script src="..'.$files[$i].'?v='.constant("envSHA").'"></script>', PHP_EOL;
      }
      unset($i);
    }
  }
  else if(constant("envDebug") == "production"){
    echo '<link rel="stylesheet" href="../dist/${envSHA}.min.css">', PHP_EOL;
    echo '<script src="../dist/${envSHA}.min.js"></script>', PHP_EOL;
  }
  ?>
</head>
<body>
  <header>
    <div class="navbar">
      <nav class="white">
        <div class="nav-wrapper">
          <a href="#"><img src="../img/mixtura-logo.png" style="width: 150px; height: 63px"></img></a>
        </div>
      </nav>
    </div>
  </header>
  <main>
    <div class="container">
      <div class="row">
        <div class="col s12 m12 l12">
          <ul class="collapsible popout" data-collapsible="accordion" watch>
            <!-- START - ADMINISTRAR TICKETS-->
            <li ng-include="'/src/support/tab/createTickets/template.html'" ng-controller="createTickets as ct"></li>
            <li ng-include="'/src/support/tab/viewTickets/template.html'" ng-controller="viewTickets as vt"></li>
          </ul>
        </div>
      </div>
    </div>
  </main>
  <footer class="page-footer footer grey darken-3">
    <div class="container">
      <div class="footer-copyright grey darken-3">
        <div class="container">
          <a href="mailto: devangelmotta@hotmail.com">© <?php echo constant("envAuthor") ?> NodeJS Dev</a>
          <a class="grey-text text-lighten-4 right" href="#!">Compilación: <?php echo constant("envShortSHA") ?></a>
        </div>
      </div>
    </div>
  </footer>
</body>
</html>