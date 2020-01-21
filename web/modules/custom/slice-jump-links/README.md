# Slice Jump Links

Adds a block to show jumps links to slices within a page.

## Usage

* The module installs fields for the jump links - add relevant fields to each slice (add the label field as a minimum)
* Add jump link anchor to each slice template `{% if jump_link is defined %}<a class="js-jump-link-anchor" id="{{ jump_link.anchor }}"></a>{% endif %}`

## Functional Tests

SIMPLETEST_BASE_URL and SIMPLETEST_DB must be set.

From the module directory run:

` ../../../../vendor/bin/phpunit .`
