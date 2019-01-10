<?php declare(strict_types = 1);

namespace Thunbolt\Assets\Loaders;

final class WebpackAssetsLoader {

	private const HASH_INIT = 0;

	/** @var string */
	private $file;

	/** @var string */
	private $hash = self::HASH_INIT;

	/** @var string */
	private $module;

	public function __construct(string $file, string $module) {
		$this->file = $file;
		$this->module = $module;
	}

	protected function getHash(): ?string {
		if ($this->hash === self::HASH_INIT) {
			if (!file_exists($this->file)) {
				return null;
			}

			$this->hash = file_get_contents($this->file);
		}

		return $this->hash;
	}

	protected function getFullHash(): string {
		$hash = $this->getHash();

		return $hash ? $hash . '.' : '';
	}

	public function getStyle(): string {
		return 'dist/' . $this->module . '.' . $this->getFullHash() . 'css';
	}

	public function getJavascript(): string {
		return 'dist/' . $this->module . '.' . $this->getFullHash() . 'js';
	}

}
