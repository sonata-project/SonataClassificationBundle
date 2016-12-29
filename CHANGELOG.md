# Change Log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [3.1.0](https://github.com/sonata-project/SonataAdminBundle/compare/3.0.1...3.1.0) - 2016-07-05
### Added
- Added `AbstractCategoriesBlockService` class
- Added `AbstractCollectionsBlockService` class
- Added `AbstractTagsBlockService` class

## [3.0.1](https://github.com/sonata-project/SonataAdminBundle/compare/3.0.0...3.0.1) - 2016-07-05
### Fixed
- Removed unmapped `count` property in `BaseCategory.mongodb.xml`
- Renamed wrong `slug` property to `name` in `BaseContext.mongodb.xml`
- `CategoryAdmin` now extends `ContextAwareAdmin`
- `CollectionAdmin` now extends `ContextAwareAdmin`

### Removed
- Some unneeded Symfony dependencies
