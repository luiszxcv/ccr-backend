<?php
/**
 * informes functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package informes
 */

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}

$template_diretorio = get_template_directory();

/*
filtragem na api rest
*/
require_once($template_diretorio . "/template-parts/filtragem.php");
#--------------------------------------------------

/*
functions query informes
*/
require_once($template_diretorio . "/template-parts/informes.php");
#--------------------------------------------------

/*
query categories data
*/
require_once($template_diretorio . "/template-parts/informes_categories.php");
#--------------------------------------------------


/*
incremento de resposta users
*/
require_once($template_diretorio . "/template-parts/users.php");
#--------------------------------------------------



/*
rotas de localizações
*/



function distance($lat1, $lon1, $lat2, $lon2, $unit) {
  if (($lat1 == $lat2) && ($lon1 == $lon2)) {
    return 0;
  }
  else {
    $theta = $lon1 - $lon2;
    $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
    $dist = acos($dist);
    $dist = rad2deg($dist);
    $miles = $dist * 60 * 1.1515;
    $unit = strtoupper($unit);

    if ($unit == "K") {
      return ($miles * 1.609344);
    } else if ($unit == "N") {
      return ($miles * 0.8684);
    } else {
      return $miles;
    }
  }
}


add_action('rest_api_init', 'registrar_coordenadas');

function registrar_coordenadas() {
    register_rest_route('wp/v2', 'coordenadas', array(
      array(
        'methods' => "POST",
        'callback' => 'api_coordenadas_post',
      ),
    ));
}

function api_coordenadas_post($request) {

    if ($request["coordenadas"] != null && $request["coordenadas"] !=""){
		
		$coordendas = wp_remote_get( 'http://enterprise.escalepro.com.br/wp-json/wp/v2/coordenadas');

		foreach(json_decode($coordendas["body"], true) as $key => $lugar){
		//$distance =distance($coordendas["centro"][0], $coordendas["centro"][1], $request["coordenadas"][0], $request["coordenadas"][1],"K");
		$distance= distance( $request["coordenadas"][0], $request["coordenadas"][1],$lugar[0], $lugar[1],"K");
		if ($distance < 0.4)/* 400 metros de precisão*/ {
			$response=[true,$key] ;
		break;
		} else {
			$response= [false,$key] ;
		  }
		}
    }
    else{
        $response = new WP_Error('Erro', 'Erro, coordenadas não localizadas.', array('status' => 404));   
    }
    return $response;
}


//get---------------------------


add_action('rest_api_init', 'registrar_api_coordenadas_get');
function registrar_api_coordenadas_get() {
    register_rest_route('wp/v2', 'coordenadas', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_coordenadas_get',
      ),
    ));
}

function api_coordenadas_get($request){

	$response =["Centro Sinop"=>array(-11.853489, -55.512705)];
    return $response ;
}


