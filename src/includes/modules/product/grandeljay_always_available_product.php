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

class grandeljay_always_available_product extends StdModule
{
    public const VERSION = Constants::MODULE_VERSION;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_PRODUCT_NAME);

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
     * Extends the modified-shop product method `buildDataArray`.
     *
     * @param array  $product_data_smarty The product data with capitalised
     *                                    keys.
     * @param array  $product_data        The product data.
     * @param string $image               Unknown. Probably product image size.
     *
     * @return array
     */
    public function buildDataArray(array $product_data_smarty, array $product_data, string $image): array
    {
        global $main;

        $shipping_status_id_available = 2;

        $shipping_status_name  = '';
        $shipping_status_image = '';
        $shipping_status_link  = '';

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
            return $product_data_smarty;
        }

        if ('true' === ACTIVATE_SHIPPING_STATUS) {
            $shipping_status_name  = $main->getShippingStatusName($shipping_status_id_available);
            $shipping_status_image = $main->getShippingStatusImage($shipping_status_id_available);
            $shipping_status_link  = $main->getShippingStatusName($shipping_status_id_available, true);
        }

        $product_data_smarty['PRODUCTS_SHIPPING_NAME']      = $shipping_status_name;
        $product_data_smarty['PRODUCTS_SHIPPING_IMAGE']     = $shipping_status_image;
        $product_data_smarty['PRODUCTS_SHIPPING_NAME_LINK'] = $shipping_status_link;
        $product_data_smarty['SHIPPING_NAME']               = $shipping_status_name;
        $product_data_smarty['SHIPPING_IMAGE']              = $shipping_status_image;
        $product_data_smarty['SHIPPING_NAME_LINK']          = $shipping_status_link;

        return $product_data_smarty;
    }
}
