<?php

defined( 'ABSPATH' ) || exit;

$at_least_one_have_tooltip_description = false;
?>
<div class="zaddon_select zaddon_option">
    <select
        name="<?= esc_attr( $name ) ?>[value]"
        data-type="<?= esc_attr( $type->type ) ?>"
		<?php do_action( 'zaddon_input_property', $type ); ?>
    >
        <option value="" class="zaddon_select_label">
			<?php echo wp_kses_post( \ZAddons\get_customize_addon_option( 'zac_select_drop_down_label', __( 'Choose an option', 'product-add-ons-woocommerce' ) ) ) ?>
        </option>
		<?php foreach ( $type->values as $value ) {
			if ( $value->hide ) {
				continue;
			}
			if ( $value->tooltip_description && ! empty( $value->description ) ) {
				$at_least_one_have_tooltip_description = true;
			}
			?>
            <option
                value="<?= esc_attr( $value->getID() ) ?>"
                data-title="<?= esc_attr( $value->title ) ?>"
                data-price="<?= esc_attr( $value->price ) ?>"
                data-description="<?= esc_attr( ! $value->hide_description && ! empty( $value->description ) ? $value->description : '' ) ?>"
                data-is-tooltip-description="<?= esc_attr( $value->tooltip_description ) ?>"
				<?php do_action( 'zaddon_add_select_option_property', $value ); ?>
				<?= $value->checked ? "selected" : "" ?>
            >
				<?= esc_html( $value->title ) ?>
				<?= wp_kses_post( $value->price ? '(' . apply_filters( 'zaddon_option_format_price', wc_price( $value->price ), $type, $value ) . ')' : "" ) ?>
            </option>
		<?php } ?>
    </select>
	<?php
	if ( $at_least_one_have_tooltip_description ) {
		?>
        <span class="za-tooltip" style="display: none;">
            <button class="za-tooltip__control" type="button" aria-label=""></button>
            <span class="za-tooltip__body"></span>
        </span>
		<?php
	}
	?>
</div>
