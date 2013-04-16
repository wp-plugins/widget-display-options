jQuery(document).ready(function($){
		
	var helpers = $('.wdo-helper');
	
	helpers.live( 'click', function(e){
		e.preventDefault();
	});
	
	$('.wdo-close-tooltip').live( 'click', function(e){
		e.preventDefault();
		$('#' + $(this).parent().data('origin') ).qtip('hide');
	});
	
	$('.wdo-return-button').live( 'click', function(e){
		e.preventDefault();
		
		var accepts = $(this).data('accepts');
		var newValue = $('.' + $(this).data('value') ).val();
		var returnTo = $('#' + $(this).data('return') );
		var oldValue = returnTo.val();
		
		if( 'string' != accepts && '' != oldValue ){
			newValue = oldValue + ', ' + newValue;
		}
		
		returnTo.val( newValue );
		
		$('#' + $(this).parent().data('origin') ).qtip('hide');
		
	});
	
	helpers.livequery( function(){
	
		$(this).qtip({
			content: {
				title: { text: $(this).data('qtitle'), button: 'x' },
				text: 'Loading Data...',
				url: ajaxurl,
				data: {
					'action'	: 'wdo_input_helper',
		  			'id'		: $(this).data('id'),
		  			'title'		: $(this).data('qtitle'),
		  			'callback'	: $(this).data('helper'),
		  			'return'	: $(this).data('return'),
		  			'accepts'	: $(this).data('accepts'),
		  			'origin'	: $(this).attr('id')
				},
				method: 'get'
			},
			position: {
				corner:{
					target: 'topRight',
					tooltip: 'bottomRight'
				} 
			},
			style: { 
				lineHeight: 1.8,
				border: {
			         width: 2,
			         radius: 0,
			         color: '#CCC'
			    },
				width: 270,
				name: 'light', 
				tip: {
					corner: 'bottomRight'
				}
			} ,
			show: {
				delay: 0,
				solo: true,
				when: { event: 'click' }
			},
			hide: {
				//fixed: true,
				when: { event: 'unfocus' } 
			}
		});
			
	});
	
	if( $('body').hasClass('wdo-use-tooltips') ){
	
		$('.wdo-tooltip').livequery( function(){
			
			$(this).qtip({ 
				content: {
					title: { text: $(this).data('qtitle') }
				},
				position: {
					corner:{
						target: 'topMiddle',
						tooltip: 'bottomRight'
					} 
				},
				style: { 
					lineHeight: 1.8,
					border: {
				         width: 2,
				         radius: 0,
				         color: '#CCC'
				    },
					name: 'light', 
					tip: {
						corner: 'bottomRight'
					}
				} ,
				show: {
					solo: true
				},
				hide: {
					fixed: true
				}
			}).click( function(e){ 
				if( $(this).attr('href') == '#' ){
					e.preventDefault();
				}
			});
			
		});
		
	}
	
	
});