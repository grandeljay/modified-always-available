<?php

/**
 * Always Available
 *
 * @author  Jay Trees <always-available@grandels.email>
 * @link    https://github.com/grandeljay/modified-always-available
 */

namespace Grandeljay\AlwaysAvailable;

$required_modules = [
    Constants::MODULE_CATEGORIES_NAME,
    Constants::MODULE_PRODUCT_NAME,
    Constants::MODULE_SHOPPING_CART_NAME,
];

foreach ($required_modules as $required_module) {
    if (\rth_is_module_disabled($required_module)) {
        return;
    }
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
<div class="clear div_box mrg5" style="margin-top: 20px;">
    <table class="tableInput border0 always-available">
        <thead>
            <tr>
                <th colspan="2">Immer verfügbar</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td colspan="2">
                    <?php
                    $parameters_product       = \http_build_query(
                        [
                            'set'    => 'product',
                            'module' => \grandeljay_always_available_product::class,
                        ]
                    );
                    $parameters_categories    = \http_build_query(
                        [
                            'set'    => 'categories',
                            'module' => \grandeljay_always_available_categories::class,
                        ]
                    );
                    $parameters_shopping_cart = \http_build_query(
                        [
                            'set'    => 'shopping_cart',
                            'module' => \grandeljay_always_available_shopping_cart::class,
                        ]
                    );
                    $link_product             = \sprintf(
                        '<a href="%s">[%s]</a>',
                        \xtc_href_link(\FILENAME_MODULES, $parameters_product),
                        'product'
                    );
                    $link_categories          = \sprintf(
                        '<a href="%s">[%s]</a>',
                        \xtc_href_link(\FILENAME_MODULES, $parameters_categories),
                        'categories'
                    );
                    $link_shopping_cart       = \sprintf(
                        '<a href="%s">[%s]</a>',
                        \xtc_href_link(\FILENAME_MODULES, $parameters_shopping_cart),
                        'shopping_cart'
                    );

                    echo \sprintf(
                        'Diese Einstellungen kommen vom Modul %s %s %s %s.',
                        '<em>' . 'Immer verfügbar' . '</em>',
                        $link_product,
                        $link_categories,
                        $link_shopping_cart,
                    );
                    ?>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="always-available">
                        <?php
                        echo \xtc_draw_checkbox_field('grandeljay_always_available[always_available]', true, $always_available) . ' ' . 'Artikel ist immer verfügbar';
                        ?>
                    </label>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<?php
