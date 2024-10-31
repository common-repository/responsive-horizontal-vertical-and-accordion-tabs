<?php
/*
 * Plugin Name: Responsive horizontal vertical and accordion Tabs
 * Plugin URI:https://www.i13websolution.com/best-wordpress-responsive-tabs-plugin.html
 * Plugin URI:https://www.i13websolution.com
 * Description:This is beautiful responsive all in one tabs for wordpress sites/blogs. Add any number of tabs sets to your site. your tabs sets will be ready within few min. 
 * Author:I Thirteen Web Solution 
 * Version:1.0
 * Text Domain:wp-best-responsive-tabs
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) exit; 

add_filter ( 'widget_text', 'do_shortcode' );
add_action ( 'admin_menu', 'wrt_responsive_tabs_add_admin_menu' );

register_activation_hook ( __FILE__, 'wrt_wp_responsive_tabs_install' );
add_action ( 'wp_enqueue_scripts', 'wrt_wp_responsive_tabs_load_styles_and_js' );
add_shortcode ( 'wrt_print_rt_wp_responsive_tabs', 'wrt_print_rt_wp_responsive_tabs_func' );
add_action('plugins_loaded', 'wrt_lang_for_wp_responsive_tabs');
add_action('wp_ajax_rt_get_tab_data_byid', 'wp_ajax_rt_get_tab_data_byid_callback');
add_action('wp_ajax_nopriv_rt_get_tab_data_byid', 'wp_ajax_rt_get_tab_data_byid_callback');

function wrt_lang_for_wp_responsive_tabs() {
            
            load_plugin_textdomain( 'wp-best-responsive-tabs', false, basename( dirname( __FILE__ ) ) . '/languages/' );
    }


function wrt_responsive_tabs_add_admin_init() {
    
        
	$url = plugin_dir_url ( __FILE__ );
	
	wp_enqueue_style( 'admincss', plugins_url('/css/admincss.css', __FILE__) );
	wp_enqueue_style( 'wrt_bootstrap-nv-only.min', plugins_url('/css/wrt_bootstrap-nv-only.min.css', __FILE__) );
	wp_enqueue_style( 'wrt_easy-responsive-tabs', plugins_url('/css/wrt_easy-responsive-tabs.css', __FILE__) );
        wp_enqueue_script('jquery');         
        wp_enqueue_script("jquery-ui-core");
        wp_enqueue_script('wrt_bootstrap-nva-only.min',plugins_url('/js/wrt_bootstrap-nva-only.min.js', __FILE__));
        wp_enqueue_script('wrt_jquery.easyResponsiveTabs',plugins_url('/js/wrt_jquery.easyResponsiveTabs.js', __FILE__));
        wp_enqueue_script('wrt_jquery.validate',plugins_url('/js/wrt_jquery.validate.js', __FILE__));
        
       
	wrt_wp_responsive_full_tabs_admin_scripts_init();
}

function wrt_wp_responsive_tabs_load_styles_and_js() {
    if (! is_admin ()) {

            wp_enqueue_style ( 'wrt_bootstrap-nv-only.min', plugins_url ( '/css/wrt_bootstrap-nv-only.min.css', __FILE__ ) );
            wp_enqueue_style ( 'wrt_easy-responsive-tabs', plugins_url ( '/css/wrt_easy-responsive-tabs.css', __FILE__ ) );
            wp_enqueue_script ( 'jquery' );
            wp_enqueue_script ( 'wrt_bootstrap-nva-only.min', plugins_url ( '/js/wrt_bootstrap-nva-only.min.js', __FILE__ ) );
            wp_enqueue_script ( 'wrt_jquery.easyResponsiveTabs', plugins_url ( '/js/wrt_jquery.easyResponsiveTabs.js', __FILE__ ) );
            

       }
}
function wrt_wp_responsive_tabs_install() {
    
	global $wpdb;
	$table_name = $wpdb->prefix . "wrt_tabs";
	$table_name2 = $wpdb->prefix . "wrt_tabs_settings";
	
        $charset_collate = $wpdb->get_charset_collate();
        
	$sql = "CREATE TABLE " . $table_name . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `tab_title` varchar(200) NOT NULL,
        `tab_description` text  DEFAULT NULL ,
        `createdon` datetime NOT NULL, 
        `is_default` tinyint(1) NOT NULL DEFAULT '0',
        `morder` int(11) NOT NULL DEFAULT '0',
        `gtab_id` int(11) NOT NULL DEFAULT '1',
         PRIMARY KEY (`id`)
        ) $charset_collate; ". 
        "CREATE TABLE " . $table_name2 . " (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `name` varchar(200) NOT NULL,
        `type` int(1) NOT NULL DEFAULT '1',
        `activetab_bg` varchar(10)  DEFAULT '#ffffff' ,
        `inactive_bg` varchar(10)  DEFAULT '#00aadd' ,
        `ac_border_color` varchar(10)  DEFAULT '#81d742' ,
        `tab_fcolor` varchar(10) DEFAULT '#ffffff' ,
        `tab_a_fcolor` varchar(10) DEFAULT '#428bca' ,
        `tab_ccolor` varchar(10) DEFAULT '#000000' ,
        `createdon` datetime NOT NULL, 
         PRIMARY KEY (`id`)
        ) $charset_collate";

        
	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	dbDelta ( $sql );

        
        
}
function wrt_responsive_tabs_add_admin_menu() {
    
	$hook_suffix = add_menu_page ( __( 'Responsive Tabs','wp-best-responsive-tabs') , __ ( 'Responsive Tabs','wp-best-responsive-tabs' ), 'administrator', 'rt_wp_responsive_tabs', 'rt_wp_responsive_wp_admin_options_func' );
	$hook_suffix=add_submenu_page ( 'rt_wp_responsive_tabs', __ ( 'Tab Sets','wp-best-responsive-tabs' ), __ ( 'Tab Sets','wp-best-responsive-tabs' ), 'administrator', 'rt_wp_responsive_tabs', 'rt_wp_responsive_wp_admin_options_func' );
	$hook_suffix_image=add_submenu_page ( 'rt_wp_responsive_tabs', __ ( 'Manage Tabs','wp-best-responsive-tabs' ), __ ( 'Manage Tabs','wp-best-responsive-tabs' ), 'administrator', 'rt_wp_responsive_tabs_management', 'rt_wp_responsive_tabs_data_management' );
	$hook_suffix_prev=add_submenu_page ( 'rt_wp_responsive_tabs', __ ( 'Preview Slider','wp-best-responsive-tabs' ), __ ( 'Preview Tabs','wp-best-responsive-tabs' ), 'administrator', 'rt_wp_responsive_tabs_preview', 'wrt_rt_wp_responsive_tabs_preview_func' );
	
	add_action( 'load-' . $hook_suffix , 'wrt_responsive_tabs_add_admin_init' );
	add_action( 'load-' . $hook_suffix_image , 'wrt_responsive_tabs_add_admin_init' );
	add_action( 'load-' . $hook_suffix_prev , 'wrt_responsive_tabs_add_admin_init' );
        
        wrt_wp_responsive_full_tabs_admin_scripts_init();
	
}

function wp_ajax_rt_get_tab_data_byid_callback(){
    
       global $wpdb;
       $retrieved_nonce='';
        if (isset($_POST['vNonce']) and $_POST['vNonce'] != '') {

           $retrieved_nonce = $_POST['vNonce'];
        }
        if (!wp_verify_nonce($retrieved_nonce, 'vNonce')) {


           wp_die('Security check fail');
        }
        
       $tab_id = 0;
	if (isset ( $_POST ['tab_id'] ) and $_POST ['tab_id'] > 0) {
            
		$tab_id = intval((trim ( $_POST ['tab_id'] )));
	} 
        
        $query = "SELECT tab_description FROM " . $wpdb->prefix . "wrt_tabs WHERE id=$tab_id";
   	$row = $wpdb->get_row ( $query, ARRAY_A );
        $description='';
        if(is_array($row) and sizeof($row)>0){
            
            $description=$row['tab_description'];
        }
        
      echo do_shortcode(wp_unslash($description));
      exit;
 
}

function rt_wp_responsive_wp_admin_options_func() {
    
        $url='admin.php?page=rt_wp_responsive_tabs';
        $order_by='id';
        $order_pos="asc";
        
        if(isset($_GET['order_by'])){
        
               if(sanitize_sql_orderby($_GET['order_by'])){
                   
                    $order_by=trim($_GET['order_by']); 
                }
                else{

                    $order_by=' id ';
                }
        }
        
        if(isset($_GET['order_pos'])){
        
           $order_pos=trim(sanitize_text_field($_GET['order_pos'])); 
        }
        
        $search_term_='';
        if(isset($_GET['search_term'])){
        
           $search_term_='&search_term='.urlencode(sanitize_text_field($_GET['search_term']));
        }
        
        
        
	$action = 'gridview';
	if (isset ( $_GET ['action'] ) and $_GET ['action'] != '') {
		
		$action = trim (sanitize_text_field($_GET ['action'] ));
	}
	if (strtolower ( $action ) == strtolower ( 'gridview' )) {?>
            <div class="wrap">
                    <style type="text/css">
                    .pagination {
                            clear: both;
                            padding: 20px 0;
                            position: relative;
                            font-size: 11px;
                            line-height: 13px;
                    }

                    .pagination span, .pagination a {
                            display: block;
                            float: left;
                            margin: 2px 2px 2px 0;
                            padding: 6px 9px 5px 9px;
                            text-decoration: none;
                            width: auto;
                            color: #fff;
                            background: #555;
                    }

                    .pagination a:hover {
                            color: #fff;
                            background: #3279BB;
                    }

                    .pagination .current {
                            padding: 6px 9px 5px 9px;
                            background: #3279BB;
                            color: #fff;
                    }
            </style>
                    <!--[if !IE]><!-->
                    <style type="text/css">
                        @media only screen and (max-width: 800px) {
                                /* Force table to not be like tables anymore */
                                #no-more-tables table, #no-more-tables thead, #no-more-tables tbody,
                                        #no-more-tables th, #no-more-tables td, #no-more-tables tr {
                                        display: block;
                                }

                                /* Hide table headers (but not display: none;, for accessibility) */
                                #no-more-tables thead tr {
                                        position: absolute;
                                        top: -9999px;
                                        left: -9999px;
                                }
                                #no-more-tables tr {
                                        border: 1px solid #ccc;
                                }
                                #no-more-tables td {
                                        /* Behave  like a "row" */
                                        border: none;
                                        border-bottom: 1px solid #eee;
                                        position: relative;
                                        padding-left: 50%;
                                        white-space: normal;
                                        text-align: left;
                                }
                                #no-more-tables td:before {
                                        /* Now like a table header */
                                        position: absolute;
                                        /* Top/left values mimic padding */
                                        top: 6px;
                                        left: 6px;
                                        width: 45%;
                                        padding-right: 10px;
                                        white-space: nowrap;
                                        text-align: left;
                                        font-weight: bold;
                                }

                                /*
                                            Label the data
                                            */
                                #no-more-tables td:before {
                                        content: attr(data-title);
                                }
                        }
            </style>
            <!--<![endif]-->
            <?php
            $url = plugin_dir_url(__FILE__);  
             ?> 
        <div id="poststuff" > 
                <div id="post-body" class="metabox-holder columns-2" >  
                  
                       <div id="post-body-content">
                           <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                          <h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/best-wordpress-responsive-tabs-plugin.html"><?php echo __('UPGRADE TO PRO VERSION','wp-best-responsive-tabs');?></a></h3>
   
                         <?php
                            $messages = get_option ( 'wrt_responsive_tabs_msg' );
                            $type = '';
                            $message = '';
                            if (isset ( $messages ['type'] ) and $messages ['type'] != "") {

                                    $type = $messages ['type'];
                                    $message = $messages ['message'];
                            }

                            if(trim($type)=='err'){ echo "<div class='notice notice-error is-dismissible'><p>"; echo $message; echo "</p></div>";}
                            else if(trim($type)=='succ'){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $message; echo "</p></div>";}
       

                            update_option ( 'wrt_responsive_tabs_msg', array () );
                            ?>    
                                <div class="icon32 icon32-posts-post" id="icon-edit">
                                            <br>
                                    </div>
                                    <h2>
                                            <?php echo __('Manage Tab Sets','wp-best-responsive-tabs');?> <a class="button add-new-h2"href="admin.php?page=rt_wp_responsive_tabs&action=addedit"><?php echo __('Add New','wp-best-responsive-tabs');?></a>
                                    </h2>
                                    <br />
                                    
                                    <form method="POST" action="admin.php?page=rt_wp_responsive_tabs&action=deleteselected" id="posts-filter" onkeypress="return event.keyCode != 13;">
                                            <div class="alignleft actions">
                                                    <select name="action_upper" id="action_upper">
                                                            <option selected="selected" value="-1"><?php echo __("Bulk Actions",'wp-best-responsive-tabs');?></option>
                                                            <option value="delete"><?php echo __('delete','wp-best-responsive-tabs');?></option>
                                                    </select> 
                                                    <input type="submit" value="<?php echo __('Apply','wp-best-responsive-tabs');?>" class="button-secondary action" id="deleteselected" name="deleteselected" onclick="return confirmDelete_bulk();">
                                            </div>
                                            <div style="clear: both;"></div>
                                            <br />
                                            <?php
                                                $setacrionpage='admin.php?page=rt_wp_responsive_tabs';

                                                if(isset($_GET['order_by']) and $_GET['order_by']!=""){
                                                 $setacrionpage.='&order_by='.sanitize_text_field($_GET['order_by']);   
                                                }

                                                if(isset($_GET['order_pos']) and $_GET['order_pos']!=""){
                                                 $setacrionpage.='&order_pos='.sanitize_text_field($_GET['order_pos']);   
                                                }

                                                $seval="";
                                                if(isset($_GET['search_term']) and $_GET['search_term']!=""){
                                                 $seval=trim(sanitize_text_field($_GET['search_term']));   
                                                }

                                            ?>
                                            <div style="padding-top:5px;padding-bottom:5px">
                                                <b><?php echo __( 'Search','wp-best-responsive-tabs');?> : </b>
                                                  <input type="text" value="<?php echo $seval;?>" id="search_term" name="search_term">&nbsp;
                                                  <input type='button'  value='<?php echo __( 'Search','wp-best-responsive-tabs');?>' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;
                                                  <input type='button'  value='<?php echo __( 'Reset Search','wp-best-responsive-tabs');?>' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" >
                                            </div>  
                                            <script type="text/javascript" >
                                               var $n = jQuery.noConflict();   
                                                $n('#search_term').on("keyup", function(e) {
                                                       if (e.which == 13) {
                                                  
                                                           SearchredirectTO();
                                                       }
                                                  });   
                                             function SearchredirectTO(){
                                               var redirectto='<?php echo $setacrionpage; ?>';
                                               var searchval=jQuery('#search_term').val();
                                               redirectto=redirectto+'&search_term='+jQuery.trim(encodeURIComponent(searchval));  
                                               window.location.href=redirectto;
                                             }
                                            function ResetSearch(){

                                                 var redirectto='<?php echo $setacrionpage; ?>';
                                                 window.location.href=redirectto;
                                                 exit;
                                            }
                                            </script>
                                            <div id="no-more-tables">
                                          <table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf wp-list-table widefat">
                                           <thead>
  

                                                <tr>
                                                        <th class="manage-column column-cb check-column"><input
                                                                type="checkbox" /></th>
                                                        <?php if($order_by=="id" and $order_pos=="asc"):?>

                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=id&order_pos=desc<?php echo $search_term_;?>"><?php echo __("Id",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                        <?php else:?>
                                                            <?php if($order_by=="id"):?>
                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Id",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                            <?php else:?>
                                                                <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Id",'wp-best-responsive-tabs');?></a></th>
                                                            <?php endif;?>    
                                                        <?php endif;?>   

                                                        <?php if($order_by=="name" and $order_pos=="asc"):?>

                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=name&order_pos=desc<?php echo $search_term_;?>"><?php echo __("Name",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                        <?php else:?>
                                                            <?php if($order_by=="name"):?>
                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=name&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Name",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                            <?php else:?>
                                                                <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=name&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Name",'wp-best-responsive-tabs');?></a></th>
                                                            <?php endif;?>    
                                                        <?php endif;?>   

                                                        <?php if($order_by=="createdon" and $order_pos=="asc"):?>

                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=createdon&order_pos=desc<?php echo $search_term_;?>"><?php echo __("Created On",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                        <?php else:?>
                                                            <?php if($order_by=="createdon"):?>
                                                        <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Created On",'wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                            <?php else:?>
                                                                <th><a href="admin.php?page=rt_wp_responsive_tabs&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __("Created On",'wp-best-responsive-tabs');?></a></th>
                                                            <?php endif;?>    
                                                        <?php endif;?>   

                                                        <th><?php echo __("Shortcode",'wp-best-responsive-tabs');?></th>
                                                        <th><?php echo __("Manage Tabs",'wp-best-responsive-tabs');?></th>
                                                        <th><?php echo __("Edit",'wp-best-responsive-tabs');?></th>
                                                        <th><?php echo __("Delete",'wp-best-responsive-tabs');?></th>
                                                </tr>
                                        </thead>

                                        <tbody id="the-list">
                            <?php
                            
                            global $wpdb;
                            $search_term='';
                            if(isset($_GET['search_term'])){
                                
                               $search_term= sanitize_text_field($_GET['search_term']);
                            }
                            
                            $query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs_settings ";
                            if($search_term!=''){
                               $query.=" where id like '%$search_term%' or name like '%$search_term%' "; 
                            }
                            
                            $order_by=sanitize_text_field($order_by);
                            $order_pos=sanitize_text_field($order_pos);
                            
                            $query.=" order by $order_by $order_pos";
                            $rows = $wpdb->get_results ( $query );
                            $rowCount = sizeof ( $rows );
                            
                            if ($rowCount > 0) {

                                    global $wp_rewrite;
                                    $rows_per_page = 15;

                                    $current = (isset ( $_GET ['paged'] )) ? ($_GET ['paged']) : 1;
                                    $pagination_args = array (
                                                    'base' => @add_query_arg ( 'paged', '%#%' ),
                                                    'format' => '',
                                                    'total' => ceil ( sizeof ( $rows ) / $rows_per_page ),
                                                    'current' => $current,
                                                    'show_all' => false,
                                                    'type' => 'plain' 
                                    );

                                    $start = ($current - 1) * $rows_per_page;
                                    $end = $start + $rows_per_page;
                                    $end = (sizeof ( $rows ) < $end) ? sizeof ( $rows ) : $end;
                                    $delRecNonce = wp_create_nonce('delete_tabset');
                                    for($i = $start; $i < $end; ++ $i) {
                                            $row = $rows [$i];
                                            $id = $row->id;
                                            $editlink = "admin.php?page=rt_wp_responsive_tabs&action=addedit&id=$id";
                                            $deletelink = "admin.php?page=rt_wp_responsive_tabs&action=delete&id=$id&nonce=$delRecNonce";
                                            $manageMedia = "admin.php?page=rt_wp_responsive_tabs_management&tabid=$id";
                                            ?>
                                                        <tr valign="top" id="">
                                                                            <td class="alignCenter check-column" data-title="<?php echo __("Select Record",'wp-best-responsive-tabs');?>"><input type="checkbox" value="<?php echo $id ?>" name="thumbnails[]"></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Id",'wp-best-responsive-tabs');?>"><?php echo intval($row->id); ?></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Name",'wp-best-responsive-tabs');?>"><strong><?php echo esc_html($row->name); ?></strong></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Created On",'wp-best-responsive-tabs');?>"><?php echo esc_html($row->createdon); ?></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("ShortCode",'wp-best-responsive-tabs');?>" scope="col"><span><input type="text" spellcheck="false" onclick="this.focus(); this.select()" readonly="readonly" style="width: 100%; height: 29px; background-color: #EEEEEE" value='[wrt_print_rt_wp_responsive_tabs tabset_id="<?php echo intval($row->id); ?>"]'></span></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Manage Tabs",'wp-best-responsive-tabs');?>" scope="col"><strong><a href='<?php echo $manageMedia; ?>' title="<?php echo __("Manage Tabs",'wp-best-responsive-tabs');?>"><?php echo __("Manage Tabs",'wp-best-responsive-tabs');?></a></strong></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Edit",'wp-best-responsive-tabs');?>"><strong><a href='<?php echo esc_html($editlink); ?>' title="<?php echo __("Edit",'wp-best-responsive-tabs');?>"><?php echo __("Edit",'wp-best-responsive-tabs');?></a></strong></td>
                                                                            <td class="alignCenter" data-title="<?php echo __("Delete",'wp-best-responsive-tabs');?>"><strong><a  href='<?php echo esc_html($deletelink); ?>' onclick="return confirmDelete();" title="<?php echo __("Delete",'wp-best-responsive-tabs');?>"><?php echo __("Delete",'wp-best-responsive-tabs');?></a> </strong></td>
                                                                    </tr>
                                                        <?php
                                    }
                            } else {
                                    ?><tr valign="top" id="">
                                            <td colspan="9" data-title="<?php echo __("No Records",'wp-best-responsive-tabs');?>" align="center"><strong><?php echo __("No Tab Sets Found",'wp-best-responsive-tabs');?></strong></td>
                                    </tr>
                        <?php
                            }
                            ?>      
                                </tbody>
                                </table>
                                                
                                
                                </div>
                           <?php
                            if (sizeof ( $rows ) > 0) {
                                    echo "<div class='pagination' style='padding-top:10px'>";
                                    echo paginate_links ( $pagination_args );
                                    echo "</div>";
                            }
                            ?>
                                    <br />
                                            <div class="alignleft actions">
                                                    <select name="action" id="action_bottom"> 
                                                            <option selected="selected" value="-1"><?php echo __("Bulk Actions",'wp-best-responsive-tabs');?></option>
                                                            <option value="delete"><?php echo __("delete",'wp-best-responsive-tabs');?></option>
                                                    </select> 
                                                <?php wp_nonce_field('action_settings_mass_delete', 'mass_delete_nonce'); ?>
                                                <input type="submit" value="<?php echo __("Apply",'wp-best-responsive-tabs');?>" class="button-secondary action" id="deleteselected" name="deleteselected" onclick="return confirmDelete_bulk();">
                                            </div>

                                    </form>
                                    <script type="text/JavaScript">

                                     function  confirmDelete_bulk(){
                                                
                                        var topval=document.getElementById("action_bottom").value;
                                        var bottomVal=document.getElementById("action_upper").value;

                                        if(topval=='delete' || bottomVal=='delete'){


                                            var agree=confirm('<?php echo __("Are you sure you want to delete selected tabs Sets ? All tabs related to this tab Sets also removed.",'wp-best-responsive-tabs');?>');
                                            if (agree)
                                             return true ;
                                            else
                                             return false;
                                          }
                                     }
                                
                                    function  confirmDelete(){
                                    var agree=confirm("<?php echo __("Are you sure you want to delete this tab sets ? All tabs related to this sets also removed.",'wp-best-responsive-tabs');?>");
                                    if (agree)
                                        return true ;
                                    else
                                        return false;
                                    }
                                </script>

                                    <br class="clear">
                            </div>
                                <div id="postbox-container-1" class="postbox-container" > 

                                    <div class="postbox"> 
                                        <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','wp-best-responsive-tabs');?></h3> 
                                        <div class="inside">
                                            <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                                    <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__);?>" width="250" height="250">
                                                </a></center>

                                            <div style="margin:10px 5px">

                                            </div>
                                        </div></div>
                                    <div class="postbox"> 
                                        <h3 class="hndle"><span></span><?php echo __('Recommended WordPress Plugin','wp-best-responsive-tabs');?></h3> 
                                        <div class="inside">
                                            <center><a href="http://shareasale.com/r.cfm?b=838943&amp;u=675922&amp;m=64312&amp;urllink=&amp;afftrack=" target="_blank">
                                                    <img src="<?php echo plugins_url( 'images/wpforms-336x280v1.png', __FILE__ );?>" width="250" height="250" border="0">
                                                </a></center>
                                            <div style="margin:10px 5px">
                                            </div>
                                        </div></div>

                                </div>  
                            
                            <?php $url = plugin_dir_url(__FILE__); ?>
                        </div>
            </div>
	<div class="clear"></div> 
            <?php
	} 
        else if(strtolower($action)==strtolower('addedit')){

           
        if(isset($_POST['btnsave'])){

            if ( !check_admin_referer( 'action_image_add_edit','add_edit_image_nonce')){

                  wp_die('Security check fail'); 
              }

            $name=trim(sanitize_text_field($_POST['name']));  
            $activetab_bg=sanitize_text_field($_POST['activetab_bg']); 
            $inactive_bg=sanitize_text_field($_POST['inactive_bg']); 
            $ac_border_color=sanitize_text_field($_POST['ac_border_color']); 
            $tab_fcolor=sanitize_text_field($_POST['tab_fcolor']); 
            $tab_a_fcolor=sanitize_text_field($_POST['tab_a_fcolor']); 
            $tab_ccolor=sanitize_text_field($_POST['tab_ccolor']); 
            $type=intval($_POST['type']); 
             
           
            $createdOn = date ( 'Y-m-d h:i:s' );
            if (function_exists ( 'date_i18n' )) {

                    $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                    if (get_option ( 'time_format' ) == 'H:i')
                            $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                    else
                            $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
            }
           
            global $wpdb;
            
            if(isset($_POST['tabid'])){

                    $tabid=(int)$_POST['tabid'];
                    
                       $query = "update ".$wpdb->prefix."wrt_tabs_settings set name='$name',type='$type',activetab_bg='$activetab_bg',inactive_bg='$inactive_bg', 
                        ac_border_color='$ac_border_color',tab_fcolor='$tab_fcolor',tab_a_fcolor='$tab_a_fcolor',tab_ccolor='$tab_ccolor' where id=$tabid";

                        $wpdb->query($query); 
                        $wrt_responsive_tabs_msg=array();
                        $wrt_responsive_tabs_msg['type']='succ';
                        $wrt_responsive_tabs_msg['message']=__('Tab set updated successfully.','wp-best-responsive-tabs');
                        update_option('wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg);
        
            }
            else{
                
                        $query = "insert into ".$wpdb->prefix."wrt_tabs_settings set name='$name',type='$type', 
                        activetab_bg='$activetab_bg',inactive_bg='$inactive_bg',ac_border_color='$ac_border_color',
                        createdon='$createdOn',tab_fcolor='$tab_fcolor',tab_a_fcolor='$tab_a_fcolor',tab_ccolor='$tab_ccolor' ";

                        $wpdb->query($query); 
                        $wrt_responsive_tabs_msg=array();
                        $wrt_responsive_tabs_msg['type']='succ';
                        $wrt_responsive_tabs_msg['message']=__('Tab set added successfully.','wp-best-responsive-tabs');
                        update_option('wp_vgallery_thumbnail_msg', $wrt_responsive_tabs_msg);
                
            }
            
            
            
            $location='admin.php?page=rt_wp_responsive_tabs';
             echo "<script type='text/javascript'> location.href='$location';</script>";
             exit;



        }  
        
         if(isset($_GET['id'])){

                global $wpdb;
                $id= $_GET['id'];
                $query="SELECT * FROM ".$wpdb->prefix."wrt_tabs_settings WHERE id=$id";
                $settings_  = $wpdb->get_row($query,ARRAY_A);
                

                if(!is_array($settings_)){

                     $settings=array(
                            'name'=>'',
                            'activetab_bg' => '#ffffff',
                            'inactive_bg' =>'#00aadd',
                            'ac_border_color' =>'#81d742',
                            'tab_fcolor'=>'#ffffff',
                            'tab_a_fcolor'=>'#428bca',
                            'tab_ccolor'=>'#000000',
                            'type' =>'3',
                        );
                     
                     

                }
                else{

                    
                        
                      $settings=array(
                            'name'=>sanitize_text_field($settings_['name']),
                            'activetab_bg' => sanitize_text_field($settings_['activetab_bg']),
                            'inactive_bg' =>sanitize_text_field($settings_['inactive_bg']),
                            'ac_border_color' =>sanitize_text_field($settings_['ac_border_color']),
                            'tab_fcolor' =>sanitize_text_field($settings_['tab_fcolor']),
                            'tab_a_fcolor' =>sanitize_text_field($settings_['tab_a_fcolor']),
                            'tab_ccolor' =>sanitize_text_field($settings_['tab_ccolor']),
                            'type' =>intval($settings_['type']),
                      
                        );
                      
                  

                }

            }else{

                 $settings=array(
                                'name'=>'',
                                'activetab_bg' => '#ffffff',
                                'inactive_bg' =>'#00aadd',
                                'ac_border_color' =>'#81d742',
                                'tab_fcolor'=>'#ffffff',
                                'tab_a_fcolor'=>'#428bca',
                                'tab_ccolor'=>'#000000',
                                'type' =>'3',
                        );

            }
        
        

    ?>      
  <div id="poststuff" > 
        <div id="post-body" class="metabox-holder columns-2" >  
           <div id="post-body-content">
                 
                <div class="wrap">
                         <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                          <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/best-wordpress-responsive-tabs-plugin.html"><?php echo __('UPGRADE TO PRO VERSION','wp-best-responsive-tabs');?></a></h3></span>
 
                <?php
                    $messages=get_option('wrt_responsive_tabs_msg'); 
                    $type='';
                    $message='';
                    if(isset($messages['type']) and $messages['type']!=""){

                        $type=$messages['type'];
                        $message=$messages['message'];

                    }  


                    if(trim($type)=='err'){ echo "<div class='notice notice-error is-dismissible'><p>"; echo $message; echo "</p></div>";}
                    else if(trim($type)=='succ'){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $message; echo "</p></div>";}
       


                    update_option('wrt_responsive_tabs_msg', array());     
                ?>      

                <?php if(isset($_GET['id']) and intval($_GET['id']>0)):?> 
                  
                    <h2><?php echo __("Update Tab Set",'wp-best-responsive-tabs');?></h2>
                    
                <?php else:?>    
                
                    <h2><?php echo __("Add Tab Set",'wp-best-responsive-tabs');?></h2>
                    
                <?php endif;?>    
                    
                <div id="poststuff">   
                    <div id="post-body" class="metabox-holder columns-2">
                        <div id="post-body-content" >
                                <form method="post" action="" id="scrollersettiings" name="scrollersettiings" >
                                    
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="link_name"><?php echo __('Name','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="name" size="30" name="name" value="<?php echo $settings['name']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    <div class="stuffbox" id="slider_easing" style="width:100%;">
                                        <h3><label><?php echo __( 'Type','wp-best-responsive-tabs');?></label></h3>
                                        <div class="inside">
                                            <table>
                                                <tr>
                                                    <td>
                                                        <select name="type" id="type">
                                                            <option value=""><?php echo __( 'Select','wp-best-responsive-tabs');?></option>
                                                            
                                                            <option <?php if ($settings['type'] == "4") { ?> selected="selected" <?php } ?> value="4"><?php echo __( 'Responsive Horizontal Tabs','wp-best-responsive-tabs');?></option>
                                                            <option <?php if ($settings['type'] == "2") { ?> selected="selected" <?php } ?> value="2"><?php echo __( 'Responsive Vertical Tabs','wp-best-responsive-tabs');?></option>
                                                            <option <?php if ($settings['type'] == "5") { ?> selected="selected" <?php } ?> value="5"><?php echo __( 'Responsive Accordion Tabs','wp-best-responsive-tabs');?></option>
                                                            
                                                            </select>
                                                        <div style="clear: both"></div>
                                                        <div></div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div style="clear:both"></div>

                                        </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="activetab_bg"><?php echo __('Active Tab Background Color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="activetab_bg" size="30" name="activetab_bg" value="<?php echo $settings['activetab_bg']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="inactive_bg"><?php echo __('Inactive Tab Background Color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="inactive_bg" size="30" name="inactive_bg" value="<?php echo $settings['inactive_bg']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="ac_border_color"><?php echo __('Border Color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="ac_border_color" size="30" name="ac_border_color" value="<?php echo $settings['ac_border_color']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="tab_fcolor"><?php echo __('Tab font color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="tab_fcolor" size="30" name="tab_fcolor" value="<?php echo $settings['tab_fcolor']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="tab_a_fcolor"><?php echo __('Active Tab font color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="tab_a_fcolor" size="30" name="tab_a_fcolor" value="<?php echo $settings['tab_a_fcolor']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                    <div class="stuffbox" id="namediv" style="width: 100%">
                                            <h3>
                                                    <label for="tab_ccolor"><?php echo __('Tab Content color','wp-best-responsive-tabs');?> 
                                                    </label>
                                            </h3>
                                            <div class="inside">
                                                    <div>
                                                            <input class="input-text" type="text" id="tab_ccolor" size="30" name="tab_ccolor" value="<?php echo $settings['tab_ccolor']; ?>">
                                                    </div>
                                                    <div style="clear: both"></div>
                                                    <div></div>
                                                    <div style="clear: both"></div>
                                            </div>
                                    </div>
                                     <?php wp_nonce_field('action_image_add_edit','add_edit_image_nonce'); ?>
                                                         <?php if(isset($_GET['id']) and (int) $_GET['id']>0){ ?> 
                                                            <input type="hidden" name="tabid" id="tabid" value="<?php echo $_GET['id'];?>">
                                                            <?php
                                                            } 
                                                        ?>  
                                                        <input type="submit"  name="btnsave" id="btnsave" value="<?php echo __("Save Changes",'wp-best-responsive-tabs');?>" class="button-primary">    
                                                        &nbsp;&nbsp;<input type="button"
										name="cancle" id="cancle" value="<?php echo __('Cancel','wp-best-responsive-tabs');?>"
										class="button-primary"
										onclick="location.href = 'admin.php?page=rt_wp_responsive_tabs'">

                                </form>
                                <script type="text/javascript">

                                    var $n = jQuery.noConflict();  
                                    
                                  
                                    $n(document).ready(function() {

                                            $n("#scrollersettiings").validate({
                                                    rules: {
                                                         
                                                         name: {
                                                            required:true,
                                                            maxlength:250
                                                        },  
                                                         type: {
                                                            required:true,
                                                            number:true,
                                                        },  
                                                        activetab_bg: {
                                                            required:true,
                                                            maxlength:7
                                                        },
                                                        inactive_bg: {
                                                            required:true,
                                                            maxlength:7
                                                        },
                                                        ac_border_color: {
                                                            required:true,
                                                            maxlength:7
                                                        },
                                                        tab_fcolor: {
                                                            required:true,
                                                            maxlength:7
                                                        },
                                                        tab_a_fcolor: {
                                                            required:true,
                                                            maxlength:7
                                                        },
                                                        tab_ccolor: {
                                                            required:true,
                                                            maxlength:7
                                                        }
                                                        

                                                    },
                                                    errorClass: "image_error",
                                                    errorPlacement: function(error, element) {
                                                        error.appendTo( element.next().next());
                                                    } 


                                            })
                                            
                                             $n('#activetab_bg').wpColorPicker();
                                             $n('#inactive_bg').wpColorPicker();
                                             $n('#ac_border_color').wpColorPicker();
                                             $n('#tab_fcolor').wpColorPicker();
                                             $n('#tab_a_fcolor').wpColorPicker();
                                             $n('#tab_ccolor').wpColorPicker();
                                    });

                                </script> 

                            </div>
                            
                            
                        
                    </div>                                              
                    <div id="postbox-container-1" class="postbox-container" style="float: right" > 

                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','wp-best-responsive-tabs');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                                <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__);?>" width="250" height="250">
                                            </a></center>

                                        <div style="margin:10px 5px">

                                        </div>
                                    </div></div>
                                <div class="postbox"> 
                                    <h3 class="hndle"><span></span><?php echo __('Speed Test For Your WP','wp-best-responsive-tabs');?></h3> 
                                    <div class="inside">
                                        <center><a href="http://shareasale.com/r.cfm?b=875645&amp;u=675922&amp;m=41388&amp;urllink=&amp;afftrack=" target="_blank">
                                                <img src="<?php echo plugins_url( 'images/300x250.png', __FILE__ );?>" width="250" height="250" border="0">
                                            </a></center>
                                        <div style="margin:10px 5px">
                                        </div>
                                    </div></div>

                            </div> 
                </div>  
            </div>      
        </div>
        <div class="clear"></div></div>  
        
        <?php
        }
        else if (strtolower ( $action ) == strtolower ( 'delete' )) {
		
                 $retrieved_nonce = '';

                if (isset($_GET['nonce']) and $_GET['nonce'] != '') {

                    $retrieved_nonce = $_GET['nonce'];
                }
                if (!wp_verify_nonce($retrieved_nonce, 'delete_tabset')) {


                    wp_die('Security check fail');
                }

		global $wpdb;
		$location = "admin.php?page=rt_wp_responsive_tabs";
		$deleteId = intval(sanitize_text_field($_GET ['id']));
		
		
		try {
			
                        
			$query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs WHERE gtab_id=$deleteId";
			$myrows = $wpdb->get_results ( $query );
			
			foreach ( $myrows as $myrow ) {
				
				if (is_object ( $myrow )) {
					
					
					$query = "delete from  " . $wpdb->prefix . "wrt_tabs where id=" . $myrow->id;
					$wpdb->query ( $query );
				}
			}
			
			$query = "delete from  " . $wpdb->prefix . "wrt_tabs_settings where id=$deleteId";
			$wpdb->query ( $query );
			
			$wrt_responsive_tabs_msg = array ();
			$wrt_responsive_tabs_msg ['type'] = 'succ';
			$wrt_responsive_tabs_msg ['message'] = __('Tab set deleted successfully.','wp-best-responsive-tabs');
			update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
                        
		} catch ( Exception $e ) {
			
			$wrt_responsive_tabs_msg = array ();
			$wrt_responsive_tabs_msg ['type'] = 'err';
			$wrt_responsive_tabs_msg ['message'] = __('Error while deleting tab set.','wp-best-responsive-tabs');
			update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
		}
		
		echo "<script type='text/javascript'> location.href='$location';</script>";
		exit ();
	} 
        else if (strtolower ( $action ) == strtolower ( 'deleteselected' )) {
		
               if (!check_admin_referer('action_settings_mass_delete', 'mass_delete_nonce')) {

                        wp_die('Security check fail');
                    }
                    
		global $wpdb;
		
		$location = "admin.php?page=rt_wp_responsive_tabs";
		if (isset ( $_POST ) and isset ( $_POST ['deleteselected'] ) and ($_POST ['action'] == 'delete' or $_POST ['action_upper'] == 'delete')) {
			
			if (sizeof ( $_POST ['thumbnails'] ) > 0) {
				
				$deleteto = $_POST ['thumbnails'];
				$implode = implode ( ',', $deleteto );
				
				try {
					
					foreach ( $deleteto as $deleteId ) {
						
                                            $deleteId=intval(sanitize_text_field($deleteId));
                                            


						$query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs WHERE gtab_id=$deleteId";
						$myrows = $wpdb->get_results ( $query );
						
						foreach ( $myrows as $myrow ) {
							
							if (is_object ( $myrow )) {
								
								
								$query = "delete from  " . $wpdb->prefix . "wrt_tabs where id=" . $myrow->id;
								$wpdb->query ( $query );
							}
						}
						
						$query = "delete from  " . $wpdb->prefix . "wrt_tabs_settings where id=$deleteId";
						$wpdb->query ( $query );
					}
                                        
					$wrt_responsive_tabs_msg = array ();
					$wrt_responsive_tabs_msg ['type'] = 'succ';
					$wrt_responsive_tabs_msg ['message'] = __('Selected tab sets deleted successfully.','wp-best-responsive-tabs');
					update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
                                        
				} catch ( Exception $e ) {
					
					$wrt_responsive_tabs_msg = array ();
					$wrt_responsive_tabs_msg ['type'] = 'err';
					$wrt_responsive_tabs_msg ['message'] = __('Error while deleting tab sets.','wp-best-responsive-tabs');
					update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
		} else {
			
			echo "<script type='text/javascript'> location.href='$location';</script>";
			exit ();
		}
	}
        
}

function rt_wp_responsive_tabs_data_management() {
    
        $tabid = 0;
	if (isset ( $_GET ['tabid'] ) and $_GET ['tabid'] > 0) {
		// do nothing
		
		$tabid = intval(sanitize_text_field( $_GET ['tabid'] ));
                
	} else {
		
		$wrt_responsive_tabs_msg = array ();
		$wrt_responsive_tabs_msg ['type'] = 'err';
		$wrt_responsive_tabs_msg ['message'] = __('Please select tab set. Click on "Manage Tabs" of your desired tab set.','wp-best-responsive-tabs');
		update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
		$location = 'admin.php?page=rt_wp_responsive_tabs';
		echo "<script type='text/javascript'> location.href='$location';</script>";
		exit ();
	}
        
	$action = 'gridview';
	global $wpdb;
	
        $location = "admin.php?page=rt_wp_responsive_tabs&tabid=$tabid";
        
	if (isset ( $_GET ['action'] ) and $_GET ['action'] != '') {
		
		$action = trim ( sanitize_text_field($_GET ['action'] ));
                
                if(isset($_GET['order_by'])){
        
                    if(sanitize_sql_orderby($_GET['order_by'])){
                        $order_by=trim($_GET['order_by']); 
                    }
                    else{
                        
                        $order_by=' id ';
                    }
                 }

                 if(isset($_GET['order_pos'])){

                    $order_pos=trim(sanitize_text_field($_GET['order_pos'])); 
                 }

                 $search_term_='';
                 if(isset($_GET['search_term'])){

                    $search_term_='&search_term='.urlencode(sanitize_text_field($_GET['search_term']));
                 }
	}
        
         $search_term_='';
        if(isset($_GET['search_term'])){

           $search_term_='&search_term='.urlencode(sanitize_text_field($_GET['search_term']));
        }
	?>

        <?php
	if (strtolower ( $action ) == strtolower ( 'gridview' )) {
		
		$wpcurrentdir = dirname ( __FILE__ );
		$wpcurrentdir = str_replace ( "\\", "/", $wpcurrentdir );
		
		$uploads = wp_upload_dir ();
		$baseurl = $uploads ['baseurl'];
		$baseurl .= '/wp-best-responsive-tabs/';
		?> 
            <div class="wrap">
                
               <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                          <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/best-wordpress-responsive-tabs-plugin.html"><?php echo __('UPGRADE TO PRO VERSION','wp-best-responsive-tabs');?></a></h3></span>

		<style type="text/css">
                .pagination {
                        clear: both;
                        padding: 20px 0;
                        position: relative;
                        font-size: 11px;
                        line-height: 13px;
                }

                .pagination span, .pagination a {
                        display: block;
                        float: left;
                        margin: 2px 2px 2px 0;
                        padding: 6px 9px 5px 9px;
                        text-decoration: none;
                        width: auto;
                        color: #fff;
                        background: #555;
                }

                .pagination a:hover {
                        color: #fff;
                        background: #3279BB;
                }

                .pagination .current {
                        padding: 6px 9px 5px 9px;
                        background: #3279BB;
                        color: #fff;
                }
                </style>
		<!--[if !IE]><!-->
		<style type="text/css">
                    @media only screen and (max-width: 800px) {
                            /* Force table to not be like tables anymore */
                            #no-more-tables table, #no-more-tables thead, #no-more-tables tbody,
                                    #no-more-tables th, #no-more-tables td, #no-more-tables tr {
                                    display: block;
                            }

                            /* Hide table headers (but not display: none;, for accessibility) */
                            #no-more-tables thead tr {
                                    position: absolute;
                                    top: -9999px;
                                    left: -9999px;
                            }
                            #no-more-tables tr {
                                    border: 1px solid #ccc;
                            }
                            #no-more-tables td {
                                    /* Behave  like a "row" */
                                    border: none;
                                    border-bottom: 1px solid #eee;
                                    position: relative;
                                    padding-left: 50%;
                                    white-space: normal;
                                    text-align: left;
                            }
                            #no-more-tables td:before {
                                    /* Now like a table header */
                                    position: absolute;
                                    /* Top/left values mimic padding */
                                    top: 6px;
                                    left: 6px;
                                    width: 45%;
                                    padding-right: 10px;
                                    white-space: nowrap;
                                    text-align: left;
                                    font-weight: bold;
                            }

                            /*
                                            Label the data
                                            */
                            #no-more-tables td:before {
                                    content: attr(data-title);
                            }
                    }
                    </style>
		<!--<![endif]-->

                <?php
		$messages = get_option ( 'wrt_responsive_tabs_msg' );
		$type = '';
		$message = '';
		if (isset ( $messages ['type'] ) and $messages ['type'] != "") {
			
			$type = $messages ['type'];
			$message = $messages ['message'];
		}
		
		 if(trim($type)=='err'){ echo "<div class='notice notice-error is-dismissible'><p>"; echo $message; echo "</p></div>";}
                 else if(trim($type)=='succ'){ echo "<div class='notice notice-success is-dismissible'><p>"; echo $message; echo "</p></div>";}
       
		
		update_option ( 'wrt_responsive_tabs_msg', array () );
		?>

                  <div id="poststuff" >
                    <div id="post-body" class="metabox-holder columns-2">
                        <div style="" id="post-body-content" >
				<div class="icon32 icon32-posts-post" id="icon-edit">
					<br>
				</div>
				<h2>
					<?php echo __('Tabs','wp-best-responsive-tabs');?><a class="button add-new-h2" href="admin.php?page=rt_wp_responsive_tabs_management&action=addedit&tabid=<?php echo $tabid; ?>"><?php echo __('Add New','wp-best-responsive-tabs');?></a>
				</h2>
				<br />

				<form method="POST"
					action="admin.php?page=rt_wp_responsive_tabs_management&action=deleteselected&tabid=<?php echo $tabid; ?>"
					id="posts-filter" onkeypress="return event.keyCode != 13;">
					<div class="alignleft actions">
						<select name="action_upper" id="action_upper">
							<option selected="selected" value="-1"><?php echo __('Bulk Actions','wp-best-responsive-tabs');?></option>
							<option value="delete"><?php echo __('delete','wp-best-responsive-tabs');?></option>
						</select> <input type="submit" value="<?php echo __('Apply','wp-best-responsive-tabs');?>"
							class="button-secondary action" id="deleteselected"
							name="deleteselected" onclick="return confirmDelete_bulk();">
					</div>
                                      <?php
                                        

                                             $setacrionpage="admin.php?page=rt_wp_responsive_tabs_management&tabid=$tabid";

                                             if(isset($_GET['order_by']) and $_GET['order_by']!=""){
                                               $setacrionpage.='&order_by='.sanitize_text_field($_GET['order_by']);   
                                             }

                                             if(isset($_GET['order_pos']) and $_GET['order_pos']!=""){
                                              $setacrionpage.='&order_pos='.sanitize_text_field($_GET['order_pos']);   
                                             }

                                             $seval="";
                                             if(isset($_GET['search_term']) and $_GET['search_term']!=""){
                                              $seval=trim(sanitize_text_field($_GET['search_term']));   
                                             }

                                         ?>
					<br class="clear">
                                                    <?php
							global $wpdb;
                                                       
							
                                                        
                                                        $order_by='id';
                                                        $order_pos="asc";

                                                        if(isset($_GET['order_by']) and sanitize_sql_orderby($_GET['order_by'])!==false){

                                                           $order_by=trim($_GET['order_by']); 
                                                        }

                                                        if(isset($_GET['order_pos'])){

                                                           $order_pos=trim(sanitize_text_field($_GET['order_pos'])); 
                                                        }
                                                         $search_term='';
                                                        if(isset($_GET['search_term'])){

                                                           $search_term= sanitize_text_field($_GET['search_term']);
                                                        }

                                                        $query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs where gtab_id=$tabid ";
                                                        if($search_term!=''){
                                                           $query.=" and ( id like '%$search_term%' or tab_title like '%$search_term%' ) "; 
                                                        }

                                                        $order_by=sanitize_text_field($order_by);
                                                        $order_pos=sanitize_text_field($order_pos);

                                                        $query.=" order by $order_by $order_pos";
                                                        
                                                        //echo $query;die;
                                                        $rows = $wpdb->get_results ( $query ,'ARRAY_A' );
                                                        $rowCount = sizeof ( $rows );
                                                                       
							?>
                                            
                                            <div style="padding-top:5px;padding-bottom:5px">
                                                <b><?php echo __( 'Search','wp-best-responsive-tabs');?> : </b>
                                                  <input type="text" value="<?php echo $seval;?>" id="search_term" name="search_term">&nbsp;
                                                  <input type='button'  value='<?php echo __( 'Search','wp-best-responsive-tabs');?>' name='searchusrsubmit' class='button-primary' id='searchusrsubmit' onclick="SearchredirectTO();" >&nbsp;
                                                  <input type='button'  value='<?php echo __( 'Reset Search','wp-best-responsive-tabs');?>' name='searchreset' class='button-primary' id='searchreset' onclick="ResetSearch();" >
                                            </div>  
                                            <script type="text/javascript" >
                                               var $n = jQuery.noConflict();   
                                                $n('#search_term').on("keyup", function(e) {
                                                       if (e.which == 13) {
                                                  
                                                           SearchredirectTO();
                                                       }
                                                  });   
                                             function SearchredirectTO(){
                                               var redirectto='<?php echo $setacrionpage; ?>';
                                               var searchval=jQuery('#search_term').val();
                                               redirectto=redirectto+'&search_term='+jQuery.trim(encodeURIComponent(searchval));  
                                               window.location.href=redirectto;
                                             }
                                            function ResetSearch(){

                                                 var redirectto='<?php echo $setacrionpage; ?>';
                                                 window.location.href=redirectto;
                                                 exit;
                                            }
                                            </script>            
                                             <div id="no-more-tables">
						<table cellspacing="0" id="gridTbl" class="table-bordered table-striped table-condensed cf wp-list-table widefat">
							<thead>
								<tr>
									<th class="manage-column column-cb check-column" scope="col"><input type="checkbox"></th>
									 <?php if($order_by=="id" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Id','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="id"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Id','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=id&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Id','wp-best-responsive-tabs');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
                                                                        
                                                                        <?php if($order_by=="tab_title" and $order_pos=="asc"):?>

                                                                             <th><a href="<?php echo $setacrionpage;?>&order_by=tab_title&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Title','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                        <?php else:?>
                                                                            <?php if($order_by=="tab_title"):?>
                                                                        <th><a href="<?php echo $setacrionpage;?>&order_by=tab_title&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Title','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <th><a href="<?php echo $setacrionpage;?>&order_by=tab_title&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Title','wp-best-responsive-tabs');?></a></th>
                                                                            <?php endif;?>    
                                                                        <?php endif;?>  
									  <?php if($order_by=="morder" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=morder&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Display Order','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="morder"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=morder&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Display Order','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=morder&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Display Order','wp-best-responsive-tabs');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
								            
                                                                           
									  <?php if($order_by=="createdon" and $order_pos=="asc"):?>
                                                                               
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=desc<?php echo $search_term_;?>"><?php echo __('Published On','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/desc.png', __FILE__); ?>"/></a></th>
                                                                            <?php else:?>
                                                                                <?php if($order_by=="createdon"):?>
                                                                            <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Published On','wp-best-responsive-tabs');?><img style="vertical-align:middle" src="<?php echo plugins_url('/images/asc.png', __FILE__); ?>"/></a></th>
                                                                                <?php else:?>
                                                                                    <th><a href="<?php echo $setacrionpage;?>&order_by=createdon&order_pos=asc<?php echo $search_term_;?>"><?php echo __('Published On','wp-best-responsive-tabs');?></a></th>
                                                                                <?php endif;?>    
                                                                            <?php endif;?>  
								                         
									
									<th><span><?php echo __('Edit','wp-best-responsive-tabs');?></span></th>
									<th><span><?php echo __('Delete','wp-best-responsive-tabs');?></span></th>
								</tr>
							</thead>

							<tbody id="the-list">
                                                            <?php
								if (count ( $rows ) > 0) {
									
									global $wp_rewrite;
									$rows_per_page = 15;
									
									$current = (isset($_GET ['paged'])) ? intval(sanitize_text_field($_GET ['paged'])) : 1;
									$pagination_args = array (
											'base' => @add_query_arg ( 'paged', '%#%' ),
											'format' => '',
											'total' => ceil ( sizeof ( $rows ) / $rows_per_page ),
											'current' => $current,
											'show_all' => false,
											'type' => 'plain' 
									);
									
									$start = ($current - 1) * $rows_per_page;
									$end = $start + $rows_per_page;
									$end = (sizeof ( $rows ) < $end) ? sizeof ( $rows ) : $end;
									$delRecNonce = wp_create_nonce('delete_tab');
									for($i = $start; $i < $end; ++ $i) {
										
										$row = $rows [$i];
										
										$id = $row ['id'];
										$editlink = "admin.php?page=rt_wp_responsive_tabs_management&action=addedit&id=$id&tabid=$tabid";
										$deletelink = "admin.php?page=rt_wp_responsive_tabs_management&action=delete&id=$id&nonce=$delRecNonce&tabid=$tabid";
										

										?>
                                                                        <tr valign="top">
                                                                            <td class="alignCenter check-column" data-title="Select Record"><input
                                                                                    type="checkbox" value="<?php echo $row['id'] ?>"
                                                                                    name="thumbnails[]"></td>
                                                                            <td data-title="<?php echo __('Id','wp-best-responsive-tabs');?>" class="alignCenter"><?php echo intval($row['id']); ?></td>
                                                                            <td data-title="<?php echo __('Title','wp-best-responsive-tabs');?>" class="alignCenter">
                                                                               <div>
                                                                                            <strong><?php echo esc_html($row['tab_title']); ?></strong>
                                                                                    </div>
                                                                            </td>
                                                                            
                                                                             <td data-title="<?php echo __('Display Order','wp-best-responsive-tabs');?>" class="alignCenter"><?php echo intval($row['morder']); ?></td>
                                                                            <td data-title="<?php echo __('Published On','wp-best-responsive-tabs');?>" class="alignCenter"><?php echo esc_html($row['createdon']); ?></td>
                                                                            <td data-title="<?php echo __('Edit','wp-best-responsive-tabs');?>" class="alignCenter"><strong><a href='<?php echo esc_url($editlink); ?>' title="<?php echo __('Edit','wp-best-responsive-tabs');?>"><?php echo __('Edit','wp-best-responsive-tabs');?></a></strong></td>
                                                                            <td data-title="<?php echo __('Delete','wp-best-responsive-tabs');?>" class="alignCenter"><strong><a href='<?php echo esc_url($deletelink); ?>' onclick="return confirmDelete();" title="<?php echo __('Delete','wp-best-responsive-tabs');?>"><?php echo __('Delete','wp-best-responsive-tabs');?></a> </strong></td>
                                                                    </tr>
                                                                    <?php
                                                                            }
                                                                    } else {
                                                                            ?>
                                                                    <tr valign="top" class=""
                                                                            id="">
                                                                            <td colspan="9" data-title="<?php echo __('No Records','wp-best-responsive-tabs');?>" align="center"><strong><?php echo __('No Tabs','wp-best-responsive-tabs');?></strong></td>
                                                                    </tr>
                                                                 <?php
								}
								?>      
                                                        </tbody>
						</table>
					</div>
                                         <?php
                                            if (sizeof ( $rows ) > 0) {
                                                    echo "<div class='pagination' style='padding-top:10px'>";
                                                    echo paginate_links ( $pagination_args );
                                                    echo "</div>";
                                            }
                                            ?>
                                         <br />
					<div class="alignleft actions">
						<select name="action" id="action_bottom">
							<option selected="selected" value="-1"><?php echo __('Bulk Actions','wp-best-responsive-tabs');?></option>
							<option value="delete"><?php echo __('Delete','wp-best-responsive-tabs');?></option>
						</select> 
                                               <?php wp_nonce_field('action_settings_mass_delete', 'mass_delete_nonce'); ?>
                                                <input type="submit" value="<?php echo __('Apply','wp-best-responsive-tabs');?>"
							class="button-secondary action" id="deleteselected"
							name="deleteselected" onclick="return confirmDelete_bulk();">
					</div>

				</form>
				<script type="text/JavaScript">

                                        function  confirmDelete_bulk(){
                                                        var topval=document.getElementById("action_bottom").value;
                                                        var bottomVal=document.getElementById("action_upper").value;

                                                        if(topval=='delete' || bottomVal=='delete'){


                                                            var agree=confirm("<?php echo __('Are you sure you want to delete selected tabs?','wp-best-responsive-tabs');?>");
                                                            if (agree)
                                                                return true ;
                                                            else
                                                                return false;
                                                        }
                                                 }

                                        function  confirmDelete(){
                                         var agree=confirm("<?php echo __('Are you sure you want to delete this tab?','wp-best-responsive-tabs');?>");
                                         if (agree)
                                             return true ;
                                        else
                                            return false;
                                        }
                             </script>
                        </div>
                        <div id="postbox-container-1" class="postbox-container" > 
                            <div class="postbox"> 
                                <h3 class="hndle"><span></span><?php echo __('Google For Business Coupon','responsive-filterable-portfolio');?></h3> 
                                <div class="inside">
                                    <center><a href="https://goo.gl/OJBuHT" target="_blank">
                                            <img src="<?php echo plugins_url( 'images/g-suite-promo-code-4.png', __FILE__ );?>" width="250" height="250" border="0">
                                        </a></center>
                                    <div style="margin:10px 5px">
                                    </div>
                                </div></div>
                            <div class="postbox"> 
                                <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','responsive-filterable-portfolio');?></h3> 
                                <div class="inside">
                                    <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                            <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ );?>" width="250" height="250">
                                        </a></center>

                                    <div style="margin:10px 5px">

                                    </div>
                                </div></div>


                        </div> 
                        <br class="clear">
			</div>
			<div style="clear: both;"></div>
                    <?php $url = plugin_dir_url(__FILE__); ?>


                </div>
		<h3><?php echo __('To print this tab sets into WordPress Post/Page use below code','wp-best-responsive-tabs');?></h3>
		<input type="text"
			value='[wrt_print_rt_wp_responsive_tabs tabset_id="<?php echo intval($tabid); ?>"] '
			style="width: 400px; height: 30px"
			onclick="this.focus(); this.select()" />
		<div class="clear"></div>
		<h3><?php echo __('To print this tab sets into WordPress theme/template PHP files use below code','wp-best-responsive-tabs');?></h3>
                <?php
		$shortcode = '[wrt_print_rt_wp_responsive_tabs tabset_id="'.intval($tabid).'"]';
		?>
                <input type="text"
			value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;"
			style="width: 400px; height: 30px"
			onclick="this.focus(); this.select()" />
            </div>    
		<div class="clear"></div>
                
    <?php
                
	} else if (strtolower ( $action ) == strtolower ( 'addedit' )) {
		$url = plugin_dir_url ( __FILE__ );
		$vNonce = wp_create_nonce('vNonce');
		
                $tabid="0";
                if(isset($_GET['tabid']) and $_GET['tabid']!=""){
                 $tabid=intval(sanitize_text_field($_GET['tabid']));   
                }
		if (isset ( $_POST ['btnsave'] )) {
			
                       if (!check_admin_referer('action_image_add_edit', 'add_edit_image_nonce')) {

                            wp_die('Security check fail');
                        }
			
                        $tab_title = trim ( sanitize_text_field($_POST ['tab_title'] )) ;
                        $morder = trim ( intval(sanitize_text_field($_POST ['morder'] ))) ;
                        $is_default=0;
                        if(isset($_POST['is_default'])){
                            
                            $is_default=1;
                            $query = "update ".$wpdb->prefix."wrt_tabs set is_default='0' where gtab_id=$tabid";
                            $wpdb->query($query); 
                            
                        }
                       
                        
                        $tab_description = trim ($_POST ['tab_description'] ) ;
                        $createdOn = date ( 'Y-m-d h:i:s' );
                        if (function_exists ( 'date_i18n' )) {

                                $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                                if (get_option ( 'time_format' ) == 'H:i')
                                        $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                                else
                                        $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
                            }
			
			   
			
			$location = "admin.php?page=rt_wp_responsive_tabs_management&tabid=$tabid";
				// edit save
			if (isset ( $_POST ['tabid'] ) and intval($_POST ['tabid'])>0) {
				
				try {
						
						$tabid=intval(sanitize_text_field($_POST ['tabid']));
						
                                                 
                                               
                                                 $wpdb->update(
                           
                                                    $wpdb->prefix.'wrt_tabs',
                                                         
                                                    array( 
                                                            'tab_title' => $tab_title, 
                                                            'morder' => $morder,
                                                            'is_default'=> $is_default,
                                                            'tab_description'=>$tab_description
                                                        ),
                                                       array( 
                                                        'id' => $tabid,          // where clause(s)
                                                       ), 
                                                       array( '%s', '%d','%d','%s' ),
                                                       array( 
                                                                '%d'
                                                        )
                                                    );
							
                                                 
							
                                                $wrt_responsive_tabs_msg = array ();
						$wrt_responsive_tabs_msg ['type'] = 'succ';
						$wrt_responsive_tabs_msg ['message'] = __('Tab updated successfully.','wp-best-responsive-tabs');
						update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
                                                
					} catch ( Exception $e ) {
							
						$wrt_responsive_tabs_msg = array ();
                                                $wrt_responsive_tabs_msg ['type'] = 'err';
                                                $wrt_responsive_tabs_msg ['message'] = __('Error while updating tab','wp-best-responsive-tabs');
                                                 update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
				     }

				
				
			} else {
				
                                    $createdOn = date ( 'Y-m-d h:i:s' );
                                    if (function_exists ( 'date_i18n' )) {

                                            $createdOn = date_i18n ( 'Y-m-d' . ' ' . get_option ( 'time_format' ), false, false );
                                            if (get_option ( 'time_format' ) == 'H:i')
                                                    $createdOn = date ( 'Y-m-d H:i:s', strtotime ( $createdOn ) );
                                            else
                                                    $createdOn = date ( 'Y-m-d h:i:s', strtotime ( $createdOn ) );
                                    }

                                    try {
					
					   $wpdb->insert(
                                                $wpdb->prefix."wrt_tabs",
                                                array( 'tab_title' => $tab_title, 'morder' => $morder,'is_default'=> $is_default,'tab_description'=>$tab_description,'createdon'=>$createdOn,'gtab_id'=>$tabid),
                                                array( '%s', '%d','%d','%s','%s' ,'%d')
                                            );
                                        
                                        
					
					$wrt_responsive_tabs_msg = array ();
					$wrt_responsive_tabs_msg ['type'] = 'succ';
					$wrt_responsive_tabs_msg ['message'] = __('New tab added successfully.','wp-best-responsive-tabs');
					
                                        update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
                                        
				} catch ( Exception $e ) {
					
					$wrt_responsive_tabs_msg = array ();
					$wrt_responsive_tabs_msg ['type'] = 'err';
					$wrt_responsive_tabs_msg ['message'] = __('Error while adding tab','wp-best-responsive-tabs');
					update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
				}
				
				
			}
                       
                          
                       
                   
                   

                    
                    echo "<script type='text/javascript'> location.href='$location';</script>";
                    exit ();
                   
                   
		} else {
			
			$uploads = wp_upload_dir ();
			$baseurl = $uploads ['baseurl'];
			$baseurl .= '/wp-best-responsive-tabs/';
			?>
         <div style="float: left; width: 100%;">
	       <div class="wrap">
                
                  <table><tr><td><a href="https://twitter.com/FreeAdsPost" class="twitter-follow-button" data-show-count="false" data-size="large" data-show-screen-name="false">Follow @FreeAdsPost</a>
                                    <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script></td>
                                <td>
                                    <a target="_blank" title="Donate" href="http://www.i13websolution.com/donate-wordpress_image_thumbnail.php">
                                        <img id="help us for free plugin" height="30" width="90" src="<?php echo plugins_url( 'images/paypaldonate.jpg', __FILE__ ) ;?>" border="0" alt="help us for free plugin" title="help us for free plugin">
                                    </a>
                                </td>
                            </tr>
                        </table>
                          <span><h3 style="color: blue;"><a target="_blank" href="https://www.i13websolution.com/best-wordpress-responsive-tabs-plugin.html"><?php echo __('UPGRADE TO PRO VERSION','wp-best-responsive-tabs');?></a></h3></span>

	    	<?php
		    	if (isset ( $_GET ['id'] ) and $_GET ['id'] > 0) {
				
				$id = intval(sanitize_text_field($_GET ['id']));
				$query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs WHERE gtab_id=$tabid and id=$id";
				
				$myrow = $wpdb->get_row ( $query );
				
				if (is_object ( $myrow )) {
				
                
					$title =  wp_unslash(esc_html($myrow->tab_title));
					$tab_description = wp_unslash($myrow->tab_description);
					$gtab_id = sanitize_text_field($myrow->gtab_id);
                                        $is_default=esc_html($myrow->is_default);
                                        $morder=esc_html($myrow->morder);
                                        
					
					
					
				}
				?>
	         <h2><?php echo __('Update Tab','wp-best-responsive-tabs');?></h2><?php
			} else {
				
				$title = '';
                                $tab_description = '';
                                $morder='';
                                $is_default=0;
                               
                                
				?>
                 <h2><?php echo __('Add Tab','wp-best-responsive-tabs');?></h2>
                   <?php } ?>
                   <br />
					<div id="poststuff">
						<div id="post-body" class="metabox-holder columns-2">
							<div id="post-body-content">
                                                            
                                                                    
                                                                   <form method="post" action="" id="addimage_" name="addimage_" enctype="multipart/form-data" >
                                                                    
                                                                        <div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"><?php echo __('Tab Title','wp-best-responsive-tabs');?> 
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="title" size="30" name="tab_title" value="<?php echo $title; ?>">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>
										</div>
									</div>
									
                                                                       
									<div class="stuffbox" id="namediv" style="width: 100%">
										<h3>
											<label for="link_name"> <?php echo __('Tab Order','wp-best-responsive-tabs');?> 
											</label>
										</h3>
										<div class="inside">
											<div>
												<input type="text" id="morder" size="30"
													name="morder" value="<?php echo $morder; ?>"
													style="width: 50px;">
											</div>
											<div style="clear: both"></div>
											<div></div>
											<div style="clear: both"></div>

										</div>
									</div>
                                                                        
                                                                       
                                                                       <div class="stuffbox cont_editor" id="namediv" style="width:100%" >
                                                                            <h3><label for="link_name"><?php echo __('Tab Content','wp-best-responsive-tabs'); ?></label></h3>
                                                                            <div class="inside">
                                                                                <?php wp_editor( $tab_description, 'tab_description' );?>
                                                                                <div>
                                                                                <input type="hidden" name="editor_val" id="editor_val" />
                                                                                </div>
                                                                                <div style="clear: both;"></div><div></div>
                                                                                <div></div>
                                                                                <div style="clear:both"></div>
                                                                               
                                                                            </div>
                                                                        </div>
                                                                       <div class="stuffbox" id="namediv" style="width: 100%">
                                                                                <h3>
                                                                                        <label for="is_default"><?php echo __('Is Default Selected Tab?','wp-best-responsive-tabs');?> 
                                                                                        </label>
                                                                                </h3>
                                                                                <div class="inside">
                                                                                        <div>
                                                                                                  <input type="checkbox" id="is_default" size="30" name="is_default" value="" <?php if($is_default==true){echo "checked='checked'";} ?> style="width:20px;">&nbsp;<?php echo __('Is Default Selected Tab?','wp-best-responsive-tabs');?>  

                                                                                        </div>
                                                                                        <div style="clear: both"></div>
                                                                                        <div></div>
                                                                                        <div style="clear: both"></div>
                                                                                </div>
                                                                        </div>
									
                                                                        <?php if (isset($_GET['id']) and intval(sanitize_text_field($_GET['id'])) > 0) { ?> 
										 <input type="hidden" name="tabid" id="tabid" value="<?php echo intval(sanitize_text_field($_GET['id'])); ?>">
                                                                         <?php
										}
										?>
                                                                            <?php wp_nonce_field('action_image_add_edit', 'add_edit_image_nonce'); ?>      
                                                                            <input type="submit"
										onclick="" name="btnsave" id="btnsave" value="<?php echo __('Save Changes','wp-best-responsive-tabs');?>"
										class="button-primary">&nbsp;&nbsp;<input type="button"
										name="cancle" id="cancle" value="<?php echo __('Cancel','wp-best-responsive-tabs');?>"
										class="button-primary"
										onclick="location.href = 'admin.php?page=rt_wp_responsive_tabs_management&tabid=<?php echo $tabid;?>'">

								</form>
                                                                   
								<script type="text/javascript">

                                                                    var $n = jQuery.noConflict();
                                                                    $n(document).ready(function() {

                                                                      
                                                                        $n.validator.setDefaults({ 
                                                                            ignore: [],
                                                                            // any other default options and/or rules
                                                                        });
                                                                        $n.validator.addMethod("chkCont", function(value, element) {
                                            
                                                                                var editorcontent=tinyMCE.get('tab_description').getContent();

                                                                                if (editorcontent.length){
                                                                                  return true;
                                                                                }
                                                                                else{
                                                                                   return false;
                                                                                }


                                                                          },
                                                                               "Please enter tab content"
                                                                          );
                                                                     
                                                                         
                                                                         
                                                                           $n("#addimage_").validate({
                                                                            rules: {
                                                                             tab_title:{
                                                                               required:true  
                                                                             },
                                                                             editor_val:{
                                                                               chkCont:true  
                                                                             },
                                                                             morder:{
                                                                                digits:true,
                                                                                maxlength:15
                                                                             }
                                                                             
                                                                             
                                                                            },
                                                                             errorClass: "image_error",
                                                                             errorPlacement: function(error, element) {
                                                                             error.appendTo(element.parent().next().next());
                                                                             }, messages: {
                                                                                 HdnMediaSelection: "Please select slider image.",

                                                                             }

                                                                         })
                                                                           
                                                                           
                                                                         
                                                                     });
                                                                     
                                                                   
                                                                 </script>

							</div>
                                                        <div id="postbox-container-1" class="postbox-container" > 
                                                            <div class="postbox"> 
                                                                <h3 class="hndle"><span></span><?php echo __('Google For Business Coupon','responsive-filterable-portfolio');?></h3> 
                                                                <div class="inside">
                                                                    <center><a href="https://goo.gl/OJBuHT" target="_blank">
                                                                            <img src="<?php echo plugins_url( 'images/g-suite-promo-code-4.png', __FILE__ );?>" width="250" height="250" border="0">
                                                                        </a></center>
                                                                    <div style="margin:10px 5px">
                                                                    </div>
                                                                </div></div>
                                                            <div class="postbox"> 
                                                                <h3 class="hndle"><span></span><?php echo __('Access All Themes In One Price','responsive-filterable-portfolio');?></h3> 
                                                                <div class="inside">
                                                                    <center><a href="http://www.elegantthemes.com/affiliates/idevaffiliate.php?id=11715_0_1_10" target="_blank">
                                                                            <img border="0" src="<?php echo plugins_url( 'images/300x250.gif', __FILE__ );?>" width="250" height="250">
                                                                        </a></center>

                                                                    <div style="margin:10px 5px">

                                                                    </div>
                                                                </div></div>
                                                           

                                                        </div>      
						</div>
                                            
					</div>
                                        
				</div>
			</div>
<?php
		}
	} else if (strtolower ( $action ) == strtolower ( 'delete' )) {
		
             $retrieved_nonce = '';

              if(isset($_GET['nonce']) and $_GET['nonce']!=''){

                  $retrieved_nonce=$_GET['nonce'];

              }
              $tabid='';
              if(isset($_GET['tabid']) and $_GET['tabid']!=''){

                  $tabid=intval($_GET['tabid']);

              }
              if (!wp_verify_nonce($retrieved_nonce, 'delete_tab' ) ){


                  wp_die('Security check fail'); 
              }

		
		
		
		$location = "admin.php?page=rt_wp_responsive_tabs_management&tabid=$tabid";
		$deleteId = (int) intval(sanitize_text_field($_GET ['id']));
		
		try {
			
			$query = "SELECT * FROM " . $wpdb->prefix . "wrt_tabs WHERE id=$deleteId ";
			$myrow = $wpdb->get_row ( $query );
			
			if (is_object ( $myrow )) {
				
				$query = "delete from  " . $wpdb->prefix . "wrt_tabs where id=$deleteId  ";
				$wpdb->query ( $query );
				
				$wrt_responsive_tabs_msg = array ();
				$wrt_responsive_tabs_msg ['type'] = 'succ';
				$wrt_responsive_tabs_msg ['message'] =  __('Tab deleted successfully.','wp-best-responsive-tabs');
				update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
			}
		} catch ( Exception $e ) {
			
			$wrt_responsive_tabs_msg = array ();
			$wrt_responsive_tabs_msg ['type'] = 'err';
			$wrt_responsive_tabs_msg ['message'] =  __('Error while deleting tab.','wp-best-responsive-tabs');
			update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
		}
		
		echo "<script type='text/javascript'> location.href='$location';</script>";
		exit ();
	} else if (strtolower ( $action ) == strtolower ( 'deleteselected' )) {
		
                if(!check_admin_referer('action_settings_mass_delete','mass_delete_nonce')){

                        wp_die('Security check fail'); 
                  }

		$tabid='';
               if(isset($_GET['tabid']) and $_GET['tabid']!=''){

                  $tabid=intval($_GET['tabid']);

               }
              
		$location = "admin.php?page=rt_wp_responsive_tabs_management&tabid=$tabid";
		
		if (isset ( $_POST ) and isset ( $_POST ['deleteselected'] ) and (sanitize_text_field($_POST ['action']) == 'delete' or sanitize_text_field($_POST ['action_upper']) == 'delete')) {
			
				
			if (sizeof ( $_POST ['thumbnails'] ) > 0) {
				
                                
				$deleteto = $_POST ['thumbnails'];
				
				try {
					
					foreach ( $deleteto as $tab ) {
						
                                                $tab=intval($tab);
						$query = "delete from  " . $wpdb->prefix . "wrt_tabs where id=$tab ";
					        $wpdb->query ( $query );
                                                
                                             
						$wrt_responsive_tabs_msg = array ();
                                                $wrt_responsive_tabs_msg ['type'] = 'succ';
                                                $wrt_responsive_tabs_msg ['message'] = __('selected tabs deleted successfully.','wp-best-responsive-tabs');
                                                update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
						
					}
                                        
				} catch ( Exception $e ) {
					
					$wrt_responsive_tabs_msg = array ();
					$wrt_responsive_tabs_msg ['type'] = 'err';
					$wrt_responsive_tabs_msg ['message'] = __('Error while deleting tabs.','wp-best-responsive-tabs');
					update_option ( 'wrt_responsive_tabs_msg', $wrt_responsive_tabs_msg );
				}
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			} else {
				
				echo "<script type='text/javascript'> location.href='$location';</script>";
				exit ();
			}
		} else {
			
			echo "<script type='text/javascript'> location.href='$location';</script>";
			exit ();
		}
	}
}

function wrt_rt_wp_responsive_tabs_preview_func(){

           global $wpdb;
           $query="SELECT * FROM ".$wpdb->prefix."wrt_tabs_settings order by createdon desc";
           $rows=$wpdb->get_results($query,'ARRAY_A');
        
           $tabid=0;
           if(isset($_GET['tabid']) and $_GET['tabid']>0){
              $tabid=(int)(trim($_GET['tabid']));
            }
           
           $query="SELECT * FROM ".$wpdb->prefix."wrt_tabs WHERE gtab_id=$tabid";
           $settings  = $wpdb->get_row($query,ARRAY_A);            
           
           $rand_Numb=uniqid('psc_thumnail_slider');
           $rand_Num_td=uniqid('psc_divSliderMain');
           $rand_var_name=uniqid('rand_');
      
           $location="admin.php?page=rt_wp_responsive_tabs_preview&tabid=";                  
           
           //$wpcurrentdir=dirname(__FILE__);
           //$wpcurrentdir=str_replace("\\","/",$wpcurrentdir);
           //$settings=get_option('thumbnail_slider_settings');    
           
    
                                      
           
     ?>     
       <div style="width: 100%;">  
            <div style="float:left;width:100%;">
                <div class="wrap">
                        <h2><?php __('Tabs Preview','wp-best-responsive-tabs');?></h2>
                <br/>
                <b><?php echo __( 'Select Tab Set','wp-best-responsive-tabs');?>:</b>
                <select name="tabs" id="tabs" onchange="location.href='<?php echo $location;?>'+this.value">
                <option value="" ><?php echo __('Select','wp-best-responsive-tabs');?></option>
                    <?php foreach($rows as $row){?>
                       <option <?php if($tabid==$row['id']){?>selected="selected" <?php } ?>  value="<?php echo $row['id'];?>"><?php echo $row['name'];?></option>
                    <?php }?>
                </select>
                <?php if(is_array($settings)){?>
                <div id="poststuff">
                  <div id="post-body" class="metabox-holder ">
                    
                      <?php echo wrt_print_rt_wp_responsive_tabs_func(array('tabset_id'=>$tabid));?> 
                      
                </div>                                      
                <div class="clear"></div>
                </div>
                <?php if(is_array($settings)){?>

                    <h3><?php echo __( 'To print this tab sets into WordPress Post/Page use below code','wp-best-responsive-tabs');?></h3>
                    <input type="text" value='[wrt_print_rt_wp_responsive_tabs tabset_id="<?php echo $tabid;?>"] ' style="width: 400px;height: 30px" onclick="this.focus();this.select()" />
                    <div class="clear"></div>
                    <h3><?php echo __( 'To print this tab sets into WordPress theme/template PHP files use below code','wp-best-responsive-tabs');?></h3>
                    <?php
                        $shortcode='[wrt_print_rt_wp_responsive_tabs tabset_id="'.$tabid.'"]';
                    ?>
                    <input type="text" value="&lt;?php echo do_shortcode('<?php echo htmlentities($shortcode, ENT_QUOTES); ?>'); ?&gt;" style="width: 400px;height: 30px" onclick="this.focus();this.select()" />

                <?php } ?>
                <div class="clear"></div>
             </div>  
            </div>
       </div>  
    <?php                
      }
    
}
    
function wrt_print_rt_wp_responsive_tabs_func($atts){

        global $wpdb;
        extract(shortcode_atts(array('tabset_id' => 0,), $atts));
        $query="SELECT * FROM ".$wpdb->prefix."wrt_tabs_settings WHERE id=$tabset_id";
        $settings  = $wpdb->get_row($query,ARRAY_A);            
        $rand=uniqid('wrt_');
        $rand2=uniqid('wrt_');
        $default='';
        $loaderImg=plugins_url( 'images/bx_loader.gif', __FILE__ ); 
        $vNonce = wp_create_nonce('vNonce');
        ob_start();
        $flag=false;
        $li_tab_class=uniqid('li_');
        $defaultSelected=null;
        ?>
       <?php if(is_array($settings) and sizeof($settings)>0):?>
            <?php $type=$settings['type'];?>
                
            <?php if($type==2 or $type==5):?>

               <style>

                    .<?php echo $rand;?> .vresp-tabs-list{margin-left: 0px}
                    .<?php echo $rand;?> a{box-shadow:none;border-bottom:none}
                    .<?php echo $rand;?> h2heading.resp-tab-active span.resp-arrow{border-bottom:12px solid <?php echo $settings['tab_a_fcolor'];?>}
                    .<?php echo $rand;?> .vresp-tab-active span.resp-arrow{border-bottom:12px solid <?php echo $settings['tab_a_fcolor'];?>}
                    .<?php echo $rand;?> .resp-arrow{border-top:12px solid <?php echo $settings['tab_fcolor'];?>}
                    .<?php echo $rand;?> .resp-tab-content{color:<?php echo $settings['tab_ccolor'];?>;border:1px solid  <?php echo $settings['ac_border_color'];?>}
                    .<?php echo $rand;?> .vresp-tab-item{color:<?php echo $settings['tab_fcolor'];?>;background-color: <?php echo $settings['inactive_bg'];?>;}
                    .<?php echo $rand;?> .vresp-tab-content{color:<?php echo $settings['tab_ccolor'];?>;border-color: <?php echo $settings['ac_border_color'];?>}
                    .<?php echo $rand;?> .vresp-tabs-container{background-color: <?php echo $settings['activetab_bg'];?>}
                    .<?php echo $rand;?> .vresp-tabs-container h2heading{}
                    .<?php echo $rand;?> .vresp-tab-active{color:<?php echo $settings['tab_fcolor'];?>;border-color:1px solid  <?php echo $settings['ac_border_color'];?>;margin-right:-1px;margin-top:2px; border-right:none !important}
                    .<?php echo $rand;?> .resp-accordion.vresp-tab-active{color:<?php echo $settings['tab_fcolor'];?>;border-color:1px solid  <?php echo $settings['ac_border_color'];?>;margin-right:0px;margin-top:0px; }

                    .<?php echo $rand;?> .vresp-tab-content-active{color:<?php echo $settings['tab_ccolor'];?>;background-color:<?php echo $settings['activetab_bg'];?> }
                    .<?php echo $rand2;?> .resp-accordion{color:<?php echo $settings['tab_fcolor'];?>;}
                    .<?php echo $rand2;?> .resp-accordion.vresp-tab-active{color:<?php echo $settings['tab_fcolor'];?>;border-right:1px solid  <?php echo $settings['ac_border_color'];?> !important }
                     .<?php echo $rand;?> .vresp-tab-item:hover{color:<?php echo $settings['tab_a_fcolor'];?>;background-color: <?php echo $settings['activetab_bg'];?>;border-left:4px solid  <?php echo $settings['ac_border_color'];?> !important ;border-top:1px solid  <?php echo $settings['ac_border_color'];?>;border-bottom:1px solid  <?php echo $settings['ac_border_color'];?>;padding:14px 14px;transition:none}
                     .<?php echo $rand;?> .vresp-tab-active{color:<?php echo $settings['tab_a_fcolor'];?>;background-color: <?php echo $settings['activetab_bg'];?>}
                     
                    @media only screen and (max-width: 768px) {
                        
                        .<?php echo $rand;?>  h2heading{background-color: <?php echo $settings['inactive_bg'];?>}
                        .<?php echo $rand;?> .vresp-tabs-container{background-color:unset }
                        .<?php echo $rand;?> .resp-accordion.vresp-tab-active{
                            
                           background-color: <?php echo $settings['activetab_bg'];?> ;
                            border-top:3px solid <?php echo $settings['ac_border_color'];?>;
                           margin-top: 0px;
                           color:<?php echo $settings['tab_a_fcolor'];?>;
                        }
                    }
                    .<?php echo $rand;?> .resp-accordion.resp-tab-active{
                            
                           background-color: <?php echo $settings['activetab_bg'];?> ;
                           border-top:3px solid <?php echo $settings['ac_border_color'];?>;
                           margin-top: 0px;
                           color:<?php echo $settings['tab_a_fcolor'];?>;
                        }
                        .<?php echo $rand;?> .resp-accordion.resp-tab-active:first-child{
                            
                           border-top:4px solid <?php echo $settings['ac_border_color'];?>;
                         
                        }
                        
                        <?php if($type==5):?>
                            .<?php echo $rand;?> .resp-tab-content-active{

                                background-color: <?php echo $settings['activetab_bg'];?> ;
                            }

                             .<?php echo $rand;?> .resp-accordion{

                                 background-color:<?php echo $settings['inactive_bg'];?>;
                                 color:<?php echo $settings['tab_fcolor'];?>;
                             }
                         <?php endif;?>
                </style>

          
            <?php endif;?>    
       
              <?php
              
                  $query="SELECT * FROM ".$wpdb->prefix."wrt_tabs WHERE gtab_id=$tabset_id order by morder asc, createdon desc";
                  $rows  = $wpdb->get_results($query,ARRAY_A);  
                  
                  $query="SELECT id FROM ".$wpdb->prefix."wrt_tabs WHERE gtab_id=$tabset_id and is_default=1 limit 1";
                  $rw  = $wpdb->get_row($query,ARRAY_A);  
                  if(is_array($rw) and sizeof($rw)>0){
                      
                     $defaultSelected= $rw['id'];
                  }
                  
                ?>
                
            <!-- wrt_print_rt_wp_responsive_tabs_func -->
            <?php if($type==2 or $type==5):?>
                <div id="<?php echo $tabset_id;?>_Tab" class="<?php echo $rand2;?>" >
                    
                     <div id="<?php echo $rand; ?>_overlay" class="overlay_" style="background: #fff url('<?php echo $loaderImg; ?>') no-repeat scroll 50% 50%;" ></div>
                   
                    <div id="<?php echo $rand;?>" class="<?php echo $rand;?>">
                        
                      <ul  class="<?php if($type==2):?>vresp-tabs-list<?php else:?>resp-tabs-list <?php endif;?> hor_<?php echo $rand;?>">
                         <?php foreach($rows as $r):?>    
                              <li data-isajaxloaded="0" data-tabid="<?php echo $r['id'];?>"><?php echo trim(wp_unslash($r['tab_title']));?></li> 
                          <?php endforeach;?>
                      </ul>
                      <div class="<?php if($type==2):?>vresp-tabs-container<?php else:?> resp-tabs-container<?php endif;?> hor_<?php echo $rand;?>">

                          <?php foreach($rows as $r):?>  

                              <div id="tab_<?php echo $rand;?>_<?php echo $r['id'];?>">
                                 <?php echo wp_unslash($r['tab_description']);?>
                              </div>

                          <?php endforeach;?>


                      </div>
              </div>
            </div>         
            <script type="text/javascript">

                var $n = jQuery.noConflict();    
                  $n(document).ready(function () {

                      $n('.<?php echo $rand;?>').easyResponsiveTabs({

                          type: '<?php if($type==1):?>default<?php elseif($type==2):?>vertical<?php elseif($type==5):?>accordion<?php endif;?>', //Types: default, vertical, accordion
                          width: 'auto', //auto or any width like 600px
                          fit: true, // 100% fit in a container
                          closed: 'accordion', // Start closed if in accordion view
                          tabidentify: 'hor_<?php echo $rand;?>', // The tab groups identifier
                          active_border_color: '<?php echo $settings['ac_border_color'];?>',
                          active_content_border_color: '<?php echo $settings['ac_border_color'];?>',
                          activetab_bg: '<?php echo $settings['activetab_bg'];?>',
                          inactive_bg: '<?php echo $settings['inactive_bg'];?>'

                      });

                    <?php if($type==2):?>
                       $n(".<?php echo $rand;?>").find(".vresp-tabs-container").css("minHeight",$n(".<?php echo $rand;?>").find(".vresp-tabs-list").height()+5);
                    <?php endif;?> 
                    
                    <?php if($type==2 or $type==5):?>
                        
                        setTimeout(function(){ 
                        
                        
                         //$n(".<?php echo $rand;?>").find('[data-tabid="<?php echo $defaultSelected;?>"]:first').addClass('resp-tab-active');
                         //$n(".<?php echo $rand;?>").find('[data-tabid="<?php echo $defaultSelected;?>"]:first').next().addClass('resp-tab-content-active');
                         
                         $n(".<?php echo $rand;?>").find('[data-tabid="<?php echo $defaultSelected;?>"]:first').trigger('click');
                         }, 1000);

                       
                        
                        
                    <?php endif;?>    
                  });
                  
                    $n(document).on("click","li.hor_<?php echo $rand;?>,h2heading.hor_<?php echo $rand;?>", function(e){
                        
                            var tabid=$n(this).data("tabid"); 
                            var isajaxloaded=$n(this).data("isajaxloaded"); 
                            var thisele=this;
                          
                            var tabContId="tab_<?php echo $rand;?>_"+tabid;

                              //$n("#"+tabContId).find("script").each(function(){

                                 //eval($n(this).text());
                               //});
                       
                            if(isajaxloaded=="0"){

                                 $n("#<?php echo $rand; ?>_overlay").css("width", $n("#<?php echo $rand; ?>").width());
                                 $n("#<?php echo $rand; ?>_overlay").css("height", $n("#<?php echo $rand; ?>").height());

                                 e.preventDefault();
                                 var data = {
                                         'action': 'rt_get_tab_data_byid',
                                         'tab_id':tabid,
                                         'vNonce':'<?php echo $vNonce;?>'
                                 };

                                 $n.ajax({
                                   type: "POST",
                                   url: "<?php echo admin_url('admin-ajax.php'); ?>",
                                   data: data,
                                   success: function(response){

                                         $n("#"+tabContId).html(response);
                                         //$n("#<?php echo $rand_var_name; ?>").replaceWith(response);
                                         $n("#<?php echo $rand; ?>_overlay").css("width", "0px");
                                         $n("#<?php echo $rand; ?>_overlay").css("height", "0px");

                                         $n(thisele).data("isajaxloaded","1"); 
                                         
                                         if($n(thisele).hasClass('resp-accordion')){
                                            $n('html, body').animate({
                                                  scrollTop: ($n('.hor_<?php echo $rand;?> [data-tabid='+tabid+']').first().offset().top)
                                              },500);
                                            // $n("#"+tabContId).offset().top() ;
                                          }  
                                         
                                   },
                                   error: function(XMLHttpRequest, textStatus, errorThrown) {
                                      $n("#<?php echo $rand; ?>_overlay").css("width", "0px");
                                      $n("#<?php echo $rand; ?>_overlay").css("height", "0px");

                                   }
                                 });
                                 
                                    
                               }else{
                               
                                    if($n(thisele).hasClass('resp-accordion')){
                                        $n('html, body').animate({
                                              scrollTop: ($n('.hor_<?php echo $rand;?> [data-tabid='+tabid+']').first().offset().top)
                                          },500);
                                        // $n("#"+tabContId).offset().top() ;
                                      } 
                               }
                         
                        
                      });   
                      
                   
            </script>
         
           <?php elseif($type==4):?>  
                
                 <style>
                     .<?php echo $rand;?> a{box-shadow:none;border-bottom:none}
                     .<?php echo $rand;?>  .bordered-tab-contents{
                         border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>;
                         border-left: 1px solid <?php echo $settings['ac_border_color'];?>;
                         border-right: 1px solid <?php echo $settings['ac_border_color'];?>;
                         color:<?php echo $settings['tab_ccolor'];?>;
                     }  
                    
                    .<?php echo $rand;?>  .bordered-tab-contents > .tab-content > .tab-pane1 {
                        border-left: 1px solid <?php echo $settings['ac_border_color'];?>;
                        border-right: 1px solid <?php echo $settings['ac_border_color'];?>;
                        border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>;
                        color:<?php echo $settings['tab_ccolor'];?>;
                    }
                    
                   
                    .<?php echo $rand;?> .nav-tabs > li.active > a, .<?php echo $rand;?>  .nav-tabs > li.active > a:hover, .<?php echo $rand;?>  .nav-tabs > li.active > a:focus{
                        
                         border-top: 1px solid <?php echo $settings['ac_border_color'];?>; 
                         border-bottom: 1px solid <?php echo $settings['inactive_bg'];?>; 
                         border-left: 1px solid <?php echo $settings['ac_border_color'];?>; 
                         border-right: 1px solid <?php echo $settings['ac_border_color'];?>; 
                         border-top:3px solid <?php echo $settings['ac_border_color'];?>; 
                         border-bottom-color:transparent;
                         top:1px;
                         padding-left:13px;
                         padding-right:13px;
                         color:<?php echo $settings['tab_a_fcolor'];?>;
                         transition:none;
                    }
                    
                    .<?php echo $rand;?>  .nav-tabs a{color:<?php echo $settings['tab_fcolor'];?>;}
                   .<?php echo $rand;?>  .nav-tabs{color:<?php echo $settings['tab_fcolor'];?>;border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>;background-color:<?php echo $settings['inactive_bg'];?>}
                   .<?php echo $rand;?> .nav-tabs > li > a:hover{color:<?php echo $settings['tab_fcolor'];?>;border-color:none;border-bottom: 0px;transition:none}
                   .<?php echo $rand;?>  .scrtabs-tab-container{color:<?php echo $settings['tab_ccolor'];?>;background-color:<?php echo $settings['inactive_bg'];?>;border:1px solid <?php echo $settings['ac_border_color'];?>;border-top:none;border-right:none;border-left:none}
                   .<?php echo $rand;?>  .nav-tabs > li.active > a, .<?php echo $rand;?>  .nav-tabs > li.active > a:hover, .<?php echo $rand;?>  .nav-tabs > li.active > a:focus{
                   
                     background-color:<?php echo $settings['activetab_bg'];?>  ;
                     color:<?php echo $settings['tab_a_fcolor'];?>;
                     transition:none;
                     
                     
                   }
                   .<?php echo $rand;?> .bordered-tab-contents{
                       
                       background-color:<?php echo $settings['activetab_bg'];?>  ;
                       color:<?php echo $settings['tab_ccolor'];?>;
                           
                   }
                   .<?php echo $rand;?>  .nav-tabs >li a:hover,.<?php echo $rand;?>  .nav-tabs >li.rtdropdown.open a.rtdropdown-toggle{
                       
                        background-color:<?php echo $settings['activetab_bg'];?>;
                        border-border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>;
                        border-bottom-color: <?php echo $settings['inactive_bg'];?>;
                        border-top: 3px solid <?php echo $settings['ac_border_color'];?>;
                        border-bottom-color: transparent;
                        border-left: 1px solid <?php echo $settings['ac_border_color'];?>; 
                        border-right: 1px solid <?php echo $settings['ac_border_color'];?>;     
                        top: 1px;
                        color:<?php echo $settings['tab_a_fcolor'];?>;
                        transition:none
                       
                        
                   }
                  
                   
                   .<?php echo $rand;?>  .nav-tabs >li.LiTab a:hover{
                       
                       padding-left:13px;
                        padding-right:13px;
                        color:<?php echo $settings['tab_a_fcolor'];?>;
                        transition:none;
                   }
                 
                   
                  
              
                   
                   .<?php echo $rand;?> .nav .open > a, .<?php echo $rand;?> .nav .open > a:hover, .<?php echo $rand;?> .nav .open > a:focus{
                       
                        background-color:<?php echo $settings['activetab_bg'];?>;
                        border: 1px solid <?php echo $settings['ac_border_color'];?>; 
                         border-bottom:none;
                         color:<?php echo $settings['tab_a_fcolor'];?>;
                         transition:none;
                   }
                   
                   .<?php echo $rand;?> .rtdropdown-menu > li > a,.<?php echo $rand;?> .rtdropdown-menu > li > a:hover,  .<?php echo $rand;?>  .rtdropdown-menu > li > a:focus{
                       
                     border:none;
                     transition:none;
                     
                     
                   }
                   .<?php echo $rand;?> .nav-tabs > li > a{border-radius: 1px}
                   
                   .<?php echo $rand;?> .nav-tabs > li:first-child.active{top:0px}
                   
                   
                   .<?php echo $rand;?> .nav-tabs > li .rtdropdown-menu li a{
                       
                       background-color:<?php echo $settings['inactive_bg'];?>;
                       border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>; 
                       padding:3px 20px;
                       color:<?php echo $settings['tab_fcolor'];?>;
                       white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
                        white-space: -webkit-pre-wrap; /*Chrome & Safari */ 
                        white-space: -pre-wrap;      /* Opera 4-6 */
                        white-space: -o-pre-wrap;    /* Opera 7 */
                        white-space: pre-wrap;       /* css-3 */
                        word-wrap: break-word;       /* Internet Explorer 5.5+ */
                        white-space: normal;
                        min-width:250px     
                       
                    }
                   .<?php echo $rand;?> .nav-tabs > li .rtdropdown-menu li a:hover,.<?php echo $rand;?> .nav-tabs > li .rtdropdown-menu li.active a{
                       
                       background-color:<?php echo $settings['activetab_bg'];?>;
                       border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>; 
                       padding:3px 20px;
                       color:<?php echo $settings['tab_a_fcolor'];?>;
                       
                    }
                   .<?php echo $rand;?> .nav-tabs > li .rtdropdown-menu {
                       
                       background-color:<?php echo $settings['inactive_bg'];?>;
                       border-top: 1px solid <?php echo $settings['ac_border_color'];?>; 
                       color:<?php echo $settings['tab_fcolor'];?>;
                       
                    }
                    
                   .<?php echo $rand;?> .nav-tabs > li.active .rtdropdown-menu li.active a,.<?php echo $rand;?> .nav-tabs > li.active .rtdropdown-menu li.active a:hover{
                       
                       background-color:<?php echo $settings['activetab_bg'];?>;
                       border-bottom: 1px solid <?php echo $settings['ac_border_color'];?>; 
                       padding:3px 20px;
                       color:<?php echo $settings['tab_a_fcolor'];?>;
                   }
                   
                   .<?php echo $rand;?> .nav-tabs > li .rtdropdown-menu li:hover {
                       
                       color:<?php echo $settings['tab_a_fcolor'];?>;
                   }
                   
                   .<?php echo $rand;?> .nav-tabs > li.active .rtdropdown-menu li.active:first-child a{
                         border-top: 1px solid <?php echo $settings['ac_border_color'];?>; 
                         color:<?php echo $settings['tab_a_fcolor'];?>;
                         transition:none;
                    }
                   
                   .<?php echo $rand;?> .arrowdown{
                       
                      border-color:<?php echo $settings['tab_fcolor'];?>; 
                      cursor:pointer;
                   }
                   
                   .<?php echo $rand;?> .arrowdown:hover{
                       
                      border-color:<?php echo $settings['tab_a_fcolor'];?>;
                      transition:none;
                   }
                   .<?php echo $rand;?> .arrowdown:hover:before{
                       
                      border-color:<?php echo $settings['tab_a_fcolor'];?>;
                      transition:none;
                   }
                   .<?php echo $rand;?> .arrowdown:before{
                       
                      border-color:<?php echo $settings['tab_fcolor'];?>; 
                   }
                   
                    .<?php echo $rand;?> .nav-tabs > li.active .arrowdown{
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                    }
                    .<?php echo $rand;?> .nav-tabs > li.active .arrowdown:before{
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                    }
                    
                    .<?php echo $rand;?> .nav-tabs > li:hover .arrowdown{
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                         transition:none;
                    }
                    .<?php echo $rand;?> .nav-tabs > li:hover .arrowdown:before{
                        
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                         transition:none;
                    }
                    
                    .<?php echo $rand;?> .nav-tabs > li.rtdropdown.open .arrowdown{
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                    }
                    .<?php echo $rand;?> .nav-tabs > li.rtdropdown.open .arrowdown:before{
                         border-color:<?php echo $settings['tab_a_fcolor'];?>;
                    }
                   
                   </style>
                <div class="btabs <?php echo $rand;?>" id="<?php echo $rand;?>">
                    <div id="<?php echo $rand; ?>_overlay" class="overlay_" style="background: #fff url('<?php echo $loaderImg; ?>') no-repeat scroll 50% 50%;" ></div>
                  
                    <div class="tab-main-container">
                    <ul class="nav nav-tabs btab ul<?php echo $rand;?>" role="tablist">
                        
                        <?php foreach($rows as $k=> $r):?>    
                               <?php if($defaultSelected==null){
                                   
                                 $r['is_default']=1;
                                 $rows[$k]=$r;
                                 $defaultSelected=$r['id'];
                               }
                               ?>
                                <?php if($r['is_default']):?>
                                    <?php $default=$rand."_".$r['id'];?>
                                <?php endif;?>
                                <li role="presentation"  data-isajaxloaded="0"  data-tabid="<?php echo $r['id'];?>" <?php if($r['is_default']):?> <?php $flag=true;?> class="<?php echo $rand."_".$r['id'];?> active LiTab <?php echo $li_tab_class;?>" <?php else:?>class="<?php echo $rand."_".$r['id'];?> LiTab <?php echo $li_tab_class;?>" <?php endif;?>><a href="#tab_<?php echo $rand;?>_<?php echo $r['id'];?>"  class="LiTab_Anchor" role="tab" data-toggle="tab" data-tabid="<?php echo $r['id'];?>" ><?php echo trim(wp_unslash($r['tab_title']));?></a></li>
                          <?php endforeach;?>  
                      
                    </ul>

                    <!-- Tab panes -->
                    <div class="bordered-tab-contents">
                            <div class="tab-content">
                                <?php foreach($rows as $r):?>  
                                     <div role="tabpanel" class=" tab-pane <?php if($r['is_default']):?> active <?php endif;?>" id="tab_<?php echo $rand;?>_<?php echo $r['id'];?>" ><?php echo wp_unslash($r['tab_description']);?></div>
                                <?php endforeach;?>
                            </div>
                      </div>
                     </div>
                </div>
              
                <script>
                    
                     var $n = jQuery.noConflict();   
                     var orgUl<?php echo $rand;?>=$n(" #<?php echo $rand;?> .nav-tabs").clone();
                     var activeTab<?php echo $rand;?>='';
                     
                     function changeLiToMenu<?php echo $rand;?>(activeLi){
                         
                         
                        var flag=false;
                        var width=0; 
                        var width_=0; 
                        var menuWidth=50;
                        var mainconWidth=$n(" #<?php echo $rand;?> .tab-main-container").width();
                        
                        $n( "#<?php echo $rand;?> .ul<?php echo $rand;?>" ).html(orgUl<?php echo $rand;?>.html());
                        
                        $n( ".<?php echo $li_tab_class;?>" ).each(function( index ) {
                            
                          width_=width_+$n(this).width();
                          
                        });   
                        
                        if(width_>mainconWidth){
                        
                                    $n( ".<?php echo $li_tab_class;?>" ).each(function( index ) {

                                      width=width+$n(this).width();
                                      if(width+menuWidth>mainconWidth){


                                          $n(this).remove();
                                          if(flag==false){

                                                var rtdropdownMarkup = '<li class="rtdropdown responsivetabs rtdropdown">'
                                              + '<a href="#" class="rtdropdown-toggle" data-toggle="rtdropdown"><span class="arrowdown"></span></a>'
                                              + '<ul class="rtdropdown-menu ';
                                              if(index==0){
                                                  
                                                    rtdropdownMarkup+=' rtdropdown-menu-left rtdropdown-menu<?php echo $rand;?>">'
                                                   + '</ul></li>';
                                                }
                                              else{
                                                  
                                                     rtdropdownMarkup+=' rtdropdown-menu-right rtdropdown-menu<?php echo $rand;?>">'
                                                   + '</ul></li>';
                                              }  
                                              $rtdropdown = $n(rtdropdownMarkup);
                                              $n( "#<?php echo $rand;?> .nav-tabs").append($rtdropdown);
                                              $n(".rtdropdown-menu<?php echo $rand;?>").append($n(this).clone());
                                              flag=true;
                                          }
                                          else{

                                              $n(".rtdropdown-menu<?php echo $rand;?>").append($n(this).clone());
                                          }

                                      }
                                      if(activeLi!=''){

                                        //$n("#<?php echo $rand;?> .LiTab:visible").removeClass('active');
                                        //$n("."+activeLi).addClass('active');
                                        $n("."+activeLi+' a').trigger('click');

                                      }
                                    });   
                         }
                         
                        
                        
                     }
                    $n( window ).load(function() {
                     
                    
                      
                     
                     <?php if($flag==false):?>
                       
                       //$n(".<?php echo $rand;?>").find(".nav-tabs li").first().find(":first-child").trigger('click');
                     <?php endif;?>  
                              
                     changeLiToMenu<?php echo $rand;?>('<?php echo $default;?>');
                     //$n('#<?php echo $rand;?> .nav-tabs').responsiveTabs();
                     //$n(window).trigger('resize');
                        
                    });
                    
                    
                    
                    
                    $n(document).on("click", "#<?php echo $rand;?>  li.<?php echo $li_tab_class;?>", function(e){
                        
                        if(activeTab<?php echo $rand;?>!="" && !$n(this).hasClass(activeTab<?php echo $rand;?>)){
                            $n('.'+activeTab<?php echo $rand;?>).removeClass('active');
                        }
                        activeLi<?php echo $rand;?>=$n(this).prop('classList');
                        classes<?php echo $rand;?> = activeLi<?php echo $rand;?>.toString().split(" ");
                        activeTab<?php echo $rand;?>=classes<?php echo $rand;?>[0];
                        
                       var tabid=$n(this).data("tabid"); 
                       var isajaxloaded=$n(this).data("isajaxloaded"); 
                       var thisele=this;
                       
                       var tabContId="tab_<?php echo $rand;?>_"+tabid;
                       
                         //$n("#"+tabContId).find("script").each(function(){
                           
                            //eval($n(this).text());
                          //});
                       
                       if(isajaxloaded=="0"){
                           
                            $n("#<?php echo $rand; ?>_overlay").css("width", $n("#<?php echo $rand; ?>").width());
                            $n("#<?php echo $rand; ?>_overlay").css("height", $n("#<?php echo $rand; ?>").height());

                            e.preventDefault();
                            var data = {
                                    'action': 'rt_get_tab_data_byid',
                                    'tab_id':tabid,
                                    'vNonce':'<?php echo $vNonce;?>'
                            };

                            $n.ajax({
                              type: "POST",
                              url: "<?php echo admin_url('admin-ajax.php'); ?>",
                              data: data,
                              success: function(response){
                                    
                                    $n("#"+tabContId).html(response);
                                    //$n("#<?php echo $rand_var_name; ?>").replaceWith(response);
                                    $n("#<?php echo $rand; ?>_overlay").css("width", "0px");
                                    $n("#<?php echo $rand; ?>_overlay").css("height", "0px");

                                    $n(thisele).data("isajaxloaded","1"); 
                              },
                              error: function(XMLHttpRequest, textStatus, errorThrown) {
                                 $n("#<?php echo $rand; ?>_overlay").css("width", "0px");
                                 $n("#<?php echo $rand; ?>_overlay").css("height", "0px");

                              }
                            });
                            
                          }
                        
                       
                    });
                    
                    $n(document).on("click", "#<?php echo $rand;?> .responsivetabs ul.rtdropdown-menu<?php echo $rand;?>.rtdropdown-menu-right li.LiTab", function(){
                        
                        
                         
                         $n( "#<?php echo $rand;?> .LiTab" ).each(function( index ) {
                             
                              if(!$n(this).hasClass(activeTab<?php echo $rand;?>)){
                                  $n(this).removeClass('active');
                              }
                         });
                         
                          $n("#<?php echo $rand;?> .responsivetabs").addClass('active');
                        
                    });
                    
                    var timer<?php echo $rand;?>;
                    var width<?php echo $rand;?> = $n(window).width();
                      $n(window).bind('resize', function(){

                       if($n(window).width() != width<?php echo $rand;?>){

                             
                              width<?php echo $rand;?> = $n(window).width();
                              timer<?php echo $rand;?> && clearTimeout(timer<?php echo $rand;?>);
                              timer<?php echo $rand;?> = setTimeout(onResize_<?php echo $rand;?>, 500);

                          }   
                      });
                      
                    function onResize_<?php echo $rand;?>(){
                        
                     
                        
                        
                      changeLiToMenu<?php echo $rand;?>(activeTab<?php echo $rand;?>);
                      
                    }
                </script>
                
          <?php endif;?>      
<!-- end wrt_print_rt_wp_responsive_tabs_func -->
<?php endif;?>
    <?php
        $output = do_shortcode(ob_get_clean());
        return $output;
}
    

function wrt_e_gallery_get_wp_version() {
	global $wp_version;
	return $wp_version;
}

// also we will add an option function that will check for plugin admin page or not
function wrt_responsive_tabs_is_plugin_page() {
	$server_uri = "http://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}";
	
	foreach ( array ('rt_wp_responsive_tabs','rt_wp_responsive_tabs'
	) as $allowURI ) {
		if (stristr ( $server_uri, $allowURI ))
			return true;
	}
	return false;
}

// add media WP scripts
function wrt_wp_responsive_full_tabs_admin_scripts_init() {
    
	if (wrt_responsive_tabs_is_plugin_page ()) {
		// double check for WordPress version and function exists
		if (function_exists ( 'wp_enqueue_media' ) && version_compare ( wrt_e_gallery_get_wp_version (), '3.5', '>=' )) {
			// call for new media manager
                    
			wp_enqueue_media ();
		}
		wp_enqueue_style ( 'media' );
                 wp_enqueue_style( 'wp-color-picker' );
                 wp_enqueue_script( 'wp-color-picker' );
                 
                
	}
}

   function wrt_remove_extra_p_tags($content){

        if(strpos($content, 'wrt_print_rt_wp_responsive_tabs_func')!==false){
        
            
            $pattern = "/<!-- wrt_print_rt_wp_responsive_tabs_func -->(.*)<!-- end wrt_print_rt_wp_responsive_tabs_func -->/Uis"; 
            $content = preg_replace_callback($pattern, function($matches) {


               $altered = str_replace("<p>","",$matches[1]);
               $altered = str_replace("</p>","",$altered);
              
                $altered=str_replace("&#038;","&",$altered);
                $altered=str_replace("&#8221;",'"',$altered);
              

              return @str_replace($matches[1], $altered, $matches[0]);
            }, $content);

              
            
        }
        
        $content = str_replace("<p><!-- wrt_print_rt_wp_responsive_tabs_func -->","<!-- wrt_print_rt_wp_responsive_tabs_func -->",$content);
        $content = str_replace("<!-- end wrt_print_rt_wp_responsive_tabs_func --></p>","<!-- end wrt_print_rt_wp_responsive_tabs_func -->",$content);
        
        
        return $content;
  }

  add_filter('widget_text_content', 'wrt_remove_extra_p_tags', 999);
  add_filter('the_content', 'wrt_remove_extra_p_tags', 999);
?>