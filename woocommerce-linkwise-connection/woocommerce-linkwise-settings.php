<?php
class Settings
{
    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;

    /**
     * Start up
     */
    public function __construct()
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
    }

    /**
     * Add options page
     */
    public function add_plugin_page()
    {
        // This page will be under "Settings"
        add_options_page(
            'Settings Admin', 
            'Linkwise Settings', 
            'manage_options', 
            'linkwise-setting-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'linkwise_settings_option' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>Linkwise Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'linkwise_settings_group' );   
                do_settings_sections( 'linkwise-setting-admin' );
                submit_button(); 
            ?>
            </form>
        </div>
        <?php
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'linkwise_settings_group', // Option group
            'linkwise_settings_option', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'Linkwise Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'linkwise-setting-admin' // Page
        );  

        add_settings_field(
            'linkwise_id', // ID
            'Linkwise ID', // Title 
            array( $this, 'linkwise_id_callback' ), // Callback
            'linkwise-setting-admin', // Page
            'setting_section_id' // Section           
        ); 
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input )
    {
        $new_input = array();
        if( isset( $input['linkwise_id'] ) )
            $new_input['linkwise_id'] = absint( $input['linkwise_id'] );

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter your settings below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function linkwise_id_callback()
    {
        printf(
            '<input type="text" id="linkwise_id" name="linkwise_settings_option[linkwise_id]" value="%s" />',
            isset( $this->options['linkwise_id'] ) ? esc_attr( $this->options['linkwise_id']) : ''
        );
    }
}

if( is_admin() )
    $linkwise_settings = new Settings();