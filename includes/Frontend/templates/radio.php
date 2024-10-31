<?php

defined( 'ABSPATH' ) || exit;

foreach ( $type->values as $id => $value ) {
	if ( $value->hide ) {
		continue;
	} ?>
    <div class="zaddon_radio zaddon_option">
        <label>
            <input
                type="radio"
				<?= $value->checked ? "checked" : "" ?>
                <?php if ( $id === 0 && $type->required ) { ?>required<?php } ?>
                name="<?= esc_attr( $name ) ?>[value]"
                value="<?= esc_attr( $value->getID() ) ?>"
                data-price="<?= esc_attr( $value->price ) ?>"
                data-type="<?= esc_attr( $type->type ) ?>"
				<?php do_action( 'zaddon_input_property', $type ); ?>
            />
			<?php do_action( 'zaddon_before_option_title', $value, $name ); ?>
            <span class="zaddon-radio-title">
                <span class="zaddon_title"><?= esc_html( $value->title ) ?></span>
				<?= wp_kses_post( $value->price ? '(' . apply_filters( 'zaddon_option_format_price', wc_price( $value->price ), $type, $value ) . ')' : "" ) ?>
				<?php
				if ( ! $value->hide_description  && ! empty( $value->description ) ) {
					if ( $value->tooltip_description ) {
						?>
                        <span class="za-tooltip">
                            <button class="za-tooltip__control" type="button" aria-label="<?php echo esc_attr( $value->description ) ?>"></button>
                            <span class="za-tooltip__body"><?php echo esc_html( $value->description ) ?></span>
                        </span>
						<?php
					} else {
						?>
                        <p class="zaddon-option-description"><?php echo esc_html( $value->description ) ?></p>
						<?php
					}
				}
				?>
            </span>
        </label>
		<?php do_action( 'zaddon_after_option_input', $value, $name ); ?>
    </div>
<?php } ?>
