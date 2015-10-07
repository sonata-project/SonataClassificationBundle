.. index::
    single: MediaBundle

MediaBundle Integration
=======================

There is an (optional) integration with the ``SonataMediaBundle``. This integration allows you to add image to Category and Collection objects.

If you have the ``SonataMediaBundle`` in your dependencies, then a new input will be visible in the admin. In order to make them work properly, you need to configure the ``sonata_media`` section like this:

.. configuration-block::

    .. code-block:: yaml

        sonata_media:
            # default configuration
            contexts:
                # ... other contexts ...
                sonata_collection:
                    providers:
                      - sonata.media.provider.image

                    formats:
                        preview: { width: 100, quality: 100}
                        wide:    { width: 820, quality: 100}

                sonata_category:
                    providers:
                      - sonata.media.provider.image

                    formats:
                        preview: { width: 100, quality: 100}
                        wide:    { width: 820, quality: 100}


So, you can display the `image` category like this:

.. code-block:: jinja

    <h1>{{ category.name}}</h1>

    {% if category.media %}
        <div class="media">
            {% media category.media, 'wide' %}
        </div>
    {% endif %}
