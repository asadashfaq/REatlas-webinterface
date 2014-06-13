/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

function debugFunctionNameWithLine()
{
    console.log(new Error().stack.split('\n')[1]);
    console.log(new Error().stack.split('\n')[2]);
}

Object.defineProperty(Array.prototype, "remove", {
    enumerable: false,
    value: function (item) {
        var removeCounter = 0;
        if(item && item != "undefined") {
        for (var index = 0; index < this.length; index++) {
            if ((this[index][0] === item[0]) && (this[index][1] === item[1])) {
                this.splice(index, 1);
                removeCounter++;
                index--;
            }
        }
        }
        return removeCounter;
    }
});

Object.defineProperty(Array.prototype,"contains",{
    enumerable: false,
    value:  function(obj) {
    var i = this.length;
    while (i--) {
        if (this[i][0] === obj[0] && this[i][1] === obj[1]) {
            return true;
        }
    }
    return false;
    }
});

function nl2br (str, is_xhtml) {
    var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
}