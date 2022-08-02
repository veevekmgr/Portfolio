<?php

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

echo Fns::rtFieldGenerator( Options::rtTPGStyleFields() ); ?>

<div class="field-holder button-color-style-wrapper">
    <div class="field-label"><?php _e( 'Button Color', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo Fns::rtFieldGenerator( Options::rtTPGStyleButtonColorFields() ); ?>
        </div>
    </div>
</div>

<div class="field-holder widget-heading-stle-wrapper">
    <div class="field-label"><?php _e( 'ShortCode Heading', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo Fns::rtFieldGenerator( Options::rtTPGStyleHeading() ); ?>
        </div>
    </div>
</div>

<div class="field-holder full-area-style-wrapper">
    <div class="field-label"><?php _e( 'Full Area / Section', 'the-post-grid' ); ?></div>
    <div class="field">
        <div class="tpg-multiple-field-group">
			<?php echo Fns::rtFieldGenerator( Options::rtTPGStyleFullArea() ); ?>
        </div>
    </div>
</div>

<?php do_action( 'rt_tpg_sc_style_group_field' ); ?>

<?php echo Fns::rtSmartStyle( Options::extraStyle() ); ?>


