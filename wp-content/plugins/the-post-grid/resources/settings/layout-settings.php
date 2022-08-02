<?php

use RT\ThePostGrid\Helpers\Fns;
use RT\ThePostGrid\Helpers\Options;

echo Fns::rtFieldGenerator(Options::rtTPGLayoutSettingFields());
echo '<div class="rd-responsive-column">';
echo Fns::rtFieldGenerator(Options::responsiveSettingsColumn());
echo '</div>';
echo Fns::rtFieldGenerator(Options::layoutMiscSettings());
