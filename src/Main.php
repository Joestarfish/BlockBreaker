<?php

declare(strict_types=1);

namespace Joestarfish\BlockBreaker;

use pocketmine\block\BlockTypeIds;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\StringToItemParser;
use pocketmine\plugin\PluginBase;

class Main extends PluginBase implements Listener {
	private array $blocks_to_items = [];
	private bool $do_break_block;
	private bool $do_drop_xp;

	public function onEnable(): void {
		$this->loadConfig();
		$this->getServer()
			->getPluginManager()
			->registerEvents($this, $this);
	}

	public function onBreak(BlockBreakEvent $event) {
		$bid = $event->getBlock()->getTypeId();
		if (!isset($this->blocks_to_items[$bid])) {
			return;
		}

		$iid = $event->getItem()->getTypeId();
		if (in_array($iid, $this->blocks_to_items[$bid])) {
			return;
		}

		if (!$this->do_break_block) {
			$event->cancel();
			return;
		}

		if (!$this->do_drop_xp) {
			$event->setXpDropAmount(0);
		}

		$event->setDrops([]);
	}

	private function loadConfig() {
		$config = $this->getConfig();

		$this->do_break_block = (bool) $config->get('do-break-block', true);
		$this->do_drop_xp = (bool) $config->get('do-drop-xp', false);

		foreach ($config->get('blocks', []) as $block_name => $items_list) {
			/** @var ?Item $block_item */
			$block_item = StringToItemParser::getInstance()->parse($block_name);
			$bid = $block_item?->getBlock()->getTypeId();

			if (!$bid || $bid == BlockTypeIds::AIR) {
				$this->getLogger()->alert(
					"The block $block_name does not exist",
				);
				continue;
			}

			// We init it explicitly in case items_list is empty (no item can destroy the block)
			$this->blocks_to_items[$bid] = [];

			foreach ($items_list as $item_name) {
				/** @var ?Item $item */
				$item = StringToItemParser::getInstance()->parse($item_name);
				if (!$item) {
					$this->getLogger()->alert(
						"The item $item_name does not exist",
					);
					continue;
				}
				$this->blocks_to_items[$bid][] = $item->getTypeId();
			}
		}

		foreach ($config->get('items', []) as $item_name => $block_list) {
			/** @var ?Item $item */
			$item = StringToItemParser::getInstance()->parse($item_name);
			if (!$item) {
				$this->getLogger()->alert("The item $item_name does not exist");
				continue;
			}

			foreach ($block_list as $block_name) {
				/** @var ?Item $block_item */
				$block_item = StringToItemParser::getInstance()->parse(
					$block_name,
				);
				$bid = $block_item?->getBlock()->getTypeId();

				if (!$bid || $bid == BlockTypeIds::AIR) {
					$this->getLogger()->alert(
						"The block $block_name does not exist",
					);
					continue;
				}

				$this->blocks_to_items[$bid][] = $item->getTypeId();
			}
		}
	}
}
