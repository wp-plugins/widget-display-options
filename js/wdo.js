jQuery(document).ready(function($){
		
	$('.wdo-display-options-toggle').live('click', function(e){
		e.preventDefault();
		var toggles = $('#wdo-display-options-' + $(this).data('toggles') );
		$(this).hide();
		toggles.slideDown();
		$('#widget-' + $(this).data('toggles') + '-wdo-hide-panel').val('0');
	});
	
	$('.wdo-display-options-hide-me').live('click', function(e){
		e.preventDefault();
		var toggleLabel = $(this).data('toggles');
		var toggles = $('#wdo-display-options-' + toggleLabel );
		toggles.slideUp( function(){
			$('#wdo-display-options-toggle-' + toggleLabel ).show();
			$('#widget-' + toggleLabel + '-wdo-hide-panel').val('1');
		});
	});
		
	
	$('.wdo-toggle-fieldset').live('click', function(e){
		e.preventDefault();
		var toggles = $('#' + $(this).data('toggles'));
		if( toggles.is(':hidden') ){
			toggles.slideDown();
			$(this).find('span').text('[-]');
		} else {
			toggles.slideUp();
			$(this).find('span').text('[+]');
		}
		
	});	
	
	$('.wdo-conditionals').livequery( function(){
		var hasValue = false;
		$(this).find('.wdo-fieldset').each( function(){
			if( check_for_empty_fieldset( $(this), true ) ){
				hasValue = true;
			}
		});
		if( !hasValue ){
			var widgetID = $(this).data('id');
			$('#wdo-fieldset-Common-' + widgetID ).find('.wdo-functions').show();
			$('#wdo-fieldset-Common-' + widgetID ).find('span').text('[-]');
		}
	});
	
	$('.wdo-checkbox').livequery( function(){ 
		update_conditional( $(this) );
		$(this).live('change', function(){
			update_conditional( $(this) );
			check_for_empty_fieldset( $(this).parents('fieldset'), false );
		});
	});
	
	function update_conditional( elm ){

		var myID = elm.attr('id');
		
		var myValField = $('#' + myID + '-params');
		
		var myNot = $('#' + myID + '-not');
		
		var myHelper = $('#' + myID + '-helper');
		
		var myHelperTable = $('#' + myID + '-helper-table');
		
		if( 'checked' == elm.attr('checked') ){
		
			elm.parents('fieldset').addClass('wdo-fieldset-active').find('span').text('[-]');
			
			elm.parent().addClass('wdo-conditional-checked');
			elm.parent().parent().addClass('wdo-conditional-active');
			myNot.parent().show();
			myHelper.show();
			myHelperTable.show();
			
			if( myValField.length != 0 ){
				myValField.show();
			}
			
		} else {
		
			elm.parent().removeClass('wdo-conditional-checked');
			elm.parent().parent().removeClass('wdo-conditional-active');
			myNot.parent().hide();
			myHelper.hide();
			myHelperTable.hide();
			
			if( myValField.length != 0 ){
				myValField.hide();
			} 
		}
	}
	
	function check_for_empty_fieldset( elm, hide ){
		
		var hasValue = false;
		
		elm.find('.wdo-checkbox').each( function(){
			if( $(this).attr('checked') == 'checked' ){
				hasValue = true;
			}
		});
		
		if( !hasValue ){
			elm.removeClass('wdo-fieldset-active');
			if( !hide ){
				elm.find('.wdo-functions').show();
			}
		}
		
		return hasValue;
		
	}
	
	$('.wdo-logic-toggles').livequery( function(){

		$(this).find( 'input[type="checkbox"]' ).each( function(){ 
			update_logic_label( $(this) );
		}).live( 'change', function(){
			update_logic_label( $(this) );
		});
		
	});
	
	
	function update_logic_label( elm ){
		
		var widgetID = elm.parent().data('id');

		if( 'checked' == elm.attr('checked') ){
			elm.parents('h4').removeClass( 'wdo-logic-not-checked' );
			$('#wdo-conditionals-' + widgetID ).slideDown();
		} else {
			elm.parents('h4').addClass( 'wdo-logic-not-checked' );
			$('#wdo-conditionals-' + widgetID ).slideUp();
		}
		
	}
	
	$('.wdo-hide-from-print').livequery( function(){

		$(this).find( 'input[type="checkbox"]' ).each( function(){ 
			update_print_label( $(this) );
		}).live( 'change', function(){
			update_print_label( $(this) );
		});
		
	});
	
	function update_print_label( elm ){
		
		if( 'checked' == elm.attr('checked') ){
			elm.parent().css( 'font-weight', 'bold' );
		} else {
			elm.parent().css( 'font-weight', 'normal' );
		}
		
	}
	
});