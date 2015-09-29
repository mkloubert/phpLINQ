<?php

/**********************************************************************************************************************
 * phpLINQ (https://github.com/mkloubert/phpLINQ)                                                                     *
 *                                                                                                                    *
 * Copyright (c) 2015, Marcel Joachim Kloubert <marcel.kloubert@gmx.net>                                              *
 * All rights reserved.                                                                                               *
 *                                                                                                                    *
 * Redistribution and use in source and binary forms, with or without modification, are permitted provided that the   *
 * following conditions are met:                                                                                      *
 *                                                                                                                    *
 * 1. Redistributions of source code must retain the above copyright notice, this list of conditions and the          *
 *    following disclaimer.                                                                                           *
 *                                                                                                                    *
 * 2. Redistributions in binary form must reproduce the above copyright notice, this list of conditions and the       *
 *    following disclaimer in the documentation and/or other materials provided with the distribution.                *
 *                                                                                                                    *
 * 3. Neither the name of the copyright holder nor the names of its contributors may be used to endorse or promote    *
 *    products derived from this software without specific prior written permission.                                  *
 *                                                                                                                    *
 *                                                                                                                    *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, *
 * INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE  *
 * DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, *
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR    *
 * SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,  *
 * WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE   *
 * USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.                                           *
 *                                                                                                                    *
 **********************************************************************************************************************/


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

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="js/jquery-1.11.3.min.js"></script>
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

                <form action="index.php" method="POST">
                    <input type="hidden" name="initalTestCode" value="<?= htmlspecialchars($e->sourceCode) ?>" />

                    <button class="btn btn-primary">Play with it</button>
                </form>

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
           Examples of <a href="https://github.com/mkloubert/phpLINQ/" target="_blank">phpLINQ</a>; syntax highlighting supported by <a href="https://highlightjs.org/" target="_blank">highlight.js</a>; code editor realized by <a href="https://codemirror.net/" target="_blank">Codemirror</a>; <a href="http://getbootstrap.com/" target="_blank">Bootstrap</a> theme provided by <a href="http://bootswatch.com/" target="_blank">bootswatch.com</a>.
       </div>
     </nav>

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