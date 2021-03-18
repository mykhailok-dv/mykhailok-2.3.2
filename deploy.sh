#!/bin/sh

echo ; echo ; echo ; echo 'Deploy starts...'
start=$(date +%s)

php bin/magento deploy:mode:set default
composer i
rm -rf var/cache/* var/page_cache/* var/view_preprocessed/* pub/static/frontend/* pub/static/adminhtml/* pub/static/_cache/* pub/static/deployed_version.txt generated/code/* generated/metadata/*
php bin/magento setup:upgrade
rm -rf var/cache/* var/page_cache/* var/view_preprocessed/* pub/static/frontend/* pub/static/adminhtml/* pub/static/_cache/* pub/static/deployed_version.txt generated/code/* generated/metadata/*
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
php bin/magento deploy:mode:set production --skip-compilation
php bin/magento cache:clean
php bin/magento cache:flush
php bin/magento cache:enable
end=$(date +%s)

echo ; echo ; echo ; echo '\e[92mDeploy spent: '"$(($end - $start))"' sec\e[39m'
