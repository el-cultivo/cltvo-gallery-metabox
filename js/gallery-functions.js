(function($) {
	$(document).ready(function(){
		
		// reacomodo de las galerías  
		
		$( ".div_galeria").sortable({
			  connectWith: ".div_galeria",
			  cancel:".borrar-img_JS",
			  update:  update_hiden_inputs,
		});
		
		$( ".div_galeria").disableSelection();	
		
		// elimina una galería completa 
		$(".borrar-galeria_JS").on("click",function(){
			var galeria_id = $(this).attr("galeriaid");
			$('#'+galeria_id).remove();
		});
		
		// elimina una imagen

		$(".borrar-img_JS").on("click",function(){
			var gal_img_id = $(this).attr("imgbor");	
				$('#'+gal_img_id).remove();
				update_hiden_inputs();
		});
		
		/**
		 * Actualiza el valor de los input tipo hidden según el orden de las imágenes en todas las galerias  
		 *
		 * Esta función se utiliza al borrar una imagen y al re ordenarlas 
		 *
		 */
		
		function update_hiden_inputs() {
			$(".gal-hidden-input_JS").each(function(){
					  var img_gal =  new Array();
					  $("#" + $(this).attr("car-gal") ).find(".div_img").each(function(i){
						  img_gal[i] = $(this).attr("img-id");
					  });
					  $(this).attr("value", php_serialize(img_gal ) );
					  if( img_gal.length == 0 ){
						  $("#" + $(this).attr("car-gal") ).find(".gal_vacia_JS").show();
					  }else{
						  $("#" + $(this).attr("car-gal") ).find(".gal_vacia_JS").hide();					  
					  }
					  
					  
				  });
		}
		
		
		/**
		 * Función tomada de http://code.activestate.com/recipes/414334-pass-javascript-arrays-to-php/
		 *
		 * PHP Serialize
		 * Morten Amundsen
		 * mor10am@gmail.com
		 *
		 *
		 * Serializa los array igual que en php 
		 *
		 */
 
		function php_serialize(obj){
			var string = '';

			if (typeof(obj) == 'object') {
				if (obj instanceof Array) {
					string = 'a:';
					tmpstring = '';
					count = 0;
					for (var key in obj) {
						tmpstring += php_serialize(key);
						tmpstring += php_serialize(obj[key]);
						count++;
					}
					string += count + ':{';
					string += tmpstring;
					string += '}';
				} else if (obj instanceof Object) {
					classname = obj.toString();

					if (classname == '[object Object]') {
						classname = 'StdClass';
					}

					string = 'O:' + classname.length + ':"' + classname + '":';
					tmpstring = '';
					count = 0;
					for (var key in obj) {
						tmpstring += php_serialize(key);
						if (obj[key]) {
							tmpstring += php_serialize(obj[key]);
						} else {
							tmpstring += php_serialize('');
						}
						count++;
					}
					string += count + ':{' + tmpstring + '}';
				}
			} else {
				switch (typeof(obj)) {
					case 'number':
						if (obj - Math.floor(obj) != 0) {
							string += 'd:' + obj + ';';
						} else {
							string += 'i:' + obj + ';';
						}
						break;
					case 'string':
						string += 's:' + obj.length + ':"' + obj + '";';
						break;
					case 'boolean':
						if (obj) {
							string += 'b:1;';
						} else {
							string += 'b:0;';
						}
						break;
				}
			}

			return string;
		}
	});
})(jQuery);