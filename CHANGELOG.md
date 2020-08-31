# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
