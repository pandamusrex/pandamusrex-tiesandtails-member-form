<?php
/**
 * Plugin Name: PandamusRex Ties and Tails Member Form
 * Version: 1.0.0
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
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_my_account_scripts' ] );
        add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_checkout_scripts' ] );
        add_action( 'woocommerce_account_memberships-tab_endpoint', [ $this, 'memberships_my_account_tab_content' ], 11 );
        add_action( 'woocommerce_after_checkout_form', [ $this, 'memberships_checkout_content' ] );
    }

    public function enqueue_my_account_scripts() {
        if ( ! function_exists( 'is_wc_endpoint_url' ) ) {
            return;
        }

        // if ( ! is_wc_endpoint_url( 'memberships-tab' ) ) {
        //     return;
        // }

        wp_enqueue_script(
            'pandamusrex-tat-my-account-form',
            plugin_dir_url( __FILE__ ) . 'scripts/pandamusrex-tat-my-account-form.js',
            [ 'jquery' ],
            '1.0.0',
            false
        );

        wp_enqueue_style(
            'pandamusrex-tiesandtails-styles',
            plugin_dir_url( __FILE__ ) . 'styles/pandamusrex-tat-common.css'
        );
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
            'pandamusrex-tat-checkout-form',
            plugin_dir_url( __FILE__ ) . 'scripts/pandamusrex-tat-checkout-form.js',
            [ 'jquery' ],
            '1.0.0',
            false
        );

        wp_enqueue_style(
            'pandamusrex-tiesandtails-styles',
            plugin_dir_url( __FILE__ ) . 'styles/pandamusrex-tat-checkout-form.css'
        );
    }

    public function memberships_my_account_tab_content() {
        self::render_membership_form_for_my_account();
    }

    public function memberships_checkout_content() {
        echo "<div id='tat-terms-modal' title='Membership Terms and Conditions'>";
        self::render_membership_form_for_checkout();
        echo "</div>";
    }

    public static function get_form_elements() {
        return [
            [
                'type' => 'heading',
                'text' => 'Hey everyone, welcome to the Club!'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'We\'ve finally got our 501(c)7!',
                    'That means we can pool our money for events without owing taxes on the money we collect.',
                    'Unfortunately, the IRS has a lot of rules about this: basically, we can only pool money from Club members.',
                    'Because of that, we need need three things from everyone who wants to attend events:',
                    '1. You\'ll need to declare yourself a member of the Social Group.',
                    '2. You\'ll need to pay $1 for an annual membership.',
                    '3. We need an email address for the Club ***that you actually monitor***',
                    'Just to be clear: weekly MeetUps, individually paid restaurant trips, and every other event are still free and open to anyone and everyone. Membership requirements *only apply to tax-free merchandise and events where we pool our money*.',
                    'Sorry. it\'s the IRS.',
                    'The upside is we get great deals on group packages, venues, and events like Trick or Meat. No cramming into the bar at Fogo like sardines!',
                    'Didja read that?'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I Understand',
                'user_meta_key' => 'tat_accepts_terms'
            ],
            [
                'type' => 'heading',
                'text' => 'Membership Qualification'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'This section covers the IRS-required legalese of joining the Social Group.',
                    'The full Bylaws can be read here.',
                    '**Non-Discrimination**',
                    'Membership is open to any person at least eighteen (18) years-old without regard to race, religious creed, skin color, national origin, ancestry, physical disability, mental disability, medical condition, marital status, gender, or sexual orientation of such persons.',
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I am at least 18 years of age',
                'user_meta_key' => 'tat_at_least_18'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Declaration of Membership',
                    'In order to qualify for membership in the Club, a member shall be required to declare themselves to be a furry.',
                    'i. Definition of a Furry',
                    'A furry, according to Merriam-Webster is a person who identifies with and enjoys sometimes dressing as anthropomorphic animals or creatures especially as a member of a fandom devoted to the practice.',
                    'ii. Expanded definition of a Furry',
                    'The Club explicitly adopts a more expansive definition of furry. A furry is anyone with an interest in furry themes, furry attire, furry personas, anthropomorphism, pup and/or handler play, or non-furry anthropomorphic personas including but not limited to plane-sonas, mechano-sonas (such as Protogens), scalies, and other commonly accepted sub-groups of the furry fandom.',
                    'I declare I\'m a furry as defined by either or both of the above definitions.'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I\'m a furry',
                'user_meta_key' => 'tat_is_a_furry'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Exclusionary Clauses',
                    'Members of the following groups must have their applications reviewed before approval. Please Contact Us through one of the methods listed for more information.',
                    'Hate Groups'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I am not a member of an SPLC designated hate group.',
                'user_meta_key' => 'tat_no_hate_group'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Sexual Criminals'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I have never been convicted of a sexual crime.',
                'user_meta_key' => 'tat_no_sex_crime'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Felons'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I have never been convicted of a felony.',
                'user_meta_key' => 'tat_no_felony'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Convention & Group Bans'
                ]
            ],
            [
                'type' => 'checkbox',
                'required' => TRUE,
                'label' => 'I am not currently banned from any conventions or gatherings (furry or otherwise).',
                'user_meta_key' => 'tat_no_bans'
            ],
/*
            [
                'type' => 'heading',
                'text' => 'Membership Information'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Next up, we\'re going to gather your info for our database.',
                    'We will never sell this data, nor make it public. We are a private social group.',
                ]
            ],
            [
                'type' => 'text',
                'label' => 'Legal First Name',
                'required' => TRUE,
                'user_meta_key' => 'tat_legal_first_name'
            ],
            [
                'type' => 'text',
                'label' => 'Legal Last Name',
                'required' => TRUE,
                'user_meta_key' => 'tat_legal_last_name'
            ],
            [
                'type' => 'text',
                'label' => 'Fursona Name',
                'required' => TRUE,
                'user_meta_key' => 'tat_fursona_name'
            ],
            [
                'type' => 'radiobutton',
                'label' => 'Would you prefer your Fursona Name to be used in Club e-mails?',
                'values' => [
                    'Yes',
                    'No'
                ],
                'user_meta_key' => 'tat_use_fursona_name_in_club_emails'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'Email',
                    'This is super important.',
                    'This email address will be used to send you legally mandated notifications like upcoming votes on vacant Board seats or other issues that we\'re required, by law, to inform you of.',
                    'Please make sure this email address is monitored.',
                    'You may want to allow-list the TiesAndTails.Club  domain and the alternate email: TiesAndTailsClub@gmail.com',
                ]
            ],            
            [
                'type' => 'email',
                'label' => 'Monitored Email',
                'required' => TRUE,
                'user_meta_key' => 'tat_monitored_email'
            ],
            [
                'type' => 'bodytext',
                'text' =>  [
                    'What other kinds of emails is it ok to send you at this address?'
                ]
            ],
            [
                'type' => 'checkbox',
                'label' => 'Restaurant Outings',
                'user_meta_key' => 'tat_outings_emails_ok'
            ],
            [
                'type' => 'checkbox',
                'label' => 'Seasonal Events',
                'user_meta_key' => 'tat_event_emails_ok'
            ],
            [
                'type' => 'checkbox',
                'label' => 'Club Newsletter',
                'user_meta_key' => 'tat_newsletter_emails_ok'
            ],
            [
                'type' => 'checkbox',
                'label' => 'New - Club Shop Listings',
                'user_meta_key' => 'tat_shop_emails_ok'
            ]
*/
        ];
    }

    public static function render_membership_form_for_my_account() {
        $form_elements = self::get_form_elements();
        $current_user_id = get_current_user_id();
        $things_that_need_fixing = [];

        // Check for form posting and process it
        if ( isset ( $_POST['pandamusrex_memberships_member_form_nonce'] ) ) {
            $nonce = sanitize_text_field( wp_unslash( $_POST['pandamusrex_memberships_member_form_nonce'] ) );
            if ( ! wp_verify_nonce( $nonce, 'pandamusrex_memberships_member_form_nonce' ) ) {
                $things_that_need_fixing[] = "Bad nonce. Form submittal rejected.";
            } else {
                // Validate the POST contents
                foreach ( $form_elements as $element ) {
                    $type = "";
                    if ( array_key_exists( 'type', $element ) ) {
                        $type = $element[ 'type' ];
                    }

                    if ( empty( $type ) ) {
                        continue;
                    }

                    $key = "";
                    if ( array_key_exists( 'user_meta_key', $element ) ) {
                        $key = $element[ 'user_meta_key' ];
                    }

                    if ( empty( $key ) ) {
                        continue;
                    }

                    $required = FALSE;
                    if ( array_key_exists( 'required', $element ) ) {
                        $required = $element[ 'required' ];
                    }

                    $label = "(Missing label in form)";
                    if ( array_key_exists( 'label', $element ) ) {
                        $label = $element[ 'label' ];
                    }

                    if ( ! array_key_exists( $key, $_POST ) && $required ) {
                        $things_that_need_fixing[] = "Required: " . $label;
                        continue;
                    }

                    $new_value = sanitize_text_field( $_POST[ $key ] );
                    $new_value = trim( $new_value );
                    if ( empty( $new_value ) && $required ) {
                        $things_that_need_fixing[] = "Non-empty value required: " . $label;
                        continue;
                    }

                    if ( $type == "email") {
                        if ( ! is_email( $new_value ) ) {
                            $things_that_need_fixing[] = "Valid email required: " . $label;
                            continue;
                        }
                    }
                } // foreach, validation
            }

            if ( ! empty( $things_that_need_fixing ) ) {
                // Display anything the user needs to fix
                foreach ( $things_that_need_fixing as $thing_that_needs_fixing ) {
                    echo "<p class='pandamusrex-tiesandtails-error'>";
                    echo $thing_that_needs_fixing;
                    echo "</p>";
                }
            } else {
                // Everything is OK. Save it.
                foreach ( $form_elements as $element ) {
                    $type = "";
                    if ( array_key_exists( 'type', $element ) ) {
                        $type = $element[ 'type' ];
                    }

                    if ( empty( $type ) ) {
                        continue;
                    }

                    $key = "";
                    if ( array_key_exists( 'user_meta_key', $element ) ) {
                        $key = $element[ 'user_meta_key' ];
                    }

                    if ( empty( $key ) ) {
                        continue;
                    }

                    if ( array_key_exists( $key, $_POST ) ) {
                        $new_value = sanitize_text_field( $_POST[ $key ] );
                        $new_value = trim( $new_value );

                        // Checkbox? Store TRUE instead
                        if ( $type == 'checkbox' ) {
                            $new_value = 'TRUE';
                        }

                        update_user_meta( $current_user_id, $key, $new_value );
                    } else {
                        // Not present? Checkbox? Delete the meta value
                        if ( $type == 'checkbox' ) {
                            delete_user_meta( $current_user_id, $key );
                        }
                    }
                } // foreach, saving meta

                echo "<p class='pandamusrex-tiesandtails-success'>";
                echo "Form changes applied successfully";
                echo "</p>";
            }
        }

        self::render_form_elements();
    }

    public static function render_membership_form_for_checkout() {
        // TODO set a class to prompt JS to manage checking and POST
        self::render_form_elements( [ 'omit-submit-button' ] );
    }

    public static function render_form_elements( $options = [] ) {
        $form_elements = self::get_form_elements();

        echo "<form method='post'>";
        echo '<input type="hidden" name="pandamusrex_memberships_member_form_nonce" id="pandamusrex_memberships_member_form_nonce" value="' . esc_attr( wp_create_nonce( 'pandamusrex_memberships_member_form_nonce' ) ) . '" />';

        // Render form contents
        foreach ( $form_elements as $element ) {
            $type = $element['type'];
            if ($type == 'heading') {
                $text = $element['text'];
                echo "<h2>";
                echo esc_html($text);
                echo "</h2>";
                continue;
            }

            if ($type == 'bodytext') {
                $text = $element['text'];
                if (! is_array( $text )) {
                    echo "<p>";
                    echo esc_html($text);
                    echo "</p>";
                } else {
                    foreach ($text as $line) {
                        echo "<p>";
                        echo esc_html($line);
                        echo "</p>";
                    }
                }
                continue;
            }

            $key = "";
            if ( array_key_exists( 'user_meta_key', $element ) ) {
                $key = $element[ 'user_meta_key' ];
            }

            if ($type == 'checkbox') {
                // If we were handing a POST, use its value, otherwise use meta
                if ( empty( $key ) ) {
                    $value = FALSE;
                }
                elseif ( ! empty( $_POST ) ) {
                    $value = array_key_exists( $key, $_POST );
                } else {
                    $value = get_user_meta( $current_user_id, $key, FALSE );
                }

                $label = $element['label'];
                $key = $element['user_meta_key'];
                $required = array_key_exists('required', $element);
                $checked = $value ? 'CHECKED' : '';
                echo "<p>";
                echo "<input type='checkbox' id='" . esc_attr( $key ) . "' name='" . esc_attr( $key ) . "'value='". esc_attr($label) ."' " . esc_attr($checked) . ">";
                echo "<label for='" . esc_attr( $key ) . "'>" . esc_html($label) . "</label>";
                if ( $required ) {
                    echo " <font color='red'>*</font>";
                }
                echo "</p>";
                continue;
            }

            // If we have something in POST, use it, otherwise use meta
            if ( empty( $key ) ) {
                $value = '';
            }
            elseif ( ! empty( $_POST ) ) {
                $value = '';
                if ( array_key_exists( $key, $_POST ) ) {
                    $value = sanitize_text_field( $_POST[ $key ] );
                    $value = trim( $value );
                }
            } else {
                $value = get_user_meta( $current_user_id, $key, '' );
            }

            if ($type == 'text') {
                $label = $element['label'];
                $key = $element['user_meta_key'];
                $required = array_key_exists('required', $element);
                $initial_value = get_user_meta( $current_user_id, $key, TRUE );
                echo "<p>";
                echo "<label for='" . esc_attr( $key ) . "'>" . esc_html($label) . "</label>";
                if ( $required ) {
                    echo " <font color='red'>*</font>";
                }
                echo "<br/>";
                echo "<input type='text' id='" . esc_attr( $key ) . "' name='" . esc_attr( $key ) . "'value='". esc_attr($initial_value) ."'>";
                echo "</p>";
                continue;
            }

            if ($type == 'email') {
                $label = $element['label'];
                $key = $element['user_meta_key'];
                $required = array_key_exists('required', $element);
                $initial_value = get_user_meta( $current_user_id, $key, TRUE );
                echo "<p>";
                echo "<label for='" . esc_attr( $key ) . "'>" . esc_html($label) . "</label>";
                if ( $required ) {
                    echo " <font color='red'>*</font>";
                }
                echo "<br/>";
                echo "<input type='text' id='" . esc_attr( $key ) . "' name='" . esc_attr( $key ) . "'value='". esc_attr($initial_value) ."'>";
                echo "</p>";
                continue;
            }

            if ( $type == 'radiobutton' ) {
                $label = $element['label'];
                echo "<p>";
                echo "<label for='" . esc_attr( $key ) . "'>" . esc_html($label) . "</label>";
                echo "<br/>";
                $values = $element['values'];
                $current_value = get_user_meta( $current_user_id, $key, TRUE );
                if ( empty( $current_value ) ) {
                    $current_value = $values[0];
                }
                foreach ($values as $value) {
                    $checked = ( $current_value == $value ) ? 'CHECKED' : '';
                    echo "<input type='radio' name='" . esc_attr( $key ) . "'value='". esc_attr($value) ."' " . esc_attr($checked) . ">";
                    echo "<label for='" . esc_attr( $key ) . "'>";
                    echo esc_html($value);
                    echo "</label><br/>";
                }
                echo "</p>";
                continue;
            }
        }

        if ( ! in_array( 'omit-submit-button', $options ) ) {
            echo "<p>";
            echo "Once you hit Submit you\'re all done!";
            echo "</p>";
            echo "<input type='submit' id='pandamusrex_memberships_membership_form_submit' value='Submit'>";
        }

        echo "</form>";
    }
}

PandamusRex_TiesAndTails_Member_Form::get_instance();
