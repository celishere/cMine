<?php

declare(strict_types=1);

namespace Mine;

use pocketmine\Server;
use pocketmine\block\Block;

use pocketmine\scheduler\Task as PMTask;

/**
 * Class Task
 * @package Mine
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class Task extends PMTask {

	private Block $block;
	private int $i;

	/**
	 * Task constructor.
	 * @param Block $block
	 */
	public function __construct(Block $block) {
		$this->block = $block;

		$this->i = count(Main::getInstance()->inQueue) + 1;

		Main::getInstance()->inQueue[$this->i] = $block;
	}

	public function onRun(int $currentTick): void {
		$block = $this->block;

		Server::getInstance()->getDefaultLevel()->setBlock($block, $block);

		unset(Main::getInstance()->inQueue[$this->i]);
    }
}