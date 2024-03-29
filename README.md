# Vietnamese Address Pack for Magento 2 - Eloab

 - [Main Functionalities](#markdown-header-main-functionalities)
 - [Installation](#markdown-header-installation)
 - [Configuration](#markdown-header-configuration)
 - [Specifications](#markdown-header-specifications)
 - [Attributes](#markdown-header-attributes)


## Main Functionalities
Override Magento_Region, Magento_Checkout, Magento_Customer for apply the new data structure for VN address.
View address saved for VN in Eloab / VietNam Address List

## Installation
\* = in production please use the `--keep-generated` option

### Type 1: Zip file

 - Install the module `Eloab_Base` before install this extension
 - Unzip the zip file in `app/code/Eloab`
 - Enable the module by running `php bin/magento module:enable Eloab_VNAddress`
 - Apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`

### Type 2: Composer

 - Make the module available in a composer repository for example:
    - private repository `repo.magento.com`
    - public repository `packagist.org`
    - public github repository as vcs
 - Add the composer repository to the configuration by running `composer config repositories.repo.magento.com composer https://repo.magento.com/`
 - Install the module composer by running `composer require eloab/module-vnaddress`
 - enable the module by running `php bin/magento module:enable Eloab_VNAddress`
 - apply database updates by running `php bin/magento setup:upgrade`\*
 - Flush the cache by running `php bin/magento cache:flush`



## Still development for address render view in Backend and Update new address function
