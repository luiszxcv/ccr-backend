<?php

/*
GET modificando as respostas
*/

/*
    add_action( 'rest_api_init', 'create_api_posts_meta_field_informes' );
    
    function create_api_posts_meta_field_informes() {
    
        // register_rest_field ( 'name-of-post-type', 'name-of-field-to-return', array-of-callbacks-and-schema() )
        register_rest_field( 'informes', 'acf', array(
            'get_callback'    => 'get_post_meta_for_api_informes',
            'schema'          => null,
            )
        );
    }
    function get_post_meta_for_api_informes( $object ) {
        //get the id of the post object array
        $post_id = $object['id'];
    
        //return the post meta~
        $post_meta = [];
        $post_meta_geral = get_post_meta( $post_id );
        foreach ($post_meta_geral as $key => $topicos){
            $post_meta[$key]= (maybe_unserialize($topicos[0]));
        }
        //$post_meta_image =$post_meta_geral['foodbakery_array_data'];
        return $post_meta;
    }
*/
#------------------------------------------------------
 

/*
functions multi uso de formatação json
*/

function informe_scheme($post_id) {
    //$post_id = get_informe_id_by_slug($slug);
  
    $images = get_attached_media('image', $post_id);
    $images_array = null;
  
    if($images) {
      $images_array = array();
      foreach($images as $key => $value) {
        $images_array[] = array(
          'titulo' => $value->post_name,
          'src' => $value->guid,
        );
      }
    }
    
    if($post_id) {
      $post_meta = get_post_meta($post_id);
      //$response=$post_meta;
      $response = array(
        'imagens' => $images_array,
        'name' => $post_meta['name'][0],
        'id_proprietario' => $post_meta['id_proprietario'][0],
        'responsaveis' => $post_meta['responsaveis'][0],
        'responsaveis_data' => $post_meta['responsaveis_data'][0],
        'contact' => $post_meta['contact'][0],
        'coordenadas' => $post_meta['coordenadas'][0],
      );
    } else {
      $response = new WP_Error('Erro', 'informe nao encontrado.', array('status' => 404));
    }
  
    return $response;
}

function category_scheme($post_id){
    $categories = [];

    $tags = wp_get_post_terms( $post_id, 'category', array( 'fields' => 'all' ));
    foreach ($tags as $tag){
        $categories["tags"][get_ancestors($tag->term_id, 'category')[0]][] = $tag;
    }

    $categories["status"] = wp_get_post_terms( $post_id, 'post_tag', array( 'fields' => 'all' ));

    return $categories;
}

function query_informes_by_user($user_id,$per_page,$page,$author_ID){

    // mandar para o front se ele for o proprietário
    $id_proprietario_query = null;

    if(current_user_can( 'author' ) || current_user_can( 'administrator' ) ) {
      
    }
    else{
        $response = new WP_Error('Erro', 'Sem permissões necessárias.', array('status' => 404));
        //return $response;
    }
    $id_proprietario_query = array(
        'key' => 'id_proprietario',
        'value' => $author_ID,
        'compare' => '='
      );

    // mandar para o front se ele estiver entre os responsáveis
    $responsaveis= array(
      'key' => 'responsaveis',
      'value'   => ",".$user_id.",",
      'compare' => 'LIKE'
    );

    $query = array(
        'post_type' => 'informes',
        'posts_per_page' => $per_page,
        'paged' => $page,
        'orderby' => 'date',
        'order'   => 'DESC',
        'meta_query' => array(
            'relation' => 'OR',
            $id_proprietario_query,
            $responsaveis
        )
    );

    $loop = new WP_Query($query);
    $informes= $loop->posts;
    $total = $loop->found_posts;
  
    foreach($informes as $key => $value) {
      $value->acf = informe_scheme($value->ID);
      $value->categories = category_scheme($value->ID);
    }
    
    // mandar total de informes
    $response = rest_ensure_response($informes);
    $response->header('X-Total-Count', $total);
    
    return($response);
}

function query_informe_by_user($user_id,$informe_id){

    // mandar para o front se ele for o proprietário
    $id_proprietario_query = null;
    if(current_user_can( 'author' ) || current_user_can( 'administrator' ) ) {
      $id_proprietario_query = array(
        'key' => 'id_proprietario',
        'value' => $user_id,
        'compare' => '='
      );
    }
    else{
        $response = new WP_Error('Erro', 'Sem permissões necessárias.', array('status' => 404));
        return $response;
    }

    // mandar para o front se ele estiver entre os responsáveis
    $responsaveis= array(
      'key' => 'responsaveis',
      'value'   => ",".$user_id.",",
      'compare' => 'LIKE'
    );

    $query = array(
        'post_type' => 'informes',
        'p' => $informe_id,
        'meta_query' => array(
            'relation' => 'OR',
            $id_proprietario_query,
            $responsaveis
        )
    );

    $loop = new WP_Query($query);
    $informes= $loop->posts;
    $total = $loop->found_posts;
  
    foreach($informes as $key => $value) {
        $value->acf = informe_scheme($value->ID);
        $value->categories = category_scheme($value->ID);
    }
    
    // mandar total de informes
    $response = rest_ensure_response($informes[0]);
    $response->header('X-Total-Count', $total);
    
    return($response);
}

function update_informe($user_id,$request){

    // mandar para o front se ele for o proprietário
    $id_proprietario_query = null;
    if(current_user_can( 'author' ) || current_user_can( 'administrator' ) ) {
      $id_proprietario_query = array(
        'key' => 'id_proprietario',
        'value' => $user_id,
        'compare' => '='
      );
    }
    else{
        $response = new WP_Error('Erro', 'Sem permissões necessárias.', array('status' => 404));
        return $response;
    }

    // mandar para o front se ele estiver entre os responsáveis
    $responsaveis= array(
      'key' => 'responsaveis',
      'value'   => ",".$user_id.",",
      'compare' => 'LIKE'
    );

    $query = array(
        'post_type' => 'informes',
        'p' => $request["id"],
        'meta_query' => array(
            'relation' => 'OR',
            $id_proprietario_query,
            $responsaveis
        )
    );

    $loop = new WP_Query($query);
    $informes= $loop->posts;
    $informe = $informes[0];
    
    if ($informe != null && $informe != false && $informe != ""){
        if ($request["acf"] != null && $request["acf"] != false && $request["acf"] != ""){
            foreach ( $request["acf"] as $key => $value){
                update_post_meta($informe->ID, $key, $value);
            }
        }
        if ($request["title"] != null && $request["title"] != false && $request["title"] != ""){
            $myinforme = array(
                'ID'           =>$informe->ID,
                'post_title'   => $request["title"],
            );
            wp_update_post( $myinforme );
        }
        if ($request["categories"] != null && $request["categories"] != false && $request["categories"] != ""){

            $tags = [];
            foreach ( explode(",",$request["categories"]) as $categors){
                $tags[]= intval($categors);
            }
            $term_taxonomy_ids = wp_set_object_terms($informe->ID, $tags, 'category' );
            
        }
        if ($request["tags"] != null && $request["tags"] != false && $request["tags"] != ""){
            wp_set_object_terms( $informe->ID, array(intval($request["tags"])), "post_tag");
        }
        if ($request["status"] != null && $request["status"] != false && $request["status"] != ""){
            wp_update_post(array(
                'ID'    =>  $informe->ID,
                'post_status'   => $request["status"]
            ));
        }

        

        return(true);
    }
    return(false);
}


#------------------------------------------------------

/*
GET modificando a resposta original e filtrando o query para somente informes com propriedade do user (todos os informes)
*/

add_action('rest_api_init', 'registrar_api_informes_get');
function registrar_api_informes_get() {
    register_rest_route('wp/v2', 'informes', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_informes_get',
      ),
    ));
}

function api_informes_get($request){

    $q = sanitize_text_field($request['q']) ?: '';
    $page = sanitize_text_field($request['page']) ?: 0;
    $per_page = sanitize_text_field($request['per_page']) ?: 10;
    $user_id = get_current_user_id();
    $author_id = sanitize_text_field($request['author']);

    return query_informes_by_user($user_id,$per_page,$page,$author_id);
}
#------------------------------------------------------

/*
GET desabilitando get api routing (informe único)
*/

add_action('rest_api_init', 'registrar_api_informe_get');

function registrar_api_informe_get() {
    register_rest_route('wp/v2', 'informes/(?P<id>[\\d]+)', array(
      array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'api_informe_get',
      ),
    ));
}

function api_informe_get($request) {
    $user_id = get_current_user_id();

    //$response = informe_scheme($request['id']);
    $response = new WP_Error('Desabilitado', 'Endpoint desabilitado.', array('status' => 404));
    
    //query_informe_by_user($user_id,$informe_id)
    return $response;//rest_ensure_response($response);
}
#------------------------------------------------------

/*
POST modificando a resposta original e filtrando o query para somente informes com propriedade do user (informe único)
*/

add_action('rest_api_init', 'registrar_api_informe_post');

function registrar_api_informe_post() {
    register_rest_route('wp/v2', 'informes/(?P<id>[\\d]+)', array(
      array(
        'methods' => 'POST',
        'callback' => 'api_informe_post',
      ),
    ));
}

function api_informe_post($request) {

    $user_id = get_current_user_id();

    $confirm = update_informe($user_id,$request);

    if ($confirm){
        $response = query_informe_by_user($user_id,$request["id"]); //new WP_Error('Desabilitado', 'Endpoint desabilitado.', array('status' => 404));
        //return $response;//rest_ensure_response($response);
    }
    else{
        $response = new WP_Error('Erro', 'Sem permissão.', array('status' => 404));   
    }
    return $response;
}

#------------------------------------------------------

/*
POST criando post meta do informe (ACF Fields)
*/

add_action( 'rest_api_init', 'criando_informes_meta' );

function criando_informes_meta() {

            $field_name = 'acf';
            register_rest_field( 'informes',
                'acf',
                array(
                    'get_callback'    => function ($params) use ($field_name) {
                        

                        
                        $phone_f = "556696181212";//.preg_replace('/\D+/', '', get_user_meta( get_current_user_id(), 'contato_familiar' , true ));
                        ////------------
                        $endpoint = 'http://boot.escalepro.com.br:8090/v1/send';
                        
                        $body = [
                            "phone"=>$phone_f,
                            "body"=> "Olá Luís o motorista ".$params["acf"]["name"]." acaba de passar pelo ponto de checkin -> Catedral Sinop"
                        ];
                        
                        $body = wp_json_encode( $body );
                        
                        $options = [
                            'body'        => $body,
                            'headers'     => [
                                'Content-Type' => 'application/json',
                            ],
                            'data_format' => 'body',
                        ];
                        
                        $response = wp_remote_post( $endpoint, $options );

                        return informe_scheme($params['id']);
                    },
                    'update_callback' => function ($values, $object, $field_name){ 

                            //$post_meta_geral = get_post_meta( $object->ID );
                            foreach ( $values as $key => $value){
                                update_post_meta($object->ID, $key, $value);
                            }

                            $array_idsResponsaveis = [];
                            $response_responsaveis=[];
                            $user_info =[];
                            $equipe_ids = "";
                            $idResponsavel="";
                            
                            if ($values["responsaveis"] != "" && $values["responsaveis"] != null ){
                                $array_idsResponsaveis = explode(",",$values["responsaveis"]);
                                $array_idsResponsaveis = array_slice($array_idsResponsaveis,1,-1);
                                foreach ($array_idsResponsaveis as $idResponsavel){
                                    
                                    $user_info = get_userdata($idResponsavel);
                                    $equipe_ids= get_user_meta( $idResponsavel, 'equipe' , true );

                                    $response_responsaveis[$idResponsavel] = ["id"=>$user_info->ID,
                                        "username"=> $user_info->user_login,
                                        "fullName"=> $user_info->first_name." ".$user_info->last_name,
                                        "role"=>$user_info->roles,
                                        "equipe"=>$equipe_ids,
                                    ];
                                }
                            }
                            update_post_meta($object->ID,"coordenadas",json_encode($values["coordenadas"])); 

                            update_post_meta($object->ID,"responsaveis_data",json_encode($response_responsaveis));  
                            update_post_meta($object->ID,"id_proprietario",get_current_user_id());  

                        return $values;
                    },
                    'schema' => null
                )
            );

            $field_name = 'categories';
            register_rest_field( 'informes',
                'categories',
                array(
                    'get_callback'    => function ($params) use ($field_name) {
                        return category_scheme($params['id']);
                    },
                    'schema' => null
                )
            );
}
#------------------------------------------------------



?>