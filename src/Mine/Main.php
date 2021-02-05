<?php

declare(strict_types=1);

namespace Mine;

use pocketmine\block\Block;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;

/**
 * Class Main
 * @package Mine
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class Main extends PluginBase {

	private static Main $instance;

	public array $blocks;
	public array $places;
	/** @var Block[] */
	public array $inQueue = [];

	public function onLoad(): void {
		self::setInstance($this);

		$blocksArray = $this->getConfig()->get('blocks');
		$placesArray = $this->getConfig()->get('places');

		foreach ($blocksArray as $id => $block) {
			$this->blocks[$block] = $id;
		}

		foreach ($placesArray as $id => $place) {
			$this->places[$place] = $id;
		}
	}

	public function onEnable(): void {
		$this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
	}

	public function onDisable(): void {
		foreach ($this->inQueue as $queue) {
			Server::getInstance()->getDefaultLevel()->setBlock($queue, $queue);
		}

		Server::getInstance()->getDefaultLevel()->save(true);
	}

	/**
	 * @param Main $instance
	 */
	private static function setInstance(Main $instance): void {
		self::$instance = $instance;
	}

	/**
	 * @return Main
	 */
	public static function getInstance(): Main {
		return self::$instance;
	}
}