.. index::
    single: BlockBundle

BlockBundle Integration
=======================

There is an (optional) integration with the ``SonataBlockBundle``. This integration allows you to render dynamic lists on a page.

Here is a sample implementation for a custom category list block:

.. code-block:: php

    <?php
    class CustomCategoriesBlockService extends AbstractCategoriesBlockService
    {
        /**
         * {@inheritdoc}
         */
        public function configureSettings(OptionsResolver $resolver)
        {
            parent::configureSettings($resolver);

            $resolver->setDefaults(array(
                'context'    => 'custom',
                'template'   => 'AcmeCustomBundle:Block:block_categories.html.twig',
            ));
        }

        /**
         * {@inheritdoc}
         */
        public function getBlockMetadata($code = null)
        {
            return new Metadata($this->getName(), (!is_null($code) ? $code : $this->getName()), false, 'AcmeCustomBundle', array(
                'class' => 'fa fa-folder-open-o',
            ));
        }
    }


.. code-block:: twig

    {% extends 'SonataClassificationBundle:Block:base_block_categories.html.twig' %}

    {% block link_category %}<a href="{{ path('acme_custom_category', { 'category': item.slug }) }}">{{ item.name }}</a>{% endblock %}

