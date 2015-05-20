<?php
/**
 * Plugin Name: Metabox de galerias del cultivo
 * Description: Toma las galerias creadas nativamente en en workpress y las transfiere al metabox que permite administrarlas facilmente 
 * Version: 0.1 
 * Author: El cutivo 
 * Author URI: elcultivo.mx
 * License: ????
 */


/**
 *
 * En esta version se agrega un archivo config-galllery.php cuyo objetivo es que se enlisten los post_types donde se mostrara el metabox.
 *
 */

include_once('config-galllery.php');


/**
 *
 * Include donde agregan los js y los CSS 
 *
 */

include_once('JS_CSS-galllery.php');

/**
 * En este archivo se incluyen los meta box y las funciones de save post. 
 *
 */

/** ==============================================================================================================
 *                                                  HOOKS
 *  ==============================================================================================================
 */

add_action( 'add_meta_boxes', 'cltvo_meta_gallery' ); // agrega las metabox
add_action( 'save_post', 'cltvo_save_gallery' ); // guarda el valor de las metabox 



/** ==============================================================================================================
 *                                                Meta box
 *  ==============================================================================================================
 */



// ---------------------- agrega el meta box ---------------------- 
function cltvo_meta_gallery(){
	global $post_types_metabox;

	foreach ($post_types_metabox as $type) {
		add_meta_box(
	        'cltvo_gal_md', // $id
	        'Galeria Imágenes y fotos', // $title
	        'cltvo_gal_fc', // $callback
	        $type, // $page
	        'normal'    
	    ); 
	}

}

// ---------------------- funcion del meta box ---------------------- 


/**
 * Elimina el Shortcode de galerias de wordpress dentro del content del post 
 *
 * Parametros: 
 *
 * @param string html contenido del post 
 * @return string contenido del post sin el shortcode de galerias de wordpress
 */

function remplace_gallery($html){
	$html = preg_replace(  '/.*\[\s*gallery.*\]/',"", $html );
	return $html;	
}

/**
 * Convierte en los subarray de ids, en string de ids serilizados. 
 *
 * Parametros: 
 *
 * @param array $galerias contiene la informacion de las galerias guardadas y requiere que de tener un sub array de ids 
 * @return array regresa un array con un valores de ids serializados. 
 */

function unserialize_ids($galerias){

	foreach ( $galerias as $key => $cada_gal ) { 
		if ( is_string( $cada_gal['ids'] ) ){
			$para_unser =  str_replace('\"','"', $cada_gal['ids']);
			$galerias[$key]['ids'] = unserialize( $para_unser );
		}
	}   		        		
	return $galerias;
}

/**
 * Obtiene las galerias insertadas en el post y las guarda en un arreglo 
 *
 * Parametros: 
 *
 * @param array|string|boolean metadata guardada
 * @return array galerias guardadas 
 */
function guarda_galerias($galerias){
    

    if ( !is_array($galerias) ){
    	unset($galerias);
    	$galerias = array();
    }

	$galerias =	unserialize_ids($galerias);

    //if ( get_post_gallery() ) :
        $gallery = get_post_galleries( get_the_ID(), false );

    	//var_dump($gallery);


        foreach( $gallery AS $src ){

        	$esta = true; // verificador de galeria no guardada 

        	if ( isset($src['ids']) ){ // si la galersia se creo corretamente
	        	$ids_array = explode( ',', $src['ids']  ); // crea un array con de las  galerias obtenidas del post 


	        	if ( !empty($galerias) ){ // si no se han guardado galerias aun 


		        	foreach ( $galerias as $key => $cada_gal ) { // para cada galeria guarda en el post 
		        		
		        		$result = array_diff( $cada_gal['ids'], $ids_array );  // compara si la la galeria guardada es igual a la que se introduce 
		        		$result2 = array_diff($ids_array, $cada_gal['ids'] ); // compara si la la galeria que se introduce  es igual a la guardada 

		        		if ( empty($result) && empty($result2) ){ // si ya se guardo una galeria igual
		        	 		
		        			$esta = false;  // cambia el verificador de galeria guardada 
		        			break; // corta el ciclo 
		        		}		        			
		        		
		        	}  
	        	}



	        	if ( $esta == true    ){ // solo guarda la galeria so no existe otra igual 
	        		$galerias[] = array( 'ids' => $ids_array, 'Nombre' => "",  'Descripccion' => ""  );

	        	}

        	}

        }

    //endif; 


	return $galerias;	
}

/**
 * Callback del metabox 
 *
 *
 */


function cltvo_gal_fc($object) {

	$prefix='image_gallery_meta';
	$meta_array = get_post_meta($object->ID, $prefix, true);
	//var_dump($meta_array );

	$meta_array['galerias'] = isset($meta_array['galerias']) ? guarda_galerias($meta_array['galerias']) : guarda_galerias(array());

	if ( empty($meta_array['galerias'] )) {
		?>
		<strong> Sin galerías.</strong> <br> <hr>
		<?php
	}

	?>
	<input type='hidden' value='true' name='<?php echo  $prefix; ?>[init]' />		
	<?php

	foreach ($meta_array['galerias'] as $key => $galeria) :

		$input_hiden_value = serialize($galeria['ids']);

		$title = $galeria['Nombre'];

		$description = $galeria['Descripccion']; 

		?>
			<div id="galeria_<?php echo $key ; ?>">
				<strong>Nombre de la galeria </strong><br>
				<input type='text' value='<?php echo $title ; ?>' name='<?php echo  $prefix; ?>[galerias][<?php echo  $key; ?>][Nombre]' id='Gal_Nombre_<?php echo $key ; ?>' style='width:100%'  /><br>
				<strong>Descripccion de la Galeria </strong><br>
				<textarea style='width:100%'   name='<?php echo  $prefix; ?>[galerias][<?php echo  $key; ?>][Descripccion]' id='Gal_Descripccion_<?php echo $key ; ?>' ><?php echo $description ; ?></textarea><br>

				<input type='hidden' value='<?php echo $input_hiden_value ; ?>' name='<?php echo  $prefix; ?>[galerias][<?php echo  $key; ?>][ids]' id='Gal_ids_<?php echo $key ; ?>' class="gal-hidden-input_JS" car-gal="car-gal_<?php echo $key ; ?>" />
				<br>
				<div class="div_galeria" id="car-gal_<?php echo $key ; ?>" >
				<?php 

					foreach ($galeria['ids'] as $orden => $image_id ) :
						?>
						<div class="div_img" img-id="<?php echo $image_id; ?>" id="gal_<?php echo $key ; ?>-img_<?php echo $image_id; ?>">
							<a class="borrar-img borrar-img_JS" imgbor="gal_<?php echo $key ; ?>-img_<?php echo $image_id; ?>"><strong >x</strong ></a>
							<?php 
							$img_src = wp_get_attachment_image($image_id, 'thumbnail' );
							echo $img_src ; 
							?>
							
						</div>
						<?php 
					endforeach;
				?>
					<div class="gal_vacia gal_vacia_JS" id="gal_<?php echo $key ; ?>-vacia">
						<strong>Galería vacía</strong>
					</div>				
				</div>

				<div class="div_quitar" >
					<a galeriaid="galeria_<?php echo $key ; ?>" id="Gal_a_<?php echo $key ; ?>"  class = "borrar-galeria borrar-galeria_JS" > Quitar </a>
				</div>
				<hr class="linea">
			</div>


			
		<?php 
	
	endforeach;

	?>
	<div align="center"> 
		<a href="#" id="insert-media-button" class="button insert-media add_media" data-editor="content" title="Añadir objeto"><span class="wp-media-buttons-icon"></span> Añadir objeto</a> 
	</div>
	<?php

 
}


/** ==============================================================================================================
 *                                                Save post
 *  ==============================================================================================================
 */

/**
 * Callback del save post 
 *
 *
 */

function cltvo_save_gallery($id){
	// Permisos
	if( !current_user_can('edit_post', $id) ) return $id;

	// Vs Autosave
	if( defined('DOING_AUTOSAVE') AND DOING_AUTOSAVE ) return $id;
	if( wp_is_post_revision($id) OR wp_is_post_autosave($id) ) return $id;


	// ---------------------- salva el meta box ----------------------  

	if( isset($_POST['image_gallery_meta']) ){

		$gal_guard= isset($_POST['image_gallery_meta']['galerias']) ? guarda_galerias($_POST['image_gallery_meta']['galerias']) : guarda_galerias(array());

		foreach ($gal_guard as $galeria => $arreglo) {
			$gal_guard[$galeria]['ids'] = serialize($arreglo['ids']);
		}

		$data_save = $_POST['image_gallery_meta'];
		$data_save['galerias'] = $gal_guard;

		update_post_meta( $id, 'image_gallery_meta' , $data_save );

		$args['ID'] = $id;
		$args['post_content'] = isset( $_POST['post_content'] ) ? remplace_gallery($_POST['post_content'] ) : "";


		if ( ! wp_is_post_revision( $id ) ){
			
			// unhook this function so it doesn't loop infinitely
			remove_action('save_post', 'cltvo_save_gallery');
			
			// update the post, which calls save_post again
			wp_update_post( $args );
			// re-hook this function
			add_action('save_post', 'cltvo_save_gallery');
		}			
	}
}

?>