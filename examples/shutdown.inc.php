<?php

//  LICENSE: GPL 3 - https://www.gnu.org/licenses/gpl-3.0.txt
//  
//  s. https://github.com/mkloubert/phpLINQ


$customContent = ob_get_contents();
ob_end_clean();

?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>phpLINQ examples :: <?php echo htmlentities($pageTitle); ?></title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    
    <link href="css/highlight/default.css" rel="stylesheet">
    <link href="css/highlight/railscasts.css" rel="stylesheet">
    
    <style type="text/css">
    
    body {
        padding-bottom: 3.5em;
        padding-left: 0.5em;
        padding-right: 0.5em;
        padding-top: 4.5em;
    }
    
    #exampleTab .tab-pane {
        padding: 0.5em;
    }
    
    #navBarBottomContent {
        line-height: 4em;
    }
    
    </style>
  </head>
  <body>
     <nav class="navbar navbar-default navbar-fixed-top">
       <div class="container-fluid">
         <div class="navbar-header">
           <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
             <span class="sr-only">Toggle navigation</span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
             <span class="icon-bar"></span>
           </button>
           
           <a class="navbar-brand" href="#">phpLINQ examples</a>
         </div>
       </div>
     </nav>
     
     <?php if (!empty($examples)): ?>
     
     <ol class="breadcrumb">
       <li><a href="index.php">Home</a></li>
       <li class="active"><?php echo htmlentities($pageTitle); ?></li>
     </ol>
  
     <ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
     <?php

         foreach ($examples as $e) {
             $elementId = 'phpLINQExample' . trim($e->id);

             $title = trim($e->title);
             if (empty($title)) {
                 $title = 'Example #' . trim($e->id + 1);
             }
             
             ?>
             <li class="<?php echo $e->id != 0 ? '' : 'active'; ?>">
               <a href="#<?php echo $elementId; ?>" data-toggle="tab"><?php echo htmlentities($title); ?></a>
             </li>
             <?php
         }
     
     ?>
     </ul>
     
     <div class="tab-content" id="exampleTab">
     <?php

         foreach ($examples as $e) {
             $elementId = 'phpLINQExample' . trim($e->id);

             ?>
             <div class="tab-pane <?php echo $e->id != 0 ? '' : 'active'; ?>" id="<?php echo $elementId; ?>">
                 <?php
                 
                 $desc = trim($e->description);
                 if (!empty($desc)) {
                     ?><p><?php echo htmlentities($desc); ?></p><?php
                 }
                 
                 ?>
             
                <h1>Code:</h1>
                <pre style="background-color: transparent;"><code class="php"><?php echo parseForHtmlOutput($e->sourceCode); ?></code></pre>
                
                <h1>Result:</h1>
                <pre style="background-color: black; color: white;"><?php
                
                ob_start();
                
                eval($e->sourceCode);
                
                $result = ob_get_contents();
                ob_end_clean();

                echo parseForHtmlOutput($result);
                
                ?></pre>
            </div>

            <?php
         }
     
     ?>
     </div>
     
     <?php else: ?>
<?php echo $customContent; ?>
     <?php endif; ?>
     
     <nav class="navbar navbar-default navbar-fixed-bottom">
       <div class="container-fluid" id="navBarBottomContent">
           Examples of <a href="https://github.com/mkloubert/phpLINQ/" target="_blank">phpLINQ</a>; syntax highlighting supported by <a href="https://highlightjs.org/" target="_blank">highlight.js</a>; <a href="http://getbootstrap.com/" target="_blank">Bootstrap</a> theme provided by <a href="http://bootswatch.com/" target="_blank">bootswatch.com</a>.
       </div>
     </nav>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
    
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    
    <script src="js/highlight.pack.js"></script>
    
    <script type="text/javascript">

    $(document).ready(function() {
        hljs.initHighlighting();
    });

    </script>
  </body>
</html>