<?php

namespace WpMock {

    use WpMock\Mocks\OptionMock;

    class WpOptions
    {
        // Static proxies
        // -------------------------------------------------------------------------------------------------------------

        public static function expects($key)
        {
            return self::getInstance()->__expects($key);
        }

        public static function verify()
        {
            self::getInstance()->__verify();
        }

        public static function reset()
        {
            $self = self::getInstance();
            $self->expectations = [];
            $self->options = [];
        }

        public static function prepareOption($key, $value, $autoload = 'yes')
        {
            $self = self::getInstance();
            $self->options[$key] = [
                'value' => $value,
                'autoload' => $autoload,
            ];
        }

        public static function prepareOptions($options = [])
        {
            $self = self::getInstance();
            foreach ($options as $key => $option) {
                if (is_array($option)) {
                    $self->options[$key] = [
                        'value' => $option['value'],
                        'autoload' => $option['autoload'],
                    ];
                } else {
                    $self->options[$key] = [
                        'value' => $option,
                        'autoload' => 'yes',
                    ];
                }
            }
        }

        // Instance
        // -------------------------------------------------------------------------------------------------------------

        private static $instance = null;

        public static function getInstance()
        {
            if (null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        // Expects
        // -------------------------------------------------------------------------------------------------------------

        protected $expectations = [];

        protected function __expects($key)
        {
            $expectation = new OptionMock($key);
            $this->expectations[] = $expectation;
            return $expectation;
        }

        // Verification
        // -------------------------------------------------------------------------------------------------------------

        public function __verify()
        {
            foreach ($this->expectations as $expectation) {
                /** @var $expectation OptionMock */
                $expectation->verify();
            }
        }

        // Invocation
        // -------------------------------------------------------------------------------------------------------------

        public function invoke($modifier, $key, $value = null, $autoload = null)
        {
            foreach ($this->expectations as $expectation) {
                /** @var $expectation OptionMock */
                if ($expectation->matches($key, $modifier)) {
                    $expectation->invoke($value, $autoload);
                }
            }
        }


        // Options
        // -------------------------------------------------------------------------------------------------------------

        protected $options = [];

        public function getOption($key, $default = false)
        {
            // Retrieve option
            if (is_string($key)) {
                if (isset($this->options[$key])) {
                    return $this->maybeUnserialize($this->options[$key]['value']);
                } else {
                    return $default;
                }
            } else {
                return $default;
            }
        }

        public function updateOption($key, $value)
        {
            // Report invoke
            $this->invoke(OptionMock::MOD_UPDATED, $key, $value);
            // Store option
            $value = $this->maybeSerialize($value);
            if (is_string($key)) {
                if (!isset($this->options[$key])) {
                    // Key not set
                    $this->options[$key] = [
                        'value' => $value,
                        'autoload' => 'yes',
                    ];
                    return true;
                } else {
                    // Key set
                    if ($this->options[$key]['value'] == $value) {
                        return false;
                    } else {
                        $this->options[$key]['value'] = $value;
                        return true;
                    }
                }
            } else {
                return false;
            }
        }

        public function addOption($key, $value, $autoload = 'yes')
        {
            // Report invoke
            $this->invoke(OptionMock::MOD_ADDED, $key, $value, $autoload === 'yes');
            // Store option
            $value = $this->maybeSerialize($value);
            if (is_string($key)) {
                if (!isset($this->options[$key])) {
                    $this->options[$key] = [
                        'value' => $value,
                        'autoload' => $autoload,
                    ];
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        public function deleteOption($key)
        {
            // Report invoke
            $this->invoke(OptionMock::MOD_DELETED, $key);
            // Delete option
            if (is_string($key)) {
                if (isset($this->options[$key])) {
                    unset($this->options[$key]);
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        }

        // Utils
        // -------------------------------------------------------------------------------------------------------------

        /**
         * Try to serialize the data and return the serialized string.
         * @param mixed $data The data to try to serialize.
         * @return mixed The data, possibly serialized.
         */
        protected function maybeSerialize($data)
        {
            if (is_array($data) || is_object($data) || $this->isSerialized($data)) {
                return serialize($data);
            } else {
                return $data;
            }
        }

        /**
         * Try to unserialize the data and return the serialized data.
         * @param mixed $data The data to try to unserialize.
         * @return mixed The data, possibly unserialized.
         */
        protected function maybeUnserialize($data)
        {
            if ($this->isSerialized($data)) {
                return unserialize($data);
            }
            return $data;
        }

        /**
         * True if the data provided was created by serialize()
         * @param mixed $data The data to check.
         * @return boolean True if the data was created by serialize.
         */
        protected function isSerialized($data)
        {
            return (@unserialize($data) !== false);
        }
    }
}

namespace

{
    /**
     * Get an option from the WP Options table.
     * @param string $key The option to retrieve.
     * @param mixed $default The default value when the option does not exist
     * @return string|boolean The value of the option, or false if the key doesn't exist.
     */
    function get_option($key, $default = false)
    {
        return WpMock\WpOptions::getInstance()->getOption($key, $default);
    }

    /**
     * Store an option in the WP Options table.
     * @param string $key The option to store.
     * @param string $value The value to store.
     * @return boolean True if the option was updated, false otherwise.
     */
    function update_option($key, $value)
    {
        return WpMock\WpOptions::getInstance()->updateOption($key, $value);
    }

    /**
     * Store an option in the WP Options table.
     * @param string $key The option to store.
     * @param string $value The value to store.
     * @param string $deprecated This value is not used anymore
     * @param string $autoload Should the value be loaded at WordPress start
     * @return boolean True if the option was updated, false otherwise.
     */
    function add_option($key, $value, $deprecated = '', $autoload = 'yes')
    {
        return WpMock\WpOptions::getInstance()->addOption($key, $value, $autoload);
    }

    /**
     * Delete an option from the WP Options table.
     * @param string $key The option to delete.
     * @return boolean True if the option was deleted.
     */
    function delete_option($key)
    {
        return WpMock\WpOptions::getInstance()->deleteOption($key);
    }
}
