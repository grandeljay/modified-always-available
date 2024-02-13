<?php

/**
 * Always Available
 *
 * @author  Jay Trees <always-available@grandels.email>
 * @link    https://github.com/grandeljay/modified-always-available
 *
 * @phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
 * @phpcs:disable Squiz.Classes.ValidClassName.NotCamelCaps
 */

use Grandeljay\AlwaysAvailable\Constants;
use RobinTheHood\ModifiedStdModule\Classes\StdModule;

class grandeljay_always_available_shopping_cart extends StdModule
{
    public const VERSION = Constants::MODULE_VERSION;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_SHOPPING_CART_NAME);

        $this->checkForUpdate(true);
    }

    protected function updateSteps(): int
    {
        if (version_compare($this->getVersion(), self::VERSION, '<')) {
            $this->setVersion(self::VERSION);

            return self::UPDATE_SUCCESS;
        }

        return self::UPDATE_NOTHING;
    }

    public function install(): void
    {
        parent::install();

        xtc_db_query(
            sprintf(
                'CREATE TABLE `%s` (
                    `products_id`      INT        NOT NULL,
                    `always_available` TINYINT(1) NOT NULL,
                    `last_modified`    DATETIME   NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
                    PRIMARY KEY (`products_id`)
                );',
                Constants::TABLE_ALWAYS_AVAILABLE
            )
        );
    }

    public function remove(): void
    {
        parent::remove();

        xtc_db_query(
            sprintf(
                'DROP TABLE `%s`',
                Constants::TABLE_ALWAYS_AVAILABLE
            )
        );
    }

    /**
     * Extends the modified-shop `shopping_cart` method `get_products`.
     *
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     *
     * @param array  $product_info The product information, slightly manipulated
     *                             since retrieving it from the database.
     * @param array  $product_data The product's data from the database.
     * @param array  $contents     The cart's products, stored as ids.
     *
     * @return array
     */
    public function get_products(array $product_info, array $product_data, array $contents): array
    {
        global $main;

        $shipping_status_id_available = 2;

        if ('true' !== \ACTIVATE_SHIPPING_STATUS) {
            return $product_info;
        }

        $always_available_query = \xtc_db_query(
            sprintf(
                'SELECT *
                   FROM `%s`
                  WHERE `products_id` = %d',
                Constants::TABLE_ALWAYS_AVAILABLE,
                $product_data['products_id']
            )
        );
        $always_available_data  = $always_available_query ? \xtc_db_fetch_array($always_available_query) : null;
        $always_available       = $always_available_data['always_available'] ?? false;

        if (false === $always_available) {
            return $product_info;
        }

        $product_info['shipping_time'] = $main->getShippingStatusName($shipping_status_id_available);

        return $product_info;
    }
    /**
     * phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
}
