<?php

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

echo Fns::rtFieldGenerator( Options::rtTPGPostType() );
$sHtml = null;
$sHtml .= '<div class="field-holder rt-tpg-field-group">';
$sHtml .= '<div class="field-label">Common Filters</div>';
$sHtml .= '<div class="field">';
$sHtml .= Fns::rtFieldGenerator( Options::rtTPGCommonFilterFields() );
$sHtml .= '</div>';
$sHtml .= '</div>';

echo $sHtml;

?>

<div class='rt-tpg-filter-container rt-tpg-field-group'>
	<?php echo Fns::rtFieldGenerator( Options::rtTPAdvanceFilters() ); ?>
    <div class="rt-tpg-filter-holder">
		<?php
		$html       = null;
		$pt         = get_post_meta( $post->ID, 'tpg_post_type', true );
		$advFilters = Options::rtTPAdvanceFilters();
		echo $html;
		?>
    </div>
</div>

<div class="rt-tpg-field-group">
	<?php echo Fns::rtFieldGenerator( Options::stickySettings() ); ?>
</div>