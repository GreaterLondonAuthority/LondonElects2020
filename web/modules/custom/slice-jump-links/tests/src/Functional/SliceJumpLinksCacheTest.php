<?php

namespace Drupal\Tests\slice_jump_links\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\simpletest\WebTestBase;
use Drupal\Tests\BrowserTestBase;

/**
 * @group slice_jump_links
 */
class SliceJumpLinksCacheTest extends BrowserTestBase {

  public static $modules = ['node', 'link', 'block', 'slice_jump_links'];

  private $node1;
  private $node2;
  private $user;
  private $labelFieldName = 'field_jump_link_label';

  public function setUp() {
    parent::setUp();

    // Enable page caching.
    $config = $this->config('system.performance');
    $config->set('cache.page.max_age', 3600);
    $config->save();

    // Create a content type with the jump link label field
    $nodeType = $this->drupalCreateContentType();

    $field_storage = FieldStorageConfig::loadByName('node', $this->labelFieldName);
    $field = FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $nodeType->id(),
      'label' => 'Slice label',
    ]);
    $field->save();

    // Show the field on the types form
    entity_get_form_display('node', $nodeType->id(), 'default')
      ->setComponent($this->labelFieldName)
      ->save();

    // Hide the field on the default view mode
    entity_get_display('node', $nodeType->id(), 'default')
      ->removeComponent($this->labelFieldName)
      ->save();

    // Create a node of this type

    $this->node1 = $this->drupalCreateNode([
      'type' => $nodeType->id(),
      'field_jump_link_label' => [
        'value' => 'This is the label'
      ]
    ]);

    $this->node2 = $this->drupalCreateNode([
      'type' => $nodeType->id(),
      'field_jump_link_label' => [
        'value' => 'Label on node 2'
      ]
    ]);

    $this->drupalPlaceBlock('jump_link_block');

    $this->user = $this->drupalCreateUser([
      'edit any ' . $nodeType->id() . ' content',
      'administer nodes'
    ]);

  }

  /**
   * Test that the jump link block's cache settings are correct
   */
  public function testBlockCache() {

    // Ensure that the block appears per-node
    $this->drupalGet('node/' . $this->node1->id());
    $this->assertSession()->responseContains('This is the label');

    $this->drupalGet('node/' . $this->node2->id());
    $this->assertSession()->responseContains('Label on node 2');

    // Ensure that the block's cache expires when the node changes
    $this->drupalLogin($this->user);
    $this->drupalGet('node/' . $this->node1->id() . '/edit');

    $formFieldName = $this->labelFieldName . '[0][value]';

    $this->assertSession()->fieldExists($formFieldName);
    $this->submitForm(
      [
        $formFieldName => 'Changed label here'
      ],
      t('Save and keep published')
    );
    $this->assertSession()->statusCodeEquals(200);
    $this->assertSession()->responseContains('Changed label here');
  }

}
