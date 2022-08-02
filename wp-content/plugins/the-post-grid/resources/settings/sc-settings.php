<?php
use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

?>
<div class="field-holder">
    <div class="field-label"><?php _e('ShortCode Heading', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCHeadingSettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label">
        <label><?php esc_html_e('Category', 'the-post-grid'); ?></label>
    </div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCCategorySettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label"><?php _e('Title', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCTitleSettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label"><?php _e('Meta', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCMetaSettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label"><?php _e('Image', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCImageSettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label"><?php _e('Excerpt', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCExcerptSettings()); ?>
    </div>
</div>
<div class="field-holder">
    <div class="field-label"><?php _e('Read More Button', 'the-post-grid'); ?></div>
    <div class="field">
        <?php echo Fns::rtFieldGenerator(Options::rtTPGSCButtonSettings()); ?>
    </div>
</div>