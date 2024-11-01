<?php
/**
 * Plugin Name: Total Control HTML5 Audio Player Basic
 * Description: jQuery plugin for streaming audio with a manageable playlist
 * Version: 1.1
 * Author: George Holmes II
 * Author URI: http://georgeholmesii.com
 
License: GPLv2 or later

//  Copyright 2012  George Holmes II  (email : georgeholmesii@gmail.com)

/*
This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

if(isset($_POST['add-playlist-name'])) {
	$playlistName = $_POST['add-playlist-name'];
	
	global $wpdb;
	$table_name = $wpdb->prefix . "total_playlists";
	$wpdb->insert($table_name, array('playlist_name' => $playlistName));
	
	$playlists = $wpdb->get_results( "SELECT * FROM " . $table_name . " ORDER BY playlist_id DESC LIMIT 1");
    
	foreach ( $playlists as $playlist )
	{   
			/* set thumbnail variables */
		echo $playlist->playlist_id;
	}
	exit();
	
}

if(isset($_POST['delete-playlist-id'])) {
	global $wpdb;
	$table_name = $wpdb->prefix . "total_playlists";
	$playlistId = $_POST['delete-playlist-id'];
	
	$wpdb->query($wpdb->prepare("DELETE FROM " . $table_name . " WHERE playlist_id = " . $playlistId));
	
	echo "success";
	exit();
	
}

if(isset($_POST['load-playlist-id'])) {
	global $wpdb;
	$table_name = $wpdb->prefix . "total_songs";
	$playlistId = $_POST['load-playlist-id'];
	
	$songs = $wpdb->get_results( "SELECT * FROM " . $table_name . " WHERE playlist = " . $playlistId);
    
	foreach ( $songs as $song )
	{   

		echo '<div class="total-song-row"><div class="total-artist-field" align="center"><input class="total-input" type="text" name="total-artist[]" value="' . $song->artist . '"/></div><div class="total-title-field" align="center"><input class="total-input" type="text" name="total-title[]" value="' . $song->title . '" /></div><div class="total-ogg-field" align="center"><input class="total-input" type="text" name="total-ogg[]" value="' . $song->ogg . '" /></div><div class="total-mp3-field" align="center"><input class="total-input" type="text" name="total-mp3[]" value="' . $song->mp3 . '"/></div><div class="total-artwork-field" align="center"><input class="total-input" type="text" name="total-artwork[]" value="' . $song->artwork . '"/></div><input type="hidden" name="total-song-id[]" value="' . $song->song_id . '"/><div style="clear:both;"></div></div>';
	}
	
	echo '<input type="hidden" name="save-playlist-id" id="save-playlist-id" value="' . $playlistId . '"/>';
	exit();
	
}

if(isset($_POST['save-playlist-id'])) {
	
	global $wpdb;
	$table_name = $wpdb->prefix . "total_songs";
	$artists = $_POST['total-artist'];
	$titles = $_POST['total-title'];
	$oggs = $_POST['total-ogg'];
	$mp3s = $_POST['total-mp3'];
	$artworks = $_POST['total-artwork'];
	
	$wpdb->query($wpdb->prepare("DELETE FROM " . $table_name . " WHERE playlist = " . $_POST['save-playlist-id']));
	
	for ($i=0; $i < count($_POST['total-artist']); $i++) {
		
		$wpdb->insert($table_name, array('artist' => $_POST['total-artist'][$i],'title' => $_POST['total-title'][$i],'ogg' => $_POST['total-ogg'][$i],'mp3' => $_POST['total-mp3'][$i],'artwork' => $_POST['total-artwork'][$i],'playlist' => $_POST['save-playlist-id']));
	}
	exit();
	
}


add_action( 'widgets_init', 'total_control_widget' );


function total_control_widget() {
	register_widget( 'Total_Control_Widget' );
}

class Total_Control_Widget extends WP_Widget {

	function Total_Control_Widget() {
		$widget_ops = array( 'classname' => 'example', 'description' => __('jQuery plugin for streaming audio with a manageable playlist ', 'example') );
		
		$control_ops = array( 'width' => 300, 'height' => 350, 'id_base' => 'total-control-widget' );
		
		$this->WP_Widget( 'total-control-widget', __('Total Control HTML5 Audio Player Widget', 'example'), $widget_ops, $control_ops );
	}
	
	function widget( $args, $instance ) {
		extract( $args );

		//Our variables from the widget settings.
		$title = apply_filters('widget_title', $instance['title'] );
		//echo '<h2>something</h2>';
		?>
        <link type="text/css" rel="stylesheet" href="<?php echo plugins_url('/css/default.css', __FILE__); ?>" />
        <script>
		 soundManager.url = "<?php echo plugins_url('/javascripts/soundmanager/swf', __FILE__); ?>";
		 var $total = jQuery.noConflict();
		$total().ready(function() {
			
			//$("body").hide();
			
			$total("#total-playlist").totalControl({
				checkboxesEnabled: <?php echo $instance['checkboxesEnabled']; ?>,
				playlistSortable: <?php echo $instance['playlistSortable']; ?>, 
				playlistHeight:<?php echo $instance['totalPlaylistHeight']; ?>,
				repeatOneEnabled: <?php echo $instance['repeatOneEnabled']; ?>,
				repeatAllEnabled: <?php echo $instance['repeatAllEnabled']; ?>,
				playlistVisible: <?php echo $instance['playlistVisible']; ?>,
				autoplayEnabled: <?php echo $instance['autoplayEnabled']; ?>
				
			});
			$total(".total-playing-title, .total-playing-artist").css("line-height", "10px");
			$total("#total-playlist").css("z-index", "3000");
			
			
			
			
		});
		</script>
        <ul id="total-playlist" style="margin-left:auto; margin-right:auto;">
            <li mp3="<?php echo get_option('total_mp3_1'); ?>" ogg="<?php echo get_option('total_ogg_1'); ?>" artist="<?php echo get_option('total_artist_1'); ?>" title="<?php echo get_option('total_title_1'); ?>"></li>
            <li mp3="<?php echo get_option('total_mp3_2'); ?>" ogg="<?php echo get_option('total_ogg_2'); ?>" artist="<?php echo get_option('total_artist_2'); ?>" title="<?php echo get_option('total_title_2'); ?>"></li>
            <li mp3="<?php echo get_option('total_mp3_3'); ?>" ogg="<?php echo get_option('total_ogg_3'); ?>" artist="<?php echo get_option('total_artist_3'); ?>" title="<?php echo get_option('total_title_3'); ?>"></li>
            <li mp3="<?php echo get_option('total_mp3_4'); ?>" ogg="<?php echo get_option('total_ogg_4'); ?>" artist="<?php echo get_option('total_artist_4'); ?>" title="<?php echo get_option('total_title_4'); ?>"></li>
            <li mp3="<?php echo get_option('total_mp3_5'); ?>" ogg="<?php echo get_option('total_ogg_5'); ?>" artist="<?php echo get_option('total_artist_5'); ?>" title="<?php echo get_option('total_title_5'); ?>"></li>
         
		
        </ul>
        
        <?php
		
	}

	//Update the widget 
	 
	function update( $new_instance, $old_instance ) {
		$instance = $old_instance;

		//Strip tags from title and name to remove HTML 
		$instance['checkboxesEnabled'] = strip_tags( $new_instance['checkboxesEnabled'] );
		$instance['playlistSortable'] = strip_tags( $new_instance['playlistSortable'] );
		$instance['totalPlaylistHeight'] = strip_tags( $new_instance['totalPlaylistHeight'] );
		$instance['repeatOneEnabled'] = strip_tags( $new_instance['repeatOneEnabled'] );
		$instance['repeatAllEnabled'] = strip_tags( $new_instance['repeatAllEnabled'] );
		$instance['shuffleEnabled'] = strip_tags( $new_instance['shuffleEnabled'] );
		$instance['playlistVisible'] = strip_tags( $new_instance['playlistVisible'] );
		$instance['autoplayEnabled'] = strip_tags( $new_instance['autoplayEnabled'] );
		

		return $instance;
	}
	

	
	function form( $instance ) {

		//Set up some default widget settings.
		$defaults = array( 
			'checkboxesEnabled' => __('true', 'true'),
			'playlistSortable' => __('true', 'true'),
			'totalPlaylistHeight' => __('165', '165'),
			'repeatOneEnabled' => __('true', 'true'),
			'repeatAllEnabled' => __('true', 'true'),
			'playlistVisible' => __('true', 'true'),
			'autoplayEnabled' => __('false', 'false'),
			'show_info' => true );
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>


<p>
    <label for="<?php echo $this->get_field_id( 'checkboxesEnabled' ); ?>">
        <?php _e('Checkboxes Enabled', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('checkboxesEnabled'); ?>" name="<?php echo $this->get_field_name('checkboxesEnabled'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['checkboxesEnabled'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['checkboxesEnabled'], 'false'); ?> value="false">false</option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'playlistSortable' ); ?>">
        <?php _e('Playlist Sortable', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('playlistSortable'); ?>" name="<?php echo $this->get_field_name('playlistSortable'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['playlistSortable'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['playlistSortable'], 'false'); ?> value="false">false</option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'totalPlaylistHeight' ); ?>">
        <?php _e('Playlist Height', 'example'); ?>
    </label>
    <input id="<?php echo $this->get_field_id('totalPlaylistHeight'); ?>" type="text" name="<?php echo $this->get_field_name('totalPlaylistHeight'); ?>" value="<?php echo $instance['totalPlaylistHeight']; ?>" class="widefat" style="width:100%;"/>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'repeatOneEnabled' ); ?>">
        <?php _e('Repeat One Enabled', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('repeatOneEnabled'); ?>" name="<?php echo $this->get_field_name('repeatOneEnabled'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['repeatOneEnabled'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['repeatOneEnabled'], 'false'); ?> value="false">false</option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'repeatAllEnabled' ); ?>">
        <?php _e('Repeat All Enabled', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('repeatAllEnabled'); ?>" name="<?php echo $this->get_field_name('repeatAllEnabled'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['repeatAllEnabled'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['repeatAllEnabled'], 'false'); ?> value="false">false</option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'playlistVisible' ); ?>">
        <?php _e('Playlist Visible', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('playlistVisible'); ?>" name="<?php echo $this->get_field_name('playlistVisible'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['playlistVisible'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['playlistVisible'], 'false'); ?> value="false">false</option>
    </select>
</p>

<p>
    <label for="<?php echo $this->get_field_id( 'autoplayEnabled' ); ?>">
        <?php _e('Autoplay Enabled', 'example'); ?>
    </label>
    <select id="<?php echo $this->get_field_id('autoplayEnabled'); ?>" name="<?php echo $this->get_field_name('autoplayEnabled'); ?>" class="widefat" style="width:100%;">
		<option <?php selected( $instance['autoplayEnabled'], 'true'); ?> value="true">true</option>
        <option <?php selected( $instance['autoplayEnabled'], 'false'); ?> value="false">false</option>
    </select>
</p>


<?php
	}
}

function total_register_settings () {
	register_setting( 'total_settings_group', 'total_artist_1' );
	register_setting( 'total_settings_group', 'total_title_1' );
	register_setting( 'total_settings_group', 'total_ogg_1' );
	register_setting( 'total_settings_group', 'total_mp3_1' );
	
	register_setting( 'total_settings_group', 'total_artist_2' );
	register_setting( 'total_settings_group', 'total_title_2' );
	register_setting( 'total_settings_group', 'total_ogg_2' );
	register_setting( 'total_settings_group', 'total_mp3_2' );
	
	register_setting( 'total_settings_group', 'total_artist_3' );
	register_setting( 'total_settings_group', 'total_title_3' );
	register_setting( 'total_settings_group', 'total_ogg_3' );
	register_setting( 'total_settings_group', 'total_mp3_3' );
	
	register_setting( 'total_settings_group', 'total_artist_4' );
	register_setting( 'total_settings_group', 'total_title_4' );
	register_setting( 'total_settings_group', 'total_ogg_4' );
	register_setting( 'total_settings_group', 'total_mp3_4' );
	
	register_setting( 'total_settings_group', 'total_artist_5' );
	register_setting( 'total_settings_group', 'total_title_5' );
	register_setting( 'total_settings_group', 'total_ogg_4' );
	register_setting( 'total_settings_group', 'total_mp3_5' );
	
}

function total_control_panel () {
?>
<link href="http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css" rel="stylesheet" type="text/css"/>
<link href="<?php echo plugins_url(); ?>/total-control-html5-audio-player-basic/css/admin-style.css" type="text/css" rel="stylesheet" />
<script src="http://code.jquery.com/jquery-1.6.4.min.js"></script> 
<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script> 

<script type="text/javascript">
	$("document").ready(function() {
		
				
		
		
		$("#total-playlist-songs").sortable();
		
		
		
		$("#total-delete-song").click(function () {
			$(".total-song-row-selected").remove();
		});
		$('.total-input').live("click", function(e) {
 			 e.stopPropagation();
			 if(e.target != $(this)){
				return true;
			 }
		});
				
		
		
		$(".total-save-button").live("click", function () {
			if($(".total-playlist-row-selected").length > 0 && $(".total-song-row").length > 0)
			{
				$.ajax({
					url: "<?php $_SERVER['PHP_SELF']; ?>",
					dataType: "text",
					data: $("#total-playlist-songs").serializeArray(),
					timeout: 5000,
					type: "POST",
					beforeSend: function( xhr ) {
						
					},
					error: function (jqXHR, textStatus, errorThrown) {
					
						alert("Could not connect to server. Please try again.");
				
					},
					success: function(data) {
						
					}
				
				});	

			}
		});
	
	});
	
	</script>
    
    <p><a target="_blank" href="http://wordpress.org/extend/plugins/total-control-html5-audio-player-basic/"><h3>If you like this plugin please take the time to rate it HERE</h3></a></p>
    <h4>If you are looking for a more advanced version of this plugin with more features such as shuffle playlist, unlimited songs, more skins, show song artwork, dynamic player positioning, and adding songs on the fly take a look at the demo by clicking the button below.</h4>
    <a target="_blank" href="http://georgeholmesii.com/total-control/example-1.php"><div class="total-save-button">View Demo</div></a>
   <hr>

<div class="total-title"><img src="<?php echo plugins_url(); ?>/total-control-html5-audio-player-basic/images/total-icon-large.png" width="30"/>&nbsp;Total Control Playlist Manager</div>
<div class="total-container">
    <div class="total-playlist-songs-container">
        <div class="total-column-title">Songs</div>
        <div class="total-songs-labels">
            <div class="total-artist-column">Artist</div>
            <div class="total-title-column">Song Title</div>
            <div class="total-ogg-column">OGG URL</div>
            <div class="total-mp3-column">MP3 URL</div>
            <div style='clear:both;'></div>
        </div>
        
        
        <form class="total-playlist-songs" id="total-playlist-songs" method="post" action="options.php">
        	<?php settings_fields( 'total_settings_group' ); ?>
    		<?php //do_settings( 'total_settings_group' ); ?>
        	<div class="total-song-row">
            	<div class="total-artist-field" align="center">
                	<input class="total-input" type="text" name="total_artist_1" value="<?php echo get_option('total_artist_1'); ?>"/>
                </div>
                <div class="total-title-field" align="center">
                	<input class="total-input" type="text" name="total_title_1" value="<?php echo get_option('total_title_1'); ?>"/>
                </div><div class="total-ogg-field" align="center">
                	<input class="total-input" type="text" name="total_ogg_1" value="<?php echo get_option('total_ogg_1'); ?>"/>
                </div>
                <div class="total-mp3-field" align="center">
                	<input class="total-input" type="text" name="total_mp3_1" value="<?php echo get_option('total_mp3_1'); ?>"/>
                </div>
                <div style="clear:both;">
                </div>
           </div>
           
           <div class="total-song-row">
            	<div class="total-artist-field" align="center">
                	<input class="total-input" type="text" name="total_artist_2" value="<?php echo get_option('total_artist_2'); ?>"/>
                </div>
                <div class="total-title-field" align="center">
                	<input class="total-input" type="text" name="total_title_2" value="<?php echo get_option('total_title_2'); ?>"/>
                </div><div class="total-ogg-field" align="center">
                	<input class="total-input" type="text" name="total_ogg_2" value="<?php echo get_option('total_ogg_2'); ?>"/>
                </div>
                <div class="total-mp3-field" align="center">
                	<input class="total-input" type="text" name="total_mp3_2" value="<?php echo get_option('total_mp3_2'); ?>"/>
                </div>
                <div style="clear:both;">
                </div>
           </div>
           
           <div class="total-song-row">
            	<div class="total-artist-field" align="center">
                	<input class="total-input" type="text" name="total_artist_3" value="<?php echo get_option('total_artist_3'); ?>"/>
                </div>
                <div class="total-title-field" align="center">
                	<input class="total-input" type="text" name="total_title_3" value="<?php echo get_option('total_title_3'); ?>"/>
                </div><div class="total-ogg-field" align="center">
                	<input class="total-input" type="text" name="total_ogg_3" value="<?php echo get_option('total_ogg_3'); ?>"/>
                </div>
                <div class="total-mp3-field" align="center">
                	<input class="total-input" type="text" name="total_mp3_3" value="<?php echo get_option('total_mp3_3'); ?>"/>
                </div>
                <div style="clear:both;">
                </div>
           </div>
           
           <div class="total-song-row">
            	<div class="total-artist-field" align="center">
                	<input class="total-input" type="text" name="total_artist_4" value="<?php echo get_option('total_artist_4'); ?>"/>
                </div>
                <div class="total-title-field" align="center">
                	<input class="total-input" type="text" name="total_title_4" value="<?php echo get_option('total_title_4'); ?>"/>
                </div><div class="total-ogg-field" align="center">
                	<input class="total-input" type="text" name="total_ogg_4" value="<?php echo get_option('total_ogg_4'); ?>"/>
                </div>
                <div class="total-mp3-field" align="center">
                	<input class="total-input" type="text" name="total_mp3_4" value="<?php echo get_option('total_mp3_4'); ?>"/>
                </div>
                <div style="clear:both;">
                </div>
           </div>
           
           <div class="total-song-row">
            	<div class="total-artist-field" align="center">
                	<input class="total-input" type="text" name="total_artist_5" value="<?php echo get_option('total_artist_5'); ?>"/>
                </div>
                <div class="total-title-field" align="center">
                	<input class="total-input" type="text" name="total_title_5" value="<?php echo get_option('total_title_5'); ?>"/>
                </div><div class="total-ogg-field" align="center">
                	<input class="total-input" type="text" name="total_ogg_5" value="<?php echo get_option('total_ogg_5'); ?>"/>
                </div>
                <div class="total-mp3-field" align="center">
                	<input class="total-input" type="text" name="total_mp3_5" value="<?php echo get_option('total_mp3_5'); ?>"/>
                </div>
                <div style="clear:both;">
                </div>
           </div>
        	<p class="submit">
    			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    		</p>
           
        </form>
    </div>
    <div style='clear:both;'></div>
</div>

<?
}

function total_control_menu () {
		add_menu_page('Total Control HTML5 Audio Player', 'Total Control HTML5 Audio Player', 'manage_options', __FILE__,'total_control_panel', plugins_url() .'/total-control-html5-audio-player-basic/images/total-icon.png');
		add_submenu_page(__FILE__, 'Playlist Manager','Playlist Manager','manage_options',__FILE__,'total_control_panel');
		
		

}

add_action('admin_menu', 'total_control_menu');
add_action( 'admin_init', 'total_register_settings' );
function total_control_activate()
{
	
	
	
}

function total_control_deactivate()
{
	
	
}

register_activation_hook(__FILE__, 'total_control_activate');
register_deactivation_hook(__FILE__, 'total_control_deactivate');

function my_scripts_method() {
	wp_register_style( 'jquery-ui-stylesheet', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8/themes/base/jquery-ui.css' );
     wp_enqueue_style( 'jquery-ui-stylesheet' );
	 
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js');
    wp_enqueue_script( 'jquery' );
	
	
	wp_register_script( 'jquery-ui-base', 'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js');
    wp_enqueue_script( 'jquery-ui-base' );
	
	wp_register_script( 'total-soundmanager', plugins_url('/javascripts/soundmanager/script/soundmanager2.js', __FILE__) );
    wp_enqueue_script( 'total-soundmanager' );
	
	wp_register_script( 'total-mousewheel', plugins_url('/javascripts/jscrollpane/script/jquery.mousewheel.js', __FILE__) );
    wp_enqueue_script( 'total-mousewheel' );
	
	wp_register_script( 'total-jscrollpane', plugins_url('/javascripts/jscrollpane/script/jquery.jscrollpane.js', __FILE__) );
    wp_enqueue_script( 'total-jscrollpane' );
	
	wp_register_script( 'total-control-script', plugins_url('/TotalControl.js', __FILE__) );
    wp_enqueue_script( 'total-control-script' );
	
	 wp_register_style( 'jscrollpane-stylesheet', plugins_url('/javascripts/jscrollpane/style/jquery.jscrollpane.css', __FILE__) );
     wp_enqueue_style( 'jscrollpane-stylesheet' );
}    
 
add_action('wp_enqueue_scripts', 'my_scripts_method');

?>