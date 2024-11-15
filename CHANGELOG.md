# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [4.9.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.8.1...4.9.0) - 2024-11-14
### Added
- [[#972](https://github.com/sonata-project/SonataClassificationBundle/pull/972)] Slovak translations ([@fbuchlak](https://github.com/fbuchlak))

## [4.8.1](https://github.com/sonata-project/SonataClassificationBundle/compare/4.8.0...4.8.1) - 2024-08-26
### Fixed
- [[#968](https://github.com/sonata-project/SonataClassificationBundle/pull/968)] Symfony 7.1 deprecation about `Symfony\Component\HttpKernel\DependencyInjection\Extension` usage ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.8.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.7.2...4.8.0) - 2023-12-14
### Added
- [[#952](https://github.com/sonata-project/SonataClassificationBundle/pull/952)] Support for packages from `symfony/*` 7.x ([@phansys](https://github.com/phansys))

## [4.7.2](https://github.com/sonata-project/SonataClassificationBundle/compare/4.7.1...4.7.2) - 2023-12-08
### Fixed
- [[#947](https://github.com/sonata-project/SonataClassificationBundle/pull/947)] Usage of deprecated `renderWithExtraParams` method ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.7.1](https://github.com/sonata-project/SonataClassificationBundle/compare/4.7.0...4.7.1) - 2023-07-24
### Added
- [[#939](https://github.com/sonata-project/SonataClassificationBundle/pull/939)] Translations (en/it) for tag, context and collection group ([@gremo](https://github.com/gremo))

## [4.7.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.6.0...4.7.0) - 2023-06-03
### Added
- [[#931](https://github.com/sonata-project/SonataClassificationBundle/pull/931)] Support for SonataBlockBundle 5.0 ([@jordisala1991](https://github.com/jordisala1991))

## [4.6.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.5.0...4.6.0) - 2023-05-13
### Added
- [[#927](https://github.com/sonata-project/SonataClassificationBundle/pull/927)] Support for `sonata-project/form-extensions` 2.0 ([@jordisala1991](https://github.com/jordisala1991))

## [4.5.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.4.1...4.5.0) - 2023-04-24
### Removed
- [[#914](https://github.com/sonata-project/SonataClassificationBundle/pull/914)] Support for Symfony 4.4 ([@jordisala1991](https://github.com/jordisala1991))
- [[#914](https://github.com/sonata-project/SonataClassificationBundle/pull/914)] Support for Twig 2 ([@jordisala1991](https://github.com/jordisala1991))

## [4.4.1](https://github.com/sonata-project/SonataClassificationBundle/compare/4.4.0...4.4.1) - 2023-04-12
### Fixed
- [[#911](https://github.com/sonata-project/SonataClassificationBundle/pull/911)] ORM schema mapping does not give validation errors ([@jordisala1991](https://github.com/jordisala1991))

## [4.4.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.3.0...4.4.0) - 2023-04-09
### Added
- [[#889](https://github.com/sonata-project/SonataClassificationBundle/pull/889)] Added support for `doctrine/collections` ^2.0. ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#889](https://github.com/sonata-project/SonataClassificationBundle/pull/889)] Deprecations for SonataAdminBundle filters ([@jordisala1991](https://github.com/jordisala1991))
- [[#907](https://github.com/sonata-project/SonataClassificationBundle/pull/907)] Create a category after a fresh install initializes the first context and the root category. ([@jordisala1991](https://github.com/jordisala1991))
- [[#903](https://github.com/sonata-project/SonataClassificationBundle/pull/903)] xml validation of MongoDB ODM mapping ([@jordisala1991](https://github.com/jordisala1991))
- [[#903](https://github.com/sonata-project/SonataClassificationBundle/pull/903)] Deprecation for enabled boolean field on MongoDB ODM ([@jordisala1991](https://github.com/jordisala1991))
- [[#904](https://github.com/sonata-project/SonataClassificationBundle/pull/904)] Not displaying the edit button in some cases for the admin list pages ([@jordisala1991](https://github.com/jordisala1991))

### Removed
- [[#889](https://github.com/sonata-project/SonataClassificationBundle/pull/889)] Support for `doctrine/persistence` ^2.0 ([@jordisala1991](https://github.com/jordisala1991))
- [[#890](https://github.com/sonata-project/SonataClassificationBundle/pull/890)] Support for PHP 7.4 ([@SonataCI](https://github.com/SonataCI))
- [[#890](https://github.com/sonata-project/SonataClassificationBundle/pull/890)] Support for Symfony 6.0 and 6.1 ([@SonataCI](https://github.com/SonataCI))

## [4.3.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.2.0...4.3.0) - 2022-08-03
### Added
- [[#862](https://github.com/sonata-project/SonataClassificationBundle/pull/862)] Added support for sonata-project/doctrine-extensions ^2 ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.2.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.1.0...4.2.0) - 2022-06-25
### Added
- [[#852](https://github.com/sonata-project/SonataClassificationBundle/pull/852)] Add support for `doctrine/persistence` ^3.0. ([@jordisala1991](https://github.com/jordisala1991))

## [4.1.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.4...4.1.0) - 2022-06-25
### Removed
- [[#849](https://github.com/sonata-project/SonataClassificationBundle/pull/849)] Avoid deprecations for console commands on Symfony 6.1. ([@jordisala1991](https://github.com/jordisala1991))
- [[#846](https://github.com/sonata-project/SonataClassificationBundle/pull/846)] Support of Symfony 5.3 ([@franmomu](https://github.com/franmomu))

## [4.0.4](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.3...4.0.4) - 2022-04-29
### Fixed
- [[#838](https://github.com/sonata-project/SonataClassificationBundle/pull/838)] Create a new context only if it does not exist ([@jerome-fix](https://github.com/jerome-fix))

## [4.0.3](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.2...4.0.3) - 2022-02-28
### Fixed
- [[#830](https://github.com/sonata-project/SonataClassificationBundle/pull/830)] Deprecations introduces by SonataAdminBundle 4.9.0 ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.0.2](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.1...4.0.2) - 2022-02-10
### Fixed
- [[#820](https://github.com/sonata-project/SonataClassificationBundle/pull/820)] Backport changes from AdminBundle to `CategoryAdminController` ([@core23](https://github.com/core23))
- [[#821](https://github.com/sonata-project/SonataClassificationBundle/pull/821)] Fix edit group translation ([@core23](https://github.com/core23))

## [4.0.1](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.0...4.0.1) - 2022-01-19
### Removed
- [[#816](https://github.com/sonata-project/SonataClassificationBundle/pull/816)] Final modifier on all the getters / setters of the entities. ([@VincentLanglet](https://github.com/VincentLanglet))

## [4.0.0](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.0-RC1...4.0.0) - 2022-01-01
### Added
- [[#778](https://github.com/sonata-project/SonataClassificationBundle/pull/778)] Support for Symfony 6 ([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#786](https://github.com/sonata-project/SonataClassificationBundle/pull/786)] Admin controller configuration ([@core23](https://github.com/core23))
- [[#786](https://github.com/sonata-project/SonataClassificationBundle/pull/786)] Calling removed admin translator ([@core23](https://github.com/core23))
- [[#787](https://github.com/sonata-project/SonataClassificationBundle/pull/787)] Symfony deprecations ([@jordisala1991](https://github.com/jordisala1991))

## [4.0.0-RC1](https://github.com/sonata-project/SonataClassificationBundle/compare/4.0.0-alpha1...4.0.0-RC1) - 2021-11-20
- No significant changes

## [4.0.0-alpha1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.17.0...4.0.0-alpha1) - 2021-11-03
### Added
- [[#752](https://github.com/sonata-project/SonataClassificationBundle/pull/752)] Make blocks editable ([@core23](https://github.com/core23))
- [[#753](https://github.com/sonata-project/SonataClassificationBundle/pull/753)] `ContextAwareInterface` ([@core23](https://github.com/core23))

### Changed
- [[#754](https://github.com/sonata-project/SonataClassificationBundle/pull/754)] Added type hints to manager methods ([@core23](https://github.com/core23))
- [[#745](https://github.com/sonata-project/SonataClassificationBundle/pull/745)] Added type hints to methods and properties ([@core23](https://github.com/core23))
- [[#745](https://github.com/sonata-project/SonataClassificationBundle/pull/745)] Reduce visibility of config methods ([@core23](https://github.com/core23))

### Fixed
- [[#757](https://github.com/sonata-project/SonataClassificationBundle/pull/757)] Removed call to unknown `FormMapper::getAdmin` method ([@core23](https://github.com/core23))

### Removed
- [[#763](https://github.com/sonata-project/SonataClassificationBundle/pull/763)] Support for Symfony 5.2 ([@jordisala1991](https://github.com/jordisala1991))
- [[#763](https://github.com/sonata-project/SonataClassificationBundle/pull/763)] Support for `doctrine/persistence` < 2 ([@jordisala1991](https://github.com/jordisala1991))
- [[#763](https://github.com/sonata-project/SonataClassificationBundle/pull/763)] Support for `cocur/slugify` < 4.0 ([@jordisala1991](https://github.com/jordisala1991))
- [[#745](https://github.com/sonata-project/SonataClassificationBundle/pull/745)] `Sonata\ClassificationBundle\Form\FormHelper` ([@core23](https://github.com/core23))
- [[#744](https://github.com/sonata-project/SonataClassificationBundle/pull/744)] Support for PHP 7.3 ([@core23](https://github.com/core23))

## [3.18.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.18.0...3.18.1) - 2022-01-05
### Fixed
- [[#812](https://github.com/sonata-project/SonataClassificationBundle/pull/812)] Fixed CategoryInterface::getSlug() PHPDoc return type. ([@kwizer15](https://github.com/kwizer15))

## [3.18.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.17.0...3.18.0) - 2021-11-06
### Deprecated
- [[#765](https://github.com/sonata-project/SonataClassificationBundle/pull/765)] `BaseCategory::disableChildrenLazyLoading()` ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#760](https://github.com/sonata-project/SonataClassificationBundle/pull/760)] CategoryManager::getRootCategoriesPager() ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#760](https://github.com/sonata-project/SonataClassificationBundle/pull/760)] CategoryManager::getSubCategoriesPager() ([@VincentLanglet](https://github.com/VincentLanglet))
- [[#735](https://github.com/sonata-project/SonataClassificationBundle/pull/735)] Deprecated ReST API with FOSRest, Nelmio Api Docs and JMS Serializer. ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] Extending classes marked as final: ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Admin\CategoryAdmin` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Admin\CollectionAdmin` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Admin\ContextAdmin` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Admin\TagAdmin` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Command\FixContextCommand` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Controller\CategoryAdminController` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\DependencyInjection\Configuration` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\DependencyInjection\SonataClassificationExtension` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Document\CategoryManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Document\CollectionManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Document\TagManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Entity\CategoryManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Entity\CollectionManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Entity\ContextManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Entity\TagManager` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\Form\Type\Category\SelectorType` ([@wbloszyk](https://github.com/wbloszyk))
- [[#738](https://github.com/sonata-project/SonataClassificationBundle/pull/738)] `Sonata\ClassificationBundle\SonataClassificationBundle` ([@wbloszyk](https://github.com/wbloszyk))

## [3.17.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.16.0...3.17.0) - 2021-09-21
### Added
- [[#721](https://github.com/sonata-project/SonataClassificationBundle/pull/721)] Missing methods in interfaces (through the `@method` annotation). ([@phansys](https://github.com/phansys))
- [[#722](https://github.com/sonata-project/SonataClassificationBundle/pull/722)] Missing requirement for "doctrine/collections". ([@phansys](https://github.com/phansys))

### Fixed
- [[#727](https://github.com/sonata-project/SonataClassificationBundle/pull/727)] Some `@return` types at `Sonata\ClassificationBundle\Controller\Api\CategoryController`; ([@phansys](https://github.com/phansys))
- [[#727](https://github.com/sonata-project/SonataClassificationBundle/pull/727)] Checking for an empty collection returned by `CategoryInterface::getChildren()` at `CategorySelectorType`. ([@phansys](https://github.com/phansys))
- [[#726](https://github.com/sonata-project/SonataClassificationBundle/pull/726)] Several wrong types in arguments and return declarations. ([@phansys](https://github.com/phansys))
- [[#721](https://github.com/sonata-project/SonataClassificationBundle/pull/721)] Calls to several undefined methods. ([@phansys](https://github.com/phansys))
- [[#724](https://github.com/sonata-project/SonataClassificationBundle/pull/724)] Calls to undefined methods in ODM queries at `CategoryManager::getPager()`, `CollectionManager::getPager()` and `TagManager::getPager()`. ([@phansys](https://github.com/phansys))
- [[#719](https://github.com/sonata-project/SonataClassificationBundle/pull/719)] Several docblock types for properties and methods under the `Sonata\ClassificationBundle\Model\` namespace. ([@phansys](https://github.com/phansys))

## [3.16.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.15.1...3.16.0) - 2021-04-06
### Added
- [[#653](https://github.com/sonata-project/SonataClassificationBundle/pull/653)] Add support for PHP 8.x ([@Yozhef](https://github.com/Yozhef))

## [3.15.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.15.0...3.15.1) - 2021-03-21
### Removed
- [[#659](https://github.com/sonata-project/SonataClassificationBundle/pull/659)] Remove controller deprecations ([@core23](https://github.com/core23))
- [[#660](https://github.com/sonata-project/SonataClassificationBundle/pull/660)] Remove admin deprecations ([@core23](https://github.com/core23))

## [3.15.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.14.0...3.15.0) - 2021-02-15
### Changed
- [[#611](https://github.com/sonata-project/SonataClassificationBundle/pull/611)] Updated Dutch translations ([@zghosts](https://github.com/zghosts))

## [3.14.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.13.2...3.14.0) - 2020-11-27
### Added
- [[#599](https://github.com/sonata-project/SonataClassificationBundle/pull/599)]
  Added support for "doctrine/persistence:^2.0".
([@awurth](https://github.com/awurth))
- [[#607](https://github.com/sonata-project/SonataClassificationBundle/pull/607)]
  Missing translation keys ([@gremo](https://github.com/gremo))
- [[#574](https://github.com/sonata-project/SonataClassificationBundle/pull/574)]
  Support for `nelmio/api-doc-bundle` >= 3.6
([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#585](https://github.com/sonata-project/SonataClassificationBundle/pull/585)]
  Remove support for `doctrine/mongodb-odm` <2.0
([@franmomu](https://github.com/franmomu))

## [3.13.2](https://github.com/sonata-project/SonataClassificationBundle/compare/3.13.1...3.13.2) - 2020-09-05
### Fixed
- [[#573](https://github.com/sonata-project/SonataClassificationBundle/pull/573)]
  Fixed support for string model identifiers at Open API definitions.
([@wbloszyk](https://github.com/wbloszyk))
- [[#567](https://github.com/sonata-project/SonataClassificationBundle/pull/567)]
  Fix loading CategoryFilter items ([@core23](https://github.com/core23))

### Removed
- [[#573](https://github.com/sonata-project/SonataClassificationBundle/pull/573)]
  Removed requirements that were only allowing integers for model identifiers
at Open API definitions. ([@wbloszyk](https://github.com/wbloszyk))

## [3.13.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.13.0...3.13.1) - 2020-08-23
### Fixed
- [[#554](https://github.com/sonata-project/SonataClassificationBundle/pull/554)]
  Make admin dependency optional for block rendering
([@core23](https://github.com/core23))

## [3.13.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.12.1...3.13.0) - 2020-08-05
### Added
- [[#545](https://github.com/sonata-project/SonataClassificationBundle/pull/545)]
  Added support for "friendsofsymfony/rest-bundle:^3.0"
([@wbloszyk](https://github.com/wbloszyk))
- [[#542](https://github.com/sonata-project/SonataClassificationBundle/pull/542)]
  Added public alias
`Sonata\ClassificationBundle\Controller\Api\CategoryController` for
`sonata.classification.controller.api.category` service
([@wbloszyk](https://github.com/wbloszyk))
- [[#542](https://github.com/sonata-project/SonataClassificationBundle/pull/542)]
  Added public alias
`Sonata\ClassificationBundle\Controller\Api\CollectionController` for
`sonata.classification.controller.api.collection` service
([@wbloszyk](https://github.com/wbloszyk))
- [[#542](https://github.com/sonata-project/SonataClassificationBundle/pull/542)]
  Added public alias `Sonata\ClassificationBundle\Controller\Api\TagController`
for `sonata.classification.controller.api.tag` service
([@wbloszyk](https://github.com/wbloszyk))
- [[#542](https://github.com/sonata-project/SonataClassificationBundle/pull/542)]
  Added public alias
`Sonata\ClassificationBundle\Controller\Api\ContextController` for
`sonata.classification.controller.api.context` service
([@wbloszyk](https://github.com/wbloszyk))

### Change
- [[#545](https://github.com/sonata-project/SonataClassificationBundle/pull/545)]
  Support for deprecated "rest" routing type in favor for xml
([@wbloszyk](https://github.com/wbloszyk))

### Changed
- [[#536](https://github.com/sonata-project/SonataClassificationBundle/pull/536)]
  SonataEasyExtendsBundle is now optional, using SonataDoctrineBundle is
preferred ([@jordisala1991](https://github.com/jordisala1991))

### Deprecated
- [[#536](https://github.com/sonata-project/SonataClassificationBundle/pull/536)]
  Using SonataEasyExtendsBundle to add Doctrine mapping information
([@jordisala1991](https://github.com/jordisala1991))

### Fixed
- [[#542](https://github.com/sonata-project/SonataClassificationBundle/pull/542)]
  Fix RestFul API - `Class could not be determined for Controller identified`
Error ([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#544](https://github.com/sonata-project/SonataClassificationBundle/pull/544)]
  Support for Symfony < 4.4 ([@wbloszyk](https://github.com/wbloszyk))

## [3.12.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.12.0...3.12.1) - 2020-06-21
### Fixed
- [[#539](https://github.com/sonata-project/SonataClassificationBundle/pull/539)]
  Fix mysql database schema ([@wbloszyk](https://github.com/wbloszyk))

### Removed
- [[#539](https://github.com/sonata-project/SonataClassificationBundle/pull/539)]
  Remove support for mssql database ([@wbloszyk](https://github.com/wbloszyk))

## [3.12.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.11.1...3.12.0) - 2020-06-19
### Added
- Added `CategoryFilter` for admin lists
- Added `CollectionFilter` for admin lists

### Fixed
- fixed database schema to work with mssql

### Changed
- Make admin bundle optional

### Removed
- SonataCoreBundle dependencies
- Support for Symfony < 4.3

## [3.11.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.11.0...3.11.1) - 2020-03-24
### Fixed
- Fix Lexer query error in managers

## [3.11.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.10.1...3.11.0) - 2020-03-18
### Added
- Add public aliases to all manager interface
- Added `CollectionManager::getBySlug` method
- Added `CategoryManager::getBySlug` method
- Added `CategoryManager::getByContext` method
- Added `TagManager::getBySlug` method
- Added `TagManager::getByContext` method

### Fixed
- Allow `cocur/slugify` ^4.0

### Removed
- Remove block deprecations

## [3.10.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.10.0...3.10.1) - 2020-02-03
### Fixed
- Fix media bundle decoupling

## [3.10.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.9.2...3.10.0) - 2020-01-31
### Fixed
- Fix media bundle decoupling

### Changed
- Loading media association based on defined class

### Removed
- Support for Symfony < 3.4
- Support for Symfony >= 4, < 4.2

## [3.9.2](https://github.com/sonata-project/SonataClassificationBundle/compare/3.9.1...3.9.2) - 2019-11-11
### Fixed
- Fix calling wrong manager in `AbstractCollectionsBlockService`

## [3.9.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.9.0...3.9.1) - 2019-10-14
### Fixed
- `getContext` method with non-existing context causing infinite loop among
  other bugs

### Added
- Add missing translation for admin menu

## [3.9.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.8.1...3.9.0) - 2019-09-20
### Added
- Add more `@method` annotation to propagate new methods of `CategoryManagerInterface`
- Add strict types to CategoryManager

### Removed
- Remove superfluous PHPDoc

### Fixed
- Match PHPDoc with doctrine model

### Changed
- `CategoryManager::getRootCategory` will throw an exception if you pass
  invalid arguments

## [3.8.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.8.0...3.8.1) - 2019-06-03

### Added
- Added translation keys for `CategoryAdmin` "General" and "Options" labels

## [3.8.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.7.1...3.8.0) - 2019-01-18

### Fixed
- Fix deprecation for symfony/config 4.2+
- Deprecations about `Sonata\CoreBundle\Model\BaseEntityManager`

### Removed
- Removed CoreBundle deprecations
- support for php 5 and php 7.0

## [3.7.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.7.0...3.7.1) - 2018-06-18
### Changed
- Stop using the deprecated method `Sonata\AdminBundle\Controller\CRUDController::render` and use the new `renderWithExtraParams`
- Force use translation strings for classification entities

### Fixed
- Make entity managers services public

## [3.7.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.6.1...3.7.0) - 2018-05-22
### Added
- make block icon configurable
- added block title translation domain

### Fixed
- Commands not working on symfony4

### Removed
- Default title from blocks
- Compatibility with older versions of FOSRestBundle (<2.1)
- Compatibility with Cocur slugify `^1.0`

## [3.6.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.6.0...3.6.1) - 2018-01-26
### Fixed
- Slugify ^3.0 support
- Make `sonata.classification.manager.category` public

## [3.6.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.5.0...3.6.0) - 2018-01-07
### Added
- Added `NotBlank` constraint to `Context::$id`

### Changed
- make admin services explicit public

### Fixed
- Compatibility with SF 3.4, SF 4

### Removed
- `NotNull` constraint from `Tag::$name`
- `NotNull` constraint from `Context::$name`
- `NotNull` constraint from `Category::$name`
- `NotNull` constraint from `Collection::$name`

## [3.5.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.4.0...3.5.0) - 2017-12-08
### Changed
- Rollback to PHP 5.6 as minimum support.

### Fixed
- It is now allowed to install Symfony 4

## [3.4.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.3.2...3.4.0) - 2017-10-22
### Removed
- Removed usage of old form type aliases
- Support for old versions of php and Symfony

## [3.3.2](https://github.com/sonata-project/SonataClassificationBundle/compare/3.3.1...3.3.2) - 2017-10-22
### Added
- context fields validation in tag/collection create forms, which prevents creating objects with empty context from UI

### Changed
- Changed string type declaration of form fields to the fully-qualified type class name.

### Fixed
- Missing brazilian translations

## [3.3.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.3.0...3.3.1) - 2017-06-16
### Fixed
- use `same as` instead of deprecated `sameas` in twig template
- Fixed hardcoded paths to classes in `.xml.skeleton` files of config
- Added route check to tree view `Resources/views/CategoryAdmin/tree.html.twig`. If there aren't edit AND show routes, render element name only.

## [3.3.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.2.1...3.3.0) - 2017-03-16
### Added
- Menu icon (fa-tags).
- Fix bad / missing translations in french.
- new methods for root categories in `CategoryManager`
- `CategoryManager::loadCategories` method now loads all root categories in context
- Categories tree now renders all root categories in context

### Fixed
- Fix usage of deprecated `choice_list` option for >=SF2.7
- Allow false value for category in `AbstractCategoriesBlockService`
- Allow false value for collection in `AbstractCollectionsBlockService`
- Allow false value for tag in `AbstractTagsBlockService`
- use `interface_exists` instead of `class_exists`
- Fixed missing type field in filters form in categories tree mode
- Twig runtime error on Symfony < 3.2 and Twig 2.x
- Fixed conflict of datagrid `context` value and persistent `context` parameter
- Fixed using `_self` in `navigate_child` macro in tree template (Twig 2.0 support)

## [3.2.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.2.0...3.2.1) - 2017-02-02
### Fixed
- Category tree view was not easily customizable
- Missing italian translation

## [3.2.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.1.0...3.2.0) - 2017-01-05
### Fixed
- Symfony 3 compatibility was improved
- Support for FosRestBundle 2.0
- Incorrect `tag` reference on command output
- Missing italian translations
- Deprecated `Admin` class usage
- Missing parameters from parent class in `ContextAwareAdmin::getPersistentParameters`
- Incorrect names transliterating for slugs
- Missing en translations for create forms
- Typo in service unit tests
- The category tree view in case the category list is empty
- Vertical centering of buttons within a navbar in list/tree selector
- Deprecated `AbstractBlockServiceTest`, `FakeTemplating`, `BaseBlockService` usage

### Removed
- Internal test classes are now excluded from the autoloader

## [3.1.0](https://github.com/sonata-project/SonataClassificationBundle/compare/3.0.1...3.1.0) - 2016-07-05
### Added
- Added `AbstractCategoriesBlockService` class
- Added `AbstractCollectionsBlockService` class
- Added `AbstractTagsBlockService` class

## [3.0.1](https://github.com/sonata-project/SonataClassificationBundle/compare/3.0.0...3.0.1) - 2016-07-05
### Fixed
- Removed unmapped `count` property in `BaseCategory.mongodb.xml`
- Renamed wrong `slug` property to `name` in `BaseContext.mongodb.xml`
- `CategoryAdmin` now extends `ContextAwareAdmin`
- `CollectionAdmin` now extends `ContextAwareAdmin`

### Removed
- Some unneeded Symfony dependencies
