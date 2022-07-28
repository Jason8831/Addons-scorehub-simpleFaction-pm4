<?php
declare(strict_types = 1);

namespace Ayzrix\SFAddon;

use Ayzrix\SFAddon\Events\Listeners\TagResolveListener;
use Ayzrix\SimpleFaction\API\FactionsAPI;
use Ifera\ScoreHud\scoreboard\ScoreTag;
use Ifera\ScoreHud\event\PlayerTagUpdateEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;

class Main extends PluginBase{

    /** @var  */
    private $SimpleFaction;

    public function onEnable(): void {
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new TagResolveListener($this), $this);

        $this->getScheduler()->scheduleRepeatingTask(new ClosureTask(function(): void{
            foreach($this->getServer()->getOnlinePlayers() as $player){
                if(!$player->isOnline()){
                    continue;
                }

                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefaction.faction", strval($this->getFaction($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefaction.power", strval($this->getPower($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefaction.money", strval($this->getMoney($player)))))->call();
                (new PlayerTagUpdateEvent($player, new ScoreTag("simplefaction.rank", strval($this->getRank($player)))))->call();
            }
        }), 20);
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getFaction(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getFaction($player->getName());
        } else return "...";
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getPower(Player $player): int {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getPower(self::getFaction($player));
        } else return 0;
    }

    /**
     * @param Player $player
     * @return int
     */
    public function getMoney(Player $player): int {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getMoney(self::getFaction($player));
        } else return 0;
    }

    /**
     * @param Player $player
     * @return string
     */
    public function getRank(Player $player): string {
        if (FactionsAPI::isInFaction($player->getName())) {
            return FactionsAPI::getRank($player->getName());
        } else return "...";
    }
}
