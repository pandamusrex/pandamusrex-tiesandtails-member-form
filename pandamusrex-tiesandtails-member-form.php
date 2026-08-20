<?php
/**
 * Plugin Name: PandamusRex Ties and Tails Member Form
 * Version: 1.5.0
 * Plugin URI: https://github.com/pandamusrex/pandamusrex-tiesandtails-member-form
 * Description: Custom member form for tiesandtails.club
 * Author: PandamusRex
 * Author URI: https://www.github.com/pandamusrex/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Requires at least: 6.4
 * Requires PHP: 7.0
 * Tested up to: 6.8
 *
 * Text Domain: pandamusrex-tiesandtails-member-form
 * Domain Path: /lang/
 *
 * @package WordPress
 * @author PandamusRex
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class PandamusRex_TiesAndTails_Member_Form {
    private static $instance;

    public static function get_instance() {
        if ( null == self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __clone() {}

    public function __wakeup() {}

    public function __construct() {
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_checkout_scripts' ] );
        add_action( 'woocommerce_after_checkout_form', [ $this, 'memberships_checkout_content' ] );
    }

    public function enqueue_checkout_scripts() {
        if ( ! function_exists( 'is_checkout' ) ) {
            return;
        }

        // if ( ! is_checkout() ) {
        //     return;
        // }

        wp_enqueue_script( 'jquery-ui-dialog' );

        // https://wordpress.stackexchange.com/questions/274610/adding-jquery-ui-elements-to-wordpress-page
        // Access the wp_scripts global to get the jquery-ui-core version used.
        global $wp_scripts;
        $ver = $wp_scripts->registered['jquery-ui-core']->ver;
        $handle = 'jquery-ui';

        // Path to stylesheet, based on the jquery-ui-core version used in core.
        $src = "https://ajax.googleapis.com/ajax/libs/jqueryui/{$ver}/themes/smoothness/{$handle}.css";

        // Register the stylesheet handle and enqueue it
        wp_register_style( $handle, $src, [], $ver );
        wp_enqueue_style( 'jquery-ui' );

        wp_enqueue_script(
            'pandamusrex-tat-member-form',
            plugin_dir_url( __FILE__ ) . 'scripts/pandamusrex-tat-member-form.js',
            [ 'jquery' ],
            '1.5.0',
            false
        );

        wp_enqueue_style(
            'pandamusrex-tat-member-form-styles',
            plugin_dir_url( __FILE__ ) . 'styles/pandamusrex-tat-member-form.css',
            [],
            '1.5.0'
        );
    }

    public function memberships_checkout_content() {
        // Don't display the terms and conditions form if a user is logged in
        // If a user is logged in, that means they completed checkout sometime in the past
        // since that's the only way users can create an account and that means
        // they've already accepted the T and C.
        if ( is_user_logged_in() ) {
            return;
        }

?>
        <div id='tat-terms-modal' title='Membership Terms and Conditions'>
            <form method='post'>
                <h1>Hey everyone, welcome to the Club!</h1>
                <p><b>Already a member? <a href="<?php echo esc_url( wp_login_url( get_permalink() ) ); ?>">Click here to log in to your account</a></b></p>
                <p>We've finally got our 501(c)7!</p>
                <p>That means we can pool our money for events without owing taxes on the money we collect.</p>
                <p>Unfortunately, the IRS has a lot of rules about this: basically, <i>we can only pool money from Club members</i>.</p>
                <p>Because of that, we need need two things from everyone who wants to attend events:</p>
                <ol>
                    <li>You'll need to declare yourself a member of the Social Group.</li>
                    <li>We need an email address for the Club <b><i>that you actually monitor</i></b>.</li>
                </ol>
                <p>Just to be clear: weekly MeetUps, individually paid restaurant trips, and every other event are still free
                    and open to anyone and everyone. Membership requirements <i>only apply to tax-free merchandise and
                    events where we pool our money</i>.</p>
                <p>Sorry. it's the IRS.</p>
                <p>The upside is we get great deals on group packages, venues, and events like Trick or Meat. No cramming
                    into the bar at Fogo like sardines!</p>
                <p>Didja read that?</p>
                <input type='checkbox' id='tat_accepts_terms' name='tat_accepts_terms' required />
                <label for='tat_accepts_terms'>I Understand <font color='red'>*</font></label>

                <h1>Membership Qualification</h1>
                <p>This section covers the IRS-required legalese of joining the Social Group.</p>
                <p>The full <a href="https://docs.google.com/document/d/1g8cAwpJ8pK2B8EeZxZOfhimDLxOjcS6vgjnEm0EOFm0/">Bylaws can be read here</a>.</p>
                <h2>Non-Discrimination</h2>
                <p>Membership is open to any person at least eighteen (18) years-old without regard to race,
                    religious creed, skin color, national origin, ancestry, physical disability, mental disability,
                    medical condition, marital status, gender, or sexual orientation of such persons.</p>
                <input type='checkbox' id='tat_at_least_18' name='tat_at_least_18' required />
                <label for='tat_at_least_18'>I am at least 18 years of age <font color='red'>*</font></label>

                <h2>Declaration of Membership</h2>
                <p>In order to qualify for membership in the Club, a member shall be required to declare themselves
                    to be a furry.</p>
                <ol type="i">
                    <li>Definition of a Furry<br/>
                    A furry, according to Merriam-Webster is a person who identifies with and enjoys sometimes
                    dressing as anthropomorphic animals or creatures especially as a member of a fandom devoted
                    to the practice.</li>
                    <li>Expanded definition of a Furry<br/>
                    The Club explicitly adopts a more expansive definition of furry. A furry is anyone with an
                    interest in furry themes, furry attire, furry personas, anthropomorphism, pup and/or
                    handler play, or non-furry anthropomorphic personas including but not limited to plane-sonas,
                    mechano-sonas (such as Protogens), scalies, and other commonly accepted sub-groups of the
                    furry fandom.</li>
                </ol>
                <p>I declare I'm a furry as defined by either or both of the above definitions.</p>
                <input type='checkbox' id='tat_is_a_furry' name='tat_is_a_furry' required />
                <label for='tat_is_a_furry'>I'm a furry! <font color='red'>*</font></label>

                <h2>Exclusionary Clauses</h2>
                <p>Members of the following groups must have their applications reviewed before approval. Please
                    Contact Us through the <a href="https://www.tiesandtails.club/contact-us/">Contact Page</a> for more information.</p>

                <p>
                    <input type='checkbox' id='tat_no_hate_group' name='tat_no_hate_group' required />
                    <label for='tat_no_hate_group'>I am not a member of an SPLC designated hate group. <font color='red'>*</font></label>
                </p>

                <p>
                    <input type='checkbox' id='tat_no_sex_crime' name='tat_no_sex_crime' required />
                    <label for='tat_no_sex_crime'>I have never been convicted of a sexual crime. <font color='red'>*</font></label>
                </p>

                <p>
                    <input type='checkbox' id='tat_no_felony' name='tat_no_felony' required />
                    <label for='tat_no_felony'>I have never been convicted of a felony. <font color='red'>*</font></label>
                </p>

                <p>
                    <input type='checkbox' id='tat_no_bans' name='tat_no_bans' required />
                    <label for='tat_no_bans'>I am not currently banned from any conventions or gatherings (furry or otherwise). <font color='red'>*</font></label>
                </p>
            </form>
        </div>
<?php
    }
}

PandamusRex_TiesAndTails_Member_Form::get_instance();
