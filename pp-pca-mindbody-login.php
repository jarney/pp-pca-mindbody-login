<?php
/**
 * Plugin Name: PP PCA Mindbody Login
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: This plugin provides a way for clients of the MindBody system to log into a WordPress page and show the user information unique to that user's classes and settings.
 * Version: 1.0
 * Author: Jon Arney, Mark Hamblin
 * Author URI: http://www.paypal.com/
 * License: GPL2
 */
/*  Copyright 2014  Jon Arney, Mark Hamblin (email : jarney1@cox.net)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as 
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
 * This class describes a user of the MINDBODY system.
 * A user consists of the user's ID (clientId) as well
 * as the globally unique session ID obtained from the MINDBODY
 * API when validating the user's username and password.  In addition,
 * we store the list of classes that the user is currently enrolled in
 * so that we can use that list to show specific content based on the user.
 */
class PPPCAMindBodyUserData {
    var $mbGUID;
    var $clientId;
    var $classList;

    public function __construct($GUID, $clientId, $classList) {
        $this->mbGUID = $GUID;
        $this->clientId = $clientId;
        $this->classList = $classList;
    }
    /**
     * The client ID is the ID within the MINDBODY system
     * for this user.
     */
    public function getClientId() {
        return $this->clientId;
    }
    /**
     * The GUID is a globally unique session ID
     * given to a user when they validate the username
     * and password upon logging in.
     */
    public function getGUID() {
        return $this->GUID;
    }
    /**
     * The class list is the list of classes that the user
     * is currently enrolled in.
     */
    public function getClassList() {
        return $this->classList;
    }
}

class PPPCAMindbodyPlugin {
    public $option;
    public function __construct() {
        add_action('init', array($this, 'init'));
        $this->option = get_option("pp_pca_options");
    }
    public function init() {
        // This shortcode simply indicates that the user must have
        // a valid MINDBODY account in order to see any content on a page
        // which contains this shortcode.
        add_shortcode('pp-pca-mindbody-login', array($this, 'shortcodeLogin'));

        // This shortcode expands to show the list of classes that this user
        // is enrolled in.  This assumes that the classes have the 'tag' attribute
        // of 'class-{classId}' where classId is the ID of the class in the
        // MINDBODY system.
        add_shortcode('pp-pca-mindbody-class-list', array($this, 'shortcodeClassList'));

        // This shortcode allows you to have specific content which is only visible
        // when a particular class has been enrolled.  This shortcode requires that
        // the 'id' attribute of the class being protected is specified.
        // For example, the content to protect might look like this:
        //     [pp-pca-mindbody-class id=classId]Content To Protect[/pp-pca-mindbody-class]
        add_shortcode('pp-pca-mindbody-class', array($this, 'shortcodeClass'));

        // In order to handle the login correctly, this override allows us to change
        // the template used to render the page based on whether the user has logged in or
        // not.
        add_action( 'template_include', array($this, 'template_include'));

        // This shortcode allows you to hide and show certain videos based on
        // buttons in the HTML code.
	add_shortcode('hide_panes', array($this, 'hidePanes'));
    }
    /**
     * This function expands to some JavaScript
     * code to facilitate hiding and showing content
     * based on selection buttons.  This allows pages to be more
     * easily used and fit within the PCA website.
     */
    public function hidePanes() {
		$sc = "<script type='text/javascript'>" .
			// "<!--" .
			"(function() {" .
			"  var fn = function(){" .
			"    jQuery('.wks').hide();" .
			"    jQuery('#'+jQuery(this).html()).toggle();" .
			"  };" .
			"  var panes = jQuery('.wks');" .
			"  for (var k = 1; k <= panes.length; k++) {" .
			"    var btn = jQuery('<button/>');" .
			"    btn.html('wk'+k).click(fn);" .
			"    jQuery('#btns').append(btn);" .
			"  }" .
			"  jQuery('.wks').hide(); jQuery('#wk1').toggle();" .
			"}());" .
			// "//-->" .
			"</script>" ;
		return $sc;
    }

    public function getOption($optionId) {
        return $this->option[$optionId];
    }

    public function getSourceCredentials() {
        $sourcename = $this->getOption('PP_PCA_MINDBODY_SOURCE_NAME');
        $password = $this->getOption('PP_PCA_MINDBODY_PASSWORD');
        $siteID = $this->getOption('PP_PCA_MINDBODY_SITE_ID');
        $sourceCredentials= new SourceCredentials($sourcename, $password, array($siteID));
	return $sourceCredentials;
    }

    public function getUserCredentials() {
        $siteusername = $this->getOption('PP_PCA_SITEOWNER_NAME');
        $sitepassword = $this->getOption('PP_PCA_SITEOWNER_PASSWORD');
        $siteID = $this->getOption('PP_PCA_MINDBODY_SITE_ID');
        $userCredentials= new UserCredentials($siteusername, $sitepassword, array($siteID));
        return $userCredentials;
    }

    public function getClassService() {
        require_once(dirname(__FILE__) . '/mindbody-api/classService.php');
        $clientService = new MBClassService();
        $clientService->SetDefaultCredentials($this->getSourceCredentials());
        $clientService->SetDefaultUserCredentials($this->getUserCredentials());
        return $clientService;
    }

    public function getClientService() {
        require_once(dirname(__FILE__) . '/mindbody-api/clientService.php');
        $clientService = new MBClientService();
        $clientService->SetDefaultCredentials($this->getSourceCredentials());
        $clientService->SetDefaultUserCredentials($this->getUserCredentials());
        return $clientService;
    }

    public function template_include($template) {
	global $post;
        global $pp_pca_mindbody_error;

	if (is_page() || is_object($post)) {
            if( is_a( $post, 'WP_Post' ) && (
                has_shortcode( $post->post_content, 'pp-pca-mindbody-login') ||
                has_shortcode( $post->post_content, 'pp-pca-mindbody-class-list') ||
                has_shortcode( $post->post_content, 'pp-pca-mindbody-class')
              )) {
                if (isset($_GET["pp_pca_mindbody_logout"])) {
                        unset($_SESSION["pp-pca-mindbody-login"]);
                }
                if (isset($_POST["pp_pca_mindbody_username"]) &&
                    isset($_POST["pp_pca_mindbody_username"]) ) {

                    $form_mindbody_username = $_POST["pp_pca_mindbody_username"];
                    $form_mindbody_password = $_POST["pp_pca_mindbody_password"];

                    ob_start();

                    $clientService = $this->getClientService();
                    $result = $clientService->ValidateLogin(
                                    $form_mindbody_username,
                                    $form_mindbody_password);

                    $classService = $this->getClassService();

                    $classDescriptionIDs = array();
                    $classIDs = array();
                    $staffIDs = array();

                    $startDate = new DateTime();
                    $startDate->modify("-90 days");

                    // Next 12 months.
                    $endDate = new DateTime();
                    $endDate->modify("+120 days");

                    $classService = $this->getClassService();
                    $getClassesResponse = $classService->GetClasses(
                        $classDescriptionIDs,
                        $classIDs,
                        $staffIDs,
                        $startDate,
                        $endDate,
                        $result->ValidateLoginResult->Client->ID
                    );

                    $classList = array();
                    foreach ($getClassesResponse->GetClassesResult->Classes->Class as $myClass) {
                        if ($myClass->IsEnrolled == 1) {
                            array_push($classList, $myClass);
                        }
                    }

                    $mb_result = ob_get_contents();

                    ob_end_clean();

                    if ($result == NULL || $result->ValidateLoginResult->Status == "InvalidParameters") {
                        if (empty($mb_result)) {
                            $pp_pca_mindbody_error = $mb_result;
                        }
                        else {
                            $pp_pca_mindbody_error = "The MindBody login information we have for you isn't correct. Please re-enter it.";
                        }
                    }
                    else {
                        $sessionData = new PPPCAMindBodyUserData(
                            $result->ValidateLoginResult->GUID,
                            $result->ValidateLoginResult->Client->ID,
                            $classList
                        );
                        $_SESSION["pp-pca-mindbody-login"] = serialize($sessionData);
                    }
                }

                if (!isset($_SESSION["pp-pca-mindbody-login"])) {
                    $template = plugin_dir_path(__FILE__) . 'pp-pca-mindbody-login-template.php';
                }
            }
	}
        return $template;
    }

    private function get_logout() {
        $selfURL = "http://" . $_SERVER['HTTP_HOST']  . $_SERVER['REQUEST_URI'];

        $logoutLink = "<a href=\"" . $selfURL . "?pp_pca_mindbody_logout=true\">Logout</a>";

        $retString = $logoutLink . "<br/>";

        return $retString;
    }

    public function shortcodeLogin() {
        $logoutLink = $this->get_logout();
        return $logoutLink;
    }

    public function getClassList() {
    }


    ///
    // Adds all of the posts for 
    // a particular class.
    //
    function shortcodeClassList($attrs) {

        $sessionData = unserialize($_SESSION["pp-pca-mindbody-login"]);

	$tagstring = "";
        $i = 0;
        foreach ($sessionData->getClassList() as $myClass) {
            $content = 
                "[ic_add_posts tag='class-" . $myClass->ClassScheduleID . "']";
            if( is_admin() ) {
                $content = "Class " .
                    $myClass->ClassScheduleID . ":" . 
                    $myClass->ClassDescription->Name . ":" .
                    $content;
            }
            if ($i == 0) {
                $tagstring = $content;
            }
            else {
                $tagstring = $tagstring . "<br/>" . $content;
            }
            $i++;
        }

        return do_shortcode($tagstring);
    }
    ///
    // Adds all of the posts for 
    // a particular class.
    //
    function shortcodeClass($attrs, $content = null) {

        $classAttributes = shortcode_atts( array(
            'id' => 'none'
        ), $attrs );

        $sessionData = unserialize($_SESSION["pp-pca-mindbody-login"]);

        $i = 0;
        $found = 0;
        foreach ($sessionData->getClassList() as $myClass) {
            if (strcmp($classAttributes['id'], $myClass->ClassScheduleID) == 0) {
                $found = 1;
                break;
            }
        }
        if ($found == 1) {
            return do_shortcode($content);
        }
        else {
            return "You are not enrolled in this class";
        }
    }

}
$ppPCAMindbodyPlugin = new PPPCAMindbodyPlugin();

/**
 * This class handles the 'Options' menu
 * for the plugin.  The main purpose of the 'Options'
 * menu is to allow a place for administrators to change
 * and maintain the various username/password credentials
 * required in order to use the MINDBODY API.
 */
class PPPCAMindbodySettings
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
            'PP PCA Mindbody Plugin', 
            'manage_options', 
            'pp-pca-settings-admin', 
            array( $this, 'create_admin_page' )
        );
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        // Set class property
        $this->options = get_option( 'pp_pca_options' );
        ?>
        <div class="wrap">
            <?php screen_icon(); ?>
            <h2>PP PCA Mindbody Settings</h2>           
            <form method="post" action="options.php">
            <?php
                // This prints out all hidden setting fields
                settings_fields( 'pp_pca_options_group' );   
                do_settings_sections( 'pp-pca-settings-admin' );
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
            'pp_pca_options_group', // Option group
            'pp_pca_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );

        add_settings_section(
            'setting_section_id', // ID
            'PP PCA Settings', // Title
            array( $this, 'print_section_info' ), // Callback
            'pp-pca-settings-admin' // Page
        );  

        add_settings_field(
            'PP_PCA_MINDBODY_SOURCE_NAME', // ID
            'Mindbody API Source Name', // Title 
            array( $this, 'mindbody_source_name_callback' ), // Callback
            'pp-pca-settings-admin', // Page
            'setting_section_id' // Section           
        );      

        add_settings_field(
            'PP_PCA_MINDBODY_PASSWORD', 
            'Mindbody API Password', 
            array( $this, 'mindbody_password_callback' ), 
            'pp-pca-settings-admin', 
            'setting_section_id'
        );      
        add_settings_field(
            'PP_PCA_MINDBODY_SITE_ID', 
            'Mindbody API Site ID', 
            array( $this, 'mindbody_site_id_callback' ), 
            'pp-pca-settings-admin', 
            'setting_section_id'
        );      
        add_settings_field(
            'PP_PCA_SITEOWNER_NAME', 
            'Mindbody API Site Owner', 
            array( $this, 'mindbody_siteowner_name_callback' ), 
            'pp-pca-settings-admin', 
            'setting_section_id'
        );      
        add_settings_field(
            'PP_PCA_SITEOWNER_PASSWORD', 
            'Mindbody API Site Owner Password', 
            array( $this, 'mindbody_siteowner_password_callback' ), 
            'pp-pca-settings-admin', 
            'setting_section_id'
        );      
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function sanitize( $input ) {
        $inputFields = array(
            'PP_PCA_MINDBODY_SOURCE_NAME',
            'PP_PCA_MINDBODY_PASSWORD',
            'PP_PCA_MINDBODY_SITE_ID',
            'PP_PCA_SITEOWNER_NAME',
            'PP_PCA_SITEOWNER_PASSWORD');

        $new_input = array();
        
        foreach ($inputFields as $field) {
            if( isset( $input[$field] ) ) {
                $new_input[$field] = sanitize_text_field( $input[$field] );
            }
       }

        return $new_input;
    }

    /** 
     * Print the Section text
     */
    public function print_section_info()
    {
        print 'Enter MindBody API credentials below:';
    }

    /** 
     * Get the settings option array and print one of its values
     */
    public function mindbody_source_name_callback() {
        $this->mindbody_generic_callback('PP_PCA_MINDBODY_SOURCE_NAME');
    }
    public function mindbody_password_callback() {
        $this->mindbody_generic_callback('PP_PCA_MINDBODY_PASSWORD');
    }
    public function mindbody_site_id_callback() {
        $this->mindbody_generic_callback('PP_PCA_MINDBODY_SITE_ID');
    }
    public function mindbody_siteowner_name_callback() {
        $this->mindbody_generic_callback('PP_PCA_SITEOWNER_NAME');
    }
    public function mindbody_siteowner_password_callback() {
        $this->mindbody_generic_callback('PP_PCA_SITEOWNER_PASSWORD');
    }

    public function mindbody_generic_callback($optionId) {
        printf(
            '<input type="text" id="' . $optionId . '" name="pp_pca_options[' . $optionId . ']" value="%s" />',
            isset( $this->options[$optionId] ) ? esc_attr( $this->options[$optionId]) : ''
        );
    }

}

if( is_admin() ) {
    $my_settings_page = new PPPCAMindbodySettings();
}
?>