# Custom Caption Filter

## Filter Caption

By default captions are added to the embed template using a caption filter.

These are added to the `filter-caption.html.twig` template (alongside the embedded entity). Confusingly the node its referencing in this case is the DOM node (not the Drupal node).
All of this is done from the `\Drupal\filter\Plugin\Filter\FilterCaption` class, which renders the `filter-caption` theme.

```
{#
/**
 * @file
 * Theme override for a filter caption.
 *
 * Returns HTML for a captioned image, audio, video or other tag.
 *
 * Available variables
 * - string node: The complete HTML tag whose contents are being captioned.
 * - string tag: The name of the HTML tag whose contents are being captioned.
 * - string caption: The caption text.
 * - string classes: The classes of the captioned HTML tag.
 */
#}
<figure role="group"{%- if classes %} class="{{ classes }}"{%- endif %}>
{{ node }}
</figure>
```

So in our case we now want to streamline the `filter-caption` template.

```
{#
/**
 * @file
 * Theme override for a filter caption.
 *
 * Returns HTML for a captioned image, audio, video or other tag.
 *
 * Available variables
 * - string node: The complete HTML tag whose contents are being captioned.
 * - string tag: The name of the HTML tag whose contents are being captioned.
 * - string caption: The caption text.
 * - string classes: The classes of the captioned HTML tag.
 */
#}
{{ node }}
```

---
**ACTION**
As we intend to replace this filter with the custom filter we need to disable it (from *Text formats and editors*) and enable our filter (*Caption images with caption placed within the media entity markup*). 
---

## How does the filter work?

* When the content is rendered it searches through the rendered output for anything with a `data-caption` attribute
* Remove the `data-caption` attribute and store its value in a variable
* Find a descendant of the item with the caption with a class of `media__wrapper`
* Append the caption (as a `figcaption` element) to the element with the `media__wrapper` class.

## The Media Template

The caption will now have been moved into the parent item of the media container, which it locates using the `media__wrapper` class. This class does *need* to be present, but doesn't need to be at the top level.

---
**ACTION**
Ensure that the media container does have the `media__wrapper` class on it, but this class can be changed in the text format configuration options.
---

Here is an example media template containing the `media__wrapper`.

```
{#
/**
 * @file
 * Theme override to display a media item.
 *
 * Available variables:
 * - name: Name of the media.
 * - content: Media content.
 *
 * @see template_preprocess_media()
 *
 * @ingroup themeable
 */
#}
<div{{ attributes }}>
  {{ title_suffix.contextual_links }}
  <div class="media__wrapper">
  {% if content %}
    {{ content }}
  {% endif %}
  </div>
</div>
```


## Embedded Entities

The initial `entity-embed-container.html.twig` template contains an article wrapping element, but this will now most likely be handled as part of the media template. THe markup in this template includes the wrapper which will hold the caption (among other information) so MUST NOT be removed.

However, should the outer container element need to be removed this can be achieved using the *Remove entity embed container* config option in the text format configuration options.
