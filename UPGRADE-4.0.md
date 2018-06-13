UPGRADE FROM 3.x to 4.0
=======================

## Deprecations

All the deprecated code introduced on 3.x is removed on 4.0.

Please read [3.x](https://github.com/sonata-project/SonataClassificationBundle/tree/3.x) upgrade guides for more information.

See also the [diff code](https://github.com/sonata-project/SonataClassificationBundle/compare/3.x...4.0.0).

### Models
If you have implemented a custom model, you must adapt the signature of the following new methods:
 * `CategoryInterface::getId`
 * `CollectionInterface::getId`
 * `TagInterface::getId`

### CategoryManager
+If you have implemented a custom category manager, you must adapt the signature of the following new methods:
 * `getRootCategoriesPager`
 * `getSubCategoriesPager`
 * `getRootCategoryWithChildren`
 * `getRootCategories`
 * `getCategories`
 * `getRootCategory`
 * `getRootCategoriesForContext`
 * `getAllRootCategories`
 * `getRootCategoriesSplitByContexts`

## Doctrine schema update
Disallowed null value on foreign keys for associations with Context for
Category, Tag and Collection entities.
