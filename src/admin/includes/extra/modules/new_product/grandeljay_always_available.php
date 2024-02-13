<?php

/**
 * Always Available
 *
 * @author  Jay Trees <always-available@grandels.email>
 * @link    https://github.com/grandeljay/modified-always-available
 */

namespace Grandeljay\AlwaysAvailable;

if (\rth_is_module_disabled(Constants::MODULE_CATEGORIES_NAME) || \rth_is_module_disabled(Constants::MODULE_PRODUCT_NAME)) {
    return;
}

if (!isset($_GET['pID'])) {
    return;
}

$always_available_query = \xtc_db_query(
    sprintf(
        'SELECT *
           FROM `%s`
          WHERE `products_id` = %d',
        Constants::TABLE_ALWAYS_AVAILABLE,
        $_GET['pID']
    )
);
$always_available_data  = $always_available_query ? \xtc_db_fetch_array($always_available_query) : null;
$always_available       = $always_available_data['always_available'] ?? false;

?>
<table class="tableInput border0">
    <thead>
        <tr>
            <th colspan="2">Immer verfügbar</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="main">
                <label class="always-available">
                    <?php
                    echo \xtc_draw_checkbox_field('grandeljay_always_available[always_available]', true, $always_available) . ' ' . 'Artikel ist immer verfügbar';
                    ?>
                </label>
            </td>
        </tr>
    </tbody>
</table>

<?php
