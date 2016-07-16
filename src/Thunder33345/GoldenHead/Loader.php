<?php
/** Created By Thunder33345 **/
namespace Thunder33345\GoldenHead;

use pocketmine\entity\Effect;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\inventory\BigShapedRecipe;
use pocketmine\item\Item;
use pocketmine\plugin\PluginBase;

class Loader extends PluginBase implements Listener
{
	/**
	 * @var \stdClass
	 */
	private $json, $nuggetMeta, $ingotMeta, $blockMeta;

	public function onLoad()
	{
	}

	public function onEnable()
	{
		//Load up sequence
		if (!file_exists($this->getDataFolder())) {
			@mkdir($this->getDataFolder(), 0777, true);
			$this->makeConfig(false);
		}
		$this->reloadConfig();
		//print_r($this->json);
		$json = $this->json;
		//Check sequence
		//Init sequence
		$this->nuggetMeta = null;
		$this->ingotMeta = null;
		$this->blockMeta = null;
		if ($json->shape->nugget->enable == true) {
			$this->nuggetMeta = $json->shape->nugget->meta;
			$this->getLogger()->info('Registering nugget recipe...');
			$this->registerNugget();
		}
		if ($json->shape->ingot->enable == true) {
			$this->ingotMeta = $json->shape->ingot->meta;
			$this->getLogger()->info('Registering ingot recipe...');
			$this->registerIngot();
		}
		if ($json->shape->block->enable == true) {
			$this->blockMeta = $json->shape->block->meta;
			$this->getLogger()->info('Registering block recipe...');
			$this->registerBlock();
		}
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->getLogger()->info('Golden Head By Thunder33345 Loaded Successfully');

	}

	public function onDisable()
	{
	}

	public function playerEatEV(PlayerItemConsumeEvent $ev)
	{
		if ($ev->getItem()->getId() == Item::GOLDEN_APPLE) {
			switch ($meta = $ev->getItem()->getDamage()) {
				case $meta == $this->nuggetMeta:
					$p = $ev->getPlayer();
					foreach ($this->json->effects->nugget as $eff) {
						$p->addEffect(Effect::getEffect($eff->id)->setDuration(20 * $eff->time)->setAmplifier($eff->amp));
					}
					break;
				case $meta == $this->ingotMeta:
					$p = $ev->getPlayer();
					foreach ($this->json->effects->ingot as $eff) {
						$p->addEffect(Effect::getEffect($eff->id)->setDuration(20 * $eff->time)->setAmplifier($eff->amp));
					}
					break;
				case $meta == $this->blockMeta:
					$p = $ev->getPlayer();
					foreach ($this->json->effects->block as $eff) {
						$p->addEffect(Effect::getEffect($eff->id)->setDuration(20 * $eff->time)->setAmplifier($eff->amp));
					}
					break;
			}
		}
	}

	private function registerNugget()
	{
		$json = $this->json->shape->nugget;
		if (is_numeric($json->head_meta)) {
			$this->getServer()->getCraftingManager()->registerRecipe(
				(new BigShapedRecipe(Item::get(Item::GOLDEN_APPLE, $json->meta, $json->amount)
					->setCustomName($json->name),
					"GGG",
					"GHG",
					"GGG"))
					->setIngredient("G", Item::get(Item::GOLD_NUGGET, 0, $json->gold))
					->setIngredient("H", Item::get(Item::MOB_HEAD, $json->head_meta, $json->head))
			);
			$this->getLogger()->info('Successfully register nugget recipe');
		} else {
			$this->getLogger()->error('Fail to register Nugget Recipe');
			$this->nuggetMeta = null;
		}
	}

	private function registerIngot()
	{
		$json = $this->json->shape->ingot;
		if (is_numeric($json->head_meta)) {
			$this->getServer()->getCraftingManager()->registerRecipe(
				(new BigShapedRecipe(Item::get(Item::GOLDEN_APPLE, $json->meta, $json->amount)
					->setCustomName($json->name),
					"GGG",
					"GHG",
					"GGG"))
					->setIngredient("G", Item::get(Item::GOLD_INGOT, 0, $json->gold))
					->setIngredient("H", Item::get(Item::MOB_HEAD, $json->head_meta, $json->head))
			);
			$this->getLogger()->info('Successfully register ingot recipe');
		} else {
			$this->getLogger()->error('Fail to register ingot Recipe');
			$this->ingotMeta = null;
		}
	}

	private function registerBlock()
	{
		$json = $this->json->shape->block;
		if (is_numeric($json->head_meta)) {
			$this->getServer()->getCraftingManager()->registerRecipe(
				(new BigShapedRecipe(Item::get(Item::GOLDEN_APPLE, $json->meta, $json->amount)
					->setCustomName($json->name),
					"GGG",
					"GHG",
					"GGG"))
					->setIngredient("G", Item::get(Item::GOLD_BLOCK, 0, $json->gold))
					->setIngredient("H", Item::get(Item::MOB_HEAD, $json->head_meta, $json->head))
			);
			$this->getLogger()->info('Successfully register block recipe');
		} else {
			$this->getLogger()->error('Fail to register block Recipe');
			$this->blockMeta = null;
		}
	}

	public function reloadConfig()
	{
		$file = file_get_contents($this->getDataFolder() . '/config.json');
		if ($file === false) {
			$this->makeConfig(true);
		} else {
			$this->json = json_decode($file);
			if ($this->json->reset == true) {
				$this->makeConfig(true);
			}
		}
	}

	private function makeConfig($force = false)
	{
		if (@file_get_contents($this->getDataFolder() . '/config.json') === false or $force == true) {
			$jsontxt = '{
  "shape": {
    "nugget": {
      "enable": true,
      "head": "1",
      "head_meta": "3",
      "gold": "1",
      "meta": "2",
      "name": "Golden Head (nugget)",
      "amount": "1"
    },
    "ingot": {
      "enable": true,
      "head": "1",
      "head_meta": "3",
      "gold": "1",
      "meta": "3",
      "name": "Golden Head (ingot)",
      "amount": "1"
    },
    "block": {
      "enable": true,
      "head": "1",
      "head_meta": "3",
      "gold": "1",
      "meta": "4",
      "name": "Golden Head (block)",
      "amount": "1"
    }
  },
  "effects": {
    "nugget": [
      {
        "id": "22",
        "time": "60",
        "amp": "2"
      },
      {
        "id": "10",
        "time": "3",
        "amp": "1"
      }
    ],
    "ingot": [
      {
        "id": "22",
        "time": "120",
        "amp": "1"
      },
      {
        "id": "10",
        "time": "5",
        "amp": "2"
      }
    ],
    "block": [
      {
        "id": "22",
        "time": "120",
        "amp": "1"
      },
      {
        "id":"21",
        "time":"120",
        "amp":"4"
      },
      {
        "id": "10",
        "time": "25",
        "amp": "3"
      },
      {
        "id":"12",
        "time":"305",
        "amp":"1"
      },
      {
        "id":"11",
        "time":"305",
        "amp":"2"
      }
    ]
  },
  "reset": false
}';
			file_put_contents($this->getDataFolder() . '/config.json', $jsontxt);
			$this->reloadConfig();
		}
	}
}
