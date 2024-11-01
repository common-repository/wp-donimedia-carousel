<?php


/*
Plugin Name: WP donimedia carousel
Plugin URI: http://wp-plugins.donimedia-servicetique.net/
Description: With this plugin , you can display a customizable Flash Gallery , on your Wordpress website .
Version: 1.0.1
Author: David DONISA
Author URI: http://wp-plugins.donimedia-servicetique.net/
*/


	global $admin_panel_title, $wp_gallery_plugin_prefix, $fullSizeImagesUploadDirectory, $thumbnailDirectory, $wp_gallery_plugin_width, $wp_gallery_plugin_height, $wp_gallery_settings_group_ID, $wp_gallery_settings_group_ID_request, $wp_gallery_content;

	//  The function below allows to generate the swf code corresponding to each gallery created by the user :

	function wp_gallery_settings_group_swf_code($wp_gallery_settings_group_ID) {

		$plugin_dir = basename(dirname(__FILE__));
		global $wp_gallery_plugin_width,$wp_gallery_plugin_height, $wp_gallery_plugin_prefix, $wp_gallery_settings_group_ID;
		
		$settings_file_name = 'movieclip_parameters'.$wp_gallery_settings_group_ID.'.xml';
		$settings_file_path = WP_PLUGIN_DIR."/{$plugin_dir}/component/$settings_file_name";

		if ( get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_width' ) != "") { 

			$wp_gallery_plugin_width  = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_width' ); 

		} else { 

			$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			if ( $gallery_is_to_be_deleted == 'false'  ) {

				update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_width', '600' );
				$wp_gallery_plugin_width  = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_width' ); 

			} else {
			
				delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			};  //  Else 2 End 

		};  //  Else 1 End 
		
		if ( get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_height' ) != "") { 

			$wp_gallery_plugin_height  = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_height' ); 

		} else { 

			$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			if ( $gallery_is_to_be_deleted == 'false'  ) {

				update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_height', '600' );
				$wp_gallery_plugin_height  = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_'.'plugin_height' ); 

			} else {
			
				delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			};  //  Else 2 End 

		};  //  Else 1 End

		if ($wp_gallery_plugin_width == 0 || $wp_gallery_plugin_height == 0) { return ''; };

		$swf_code = '<center>';
		$swf_code .= '<object width="'.$wp_gallery_plugin_width.'" height="'.$wp_gallery_plugin_height.'">';
		$swf_code .= '<param name="movie" value="'.WP_PLUGIN_URL."/{$plugin_dir}/component/wp_donimedia_carousel.swf".'"></param>';
		$swf_code .= '<param name="scale" value="showall"></param>';
		$swf_code .= '<param name="salign" value="default"></param>';
		$swf_code .= '<param name="wmode" value="transparent"></param>';
		$swf_code .= '<param name="allowScriptAccess" value="sameDomain"></param>';
		$swf_code .= '<param name=FlashVars value="gallery_ID_request='.$wp_gallery_settings_group_ID.'">'."\n";
		$swf_code .= '<param name="allowFullScreen" value="true"></param>';
		$swf_code .= '<param name="sameDomain" value="true"></param>';
		$swf_code .= '<embed type="application/x-shockwave-flash" width="'.$wp_gallery_plugin_width.'" height="'.$wp_gallery_plugin_height.'" src="'.WP_PLUGIN_URL."/{$plugin_dir}/component/wp_donimedia_carousel.swf".'" scale="showall" salign="default" wmode="transparent" allowScriptAccess="sameDomain" allowFullScreen="true" FlashVars="gallery_ID_request='.$wp_gallery_settings_group_ID.'"';
		$swf_code .= '></embed>';
		$swf_code .= '</object>';
		$swf_code .= '</center>';
		return $swf_code;

}  //   wp_gallery_swf_code End

	//  The function below allows to insert a script between the <head> and </head> tag , which detects automatically the Flash player :

	function wp_gallery_settings_group_load_swf() {
		wp_enqueue_script('swfobject');
	}













function wp_gallery_content_update($wp_gallery_settings_group_ID){

	global $wp_gallery_settings_group_ID, $wp_gallery_content;


	$post_ID = get_the_ID();



	//  Connexion à la Base de données :

	mysql_connect(DB_HOST,DB_USER,DB_PASSWORD);  
	mysql_select_db(DB_NAME);      

	$query = "SELECT post_content FROM wp1_posts WHERE ID='".$post_ID."'";
  
	$result = mysql_query($query);
	$row = mysql_fetch_assoc($result);   

	$post_content = $row["post_content"];


	

	//  $myrows = $wpdb->get_row( "SELECT $wpdb->post_content FROM $wpdb->wp1_posts WHERE $wpdb->ID='".$post_ID."'" );

	//  $post_content = $myrows->post_content;




	if ( get_option( 'wp_gallery_plugin_group_counter' ) != "") { 

		$settings_group_counter  = get_option( 'wp_gallery_plugin_group_counter' ); 

	} else { 

		update_option( 'wp_gallery_plugin_group_counter', '0' );
		$settings_group_counter  = 0;

	};




	$wp_gallery_content  = $post_content;  //  get_the_content('');

	for ( $i = 0; $i <= $settings_group_counter ; $i++ ) {

		$wp_gallery_settings_group_ID = $i;

		$wp_gallery_content = preg_replace_callback('|\[wp_gallery'.$wp_gallery_settings_group_ID.'\s*()?\s*\](.*)\[/wp_gallery'.$wp_gallery_settings_group_ID.'\]|i', 'wp_gallery_settings_group_swf_code', $wp_gallery_content);  //  Remplace pattern par résultat-fonction dans $wp_gallery_content

	};	



	$new_content = $wp_gallery_content;



	$query = "UPDATE wp1_posts SET post_content = '".$new_content."' WHERE ID = '".$post_ID."'";
  
	$result = mysql_query($query);  



	return $wp_gallery_content;


}  //  function wp_gallery_content_update($wp_gallery_settings_group_ID ) End




	add_action('save_post','wp_gallery_content_update');
	add_action('init', 'wp_gallery_settings_group_load_swf');






















	//  The function below allows to delete a file or to clear a directory ( without removing it ) :

    function clear_directory_or_file($path_to_directory_or_file) {

        if (is_dir($path_to_directory_or_file)) {

             $dir_pointer = opendir($path_to_directory_or_file); // lecture

             while ($dir_entry = readdir($dir_pointer)) {

             	if ($dir_entry != '.' && $dir_entry != '..') {

				$file = $path_to_directory_or_file.$dir_entry; // chemin fichier
             		if (is_dir($file)) {

					clear_directory_or_file($file); // rapel la fonction de manière récursive

				} else {

					unlink($file); // sup le fichier 

				} //  if 3 End
             	}  //  if  2 End
                }  // while End

		closedir($dir_pointer);
                             
	}  else {

                unlink($path_to_directory_or_file);  // sup le fichier
         }

}  //  function clear_directory_or_file End

//  The function below allows to remove completely a directory :

function clearDir( $directory ) {

	$dir_pointer = @opendir($directory);

	if (!$dir_pointer) { return; };

	while( $dir_entry = readdir($dir_pointer) ) {

		if ( $dir_entry == '.' || $dir_entry == '..' ) { continue; };

			if (is_dir($directory."/".$dir_entry)) {

				$is_cleared = clearDir($directory."/".$dir_entry);
				if ( !$is_cleared ) { return false; };

			} else {

				$is_cleared = @unlink($directory."/".$dir_entry);
				if ( !$is_cleared ) { return false; };
			}

	}  // While End

	closedir($dir_pointer);
	$is_cleared=@rmdir($directory);

	if ( !$is_cleared ) { return false; };
	
	return true;

}  //  function End





//  The function below allows to create a thumbnail from images drawn of a given directory : 

function createThumbs( $pathToImages, $pathToThumbs, $thumbWidth, $plg_prefix_reception, $plg_gallery_ID, $creationType ) {

	$dir_pointer = opendir( $pathToImages );

	while (false !== ($dir_entry = readdir($dir_pointer))) {

		$info = pathinfo($pathToImages.$dir_entry);
		if ( ( strtolower($info['extension']) == 'jpg' ) || ( strtolower($info['extension']) == 'jpeg' ) ) {

      		$image = imagecreatefromjpeg( "{$pathToImages}{$dir_entry}" );
     			$width = imagesx( $image );
      		$height = imagesy( $image );

      		$new_width = $thumbWidth;
      		$new_height = floor( $height * ( $thumbWidth / $width ) );

     			$temporary_image = imagecreatetruecolor( $new_width, $new_height );
     			imagecopyresized( $temporary_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
      		imagejpeg( $temporary_image, "{$pathToThumbs}{$dir_entry}" );

			if ( $creationType == "thumbnail_regeneration" ) { 

				if ( get_option( $plg_prefix_reception.'compteur_images' ) != "") { 

					$compteur_images  = get_option( $plg_prefix_reception.'compteur_images' ); 

				} else { 

					$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$plg_gallery_ID.'_gallery_is_to_be_deleted' );

					if ( $gallery_is_to_be_deleted == 'false'  ) {

						update_option( $plg_prefix_reception.'compteur_images', '0' );
						$compteur_images  = get_option( $plg_prefix_reception.'compteur_images' ); 

					} else {
			
						delete_option( 'plugin_prefix'.$plg_gallery_ID.'_gallery_is_to_be_deleted' );

					};  //  Else 2 End 

				};  //  Else 1 End
				
				//  Database update , necessary to create the XML file containing the plugin parameters .
				
				$imageName = $info['basename'];
				$fullSizeImageWidth = get_image_infos($pathToImages.$imageName, 'width');	
                    	$fullSizeImageHeight = get_image_infos($pathToImages.$imageName, 'height');
                    	$thumbnailWidth = get_image_infos($pathToThumbs.$imageName, 'width');
                    	$thumbnailHeight = get_image_infos($pathToThumbs.$imageName, 'height');	
				
				update_option( $plg_prefix_reception.'imageName'.$compteur_images, $imageName );
				update_option( $plg_prefix_reception.'fullSizeImageWidth'.$compteur_images, $fullSizeImageWidth );
				update_option( $plg_prefix_reception.'fullSizeImageHeight'.$compteur_images, $fullSizeImageHeight );
				update_option( $plg_prefix_reception.'thumbnailWidth'.$compteur_images, $thumbnailWidth );
				update_option( $plg_prefix_reception.'thumbnailHeight'.$compteur_images, $thumbnailHeight );

				$compteur_images = intval(get_option( $plg_prefix_reception.'compteur_images' ));
				update_option( $plg_prefix_reception.'compteur_images', $compteur_images + 1 );
				
			}; //  If $creationType End

    		} else if ( strtolower($info['extension']) == 'png' ) {
		
      		$image = imagecreatefrompng( "{$pathToImages}{$dir_entry}" );
     			$width = imagesx( $image );
      		$height = imagesy( $image );

      		$new_width = $thumbWidth;
      		$new_height = floor( $height * ( $thumbWidth / $width ) );

     			$temporary_image = imagecreatetruecolor( $new_width, $new_height );
     			imagecopyresized( $temporary_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
      		imagepng( $temporary_image, "{$pathToThumbs}{$dir_entry}" );

			if ( $creationType == "thumbnail_regeneration" ) { 

				if ( get_option( $plg_prefix_reception.'compteur_images' ) != "") { 

					$compteur_images  = get_option( $plg_prefix_reception.'compteur_images' ); 

				} else { 

					$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$plg_gallery_ID.'_gallery_is_to_be_deleted' );

					if ( $gallery_is_to_be_deleted == 'false'  ) {

						update_option( $plg_prefix_reception.'compteur_images', '0' );
						$compteur_images  = get_option( $plg_prefix_reception.'compteur_images' ); 

					} else {
			
						delete_option( 'plugin_prefix'.$plg_gallery_ID.'_gallery_is_to_be_deleted' );

					};  //  Else 2 End 

				};  //  Else 1 End
				
				//  Database update , necessary to create the XML file containing the plugin parameters .
				
				$imageName = $info['basename'];
				$fullSizeImageWidth = get_image_infos($pathToImages.$imageName, 'width');	
                    	$fullSizeImageHeight = get_image_infos($pathToImages.$imageName, 'height');
                    	$thumbnailWidth = get_image_infos($pathToThumbs.$imageName, 'width');
                    	$thumbnailHeight = get_image_infos($pathToThumbs.$imageName, 'height');	
				
				update_option( $plg_prefix_reception.'imageName'.$compteur_images, $imageName );
				update_option( $plg_prefix_reception.'fullSizeImageWidth'.$compteur_images, $fullSizeImageWidth );
				update_option( $plg_prefix_reception.'fullSizeImageHeight'.$compteur_images, $fullSizeImageHeight );
				update_option( $plg_prefix_reception.'thumbnailWidth'.$compteur_images, $thumbnailWidth );
				update_option( $plg_prefix_reception.'thumbnailHeight'.$compteur_images, $thumbnailHeight );

				$compteur_images = intval(get_option( $plg_prefix_reception.'compteur_images' ));
				update_option( $plg_prefix_reception.'compteur_images', $compteur_images + 1 );
				
			}; //  If $creationType End
		}	
  	}  //  while End 

	closedir( $dir_pointer );

}  //  function createThumbs() End








//  The function below allows to resize a given image :

function resizeSingleImage( $imageName, $pathToOriginalImage, $pathToResizedImage, $newImageWidth ) {

	$dir_pointer = opendir( $pathToOriginalImage );

	while (false !== ($dir_entry = readdir( $dir_pointer ))) {

    		$info = pathinfo($pathToOriginalImage.$dir_entry);

		if ( $info['basename'] == $imageName ) {
		
   			if ( ( strtolower($info['extension']) == 'jpg' ) || ( strtolower($info['extension']) == 'jpeg' ) ) {

	      		$image = imagecreatefromjpeg( "{$pathToOriginalImage}{$dir_entry}" );
	     			$width = imagesx( $image );
	      		$height = imagesy( $image );

      			$new_width = $newImageWidth;
      			$new_height = floor( $height * ( $newImageWidth / $width ) );

	     			$temporary_image = imagecreatetruecolor( $new_width, $new_height );

		     		imagecopyresized( $temporary_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );

	      		imagejpeg( $temporary_image, "{$pathToResizedImage}{$dir_entry}" );

	    		} else if ( strtolower($info['extension']) == 'png' ) {
		
	      		$image = imagecreatefrompng( "{$pathToOriginalImage}{$dir_entry}" );
		     		$width = imagesx( $image );
      			$height = imagesy( $image );

	      		$new_width = $newImageWidth;
	      		$new_height = floor( $height * ( $newImageWidth / $width ) );

		     		$temporary_image = imagecreatetruecolor( $new_width, $new_height );
     				imagecopyresized( $temporary_image, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height );
      			imagepng( $temporary_image, "{$pathToResizedImage}{$dir_entry}" );

			}
    		}

	}  //  While End

  	closedir( $dir_pointer );

}  //  function resizeSingleImage() End










//  The function below allows to retrieve pieces of information about a given image ( either its width or its height or its type or a code which can be inserted into the <img> tag :

function get_image_infos($path_to_image, $type_infos) {

	$image_width = 0;
	$image_height = 0;
	$image_attr = '';

	list($image_width, $image_height, $image_type, $image_attr) = getimagesize($path_to_image);
		
	switch ( $type_infos ) {

		case "width":
			return $image_width;
			break;

		case "height":
			return $image_height;
			break;

		case "type":
			return $image_type;
    			break;

		case "attr":
			return $image_attr;
    			break;

	}  //  switch End
}  //  function get_image_width End

//  The function below allows to remove accents from a given sentence :

function wp_delete_accents($sentence) { 

	$charset='utf-8';
    	$sentence = htmlentities($sentence, ENT_NOQUOTES, $charset); 
     
    	$sentence = preg_replace('#\&([A-za-z])(?:acute|cedil|circ|grave|ring|tilde|uml)\;#', '\1', $sentence);
    	$sentence = preg_replace('#\&([A-za-z]{2})(?:lig)\;#', '\1', $sentence);
    	$sentence = preg_replace('#\&[^;]+\;#', '', $sentence);
	$sentence = preg_replace( '/\s+/', '_', $sentence);     

    	return $sentence;

}  //  function wp_delete_accents End

//  The function below allows to verify if the given entry already exists in the database  so that it avoids duplicate entries in the database :

function option_already_exists($type_option, $index_option) { 

	global $wp_gallery_plugin_prefix, $wp_gallery_settings_group_ID, $wp_gallery_settings_group_ID_request;

	$result = false;

	if ( get_option( $wp_gallery_plugin_prefix.'compteur_images' ) != "") { 

		$compteur_images  = get_option( $wp_gallery_plugin_prefix.'compteur_images' ); 

	} else { 

			$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			if ( $gallery_is_to_be_deleted == 'false'  ) {

				update_option( $wp_gallery_plugin_prefix.'compteur_images', '0' );
				$compteur_images  = get_option( $wp_gallery_plugin_prefix.'compteur_images' ); 

			} else {
			
				delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			};  //  Else 2 End 

	};  //  Else 1 End

	for ( $i = 0;  $i <= $compteur_images - 1; $i++) {

		if ( $type_option.$index_option == $type_option.$i ) { $result = true; };

	};
  
	return $result;

}  //  function option_already_exists End


//  This function allows to delete a given image :

function delete_image($index_option, $wp_gallery_plugin_prefix) {

	global $wp_gallery_settings_group_ID, $wp_gallery_settings_group_ID_request;

	if ( get_option( $wp_gallery_plugin_prefix.'compteur_images' ) != "") { 
 
		$compteur_images  = get_option( $wp_gallery_plugin_prefix.'compteur_images' ); 

	} else { 

			$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			if ( $gallery_is_to_be_deleted == 'false'  ) {

				update_option( $wp_gallery_plugin_prefix.'compteur_images', '0' );
				$compteur_images  = get_option( $wp_gallery_plugin_prefix.'compteur_images' ); 

			} else {
			
				delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID.'_gallery_is_to_be_deleted' );

			};  //  Else 2 End 

	};  //  Else 1 End

	for ( $i = 0;  $i <= $compteur_images - 2; $i++) {
	
		update_option( $wp_gallery_plugin_prefix.'imageName'.( $i + $index_option ), get_option( $wp_gallery_plugin_prefix.'imageName'.( $i + $index_option + 1 )) );	
		update_option( $wp_gallery_plugin_prefix.'fullSizeImageWidth'.( $i + $index_option ), get_option( $wp_gallery_plugin_prefix.'fullSizeImageWidth'.( $i + $index_option + 1 )) );
		update_option( $wp_gallery_plugin_prefix.'fullSizeImageHeight'.( $i + $index_option ), get_option( $wp_gallery_plugin_prefix.'fullSizeImageHeight'.( $i + $index_option + 1 )) );
		update_option( $wp_gallery_plugin_prefix.'thumbnailWidth'.( $i + $index_option ), get_option( $wp_gallery_plugin_prefix.'thumbnailWidth'.( $i + $index_option + 1 )) );
		update_option( $wp_gallery_plugin_prefix.'thumbnailHeight'.( $i + $index_option ), get_option( $wp_gallery_plugin_prefix.'thumbnailHeight'.( $i + $index_option + 1 )) );
	};
		delete_option( $wp_gallery_plugin_prefix.'imageName'.($compteur_images - 1 ) );	
		delete_option( $wp_gallery_plugin_prefix.'fullSizeImageWidth'.($compteur_images - 1 ) );
		delete_option( $wp_gallery_plugin_prefix.'fullSizeImageHeight'.($compteur_images - 1 ) );
		delete_option( $wp_gallery_plugin_prefix.'thumbnailWidth'.($compteur_images - 1 ) );
		delete_option( $wp_gallery_plugin_prefix.'thumbnailHeight'.($compteur_images - 1 ) );

		update_option( $wp_gallery_plugin_prefix.'compteur_images', ($compteur_images - 1 ) );

}  //  function delete_image End

//  The function below returns the ID of the first valid gallery which is located below the one whose gallery ID equals to $wp_gallery_settings_group_ID  :

function first_valid_gallery_ID( $wp_gallery_settings_group_ID ) {

	$first_valid_gallery_ID = $wp_gallery_settings_group_ID - 1;

	//  Decreasing While loop to seek first gallery valid ID :

	while ( ( get_option( 'plugin_prefix'.$first_valid_gallery_ID ) == "" )  && ( $first_valid_gallery_ID > 0 ) ) {

		$first_valid_gallery_ID  -= 1;

	}; //  Loop While End

	return $first_valid_gallery_ID;

}  //  funtion first_valid_gallery_ID  End




//  The function below allows to handle the different requests launched by the user , from the admin panel :

function mytheme_add_admin() {
 
global $admin_panel_title, $wp_gallery_plugin_width, $wp_gallery_plugin_height, $wp_gallery_plugin_prefix, $wp_gallery_settings_group_ID, $wp_gallery_settings_group_ID_request, $wp_gallery_settings_group_ID_reception;

if ( $_GET['page'] == basename(__FILE__) ) {

	switch ( $_REQUEST['action'] ) {

		case 'save_gallery_ID' :

			 $wp_gallery_settings_group_ID_reception = $_REQUEST['plugin_prefix_gallery_ID'];

			update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception, 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_' );
			update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_ID', $wp_gallery_settings_group_ID_reception );


			//  This instruction creates an indicator which determines if the gallery options must be totally deleted or only reset .
			//  That is to be said , if its value is set to ""false" , then the gallery is no to be deleted , thus the gallery options will only be reset .

			update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted', 'false' );

			
			//  Incrementation of the galleries counter :

			if ( get_option( 'wp_gallery_plugin_group_counter' ) != "") { 

				$galleries_counter  = intval(get_option( 'wp_gallery_plugin_group_counter' )); 

			} else { 

				update_option( 'wp_gallery_plugin_group_counter', '0' );
				$galleries_counter  = 0;

			};

			//  Greater Gallery ID storage :

			if ( $wp_gallery_settings_group_ID_reception > $galleries_counter ) {

				update_option( 'wp_gallery_plugin_group_counter', $wp_gallery_settings_group_ID_reception );			

			};

			header("Location: admin.php?page=wp_donimedia_carousel.php&gallery_ID_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."");
			die;

    			break;

		case 'save_plugin_width' :
			
			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'plugin_width', $_REQUEST[$wp_gallery_plugin_prefix_reception.'plugin_width'] ); 

			header("Location: admin.php?page=wp_donimedia_carousel.php&plugin_width_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'save_plugin_height' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'plugin_height', $_REQUEST[$wp_gallery_plugin_prefix_reception.'plugin_height'] ); 
 
			header("Location: admin.php?page=wp_donimedia_carousel.php&plugin_height_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'save_magnifying_glass_visibility' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'magnifying_glass_visibility', $_REQUEST[$wp_gallery_plugin_prefix_reception.'magnifying_glass_visibility'] ); 
 
			header("Location: admin.php?page=wp_donimedia_carousel.php&magnifying_glass_visibility_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;


		case 'delete_all_full_size_images' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			$full_size_images_upload_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/';
			$thumbnail_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/';

			if ( is_dir( $full_size_images_upload_directory  ) ) {

				clear_directory_or_file($full_size_images_upload_directory);
				clear_directory_or_file($thumbnail_directory);

				$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' );

				for ($i = 0; $i <= $compteur_images - 1; $i++) {
    			
					delete_option( $wp_gallery_plugin_prefix_reception.'imageName'.$i );
					delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageWidth'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageHeight'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailWidth'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailHeight'.$i ); 
			
				};
			
				update_option( $wp_gallery_plugin_prefix_reception.'compteur_images', '0' );
 
				header("Location: admin.php?page=wp_donimedia_carousel.php&all_full_size_image_deleted=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
				die;
			
			} else {

				header("Location: admin.php?page=wp_donimedia_carousel.php&no_image_uploaded=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
				die;
			}

    			break;
			
		case 'save_thumbnail_images_width' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];
			$thumbnail_images_width_reception = $_REQUEST[$wp_gallery_plugin_prefix_reception.'thumbnail_images_width'];

			$full_size_images_upload_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/';
			$thumbnail_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/';

			if ( is_dir( $full_size_images_upload_directory  ) ) {

				clear_directory_or_file($thumbnail_directory);

				$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' );

				for ($i = 0; $i <= $compteur_images - 1; $i++) {
    			
					delete_option( $wp_gallery_plugin_prefix_reception.'imageName'.$i );
					delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageWidth'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageHeight'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailWidth'.$i ); 
					delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailHeight'.$i ); 
			
				};

				$compteur_images = 0;
				update_option( $wp_gallery_plugin_prefix_reception.'compteur_images', $compteur_images );

				createThumbs($full_size_images_upload_directory,$thumbnail_directory, $thumbnail_images_width_reception, $wp_gallery_plugin_prefix_reception, $wp_gallery_settings_group_ID_reception, "thumbnail_regeneration");

				update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width', $thumbnail_images_width_reception );

				header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_images_width_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
				die;

			} else {

				header("Location: admin.php?page=wp_donimedia_carousel.php&no_image_uploaded=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
				die;
			}

    			break;

		case 'save_thumbnail_border_color' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_border_color', $_REQUEST[$wp_gallery_plugin_prefix_reception.'thumbnail_border_color'] );

			header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_border_color_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'save_carousel_radius' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_radius', $_REQUEST[$wp_gallery_plugin_prefix_reception.'thumbnail_carousel_radius'] ); 

			header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_carousel_radius_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'save_carousel_horizontal_position' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_horizontal_position', $_REQUEST[$wp_gallery_plugin_prefix_reception.'thumbnail_carousel_horizontal_position'] ); 

			header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_carousel_horizontal_position_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'save_carousel_vertical_position' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_vertical_position', $_REQUEST[$wp_gallery_plugin_prefix_reception.'thumbnail_carousel_vertical_position'] ); 

			header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_carousel_vertical_position_saved=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'delete_thumbnail_image' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			$full_size_images_upload_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/';
			$thumbnail_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/';

			$image_index = $_REQUEST['image_index'];

			$image_name = get_option( $wp_gallery_plugin_prefix_reception.'imageName'.$image_index);

			delete_image($image_index, $wp_gallery_plugin_prefix_reception);
			
			clear_directory_or_file($full_size_images_upload_directory.$image_name);

			clear_directory_or_file($thumbnail_directory.$image_name);
			
			if ( get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ) != "") { 

				$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

			} else { 

				$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

				if ( $gallery_is_to_be_deleted == 'false'  ) {

					update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width', '100' );
					$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

				} else {
			
					delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

				};  //  Else 2 End 

			};  //  Else 1 End

			$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' );
			
			for ($i = 0; $i <= $compteur_images - 1; $i++) {
    				
				$imageName = get_option( $wp_gallery_plugin_prefix_reception.'imageName'.$i );
				update_option( $wp_gallery_plugin_prefix_reception.'thumbnailWidth'.$i, get_image_infos($thumbnail_directory.$imageName, 'width') ); 
				update_option( $wp_gallery_plugin_prefix_reception.'thumbnailHeight'.$i, get_image_infos($thumbnail_directory.$imageName, 'height') ); 
					
			};			
		
			header("Location: admin.php?page=wp_donimedia_carousel.php&thumbnail_image_deleted=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die;

    			break;

		case 'reset' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			$full_size_images_upload_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/';
			$thumbnail_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/';

			delete_option( $wp_gallery_plugin_prefix_reception.'plugin_width' );
			delete_option( $wp_gallery_plugin_prefix_reception.'plugin_height' );
			delete_option( $wp_gallery_plugin_prefix_reception.'magnifying_glass_visibility' );
			delete_option( $wp_gallery_plugin_prefix_reception.'magnifying_glass_visibility_zoom' );
			delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' );
			delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_border_color' );
			delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_radius' );
			delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_horizontal_position' );
			delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_vertical_position' );

			if ( get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ) != "") { 

				$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

			} else { 

				$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

				if ( $gallery_is_to_be_deleted == 'false'  ) {

					update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width', '100' );
					$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

				} else {
			
					delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

				};  //  Else 2 End 

			};  //  Else 1 End

			clear_directory_or_file($thumbnail_directory);
			createThumbs($full_size_images_upload_directory,$thumbnail_directory, $thumbnail_images_width,  $wp_gallery_plugin_prefix_reception, $wp_gallery_settings_group_ID_reception, "options_reset");

			header("Location: admin.php?page=wp_donimedia_carousel.php&reset=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die; 

    			break;

		case 'delete_gallery' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			$main_gallery_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/';

			clearDir($main_gallery_directory);

			$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' );

			for ($i = 0; $i <= $compteur_images - 1; $i++) {
    			
				delete_option( $wp_gallery_plugin_prefix_reception.'imageName'.$i );
				delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageWidth'.$i ); 
				delete_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageHeight'.$i ); 
				delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailWidth'.$i ); 
				delete_option( $wp_gallery_plugin_prefix_reception.'thumbnailHeight'.$i ); 
			
			};

			delete_option( substr($wp_gallery_plugin_prefix_reception, 0, strlen($wp_gallery_plugin_prefix_reception)-1));
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'gallery_ID');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'magnifying_glass_visibility');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'plugin_height');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'plugin_width');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_border_color');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_horizontal_position');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_radius');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_carousel_vertical_position');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width');
 	 	 	delete_option( $wp_gallery_plugin_prefix_reception.'compteur_images');

			// Decrementation of the galleries counter :

			$galleries_counter = intval(get_option( 'wp_gallery_plugin_group_counter' ));

			//  Greater Gallery ID storage : If user removed a gallery whose ID was the greatest of all ( for instance , 4 ) , then the new greatest ID 
			//  is the integer that precedes ( Therefore : 3 ) . 

			//  Where ( $wp_gallery_settings_group_ID_reception > $galleries_counter ) is not discussed because this case never met here

			if ( $wp_gallery_settings_group_ID_reception == $galleries_counter ) { 

				//  In this case, using a function , we must seek first valid gallery ID ( that is to say, the first gallery with a plugin_prefix ) , 
				//  which is located below the one whose gallery ID equals to $galleries_counter :

				update_option( 'first_valid_gallery_ID', first_valid_gallery_ID( $wp_gallery_settings_group_ID_reception ) );

				if ( first_valid_gallery_ID( $wp_gallery_settings_group_ID_reception ) != 0 ) {

					update_option( 'wp_gallery_plugin_group_counter', first_valid_gallery_ID( $wp_gallery_settings_group_ID_reception ) );				

				} else {

					delete_option( 'wp_gallery_plugin_group_counter' );

				};
			};

			//  This instruction updates the indicator which determines if the gallery options must be totally deleted or only reset .
			//  In this case , its value is set to "true" in order to indicate that the gallery is to be deleted , thus the gallery options will be totally deleted .

			update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted', 'true' );

			//  Redirection to admin page with appropriate message :

			header("Location: admin.php?page=wp_donimedia_carousel.php&delete_gallery=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
			die; 

    			break;

		case 'upload-plugin' :

			$wp_gallery_plugin_prefix_reception = $_REQUEST['plugin_prefix_request'];
			$wp_gallery_settings_group_ID_reception = $_REQUEST['gallery_ID_request'];

			$old = umask(0);  // Necessary to change the chmod of the newly created directories . Otherwise directories images won't display ( even though they really are in those directories 

			if ( !(is_dir( '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/' )) ) {

				mkdir('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/', 0755, true);
				mkdir('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/', 0755, true);
			
				chmod('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/', 0755);
		     		chmod('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/', 0755);
			     	chmod('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/', 0755);
			     	chmod('../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/', 0755);

				umask($old);

				// verification :
				if ($old != umask()) {
			    		die('An error occured while changing back the umask');
				}

			}

			$full_size_images_upload_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/full_size_images_upload/';
			$thumbnail_directory = '../wp-content/plugins/wp_donimedia_carousel/gallery'.$wp_gallery_settings_group_ID_reception.'/thumbnail/';
						
			if ((($_FILES["fichier"]["type"] == "image/jpeg")
			|| ($_FILES["fichier"]["type"] == "image/jpg")
			|| ($_FILES["fichier"]["type"] == "image/pjpeg")
			|| ($_FILES["fichier"]["type"] == "image/png")))  {

			if ($_FILES["fichier"]["error"] > 0) {

				echo '<div id="message" class="updated fade"><p><strong>Return Code : '.$_FILES["fichier"]["error"].'<br /></strong></p></div>';  //  Affichage raffiné .

				header("Location: admin.php?page=wp_donimedia_carousel.php&uploading_error_message=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."&uploading_error=".$_FILES["fichier"]["error"]."");
				die;

    			} else if (  is_uploaded_file($_FILES['fichier']['tmp_name']) ) {

					if ( ( intval(get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' )) < 10 )) {
						
    						$expurgated_image_name = wp_delete_accents($_FILES['fichier']['name']);

						move_uploaded_file($_FILES["fichier"]["tmp_name"],$full_size_images_upload_directory.$expurgated_image_name);

						$image_width = get_image_infos($full_size_images_upload_directory.$expurgated_image_name, 'width');

						if ( get_option( $wp_gallery_plugin_prefix_reception.'plugin_width' ) != "") { 

							$wp_gallery_plugin_width  = get_option( $wp_gallery_plugin_prefix_reception.'plugin_width' ); 

						} else { 

							$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							if ( $gallery_is_to_be_deleted == 'false'  ) {

								update_option( $wp_gallery_plugin_prefix_reception.'plugin_width', '600' );
								$wp_gallery_plugin_width  = get_option( $wp_gallery_plugin_prefix_reception.'plugin_width' ); 

							} else {
			
								delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							};  //  Else 2 End 

						};  //  Else 1 End

						if ( $image_width >  $wp_gallery_plugin_width) { $image_width = $wp_gallery_plugin_width; };

						//  Quand on importe une image "pleine taille" , on crée automatiquement une image de "petite taille" , dans le thumbnail .

						if ( get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ) != "") { 

							$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

						} else { 

							$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							if ( $gallery_is_to_be_deleted == 'false'  ) {

								update_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width', '100' );
								$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix_reception.'thumbnail_images_width' ); 

							} else {
			
								delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							};  //  Else 2 End 

						};  //  Else 1 End

						resizeSingleImage( $expurgated_image_name, $full_size_images_upload_directory, $full_size_images_upload_directory, $image_width );
						
						createThumbs($full_size_images_upload_directory,$thumbnail_directory, $thumbnail_images_width, $wp_gallery_plugin_prefix_reception, $wp_gallery_settings_group_ID_reception, "thumbnail_first_creation");

						//  Database update , necessary to create the XML file containing the plugin parameters .
						
						$imageName = $expurgated_image_name;
						$fullSizeImageWidth = $image_width;		
                    			$fullSizeImageHeight = get_image_infos($full_size_images_upload_directory.$expurgated_image_name, 'height');
                    			$thumbnailWidth = get_image_infos($thumbnail_directory.$expurgated_image_name, 'width');
                    			$thumbnailHeight = get_image_infos($thumbnail_directory.$expurgated_image_name, 'height');	
						
						if ( get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' ) != "") { 

							$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' ); 

						} else { 

							$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							if ( $gallery_is_to_be_deleted == 'false'  ) {

								update_option( $wp_gallery_plugin_prefix_reception.'compteur_images', '0' );
								$compteur_images  = get_option( $wp_gallery_plugin_prefix_reception.'compteur_images' ); 

							} else {
			
								delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_reception.'_gallery_is_to_be_deleted' );

							};  //  Else 2 End 

						};  //  Else 1 End
						
						if ( !(option_already_exists( $wp_gallery_plugin_prefix_reception.'imageName', $compteur_images)) ) { 

							update_option( $wp_gallery_plugin_prefix_reception.'imageName'.$compteur_images, $imageName );
							update_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageWidth'.$compteur_images, $fullSizeImageWidth );
							update_option( $wp_gallery_plugin_prefix_reception.'fullSizeImageHeight'.$compteur_images, $fullSizeImageHeight );
							update_option( $wp_gallery_plugin_prefix_reception.'thumbnailWidth'.$compteur_images, $thumbnailWidth );
							update_option( $wp_gallery_plugin_prefix_reception.'thumbnailHeight'.$compteur_images, $thumbnailHeight );

							$compteur_images += 1;
							update_option( $wp_gallery_plugin_prefix_reception.'compteur_images', $compteur_images );

						};
		
						header("Location: admin.php?page=wp_donimedia_carousel.php&image_uploaded=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."&image_uploaded_name=".$expurgated_image_name."&full_size_images_upload_directory=".get_bloginfo('url').substr($full_size_images_upload_directory, 2, strlen($full_size_images_upload_directory))."");
						die;

					} else   {

						header("Location: admin.php?page=wp_donimedia_carousel.php&images_total_error=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
						die;

					}

   				 }  //  else End

  			} else   {

				//  echo "Invalid file";   //  Affichage classique .
				//  echo '<div id="message" class="updated fade"><p><strong>Invalid file : verify the image extension or the image size , please . </strong></p></div>';  //  Affichage raffiné .

				header("Location: admin.php?page=wp_donimedia_carousel.php&upload_file_error=true&gallery_ID_request=".$wp_gallery_settings_group_ID_reception."&plugin_prefix_request=".$wp_gallery_plugin_prefix_reception."");
				die;
  			}

    			break;

	}  //  switch End

}  // if ( $_GET['page'] End



$admin_panel_title = "WP donimedia carousel";

//  add_menu_page($admin_panel_title, $admin_panel_title, 'administrator', basename(__FILE__), 'mytheme_admin');  //  This instruction allows to display the plugin options in a top level menu .
add_options_page($admin_panel_title, $admin_panel_title, 'administrator', basename(__FILE__), 'mytheme_admin');   //  This instruction allows to display the plugin options in a submenu of the SETTINGS menu .

}  //  mytheme_add_admin End

//  The function below allows to add the addresses of the plugin stylesheet and of a JQuery script , between the <HEAD> and </HEAD> tags :

function mytheme_add_init() {

	$file_dir=get_bloginfo('url')."/wp-content/plugins/wp_donimedia_carousel";
	wp_enqueue_style("pluginStylesheet", $file_dir."/styles/styles.css", false, "1.0", "all");
	wp_enqueue_script("pluginJQueryScript", $file_dir."/scripts/script.js", false, "1.0");
}

//  The function below allows to display the different admin panel options and the infos messages appropriate to the user actions :

function mytheme_admin() {
 
	global $admin_panel_title, $wp_gallery_plugin_width, $wp_gallery_plugin_height, $wp_gallery_plugin_prefix, $fullSizeImagesUploadDirectory, $thumbnailDirectory, $wp_gallery_settings_group_ID, $wp_gallery_settings_group_ID_request;

	$i=0;
	$image_width = 0;

	if ( $_REQUEST['gallery_ID_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Gallery ID saved .</strong></p></div>'; };
	if ( $_REQUEST['plugin_width_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Plugin width saved .</strong></p></div>'; };
	if ( $_REQUEST['plugin_height_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Plugin height saved .</strong></p></div>'; };
	if ( $_REQUEST['uploading_error_message'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Uploading error code : '.$_REQUEST['uploading_error'].'</strong></p></div>'; };
	if ( $_REQUEST['image_uploaded'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>'.$_REQUEST['image_uploaded_name'].' stored in : <br />'.$_REQUEST['full_size_images_upload_directory'].'</strong></p></div>'; };
	if ( $_REQUEST['upload_file_error'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Invalid file : verify the image extension or the image size , please . </strong></p></div>'; };
	if ( $_REQUEST['images_total_error'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong> You must not upload more than 10 images in a gallery to avoid images overlapping . </strong></p></div>'; };
	if ( $_REQUEST['magnifying_glass_visibility_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Magnifying glass visibility saved .</strong></p></div>'; };
	if ( $_REQUEST['all_full_size_image_deleted'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>All full size images deleted ( the associated thumbnail has been automatically deleted too ! ) . </strong></p></div>'; };

	if ( $_REQUEST['no_image_uploaded'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>You must first upload at least one image !</strong></p></div>'; };

 	if ( $_REQUEST['thumbnail_images_width_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Thumbnail images width saved . If needed , don\'t forget to adjust the spacing between the thumbnail images by changing the carousel radius !</strong></p></div>'; };
	if ( $_REQUEST['thumbnail_border_color_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Thumbnail border color saved .</strong></p></div>';  };
 	if ( $_REQUEST['thumbnail_carousel_radius_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Carousel radius saved .</strong></p></div>'; };
 	if ( $_REQUEST['thumbnail_carousel_horizontal_position_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Thumbnail carousel horizontal position saved .</strong></p></div>'; };
 	if ( $_REQUEST['thumbnail_carousel_vertical_position_saved'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Thumbnail carousel vertical position saved .</strong></p></div>'; };
	if ( $_REQUEST['thumbnail_image_deleted'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>Thumbnail Image deleted .</strong></p></div>'; };
	if ( $_REQUEST['reset'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>'.$admin_panel_title.' settings reset .</strong></p></div>'; };
	if ( $_REQUEST['delete_gallery'] ) { $wp_gallery_plugin_prefix_request  = $_REQUEST['plugin_prefix_request']; $wp_gallery_settings_group_ID_request  = $_REQUEST['gallery_ID_request']; echo '<div id="message" class="updated fade"><p><strong>'.$admin_panel_title.' '.$wp_gallery_settings_group_ID_request.' deleted . </strong></p></div>'; };

	if ( get_option( $wp_gallery_plugin_prefix_request.$wp_gallery_settings_group_ID_request ) != "") { 

		$wp_gallery_plugin_prefix  = get_option( $wp_gallery_plugin_prefix_request.$wp_gallery_settings_group_ID_request );
		echo "plugin_prefix_request.$wp_gallery_settings_group_ID_request = ".$wp_gallery_plugin_prefix_request.$wp_gallery_settings_group_ID_request; 

	} else { 

		$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

		if ( $gallery_is_to_be_deleted == 'false'  ) {

			update_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request, 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_');  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )
			$wp_gallery_plugin_prefix  = 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_';

		} else {
			
			delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

		};  //  Else 2 End 

	};  //  Else 1 End









//  Gestion de l'affichage côté BACKEND :
//  -----------------------------------

$admin_panel_title = "WP donimedia carousel";


echo '<div class="wrap container">'."\n";
echo '<h2>'.$admin_panel_title.' Settings</h2>'."\n";

echo '<div class="container_options">'."\n";

echo '<div class="subdivision">'."\n";
echo '<div class="title"><h3><img src="../component/images/trans.png" class="close" alt=""">General settings</h3>'."\n";
echo '<div class="clear_both"></div></div>'."\n";
echo '<div class="options">'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'gallery_ID' ) != "") { 

	$wp_gallery_settings_group_ID  = get_option( $wp_gallery_plugin_prefix.'gallery_ID' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'gallery_ID', '0' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )	
		$wp_gallery_settings_group_ID  = "0"; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action=admin.php?page=wp_donimedia_carousel.php&action=save_gallery_ID" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.'plugin_prefix_gallery_ID'.'">Gallery identifier : <br /> ( Unique number > 0 )<br /> ( default ID is 0 )</label>'."\n";
echo ' 	<input name="'.'plugin_prefix_gallery_ID'.'" id="'.'plugin_prefix_gallery_ID'.'" type="text" value="'.$wp_gallery_settings_group_ID.'" />'."\n";
echo ' <span class="submit"><input name="save_gallery_ID" type="submit" value="Save the gallery ID" /></span>'."\n"; 
if ( $wp_gallery_settings_group_ID ) { echo '	<small class="first_option">To display Gallery '.$wp_gallery_settings_group_ID.' in your post , copy the following code and paste it into your post :<br /><b class="red_color">[wp_gallery'.$wp_gallery_settings_group_ID.'][/wp_gallery'.$wp_gallery_settings_group_ID.']</b></small><div class="clear_both"></div>'."\n"; };
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_gallery_ID" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'plugin_width' ) != "") { 

	$wp_gallery_plugin_width  = get_option( $wp_gallery_plugin_prefix.'plugin_width' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'plugin_width','600' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )	
		$wp_gallery_plugin_width  = '600'; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_plugin_width&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'plugin_width'.'">Plugin width : <br /> ( default is 600 pixels )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'plugin_width'.'" id="'.$wp_gallery_plugin_prefix.'plugin_width'.'" type="text" value="'.$wp_gallery_plugin_width.'" />'."\n";
echo ' <span class="submit"><input name="save_plugin_width" type="submit" value="Save the plugin width" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_plugin_width" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'plugin_height' ) != "") { 

	$wp_gallery_plugin_height  = get_option( $wp_gallery_plugin_prefix.'plugin_height' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'plugin_height','600' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )	
		$wp_gallery_plugin_height  = '600';

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_plugin_height&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'plugin_height'.'">Plugin height : <br /> ( default is 600 pixels )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'plugin_height'.'" id="'.$wp_gallery_plugin_prefix.'plugin_height'.'" type="text" value="'.$wp_gallery_plugin_height.'" />'."\n";
echo ' <span class="submit"><input name="save_plugin_height" type="submit" value="Save the plugin height" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_plugin_height" />'."\n";
echo '</form>'."\n";

echo '<div style="position: relative; margin-bottom: 50px;">'."\n";

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=reset&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '	<p class="input_left" >'."\n";
echo '		<input name="reset" type="submit" value="Reset all options !" />'."\n";
echo '		<input type="hidden" name="action" value="reset" />'."\n";
echo '	</p>'."\n";
echo '</form>'."\n";

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=delete_gallery&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '	<p class="input_right" >'."\n";
echo '		<input name="delete_gallery" type="submit" value="Delete the gallery !" />'."\n";
echo '		<input type="hidden" name="action" value="delete_gallery" />'."\n";
echo '	</p>'."\n";
echo '</form>'."\n";

echo '</div>'."\n";
echo '</div>'."\n";
echo '</div>'."\n";
echo '<br />'."\n";

echo '<div class="subdivision">'."\n";
echo '<div class="title"><h3><img src="../component/images/trans.png" class="close" alt=""">Full size images settings</h3>'."\n";
echo '<div class="clear_both"></div>'."\n";
echo '</div>'."\n";
echo '<div class="options">'."\n";

$remaining_uploads = 10 - intval(get_option( $wp_gallery_plugin_prefix.'compteur_images' ));

echo '<div class="input upload">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'images_upload'.'">Upload your images : <br />( Total max uploads : 10 )<br />( Remaining uploads : '.$remaining_uploads.' )</label>'."\n";
echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=upload-plugin&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post" enctype="multipart/form-data">'."\n";
echo '<input type="file" name="fichier" id="fichier_id" /> <input type="submit" name="upload" value="Submit" />'."\n";
echo '	<small>Authorized extensions :<br />  png , jpg , jpeg .<br />Recommended Max image size :<br /> 150 Ko .</small><div class="clear_both"></div>'."\n";
echo '</form>'."\n";
echo '</div>'."\n";

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_magnifying_glass_visibility&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input select">'."\n";
echo '<label for="'.$wp_gallery_plugin_prefix.'magnifying_glass_visibility'.'">Magnifying glass : </label>'."\n";
echo '<select name="'.$wp_gallery_plugin_prefix.'magnifying_glass_visibility'.'" id="'.$wp_gallery_plugin_prefix.'magnifying_glass_visibility'.'">'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'magnifying_glass_visibility' ) != "") { 

	$magnifying_glass_visibility  = get_option( $wp_gallery_plugin_prefix.'magnifying_glass_visibility' ); 
	if ( get_option( $wp_gallery_plugin_prefix.'magnifying_glass_visibility' ) == "visible") { 

		$option_selected_attribute_for_option1 = 'selected="selected"';
		$option_selected_attribute_for_option2 = '';

	} else { 

		$option_selected_attribute_for_option1 = '';
		$option_selected_attribute_for_option2 = 'selected="selected"';

	};

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'magnifying_glass_visibility','visible' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )	
		$option_selected_attribute_for_option1 = 'selected="selected"';
		$option_selected_attribute_for_option2 = '';

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '	<option '.$option_selected_attribute_for_option1.' >visible</option>'."\n";
echo '	<option '.$option_selected_attribute_for_option2.' >hidden</option>'."\n";
echo '</select><input type="submit" name="save_magnifying_glass_visibility" value="Submit" />'."\n";
echo '<small></small><div class="clear_both"></div>'."\n";

echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_magnifying_glass_visibility" />'."\n";
echo '</form>'."\n";

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=delete_all_full_size_images&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '	<p class="submit">'."\n";
echo '		<input name="delete_all_full_size_images" type="submit" value="Delete all full size images !" />'."\n";
echo '	<small>The associated thumbnail will be automatically deleted too !</small>'."\n";
echo '		<input type="hidden" name="action" value="delete_all_full_size_images" />'."\n";
echo '	</p>'."\n";
echo '</form>'."\n";
 
echo '</div>'."\n";
echo '</div>'."\n";
echo '<br />'."\n";

echo '<div class="subdivision">'."\n";
echo '<div class="title"><h3><img src="../component/images/trans.png" class="close" alt=""">Thumbnail settings</h3>'."\n";
echo '<div class="clear_both"></div></div>'."\n";
echo '<div class="options">'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'thumbnail_images_width' ) != "") { 

	$thumbnail_images_width  = get_option( $wp_gallery_plugin_prefix.'thumbnail_images_width' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'thumbnail_images_width','100' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )	
		$thumbnail_images_width  = '100'; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_thumbnail_images_width&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";

echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'thumbnail_images_width'.'">Thumbnail images width : <br /> ( default is 100 pixels )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'thumbnail_images_width'.'" id="'.$wp_gallery_plugin_prefix.'thumbnail_images_width'.'" type="text" value="'.$thumbnail_images_width.'" />'."\n";
echo ' <span class="submit"><input name="save_thumbnail_images_width" type="submit" value="Save the width" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_thumbnail_images_width" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'thumbnail_border_color' ) != "") { 

	$thumbnail_border_color  = get_option( $wp_gallery_plugin_prefix.'thumbnail_border_color' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'thumbnail_border_color','ffffff' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )
		$thumbnail_border_color  = 'ffffff'; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_thumbnail_border_color&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";

echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'thumbnail_border_color'.'">Thumbnail border color : <br /> ( default is ffffff )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'thumbnail_border_color'.'" id="'.$wp_gallery_plugin_prefix.'thumbnail_border_color'.'" type="text" value="'.$thumbnail_border_color.'" />'."\n";
echo ' <span class="submit"><input name="save_thumbnail_border_color" type="submit" value="Save the border color" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_thumbnail_border_color" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_radius' ) != "") { 

	$thumbnail_carousel_radius  = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_radius' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_radius','150' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )
		$thumbnail_carousel_radius  = '150';

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_carousel_radius&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_radius'.'">Carousel radius : <br /> ( Must be between 0 to 200 )<br /> ( Default is 150 )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_radius'.'" id="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_radius'.'" type="text" value="'.$thumbnail_carousel_radius.'" />'."\n";
echo ' <span class="submit"><input name="save_carousel_radius" type="submit" value="Save the radius" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_carousel_radius" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position' ) != "") { 

	$thumbnail_carousel_horizontal_position  = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position','450' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )
		$thumbnail_carousel_horizontal_position  = '450'; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_carousel_horizontal_position&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position'.'">Carousel horizontal position : <br /> ( Distance from left border )<br />( Default is 450 )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position'.'" id="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position'.'" type="text" value="'.$thumbnail_carousel_horizontal_position.'" />'."\n";
echo ' <span class="submit"><input name="save_carousel_horizontal_position" type="submit" value="Save the position" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_carousel_horizontal_position" />'."\n";
echo '</form>'."\n";

if ( get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position' ) != "") { 

	$thumbnail_carousel_vertical_position  = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position' ); 

} else { 

	$gallery_is_to_be_deleted = get_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	if ( $gallery_is_to_be_deleted == 'false'  ) {

		update_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position','100' );  //  This instruction is very useful to store the option as soon as the plugin runs ( and more precisely , before the user modify it )
		$thumbnail_carousel_vertical_position  = '100'; 

	} else {
			
		delete_option( 'plugin_prefix'.$wp_gallery_settings_group_ID_request.'_gallery_is_to_be_deleted' );

	};  //  Else 2 End 

};  //  Else 1 End

echo '<form action="admin.php?page=wp_donimedia_carousel.php&action=save_carousel_vertical_position&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'" method="post">'."\n";
echo '<div class="input text">'."\n";
echo '	<label for="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position'.'">Carousel vertical position : <br /> ( Distance from top border )<br />( Default is 100 )</label>'."\n";
echo ' 	<input name="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position'.'" id="'.$wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position'.'" type="text" value="'.$thumbnail_carousel_vertical_position.'" />'."\n";
echo ' <span class="submit"><input name="save_carousel_vertical_position" type="submit" value="Save the position" /></span>'."\n";
 
echo ' </div>'."\n";
echo '	<input type="hidden" name="action" value="save_carousel_vertical_position" />'."\n";
echo '</form>'."\n";

//  ----------------------------------------------------------   This part of the code creates the XML file which gather all the information necessary for the Flash movieclip  ------------------------------------------------------------------

$fullSizeImagesUploadDirectory = WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/gallery'.$wp_gallery_settings_group_ID_request.'/full_size_images_upload/';
$thumbnailDirectory = WP_PLUGIN_URL.'/'.basename(dirname(__FILE__)).'/gallery'.$wp_gallery_settings_group_ID_request.'/thumbnail/';

//  The block of instructions below creates arrays of parameters :

$imageName = array();
$fullSizeImageWidth = array();
$fullSizeImageHeight = array();
$thumbnailWidth = array();
$thumbnailHeight = array();
$thumbnailImagesSpacing = array();

//  The block of instructions below retrieves plugin parameters and options values stored in database :

$wp_gallery_plugin_width  = get_option( $wp_gallery_plugin_prefix.'plugin_width' );
$wp_gallery_plugin_height  = get_option( $wp_gallery_plugin_prefix.'plugin_height' );
$magnifying_glass_visibility = get_option( $wp_gallery_plugin_prefix.'magnifying_glass_visibility' );
$thumbnail_border_color = get_option( $wp_gallery_plugin_prefix.'thumbnail_border_color' );
$thumbnail_carousel_radius = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_radius' );
$thumbnail_carousel_horizontal_position = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_horizontal_position' );
$thumbnail_carousel_vertical_position = get_option( $wp_gallery_plugin_prefix.'thumbnail_carousel_vertical_position' );

$compteur_images  = get_option( $wp_gallery_plugin_prefix.'compteur_images' );

for ($i = 0; $i <= $compteur_images - 1; $i++) {
    			
	$imageName[$i] = get_option( $wp_gallery_plugin_prefix.'imageName'.$i );  
	$fullSizeImageWidth[$i] = get_option( $wp_gallery_plugin_prefix.'fullSizeImageWidth'.$i ); 
	$fullSizeImageHeight[$i] = get_option( $wp_gallery_plugin_prefix.'fullSizeImageHeight'.$i ); 
	$thumbnailWidth[$i] = get_option( $wp_gallery_plugin_prefix.'thumbnailWidth'.$i ); 
	$thumbnailHeight[$i] = get_option( $wp_gallery_plugin_prefix.'thumbnailHeight'.$i ); 
	
};

//  The instruction below creates an Instance of DOMDocument class :

$doc_xml = new DOMDocument();

//  The instructions below defines the XML file version and encoding :

$doc_xml->version = '1.0'; 
$doc_xml->encoding = 'ISO-8859-1';

$parameters_group = $doc_xml->createElement("parameters_group");		//  This instruction creates the root element and associates it to the XML document .
$doc_xml->appendChild($parameters_group);								//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('fullSizeImagesUploadDirectory', trim($fullSizeImagesUploadDirectory));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('thumbnailDirectory', trim($thumbnailDirectory));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('pluginWidth', trim($wp_gallery_plugin_width));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('pluginHeight', trim($wp_gallery_plugin_height));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('magnifyingGlassVisibility', trim($magnifying_glass_visibility));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('thumbnailBorderColor', trim($thumbnail_border_color));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('thumbnailCarouselRadius', trim($thumbnail_carousel_radius));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('thumbnailCarouselHorizontalPosition', trim($thumbnail_carousel_horizontal_position));

$item = $doc_xml->createElement("item");						//  This instruction creates the "item" element and associates it to the XML document .
$parameters_group->appendChild($item);						//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .
$item->setAttribute('thumbnailCarouselVerticalPosition', trim($thumbnail_carousel_vertical_position));

for ( $i = 0; $i <= ( $compteur_images - 1 ); $i++ ) {

	$item = $doc_xml->createElement("item");					//  This instruction creates the "item" element which contains a parameters data and associates it to the XML document .
	$parameters_group->appendChild($item);					//  This instruction adds the element previously created  as a "child node" of the existing structure of the XML document .


	//   Attributes are assigned to the "item" element, knowing that each "item" element must be in the following form :
	//  <item imageName="image_name.png" fullSizeImageWidth="333.5" fullSizeImageHeight="250" thumbnailWidth="333.5" thumbnailHeight="250" />

	$item->setAttribute('imageName', trim($imageName[$i]));
	$item->setAttribute('fullSizeImageWidth', trim($fullSizeImageWidth[$i]));
	$item->setAttribute('fullSizeImageHeight', trim($fullSizeImageHeight[$i]));
	$item->setAttribute('thumbnailWidth', trim($thumbnailWidth[$i]));
	$item->setAttribute('thumbnailHeight', trim($thumbnailHeight[$i]));

}  //  For End

//  The instruction below improves the XML document presentation :
$doc_xml->formatOutput = true;

//  The instruction below displays the XML document , only on the screen :
//  echo $doc_xml->saveXML();

//  The instruction below saves the XML document in a file whose name is in the following form : movieclip_parameters'.$wp_gallery_settings_group_ID_request.'.xml
$doc_xml->save('../wp-content/plugins/wp_donimedia_carousel/component/movieclip_parameters'.$wp_gallery_settings_group_ID_request.'.xml');

//  The instructions below allow to display the thumbnail preview , at the bottom side of the admin panel :

for ( $i = 0; $i <= $compteur_images -1; $i++ ) {

	if ( $imageName[$i] != "" ) {

	echo '<div class="container_backend_images">'."\n";
	echo '<div class="backend_images">'."\n";

	echo '	<div class="">'."\n";
	echo '		<img src="'.trim($thumbnailDirectory.$imageName[$i]).'" "width="'.trim($thumbnailWidth[$i]).'" height="'.trim($thumbnailHeight[$i]).'"/>'."\n";
	echo '	</div>'."\n";
	echo '<div class="backend_images_text"><a href="admin.php?page=wp_donimedia_carousel.php&action=delete_thumbnail_image&image_index='.$i.'&gallery_ID_request='.$wp_gallery_settings_group_ID_request.'&plugin_prefix_request='.$wp_gallery_plugin_prefix.'">delete</a>'."\n";
	echo '</div>'."\n";

	echo '</div>'."\n";
	echo '</div>'."\n";

	};  // If End

}  //  For End

echo '</div>'."\n";
echo '</div>'."\n";
echo '<br />'."\n";

}

add_action('admin_init', 'mytheme_add_init');
add_action('admin_menu', 'mytheme_add_admin');

?>