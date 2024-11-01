<?php

/*
Plugin Name: WP REST API Add Taxnomies
Description: This plugin adds Taxnomies to WP REST API(ver2)
Author: Nakashima Masahiro
Version: 1.0.0
Author URI: http://www.kigurumi.asia
*/

/**
 * Class and Function List:
 * Function list:
 * - wp_rest_api_add_taxonomies()
 * - wp_rest_api_register_taxonomy()
 * Classes list:
 */

class WP_REST_API_Add_Taxnomies {
    
    public function __construct() {
        add_action( 'init', array($this, 'wp_rest_api_add_taxonomies'), 12 );
    }

    function wp_rest_api_add_taxonomies() {
        
        $post_types = get_post_types( array(
            'public' => true
        ) , 'objects' );
        
        foreach( $post_types as $post_type ) {
            
            $show_in_rest =( isset( $post_type->show_in_rest ) && $post_type->show_in_rest ) ? true : false;
            
            if( $show_in_rest ) {
                
                $taxonomies = get_taxonomies( array(), "objects" );
                foreach ( $taxonomies as $taxonomy ){
                    if ( is_object_in_taxonomy( $post_type->name, $taxonomy->name ) ) {
                        register_api_field( $post_type->name, $taxonomy->name, array(
                            'get_callback' => array($this, 'wp_rest_api_register_taxonomy'),
                            'schema' => null,
                        ) );
                    }
                }
            }
        }
    }

    function wp_rest_api_register_taxonomy( $object, $field_name, $request ) {
        
        if( $object['id'] ) {
            $post_id = (int)$object['id'];
        } 
        else {
            return null;
        }
        
        $the_terms = get_the_terms( $post_id, $field_name );
        
        if( !$the_terms ) {
            return null;
        }
        
        $terms = array();
        foreach( $the_terms as $key => $term ) {
            $terms[] = $term;
        }
        
        return $terms;
    }    
}
new WP_REST_API_Add_Taxnomies();




