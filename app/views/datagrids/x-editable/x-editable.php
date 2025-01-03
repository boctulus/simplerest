<style type="text/css">       
    #comments:hover {
        background-color: #FFFFC0;
        cursor: text; 
    }
</style>

<script>
    onLoaded((e)=>{
        var c = window.location.href.match(/c=inline/i) ? 'inline' : 'popup';
        $.fn.editable.defaults.mode = c === 'inline' ? 'inline' : 'popup';

        $('#f').val(f);
        $('#c').val(c);
        
        $('#frm').submit(function(){
            var f = $('#f').val();
            if(f === 'jqueryui') {
                $(this).attr('action', 'demo-jqueryui.html');
            } else if(f === 'plain') {
                $(this).attr('action', 'demo-plain.html');
            } else if(f === 'bootstrap2') {
                $(this).attr('action', 'demo.html');
            } else {
                $(this).attr('action', 'demo-bs3.html');                        
            }
        });
    })


</script>        

        
<style type="text/css">
    body {
        padding-top: 50px;
        padding-bottom: 30px;
    }
    
    table.table > tbody > tr > td {
        height: 30px;
        vertical-align: middle;
    }
</style>         



<div style="width: 80%; margin: auto;"> 
    <h1>X-editable Demo</h1>
    <hr>

    <h2>Settings</h2>
        <form method="get" id="frm" class="form-inline" action="demo.html">
        
        <label>
            <span>Form style:</span>
            <select id="f" class="form-control">
                <option value="bootstrap3">Bootstrap 3</option>
                <option value="bootstrap2">Bootstrap 2</option>
                <option value="jqueryui">jQuery UI</option>
                <option value="plain">Plain</option>
            </select>
        </label>

        <label style="margin-left: 30px">Mode:
            <select name="c" id="c" class="form-control">
                <option value="popup">Popup</option>
                <option value="inline">Inline</option>
            </select>
        </label>

        <button type="submit" class="btn btn-primary" style="margin-left: 30px">refresh</button>
    </form>

    <hr>

    <h2>Example</h2>   
    <div style="float: right; margin-bottom: 10px">
    <label style="display: inline-block; margin-right: 50px"><input type="checkbox" id="autoopen" style="vertical-align: baseline">&nbsp;auto-open next field</label>
    <button id="enable" class="btn btn-default">enable / disable</button>
    </div>
    <p>Click to edit</p>
    <table id="user" class="table table-bordered table-striped" style="clear: both">
        <tbody> 
            <tr>         
                <td width="35%">Simple text field</td>
                <td width="65%"><a href="#" id="username" data-type="text" data-pk="1" data-title="Enter username">superuser</a></td>
            </tr>
            <tr>         
                <td>Empty text field, required</td>
                <td><a href="#" id="firstname" data-type="text" data-pk="1" data-placement="right" data-placeholder="Required" data-title="Enter your firstname"></a></td>
            </tr>  
            <tr>         
                <td>Select, local array, custom display</td>
                <td><a href="#" id="sex" data-type="select" data-pk="1" data-value="" data-title="Select sex"></a></td>
            </tr>
            <tr>         
                <td>Select, remote array, no buttons</td>
                <td><a href="#" id="group" data-type="select" data-pk="1" data-value="5" data-source="/groups" data-title="Select group">Admin</a></td>
            </tr> 
            <tr>         
                <td>Select, error while loading</td>
                <td><a href="#" id="status" data-type="select" data-pk="1" data-value="0" data-source="/status" data-title="Select status">Active</a></td>
            </tr>  
                    
            <tr>         
                <td>Datepicker</td>
                <td>
                
                <span class="notready">not implemented for Bootstrap 3 yet</span>
                
                </td>
            </tr>
            <tr>         
                <td>Combodate (date)</td>
                <td><a href="#" id="dob" data-type="combodate" data-value="1984-05-15" data-format="YYYY-MM-DD" data-viewformat="DD/MM/YYYY" data-template="D / MMM / YYYY" data-pk="1"  data-title="Select Date of birth"></a></td>
            </tr> 
            <tr>         
                <td>Combodate (datetime)</td>
                <td><a href="#" id="event" data-type="combodate" data-template="D MMM YYYY  HH:mm" data-format="YYYY-MM-DD HH:mm" data-viewformat="MMM D, YYYY, HH:mm" data-pk="1"  data-title="Setup event date and time"></a></td>
            </tr> 
            
                                    
                                
            <tr>         
                <td>Textarea, buttons below. Submit by <i>ctrl+enter</i></td>
                <td><a href="#" id="comments" data-type="textarea" data-pk="1" data-placeholder="Your comments here..." data-title="Enter comments">awesome
user!</a></td>
            </tr> 
            
            
            
            
            <tr>         
                <td>Twitter typeahead.js</td>
                <td><a href="#" id="state2" data-type="typeaheadjs" data-pk="1" data-placement="right" data-title="Start typing State.."></a></td>
            </tr>                       
                                    
                                                
            <tr>         
                <td>Checklist</td>
                <td><a href="#" id="fruits" data-type="checklist" data-value="2,3" data-title="Select fruits"></a></td>
            </tr>

            <tr>         
                <td>Select2 (tags mode)</td>
                <td><a href="#" id="tags" data-type="select2" data-pk="1" data-title="Enter tags">html, javascript</a></td>
            </tr>                    

            <tr>         
                <td>Select2 (dropdown mode)</td>
                <td><a href="#" id="country" data-type="select2" data-pk="1" data-value="BS" data-title="Select country"></a></td>
            </tr>  
            
            <tr>         
                <td>Custom input, several fields</td>
                <td><a href="#" id="address" data-type="address" data-pk="1" data-title="Please, fill address"></a></td>
            </tr>                      
                                                                                                        
            <tr>         
                <td>Wysihtml5 (now support bootstrap 3 !!!). <a href="#" id="pencil"><i class="icon-pencil" style="padding-right: 5px"></i>[edit]</a></td>
                <td>
                    <div id="note" data-pk="1" data-type="wysihtml5" data-toggle="manual" data-title="Enter notes" data-placement="top">
                    <h3>WYSIWYG</h3>
                    WYSIWYG means <i>What You See Is What You Get</i>.<br>
                    But may also refer to:
                        <ul>
                        <li>WYSIWYG (album), a 2000 album by Chumbawamba</li>
                        <li>"Whatcha See is Whatcha Get", a 1971 song by The Dramatics</li>
                        <li>WYSIWYG Film Festival, an annual Christian film festival</li>
                        </ul>
                        <i>Source:</i> <a href="http://en.wikipedia.org/wiki/WYSIWYG_%28disambiguation%29">wikipedia.org</a> 
                    
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    
    <div style="float: left; width: 50%">
        <h3>Console <small>(all ajax requests here are emulated)</small></h3> 
        <div><textarea id="console" class="form-control" rows="8" style="width: 70%" autocomplete="off"></textarea></div>
    </div>
    
    <div style="float: left">             
        <h3>More examples and tricks <small>(jsFiddle)</small></h3> 
        <ul>
            <li><a href="http://jsfiddle.net/xBB5x/38" target="_blank">Submit data via PUT method</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/39" target="_blank">Autotext option for select</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/40" target="_blank">Display checklist as &lt;UL&gt;</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/62" target="_blank">Process JSON response</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/63" target="_blank">Editable column in table</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/64" target="_blank">MVC pattern in editable interface</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/194" target="_blank">Change buttons style</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/278" target="_blank">Display server response as element's text</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/297" target="_blank">Dependent SELECTs</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/331" target="_blank">Single regular checkbox</a></li>
            <li><a href="http://jsfiddle.net/xBB5x/329" target="_blank">Live events: work with delegated targets</a></li>
            <li><a href="http://jsfiddle.net/wQysh/8" target="_blank">SELECT2 remote source</a></li>
        </ul>
    </div>

</div>

<?php
    /*
        Importante para la DEMO
    */

    js_file('third_party/x-editable/demo/demo.js');
    js_file('third_party/x-editable/demo/demo-mock.js');
?>