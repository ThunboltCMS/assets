<?php declare(strict_types = 1);

namespace Thunbolt\Assets;

use Nette\Http\IRequest;
use Nette\Http\IResponse;
use Nette\Utils\Html;

class AssetsLoader implements IAssetsLoader {

	/** @var IResponse */
	private $response;

	/** @var IRequest */
	private $request;

	/** @var array */
	private $styles = [];

	/** @var array */
	private $javascript = [];

	public function __construct(IResponse $response, IRequest $request) {
		$this->response = $response;
		$this->request = $request;
	}

	public function addStyle(string $link, bool $absolute = false) {
		$this->styles[] = ($absolute ? '' : $this->request->getUrl()->getBasePath()) .$link;

		return $this;
	}

	public function addJavascript(string $link, bool $absolute = false) {
		$this->javascript[] = ($absolute ? '' : $this->request->getUrl()->getBasePath()) . $link;

		return $this;
	}

	public function preload(): void {
		foreach ($this->styles as $style) {
			$this->response->addHeader('Link', "<$style>; rel=preload; as=style");
		}
		foreach ($this->javascript as $js) {
			$this->response->addHeader('Link', "<$js>; rel=preload; as=script");
		}
	}

	public function getHtmlStyles(): Html {
		$wrapper = Html::el();

		foreach ($this->styles as $href) {
			$wrapper->create('link', [
				'rel' => 'stylesheet',
				'href' => $href,
			]);
		}

		return $wrapper;
	}

	public function getHtmlJavascript(): Html {
		$wrapper = Html::el();

		foreach ($this->javascript as $href) {
			$wrapper->create('script', [
				'src' => $href,
			]);
		}

		return $wrapper;
	}

}
