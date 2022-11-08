<?php

if (!class_exists('Toolkit_Elementor_Syncer')):
    class Toolkit_Elementor_Syncer extends \Elementor\TemplateLibrary\Source_Local
    {
        // The auth class
        private $auth = null;
        private $user_id = 0;
        private $endpoint = '/wp-json/toolkit/v1/syncer';

        /**
         * Constructor
         *
         */
        public function __construct()
        {
            // Get auth
            $this->auth = new \Toolkit_Elementor_Syncer_Auth();
            $this->user_id = get_current_user_id();

            // Register the api etc
            add_action('rest_api_init', [$this, 'register_rest_api']);

            add_action('admin_post_toolkit_generate_syncer_key', [$this, 'generate_syncer_key']);
            add_action('admin_post_toolkit_delete_syncer_key', [$this, 'delete_syncer_key']);
            add_action('admin_post_toolkit_update_syncer_key', [$this, 'update_syncer_key']);
            add_action('admin_post_toolkit_remote_templates', [$this, 'post_get_remote_templates']);
            add_action('admin_post_toolkit_remote_template', [$this, 'post_get_remote_template']);
            add_action('wp_ajax_toolkit_remove_syncer_key', [$this, 'remove_syncer_key']);
            add_action('wp_ajax_toolkit_bookmark_syncer_key', [$this, 'bookmark_syncer_key']);
        }

        /**
         * Register rest api
         *
         */
        public function register_rest_api()
        {
            // Get templates - this is for remote to call
            register_rest_route('toolkit/v1', '/syncer/templates', [
                'methods' => 'GET',
                'callback' => [$this, 'get_templates'],
                'permission_callback' => [$this->auth, 'check_auth_key'],
            ]);

            // Get template - this is for remote to call
            register_rest_route('toolkit/v1', '/syncer/template/(?P<id>\d+)', [
                'methods' => 'GET',
                'callback' => [$this, 'get_template'],
                'permission_callback' => [$this->auth, 'check_auth_key'],
            ]);
        }

        /*
         * Check if the current user have permission to make api calls
         *
         */
        public function has_permission()
        {
            return current_user_can('manage_options');
        }

        public function generate_syncer_key()
        {
            if (!$this->has_permission()) {
                wp_send_json_error('Sorry, your user role does not have permission to update this.', 400);
                return;
            }

            $result = $this->auth->generate_syncer_key();
            wp_send_json($result);
        }

        public function delete_syncer_key(){
            if (!$this->has_permission()) {
                wp_send_json_error('Sorry, your user role does not have permission to update this.', 400);
                return;
            }
            $keys = $this->auth->get_syncer_keys();
            $rem_key = $_REQUEST['rem_key'];
            if( $keys ){
                $new_keys = array();
                foreach ($keys as $index => $key){
                    if( $key[0] != $rem_key ){
                        $new_keys[] = $key;
                    }
                }
                update_option('_toolkit_syncer_key', $new_keys);
            }
            wp_send_json(array('success'=>true, 'message'=>__('Key has removed successfully!')));
        }

        public function update_syncer_key(){
            if (!$this->has_permission()) {
                wp_send_json_error('Sorry, your user role does not have permission to update this.', 400);
                return;
            }
            $keys = $this->auth->get_syncer_keys();
            $rem_key = $_REQUEST['rem_key'];
            $notes = $_REQUEST['notes'];
            if( $keys ){
                $new_keys = array();
                foreach ($keys as $index => $key){
                    if( $key[0] == $rem_key ){
                        $key[2] = $notes;
                        $new_keys[] = $key;
                    } else {
                        $new_keys[] = $key;
                    }
                }
                update_option('_toolkit_syncer_key', $new_keys);
            }
            wp_send_json(array('success'=>true, 'message'=>__('Key has updated successfully!')));
        }

        /**
         * Check remote calls
         *
         */
        private function remote_call_checks($raw_data)
        {
            $key = $raw_data['key'];
            $keyParts = explode(':', $key);
            $remoteSiteUrl = base64_decode($keyParts[1]);

            // Check for the auth key
            if (empty($key) || empty($remoteSiteUrl)) {
                return new \WP_Error('Invalid call', 'Sorry invalid key or remote site');
            }

            return true;
        }

        public function post_get_remote_templates()
        {
            if (!$this->has_permission()) {
                wp_send_json_error('Sorry, your user role does not have permission to access this.', 400);
                return;
            }

            $result = $this->get_remote_templates($_REQUEST);

            if (is_wp_error($result)) {
                wp_send_json_error($result, 400);
                return;
            }

            wp_send_json($result);
        }

        public function post_get_remote_template()
        {
            if (!$this->has_permission()) {
                wp_send_json_error('Sorry, your user role does not have permission to access this.', 400);
                return;
            }

            $result = $this->download_remote_template($_REQUEST);

            if (is_wp_error($result)) {
                wp_send_json_error($result, 400);
                return;
            }

            wp_send_json($result);
        }

        /**
         * Get remote templates
         *
         */
        public function get_remote_templates($raw_data)
        {
            // Check the incoming data
            $check = $this->remote_call_checks($raw_data);
            if (is_wp_error($check)) {
                return $check;
            }

            // Make a call to remote site
            // Get the key
            $key = $raw_data['key'];
            $keyParts = explode(':', $key);
            $remoteSiteUrl = base64_decode($keyParts[1]);

            $url = $remoteSiteUrl . $this->endpoint . "/templates?key=" . urlEncode($key);
            //$response = wp_remote_get($url);
            $response = wp_remote_get($url, [
                'timeout' => 20,
                'blocking' => true,
                'cookie' => $_COOKIE,
                'sslverify' => false,
                'headers' => [
                    'HTTP_ORIGIN' => site_url(),
                    'HTTP_REFERER' => site_url(),
                ],
            ]);

            // Check the result
            if (is_wp_error($response)) {
                return new WP_Error('error', 'error response from remote', [
                    'response' => $response,
                    'url' => $url,
                    'site' => $raw_data['site'],
                    'siteUrl' => site_url(),
                ]);
            }

            if ($response['response']['code'] === 200) {
                return json_decode($response['body']);
            } else {
                return new WP_Error('error', 'invalid response from remote', [
                    'response' => $response,
                    'url' => $url,
                    'site' => $raw_data['site'],
                    'siteUrl' => site_url(),
                ]);
            }
        }

        /**
         * Get remote template
         *
         */
        public function download_remote_template($raw_data)
        {
            // This will allow it to import pictures
            include_once(ABSPATH . 'wp-admin/includes/image.php');

            // Check the incoming data
            $check = $raw_data['key'];
            if (is_wp_error($check)) {
                return $check;
            }

            // Make a call to remote site
            // Get the key
            $key = $raw_data['key'];
            $keyParts = explode(':', $key);
            $remoteSiteUrl = base64_decode($keyParts[1]);
            $template_id = $raw_data['id'];

            $url = $remoteSiteUrl . $this->endpoint . "/template/$template_id?key=" . urlEncode($key);
            //$response = wp_remote_get($url);
            $response = wp_remote_get($url, [
                'timeout' => 20,
                'blocking' => true,
                'cookie' => $_COOKIE,
                'sslverify' => false,
                'headers' => [
                    'HTTP_ORIGIN' => site_url(),
                    'HTTP_REFERER' => site_url(),
                ],
            ]);

            // Check the result
            if (is_wp_error($response)) {
                return new WP_Error('error', 'error response from remote', [
                    'response' => $response,
                    'url' => $url,
                    'site' => $raw_data['site'],
                    'siteUrl' => site_url(),
                ]);
            }

            if ($response['response']['code'] === 200) {
                $data = json_decode($response['body'], true);

                // Change the title, so its easy to differentiate
                //$data['title'] .= ' - from '. $raw_data['site'];

                // Save the template
                return $this->save_template($data);
            } else {
                return new WP_Error('error', 'invalid response from remote', [
                    'response' => $response,
                    'url' => $url,
                    'site' => $raw_data['site'],
                    'siteUrl' => site_url(),
                ]);
            }
        }

        /**
         * Get template
         */
        public function get_templates($raw_data)
        {
            // Use the elementor source to get templates
            $templates = $this->get_items([
                'type' => [
                    'page',
                    'single-page',
                    'single-post',
                    'section',
                    'loop',
                    'widget',
                    'popup',
                    'header',
                    'footer',
                    'single',
                    'archive',
                    'product',
                    'product-archive',
                    'search-results',
                    'error-404'
                ],
            ]);
            require_once TOOLKIT_FOR_ELEMENTOR_PATH . 'admin/class-toolkit-for-elementor-lpage.php';
            $lpage = new Toolkit_For_Elementor_LPage();
            $landing_pages = $lpage->get_landing_pages();
            $templates = array_merge($templates, $landing_pages);
            return $templates;
        }

        /**
         * Get the template
         */
        public function get_template($raw_data)
        {
            // First get the template
            $template_id = $raw_data['id'];
            $template_data = $this->get_data([
                'template_id' => $template_id,
            ]);

            if (empty($template_data['content'])) {
                return new \WP_Error('empty_template', 'The template is empty');
            }

            $template_data['content'] = $this->process_export_import_content($template_data['content'], 'on_export');

            if (get_post_meta($template_id, '_elementor_page_settings', true)) {
                $page = \Elementor\Core\Settings\Manager::get_settings_managers('page')->get_model($template_id);

                $page_settings_data = $this->process_element_export_import_content($page, 'on_export');

                if (!empty($page_settings_data['settings'])) {
                    $template_data['page_settings'] = $page_settings_data['settings'];
                }
            }

            // The export data
            $export_data = [
                'version' => \Elementor\DB::DB_VERSION,
                'title' => get_the_title($template_id),
                'type' => self::get_template_type($template_id),
            ];
            $export_data += $template_data;

            return $export_data;
        }

        /**
         *
         * Get the current user to admin
         * Because some function in elementor need privileages
         *
         */
        private function setAdminUser()
        {
            // Get a list of users
            $users = get_users([
                'role' => 'administrator',
                'number' => 1,
                'fields' => ['id'],
                'orderby' => 'ID',
                'order' => 'asc'
            ]);

            if (!empty($users)) {
                wp_set_current_user($users[0]->id);
            }
        }

        /**
         * Save template
         *
         */
        protected function save_template($data)
        {
            if (empty($data)) {
                return new \WP_Error('no_data', 'Sorry no template data');
            }

            // Check content
            $content = $data['content'];
            if (!is_array($content)) {
                return new \WP_Error('no_content', 'Template does not have content');
            }

            // Some of the functions will need privilages
            $this->setAdminUser();
            // Import content
            $content = $this->process_export_import_content($content, 'on_import');

            // Get page setting
            $page_settings = [];
            if (!empty($data['page_settings'])) {
                $page = new \Elementor\Core\Settings\Page\Model([
                    'id' => 0,
                    'settings' => $data['page_settings'],
                ]);

                $page_settings_data = $this->process_element_export_import_content($page, 'on_import');
                if (!empty($page_settings_data['settings'])) {
                    $page_settings = $page_settings_data['settings'];
                }
            }

            // Different version of elementor use different save item function
            if (version_compare(ELEMENTOR_VERSION, '2.6.100') === -1) {
                // If its less than 2.7.0 use the old save_item
                $template_id = $this->save_item([
                    'content' => $content,
                    'title' => $data['title'],
                    'type' => $data['type'],
                    'page_settings' => $page_settings,
                ]);
            } else {
                $template_id = $this->save_item27([
                    'content' => $content,
                    'title' => $data['title'],
                    'type' => $data['type'],
                    'page_settings' => $page_settings,
                ]);
            }
            wp_set_current_user($this->user_id);
            if (is_wp_error($template_id)) {
                return $template_id;
            }

            return $this->get_item($template_id);
        }

        /**
         * Save local template.
         *
         * Save new or update existing template on the database.
         *
         * @since 1.0.0
         * @access public
         *
         * @param array $template_data Local template data.
         *
         * @return \WP_Error|int The ID of the saved/updated template, `WP_Error` otherwise.
         */
        public function save_item27($template_data)
        {
            $defaults = [
                'title' => __('(no title)', 'elementor'),
                'page_settings' => [],
                'status' => current_user_can('publish_posts') ? 'publish' : 'pending',
            ];
            $template_data = wp_parse_args($template_data, $defaults);
            $document = \Elementor\Plugin::$instance->documents->create(
                $template_data['type'],
                [
                    'post_title' => $template_data['title'],
                    'post_status' => $template_data['status'],
                    'post_type' => ($template_data['type'] == 'landing-page') ? 'e-landing-page' : self::CPT,
                    'post_author' => get_current_user_id()
                ]
            );

            if (is_wp_error($document)) {
                /**
                 * @var \WP_Error $document
                 */
                return $document;
            }
            $document->save([
                'elements' => $template_data['content'],
                'settings' => $template_data['page_settings'],
            ]);
            $template_id = $document->get_main_id();
            /**
             * After template library save.
             *
             * Fires after Elementor template library was saved.
             *
             * @since 1.0.1
             *
             * @param int $template_id The ID of the template.
             * @param array $template_data The template data.
             */
            do_action('elementor/template-library/after_save_template', $template_id, $template_data);
            /**
             * After template library update.
             *
             * Fires after Elementor template library was updated.
             *
             * @since 1.0.1
             *
             * @param int $template_id The ID of the template.
             * @param array $template_data The template data.
             */
            do_action('elementor/template-library/after_update_template', $template_id, $template_data);
            return $template_id;
        }

        /**
         * Override the original save_item
         *
         */
        public function save_item($template_data)
        {
            $type = \Elementor\Plugin::$instance->documents->get_document_type($template_data['type'], false);

            if (!$type) {
                return new \WP_Error('save_error', sprintf('Invalid template type "%s".', $template_data['type']));
            }

            $template_id = wp_insert_post([
                'post_title' => !empty($template_data['title']) ? $template_data['title'] : __('(no title)', 'elementor'),
                'post_status' => 'publish',
                'post_type' => self::CPT,
                'post_author' => get_current_user_id()
            ]);

            if (is_wp_error($template_id)) {
                return $template_id;
            }
            if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.1.0', '>=')) {
                \Elementor\Plugin::$instance->documents->get($template_id)->set_is_built_with_elementor(true);
            } else {
                \Elementor\Plugin::$instance->db->set_is_elementor_page($template_id);
            }

            $this->save_item_type($template_id, $template_data['type']);

            if (defined('ELEMENTOR_VERSION') && version_compare(ELEMENTOR_VERSION, '3.0', '>=')) {
                \Elementor\Plugin::$instance->documents->get($template_id)->save($template_data['content']);
            } else {
                \Elementor\Plugin::$instance->db->save_editor($template_id, $template_data['content']);
            }

            if (!empty($template_data['page_settings'])) {
                \Elementor\Core\Settings\Manager::get_settings_managers('page')->save_settings($template_data['page_settings'], $template_id);
            }

            /**
             * After template library save.
             *
             * Fires after Elementor template library was saved.
             *
             * @since 1.0.1
             *
             * @param int $template_id The ID of the template.
             * @param array $template_data The template data.
             */
            do_action('elementor/template-library/after_save_template', $template_id, $template_data);

            /**
             * After template library update.
             *
             * Fires after Elementor template library was updated.
             *
             * @since 1.0.1
             *
             * @param int $template_id The ID of the template.
             * @param array $template_data The template data.
             */
            do_action('elementor/template-library/after_update_template', $template_id, $template_data);

            return $template_id;
        }

        /**
         * Override the original save_item_type
         *
         */
        private function save_item_type($post_id, $type)
        {
            update_post_meta($post_id, \Elementor\Core\Base\Document::TYPE_META_KEY, $type);

            wp_set_object_terms($post_id, $type, self::TAXONOMY_TYPE_SLUG);
        }

        public function remove_syncer_key()
        {
            if (isset($_POST['site_id']) && $_POST['site_id'] > 0) {
                $deleted = wp_delete_post((int)$_POST['site_id'], true);
                if ($deleted) {
                    $response = array('success' => true, 'message' => __("Key has been removed successfully.", "toolkit-for-elementor"));
                } else {
                    $response = array('success' => false, 'message' => __("Something went wrong, please try again later.", "toolkit-for-elementor"));
                }
            } else {
                $response = array('success' => false, 'message' => __("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }

        public function bookmark_syncer_key()
        {
            if (isset($_POST['key']) && $_POST['key']) {
                $key = $_POST['key'];
                $keyParts = explode(':', $key);
                $remoteSiteUrl = base64_decode($keyParts[1]);
                $my_post = array(
                    'post_title' => esc_sql(wp_strip_all_tags($keyParts[0])),
                    'post_content' => esc_sql($key),
                    'post_status' => 'publish',
                    'post_type' => 'syncer_site',
                    'post_excerpt' => esc_sql($remoteSiteUrl)
                );
                global $wpdb;
                $sql = "SELECT" . " * FROM {$wpdb->posts} WHERE `post_type`='syncer_site' AND `post_excerpt`='$remoteSiteUrl'";
                $row = $wpdb->get_row($sql);
                if ($row) {
                    $my_post['ID'] = (int)$row->ID;
                }
                $post_id = wp_insert_post($my_post);
                if (!is_wp_error($post_id)) {
                    $response = array('success' => true, 'message' => __("Key has been saved successfully.", "toolkit-for-elementor"));
                } else {
                    $response = array('success' => false, 'message' => __("Something went wrong, try again later.", "toolkit-for-elementor"));
                }
            } else {
                $response = array('success' => false, 'message' => __("Invalid parameters passed.", "toolkit-for-elementor"));
            }
            wp_send_json($response);
        }
    }

    new Toolkit_Elementor_Syncer();
endif;
