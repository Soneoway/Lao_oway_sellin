$(function () {
	
	
	$('.subnavbar').find ('li').each (function (i) {
	
		var mod = i % 3;
		
		if (mod === 2) {
			$(this).addClass ('subnavbar-open-right');
		}
		
	});
	
	
	
});

var showOnlyOptionsSimilarToText = function (selectionEl, str, isCaseSensitive, callback) {
    if (typeof isCaseSensitive == 'undefined')
        isCaseSensitive = true;
    if (isCaseSensitive)
        str = str.toLowerCase();

    var $el = $(selectionEl);

    $el.children("option:selected").removeAttr('selected');
    $el.val('');
    $el.children("option").hide();

    $el.children("option").filter(function () {
        var text = $(this).text();
        if (isCaseSensitive)
            text = text.toLowerCase();

        var textNonUnicode = remove_unicode(text);
        if (text.indexOf(str) > -1 || textNonUnicode.indexOf(str) > -1){
			
			return true;
		}
            
			
        return false;
    }).show();

    if (callback)	
        eval(callback+'();');
};

function remove_unicode(str) 
{  
	  //str= str.toLowerCase();  
	  str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");  
	  str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");  
	  str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");  
	  str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");  
	  str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");  
	  str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");  
	  str= str.replace(/đ/g,"d");  
	  return str;  
}

function initSearchOptionDistributor(elm, searchEl){
    var timeout;
	
    $('#'+searchEl).on("keyup", function (e) {
        if(e.keyCode == 40){
            $('#'+elm).focus();
            $('#'+elm + ' option:visible').first().attr('selected','selected');

        }else{
            var userInput = $('#'+searchEl).val();
            window.clearTimeout(timeout);
            timeout = window.setTimeout(function() {
                showOnlyOptionsSimilarToText($('#'+elm), userInput, true);
            }, 500);
        }
    });
}

fix_digit = 0;

Number.prototype.formatMoney = function(c, d, t){
var n = this, 
    c = isNaN(c = Math.abs(c)) ? 2 : c, 
    d = d == undefined ? "." : d, 
    t = t == undefined ? "," : t, 
    s = n < 0 ? "-" : "", 
    i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", 
    j = (j = i.length) > 3 ? j % 3 : 0;
   return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
 };

 // author: buu.pham
// parse float from formated string
//      floatval('2,474,015.69666')
//      >>> 2474015.69666
// convert from php function
function floatval(num) {
    dotPos = num.indexOf('.');
    commaPos = num.lastIndexOf(',');
    sep = ((dotPos > commaPos) && dotPos > 0 && commaPos > 0) ? dotPos : 
        (((commaPos > dotPos) && dotPos > 0 && commaPos > 0) ? commaPos : false );
   
    if (!sep) {
        return (parseFloat(num.replace(/[^0-9]/g, ""))).toFixed(fix_digit);
    } 

    return (parseFloat(
        num.substring(0, sep).replace(/[^0-9]/g, "") + '.' +
        num.substring(sep+1, num.length).replace(/[^0-9]/g, "")
    )).toFixed(fix_digit);
}

function float_f(num) {
    return parseFloat(num).formatMoney(fix_digit, ',', '.');
}

$.fn.extend({
    addMore: function(options) {
        var default_option = {
            remove_btn_html: '<button class="btn btn-danger btn-mini remove_item_btn new" type="button"><i class="icon-minus"></i></button>',
            add_btn_html: '<button class="btn btn-success btn-mini add_item_btn" type="button"><i class="icon-plus"></i></button>'
        };

        var options = jQuery.extend(default_option, options);
        var _self = $(this);

        var _remove_control = options.remove_control || function(e) {
            _e = $(e.target).hasClass("remove_item_btn") ? $(e.target) : $(e.target).parent();
            _e.parents('.list_item').remove();
        };

        var _add_control = options.add_control || function(e) {
            _e = $(e.target).hasClass("add_item_btn") ? $(e.target) : $(e.target).parent();
            _e.before(options.select_html);
            _self.find('.list_item.new').append(options.remove_btn_html);
            _self.find('.remove_item_btn.new').off('click').on('click', _remove_control).removeClass('new');
            _self.find('.list_item.new').removeClass("new");
        };

        var init = function() {
            _self.find('.remove_item_btn').remove();
            _self.find('.add_item_btn').remove();
            _self.find('.list_item').append(options.remove_btn_html);
            _self.append(options.add_btn_html);
            _self.find('.remove_item_btn.new').off('click').on('click', _remove_control).removeClass("new");
            _self.find('.add_item_btn').off('click').on('click', _add_control);
        };

        init();
    },
});