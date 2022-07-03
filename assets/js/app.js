jQuery( document ).ready( function( $ ) {

	$( "#wpss_buttons_position" ).select2();

	$( "#wpss_buttons_icontext_field" ).select2();

	$( ".buttons_style_preview" ).closest( 'tr' ).find( "th" ).css( 'line-height', '2.5' );

	$( '[data-toggle="tooltip"]' ).tooltip();

	$( "#social_service_toggle_btn" ).click( function( event ) {

		event.preventDefault();

		var values = $( "#wpss_buttons_list_field_values" ).val();
		try{
			$.each( values.split( ","), function( index, val ) {
				
				var target = $( "#selectable li[data-id='" + val + "']" );
				
				var bg = $( target ).children( 'span' ).attr( 'style' );
				
				$( target ).addClass( 'added' );
				
				$( target ).attr( 'style', bg );
			});
		
		}catch( err ){
			
			// console.log(err);
		}

	});

	var selected = [];

	$( "#myModal .close" ).click( function( event ) {

		$( "input#wpss_buttons_list_field_values" ).val( null );

		$( ".selected_container" ).children( 'li' ).length ? $( "#social_service_toggle_btn" ).css( 'margin-left', '15px' ) : $( "#social_service_toggle_btn" ).css( 'margin-left', '0px' );

		selected.length = 0;

		$.each( $( "#social_service_modal ul li.added" ), function( index, val ) {
			
			selected.push( $( val ).data( 'id' ) );
		});

		$( ".selected_container" ).empty();

		$.each( $( "#social_service_modal ul li.added" ), function( index, val ) {
			
			$( val ).clone().appendTo( ".selected_container" );
		});

		$( "input#wpss_buttons_list_field_values" ).val( selected );
	});


	$( "#selectable li" ).click( function( e ) {

		$( this ).toggleClass( 'added' );

		var bg = $( this ).children( 'span' ).attr( 'style' );

		$( this ).attr( 'style' ) ? $( this ).removeAttr( 'style' ) : $( this ).attr( 'style', bg );
	});
});