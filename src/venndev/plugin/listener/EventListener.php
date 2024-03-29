<?php

declare(strict_types=1);

namespace venndev\plugin\listener;

use pocketmine\block\tile\Tile;
use pocketmine\event\inventory\InventoryTransactionEvent;
use pocketmine\event\player\PlayerMissSwingEvent;
use pocketmine\event\server\DataPacketSendEvent;
use pocketmine\math\Vector3;
use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\entity\EntityRegainHealthEvent;
use pocketmine\event\entity\EntityShootBowEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemUseEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use venndev\plugin\event\DamageFerocityEvent;
use venndev\plugin\item\BaseItem;
use venndev\plugin\manager\ItemManager;
use venndev\plugin\manager\PluginSettings;
use venndev\plugin\manager\ServerHandler;
use venndev\plugin\manager\StatsManager;
use venndev\plugin\manager\UtilManager;
use venndev\plugin\player\VPlayer;
use venndev\plugin\utils\BlockBreakHandler;
use venndev\plugin\utils\ClickMode;
use venndev\plugin\utils\ItemUtil;
use venndev\plugin\utils\ParticlesAbility;
use venndev\plugin\utils\StatsItem;
use venndev\plugin\utils\StatsPlayer;
use venndev\plugin\VItemsAPI;
use pocketmine\player\Player;
use venndev\plugin\world\particle\DamageDisplay;
use vennv\vapm\FiberManager;
use vennv\vapm\Promise;
use vennv\vapm\System;
use Throwable;

final class EventListener implements Listener
{
    use StatsItem;
    use StatsPlayer;
    use ParticlesAbility;

    /**
     * @priority HIGH
     */
    public function onPlayerJoinEvent(PlayerJoinEvent $event) : void
    {
        $player = $event->getPlayer();
        $statsManager = new StatsManager($player);
        $statsManager->setHasJoined(true);
        $statsManager->checkStatsHandler();
    }

    /**
     * @priority HIGH
     */
    public function onPlayerQuitEvent(PlayerQuitEvent $event) : void
    {
        $player = $event->getPlayer();

        // Clear the stats handler for the player when they leave the server.
        $statsManager = new StatsManager($player);
        $statsManager->clearStatsHandler();
        $statsManager->clearItemStatsHandler();
        $statsManager->setHasJoined(false);
    }

    /**
     * @priority HIGH
     */
    public function onBlockBreak(BlockBreakEvent $event) : void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $itemInHand = clone $player->getInventory()->getItemInHand();
        $itemRegistered = ItemManager::getItemIsRegistered($itemInHand);

        $itemRegistered?->onBreakBlock($block, $player);

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

    /**
     * @priority HIGH
     */
    public function onPlayerItemUse(PlayerItemUseEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onUse($player);
    }

    /**
     * @priority HIGH
     */
    public function onPlayerItemHeld(PlayerItemHeldEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onHeld($player);
    }

    /**
     * @priority HIGH
     */
    public function onPlayerMissSwing(PlayerMissSwingEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = clone $player->getInventory()->getItemInHand();

        $statsManager = new StatsManager($player);

        $statsManager->checkStatsHandler(); // Check if the stats handler is empty

        // Calculate the attack speed based on the default attack speed
        $statsManager->calculateAttackSpeed($statsManager->getStatsHandler()[StatsManager::DEFAULT_ATTACK_SPEED_TAG]);
    }

    /**
     * @throws Throwable
     * @priority HIGH
     */
    public function onEntityDamage(EntityDamageEvent $event) : void
    {
        $cause = $event->getCause();

        if (($entity = $event->getEntity()) && $event instanceof EntityDamageByEntityEvent && !$event instanceof DamageFerocityEvent) {
            /** @var  Player $attacker */
            if (($attacker = $event->getDamager()) instanceof Player) {
                if (($vitem = ItemManager::getItemIsRegistered($attacker->getInventory()->getItemInHand())) instanceof BaseItem) {
                    if ($vitem->canAttack()) {
                        $statsAttacker = new StatsManager($attacker);

                        // Recalculate the attack speed based on the default attack speed
                        //$statsAttacker->setStatHandler(StatsManager::DEFAULT_ATTACK_SPEED_TAG, $event->getAttackCooldown());

                        $event->setAttackCooldown((int) $statsAttacker->calculateAttackSpeed($event->getAttackCooldown()));

                        $damageDealtObj = $statsAttacker->getDamageDealt();
                        $damageDealt = $damageDealtObj->damage + $event->getBaseDamage();
                        $hasCrit = $damageDealtObj->hasCrit;

                        /**
                         * If the cause is a projectile, we will add the arrow damage to the base damage
                         * If not, we will just set the base damage to the damage dealt
                         */
                        $event->getCause() === $event::CAUSE_PROJECTILE ? $event->setBaseDamage($damageDealt + ($damageDealt * ($statsAttacker->getTotalStat(self::ARROW_DAMAGE) / 100))) : $event->setBaseDamage($damageDealt);

                        // Spawn the damage display
                        UtilManager::spawnDamageDisplay($entity, (int) $event->getBaseDamage(), $hasCrit);

                        /** @var Player $attacker */
                        ItemManager::getItemIsRegistered($attacker->getInventory()->getItemInHand())?->onHit($attacker);

                        $ferocity = $statsAttacker->calculateFerocityAttacks();
                        if (!$statsAttacker->inQueueFerocity()) {

                            // TODO: Make this a promise to prevent lag
                            $statsAttacker->setQueueFerocity(Promise::c(function($resolve) use ($event, $ferocity, $hasCrit) {
                                for ($i = 0; $i < $ferocity; $i++) {
                                    System::setTimeout(function() use ($event, $hasCrit) {
                                        $entity = $event->getEntity();
                                        $attacker = $event->getDamager();

                                        $locationAttacker = $entity->getLocation();

                                        if ($entity->isClosed() || $attacker->isClosed()) return;

                                        $baseDamage = $event->getBaseDamage();

                                        // Maybe we can use this to call the event again?
                                        $entity->attack(new DamageFerocityEvent($attacker, $entity, $event->getCause(), $baseDamage, $event->getModifiers(), 0.0));

                                        // Spawn the damage display
                                        UtilManager::spawnDamageDisplay($entity, (int) $event->getBaseDamage(), $hasCrit);

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
                                        $yTarget = $location->getY() + 1.5;
                                        $zTarget = $location->getZ();

                                        $angle = mt_rand(0, 360);
                                        for ($i = -3; $i < 3; $i++) {
                                            $value = $i / 5;

                                            $x = $xTarget - $value * cos(deg2rad($angle));
                                            $z = $zTarget - $value * sin(deg2rad($angle));
                                            $y = $yTarget + $i / 2;

                                            $location->getWorld()->addParticle(new Vector3($x, $y, $z), self::getParticleFerocity());
                                        }
                                    }, (int) ($i * PluginSettings::TIME_BETWEEN_FEROCITY_SPAWNS));
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
                $entity->heal(new EntityRegainHealthEvent($entity, $statsEntity->calculateEHP(), EntityRegainHealthEvent::CAUSE_CUSTOM));
            }
        }
    }

    /**
     * @priority HIGH
     */
    public function onEntityShootBow(EntityShootBowEvent $event) : void
    {
        $entity = $event->getEntity();

        if ($entity instanceof Player) $event->setForce($event->getForce() + (new StatsManager($entity))->getTotalStat(self::ARROW_PIERCING));
    }

    /**
     * @priority HIGH
     */
    public function onPlayerInteract(PlayerInteractEvent $event) : void
    {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $itemInHand = $player->getInventory()->getItemInHand();
        $action = $event->getAction();

        $clickMode = match ($action) {
            $event::LEFT_CLICK_BLOCK => ClickMode::LEFT_CLICK_BLOCK,
            $event::RIGHT_CLICK_BLOCK => ClickMode::RIGHT_CLICK_BLOCK,
            default => ClickMode::UNKNOWN
        };

        ItemManager::getItemIsRegistered($itemInHand)?->onClickBlock($player, $block, $clickMode);
    }

    /**
     * @priority HIGH
     */
    public function onDataPacketSend(DataPacketSendEvent $event) : void
    {
        $packets = $event->getPackets();
        $targets = $event->getTargets();

        foreach ($packets as $packet) {
            foreach ($targets as $target) {
                if (($player = $target->getPlayer()) !== null && $packet instanceof LevelEventPacket) {
                    if ($packet->eventId === LevelEvent::PARTICLE_PUNCH_BLOCK) {
                        $vPlayer = new VPlayer($player);
                        $blockBreakHandler = $vPlayer->getBlockBreakHandler();

                        if ($blockBreakHandler instanceof BlockBreakHandler) $vPlayer->attackVBlock($blockBreakHandler->getBlock()->getPosition()->asVector3());
                        break;
                    }
                }
            }
        }
    }

    /**
     * @throws Throwable
     * @priority HIGHEST
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

    /**
     * @priority HIGH
     */
    public function onPlayerItemConsume(PlayerItemConsumeEvent $event) : void
    {
        $player = $event->getPlayer();
        $itemInHand = $event->getItem();

        ItemManager::getItemIsRegistered($itemInHand)?->onUse($player);
    }

}