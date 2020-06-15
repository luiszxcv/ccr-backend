<?php
/*
functions de query
*/


function user_scheme($user_id){

    $user_info = get_userdata($user_id);
    if ($user_info){
        $equipe = get_user_meta( $user_id, 'equipe' , true );
        $nome_c = get_user_meta( $user_id, 'nome_caminhoneiro' , true );
        $numero_c = get_user_meta( $user_id, 'contato_caminhoneiro' , true );
        $numero_f = get_user_meta( $user_id, 'contato_familiar' , true );
        $nome_f = get_user_meta( $user_id, 'nome_familiar' , true );
        $username =$user_info->user_login;
        $user_role= implode(', ', $user_info->roles);


        $first_name ="";
        $first_name = $user_info->first_name;
        $last_name ="";
        $last_name = $user_info->last_name;

        if ($first_name == "" && $last_name==""){
            $nome=$username;
        }
        else{
            $nome= $first_name." ".$last_name;
        }

        //$avatar_url = get_avatar( $user_id);
        /*$total= count( get_posts( array( 
            'post_type' => 'informes', 
            'author'    => $user_id, 
            'nopaging'  => true, // display all posts
        ) ) );*/
        
        $data = array(
            'username' => $username,
            'id' => $user_id,
            'role' => $user_role,
            'name' => $nome,
            'equipe' => $equipe,
            "nome_caminhoneiro"=>$nome_c,
            "contato_caminhoneiro"=>$numero_c,
            "nome_familiar"=>$nome_f,
            "contato_familiar"=>$numero_f,
            
            //'total_informes'=> $total
            //'avatar_url'=> $avatar_url
        );
    }
    else{
        return false;
    }
    return $data;
}

function query_users_by_user($user_id){

    // mandar para o front se ele for o proprietário  
    if(current_user_can( 'author' )) {
        //
    }
    elseif( !current_user_can( 'administrator' ) ){
        $response = new WP_Error('Erro', 'Sem permissões necessárias.', array('status' => 404));
        return $response;
    }

    // mandar para o front se ele estiver entre os responsáveis
    
        $equipe= array(
            'key' => 'equipe',
            'value'   => ",".$user_id.",",
            'compare' => 'LIKE'
        );/**/

        // WP_User_Query arguments
        /*$args = array (
            'role' => 'author',
            'order' => 'ASC',
            'orderby' => 'display_name',
            'meta_query' => array(
                $equipe
            )
        );
    */


    /*$equipe = get_user_meta($user_id, 'equipe' , true );
    $arrayIds_equipe = explode(",",$equipe);
    $arrayIds_equipe = array_slice($arrayIds_equipe,1,-1);*/
        
    $arrayIds_equipe = get_users(array(
        'meta_query' => array(
            $equipe
        ),
        'role'    => 'author',
        'orderby' => 'user_nicename',
        'order'   => 'ASC',
        array( 'fields' => array( 'ID' ))),
        

    );

    $users = [];

    $ind =0;
    foreach ($arrayIds_equipe as $id) {
        $user=false;
        $user = user_scheme($id->ID);
        if ($user){
            $users[]= $user;
        }
    }

    $total = count($users);
    
    // mandar total de users
    $response = rest_ensure_response($users);
    $response->header('X-Total-Count', $total);
    
    return($response);
}
#-----------------------------------------------------

/*
modificando a resposta do post jwt de token e atualizando ultimo login
*/
add_filter('jwt_auth_token_before_dispatch', 'do_something', 10, 2);
function do_something($data, $user)
{   
    date_default_timezone_set("America/Belem");
    update_user_meta( $user->data->ID, '_ultimo_login', date("d/m/Y")." ".date("H:i:sa") );
    $u_login= get_user_meta( $user_id, '_ultimo_login' , true );

    $response = user_scheme($user->data->ID);
    $response['token']=$data['token'];
    $response['u_login'] = $u_login;
    
    return $response;
}
#---------------------------------------------------------------------


/*
modificando a resposta do post jwt de token e atualizando ultimo login
*/

add_action( 'rest_api_init', 'adding_user_meta_rest' );

function adding_user_meta_rest() {
    register_rest_field( 'user','acf',array(
        'get_callback'      =>function ($params) use ($field_name)
        {
            return array("equipe"=>get_user_meta($params['id'], 'equipe' , true ));
        },
        'update_callback'   =>function ($values, $object, $field_name)
        { 
            foreach ( $values as $key => $value){
                update_user_meta($object->ID, $key, $value);
            }

        },
        'schema'=> null,
    ));
    register_rest_field( 'user','name',array(
        'get_callback'      =>function ($params) use ($field_name)
        {
            return get_user_meta($params['id'], 'name' , true );
        },
        'update_callback'   =>function ($values, $object, $field_name)
        { 
            $name =$values;
            if ($name != null && $name != false && $name != ""){
                $name = explode(" ",$name);
                $f_name = $name[0];
                $l_name = implode(" ", array_slice($name,1));
                $name = implode(" ",$name);
            
                update_user_meta( $object->ID, "first_name", $f_name);
                update_user_meta( $object->ID, "last_name", $l_name);
                update_user_meta($object->ID, "name", $name);
            }

        },
        'schema'=> null,
    ));
}
#------------------------------------------------------


/*
GET modificando a resposta do post users segundo o tipo de usuário (todos os users)
*/

add_action('rest_api_init', 'registrar_api_users_get');

function registrar_api_users_get() {
    register_rest_route('wp/v2', 'users', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_users_get',
      ),
    ));
}

function api_users_get($request) {

    $user_id = get_current_user_id();
    $response = query_users_by_user($user_id);
    //$response = new WP_Error('Desabilitado', 'Endpoint desabilitado.', array('status' => 404));
    return $response;//rest_ensure_response($response);
}

add_action('rest_api_init', 'registrar_api_user_get');

function registrar_api_user_get() {
    register_rest_route('wp/v2', '/users/(?P<id>\d+)', array(
      array(
        'methods' => 'GET',
        'callback' => 'api_user_get',
      ),
    ));
}

function api_user_get($request) {

    $user_id = get_current_user_id();
    if ($user_id == $request["id"]){
        $response= user_scheme($user_id);
    }
    else{
        $response = new WP_Error('Erro', 'Sem permissão.', array('status' => 404));
    }
    return $response;
}

//////////-------------post update user
add_action('rest_api_init', 'registrar_api_user_post');

function registrar_api_user_post() {
    register_rest_route('wp/v2', '/users/(?P<id>\d+)', array(
      array(
        'methods' => 'POST',
        'callback' => 'api_user_post',
      ),
    ));
}

function api_user_post($request) {

    $user_id = get_current_user_id();

    if ($request['equipe_modifi'] == "add"){
        $equipe = get_user_meta($request['id'], 'equipe' , true );
        update_user_meta($request["id"], "equipe", $equipe.$user_id.",");
    }
    if ($request['equipe_modifi'] == "remove"){

        $equipe = get_user_meta($request['id'], 'equipe' , true );
        $arrayIds_equipe = explode(",",$equipe);
        $arrayIds_equipe = array_slice($arrayIds_equipe,1,-1);

        if (($key = array_search($user_id, $arrayIds_equipe )) !== false) {
            unset($arrayIds_equipe[$key]);
        }

        update_user_meta($request["id"], "equipe", ",".implode(",",$arrayIds_equipe).",");
    }

    if ($request["meta"] != null && $request["meta"] != "" && $request["meta"] != "undefined"){
        foreach ($request["meta"] as $keym => $valuem)
        update_user_meta( $request["id"],$keym ,$valuem);
    }
   
    $response= user_scheme($request["id"]);
    return $response;
}



/*
POST Verificando se user existe
*/
add_action('rest_api_init', 'registrar_api_user_exist');

function registrar_api_user_exist() {
    register_rest_route('wp/v2', '/existuser', array(
      array(
        'methods' => 'POST',
        'callback' => 'verifi_userExist',
      ),
    ));
}

function verifi_userExist($request) {

    update_user_meta( 89,"contato_familiar" ,"556696181212"); 
    update_user_meta( 89,"nome_familiar" ,"Luís"); 

    $array_email = explode("@",$request["email"]);
    $count_number= strlen($array_email[0]);

    $email = $array_email[0];

    if($count_number == 13){
        if(substr($array_email[0],0,2) =="55"){
            $email = substr($array_email[0],2);
        }
    }
    if($count_number == 11){
        if(substr($array_email[0],2,1) =="9"){
            //$email = substr( $array_email[0],0,2);
            //$email.= substr( $array_email[0],3);
        }
    }
    if($count_number == 10){
        if(substr($array_email[0],0,2) =="66"){
            $email = substr( $array_email[0],0,2);
            $email.= "9".substr( $array_email[0],2);
        }
    }
    if($count_number == 12){
        if(substr($array_email[0],0,2) =="55"){
            //$email = substr( $array_email[0],0,2);
            $email="669";
            $email.= substr( $array_email[0],4);
        }
    }

    if($count_number == 9){
        if(substr($array_email[0],0,1) =="9"){
            $email ="66"+$email;
        }
    }
    if($count_number == 8){
        if(is_numeric($email)){
            $email ="669"+$email;
        }
        
    }
    $email.="@".$array_email[1];

    $user = get_user_by( 'email', $email);
    
    if ( !$user ) {
        $user = get_user_by('username', $email);
    }   

    if ( $user ) {
        $response= $user->ID;
    }else{
        $response = new WP_Error('Erro',  "Usuário ".$email." inexistente", array('status' => 404));
    }
    return $response;
}

