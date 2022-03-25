<?php

namespace Drupal\Tests\pdb\Unit\Plugin\Block;

use Drupal\Tests\UnitTestCase;
use Drupal\pdb\Plugin\Block\PdbBlock;
use Drupal\Core\DependencyInjection\ContainerBuilder;
use Drupal\Core\Plugin\Context\EntityContextDefinition;
use Drupal\Component\Uuid\UuidInterface;

/**
 * @coversDefaultClass \Drupal\pdb\Plugin\Block\PdbBlock
 * @group pdb
 */
class PdbBlockTest extends UnitTestCase {

  /**
   * Instance of the Plugin.
   *
   * @var \Drupal\pdb\Plugin\Block\PdbBlock
   */
  protected $plugin;

  /**
   * Create the setup for constants and plugin instance.
   */
  protected function setUp() {
    parent::setUp();

    // Mock the UUID service.
    $uuid = $this->prophesize(UuidInterface::CLASS);
    $uuid->generate()->willReturn('uuid');

    $context_definition = $this->prophesize(EntityContextDefinition::CLASS);

    // Create a container needed by PdbBlock.
    $container = new ContainerBuilder();
    $container->set('uuid', $uuid->reveal());
    \Drupal::setContainer($container);

    $configuration = [
      'pdb_configuration' => [
        'testField' => 'test',
        'second_field' => 1,
      ],
    ];
    $plugin_id = 'pdb';
    $plugin_definition = [
      'provider' => 'pdb',
      'info' => [
        'machine_name' => 'example-1',
        'add_js' => [
          'footer' => [
            'example-1.js' => [],
          ],
        ],
        'settings' => [
          'pdb' => ['settings test'],
        ],
      ],
      'context_definitions' => [
        'entity' => $context_definition->reveal(),
      ],
    ];

    // Create a new instance from the Abstract Class.
    $anonymous_class_from_abstract = new class($configuration, $plugin_id, $plugin_definition) extends PdbBlock {

      public function returnThis() {
        return $this;
      }

      public function attachFramework(array $component) {
        return ['drupalSettings' => ['pdb' => ['webcomponents' => []]]];
      }

      public function attachPageHeader(array $component) {
        return ['page_attachment'];
      }

      protected function getJsContexts(array $contexts) {
        return 'js_contexts';
      }

    };

    $this->plugin = $anonymous_class_from_abstract->returnThis();
  }

  /**
   * Tests the build() method.
   */
  public function testBuild() {
    $expected = [
      '#attached' => [
        'drupalSettings' => [
          'pdb' => [
            'settings test',
            'webcomponents' => [],
            'configuration' => [
              'uuid' => [
                'testField' => 'test',
                'second_field' => 1,
              ],
            ],
            'contexts' => 'js_contexts',
          ],
        ],
        'pdb/example-1/footer',
        'page_attachment',
      ],
    ];

    $return = $this->plugin->build();
    $this->assertEquals($expected, $return);
  }

  /**
   * Tests the attachLibraries() method.
   *
   * @dataProvider attachLibrariesProvider
   */
  public function testAttachLibraries($value, $expected) {
    $component = [
      'machine_name' => 'example-1',
    ];

    $component = array_merge($component, $value);

    $return = $this->plugin->attachLibraries($component);
    $this->assertEquals($expected, $return);
  }

  /**
   * Provider for testAttachLibraries().
   */
  public function attachLibrariesProvider() {
    return [
      [
        [
          'add_js' => [
            'header' => [
              'example-1.js' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/header',
        ],
      ],
      [
        [
          'add_css' => [
            'header' => [
              'example-1.css' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/header',
        ],
      ],
      [
        [
          'add_css' => [
            'header' => [
              'example-1.css' => [],
            ],
          ],
          'add_js' => [
            'header' => [
              'example-1.js' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/header',
        ],
      ],
      [
        [
          'add_js' => [
            'footer' => [
              'example-1.js' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/footer',
        ],
      ],
      [
        [
          'add_css' => [
            'footer' => [
              'example-1.css' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/footer',
        ],
      ],
      [
        [
          'add_css' => [
            'footer' => [
              'example-1.css' => [],
            ],
          ],
          'add_js' => [
            'footer' => [
              'example-1.js' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/footer',
        ],
      ],
      [
        [
          'add_css' => [
            'header' => [
              'example-1.css' => [],
            ],
            'footer' => [
              'example-1.css' => [],
            ],
          ],
          'add_js' => [
            'header' => [
              'example-1.js' => [],
            ],
            'footer' => [
              'example-1.js' => [],
            ],
          ],
        ],
        [
          'pdb/example-1/header',
          'pdb/example-1/footer',
        ],
      ],
    ];
  }

  /**
   * Tests the attachSettings() method.
   */
  public function testAttachSettings() {
    $component = [
      'settings' => [
        'pdb' => ['foobar'],
      ],
    ];

    $expected = [
      'drupalSettings' => [
        'pdb' => ['foobar'],
      ],
    ];

    $return = $this->plugin->attachSettings($component);
    $this->assertEquals($expected, $return);
  }

}
