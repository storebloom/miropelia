<?php

/**
 * Class Ga_Template
 */
class Ga_Template {
	/**
	 * @var array Array of template properties.
	 */
	protected $props;

	/**
	 * @var string Relative path in view/ folder.
	 */
	protected $path;

	/**
	 * Ga_Template constructor.
	 *
	 * @param string $path Relative path in view/ folder.
	 * @param array $props Array of props to be passed to the template.
	 */
	public function __construct( $path, $props = [] ) {
		$this->path  = $path;
		$this->props = $props;
	}

	/**
	 * Include rendered template inline.
	 *
	 * @param string $path Relative path in view/ folder.
	 * @param array $props Array of props to be passed to the template.
	 */
	public static function load( $path, $props = [] ) {
		( new static( $path, $props ) )->includeTemplate();
	}

	/**
	 * Get rendered template.
	 *
	 * @param string $path Relative path in view/ folder.
	 * @param array $props Array of props to be passed to the template.
	 *
	 * @return string Rendered template.
	 */
	public static function render( $path, $props = [] ) {
		return ( new static( $path, $props ) )->renderTemplate();
	}

	/**
	 * Include template.
	 */
	public function includeTemplate() {
		$template_path = GA_PLUGIN_DIR . '/view/' . $this->path . '.php';

		if ( is_readable( $template_path ) ) {
			load_template( $template_path, false, $this->props );
		}
	}

	/**
	 * Get rendered template.
	 *
	 * @return string
	 */
	public function renderTemplate() {
		ob_start();
		$this->includeTemplate();
		$render = ob_get_contents();

		return $render ?: '';
	}
}