<?php

declare(strict_types=1);

namespace venndev\plugin\listener;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\math\Vector3;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\player\Player;
use venndev\plugin\item\BaseItem;
use venndev\plugin\manager\ItemManager;
use venndev\plugin\manager\PluginSettings;
use venndev\plugin\manager\ServerHandler;
use venndev\plugin\manager\StatsManager;
use venndev\plugin\utils\ClickMode;
use venndev\plugin\utils\ItemUtil;
use venndev\plugin\utils\ParticlesAbility;
use venndev\plugin\utils\StatsItem;
use venndev\plugin\VItemsAPI;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;
use vennv\vapm\System;
use Throwable;

final class EventListener implements Listener
{
    use StatsItem;
    use ParticlesAbility;

    public function onPlayerJoinEvent(PlayerJoinEvent $event) : void
    {
        $player = $event->getPlayer();
        $statsManager = new StatsManager($player);
        $statsManager->setHasJoined(true);
    }

    public function onPlayerQuitEvent(PlayerQuitEvent $event) : void
    {
        $player = $event->getPlayer();

        // Clear the stats handler for the player when they leave the server.
        $statsManager = new StatsManager($player);
        $statsManager->clearStatsHandler();
        $statsManager->clearItemStatsHandler();
        $statsManager->setHasJoined(false);
    }

    public function onBlockBreak(BlockBreakEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $player->getInventory()->getItemInHand();

        ItemManager::getItemIsRegistered($itemInHand)?->onBreakBlock($player);

        $statsManager = new StatsManager($player);
        $drops = $event->getDrops();

        foreach ($drops as $drop) {
            $dropChance = ItemManager::getVItemDropChance($drop);

            if ($dropChance > 0) {
                if (ItemManager::hasVItemMining($drop)) $drop->setCount($drop->getCount() + $statsManager->calculateFortune(self::MINING_FORTUNE, $dropChance, false));
                if (ItemManager::hasVItemFarming($drop)) $drop->setCount($drop->getCount() + $statsManager->calculateFortune(self::FARMING_FORTUNE, $dropChance, true));
            }
        }
    }

    public function onPlayerItemUse(PlayerItemUseEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onUse($player);
    }

    public function onPlayerItemHeld(PlayerItemHeldEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onHeld($player);
    }

    /**
     * @throws Throwable
     */
    public function onEntityDamage(EntityDamageEvent $event) : void
    {
        $cause = $event->getCause();

        if (($entity = $event->getEntity()) && $event instanceof EntityDamageByEntityEvent) {
            /** @var  Player $attacker */
            if (($attacker = $event->getDamager()) instanceof Player) {
                if (($vitem = ItemManager::getItemIsRegistered($attacker->getInventory()->getItemInHand())) instanceof BaseItem) {
                    if ($vitem->canAttack()) {
                        $statsAttacker = new StatsManager($attacker);
                        $event->setAttackCooldown((int) $statsAttacker->calculateAttackSpeed($event->getAttackCooldown()));
                        $event->setBaseDamage($statsAttacker->getDamageDealt());

                        // Maybe it good for arrow damage and arrow piercing
                        if ($event->getCause() === $event::CAUSE_PROJECTILE) {
                            $arrowDamage = $statsAttacker->getDamageDealt() + ($statsAttacker->getDamageDealt() * ($statsAttacker->getTotalStat(self::ARROW_DAMAGE) / 100));
                            $event->setBaseDamage($arrowDamage);
                        }

                        /** @var Player $attacker */
                        ItemManager::getItemIsRegistered($attacker->getInventory()->getItemInHand())?->onHit($attacker);

                        $ferocity = $statsAttacker->calculateFerocityAttacks();
                        if (!$statsAttacker->inQueueFerocity()) {

                            // TODO: Make this a promise to prevent lag
                            $statsAttacker->setQueueFerocity(Promise::c(function($resolve) use ($event, $ferocity) {
                                for ($i = 0; $i < $ferocity; $i++) {
                                    System::setTimeout(function() use ($event) {
                                        $entity = $event->getEntity();
                                        $attacker = $event->getDamager();

                                        $locationAttacker = $entity->getLocation();

                                        if ($entity->isClosed() || $attacker->isClosed()) return;

                                        $baseDamage = $event->getBaseDamage();
                                        // Maybe we can use this to call the event again?
                                        $entity->attack(new EntityDamageByEntityEvent($attacker, $entity, $event->getCause(), $baseDamage, $event->getModifiers(), 0.0));

                                        $soundPacket = new \pocketmine\network\mcpe\protocol\PlaySoundPacket();
                                        $soundPacket->soundName = "tile.piston.out";
                                        $soundPacket->x = $locationAttacker->getX();
                                        $soundPacket->y = $locationAttacker->getY();
                                        $soundPacket->z = $locationAttacker->getZ();
                                        $soundPacket->volume = 25;
                                        $soundPacket->pitch = 1; // I don't know what this is for

                                        /** @var Player $attacker */
                                        $attacker->getNetworkSession()->sendDataPacket($soundPacket);

                                        $location = $entity->getLocation();
                                        $xTarget = $location->getX();
                                        $yTarget = $location->getY();
                                        $zTarget = $location->getZ();

                                        $angle = mt_rand(0, 360);
                                        for ($i = -3; $i < 6; $i++) {
                                            $value = $i / 5;

                                            $x = $xTarget + $value * cos(deg2rad($angle)) * 2;
                                            $z = $zTarget + $value * sin(deg2rad($angle)) * 2;
                                            $y = $yTarget + $i / 2;

                                            $location->getWorld()->addParticle(new Vector3($x, $y, $z), self::getParticleFerocity());
                                        }

                                    }, $i * 200);
                                }

                                $timeResetFerocity = PluginSettings::isReduceLag() ? PluginSettings::TIME_TO_FEROCITY_RESET_IF_LAG : PluginSettings::TIME_TO_FEROCITY_RESET;

                                System::setTimeout(fn() => $resolve(), (int)($ferocity * $timeResetFerocity));
                            })->then(function() use($statsAttacker) {
                                $statsAttacker->removeQueueFerocity();
                            }));
                        }
                    }
                }
            }

            if ($entity instanceof Player) {
                ItemManager::getItemIsRegistered($entity->getInventory()->getItemInHand())?->onHitBy($entity, $attacker);

                $statsEntity = new StatsManager($entity);

                // Reduce damage based on true defense
                $trueDefense = $statsEntity->getTotalStat(self::TRUE_DEFENSE);
                $event->setBaseDamage($event->getBaseDamage() - ($trueDefense / ($trueDefense + 100)));

                // Regen health based on EHP
                $ehp = $statsEntity->calculateEHP();
                $regenEvent = new EntityRegainHealthEvent($entity, $ehp, EntityRegainHealthEvent::CAUSE_CUSTOM);
                $entity->heal($regenEvent);
            }
        }
    }

    public function onEntityShootBow(EntityShootBowEvent $event) : void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player) {
            $statsManager = new StatsManager($entity);
            $event->setForce($event->getForce() + $statsManager->getTotalStat(self::ARROW_PIERCING));
        }
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = $player->getInventory()->getItemInHand();
        $action = $event->getAction();

        $clickMode = match ($action) {
            $event::LEFT_CLICK_BLOCK => ClickMode::LEFT_CLICK_BLOCK,
            $event::RIGHT_CLICK_BLOCK => ClickMode::RIGHT_CLICK_BLOCK,
            default => ClickMode::UNKNOWN
        };

        ItemManager::getItemIsRegistered($itemInHand)?->onClickBlock($player, $clickMode);
    }

    /**
     * @throws Throwable
     */
    public function onDataPacketReceive(DataPacketReceiveEvent $event) : void
    {
        /**
         * This is a promise to prevent lag
         * Update all items on the server
         */
        if (!ServerHandler::hasUpdatedItems()) {
            Promise::c(function($resolve) {
                ServerHandler::setHasUpdatedItems(true); // This is a static method

                foreach (VItemsAPI::getInstance()->getServer()->getOnlinePlayers() as $player) {
                    foreach ($player->getInventory()->getContents() as $item) ItemManager::getItemIsRegistered($item)?->onTick($player);

                    if (PluginSettings::isReduceLag()) FiberManager::wait();
                }

                $resolve();
            })->then(function() {
                ServerHandler::setHasUpdatedItems(false); // This is a static method
            });
        }
    }

    public function PlayerItemConsume(PlayerItemConsumeEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onUse($player);
    }

}