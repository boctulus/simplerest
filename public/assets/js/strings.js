// Convierte algo como &euro; en su equivalente en UTF-8
function decodeHTMLEntities(text) {
    var textArea = document.createElement('textarea');
    textArea.innerHTML = text;
    return textArea.value;
}

const ucfirst = s => (s && s[0].toUpperCase() + s.slice(1)) || ""
