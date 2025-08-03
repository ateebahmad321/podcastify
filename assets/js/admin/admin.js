import { Datepicker } from 'vanillajs-datepicker';
import '../lib/choices/choices.min.js';
//import foo from './bar'

// Uploading files
const { applyFilters } = wp.hooks;

let  wppSeriesFileFrame;

/**
 * File upload callback.
 * 
 * @since 1.0.0
 * @param {Object } e  event.
 * @param { object } labels labels.
 * @param {Object} args Settings.
 * @param {Function} callBack call back after select the file.
 */
function fileUploadHandler( e, labels = {}, args = { multiple: false }, callBack = () => {} ) {

	// If the media frame already exists, reopen it.
	if ( wppSeriesFileFrame ) {
		wppSeriesFileFrame.open();
		return;
	}

	const mediaSettings = {
		title: labels.frameTitle || 'Chose file',
		button: {
			text: labels.buttonText || 'Upload file',
		},
		multiple: args.multiple
	};

	if( args.library ){
		mediaSettings.library = args.library;
	}
	
	//Creating media field
	wppSeriesFileFrame = wp.media( applyFilters( 'wpp.media.upload.settings', mediaSettings ) );
	

	//On select the image
	wppSeriesFileFrame.on( 'select', () => {
		// Get the attachment detail on selection.
		const attachment = wppSeriesFileFrame.state().get( 'selection' ).first().toJSON();
		
		callBack( attachment );
	} );

	wppSeriesFileFrame.open();

}

if ( document.getElementById( 'episode_series_upload_image_button' ) ) {
	document.getElementById( 'episode_series_upload_image_button' ).addEventListener( 'click', ( e ) =>{

		/**
		 * Series image callback.
		 * 
		 * @since 1.0.0
		 * @param {Object} attachment 
		 */
		function seriesUploadImageCallback( attachment ){
			document.getElementById( 'episode_series_image_preview' ).setAttribute( 'src', attachment.url );
			document.getElementById( 'episode_series_image_id' ).value = attachment.id;
			document.getElementById( 'episode_series_remove_image_button' ).classList.remove( 'hidden' );
		}
	
		//Need to fix
		const  { uploadButtonText ='', uploadTitle = '' } = e.target.dataset;
		
		fileUploadHandler( e , {  frameTitle: uploadTitle, buttonText: uploadButtonText }, { library:{ type: ['image'] } ,multiple: false } , seriesUploadImageCallback );
	} );
}

/**
 * Remove image from series.
 * 
 * @since 1.0.0
 * @param {object} e  event.
 */
function seriesImageRemove( e ) {
	e.preventDefault();
	e.target.classList.add( 'hidden' );
	const perViewImg = document.getElementById( 'episode_series_image_preview' );
	const imgField = document.getElementById( 'episode_series_image_id' );
	perViewImg.setAttribute( 'src', perViewImg.dataset.src );
	imgField.value = '';
}
if ( document.getElementById( 'episode_series_remove_image_button' ) ) {
	document.getElementById( 'episode_series_remove_image_button' ).addEventListener( 'click', seriesImageRemove );
}


if ( null  !== document.querySelector( '.wpp-date' ) ) {
	const elem = document.querySelector( '.wpp-date' );
	new Datepicker( elem );
}


if ( document.getElementById( 'upload-podcast_file' ) ) {
	document.getElementById( 'upload-podcast_file' ).addEventListener( 'click', ( e ) => {

		/**
		 * Set tje podcast file.
		 * @param {String} file file path url
		 */
		function setPodcastFile( file ) {
			if ( file ) {

				document.getElementById( 'wpp-podcast_file' ).value = file.url;
				document.getElementById( 'wpp-filesize' ).value = file.filesizeHumanReadable.split( ' ' ).join( '' );
				document.getElementById( 'wpp-duration' ).value = file.fileLength;
				
			}
		}
		fileUploadHandler( e,  { frameTitle: 'Chose Podcast file', buttonText: 'Select file' },{ library:{ type: [ 'audio', 'video' ] } ,multiple: false }, setPodcastFile );

	} );
}



if ( document.getElementsByClassName( 'copy-feed-url' ) ) {
	document.querySelectorAll( '.copy-feed-url' ).forEach( ( item ) =>{
		item.addEventListener( 'click' , ( e )=>{
			e.target.previousElementSibling.select();
			document.execCommand( 'copy' );
			e.target.innerText = 'Copied!';
		} );
	} );
}


if ( document.getElementsByClassName( '.wppfy-select' ) ) {
	document.querySelectorAll( '.wppfy-select' ).forEach( ( item ) =>{
		//const element = document.querySelector( '.wppfy-select' );
		if( ! item.classList.contains( 'wppfy-multi' ) ){

			new Choices( item );  // eslint-disable-line

		}else {
			new Choices( item, {  // eslint-disable-line
				removeItemButton: true,
			} );
		}
	} );
}
