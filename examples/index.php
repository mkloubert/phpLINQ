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


require_once './bootstrap.inc.php';


$pageTitle = 'Home';

?>

<link href="css/codemirror.css" rel="stylesheet">
<link href="css/vibrant-ink.css" rel="stylesheet">

<style type="text/css">
    .CodeMirror {
        height: 400px;
    }

    .CodeMirror pre {
        padding: 0 4px 0 1em !important;
    }

    #phpLINQTestCodeResult {
        background-color: black;
        color: white;
        height: 400px;
        overflow: auto;
    }

    .phpLINQAjaxLoader {
        display: block;
        margin-left: auto;
        margin-right: auto;
        margin-top: 150px;
    }
</style>

<script type="text/javascript" src="js/codemirror-compressed.js"></script>

<ol class="breadcrumb">
  <li class="active">Home</li>
</ol>

<div class="panel panel-default">
    <div class="panel-heading">Try it (scroll down to see the links to the examples)</div>

    <div class="panel-body">
        <div id="phpLINQTestCodeAlerts" style="display: none;"></div>

        <table class="table">
            <thead>
                <tr>
                    <th width="50%">Code</th>
                    <th width="50%">Result</th>
                </tr>
            </thead>

            <tbody>
                <tr>
                    <td><textarea id="phpLINQTestCodeEditor"></textarea></td>
                    <td><pre id="phpLINQTestCodeResult"></pre></td>
                </tr>

                <tr>
                    <td>
                        <input id="phpLINQExecuteBtn" class="btn btn-primary" type="button" value="Execute" onclick="phplinq_ExecuteCode()" />&nbsp;
                        <input id="phpLINQResetBtn" class="btn btn-warning" type="button" value="Reset" onclick="phplinq_ResetCode(true)" />
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<ul id="tabs" class="nav nav-tabs" data-tabs="tabs">
    <li>
        <a href="#EnumerableTab" data-toggle="tab">Enumerable</a>
    </li>

    <li class="active">
        <a href="#IEnumerableTab" data-toggle="tab">IEnumerable</a>
    </li>
</ul>

<div class="tab-content" id="mainTab">
    <div class="tab-pane" id="EnumerableTab">
        <div class="panel panel-default">
            <div class="panel-heading">B</div>

            <div class="panel-body">
                <div class="list-group">
                    <a href="examples_buildRandom.php" class="list-group-item">buildRandom()</a>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">C</div>

            <div class="panel-body">
                <div class="list-group">
                    <a href="examples_create.php" class="list-group-item">create()</a>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">F</div>

            <div class="panel-body">
                <div class="list-group">
                    <a href="examples_fromJson.php" class="list-group-item">fromJson()</a>
                    <a href="examples_fromValues.php" class="list-group-item">fromValues()</a>
                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">R</div>

            <div class="panel-body">
                <div class="list-group">
                    <a href="examples_range.php" class="list-group-item">range()</a>
                </div>
            </div>
        </div>
    </div>

    <div class="tab-pane active" id="IEnumerableTab">
        <div class="panel panel-default">
          <div class="panel-heading">A</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_aggregate.php" class="list-group-item">aggregate()</a>
              <a href="examples_all.php" class="list-group-item">all()</a>
              <a href="examples_any.php" class="list-group-item">any()</a>
              <a href="examples_appendToArray.php" class="list-group-item">appendToArray()</a>
              <a href="examples_average.php" class="list-group-item">average()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">C</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_cast.php" class="list-group-item">cast()</a>
              <a href="examples_concat.php" class="list-group-item">concat()</a>
              <a href="examples_concatToString.php" class="list-group-item">concatToString()</a>
              <a href="examples_concatValues.php" class="list-group-item">concatValues()</a>
              <a href="examples_contains.php" class="list-group-item">contains()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">D</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_defaultArrayIfEmpty.php" class="list-group-item">defaultArrayIfEmpty()</a>
              <a href="examples_defaultIfEmpty.php" class="list-group-item">defaultIfEmpty()</a>
              <a href="examples_distinct.php" class="list-group-item">distinct()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">E</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_each.php" class="list-group-item">each()</a>
              <a href="examples_elementAtOrDefault.php" class="list-group-item">elementAtOrDefault()</a>
              <a href="examples_except.php" class="list-group-item">except()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">F</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_firstOrDefault.php" class="list-group-item">firstOrDefault()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">G</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_groupBy.php" class="list-group-item">groupBy()</a>
              <a href="examples_groupJoin.php" class="list-group-item">groupJoin()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">I</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_intersect.php" class="list-group-item">intersect()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">J</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_join.php" class="list-group-item">join()</a>
              <a href="examples_joinToString.php" class="list-group-item">joinToString()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">L</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_lastOrDefault.php" class="list-group-item">lastOrDefault()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">M</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_max.php" class="list-group-item">max()</a>
              <a href="examples_min.php" class="list-group-item">min()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">O</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_ofType.php" class="list-group-item">ofType()</a>
              <a href="examples_order.php" class="list-group-item">order()</a>
              <a href="examples_orderBy.php" class="list-group-item">orderBy()</a>
              <a href="examples_orderByDescending.php" class="list-group-item">orderByDescending()</a>
              <a href="examples_orderDescending.php" class="list-group-item">orderDescending()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">P</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_product.php" class="list-group-item">product()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">R</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_randomize.php" class="list-group-item">randomize()</a>
              <a href="examples_reverse.php" class="list-group-item">reverse()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">S</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_select.php" class="list-group-item">select()</a>
              <a href="examples_selectMany.php" class="list-group-item">selectMany()</a>
              <a href="examples_sequenceEqual.php" class="list-group-item">sequenceEqual()</a>
              <a href="examples_serialize.php" class="list-group-item">serialize()</a>
              <a href="examples_singleOrDefault.php" class="list-group-item">singleOrDefault()</a>
              <a href="examples_skip.php" class="list-group-item">skip()</a>
              <a href="examples_skipWhile.php" class="list-group-item">skipWhile()</a>
              <a href="examples_sum.php" class="list-group-item">sum()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">T</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_take.php" class="list-group-item">take()</a>
              <a href="examples_takeWhile.php" class="list-group-item">takeWhile()</a>
              <a href="examples_toArray.php" class="list-group-item">toArray()</a>
              <a href="examples_toDictionary.php" class="list-group-item">toDictionary()</a>
              <a href="examples_toJson.php" class="list-group-item">toJson()</a>
              <a href="examples_toList.php" class="list-group-item">toList()</a>
              <a href="examples_toLookup.php" class="list-group-item">toLookup()</a>
              <a href="examples_toSet.php" class="list-group-item">toSet()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">U</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_union.php" class="list-group-item">union()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">W</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_where.php" class="list-group-item">where()</a>
            </div>
          </div>
        </div>

        <div class="panel panel-default">
          <div class="panel-heading">Z</div>

          <div class="panel-body">
            <div class="list-group">
              <a href="examples_zip.php" class="list-group-item">zip()</a>
            </div>
          </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    var codeEditor;

    $(function() {
        codeEditor = CodeMirror.fromTextArea(document.getElementById('phpLINQTestCodeEditor'), {
            lineNumbers: true,
            mode: 'text/x-php',
            theme: 'vibrant-ink'
        });

        <?php
            if (!isset($_POST['initalTestCode'])) {
                ?>

        phplinq_ResetCode(false);

                <?php
            }
            else {
                ?>

        codeEditor.setValue(<?= json_encode($_POST['initalTestCode']) ?>);

                <?php
            }
?>

    });

    function phplinq_ExecuteCode() {
        var resultArea = $('#phpLINQTestCodeResult');

        var executeBtn = $('#phpLINQExecuteBtn');
        var resetBtn = $('#phpLINQResetBtn');

        $.ajax({
           'url': 'execcode.php',
           'data': {
               'code': codeEditor.getValue()
           },
           'type': 'POST',
           'beforeSend': function() {
               resultArea.html('<img class="phpLINQAjaxLoader" src="img/ajax-loader.gif" />');
               phplinq_HideAlert();

               executeBtn.prop('disabled', true);
               resetBtn.prop('disabled', true);
           },
           'success': function(result) {
               resultArea.html('');

               switch (result.code) {
                   case 0:
                       resultArea.text(phplinq_ParseResultForOutput(result.data.content));
                       phplinq_ShowSuccess('Code was executed after ' + result.data.time.duration + ' seconds ' +
                                           'and required ' + result.data.memory.allocated.difference + ' bytes of RAM.');
                       break;

                   case -1:
                       phplinq_ShowError('EXCEPTION: ' + result.data.msg);
                       break;

                   case -2:
                       phplinq_ShowError('ERROR: ' + result.data.msg);
                       break;

                   default:
                       phplinq_ShowWarning('Unknown result: [' + result.code + '] ' + result.msg);
                       break;
               }
           },
           'error': function(jqXHR, textStatus, errorThrown) {
               phplinq_ShowError('AJAX ERROR: ' + textStatus);
           },
           'complete': function() {
               executeBtn.prop('disabled', false);
               resetBtn.prop('disabled', false);
           }
        });
    }

    function phplinq_HideAlert() {
        var alertArea = $('#phpLINQTestCodeAlerts');
        alertArea.hide();
    }

    function phplinq_ParseResultForOutput(str) {
        return String(str);
    }

    function phplinq_ResetCode(showQuestion) {
        if (showQuestion && !confirm("Are you sure to reset the editor's content with the initial source code?")) {
            return;
        }

        var newEditorValue = "use \\System\\Collections\\Collection;\n" +
                             "use \\System\\Collections\\Dictionary;\n" +
                             "use \\System\\Collections\\Set;\n" +
                             "use \\System\\Linq\\Enumerable;\n" +
                             "\n" +
                             "\n" +
                             '$seq = Enumerable::fromValues(5979, 23979, null, 23979, 1781, 241279);\n' +
                             "\n" +
                             '$newSeq = $seq->select(\'$x => (string)$x\')' + "\n" +
                             '              ->where(\'$x => !empty($x)\')' + "\n" +
                             '              ->skip(1)' + "\n" +
                             '              ->take(3)' + "\n" +
                             '              ->distinct()' + "\n" +
                             '              ->order();' + "\n" +
                             "\n" +
                             'foreach ($newSeq as $key => $item) {' + "\n" +
                             '    echo sprintf("[%s] :: [%s] %s\\n",' + "\n" +
                             '                 $key, gettype($item), $item);' + "\n" +
                             '}';

        codeEditor.setValue(newEditorValue);
    }

    function phplinq_ShowAlert(str, type) {
        var alertArea = $('#phpLINQTestCodeAlerts');
        alertArea.html('');

        var newAlert = $('<div class="alert alert-' + type + '" role="alert"></div>');
        newAlert.text(str);

        alertArea.append(newAlert);

        alertArea.show();
    }

    function phplinq_ShowError(str) {
        phplinq_ShowAlert(str, 'danger');
    }

    function phplinq_ShowSuccess(str) {
        phplinq_ShowAlert(str, 'success');
    }

    function phplinq_ShowWarning(str) {
        phplinq_ShowAlert(str, 'warning');
    }


</script>
<?php

require_once './shutdown.inc.php';
