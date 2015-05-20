<?php


/**
 * En este archivo se incluyen los scripts
 *
 */


define("PlUGINPATH", plugins_url()."/cltvo-gallery-metabox/");

/** ==============================================================================================================
 *                                                HOOKS
 *  ==============================================================================================================
 */


add_action( 'admin_enqueue_scripts', 'cltvo_gallery_js' ); // incluye el admin-functions.js. Descomentar para tener JS en admin (no olvidar crear el file [admin-functions.js])


/** ==============================================================================================================
 *                                               SCRIPTS
 *  ==============================================================================================================
 */


/**
 * Callback del registro de js y css 
 *
 *
 */

function cltvo_gallery_js(){
	wp_register_script( 'cltvo_gallery_functions_js', PlUGINPATH.'/js/gallery-functions.js', array('jquery','cltvo_jqueryui_js'), false, true ); // registro del jquery ui necesario
	wp_register_script( 'cltvo_jqueryui_js', PlUGINPATH.'/js/jquery-ui.js', array('jquery'), false, true ); // registro del functions .js

	wp_enqueue_style('admin-styles', PlUGINPATH.'/css/ultraligero_gallery.css' ); // registro del css 

	wp_enqueue_script('jquery');
	wp_enqueue_script('cltvo_gallery_functions_js');
}


