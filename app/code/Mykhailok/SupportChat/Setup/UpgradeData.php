<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    private \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable;

    /**
     * UpgradeSchema constructor.
     * @param \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable
     */
    public function __construct(
        \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable
    ) {
        $this->migrateDataFromOldTable = $migrateDataFromOldTable;
    }

    /**
     * @param \Magento\Framework\Setup\ModuleDataSetupInterface $setup
     * @param \Magento\Framework\Setup\ModuleContextInterface $context
     * @throws \Zend_Db_Statement_Exception
     */
    public function upgrade(
        \Magento\Framework\Setup\ModuleDataSetupInterface $setup,
        \Magento\Framework\Setup\ModuleContextInterface $context
    ) {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.0.1') >= 0) {
            $this->migrateDataFromOldTable->execute();
        }

        // Drop old table.
        $this->migrateDataFromOldTable->dropOldTable();

        $setup->endSetup();
    }
}
