<?php

namespace Drupal\Tests\google_place_autocomplete\Functional;

use Drupal\Tests\BrowserTestBase;

/**
 * Simple test to ensure that manage form display works.
 *
 * @group google_place_autocomplete
 */
class ManageFormDisplayTest extends BrowserTestBase {

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * Modules to enable.
   *
   * @var array
   */
  protected static $modules = [
    'google_place_autocomplete',
    'key',
    'node',
    'datetime',
    'user',
    'field_ui',
    'help',
  ];

  /**
   * A user with permission to administer site configuration.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $user;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    // Create page content type.
    $this->drupalCreateContentType([
      'type' => 'page',
      'name' => 'Basic page',
      'display_submitted' => FALSE,
    ]);

    // Create admin user.
    $this->user = $this->createUser([
      'administer content types',
      'administer nodes',
      'access content',
      'administer node fields',
      'administer node form display',
      'administer site configuration',
      'administer keys',
    ]);

    // Login user.
    $this->drupalLogin($this->user);
  }

  /**
   * Tests that the manage form display works.
   */
  public function testManageFormDisplayPage() {
    // Get session.
    $assert_session = $this->assertSession();

    // Goto manage form display page for "Basic page" node.
    $this->drupalGet('admin/structure/types/manage/page/form-display');
    $assert_session->statusCodeEquals(200);

    // Field information.
    $field_name = 'fields[title][type]';
    $field_option = 'google_place_autocomplete_field_widget';

    // Setup variables for further use.
    $session = $this->getSession();
    $page = $session->getPage();

    // Also make sure option is available for selection. It should appear
    // at the very least.
    $assert_session->optionExists($field_name, $field_option);

    // Find title type field and save button.
    $field_title_type = $page->findField($field_name);
    $button_save = $page->findButton('Save');

    // Update title type field with our custom field widget and save form.
    $field_title_type->setValue($field_option);
    $button_save->click();

    // Load up manage form display page.
    $this->drupalGet('admin/structure/types/manage/page/form-display');
    $assert_session->statusCodeEquals(200);

    // Make sure out field widget is selected.
    $this->assertEquals($field_option, $field_title_type->getValue(), 'The expected widget is selected.');
  }

}
