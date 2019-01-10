<?php declare(strict_types = 1);

namespace Thunbolt\Assets;

use Nette\Utils\Html;

interface IAssetsLoader {

	public function addStyle(string $link, bool $absolute = false);

	public function addJavascript(string $link, bool $absolute = false);

	public function preload(): void;

	public function getHtmlStyles(): Html;

	public function getHtmlJavascript(): Html;

}
