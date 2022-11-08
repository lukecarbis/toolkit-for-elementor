<?php 
$ninja = new Toolkit_For_Elementor_Ninja();
$exclude_types = $ninja->exc_post_types;
$post_types = get_post_types(['public'=>true], 'all'); ?>
<div class="single-tab" id="script-ninja-tab">
    <div class="minification-setting-section">
        <div class="widget-panel-title"><?php _e('Script Ninja'); ?></div>
	<p class="m-0"><?php _e('Script Ninja helps dequeue unneeded CSS & JS scripts on a per page/post basis. Simply select a page to view all of the loaded scripts, and dequeue any scripts that you aren\'t using on that page. | <a href="https://toolkitforelementor.com/topics/booster/script-ninja/">Learn More</a>'); ?></p><br />
        <div id="post-types-tabs" class="normal-inner-tabs">
            <div class="inner-tabs-headings">
                <?php if( $post_types ){
                    $active_class = 'active-heading';
                    foreach ($post_types as $post_type) {
                        if( ! in_array($post_type->name, $exclude_types) ){
                            echo '<div id="tabheading-'.$post_type->name.'" data-ptype="'.$post_type->name.'" class="inner-tab-heading '.$active_class.'">';
                            echo $post_type->label.'</div>';
                            $active_class = '';
                        }
                    }
                } ?>
            </div>
            <div class="inner-tabs-contents">
                <?php if( $post_types ){
                    $active_class = 'active-content';
                    foreach ($post_types as $post_type) {
                        if( ! in_array($post_type->name, $exclude_types) ){
                            $posts = get_posts(
                                array(
                                    'post_type'     => $post_type->name,
                                    'posts_per_page'=> -1
                                )
                            );
                            echo '<div id="tabcontent-'.$post_type->name.'" class="inner-tab-content '.$active_class.'">'; ?>
                            <div class="widget-panel-title"><?php echo $post_type->label; ?></div>
                            <div class="controls-section">
                                <?php if( $posts ){ ?>
                                    <div class="assets-controls">
                                        <select class="post-type-script text-field">
                                            <option value=""><?php _e("Select Post"); ?></option>
                                            <?php foreach ($posts as $post){
                                                echo "<option value='".$post->ID."'>".$post->post_title."</option>";
                                            } ?>
                                        </select>
                                        <button type="button" class="button toolkit-btn show-ninja-scripts"><?php _e('Display Scripts List'); ?></button>
                                    </div>
                                    <div class="display-assets-list"></div>
                                <?php } else { ?>
                                    <div class="info"><?php _e("No posts available."); ?></div>
                                <?php } ?>
                            </div>
                            <?php echo '</div>';
                            $active_class = '';
                        }
                    }
                } ?>
            </div>
        </div>
    </div>
</div>
