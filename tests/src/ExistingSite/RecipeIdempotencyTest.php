<?php

declare(strict_types=1);

namespace Drupal\Tests\starshot\ExistingSite;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use weitzman\DrupalTestTraits\ExistingSiteBase;

/**
 * @group starshot
 */
class RecipeIdempotencyTest extends ExistingSiteBase {

  public static function providerRecipeIdempotency(): iterable {
    $finder = Finder::create()
      ->in(__DIR__ . '/../../../recipes')
      ->directories()
      ->depth(0);

    /** @var \Symfony\Component\Finder\SplFileInfo $dir */
    foreach ($finder as $dir) {
      yield $dir->getBasename() => [
        $dir->getPathname(),
      ];
    }
  }

  /**
   * @dataProvider providerRecipeIdempotency
   */
  public function testRecipeIdempotency(string $path): void {
    $arguments = [
      (new PhpExecutableFinder())->find(),
      'core/scripts/drupal',
      'recipe',
      $path,
    ];
    $process = (new Process($arguments))
      ->setWorkingDirectory(__DIR__ . '/../../../web')
      ->setTimeout(500);

    $process->run();
    $this->assertSame(0, $process->getExitCode(), $process->getErrorOutput());
  }

}
