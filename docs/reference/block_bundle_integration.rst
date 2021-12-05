.. index::
    single: BlockBundle

BlockBundle Integration
=======================

There is an (optional) integration with the ``SonataBlockBundle``. This integration allows you to render dynamic lists on a page.

Here is a sample implementation for a custom category list block::

    class CustomCategoriesBlockService extends AbstractCategoriesBlockService
    {
        public function configureSettings(OptionsResolver $resolver): void
        {
            parent::configureSettings($resolver);

            $resolver->setDefaults([
                'context' => 'custom',
                'template' => '@AcmeCustom/Block/block_categories.html.twig',
            ]);
        }

        public function getMetadata(): Metadata
        {
            return new Metadata('my_custom_block_title', null, null, 'AcmeCustomBundle', [
                'class' => 'fa fa-folder-open-o',
            ]);
        }
    }


.. code-block:: html+twig

    {% extends '@SonataClassification/Block/base_block_categories.html.twig' %}

    {% block link_category %}
        <a href="{{ path('acme_custom_category', { 'category': item.slug }) }}">{{ item.name }}</a>
    {% endblock %}
