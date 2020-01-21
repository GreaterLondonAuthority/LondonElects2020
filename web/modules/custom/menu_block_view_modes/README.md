# Menu Block View Modes #

Extension to Menu Block to enable the output of menu items as entities with a specified view modes.

## Usage ##

When selected this module makes an additional `item.entity` property available to your twig templates, you can then check for this in the menu template and output instead of  menu link.

## Requirements ##

* Menu Block
* Patch to enable alter hooks & module additions: https://www.drupal.org/files/issues/2779465_menu_block_alter_hooks.patch

## Outstanding ##
This module still needs:

* to work with entities other than nodes
* to have tests written