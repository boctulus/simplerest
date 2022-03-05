/*
    Core functions
*/

function parseJSON(response) {
    return response.text().then(function(text) {
        return text ? JSON.parse(text) : {}
    })
}

const getIntValue = (elem, $default = null) => {
    if ($default != null && (typeof elem == 'undefined' || elem.value == '')){
        return $default;
    }
    return parseInt(elem.value);
}

const getFloatValue = (elem, $default = null) => {
    if ($default != null && (typeof elem == 'undefined' || elem.value == '')){
        return $default;
    }
    return parseFloat(elem.value);
}

function assets(filename){
    load = filename.match(/^\/\//) || filename.match(/^http:/) || filename.match(/^https:/) ? filename : 'assets/' + filename;

    if (filename.match(/\.js$/)){ 
        var fileref=document.createElement('script')
        fileref.setAttribute("type","text/javascript")
        fileref.setAttribute("src", load)
    }
    else if (filename.match(/\.css$/)){ 
        var fileref=document.createElement("link")
        fileref.setAttribute("rel", "stylesheet")
        fileref.setAttribute("type", "text/css")
        fileref.setAttribute("href", load)
    }
    if (typeof fileref!="undefined")
        document.getElementsByTagName("head")[0].appendChild(fileref)
}

function getSiteRoot()
{
    var rootPath = "//" + window.location.host + "/";
    if (window.location.hostname == "localhost")
    {
        var path = window.location.pathname;
        if (path.indexOf("/") == 0)
        {
            path = path.substring(1);
        }
        path = path.split("/", 1);
        if (path != "")
        {
            rootPath = rootPath + path + "/";
        }
    }
    return rootPath;
}


// function to make form values to json format
// from codeofaninja.com
$.fn.serializeObject = function(){

    var o = {};
    var a = this.serializeArray();
    $.each(a, function() {
        if (o[this.name] !== undefined) {
            if (!o[this.name].push) {
                o[this.name] = [o[this.name]];
            }
            o[this.name].push(this.value || '');
        } else {
            o[this.name] = this.value || '';
        }
    });
    return o;
};
