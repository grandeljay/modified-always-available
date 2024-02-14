<?php

/**
 * Always Available
 *
 * @author  Jay Trees <always-available@grandels.email>
 * @link    https://github.com/grandeljay/modified-always-available
 */

namespace Grandeljay\AlwaysAvailable;

use Grandeljay\Translator\Translations;

$translations = new Translations(__FILE__, Constants::MODULE_CATEGORIES_NAME);
$translations->add('TITLE', 'grandeljay - Siempre disponible');
$translations->add('TEXT_TITLE', 'Siempre disponible');

$translations->define();
