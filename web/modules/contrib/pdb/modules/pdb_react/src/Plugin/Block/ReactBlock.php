<?php

namespace Drupal\pdb_react\Plugin\Block;

use Drupal\pdb\Plugin\Block\PdbBlock;

/**
 * Exposes a React component as a block.
 *
 * @Block(
 *   id = "react_component",
 *   admin_label = @Translation("React component"),
 *   deriver = "\Drupal\pdb_react\Plugin\Derivative\ReactBlockDeriver"
 * )
 */
class ReactBlock extends PdbBlock {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $info = $this->getComponentInfo();
    $machine_name = $info['machine_name'];

    $build = parent::build();
    $build['#allowed_tags'] = [$machine_name];
    $build['#markup'] = '<' . $machine_name . ' id="' . $machine_name . '"></' . $machine_name . '>';

    return $build;
  }

  /**
   * {@inheritdoc}
   */
  public function attachSettings(array $component) {
    $machine_name = $component['machine_name'];

    $attached = [];
    $attached['drupalSettings']['react-apps'][$machine_name]['uri'] = '/' . $component['path'];

    return $attached;
  }

  /**
   * {@inheritdoc}
   */
  public function attachLibraries(array $component) {
    $parent_libraries = parent::attachLibraries($component);

    $framework_libraries = [
      'pdb_react/react',
      'pdb_react/components',
    ];

    $libraries = [
      'library' => array_merge($parent_libraries, $framework_libraries),
    ];

    return $libraries;
  }

}
