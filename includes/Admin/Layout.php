<?php

namespace ZAddons\Admin;

defined( 'ABSPATH' ) || exit;

use ZAddons\Addons;use ZAddons\Admin;
use ZAddons\Plugin;

class Layout
{
	public function __construct()
	{
		add_action('in_admin_header', [$this, 'pageHeader']);
	}

	public function pageHeader()
	{
		if (!self::isActive()) {
			return;
		} ?>
				<div class="zaddon-layout-wrapper">
						<div class="zaddon-layout">
								<div class="zaddon-base">
										<a href="https://www.bizswoop.com/wp/product-management" target="_blank">
												<img
													class="zaddon-logo"
													src="<?= esc_attr( Plugin::getUrl('assets/images/admin/logo.png') ); ?>"
													alt="Product Add-ons"
												>
										</a>
										<div class="zaddon-title">
												<a href="https://www.bizswoop.com/wp/product-management" target="_blank">
														<?php esc_html_e('Product Add-ons', 'product-add-ons-woocommerce') ?>
												</a>
										</div>
										<div class="zaddon-slogan">
												<?php esc_html_e('Advanced Customizations', 'product-add-ons-woocommerce') ?>
										</div>
								</div>
								<div class="zaddon-navigation">
										<ul>
												<li>
														<a
															href="<?= esc_attr( Admin::getUrl('groups') ); ?>"
															class="<?= esc_attr( self::isActiveClass('groups') ); ?>"
														>
																<div class="zaddon-icon">
																		<i class="fal fa-th-large"></i>
																</div>
																<?php esc_html_e('Groups', 'product-add-ons-woocommerce') ?>
														</a>
												</li>
												<?php if (Addons::is_active_add_on(Addons::CUSTOMIZE_ADDON_NAMESPACE, true) || Addons::is_active_add_on(Addons::CHECKOUT_ADDON_NAMESPACE, true)) : ?>
												<li>
														<a
															href="<?= esc_attr( Admin::getUrl('settings') ); ?>"
															class="<?= esc_attr( self::isActiveClass('settings') ); ?>"
														>
																<div class="zaddon-icon">
																		<i class="fal fa-cog"></i>
																</div>
																<?php esc_html_e('Settings', 'product-add-ons-woocommerce') ?>
														</a>
												</li>
												<?php endif; ?>
												<li>
														<a
															href="<?= esc_attr( Admin::getUrl('add-ons') ); ?>"
															class="<?= esc_attr( self::isActiveClass('add-ons') ); ?>"
														>
																<div class="zaddon-icon">
																		<i class="far fa-cubes"></i>
																</div>
																<?php esc_html_e('Add-ons', 'product-add-ons-woocommerce') ?>
														</a>
												</li>
												<li>
														<a href="http://bizswoop.com/" target="_blank">
																<div class="zaddon-icon">
																		<img
																			src="<?= esc_attr( Plugin::getUrl('assets/images/admin/bizswoop.png') ); ?>"
																			alt="BizSwoop">
																</div>
																BizSwoop
														</a>
												</li>
										</ul>
								</div>
						</div>
				</div>
				<?php
	}

	public static function isActive()
	{
			return \get_current_screen()->id === 'product_page_za_groups';
	}

	public static function isActiveClass($tag)
	{
			$is_active = isset($_GET['tab']) && $_GET['tab'] === $tag || !isset($_GET['tab']) && $tag === 'groups';
			return $is_active ? 'zaddon-active-link' : '';
	}
}
