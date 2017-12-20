<?php
/*
 Plugin Name:Flip Boxes
 Plugin URI:http://www.cooltimeline.com
 Description:Use animated Flip Boxes WordPress plugin to highlight your content inside your page in a great way. Use shortcode to add anywhere.
 Version:1.2.2
 License:GPL2
 Author:Cool Timeline Team
 Author URI:http://www.cooltimeline.com
 License URI:https://www.gnu.org/licenses/gpl-2.0.html
 Domain Path: /languages 
 Text Domain:c-flipboxes
*/
defined( 'ABSPATH' ) or die( "No script kiddies please!" );
if( !defined( 'CFB_VERSION' ) ) {
    define( 'CFB_VERSION', '1.2.2' );
}
if( !defined( 'CFB_DIR_PATH' ) ) {
	define( 'CFB_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if( !defined( 'CFB_URL' ) ) {
    define( 'CFB_URL', plugin_dir_url( __FILE__ ));	
}

if( !defined( 'CFB_CSS_URL' ) ) {
    define( 'CFB_CSS_URL', CFB_URL. 'css' );	
}
if( !defined( 'CFB_IMAGE_DIR' ) ) {
    define( 'CFB_IMAGE_DIR', CFB_URL . 'images' );
}
if( !defined( 'CFB_JS_DIR' ) ) {
    define( 'CFB_JS_DIR', CFB_URL . 'js' );
}

if( !class_exists( 'CflipBoxes' ) ) 
{
	class CflipBoxes 
	{
		
		/* Initializes the plugin functions*/
		function __construct()  
		{
			if ( file_exists( CFB_DIR_PATH . '/CMB2/init.php' ) ) {
            require_once CFB_DIR_PATH . '/CMB2/init.php';
            require_once CFB_DIR_PATH . '/cmb2-fontawesome-picker.php';
            }
			add_action('init', array($this,'cfb_register_post_type'));
			add_action( 'cmb2_admin_init', array($this,'cfb_metaboxes'));
			add_action( 'cmb2_admin_init', array($this, 'cfb_settings' ));
			add_action( 'wp_enqueue_scripts',array($this,'cfb_register_frontend_assets')); //registers js and css for frontend
			add_shortcode( 'flipboxes',array($this,'cfb_shortcode'));
			add_filter('manage_edit-flipboxes_columns',array($this, 'cfb_add_custom_columns'));
			add_action('manage_flipboxes_posts_custom_column', array($this, 'cfb_columns_content'), 10, 2 );
			add_action( 'add_meta_boxes',array($this, 'cfb_shortcode_metabox' ));
			add_action( 'admin_notices',array($this,'cfb_admin_messages'));
            add_action( 'wp_ajax_cfb_hideRating',array($this,'cfb_HideRating' ));
		}
	
	/**
         * Activating plugin and adding some info
         */
        public static function activate() {
              update_option("Flip-Boxes-v",CFB_VERSION);
              update_option("Flip-Boxes-type","FREE");
              update_option("Flip-Boxes-installDate",date('Y-m-d h:i:s') );
              update_option("Flip-Boxes-ratingDiv","yes");
        }

		// END public static function activate

        /**
         * Deactivate the plugin
         */
        public static function deactivate() {
            // Do nothing
        } 
      
				      // Admin notificaiton for review  
   public function cfb_admin_messages() {
  
     if( !current_user_can( 'update_plugins' ) ){
        return;
     }
    $install_date = get_option( 'Flip-Boxes-installDate' );
    $ratingDiv =get_option( 'Flip-Boxes-ratingDiv' )!=false?get_option( 'Flip-Boxes-ratingDiv'):"no";

    $dynamic_msz='<div class="cool_fivestar update-nag" style="box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);">'.__('
      <p>Dear Flip Boxes Plugin User, Hopefully you\'re happy with our plugin. <br> May I ask you to give it a <strong>5-star rating</strong> on WordPress.org ? 
        This will help to spread its popularity and to make this plugin a better one.
        <br><br>Your help is much appreciated.Thank you very much!').'
        <ul>
            <li class="float:left"><a href="https://wordpress.org/support/plugin/flip-boxes/reviews/?filter=5#new-post" class="thankyou button button-primary" target="_new" title="I Like Flipbox" style="color: #ffffff;-webkit-box-shadow: 0 1px 0 #256e34;box-shadow: 0 1px 0 #256e34;font-weight: normal;float:left;margin-right:10px;">'.__('I Like Flip Boxes').'</a></li>
            <li><a href="javascript:void(0);" class="coolHideRating button" title="I already did" style="">'.__('I already rated it').'</a></li>
            <li><a href="javascript:void(0);" class="coolHideRating" title="No, not good enough" style="">'.__('No, not good enough, i do not like to rate it!').'</a></li>
        </ul>
    </div>
    <script>
    jQuery( document ).ready(function( $ ) {

    jQuery(\'.coolHideRating\').click(function(){
        var data={\'action\':\'cfb_hideRating\'}
             jQuery.ajax({
        
        url: "' . admin_url( 'admin-ajax.php' ) . '",
        type: "post",
        data: data,
        dataType: "json",
        async: !0,
        success: function(e) {
            if (e=="success") {
               jQuery(\'.cool_fivestar\').slideUp(\'fast\');
         
            }
        }
         });
        })
    
    });
    </script>';

     if(get_option( 'Flip-Boxes-installDate' )==false && $ratingDiv== "yes" )
       {
       echo $dynamic_msz;
       }else{
            $display_date = date( 'Y-m-d h:i:s' );
            $install_date= new DateTime( $install_date );
            $current_date = new DateTime( $display_date );
            $difference = $install_date->diff($current_date);
          $diff_days= $difference->days;
        if (isset($diff_days) && $diff_days>=15 && $ratingDiv == "yes" ) {
            echo $dynamic_msz;
          }
     	 }      

  	}
  
	  // ajax handler for feedback callback
	  public function cfb_HideRating() {
	    update_option( 'Flip-Boxes-ratingDiv','no' );
	    echo json_encode( array("success") );
	    exit;
	    }
  
  
		/*Define the metabox and field configurations.*/
		function cfb_settings() 
		{
			// Start with an underscore to hide fields from custom fields list
			$prefix = '_cfb_';
			
			/*Initiate the metabox*/
			$flip = new_cmb2_box( array(
				'id'            => 'side-mt',
				'title'         => __( 'Flipbox Settings', 'cmb2' ),
				'object_types'  => array( 'flipboxes', ), // Post type
				'context'       => 'side',
				'priority'      => 'low',
				'show_names'    => true, // Show field names on the left
			) );

			// Regular text field
			$flip->add_field( array(
				'name'               => __( 'Flipbox layout','cmb2' ),
				'desc'               => __( 'Select Flipbox Layout','cmb2' ),
				'id'                 => $prefix . 'flip_layout',
				'type'               => 'select',
				'show_option_none'   => false,
				'default'            => 'dashed-with-icon',
				'options'            => array(
					'dashed-with-icon'  => __( 'Dashed With Icon', 'cmb2' ),
					'with-image'        => __( 'With Image', 'cmb2' ),
					'solid-with-icon'   => __( 'Solid With Icon', 'cmb2' ),
				),
			) );
	
			$flip->add_field( array(
				'name'             => __( 'Flipbox Effect', 'cmb2' ),
				'desc'             => __( 'Select Flipbox Effect', 'cmb2' ),
				'id'               => $prefix . 'effect',
				'type'             => 'select',
				'show_option_none' => false,
				'default'          => 'left-to-right',
				'options'          => array(
				   'x' => __( 'Bottom To Top', 'cmb2' ),
					'y' => __( 'Left To Right', 'cmb2' ),
				),
			) );
			
			$flip->add_field( array(
				'name'             =>  __( 'Number of columns', 'cmb2' ),
				'desc'             =>  __( 'Select Number of columns', 'cmb2' ),
				'id'               => $prefix . 'column',
				'type'             => 'select',
				'show_option_none' => false,
				'default'          => 'col-md-4',
				'options'          => array(
					'col-md-12'=> __( 'One','cmb2' ),
					'col-md-6' => __( 'Two', 'cmb2' ),
					'col-md-4' => __( 'Three', 'cmb2' ),
					'col-md-3' => __( 'Four', 'cmb2' ),
				),
			) );
			
			$flip->add_field( array(
				'name'       => __( 'Icon Size(in px)', 'cmb2' ),
				'desc'       => __( 'Enter icon size', 'cmb2' ),
				'id'         => $prefix .'icon_size',
				'type'       => 'text',
				'default'    => '52px',
			) );
			
			$flip->add_field( array(
				'name'        => __( 'Skin Color', 'cmb2' ),
				'description' => __( 'Choose a skin color', 'cmb2' ),
				'id'          => $prefix .'skin_color',
				'type'        => 'colorpicker',
				'default'     => '#f4bf64',
			) );
			
			$flip->add_field( array(
				'name'       => __( 'Number of Flipboxes', 'cmb2' ),
				'desc'       => __( 'Enter number of flipboxes to show', 'cmb2' ),
				'id'         => $prefix .'no_of_items',
				'type'       => 'text',
			) );

			$flip->add_field( array(
				'name'    => __( 'Bootstrap', 'cmb2' ),
				'id'      => $prefix . 'bootstrap',
				'default' => 'enable',
				'type'    => 'radio',
				'options' => array(
					'enable' => __( 'Enable Bootstrap', 'cmb2' ),
					'disable' =>__( 'Disable Bootstrap', 'cmb2' ),
				),
			) );
			
			$flip->add_field( array(
				'name'    =>__( 'Fontawesome', 'cmb2' ),
				'id'      => $prefix . 'font',
				'default' => 'enable',
				'type'    => 'radio',
				'options' => array(
					'enable' =>__( 'Enable Fontawesome', 'cmb2' ),
					'disable' =>__( 'Disable Fontawesome', 'cmb2' ),
				),
			) );
		}
		
		function cfb_shortcode_metabox()
		{
			add_meta_box( 'my-meta-box-id', 'Use This Shortcode',array($this,'cfb_shortcode_text'), 'flipboxes', 'side', 'high' );
		}
		
		function cfb_shortcode_text()
		{
			$id = get_the_ID();
			$dynamic_attr='';
			_e("Paste this shortcode in anywhere (page/post)", "c-flipbox");
			$dynamic_attr.="[flipboxes id=\"{$id}\"";
			$dynamic_attr.=']';
			$prefix="_cfb_";
			?>
		<input type="text" class="regular-small" name="my_meta_box_text" id="my_meta_box_text" value="<?php  echo htmlentities ($dynamic_attr);?>" readonly/>
			<?php
		}
		// ADD NEW COLUMN
		function cfb_add_custom_columns($flip_cols) 
		{
			$new_columns['cb']            =  '<input type="checkbox" />';
			$new_columns['title']         =  _x('Title', 'column name');
			$new_columns['flip_layout']   =  _x('Flipbox Layout', 'flipboxes');
			$new_columns['effect']        =  __('Flipbox Effect','flipboxes');
			$new_columns['code']          =  __('Shortcode','flipboxes');
			$new_columns['date']          =  _x('Sort By Date', 'column name');
			return $new_columns;
		}
	
		function cfb_columns_content($flip_cols,$post) 
		{                   
			$prefix="_cfb_";
			global $layouts;
			$layouts=array(
				'dashed-with-icon'     => __( 'Dashed With Icons', 'cmb2' ),
				'with-image'           => __( 'With Image', 'cmb2' ),
				'solid-with-icon'      => __( 'Solid With Icon', 'cmb2' ),
				);
			global $effects;
			$effects=array(
					'x'   => __( 'Bottom To Top', 'cmb2' ),
					'y'   => __( 'Left To Right', 'cmb2' ),
				);
				
			switch ( $flip_cols ) 
				{
					case "flip_layout":
					$lt = get_post_meta( $post, $prefix . 'flip_layout', true );
					if(isset($layouts[$lt]))
					{
						echo $layouts[$lt];
					}
					break;
					case "effect":
					$eff= get_post_meta( $post, $prefix . 'effect', true );
					if(isset($effects[$eff]))
					{
						echo $effects[$eff];
					}
					break;
					case "code":
					global $dynamic_attr;
					global $id;
					$dynamic_attr="[flipboxes id=\"{$id}\"]";
					echo "<input type='text' value='".$dynamic_attr."'>";
					break;
				}
		}
		function cfb_register_post_type()
		{
			$labels = array(
				'name'                  => _x( 'Flipboxes', 'Post Type General Name'),
				'singular_name'         => _x( 'Flipboxes', 'Post Type Singular Name'),
				'menu_name'             => __( 'Flipboxes'),
				'name_admin_bar'        => __( 'Flipboxes'),
				'archives'              => __( 'Item Archives'),
				'attributes'            => __( 'Item Attributes'),
				'parent_item_colon'     => __( 'Parent Item:'),
				'all_items'             => __( 'All Flipboxes'),
				'add_new_item'          => __( 'Add New Flipbox'),
				'add_new'               => __( 'Add New'),
				'new_item'              => __( 'New Item'),
				'edit_item'             => __( 'Edit Item'),
				'update_item'           => __( 'Update Item'),
				'view_item'             => __( 'View Item'),
				'view_items'            => __( 'View Items'),
				'search_items'          => __( 'Search Item'),
				'not_found'             => __( 'Not found'),
				'not_found_in_trash'    => __( 'Not found in Trash'),
				'featured_image'        => __( 'Featured Image'),
				'set_featured_image'    => __( 'Set featured image'),
				'remove_featured_image' => __( 'Remove featured image'),
				'use_featured_image'    => __( 'Use as featured image'),
				'insert_into_item'      => __( 'Insert into item'),
				'uploaded_to_this_item' => __( 'Uploaded to this item'),
				'items_list'            => __( 'Items list'),
				'items_list_navigation' => __( 'Items list navigation'),
				'filter_items_list'     => __( 'Filter items list'),
				);
			$args = array(
				'label'                 => __( 'Flipboxes'),
				'description'           => __( 'Post Type Description'),
				'labels'                => $labels,
				'supports'              => array('title'),
				'taxonomies'            => array(),
				'hierarchical'          => false,
				'public'                => true,
				'show_ui'               => true,
				'show_in_menu'          => true,
				'menu_position'         => 5,
				'show_in_admin_bar'     => true,
				'show_in_nav_menus'     => true,
				'can_export'            => true,
				'has_archive'           => true,        
				'exclude_from_search'   => false,
				'publicly_queryable'    => true,
				'capability_type'       => 'page',
				);
			register_post_type('flipboxes',$args);
		}

		
		/*Define the metabox and field configurations*/
		function cfb_metaboxes()
        {
            // Start with an underscore to hide fields from custom fields list
            $prefix = '_cfb_';
            
            /* Initiate the metabox*/
            $flip = new_cmb2_box( array(
                'id'            => 'test_metabox',
                'title'         => __( 'Flipboxes', 'cmb2' ),
                'object_types'  => array( 'flipboxes'), // Post type
                'context'       => 'normal',
                'priority'      => 'high',
                'show_names'    => true, // Show field names on the left
            ) );

        
            $group_field_id = $flip->add_field( array(
                'id'          =>$prefix.'flip_repeat_group',
                'type'        => 'group',
                'description' => __( 'Add Flipboxes', 'cmb2' ),
                'options'     => array(
                    'group_title'   => __( 'Flipbox {#}', 'cmb2' ), // since version 1.1.4, {#} gets replaced by row number
                    'add_button'    => __( 'Add Another Flipbox', 'cmb2' ),
                    'remove_button' => __( 'Remove Flipbox', 'cmb2' ),
                    'sortable'      => true, // beta
                ),
            ) );

            // Id's for group's fields only need to be unique for the group. Prefix is not needed.
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Title','c-flipbox'),
                'description' => __('Enter a title for this Flipbox','c-flipbox'),
                'id'   => 'flipbox_title',
                'type' => 'text',
				'default' =>'Enter a Title',
            ) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Extra Text','c-flipbox'),
                'id'   => 'flipbox_label',
                'description' => __('Add Extra Text for this Flipbox','c-flipbox'),
                'type' => 'text',
				'default' =>'Extra Text',
            ) );
            
            $flip->add_group_field( $group_field_id, array(
                'name'        => __('Back Description','c-flipbox'),
                'description' => __('Recomended : One Line Description','c-flipbox'),
                'id'          => 'flipbox_desc',
                'type'        => 'textarea_small',
				'default'     => 'Lorem Ipsum is simply dummy text of the printing and typesetting industry.',    
            ) );
			
			$flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Description Length', 'c-flipbox' ),
                'description' => __('Enter number of characters','c-flipbox'),
                'id'          => 'flipbox_desc_length',
                'type'        => 'text',
				'default'     => '75',
            ) );
            
			$flip->add_group_field( $group_field_id, array(
                'name' => __('Extra Text','c-flipbox'),
                'id'   => 'flipbox_label',
                'description' => __('Add Extra Text for this Flipbox','c-flipbox'),
                'type' => 'text',
				'default' =>'Extra Text',
            ) );
			$flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Select Icon', 'c-flipbox' ),
                'description' => __('Choose an Icon for "dashed with icon" or "solid with icon" Flipbox Layout','c-flipbox'),
                'id'          => 'flipbox_icon',
                'type'        => 'fontawesome_icon',
            ) );
            $flip->add_group_field( $group_field_id, array(
                'name'        => __( 'Color Scheme', 'c-flipbox' ),
                'description' => __('Choose Color Scheme','c-flipbox'),
                'id'          => 'color_scheme',
                'type'        => 'colorpicker',
				//'default'     => '',
            ) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('Image','c-flipbox'),
                'id'   => 'flipbox_image',
                'description' => __('Upload an Image for "with-image" Flipbox Layout','c-flipbox'),
                'type' => 'file',
            ) );
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('URL','c-flipbox'),
                'id'   => 'flipbox_url',
                'description' => __('Enter URL for this Flipbox','c-flipbox'),
                'type' => 'text_url',
                'protocols' => array('http', 'https'),
            ) );
            
            
            $flip->add_group_field( $group_field_id, array(
                'name' => __('URL Text','c-flipbox'),
                'id'   => 'read_more_link',
                'description' => __('Enter Text For Link','c-flipbox'),
                'type' => 'text',
				'default'=>'Read More',
            ) );
        }
		function cfb_register_frontend_assets() 
		{
			wp_register_script( 'flipbox-js', CFB_JS_DIR . '/flipbox.js', array('jquery'), CFB_VERSION );
			
			wp_register_style( 'cfb-fontawesome',CFB_URL . 'assets/css/font-awesome.min.css', array(), CFB_VERSION);

			wp_register_style( 'flipstyle',CFB_CSS_URL . '/flipboxes.css', array(), CFB_VERSION);
			wp_register_style( 'cfb-bootstrap',CFB_CSS_URL . '/bootstrap-3.3.7/dist/css/bootstrap.min.css', array(), CFB_VERSION ,'all');
			
			wp_register_script( 'flip-min-js', CFB_JS_DIR . '/jquery.flip.min.js', array('jquery'), CFB_VERSION );
		    
			global $post; 
			if(isset($post->post_content) && has_shortcode( $post->post_content, 'flipboxes'))
				{
					//Enqueue common required assets
					 wp_enqueue_style( 'flipstyle');
					 wp_enqueue_script( 'flip-min-js');
					wp_enqueue_script( 'flipbox-js');
				
				}
		}
		
		function cfb_shortcode($atts)
		{
			extract( shortcode_atts( array (
			'id' => '',
			), $atts ));
			$prefix   = "_cfb_";
			$title          = get_the_title($id);
			$flip_layout    = get_post_meta( $id, $prefix . 'flip_layout', true );
			$effect         = get_post_meta( $id, $prefix . 'effect', true );

			$iconsize      = get_post_meta( $id, $prefix .'icon_size', true );
			$icon_size=isset($iconsize )&&!empty($iconsize)?$iconsize:"52px";
			$skincolor= get_post_meta( $id, $prefix .'skin_color', true );
			$skincolor=isset($skincolor)&& !empty($skincolor)?$skincolor:"#f4bf64";

			$cols= get_post_meta( $id, $prefix . 'column', true );
			$bootstrap = get_post_meta( $id, $prefix . 'bootstrap', true );
			$fontawesome= get_post_meta( $id, $prefix . 'font', true );
			$noitems = get_post_meta( $id, $prefix . 'no_of_items', true );
			$entries = get_post_meta( $id, $prefix .'flip_repeat_group', true );

				if ($bootstrap=='enable')
					{
						wp_enqueue_style( 'cfb-bootstrap');
					}
				if ($fontawesome=='enable')
					{
						wp_enqueue_style( 'cfb-fontawesome');
					}
				if($noitems!=false){
					$no_of_items=$noitems;
				}else{
					$no_of_items =999;
				}

				ob_start();
				$i=0;
				if($entries)
				{	
					foreach ( (array) $entries as $key => $entry ) 
					{
						$i++;
						if($i>$no_of_items){
						break;
						}
						
						$flipbox_title         =isset($entry['flipbox_title'])?$entry ['flipbox_title']:'Enter a Title';
						$flipbox_desc          =isset($entry['flipbox_desc'])?$entry['flipbox_desc']:'Lorem Ipsum is simply dummy text of the printing and typesetting industry.';
						$flipbox_desc_length   =isset($entry['flipbox_desc_length'])?$entry ['flipbox_desc_length']:'75';
						$flipbox_truncate      =mb_strimwidth($flipbox_desc ,0,$flipbox_desc_length,"...");
						$single_f_c  		   =isset($entry['color_scheme'])?$entry['color_scheme']:"";
						$flipbox_icon          =isset($entry['flipbox_icon'])?$entry['flipbox_icon']:'fa-clock-o';
						$flipbox_image         =isset($entry['flipbox_image'])?$entry['flipbox_image']:'';
				        $flipbox_url           =isset($entry['flipbox_url'])?$entry['flipbox_url']:'';
						$flipbox_label         =isset($entry['flipbox_label'])?$entry['flipbox_label']:'Extra Text';
						$read_more_link        =isset($entry['read_more_link'])?$entry['read_more_link']:'Read More';
						
						require CFB_DIR_PATH .'/flipboxes-view.php';
					}
				}
               			
				$out = ob_get_clean();
				return $out;
		}
		
	}// end class
}
// Installation and uninstallation hooks
    register_activation_hook(__FILE__, array('CflipBoxes', 'activate'));
    register_deactivation_hook(__FILE__, array('CflipBoxes', 'deactivate'));
	
$CflipBoxes_obj = new CflipBoxes(); //initialization of plugin
	