<?php
declare(strict_types=1);

namespace Mykhailok\SupportChat\Setup\UpgradeData;

class RegisterAuthorizationRoles
{
    /** @var \Psr\Log\LoggerInterface $logger */
    private \Psr\Log\LoggerInterface $logger;

    /** @var \Magento\Authorization\Model\RoleFactory $roleFactory */
    private \Magento\Authorization\Model\RoleFactory $roleFactory;

    /** @var \Magento\Authorization\Model\RulesFactory $rulesFactory */
    private \Magento\Authorization\Model\RulesFactory $rulesFactory;

    /** @var \Magento\Authorization\Model\ResourceModel\Role $roleResourceModel */
    private \Magento\Authorization\Model\ResourceModel\Role $roleResourceModel;

    /** @var \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory */
    private \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory;

    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Authorization\Model\RoleFactory $roleFactory,
        \Magento\Authorization\Model\RulesFactory $rulesFactory,
        \Magento\Authorization\Model\ResourceModel\Role $roleResourceModel,
        \Magento\Authorization\Model\ResourceModel\Role\CollectionFactory $roleCollectionFactory
    ) {
        $this->roleCollectionFactory = $roleCollectionFactory;
        $this->roleFactory = $roleFactory;
        $this->logger = $logger;
        $this->roleResourceModel = $roleResourceModel;
        $this->rulesFactory = $rulesFactory;
    }

    /**
     * @param string $additionalRoleRulesVersion
     * @return void
     * @throws \Exception
     */
    public function execute(string $additionalRoleRulesVersion): void {
        $neededRoles = $this->{'getRoleRules' . $additionalRoleRulesVersion}();

        /* This code will be running every setup:upgrade,
           so we should exclude roles that already exist.
           START excluding. */
        $rolesCollection = $this->roleCollectionFactory->create();
        $rolesCollection->addFieldToFilter('role_name', ['in' => array_keys($neededRoles)]);

        /** @var \Magento\Authorization\Model\Role $roleModel */
        foreach ($rolesCollection as $roleModel) {
            $existingRoleName = $roleModel->getRoleName();
            unset($neededRoles[$existingRoleName]);
        }
        /* END excluding. */

        /** @var array $ruleResourceIds */
        foreach ($neededRoles as $roleName => $ruleResourceIds) {
            /** @var \Magento\Authorization\Model\Role $role */
            $role = $this->roleFactory->create();
            $role
                ->setRoleName($roleName)
                ->setParentId(0)
                ->setRoleType(\Magento\Authorization\Model\Acl\Role\Group::ROLE_TYPE)
                ->setUserType((string)\Magento\Authorization\Model\UserContextInterface::USER_TYPE_ADMIN);

            try {
                $this->roleResourceModel->save($role);
            } catch (\Magento\Framework\Exception\AlreadyExistsException $exception) {
                $this->logger->info($exception->getMessage());
            } catch (\Exception $exception) {
                $this->logger->error($exception->getMessage(), $exception->getTrace());
                throw new \Exception(
                    $exception->getMessage(),
                    $exception->getCode(),
                    $exception
                );
            }

            $rule = $this->rulesFactory->create();
            /** @noinspection PhpUndefinedMethodInspection */
            $rule
                ->setRoleId($role->getId())
                ->setResources($ruleResourceIds)
                ->saveRel();
        }
    }

    /**
     * @return array
     */
    protected function getRoleRulesV1(): array
    {
        $commonResources = [
            'Magento_Customer::customer',
            'Mykhailok_SupportChat::chat',
            'Mykhailok_SupportChat::chat_manage',
            'Mykhailok_SupportChat::chat_actions',
        ];

        $allResources = [
            'delete' => 'Mykhailok_SupportChat::chat_delete',
            'chatting' => 'Mykhailok_SupportChat::chat_chatting',
            'reading' => 'Mykhailok_SupportChat::chat_reading',
        ];

        return [
            'Customer Chat Manager (All rules)' =>
                array_merge($commonResources, [
                    $allResources['reading'],
                    $allResources['chatting'],
                    $allResources['delete'],
                    ]
                ),
            'Customer Chat Manager (Can chatting with client)' =>
                array_merge($commonResources, [
                    $allResources['reading'],
                    $allResources['chatting'],
                    ]
                ),
            'Customer Chat Manager (Can reading client chats)' =>
                array_merge($commonResources, [
                    $allResources['reading'],
                    ]
                ),
            ];
    }
}
