<?php

namespace Drupal\Tests\pdb\Unit\Plugin\Derivative;

use Drupal\Tests\UnitTestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\pdb\Plugin\Derivative\PdbBlockDeriver;
use Drupal\pdb\ComponentDiscoveryInterface;

/**
 * @coversDefaultClass \Drupal\pdb\Plugin\Derivative\PdbBlockDeriver
 * @group pdb
 */
class PdbBlockDeriverTest extends UnitTestCase {

  /**
   * Mocked Component Discovery.
   *
   * @var \Drupal\pdb\ComponentDiscoveryInterface
   */
  protected $componentDiscovery;

  /**
   * Instance of the Block Deriver.
   *
   * @var \Drupal\pdb\Plugin\Derivative\PdbBlockDeriver
   */
  protected $deriver;

  /**
   * Create the setup for constants.
   */
  protected function setUp() {
    parent::setUp();

    // Mock the UUID service.
    $this->componentDiscovery = $this->prophesize(ComponentDiscoveryInterface::CLASS);
    $this->componentDiscovery->getComponents()->willReturn([
      'block_1' => (object) [
        'type' => 'pdb',
        'info' => [
          'name' => 'Block 1',
          'machine_name' => 'block_1',
          'presentation' => 'pdb',
          'contexts' => ['entity' => 'node'],
        ],
      ],
    ]);

    $this->deriver = new PdbBlockDeriver($this->componentDiscovery->reveal());
  }

  /**
   * Tests the create method.
   *
   * @see ::create()
   */
  public function testCreate() {
    $base_plugin_id = 'pdb';

    $container = $this->prophesize(ContainerInterface::CLASS);
    $container->get('pdb.component_discovery')
      ->willReturn($this->componentDiscovery);

    $instance = PdbBlockDeriver::create(
      $container->reveal(),
      $base_plugin_id
    );
    $this->assertInstanceOf('Drupal\pdb\Plugin\Derivative\PdbBlockDeriver', $instance);
  }

  /**
   * Tests the getDerivativeDefinitions() method.
   */
  public function testGetDerivativeDefinitions() {
    $base_plugin_definition = [
      'provider' => 'pdb',
    ];

    // example_1 should not appear due to debug mode being off.
    $expected = [
      'block_1' => [
        'info' => [
          'name' => 'Block 1',
          'machine_name' => 'block_1',
          'presentation' => 'pdb',
          'contexts' => ['entity' => 'node'],
        ],
        'provider' => 'pdb',
        'admin_label' => 'Block 1',
        'cache' => ['max-age' => 0],
      ],
    ];

    $return = $this->deriver->getDerivativeDefinitions($base_plugin_definition);
    $this->assertInstanceOf('Drupal\Core\Plugin\Context\EntityContextDefinition', $return['block_1']['context_definitions']['entity']);

    // Remove the context to compare the arrays.
    unset($return['block_1']['context_definitions']);
    $this->assertEquals($expected, $return);
  }

}
