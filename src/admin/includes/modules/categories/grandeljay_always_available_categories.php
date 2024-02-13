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

class grandeljay_always_available_categories extends StdModule
{
    public const VERSION = Constants::MODULE_VERSION;

    public function __construct()
    {
        parent::__construct(Constants::MODULE_CATEGORIES_NAME);

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
    }

    public function remove(): void
    {
        parent::remove();
    }

    /**
     * Extends the modified-shop categories method `insert_product_after`.
     *
     * phpcs:disable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     *
     * @param array  $products_data The product data that was inserted into the
     *                              database.
     * @param array  $products_id   The inserted product's id.
     *
     * @return array
     */
    public function insert_product_after(array $products_data, int $products_id): void
    {
        $always_available = isset($_POST['grandeljay_always_available']['always_available']);

        if ($always_available) {
            xtc_db_query(
                sprintf(
                    'REPLACE INTO `%s` (`products_id`, `always_available`, `last_modified`)
                           VALUES (%d, %d, "%s")',
                    Constants::TABLE_ALWAYS_AVAILABLE,
                    $products_id,
                    $always_available,
                    date('Y-m-d H:i:s')
                )
            );
        } else {
            xtc_db_query(
                sprintf(
                    'DELETE FROM `%s`
                           WHERE `products_id` = %d',
                    Constants::TABLE_ALWAYS_AVAILABLE,
                    $products_id
                )
            );
        }
    }
    /**
     * phpcs:enable PSR1.Methods.CamelCapsMethodName.NotCamelCaps
     */
}
