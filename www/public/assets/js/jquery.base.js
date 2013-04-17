var base = {
    common : {
        init : function() {
            window.$layout = { 
                top      : $('#top'),
                con      : $('#container'),
                fmr      : $('#fmr'),
                jmail    : $('span.jmail'),
                external : $('a[rel="external"]'),
                internal : $('a.internal'),
                aprint   : $('a.print'),
                adelete  : $('a.delete-item')
            };

            $layout.aprint.click(function(e) {
                e.preventDefault();
                return window.print();
            });

            $layout.adelete.click(function(e) {
                e.preventDefault();
                return _delete_item(this);
            });

            $layout.external.click(function(e) {
                e.preventDefault();
                return _window_open($(this).attr('href'));
            });

            $.ajax({
                url: '/assets/js/plugins/jquery.pngFix.pack.js',
                dataType: 'script',
                cache: true,
                async: false,
                success: function() {
                    $(document).pngFix();
                }
            });

            _fmr();
            _jmail($layout.jmail);
            _overlay();
        }
    },
    forms : {
        init : function() {
            window.$forms = { 
                fieldset : $('fieldset'),
                elems    : $('input[type="text"], input[type="password"], select'),
                login    : $('#login_form'),
                uname    : $('#username'),
                pword    : $('#password'),
                XID      : $('#XID'),
                hjson    : $('#hjson'),
                city     : $('#city'),
                state    : $('#state'),
                zipcode  : $('#zipcode'),
                methods  : $('#methods'),
                emails   : $('#email'),
                resact   : $('#resend_activation'),
                autoarea : $('.auto-textarea'),
                selitem  : $('.selitem'),
                moption  : $('.moption'),
                disabled : $('.disabled')
            };

            _autoUpdate();
            _methods();

            $forms.selitem.live('click', function(e) {
                e.preventDefault();
                field  = $(this).attr('rel');
                cont   = $(this).text();
                _insertAutoUpdate(field, cont);
                _winnowAutoUpdate(field, cont);
            });

            $forms.resact.click(function(e) {
                e.preventDefault();
                $(this).parent('span').load($(this).attr('href'));
            });

            if ( $.browser.webkit ) {
                $forms.autoarea.bind('blur', function(e) {
                    $(this).css({ overflowY: 'hidden' }).animate({ height: '22px', width: '400px' });
                });
            } else {
                $forms.autoarea.bind('click', function(e) {
                    $(this).css({ overflowY: 'auto' }).animate({ height: '140px', width: '400px' });
                    return true;
                }).bind('blur', function(e) {
                    $(this).css({ overflowY: 'hidden' }).animate({ height: '22px', width: '400px' });
                });
            }
        }
    },
    tables : {
        init : function() {
            window.$tables = { 
                sort : $('#sort'),
                tr   : $('#sort tbody tr'),
                th   : $('#sort thead tr th')
            };

            $.ajax({
                url: '/assets/js/plugins/jquery.tablesort.js',
                dataType: 'script',
                cache: true,
                async: false,
                success: function() {
                    $tables.sort.tablesorter({widgets:['zebra']});
                }
            });
        }
    },
    widget : {
        init : function() {
            window.$widget = { 
                ordergroup : $('fieldset.order-group')
            };
            _dnd();
        }
    },
    jmail : {
        init : function() {
            window.$jmail = { 
                jmail : $('span.jmail')
            };
            _jmail($jmail.jmail);
        }
    }
};

_delete_item = function(obj) {

	var qstring = $(obj).attr('href');

	$.ajax({ 
		type: 'POST', 
		url: '/index/delete/',
		data: qstring.toString(),
		success: function(response) {
			if(response < 0) {
				_load_fm();
				_man_fm('Delete Failed', true);
			} else {
				_load_fm();
				_man_fm('Item Deleted');
				location.href = location.href;
			}
		}
	});
};

_overlay = function() {
	$(document).ready(function() {
		$.ajax({
			url: '/assets/js/plugins/jquery.tools.min.js',
			dataType: 'script',
			cache: true,
			async: false,
			success: function(response) {
				$('a.internal[rel]').overlay({
					top: 19,
					mask: { color: '#333', loadSpeed: 500, opacity: 0.7 },
					onBeforeLoad: function() {
						var wrap = this.getOverlay().find(".display");
						wrap.load(this.getTrigger().attr("href"));
						this.getOverlay().appendTo('#content');
					}
				});
	        }
    	});
	});
};

_fmr = function() {
    $.ajax({ 
        type: 'POST', 
        url: '/index/fmd/',
        success: function(response) {
            if(response != "0") {
                _load_fm();
                $('#fmr').html(response);
            }
        },
        complete: function() {
            _unload_fm();
        }
    });
};

_load_fm = function() {
    if( $('#fmr').length == 0 ) {
        fmr = $('<div id="fmr" class="fm" />');
        $layout.con.prepend(fmr);
        $('#fmr').slideDown('fast');
    }
};

_man_fm = function(fm, err) {
	var cls = (err) ? 'error':'notice';
    if( $('#fmr').find('div.notice').length == 0 ) {
        $('#fmr').html('').append('<div class="'+cls+'"><span/></div>');
    }
    $('#fmr div span').html(fm);
};

_unload_fm = function() {
    if( $('#fmr').length > 0 ) {
        obj = $('#fmr');
        _timer(4, 'slideup', obj);
        _timer(6, 'remove', obj);
    }
};

_dnd = function() {
    $.ajax({
        url: '/assets/js/plugins/jquery.ui.min.js',
        dataType: 'script',
        cache: true,
        async: false,
        success: function() {
            $("#order_group").sortable({
                axis: 'y',
                revert: 500,
                placeholder: 'oform placeholder',
                update: function(e, u) {
                    var status = "1"; 
                    $('fieldset.order-group').each(function(i) {
                        id = $(this).attr('rel');
                        if(id) {
                            $.ajax({ 
                                type: 'POST', 
                                url: '/index/update/',
                                data: 'table=AllowanceConfigs&field=display_order&value='+(i+1)+'&key='+id,
                                success: function(response) {
                                    status = (status == "1") ? response:status;
                                }
                            });
                        }
                    });
                    if(status != "1") {
                        _load_fm();
                        _man_fm('Reorder Failed');
                    } else {
                        _load_fm();
                        _man_fm('Reordered');
                        location.href = location.href;
                    }
                }
            });
        }
    });
};

_autoUpdate = function() {
    $forms.city.live('keyup', function(e) {
        e.preventDefault();
         args = { 'obj':'city', 'href':'/ajax/geoloc/' };
        _timer(2, 'ajax', args)
    });
    $forms.state.live('keyup', function(e) {
        e.preventDefault();
         args = { 'obj':'state', 'href':'/ajax/geoloc/' };
        _timer(2, 'ajax', args)
    });
    $forms.zipcode.live('keyup', function(e) {
        e.preventDefault();
         args = { 'obj':'zipcode', 'href':'/ajax/geoloc/' };
        _timer(2, 'ajax', args)
    });
};

_insertAutoUpdate = function(field, cont) {
    $('#'+field).val(cont);
    $('#'+field+'_dd .auxcontainer').remove();
};

_ajaxAutoUpdate = function(field, href) {
    string = $('#'+field).val();
    $.ajax({ 
        type: 'POST', 
        url: href,
        data: 'field='+field+'&string='+string,
        success: function(ret) {
            if(ret != '') {
                $forms.hjson.val(ret);
                var json = eval(ret);
                _parseAutoUpdate(null, json);
                //$('#demo').html(ret);
            }
        }
    });
};

_winnowAutoUpdate = function(field, cont) {
    temp = eval($forms.hjson.val());
    var json = [];
    for(i=0;i<temp.length;i++) {
        if( temp[i][field] == cont ) {
            json.push(temp[i]);
        }
    }
    _parseAutoUpdate(field, json);
};

_strConvert = function(str) {
    return str.toLowerCase().replace(/\s+/gi, '_');
};

_integers = function(value) {
    var regex = new RegExp(/^\d+$/);
    return ( ! regex.test(value)) ? false:true;
};

_parseAutoUpdate = function(field, json) {

    city    = [];
    state   = [];
    zipcode = [];
    z         = 1000;
    w         = '<div class="auxcontainer" />';

    for(i=0;i<json.length;i++) {
        city.push(json[i].city);
        state.push(json[i].state);
        zipcode.push(json[i].zipcode);
    }

    if( field == null || field != 'city' ) {
        if( ! $('#city_dd .auxcontainer').size() ) {
            $('#city_dd').css({ zIndex : z }).append(w);
            _timer(10, 'fadeout', '#city_dd .auxcontainer');
        } else {
            $('#city_dd .auxcontainer').html('').show();
        }

        if(city.length > 1) {
            for(i=0;i<city.length;i++) {
                str = _strConvert(city[i]);
                if( $('#city_'+str).length == 0 ) {
                    str = '<div id="city_'+str+'" class="selitem" rel="city" rev="'+i+'">'+city[i]+'</div>';
                    $('#city_dd .auxcontainer').append(str);
                }
            }
        } else {
            _insertAutoUpdate('city', city[0]);
        }
    }
    if( field == null || field != 'state' ) {
        if( ! $('#state_dd .auxcontainer').size() ) {
            $('#state_dd').css({ zIndex : z }).append(w);
            _timer(10, 'fadeout', '#state_dd .auxcontainer');
        } else {
            $('#state_dd .auxcontainer').html('').show();
        }
        if(state.length > 1) {
            for(i=0;i<state.length;i++) {
                str = _strConvert(state[i]);
                if( $('#state_'+str).length == 0 ) {
                    str = '<div id="state_'+str+'" class="selitem" rel="state" rev="'+i+'">'+state[i]+'</div>';
                    $('#state_dd .auxcontainer').append(str);
                }
            }

        } else {
            _insertAutoUpdate('state', state[0]);
        }
    }
    if(  field == null || field != 'zipcode' ) {
        if( ! $('#zipcode_dd .auxcontainer').size() ) {
            $('#zipcode_dd').css({ zIndex : z }).append(w);
            _timer(10, 'fadeout', '#zipcode_dd .auxcontainer');
        } else {
            $('#zipcode_dd .auxcontainer').html('').show();
        }

        if(zipcode.length > 1) {
            for(i=0;i<zipcode.length;i++) {
                str = _strConvert(zipcode[i]);
    
                if( $('#zipcode_'+str).length == 0 ) {
                    str = '<div id="zipcode_'+str+'" class="selitem" rel="zipcode" rev="'+i+'">'+zipcode[i]+'</div>';
                    $('#zipcode_dd .auxcontainer').append(str);
                }
            }
        } else {
            _insertAutoUpdate('zipcode', zipcode[0]);
        }
    }
};

_methods = function() { 
    $forms.methods.find('option').each(function(e) {
        id  = $(this).attr('rel'); 
        num = $('#'+id).val();
        if( num == '' || num.length < 10 ) {
            if( ! $(this).hasClass('disabled') ) {
                $(this).addClass('disabled');
            }
            if( ! $(this).attr('disabled') ) {
                $(this).attr('disabled', 'disabled');
            }
        } else {
            if( $(this).hasClass('disabled') ) {
                $(this).removeClass('disabled');
            }
            if( $(this).attr('disabled') ) {
                $(this).removeAttr('disabled');
            }
        }
    });
};

_window_open = function(href, name, options) {
    if(href === null) { 
        return;
    }
    name    = (name) ? name:'new';
    options = (options) ? options:'';
    window.open(href, name, options);
};

_jmail = function(obj) {
    $(obj).each(function(i) {
        e = $(this).text().split('|');
        if(!e[1]) return;
        un = e[0];
        dn = e[1];
        dm = e[2];
        if(!un || !dn || !dm) {
            $(this).html('<a name="">no email</a>');
            return;
        }
        str = un+'@'+dn+'.'+dm;
        tx    = (e[3]) ? e[3]:str;
        $(this).html('<a href="mailto:'+str+'">'+tx+'</a>');
    });
};

_timer = function(secs, type, var1) {
    if(secs > 0) { 
        setTimeout((function() {  
            secs--;
            _timer(secs, type, var1);
        }), 1000);
    } 
    else { 
        if(type == 'ajax') {
            _ajaxAutoUpdate(var1.obj, var1.href);
        } 
        else if(type == 'fadeout') {
            $(var1).fadeOut('slow');
        }
        else if(type == 'slideup') {
            $(var1).slideUp('fast');
        } 
        else if(type == 'remove') {
            $(var1).remove();
        }
        else if(type == 'redirect') {
            location.href = var1;
        }
    }
};