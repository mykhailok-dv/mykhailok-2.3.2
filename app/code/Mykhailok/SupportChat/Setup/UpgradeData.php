<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Setup;

class UpgradeData implements \Magento\Framework\Setup\UpgradeDataInterface
{
    /**
     * @var UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable
     */
    private \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable;

    /**
     * @var UpgradeData\RegisterAuthorizationRoles $registerAuthorizationRoles
     */
    private UpgradeData\RegisterAuthorizationRoles $registerAuthorizationRoles;

    /**
     * UpgradeSchema constructor.
     * @param \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable
     * @param UpgradeData\RegisterAuthorizationRoles $registerAuthorizationRoles
     */
    public function __construct(
        \Mykhailok\SupportChat\Setup\UpgradeData\MigrateDataFromOldTable $migrateDataFromOldTable,
        \Mykhailok\SupportChat\Setup\UpgradeData\RegisterAuthorizationRoles $registerAuthorizationRoles
    ) {
        $this->migrateDataFromOldTable = $migrateDataFromOldTable;
        $this->registerAuthorizationRoles = $registerAuthorizationRoles;
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

        if (version_compare($context->getVersion(), '1.0.2') >= 0) {
            $this->registerAuthorizationRoles->execute('V1');
        }

        // Drop old table.
        $this->migrateDataFromOldTable->dropOldTable();

        $setup->endSetup();
    }
}
