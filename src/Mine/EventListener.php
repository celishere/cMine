<?php

declare(strict_types=1);

namespace Mine;

use pocketmine\block\Block;

use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\item\Item;

use pocketmine\Server;

use pocketmine\utils\TextFormat;

use Tools\tool\LocationUtils;

use JsonException;

/**
 * Class EventListener
 * @package Mine
 *
 * @version 1.0.0
 * @since   1.0.0
 */
class EventListener implements Listener {

	private array $blocks = [];

	public function onBreak(BlockBreakEvent $e): void {
		$block = $e->getBlock();

		try {
			$string = LocationUtils::packPositionToRaw($block->asPosition());

			if (isset(Main::getInstance()->blocks[$string])) {
				$e->setCancelled();

				if ($e->getPlayer()->getGamemode() !== 0) {
					$e->getPlayer()->sendPopup(TextFormat::RED. 'Только в режиме выживания.');
					return;
				}

				if ($block->getId() !== Block::REDSTONE_BLOCK) {
					Server::getInstance()->getDefaultLevel()->dropItem($block->add(0.5, 1, 0.5), Item::get($block->getId()));
					Main::getInstance()->getScheduler()->scheduleDelayedTask(new Task($block), 20 * 7);

					Server::getInstance()->getDefaultLevel()->setBlock($block, Block::get(Block::REDSTONE_BLOCK, 0, $block));
				}
			}
		} catch (JsonException $e) {}
	}

	public function onPlace(BlockPlaceEvent $e): void {
		$block = $e->getBlock();

		try {
			$string = LocationUtils::packPositionToRaw($block->asPosition());

			if (isset(Main::getInstance()->places[$string])) {
				$e->setCancelled();

				$player = $e->getPlayer();
				$inv = $player->getInventory();
				$uuid = $player->getUniqueId()->toString();

				if ($player->getGamemode() !== 0) {
					$player->sendPopup(TextFormat::RED. 'Только в режиме выживания.');
					return;
				}

				if (!isset($this->blocks[$uuid][$block->getId()])){
					$this->blocks[$uuid][$block->getId()] = 0;
				}

				$this->blocks[$uuid][$block->getId()]++;

				$inv->removeItem(Item::get($inv->getItemInHand()->getId()));
				$player->sendMessage(TextFormat::colorize("&eВсего было доставлено этой руды&7: &a&l".$this->blocks[$uuid][$block->getId()]."&r&8/&l&c9"));

				if($this->blocks[$uuid][$block->getId()] == 9){
					$player->sendMessage(TextFormat::colorize("&aОтличная работа! &f+ &e{reward}&2$&f!"));

					unset($this->blocks[$uuid][$block->getId()]);
				}
			}
		} catch (JsonException $e) {}
	}
}