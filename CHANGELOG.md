# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

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
