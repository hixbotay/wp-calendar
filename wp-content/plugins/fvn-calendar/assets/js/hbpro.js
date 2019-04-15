var x = null;
var y = null;
jQuery(document).ready(function($){
	
	$('body').on('mouseup',function(e){
		x=e.pageX;y=e.pageY;
		$('.error-message-alert').remove();
	});
	$('.fvn-function').click(function(){
		var ask = $(this).attr('data-ask');
		if(ask){
			var question = $(this).attr('data-question');
			if(!question){
				question = 'Do you want to process?';
			}
			if(!confirm(question)){
				return false;
			}
		}
		var link = $(this).attr('data-href');
		if(link!=null && link != undefined && link != ''){
			window.location.href=link;
			return;
		}
		return hb_submit_form($(this).attr('action'));
	});
});
function jtrigger_error(message,style){
	if(style !='' ||  style !== undefined){
		style = 'position:absolute;top:'+(y-20)+'px;left:'+x+'px;';
	}
	var html = jQuery('<span class="error-message-alert" style="'+style+'">'+message+'</span>');
	jQuery('body').append(html);    	
}

function hb_submit_form(task){
	jQuery('input[name="task"]').val(task);
	jQuery('#adminForm').submit();
}
//show modal pop-up
(function ($) {
	
    /**
     * Confirm a link or a button
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     */
    $.fn.confirmClick = function (options) {
        if (typeof options === 'undefined') {
            options = {};
        }

        this.click(function (e) {
            e.preventDefault();

            var newOptions = $.extend({
                button: $(this)
            }, options);

            $.confirm(newOptions, e);
        });

        return this;
    };
    
    $.fn.confirm = function (options) {
        if (typeof options === 'undefined') {
            options = {};
        }


        var newOptions = $.extend({
            button: $(this)
        }, options);

        $.confirm(newOptions, this);
       

        return this;
    };


    /**
     * Show a confirmation dialog
     * @param [options] {{title, text, confirm, cancel, confirmButton, cancelButton, post, confirmButtonClass}}
     * @param [e] {Event}
     */
    $.confirm = function (options, e) {
        // Do nothing when active confirm modal.
        if ($('.confirmation-modal').length > 0)
            return;

        // Parse options defined with "data-" attributes
        var dataOptions = {};
        if (options.button) {
            var dataOptionsMapping = {
                'title': 'title',
                'text': 'text',
                'confirm-button': 'confirmButton',
                'cancel-button': 'cancelButton',
                'confirm-button-class': 'confirmButtonClass',
                'cancel-button-class': 'cancelButtonClass'
            };
            $.each(dataOptionsMapping, function(attributeName, optionName) {
                var value = options.button.data(attributeName);
                if (value) {
                    dataOptions[optionName] = value;
                }
            });
        }

        // Default options
        var settings = $.extend({}, $.confirm.options, {
            confirm: function () {
                var url = e && (('string' === typeof e && e) || (e.currentTarget && e.currentTarget.attributes['href'].value));
                if (url) {
                    if (options.post) {
                        var form = $('<form method="post" class="hide" action="' + url + '"></form>');
                        $("body").append(form);
                        form.submit();
                    } else {
                        window.location = url;
                    }
                }
            },
            cancel: function (o) {
            },
            button: null
        }, dataOptions, options);

        // Modal
        var modalHeader = '';
        if (settings.title !== '') {
            modalHeader =
                '<div class=modal-header>' +
                    '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>' +
                    '<h4 class="modal-title">' + settings.title+'</h4>' +
                '</div>';
        }
        var modalHTML =
                '<div class="confirmation-modal modal fade" tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">' +
                            modalHeader +
                            '<div class="modal-body">' + settings.text + '</div>' +
                            '<div class="modal-footer">' +
                                '<button class="confirm btn ' + settings.confirmButtonClass + '" type="button" data-dismiss="modal">' +
                                    settings.confirmButton +
                                '</button>' +
                                '<button class="cancel btn ' + settings.cancelButtonClass + '" type="button" data-dismiss="modal">' +
                                    settings.cancelButton +
                                '</button>' +
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
        });
        modal.on('hidden.bs.modal', function () {
            modal.remove();
        });
        modal.find(".confirm").click(function () {
            settings.confirm(settings.button);
        });
        modal.find(".cancel").click(function () {
            settings.cancel(settings.button);
        });

        // Show the modal
        $("body").append(modal);
        modal.modal('show');
    };

    /**
     * Globally definable rules
     */
    $.confirm.options = {
        text: "Are you sure?",
        title: "",
        confirmButton: "Yes",
        cancelButton: "Cancel",
        post: false,
        confirmButtonClass: "btn-primary",
        cancelButtonClass: "btn-default"
    }
    
    /**
     * Show beauty popup
     * @param message message of popup
     * @param title title of popup
     * @param type type of popup(just have warning)
     */
    jAlert = function(message,title,type) {
    	title = title==undefined ? 'Notice' : '';
    	var html = '<div id="fvn-popup1" class="fvn-overlay">'+
	'<div class="fvn-popup">'+
		'<h2>'+title+'</h2>'+
		'<a class="fvn-close" href="javascript:void(0)" onclick="jQuery(\'#fvn-popup1\').remove();">&times;</a>'+
		'<div class="content">'+message+'</div>'+
	'</div>'
'</div>';
    	jQuery('body').append(html);
	}
    
    /**
     * Show a force popup - Can not be dismiss
     * @param message 
     * @param title
     * @param link (link of button)
     * @param btn_text (text of button)
     */
    jAlertFocus = function(message,title,link,btn_text) {
    	if ($('.confirmation-modal').length > 0){
    		$('.confirmation-modal').modal('hide');
    	}
    	var button_text = 'OK';
    	if (btn_text != '' || btn_text != undefined)
    		button_text = btn_text;
    		
    	if (title == 'warning')
    		title = '<span style="color:red"><i class="icon-warning"></i></span>&nbsp; Warning';
        var modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+ 
                        	'<div class="modal-header"><b>'+title+'</b></div>'+
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                                '<a href="'+link+'" class="center btn-primary btn" >'+button_text+'</a>' +                                
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';
        
        if(title == undefined || title == '')
        	modalHTML =
                '<div class="confirmation-modal modal " tabindex="-1" role="dialog">' +
                    '<div class="modal-dialog">' +
                        '<div class="modal-content">'+                        	
                            '<div class="modal-body">' + message + '</div>' +
                            '<div class="modal-footer center" style="">' +
                            	'<a href="'+link+'" class="center btn-primary btn" >'+button_text+'</a>' +                               
                            '</div>' +
                        '</div>' +
                    '</div>' +
                '</div>';    

        var modal = $(modalHTML);

        modal.on('shown.bs.modal', function () {
            modal.find(".btn-primary:first").focus();
        });
        
       
      
        // Show the modal
        $("body").append(modal);
        modal.modal({
            backdrop: 'static',
            keyboard: false  // to prevent closing with Esc button (if you want this too)
        });
        modal.modal('show');
	}
   
    
})(jQuery);

//get input value with name is filter by "filter" value
(function ($) {
    $.fn.jbGetFilterValue = function (filter) {    	
    	var result = '';
    	result += $('input').jbGetOptionValue(filter);
    	result += $('select').jbGetOptionValue(filter);
    	return result;
    	
    };
    $.fn.jbGetOptionValue = function(filter){
    	var length = filter.length;
    	var result = '';
    	$(this).each(function(){
    		var name = $(this).attr('name');
    		if(name){
    			if(name.substring(0, length)  == filter){
    				result += '&'+name+'='+$(this).val();
        		}
    		}    		
    	});
    	return result;
    }
})(jQuery);

//check session by ajax return false if session is expired
function checkSession(){
	return jQuery.ajax({
	  	url: 'index.php?option=com_bookpro&controler=flight&task=flight.ajaxCheckSession',
	  	dataType: "html",
	  	async: !1
	 }).responseText;
	
}

function jbsetCookie(cname,cvalue,exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires=" + d.toGMTString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

function jbgetCookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return "";
}


function format_date(date,format,convert){
	if(convert===undefined){
		convert='y-m-d';
	}
	//get format
	if (date == '')
		return '';
	if(date instanceof Date){
		date = date.toISOString().slice(0, 10);
	}
	var format = format.toLowerCase();
	//get id of element
	var format_array = format.split('-');
	var date = date.split('-');
	var d = 0;
	var m = 0;
	var y = 0;
	for(i =0; i<3; i++){
		if(format_array[i].substring(0, 1) == 'd'){
		d = i;
		}
		if(format_array[i].substring(0, 1) == 'm'){
		m = i;
		}
		if(format_array[i].substring(0, 1) == 'y'){
		y = i;
		}
	}
	
	var date_str = [];
	format_array = convert.split('-');
	for(i =0; i<3; i++){
		if(format_array[i].substring(0, 1) == 'd'){
			date_str.push(date[d]);
		}
		if(format_array[i].substring(0, 1) == 'm'){
			date_str.push(date[m]);
		}
		if(format_array[i].substring(0, 1) == 'y'){
			date_str.push(date[y]);
		}
	}
	date = date_str.join('-'); 
	return date;
}

function display_processing_form(enable){
	if(enable){
		jQuery('body').append('<img id="jbform_loading"  style="position: fixed;top:50%;left: 50%;margin-left: -100px;margin-top: -100px;width:200px;height:200px;" src="/wp-content/plugins/visa-fvn/assets/images/loading.gif"/>');

	}else{
		jQuery('#jbform_loading').remove();
	}
}

(function ($) {
	 var wfslideshow = function (element, options) {
        this.$element = $(element);
        this.options = $.extend({}, $.fn.wfslideshow.defaults, options, this.$element.data());
		
		this.offset = 0;
        this.init();  
     };
	 
	 wfslideshow.prototype = {
        constructor: wfslideshow, 
        init: function () {
			
            var html = '<div class="slideshow_button slideshow_previous slideshow_transparent wf_slide_show_btn" route="pre" role="button" style="display:none;"></div><div class="slideshow_button slideshow_next slideshow_transparent" route="next" role="button"></div><div class="wf-slideshow-content">';		
           
			
			var phone_class = 'col-xs-'+Math.floor(12/this.options.itemPhone);			
			var tablet_class = 'col-md-'+Math.floor(12/this.options.itemTablet);
			var desktop_class = 'col-lg-'+Math.floor(12/this.options.itemDesktop);
			this.itemClass = phone_class+' '+tablet_class+' '+desktop_class;
			
			this.resize($(window).width());
			
			
			
			
			defaultHtml = '<div class="col-xs-12 '+this.options.classItem+'">'+'<div class="'+this.options.classAvatar+'"></div>'+'<div class="'+this.options.classTitle+'"></div>'+'<div class="'+this.options.classDesc+'"></div>'+'</div>';
			
			if(this.options.data.length ==0){
				 html += defaultHtml;
			}else{
				html += this.renderHtml(this.options.data);
			}
			html += '</div>';
            this.$element.html(html);
			
			var wfslideshow = this;
			
			
			if(this.options.data.length ==0){
				$.ajax({
					url: wfslideshow.options.url,
					data: {'limit':wfslideshow.limit,'offset':wfslideshow.offset},
					dataType: "json",
					beforeSend: function(){
						wfslideshow.$element.find('.wf-slideshow-content').css('opacity','0.5');
					},
					success: function(result){
						wfslideshow.$element.find('.wf-slideshow-content').css('opacity','1');
						html = wfslideshow.renderHtml(result);	
						wfslideshow.$element.find('.wf-slideshow-content').html(html);
						
					}
				 });
			}
				
				
			$( window ).resize(function() {
			  wfslideshow.resize($( window ).width());
			});
			
			
			if(this.options.classActive != ''){		
				this.$element.find('.'+phone_class).click(function(e){
					alert();
					e.preventdefault();
					wfslideshow.$element.find('.'+phone_class).removeClass();
					$(this).addClass(wfslideshow.options.classActive);
				});
			}
			
			this.$element.find('.slideshow_button').click(function(){	
				var role = $(this).attr('route');
				if(role == 'next'){
					wfslideshow.offset = wfslideshow.offset+wfslideshow.limit;
				}else{
					wfslideshow.offset = wfslideshow.offset - wfslideshow.limit;	
					if(wfslideshow.offset<0){
						wfslideshow.offset = 0;
					}
				}
						
				$.ajax({
					url: wfslideshow.options.url,
					data: {'limit':wfslideshow.limit,'offset':wfslideshow.offset},
					dataType: "json",
					beforeSend: function(){
						wfslideshow.$element.find('.wf-slideshow-content').css('opacity','0.5');
					},
					success: function(result){
						wfslideshow.$element.find('.wf-slideshow-content').css('opacity','1');
						html = wfslideshow.renderHtml(result);
						if(wfslideshow.offset!=0){
							wfslideshow.$element.find('.slideshow_previous').show();
						}else{
							wfslideshow.$element.find('.slideshow_previous').hide();
						}
						
						if(result.length < wfslideshow.limit){
							wfslideshow.$element.find('.slideshow_next').hide();
						}else{
							wfslideshow.$element.find('.slideshow_next').show();
						}
						wfslideshow.$element.find('.wf-slideshow-content').html(html);
						
					}
				 });
				
				 
			});
			
			
			//wfslideshow = null;
			
        },
		
		resize: function(size){
			if(size < 780){
				this.device = 'xs';
				this.limit = this.options.itemPhone;
			}else if(size < 1000){
				this.device = 'md';
				this.limit = this.options.itemTablet;
			}else{
				this.device = 'lg';
				this.limit = this.options.itemDesktop;
			}
			return this.device;
		},
		
		renderHtml: function(data){
			var options = this.options;			
			var html = '';
			var itemClass = this.itemClass;
			$.each(data, function( i,item ){
				j = i+1;
				var item_class_reponsive = itemClass;
				if(j>options.itemTablet){
					item_class_reponsive += ' '+'hidden-md';
				}
				if(j>options.itemPhone){
					item_class_reponsive += ' '+'hidden-sm';
				}
				html += '<div class="'+item_class_reponsive+'">'+
									'<div class="'+options.classItem+' '+'bg-'+options.color[i]+'" >'+
									'<div class="'+options.classAvatar+' border-'+options.classborderAvartar[i]+'"><a href="'+item.link+'" ><img src="'+item.image+'" data-image="'+item.image_max+'" class="wp-post-image"/></a></div>'+
									'<div class="'+options.classTitle+'"><a href="'+item.link+'">'+item.title+'</a></div>';
				if(options.classDesc != ''){
					html += '<div class="summary-item-news">'+item.desc+'</div>';
				}
				if(options.classMore != ''){
					html += '<div class="'+options.classMore+'"><a href="'+item.link+'">Tìm hiểu thêm &nbsp;&nbsp;&nbsp;&nbsp;<i class="glyphicon glyphicon-play-circle"></i></a></div>';
				}
				html += '</div></div>';
			});
			
			
			if(options.onclick !== null){
				html = $(html);
				
				html.find("a").bind('click',function (e) {
					e.preventDefault();
					options.onclick($(this));
				});
				
			}
			
			return html;
		}
		
		
	};
	
	$.fn.wfslideshow = function ( option ) {
    	
        var d, args = Array.apply(null, arguments);
        args.shift();
       
        //getValue returns date as string / object (not jQuery object)
        if(option === 'getValue' && this.length && (d = this.eq(0).data('wfslideshow'))) {
          return d.getValue.apply(d, args);
        }        
        return this.each(function () {
            var $this = $(this),
            data = $this.data('wfslideshow'),            
            options = typeof option == 'object' && option;
            if (!data) {
                $this.data('wfslideshow', (data = new wfslideshow(this, options)));
            }
            if (typeof option == 'string' && typeof data[option] == 'function') {
                data[option].apply(data, args);
            }
        });
    }; 
	
	$.fn.wfslideshow.defaults = {
        url: '', 
		classItem: 'item-news',
		color: [],
		classActive: '',
		classAvatar: 'avatar-item-news',
		classborderAvartar: [],
		classTitle: 'title-item-news',
		classDesc: '',
		classMore: '',
		itemPhone: 2,
		itemDesktop: 4,
		itemTablet: 3,
		step: 0,
		onclick: null,
		data: []
    };
}(window.jQuery));


function jnotice(msg){
	if(jQuery('#jbnotice').length){
		jQuery('#jbnotice').remove();
	}
	var html = '<div id="jbnotice" style="text-align:center;font-size:20px;border: 1px solid #ccc;background: rgba(95, 186, 125, 0.7);color:white;position:fixed;width:300px;top:50%;left:50%;margin-left:-150px;magrin-top:-20px;line-height:40px;padding:10px;">'+msg+'</div>';
	jQuery('body').append(html);
	jQuery('#jbnotice').fadeOut(3000);

}