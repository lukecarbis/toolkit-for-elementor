<?php
if( ! class_exists('Toolkit_For_Elementor_DbOpt') ){
    class Toolkit_For_Elementor_DbOpt {

        public $cron_hook = 'toolkit_purge_db_optimization';

        function __construct(){

        }

        function save_settings(){
            if( isset($_POST['dbauto_clean']) && isset($_POST['posts_revs']) ){
                $options = array(
                    'posts_revs'    => esc_sql($_POST['posts_revs']),
                    'auto_drafts'   => esc_sql($_POST['auto_drafts']),
                    'trash_posts'   => esc_sql($_POST['trash_posts']),
                    'spam_comments' => esc_sql($_POST['spam_comments']),
                    'trash_comments'=> esc_sql($_POST['trash_comments']),
                    'exp_transients'=> esc_sql($_POST['exp_transients']),
                    'all_transients'=> esc_sql($_POST['all_transients']),
                    'opt_tables'    => esc_sql($_POST['opt_tables']),
                    'dbauto_clean'  => esc_sql($_POST['dbauto_clean'])
                );
                update_option('toolkit_elementor_dbopt_settings', $options);
                $this->setup_lifespan($_POST);
                $this->clean_database();
                $response = array('success'=>true, 'message'=>__("Database cleaned successfully!", "toolkit-for-elementor"));
            } else {
                $response = array('success'=>false, 'message'=>__("Error: Unfortunately we were unable to clean your database.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        function setup_lifespan($options){
            if ($options['dbauto_clean'] === 'never') {
                wp_clear_scheduled_hook($this->cron_hook);
            } elseif ( ! wp_next_scheduled($this->cron_hook) ) {
                wp_schedule_event(time(), $options['dbauto_clean'], $this->cron_hook);
            } else {
                wp_clear_scheduled_hook($this->cron_hook);
                wp_schedule_event(time(), $options['dbauto_clean'], $this->cron_hook);
            }
        }

        function cron_schedules($schedules){
            $schedules['monthly'] = array(
                'interval' => MONTH_IN_SECONDS,
                'display'  => __( 'Monthly' )
            );
            return $schedules;
        }

        public function get_stats(){
            global $wpdb;
            $stats = [];
            $stats['posts_revs'] = $wpdb->get_var(
                "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_type = 'revision'"
            );

            $stats['auto_drafts'] = $wpdb->get_var(
                "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'auto-draft'"
            );

            $stats['trash_posts'] = $wpdb->get_var(
                "SELECT COUNT(ID) FROM $wpdb->posts WHERE post_status = 'trash'"
            );

            $stats['spam_comments'] = $wpdb->get_var(
                "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_approved = 'spam'"
            );

            $stats['trash_comments'] = $wpdb->get_var(
                "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE (comment_approved = 'trash' OR comment_approved = 'post-trashed')"
            );

            $time = isset($_SERVER['REQUEST_TIME']) ? (int) $_SERVER['REQUEST_TIME'] : time();
            $stats['exp_transients'] = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(option_name) FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d",
                    $wpdb->esc_like('_transient_timeout') . '%',
                    $time
                )
            );

            $stats['all_transients'] = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT COUNT(option_id) FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s",
                    $wpdb->esc_like('_transient_') . '%',
                    $wpdb->esc_like('_site_transient_') . '%'
                )
            );

            $stats['opt_tables'] = $wpdb->get_var(
                "SELECT COUNT(table_name) FROM information_schema.tables WHERE table_schema = '" .
                DB_NAME .
                "' and Engine <> 'InnoDB' and data_free > 0"
            );

            return $stats;
        }

        public function clean_database(){
            global $wpdb;
            $dbOpt = get_option('toolkit_elementor_dbopt_settings', array());

            if ( isset($dbOpt['posts_revs']) && $dbOpt['posts_revs'] == 'on' ) {
                $query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_type = 'revision'");
                if ($query) {
                    foreach ($query as $id) {
                        wp_delete_post_revision(intval($id)) instanceof \WP_Post ? 1 : 0;
                    }
                }
            }

            if ( isset($dbOpt['auto_drafts']) && $dbOpt['auto_drafts'] == 'on' ) {
                $query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'auto-draft'");
                if ($query) {
                    foreach ($query as $id) {
                        wp_delete_post(intval($id), true) instanceof \WP_Post ? 1 : 0;
                    }
                }
            }

            if ( isset($dbOpt['trash_posts']) && $dbOpt['trash_posts'] == 'on' ) {
                $query = $wpdb->get_col("SELECT ID FROM $wpdb->posts WHERE post_status = 'trash'");
                if ($query) {
                    foreach ($query as $id) {
                        wp_delete_post($id, true) instanceof \WP_Post ? 1 : 0;
                    }
                }
            }

            if ( isset($dbOpt['spam_comments']) && $dbOpt['spam_comments'] == 'on' ) {
                $query = $wpdb->get_col(
                    "SELECT comment_ID FROM $wpdb->comments WHERE comment_approved = 'spam'"
                );
                if ($query) {
                    foreach ($query as $id) {
                        wp_delete_comment(intval($id), true);
                    }
                }
            }

            if ( isset($dbOpt['trash_comments']) && $dbOpt['trash_comments'] == 'on' ) {
                $query = $wpdb->get_col(
                    "SELECT comment_ID FROM $wpdb->comments WHERE (comment_approved = 'trash' OR comment_approved = 'post-trashed')"
                );
                if ($query) {
                    foreach ($query as $id) {
                        wp_delete_comment(intval($id), true);
                    }
                }
            }

            if ( isset($dbOpt['exp_transients']) && $dbOpt['exp_transients'] == 'on' ) {
                $time = isset($_SERVER['REQUEST_TIME']) ? (int) $_SERVER['REQUEST_TIME'] : time();
                $query = $wpdb->get_col(
                    $wpdb->prepare(
                        "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s AND option_value < %d",
                        $wpdb->esc_like('_transient_timeout') . '%',
                        $time
                    )
                );
                if ($query) {
                    foreach ($query as $transient) {
                        $key = str_replace('_transient_timeout_', '', $transient);
                        delete_transient($key);
                    }
                }
            }

            if ( isset($dbOpt['all_transients']) && $dbOpt['all_transients'] == 'on' ) {
                $query = $wpdb->get_col(
                    $wpdb->prepare(
                        "SELECT option_name FROM $wpdb->options WHERE option_name LIKE %s OR option_name LIKE %s",
                        $wpdb->esc_like('_transient_') . '%',
                        $wpdb->esc_like('_site_transient_') . '%'
                    )
                );
                if ($query) {
                    foreach ($query as $transient) {
                        if (strpos($transient, '_site_transient_') !== false) {
                            delete_site_transient(str_replace('_site_transient_', '', $transient));
                        } else {
                            delete_transient(str_replace('_transient_', '', $transient));
                        }
                    }
                }
            }

            if ( isset($dbOpt['opt_tables']) && $dbOpt['opt_tables'] == 'on' ) {
                $query = $wpdb->get_results(
                    "SELECT table_name, data_free FROM information_schema.tables WHERE table_schema = '" .
                    DB_NAME .
                    "' and Engine <> 'InnoDB' and data_free > 0"
                );
                if ($query) {
                    foreach ($query as $table) {
                        $wpdb->query("OPTIMIZE TABLE $table->table_name");
                    }
                }
            }
        }

    }
}
