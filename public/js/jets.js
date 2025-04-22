/*! Jets.js - v0.1.0 - 2015-09-10
* http://NeXTs.github.com/Jets.js/
* Copyright (c) 2015 Denis Lukov; Licensed MIT */

;(function(name, definition) {
  if (typeof module != 'undefined') module.exports = definition();
  else if (typeof define == 'function' && typeof define.amd == 'object') define(definition);
  else this[name] = definition();
}('Jets', function() {
  "use strict"

  function Jets(opts) {
    if( ! (this instanceof Jets)) {
      return new Jets(opts);
    }
    var timeout;

    var self = this;
    ['searchTag', 'contentTag'].forEach(function(param) {
      var name = param.replace('Tag', '');
      self[name + '_tag'] = document.querySelector(opts[param]);
      self[name + '_param'] = opts[param];
      if( ! self[name + '_tag']) {
        throw new Error('Error! Could not find ' + param + ' element');
      }
    });

    self.options = {};
    ['columns', 'addImportant', 'searchSelector', 'manualContentHandling'].forEach(function(name) {
      self.options[name] = opts[name];
    });

    var last_search_query,
    callSearch = function() {
      if(last_search_query != (last_search_query = self.search_tag.value))
        self._applyCSS();
    };
    self._onSearch = function(event) {
      if (event.keyCode == 40) {
        event.preventDefault();
        self.selectDown();
      }

      if(event.type == 'keydown') {
        clearTimeout(timeout);
        timeout = setTimeout(callSearch, 150);
        return false
      }
      // callSearch();
    };
    self.destroy = function() {
      self._processEventListeners('remove');
      self._destroy();
    };
    self.selectDown = function() {
      this.content_tag.focus();
      if ($ || jQuery) {
        try {
          $(this.content_tag).find('option:visible:first').prop('selected', true);
        } catch(err) {console.log(err.message)} 
      }
    };

    self._processEventListeners('add');
    self._addStyleTag();
    self._setJets();
    self._applyCSS();
  }

  Jets.prototype = {
    constructor: Jets,
    _processEventListeners: function(action) {
      ['input', 'keydown', 'change'].forEach(function(event_type) {
        this.search_tag[action + 'EventListener'](event_type, this._onSearch);
      }.bind(this));
    },
    _removeUnicode: function(str) {
      str= str.replace(/à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ/g,"a");
      str= str.replace(/è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ/g,"e");
      str= str.replace(/ì|í|ị|ỉ|ĩ/g,"i");
      str= str.replace(/ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ/g,"o");
      str= str.replace(/ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ/g,"u");
      str= str.replace(/ỳ|ý|ỵ|ỷ|ỹ/g,"y");
      str= str.replace(/đ/g,"d");
      return str;
    },
    _applyCSS: function() {
      var value = this.search_tag.value.trim().toLowerCase(),
      value_nouni = this._removeUnicode(this.search_tag.value.trim().toLowerCase()),
      css = this.content_param + '>:not([data-jets' + (this.options.searchSelector || '*') +
        '="' + value + '"]):not([data-jets-nouni' + (this.options.searchSelector || '*') +
        '="' + value_nouni + '"]){display:none' + (this.options.addImportant ? '!important' : '') + '}';
      this.styleTag.innerHTML = value ? css : '';
    },
    _addStyleTag: function() {
      this.styleTag = document.createElement('style');
      document.head.appendChild(this.styleTag);
    },
    _getText: function(tag) {
      return tag && (tag.innerText || tag.textContent) || '';
    },
    _getContentTags: function(query) {
      return Array.prototype.slice.call(this.content_tag.querySelectorAll(query || ':scope > *'));
    },
    _setJets: function(query, force) {
      var self = this,
        tags = self._getContentTags(force ? '' : query), text;
      for(var i = 0, tag; tag = tags[i]; i++) {
        if((tag.hasAttribute('data-jets') || tag.hasAttribute('data-jets-nouni')) && ! force) continue;
        text = this.options.manualContentHandling
          ? this.options.manualContentHandling(tag)
          : self.options.columns && self.options.columns.length
            ? self.options.columns.map(function(column) {
                return self._getText(tag.children[column]);
              }).join(' ')
            : self._getText(tag);
        tag.setAttribute('data-jets', text.trim().replace(/\s+/g, ' ').toLowerCase());
        tag.setAttribute('data-jets-nouni', self._removeUnicode( text.trim().replace(/\s+/g, ' ').toLowerCase() ) );
      };
    },
    update: function(force) {
      this._setJets(':scope > :not([data-jets])', force);
    },
    _destroy: function() {
      this.styleTag.parentNode && document.head.removeChild(this.styleTag);
      var tags = this._getContentTags();
      for(var i = 0, tag; tag = tags[i]; i++) {
        tag.removeAttribute('data-jets');
        tag.removeAttribute('data-jets-nouni');
      }
    }
  }

  // :scope polyfill
  // http://stackoverflow.com/a/17989803/1221082
  ;(function(doc, proto) {
    try {
      doc.querySelector(':scope body');
    } catch (err) {
      ['querySelector', 'querySelectorAll'].forEach(function(method) {
        var nativ = proto[method];
        proto[method] = function(selectors) {
          if (/(^|,)\s*:scope/.test(selectors)) {
            var id = this.id;
            this.id = 'ID_' + Date.now();
            selectors = selectors.replace(/((^|,)\s*):scope/g, '$1#' + this.id);
            var result = doc[method](selectors);
            this.id = id;
            return result;
          } else {
            return nativ.call(this, selectors);
          }
        }
      });
    }
  })(window.document, Element.prototype);

  return Jets;
}));