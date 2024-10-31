<?php

defined( 'ABSPATH' ) || exit;

foreach ( $type->values as $value ) {
	if ( $value->hide ) {
		continue;
	}
	?>
    <div class="zaddon_text zaddon_option">
        <label class="zaddon-flex" for="zaddon_<?= esc_attr(  $value->getID() ) ?>">
			<?php do_action( 'zaddon_before_option_title', $value, $name ); ?>
			<span class="zaddon_title"><?= wp_kses_post( $value->title ) ?></span> <?= wp_kses_post( $value->price ? '(' . apply_filters( 'zaddon_option_format_price', wc_price( $value->price ), $type, $value ) . ')' : "" ) ?>
			<?php
			if ( $value->tooltip_description && ! $value->hide_description && ! empty( $value->description ) ) {
				?>
                <span class="za-tooltip">
                    <button class="za-tooltip__control" type="button" aria-label="<?php echo esc_attr( $value->description ) ?>"></button>
                    <span class="za-tooltip__body"><?php echo esc_html( $value->description ) ?></span>
                </span>
				<?php
			}
			?>
        </label>
        <input
            type="text"
			<?= isset( $value->required ) && $value->required ? "required" : "" ?>
            id="zaddon_<?= esc_attr( $value->getID() ) ?>"
            name="<?= esc_attr( $name ) ?>[value][<?= esc_attr( $value->getID() ) ?>]"
            data-price="<?= esc_attr( $value->price ) ?>"
            data-type="<?= esc_attr( $type->type ) ?>"
			<?php do_action( 'zaddon_input_property', $type ); ?>
        />
		<?php do_action( 'zaddon_after_option_input', $value, $name ); ?>
		<?= esc_html( ! $value->hide_description && ! $value->tooltip_description && ! empty( $value->description ) ? '<p class="zaddon-option-description">' . $value->description . '</p>' : "" ) ?>
    </div>
<?php } ?>
