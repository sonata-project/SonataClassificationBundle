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

## Final classes

Some classes  are now `final` and should not be overridden:

* `Sonata\ClassificationBundle\Admin\CategoryAdmin`
* `Sonata\ClassificationBundle\Admin\CollectionAdmin`
* `Sonata\ClassificationBundle\Admin\ContextAdmin`
* `Sonata\ClassificationBundle\Admin\TagAdmin`
* `Sonata\ClassificationBundle\Command\FixContextCommand`
* `Sonata\ClassificationBundle\Controller\CategoryAdminController`
* `Sonata\ClassificationBundle\DependencyInjection\Configuration`
* `Sonata\ClassificationBundle\DependencyInjection\SonataClassificationExtension`
* `Sonata\ClassificationBundle\Document\CategoryManager`
* `Sonata\ClassificationBundle\Document\CollectionManager`
* `Sonata\ClassificationBundle\Document\TagManager`
* `Sonata\ClassificationBundle\Entity\CategoryManager`
* `Sonata\ClassificationBundle\Entity\CollectionManager`
* `Sonata\ClassificationBundle\Entity\ContextManager`
* `Sonata\ClassificationBundle\Entity\TagManager`
* `Sonata\ClassificationBundle\Form\Type\Category\SelectorType`
* `Sonata\ClassificationBundle\SonataClassificationBundle`

## Doctrine schema update
Disallowed null value on foreign keys for associations with Context for
Category, Tag and Collection entities.

## Removed media previews

The media features were removed, to resolve internal circular dependency issues.
If you still need these, please have a look at the [ClassificationMediaBundle](https://github.com/sonata-project/SonataClassificationMediaBundle).

