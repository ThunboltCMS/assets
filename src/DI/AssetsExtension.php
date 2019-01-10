<?php declare(strict_types = 1);

namespace Thunbolt\Assets\DI;

use Nette\DI\CompilerExtension;
use Thunbolt\Assets\AssetsLoader;
use Thunbolt\Assets\IAssetsLoader;
use Thunbolt\Assets\Loaders\WebpackAssetsLoader;
use WebChemistry\Utils\Strings;

final class AssetsExtension extends CompilerExtension {

	/** @var array */
	public $defaults = [
		'styles' => [],
		'javascript' => [],
		'webpack' => [
			'module' => 'src',
			'directory' => null,
			'file' => 'webpack-hash',
		]
	];

	protected function isAbsolute(string $url): bool {
		foreach (['//', 'https://', 'http://'] as $item) {
			if (Strings::startsWith($url, $item)) {
				return true;
			}
		}

		return false;
	}

	public function parseConfig(): array {
		$config = $this->validateConfig($this->defaults);
		if ($config['webpack']['directory'] === null) {
			$config['webpack']['directory'] = $this->getContainerBuilder()->parameters['appVarDir'];
		}

		return $config;
	}

	public function loadConfiguration(): void {
		$builder = $this->getContainerBuilder();
		$config = $this->parseConfig();

		$webpack = $config['webpack'];
		$builder->addDefinition($this->prefix('webpackLoader'))
			->setFactory(WebpackAssetsLoader::class, [
				$webpack['directory'] . '/' . $webpack['file'],
				$webpack['module'],
			]);

		$def = $builder->addDefinition($this->prefix('assetsLoader'))
			->setType(IAssetsLoader::class)
			->setFactory(AssetsLoader::class);

		foreach ($config['styles'] as $style) {
			$def->addSetup('addStyle', [$style, is_string($style) ? $this->isAbsolute($style) : false]);
		}
		foreach ($config['javascript'] as $js) {
			$def->addSetup('addJavascript', [$js, is_string($js) ? $this->isAbsolute($js) : false]);
		}
	}

}
