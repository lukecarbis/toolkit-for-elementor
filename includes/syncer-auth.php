<?php

if (!class_exists('Toolkit_Elementor_Syncer_Auth')):
    class Toolkit_Elementor_Syncer_Auth
    {
        /**
         * Constructor
         *
         */
        public function __construct()
        {
            // Ensur ethe encryption key is there
            $this->ensureEncryptionKey();
        }

        /**
         * Check Auth code agaist caller's auth key
         *
         */
        public function check_auth_key()
        {
            // Get the incoming key
            $key = $_REQUEST['key'];

            try {
                $this->checkAndGetKeyInfo($key);

                return true;
            } catch (\Exception $e) {
                return new \WP_Error('invalid_key', 'Sorry invalid key', [
                    'message' => $e->getMessage(),
                    'key' => $key,
                ]);
            }

        }

        public function encrypt($message, $key)
        {
            //$key previously generated safely, ie: openssl_random_pseudo_bytes
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = openssl_random_pseudo_bytes($ivlen);
            $ciphertext_raw = openssl_encrypt($message, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $hmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            return base64_encode($iv . $hmac . $ciphertext_raw);
        }

        public function decrypt($cipherText, $key)
        {
            $c = base64_decode($cipherText);
            $ivlen = openssl_cipher_iv_length($cipher = "AES-128-CBC");
            $iv = substr($c, 0, $ivlen);
            $hmac = substr($c, $ivlen, $sha2len = 32);
            $ciphertext_raw = substr($c, $ivlen + $sha2len);
            $original_plaintext = openssl_decrypt($ciphertext_raw, $cipher, $key, $options = OPENSSL_RAW_DATA, $iv);
            $calcmac = hash_hmac('sha256', $ciphertext_raw, $key, $as_binary = true);
            if (hash_equals($hmac, $calcmac)) {
                return $original_plaintext;
            }

            throw new \Exception('Something went wrong, please check your Syncer Key');
        }

        private function ensureEncryptionKey()
        {
            $key = get_option('_toolkit_syncer_encryption_key');

            if (!$key) {
                // Key length
                $length = 32;

                // Generate a random key
                $key = '';
                for ($i = 0; $i < $length; $i++) {
                    $key .= chr(mt_rand(33, 126));
                }

                // Store the key
                update_option('_toolkit_syncer_encryption_key', $key);
            }
        }

        /**
         * Generate Syncer Key
         */
        public function generate_syncer_key()
        {
            $siteUrl = get_site_url();
            $keyPrefix = str_replace('https://', '', $siteUrl);
            $keyPrefix = str_replace('http://', '', $keyPrefix);
            $keyPrefix = str_replace(':', '', $keyPrefix);
            $keyPrefix = rtrim($keyPrefix);
            $siteName = get_bloginfo('name');

            $expiration = $_REQUEST['expiration'];
            if ($expiration) {
                $expiration = strtotime($expiration);
            } else {
                $expiration = time() + 60 * 60 * 24 * 365;
            }

            $random = uniqid();
            $parts = [
                base64_encode($siteUrl),
                $siteName,
                $expiration,
                $random,
            ];

            $key = get_option('_toolkit_syncer_encryption_key');
            $message = implode(':', $parts);
            $cipherText = $this->encrypt($message, $key);

            // Pull together the syncer key
            $syncerKey = implode(':', [
                $keyPrefix,
                base64_encode($siteUrl),
                $cipherText,
            ]);

            $notes = isset($_REQUEST['notes']) ? $_REQUEST['notes'] : '';
            // Save the syncer key
            $existed_keys = get_option('_toolkit_syncer_key', array());
            if( $existed_keys && is_array($existed_keys) ){
                $existed_keys[] = array($syncerKey, $expiration, $notes);
                update_option('_toolkit_syncer_key', $existed_keys);
            } elseif( $existed_keys ){
                $parts = explode(':', $existed_keys);
                if (count($parts) == 3) {
                    $message = $this->decrypt($parts[2], $key);
                    $keyInfo = explode(':', $message);
                    $new_keys = array(
                        array($existed_keys, $keyInfo[2], ''),
                        array($syncerKey, $expiration, $notes)
                    );
                } else {
                    $new_keys = array(
                        array($syncerKey, $expiration, $notes)
                    );
                }
                update_option('_toolkit_syncer_key', $new_keys);
            } else {
                $new_keys = array(
                    array($syncerKey, $expiration, $notes)
                );
                update_option('_toolkit_syncer_key', $new_keys);
            }

            return [
                'key' => $syncerKey,
                'expiration' => $expiration,
            ];
        }

        /**
         * Get the template
         */
        public function checkAndGetKeyInfo($key)
        {
            if (!$key) {
                throw new \Exception('Sorry, no syncer key detected.');
            }

            $parts = explode(':', $key);
            if (count($parts) !== 3) {
                throw new \Exception('Sorry, please check your Syncer Key for accuracy.');
            }

            $cipherText = $parts[2];
            $key = get_option('_toolkit_syncer_encryption_key');
            $message = $this->decrypt($cipherText, $key);
            $messageParts = explode(':', $message);

            $siteUrl = get_site_url();
            $siteName = get_bloginfo('name');

            // Check fo the name
            if ($messageParts[1] !== $siteName) {
                throw new \Exception('Sorry, incorrect key detected.');
            }

            if ($messageParts[2] < time()) {
                throw new \Exception('Sorry, key expired');
            }

            return [
                'siteUrl' => $siteUrl,
                'siteName' => $siteName,
                'expiration' => $messageParts[2],
            ];
        }

        /**
         * Get the template
         */
        public function get_syncer_key(){
            $syncerKey = get_option('_toolkit_syncer_key', array());
            if( $syncerKey && is_array($syncerKey) ){
                $syncerKey = end($syncerKey);
                $syncerKey = $syncerKey[0];
            }
            try {
                $keyInfo = $this->checkAndGetKeyInfo($syncerKey);
                return [
                    'key' => $syncerKey,
                    'expiration' => $keyInfo['expiration'],
                ];
            } catch (\Exception $e) {
                return [
                    'key' => $syncerKey,
                    'expiration' => '',
                ];
            }
        }

        public function get_syncer_keys(){
            $syncerKeys = get_option('_toolkit_syncer_key', array());
            if( $syncerKeys && is_array($syncerKeys) ){
                return $syncerKeys;
            } elseif( $syncerKeys ) {
                $parts = explode(':', $syncerKeys);
                if (count($parts) == 3) {
                    $key = get_option('_toolkit_syncer_encryption_key');
                    $message = $this->decrypt($parts[2], $key);
                    $keyInfo = explode(':', $message);
                    return array(
                        array($syncerKeys, $keyInfo[2])
                    );
                }
            }
            return array();
        }
    }
endif;
