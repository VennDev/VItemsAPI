<?php

/**
 * All Rights Reserved
 *
 * Copyright (c) 2024 VennDev (https://github.com/VennDev)
 *
 * THE CONTENTS OF THIS PROJECT ARE PROPRIETARY AND CONFIDENTIAL.
 * UNAUTHORIZED COPYING, TRANSFERRING OR REPRODUCTION OF THE CONTENTS OF THIS PROJECT, VIA ANY MEDIUM IS STRICTLY PROHIBITED.
 *
 * The receipt or possession of the source code and/or any parts thereof does not convey or imply any right to use them
 * for any purpose other than the purpose for which they were provided to you.
 *
 * The software is provided "AS IS", without warranty of any kind, express or implied, including but not limited to
 * the warranties of merchantability, fitness for a particular purpose and non infringement.
 * In no event shall the authors or copyright holders be liable for any claim, damages or other liability,
 * whether in an action of contract, tort or otherwise, arising from, out of or in connection with the software
 * or the use or other dealings in the software.
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 */

declare(strict_types=1);

namespace venndev\plugin;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;
use vennv\vapm\VapmPMMP;

class VItemsAPI extends PluginBase
{
    use SingletonTrait;

    /**
     * @return void
     */
    protected function onLoad() : void
    {
        self::setInstance($this);
    }

    /**
     * @return void
     */
    protected function onEnable() : void
    {
        // Initialize the VAPM API
        VapmPMMP::init($this);

        // Register the event listener
        $this->getServer()->getPluginManager()->registerEvents(new \venndev\plugin\listener\EventListener(), $this);

        // Register the server tick task
        $this->getScheduler()->scheduleRepeatingTask(new \venndev\plugin\task\ServerTickTask($this->getServer()), 20);
    }

}