<?php

namespace ZAddons\Admin;

defined( 'ABSPATH' ) || exit;

use ZAddons\Addons;
use ZAddons\Admin;
use ZAddons\Model\AddOn;
use ZAddons\Model\Group;
use ZAddons\Plugin;
use const ZAddons\PLUGIN_ROOT_FILE;
use const ZAddons\REST_NAMESPACE;

class ListGroup {
	private $groups_page;

	public function __construct() {
		add_action( 'admin_menu', [ $this, 'admin_menu' ], 1000 );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
	}

	public function admin_menu() {
		$this->groups_page = add_submenu_page(
			'edit.php?post_type=product',
			__( 'Add-on Groups', 'product-add-ons-woocommerce' ),
			__( 'Add-on Groups', 'product-add-ons-woocommerce' ),
			'manage_woocommerce',
			'za_groups',
			[ $this, 'process' ]
		);
	}

	public function admin_scripts() {
		if ( get_current_screen()->base === $this->groups_page ) {
			wp_enqueue_script( 'za_groups', plugins_url( 'assets/scripts/adminGroups.js', \ZAddons\PLUGIN_ROOT_FILE ),
				[ 'zAddons', 'wp-i18n' ], ZA_VERSION );
			wp_set_script_translations( 'za_groups', 'product-add-ons-woocommerce',
				plugin_dir_path( PLUGIN_ROOT_FILE ) . 'lang' );

			$data = [
				'SITE_URL'              => esc_url_raw( get_site_url() ),
				'isCheckoutAddOnActive' => Addons::is_active_add_on( Addons::CHECKOUT_ADDON_NAMESPACE ),
				'WC_REST'               => esc_url_raw( rest_url( '/' . REST_NAMESPACE . '/' ) ),
				'WC_NONCE'              => wp_create_nonce( 'wp_rest' ),
			];

			wp_localize_script( 'za_groups', 'ZAddons', $data );
		}
		wp_enqueue_style( 'za_admin.css', plugins_url( 'assets/styles/admin.css', \ZAddons\PLUGIN_ROOT_FILE ), [], ZA_VERSION );
		wp_enqueue_style( 'za_fa.css', plugins_url( 'assets/styles/fa.css', \ZAddons\PLUGIN_ROOT_FILE ), null, ZA_VERSION );
	}

	public function process() {
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] === 'POST' && method_exists( $this, 'update' ) ) {
			$this->update();
		} else {
			$this->render();
		}
	}

	protected function render() {
		$groups     = Group::getAll();
		$groups     = array_map( function ( Group $group ) {
			return $group->getData();
		}, $groups );
		$active_tab = isset( $_GET['tab'] ) ? sanitize_text_field( wp_unslash( $_GET['tab'] ) ) : 'groups';
		$page_data  = compact( 'groups' );
		?>
        <div class="wrap">
            <h1 class="nav-tab-wrapper woo-nav-tab-wrapper">
                <a href="<?= esc_attr( Admin::getUrl( 'groups' ) ); ?>"
                   class="nav-tab <?= esc_attr( self::get_tab_active_class( $active_tab, 'groups' ) ); ?>">
					<?php esc_html_e( 'Groups', 'product-add-ons-woocommerce' );
					?>
                </a>
				<?php do_action( 'zaddon_admin_tab', $active_tab ) ?>
                <a href="<?= esc_attr( Admin::getUrl( 'add-ons' ) ); ?>"
                   class="nav-tab <?= esc_attr( self::get_tab_active_class( $active_tab, 'add-ons' ) ); ?>">
					<?php esc_html_e( 'Add-Ons', 'product-add-ons-woocommerce' ); ?>
                </a>
                <a href="<?= esc_attr( ( new Group() )->getLink() ); ?>" class="alignright page-title-action">
					<?php echo esc_html( _x( 'Add new', 'Add new group', 'product-add-ons-woocommerce' ) ); ?>
                </a>
            </h1>
			<?php
			if ( 'settings' === $active_tab ) :
				?>
                <ul class="zaddon-subsubsub subsubsub" style="float: none">
					<?php do_action( 'zaddon_admin_tab_before_content', $active_tab ); ?>
                </ul>
			<?php
			endif;
			?>
			<?php
			if ( 'groups' === $active_tab ) : ?>
                <div id="react-root"></div>
                <script>
                    renderGroups(<?php echo wp_kses_post( json_encode( $page_data ) ); ?>, document.getElementById("react-root"));
                </script>
			<?php elseif ( 'add-ons' === $active_tab ) : $this->add_ons_render(); ?>
			<?php else : ?>
            <form action="<?= esc_attr( admin_url( 'options.php' ) ); ?>" method="post">
				<?php
                $is_checkout = ( isset( $_GET['subtab'] ) && 'checkout' === $_GET['subtab'] ) || ( ! isset( $_GET['subtab'] ) && ! Addons::is_active_add_on( Addons::CUSTOMIZE_ADDON_NAMESPACE, true ) );
                if ( $is_checkout ) echo '<div class="zaddon-labels-no-wrap">';
				settings_fields( $is_checkout ? 'za_groups_checkout' : $active_tab );
				do_settings_sections( 'za_groups' );
				submit_button();
				if ( $is_checkout ) echo '</div>';
				endif;
				?>
            </form>
        </div>
		<?php
	}

	public static function get_tab_active_class( $active_tab, $current_tab ) {
		return $active_tab === $current_tab ? 'nav-tab-active' : '';
	}

	public static function get_subtab_active_class( $active_subtab, $current_subtab ) {
		if ( Addons::is_active_add_on( Addons::CUSTOMIZE_ADDON_NAMESPACE ) && ! isset( $_GET['subtab'] ) ) {
			$active_subtab = 'plus';
		} elseif ( ! isset( $_GET['subtab'] ) ) {
			$active_subtab = $current_subtab;
        }
		return $active_subtab === $current_subtab ? 'active' : '';
	}

	public function add_ons_render() {
		$add_ons = Addons::get_all_add_ons();
		?>
        <div class="plugins-area">
            <h2 class="section-header"><i
                        class="fal fa-plus-circle section-header-icon"></i><?php esc_html_e( 'Add more functionality',
					'product-add-ons-woocommerce' ); ?></h2>
            <div class="services-wrapper">
				<?php
				foreach ( $add_ons as $key => $plugin ) {
					?>
                    <div class="card-box-plugin" id="<?= esc_attr( $key ) ?>">
                        <div class="card-box-header">
							<?= wp_kses_post( $plugin->getTitle() ); ?>
                        </div>
                        <div class="card-box-description">
							<?= wp_kses_post( $plugin->getDescription() ); ?>
                        </div>
                        <div class="card-box-footer">
                            <div class="card-box-left-footer">
								<?php
								if ( ! Addons::is_active_add_on( $plugin->getNamespace() ) ) {
									?>
                                    <span class="dot dot-enable"></span> <span>
																		<a href="<?= esc_attr( admin_url( 'plugins.php' ) ) ?>"><?php esc_html_e( 'Enable',
																				'product-add-ons-woocommerce' ); ?></a>
																</span>
									<?php
								} else {
									?>
                                    <span class="dot dot-active"></span>
                                    <span><?php esc_html_e( 'Active', 'product-add-ons-woocommerce' ); ?></span>
									<?php
								}
								?>
                            </div>
                            <div class="card-box-right-footer">
                                <a href="<?= esc_html( $plugin->getLink() ); ?>"><?php esc_html_e( 'More info',
										'product-add-ons-woocommerce' ); ?></a>
                            </div>
                        </div>
                    </div>
				<?php } ?>
            </div>
            <hr>
            <h2 class="section-header"><i
                        class="fal fa-compass section-header-icon"></i><?php esc_html_e( 'Explore more products, platforms and services',
					'product-add-ons-woocommerce' ); ?></h2>
            <div class="services-wrapper">
                <div class="card-box-plugin">
                    <div class="card-box-service-header">
                        <i class="fab fa-wordpress-simple card-box-header-icon"></i>
                        <br/>
						<?php esc_html_e( 'WordPress.org', 'product-add-ons-woocommerce' ); ?>
                    </div>
                    <div class="card-box-description"><?php esc_html_e( 'Free plugin apps for the open source community',
							'product-add-ons-woocommerce' ); ?></div>
                    <div class="card-box-footer-center">
                        <a href="https://wordpress.org/plugins/search/bizswoop"><?php esc_html_e( 'Explore Free Apps',
								'product-add-ons-woocommerce' ); ?></a>
                    </div>
                </div>
                <div class="card-box-plugin">
                    <div class="card-box-service-header">
                        <i class="fal fa-cubes card-box-header-icon"></i>
                        <br/>
						<?php esc_html_e( 'Premium Plugins', 'product-add-ons-woocommerce' ); ?>
                    </div>
                    <div class="card-box-description"><?php esc_html_e( 'Smart plugin apps for your advanced business requirements',
							'product-add-ons-woocommerce' ); ?></div>
                    <div class="card-box-footer-center">
                        <a href="https://www.bizswoop.com/wp"><?php esc_html_e( 'Explore All Apps',
								'product-add-ons-woocommerce' ); ?></a>
                    </div>
                </div>
                <div class="card-box-plugin">
                    <div class="card-box-service-header">
                        <i class="fal fa-window card-box-header-icon"></i>
                        <br/>
						<?php esc_html_e( 'Powerful Platforms', 'product-add-ons-woocommerce' ); ?>
                    </div>
                    <div class="card-box-description"><?php esc_html_e( 'Advanced platforms for agencies, developers and businesses',
							'product-add-ons-woocommerce' ); ?></div>
                    <div class="card-box-footer-center">
                        <a href="https://www.bizswoop.com/platforms"><?php esc_html_e( 'Explore Platforms',
								'product-add-ons-woocommerce' ); ?></a>
                    </div>
                </div>
                <div class="card-box-plugin">
                    <div class="card-box-service-header">
                        <i class="fal fa-feather card-box-header-icon"></i>
                        <br/>
						<?php esc_html_e( 'Super Services', 'product-add-ons-woocommerce' ); ?>
                    </div>
                    <div class="card-box-description"><?php esc_html_e( 'High-touch services to boost your business technology solutions',
							'product-add-ons-woocommerce' ); ?></div>
                    <div class="card-box-footer-center">
                        <a href="https://www.bizswoop.com/services/"><?php esc_html_e( 'Explore Services',
								'product-add-ons-woocommerce' ); ?></a>
                    </div>
                </div>
                <div class="card-box-plugin">
                    <div class="bzswp-box">
                        <img class="bzswp-box-logo" src="<?= esc_attr( Plugin::getUrl( 'assets/images/admin/bizswoop.png' ) ); ?>"
                             alt="bizswoop">
                        <h2>BizSwoop</h2>
                        <p class="bzswp-description"><?php esc_html_e( 'Your life`s work, our technology',
								'product-add-ons-woocommerce' ); ?></p>
                    </div>

                    <div class="card-box-footer-center">
                        <a href="https://www.bizswoop.com"><?php esc_html_e( 'Visit Us', 'product-add-ons-woocommerce' ); ?></a>
                    </div>
                </div>
            </div>
        </div>
		<?php
	}
}
